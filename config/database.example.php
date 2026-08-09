<?php
/*
 * TEMPLATE ONLY — copy to database.php and fill in real values.
 * database.php is gitignored: production credentials must never enter the repo.
 */
// ── Database Configuration ────────────────────────────────────
define('DB_HOST', 'localhost');              // correct for cPanel
define('DB_NAME', 'yourcpanel_dbname');       // your prefixed database name
define('DB_USER', 'yourcpanel_dbuser');       // your prefixed user name
define('DB_PASS', 'CHANGE_ME');

define('DB_CHARSET', 'utf8mb4');

// ── Site Configuration ────────────────────────────────────────
define('SITE_NAME',  'Fabloom Group of Company');
define('BRAND_NAME',    'Fabloom');            // shown in the logo area
define('BRAND_TAGLINE', 'Group of Company');   // shown under the logo name

// The live address. Every link, image and canonical is built from this,
// so it must have no trailing slash.
define('SITE_URL_LIVE', 'https://www.thefabloom.com');

// Serving from localhost (XAMPP, php -S, a staging box) should not require
// editing this file back and forth — detect it and build the base URL from
// the actual request instead. Anything else falls through to the live value.
$_host = $_SERVER['HTTP_HOST'] ?? '';
if ($_host !== '' && preg_match('/^(localhost|127\.0\.0\.1|\[::1\])(:\d+)?$/i', $_host)) {
    $_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    // keep any subfolder the site is served from, e.g. /fabloom
    // dirname() returns a backslash at the root on Windows, so normalise
    // after every call — otherwise SITE_URL ends with "\" and every asset
    // URL becomes "http://host\/css/style.css", which no browser will load.
    $_norm = static fn(string $p): string => rtrim(str_replace('\\', '/', $p), '/');

    $_base = $_norm(dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    if (in_array(basename($_base), ['locations', 'account', 'admin', 'api'], true)) {
        $_base = $_norm(dirname($_base));
    }
    define('SITE_URL', $_scheme . '://' . $_host . $_base);
} else {
    define('SITE_URL', SITE_URL_LIVE);
}

define('CURRENCY',   '₹');

// Fabric is quoted per metre and sold in minimum 50 m lots. Sarees are
// finished pieces and are exempt — see product_is_metre() in functions.php.
define('MIN_ORDER_METRES', 50);

// Fabric is woven to order, so on-hand stock is not a real constraint.
// With this off, nothing is ever shown as out of stock and the stored
// stock figures are ignored for availability. Set to true to enforce them.
define('TRACK_STOCK', false);
// Shipping is free on every order. With a 50 m minimum the old ₹2,000
// threshold was met by definition, so the tiered rule only ever added
// misleading copy. Set SHIPPING_CHARGE above 0 to reinstate charging.
define('FREE_SHIPPING_ABOVE', 0);     // 0 = every order qualifies
define('SHIPPING_CHARGE',     0);     // flat charge when it does not

// ── Mail Configuration ────────────────────────────────────────
// Every website form (contact, enquiries, sample requests) and every
// new order notification is delivered to these inboxes.
define('MAIL_TO', [
    'info@thefabloom.com',
    'fabloom86@gmail.com',
]);

// The envelope sender. This MUST be an address on your own domain or the
// mail will be rejected/spam-foldered — never put the visitor's address here.
define('MAIL_FROM',      'no-reply@thefabloom.com');
define('MAIL_FROM_NAME', SITE_NAME);

/**
 * Non-fatal database handle.
 *
 * db() calls die() when the connection fails, which is fine for page
 * requests but fatal for JSON endpoints — it emits HTML mid-response and
 * aborts before anything else can run. Callers that must survive a
 * database outage (e.g. the enquiry endpoint, which still needs to send
 * the notification email) should use this and handle null.
 */
function db_optional(): ?PDO {
    static $tried = false;
    static $pdo   = null;
    if ($tried) return $pdo;
    $tried = true;
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('DB connection failed (non-fatal): ' . $e->getMessage());
        $pdo = null;
    }
    return $pdo;
}

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // In production, log the error and show a friendly message
            error_log('DB connection failed: ' . $e->getMessage());
            die('<p style="font-family:sans-serif;color:#c0282a;padding:2rem;">Database connection failed. Please check your configuration.</p>');
        }
    }
    return $pdo;
}
