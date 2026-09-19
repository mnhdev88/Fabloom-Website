<?php
require_once __DIR__ . '/../config/database.php';

// ── Session ───────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Auth helpers ─────────────────────────────────────────────
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user === null) {
        $stmt = db()->prepare('SELECT id, name, email, phone, is_admin FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function is_admin(): bool {
    $u = current_user();
    return $u && $u['is_admin'];
}

function require_login(string $redirect = '/account/login.php'): void {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . SITE_URL . $redirect);
        exit;
    }
}

function require_admin(): void {
    require_login('/admin/login.php');
    if (!is_admin()) {
        header('Location: ' . SITE_URL);
        exit;
    }
}

// ── Mail ──────────────────────────────────────────────────────
/**
 * Send a notification to every address in MAIL_TO.
 *
 * The From: header is always our own domain (MAIL_FROM) — putting the
 * visitor's address there makes the message fail SPF and land in spam.
 * The visitor goes in Reply-To instead, so hitting reply just works.
 *
 * Returns [bool $ok, string $error].
 */
function send_notification(
    string $subject,
    string $html_body,
    string $reply_to = '',
    string $reply_name = '',
    array $attachments = [],     // [['name' => ..., 'type' => ..., 'data' => raw bytes], ...]
    string $to_override = ''     // send to this address instead of the office inbox
): array {
    if (!function_exists('mail')) {
        return [false, 'PHP mail() is not available on this server'];
    }

    // Everything else here notifies the office, so MAIL_TO is the default.
    // Transactional mail addressed to a customer — a password reset link —
    // passes the recipient explicitly. It is validated and stripped of CR/LF
    // before use: an unchecked address in the To: header is the classic mail
    // header-injection hole.
    $to = implode(', ', MAIL_TO);
    if ($to_override !== '') {
        $candidate = trim(str_replace(["\r", "\n", "\0"], '', $to_override));
        if (!filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
            return [false, 'Invalid recipient address'];
        }
        $to = $candidate;
    }

    // Header injection guard — a newline in any of these would let an
    // attacker append arbitrary headers via the form fields.
    $clean = static fn(string $v): string => trim(str_replace(["\r", "\n", "\0"], '', $v));
    $reply_to   = $clean($reply_to);
    $reply_name = $clean($reply_name);
    $subject    = $clean($subject);

    if ($reply_to !== '' && !filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
        $reply_to = '';
    }

    // Display names go through RFC 2047 encoding. Quoting them raw lets a
    // name like  Bad" <attacker@evil.com>, "x  break out of the quoted
    // string and add a second, attacker-controlled Reply-To address.
    $enc_name = static fn(string $v): string =>
        $v === '' ? '' : '=?UTF-8?B?' . base64_encode($v) . '?= ';

    $alt  = '=_alt_' . bin2hex(random_bytes(10));
    $mixed = '=_mix_' . bin2hex(random_bytes(10));
    $outer = $attachments ? $mixed : $alt;
    $outer_type = $attachments ? 'multipart/mixed' : 'multipart/alternative';

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: ' . $outer_type . '; boundary="' . $outer . '"',
        'From: ' . $enc_name($clean(MAIL_FROM_NAME)) . '<' . MAIL_FROM . '>',
        'X-Mailer: Fabloom Website',
    ];
    if ($reply_to !== '') {
        $headers[] = 'Reply-To: ' . $enc_name($reply_name) . '<' . $reply_to . '>';
    }

    // Plain-text part first so text-only clients get the readable version.
    // Cell ends become ": " and row ends become newlines, otherwise every
    // label runs straight into its value ("NameAarti Sharma").
    $plain = $html_body;
    $plain = preg_replace('#</t[dh]>\s*<t[dh][^>]*>#i', ': ', $plain);   // between cells
    $plain = preg_replace('#</(tr|p|div|h[1-6]|table)>#i', "\n", $plain); // block ends
    $plain = preg_replace('#<br\s*/?>#i', "\n", $plain);
    $text  = trim(html_entity_decode(strip_tags($plain), ENT_QUOTES, 'UTF-8'));
    $text  = preg_replace("/[ \t]+\n/", "\n", $text);
    $text  = preg_replace("/\n{3,}/", "\n\n", $text);

    $alt_part = "--$alt\r\n"
              . "Content-Type: text/plain; charset=UTF-8\r\n"
              . "Content-Transfer-Encoding: 8bit\r\n\r\n"
              . $text . "\r\n\r\n"
              . "--$alt\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\n"
              . "Content-Transfer-Encoding: 8bit\r\n\r\n"
              . $html_body . "\r\n\r\n"
              . "--$alt--";

    if (!$attachments) {
        $body = $alt_part;
    } else {
        $body = "--$mixed\r\n"
              . "Content-Type: multipart/alternative; boundary=\"$alt\"\r\n\r\n"
              . $alt_part . "\r\n\r\n";
        foreach ($attachments as $a) {
            $fname = $clean((string)($a['name'] ?? 'attachment'));
            $fname = preg_replace('/[^A-Za-z0-9._-]/', '_', $fname) ?: 'attachment';
            $body .= "--$mixed\r\n"
                   . 'Content-Type: ' . $clean((string)($a['type'] ?? 'application/octet-stream'))
                   . '; name="' . $fname . "\"\r\n"
                   . "Content-Transfer-Encoding: base64\r\n"
                   . 'Content-Disposition: attachment; filename="' . $fname . "\"\r\n\r\n"
                   . chunk_split(base64_encode((string)($a['data'] ?? ''))) . "\r\n";
        }
        $body .= "--$mixed--";
    }

    $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    // -f sets the envelope sender, which many hosts require for delivery.
    $ok = @mail($to, $encoded_subject, $body, implode("\r\n", $headers), '-f' . MAIL_FROM);

    if (!$ok) {
        $err = error_get_last()['message'] ?? 'mail() returned false';
        error_log('Fabloom mail failure: ' . $err);
        return [false, substr($err, 0, 250)];
    }
    return [true, ''];
}

/** Render an array of label => value pairs as a readable email table. */
function mail_table(array $rows): string {
    $out = '<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;font:14px/1.6 Arial,Helvetica,sans-serif;color:#1C1917">';
    foreach ($rows as $label => $value) {
        if ($value === '' || $value === null) continue;
        $out .= '<tr>'
              . '<td style="padding:8px 12px;border-bottom:1px solid #E8E2D9;background:#FAF8F5;font-weight:700;white-space:nowrap;vertical-align:top">'
              . htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') . '</td>'
              . '<td style="padding:8px 12px;border-bottom:1px solid #E8E2D9;vertical-align:top">'
              . nl2br(htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')) . '</td>'
              . '</tr>';
    }
    return $out . '</table>';
}

/** Wrap notification content in a branded shell. */
function mail_wrap(string $heading, string $inner, string $footnote = ''): string {
    return '<div style="background:#FAF8F5;padding:24px;font:14px/1.6 Arial,Helvetica,sans-serif">'
         . '<div style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #E8E2D9;border-radius:8px;overflow:hidden">'
         . '<div style="background:#C0282A;color:#fff;padding:16px 20px;font-size:17px;font-weight:700">'
         . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</div>'
         . '<div style="padding:20px">' . $inner . '</div>'
         . ($footnote !== ''
             ? '<div style="padding:12px 20px;background:#FAF8F5;border-top:1px solid #E8E2D9;font-size:12px;color:#57534E">'
               . htmlspecialchars($footnote, ENT_QUOTES, 'UTF-8') . '</div>'
             : '')
         . '</div></div>';
}

// ── Price helpers ─────────────────────────────────────────────
function fmt_price(float $amount): string {
    return CURRENCY . number_format($amount, 2);
}

function product_price(array $p): float {
    return ($p['sale_price'] && $p['sale_price'] < $p['price']) ? (float)$p['sale_price'] : (float)$p['price'];
}

function discount_pct(array $p): int {
    if (!$p['sale_price'] || $p['sale_price'] >= $p['price']) return 0;
    return (int)round((($p['price'] - $p['sale_price']) / $p['price']) * 100);
}

// ── Units and minimum order ───────────────────────────────────
/**
 * Fabric is sold by the metre against a minimum cut. Sarees are sold as a
 * finished piece, so they must not inherit either rule — quoting a saree
 * "per metre, minimum 50" would be nonsense. See MIN_ORDER_BY_CATEGORY for
 * the categories that cut shorter than MIN_ORDER_METRES.
 */
function product_is_metre(array $p): bool {
    $slug = strtolower((string)($p['cat_slug'] ?? $p['category_slug'] ?? ''));
    $name = strtolower((string)($p['cat_name'] ?? ''));
    if ($slug === 'saree' || $name === 'saree') return false;
    // seeded sarees carry '–' for width because a width is meaningless there
    if (isset($p['width_inches']) && in_array(trim((string)$p['width_inches']), ['–', '-', ''], true)) {
        return false;
    }
    return true;
}

/** Short unit label: 'm' for fabric, 'piece' for sarees. */
function product_unit(array $p): string {
    return product_is_metre($p) ? 'm' : 'piece';
}

/**
 * Categories whose minimum cut differs from MIN_ORDER_METRES, keyed by
 * category slug. Both print lines are applied to already-woven cloth — block
 * print a repeat at a time by hand, digital print straight from the file — so
 * neither has to come off a full loom lot the way woven yardage does.
 * Anything not listed here falls back to the standard fabric minimum.
 */
const MIN_ORDER_BY_CATEGORY = [
    'block-print'   => 5,
    'digital-print' => 5,
];

/** Smallest quantity that may be ordered. */
function product_min_qty(array $p): int {
    if (!product_is_metre($p)) return 1;
    $slug = strtolower((string)($p['cat_slug'] ?? $p['category_slug'] ?? ''));
    return (int) (MIN_ORDER_BY_CATEGORY[$slug] ?? MIN_ORDER_METRES);
}

/** Price with its unit suffix, e.g. "₹1,050.00 / m". */
function fmt_price_unit(float $amount, array $p): string {
    return fmt_price($amount) . ' <span class="price-unit">/ ' . h(product_unit($p)) . '</span>';
}

/** Is this product orderable? Stock only counts when TRACK_STOCK is on. */
function product_in_stock(array $p): bool {
    if (!TRACK_STOCK) return true;
    return (int)($p['stock'] ?? 0) >= product_min_qty($p);
}

/** Upper bound for a quantity field. Untracked stock means no real ceiling. */
function product_max_qty(array $p): int {
    if (!TRACK_STOCK) return 9999;
    return max(product_min_qty($p), (int)($p['stock'] ?? 0));
}

/** Human note for the minimum, or '' when there is no minimum. */
function min_order_note(array $p): string {
    return product_is_metre($p)
        ? 'Minimum order ' . product_min_qty($p) . ' metres'
        : '';
}

/**
 * Raise a posted quantity to the product's minimum. Looks the product up so
 * the rule follows the item's own category rather than trusting the form.
 * Falls back to the fabric minimum if the product cannot be read.
 */
function enforce_min_qty(int $product_id, int $qty): int {
    static $cache = [];
    if (!array_key_exists($product_id, $cache)) {
        $cache[$product_id] = null;
        try {
            $stmt = db()->prepare(
                'SELECT p.width_inches, c.slug AS cat_slug, c.name AS cat_name
                 FROM products p LEFT JOIN categories c ON c.id = p.category_id
                 WHERE p.id = ?'
            );
            $stmt->execute([$product_id]);
            $cache[$product_id] = $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log('enforce_min_qty lookup failed: ' . $e->getMessage());
        }
    }
    $row = $cache[$product_id];
    $min = $row ? product_min_qty($row) : (int) MIN_ORDER_METRES;
    return max($min, $qty);
}

// ── Cart helpers (session-based) ──────────────────────────────
function cart_items(): array {
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int {
    return array_sum(array_column(cart_items(), 'qty'));
}

function cart_add(int $product_id, int $qty = 1): void {
    $cart = cart_items();
    if (isset($cart[$product_id])) {
        $cart[$product_id]['qty'] += $qty;
    } else {
        // width_inches and the category come along so the cart can show the
        // right unit and hold the line to its minimum without re-querying.
        $stmt = db()->prepare(
            'SELECT p.id, p.name, p.price, p.sale_price, p.image, p.width_inches,
                    c.slug AS cat_slug, c.name AS cat_name
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = ? AND p.status = "active"'
        );
        $stmt->execute([$product_id]);
        $p = $stmt->fetch();
        if (!$p) return;
        $cart[$product_id] = [
            'id'      => $p['id'],
            'name'    => $p['name'],
            'price'   => product_price($p),
            'image'   => $p['image'],
            'qty'     => $qty,
            'unit'    => product_unit($p),
            'min_qty' => product_min_qty($p),
        ];
    }
    $_SESSION['cart'] = $cart;
}

function cart_update(int $product_id, int $qty): void {
    if ($qty <= 0) {
        cart_remove($product_id);
        return;
    }
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['qty'] = $qty;
    }
}

function cart_remove(int $product_id): void {
    unset($_SESSION['cart'][$product_id]);
}

function cart_clear(): void {
    $_SESSION['cart'] = [];
}

function cart_subtotal(): float {
    $total = 0;
    foreach (cart_items() as $item) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}

function cart_shipping(): float {
    return cart_subtotal() >= FREE_SHIPPING_ABOVE ? 0 : SHIPPING_CHARGE;
}

function cart_total(): float {
    return cart_subtotal() + cart_shipping();
}

// ── Slug / URL helpers ────────────────────────────────────────
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Canonical address of a product page.
 *
 * Path segment, not a query string: robots.txt disallows "/*?*", so every
 * product sitting on ?slug= was blocked from crawling outright. .htaccess
 * rewrites /product/<slug> back to product.php?slug=<slug>, and 301s the old
 * query form here, so existing links keep working.
 */
function product_url(array $p): string {
    return SITE_URL . '/product/' . rawurlencode($p['slug']);
}

function product_img(string $image, string $fallback = 'products/linen-khadi.webp'): string {
    $img = $image ?: $fallback;
    return SITE_URL . '/assets/images/' . htmlspecialchars($img);
}

// ── Flash messages ────────────────────────────────────────────
function flash(string $key, string $msg): void {
    $_SESSION['flash'][$key] = $msg;
}

function get_flash(string $key): string {
    $msg = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $msg;
}

// ── CSRF ──────────────────────────────────────────────────────
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): bool {
    return isset($_POST['csrf_token']) && hash_equals(csrf_token(), $_POST['csrf_token']);
}

// ── Order number generator ────────────────────────────────────
function generate_order_number(): string {
    return 'FB' . strtoupper(substr(uniqid('', true), -8));
}

// ── Pagination ────────────────────────────────────────────────
function paginate(int $total, int $per_page, int $current_page): array {
    $total_pages = (int)ceil($total / $per_page);
    return [
        'total'       => $total,
        'per_page'    => $per_page,
        'current'     => $current_page,
        'total_pages' => $total_pages,
        'offset'      => ($current_page - 1) * $per_page,
        'has_prev'    => $current_page > 1,
        'has_next'    => $current_page < $total_pages,
    ];
}

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Versioned URL for a stylesheet or script.
 *
 * .htaccess serves CSS and JS with "access plus 1 week", so a returning
 * visitor kept the old file for up to seven days after a deploy — a phone
 * that had seen the site before rendered new markup against stale CSS.
 * Stamping the file's mtime into the query string keeps the long cache for
 * unchanged files and invalidates the moment one is edited.
 */
function asset(string $path): string {
    static $cache = [];
    $rel = '/' . ltrim($path, '/');
    if (!array_key_exists($rel, $cache)) {
        $file = dirname(__DIR__) . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        $cache[$rel] = is_file($file) ? (string)filemtime($file) : '';
    }
    return SITE_URL . $rel . ($cache[$rel] !== '' ? '?v=' . $cache[$rel] : '');
}
