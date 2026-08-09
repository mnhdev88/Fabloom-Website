<?php
/**
 * Fabloom – Order Confirmation / Success Page
 * Loads order by ?order=ORDER_NUMBER and verifies it belongs to the current user.
 * Guest fallback: verifies against the session user_id stored during checkout.
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

// ── Auth guard ──────────────────────────────────────────────────────────────
// Only authenticated users land here (checkout.php enforces require_login first)
require_login('/account/login.php');

$order_number = trim($_GET['order'] ?? '');

// ── Validate order number format (basic sanity check) ───────────────────────
if ($order_number === '' || !preg_match('/^[A-Z0-9]{6,20}$/', $order_number)) {
    flash('error', 'Invalid order reference.');
    header('Location: ' . SITE_URL . '/account/orders');
    exit;
}

// ── Fetch order – must belong to the current logged-in user ────────────────
$user = current_user();

$stmt = db()->prepare(
    'SELECT o.*
     FROM   orders o
     WHERE  o.order_number = :order_number
       AND  o.user_id      = :user_id
     LIMIT  1'
);
$stmt->execute([
    ':order_number' => $order_number,
    ':user_id'      => $user['id'],
]);
$order = $stmt->fetch();

if (!$order) {
    flash('error', 'Order not found or you do not have permission to view it.');
    header('Location: ' . SITE_URL . '/account/orders');
    exit;
}

// ── Fetch order items ────────────────────────────────────────────────────────
$items_stmt = db()->prepare(
    'SELECT oi.name, oi.price, oi.quantity,
            p.slug, p.image
     FROM   order_items oi
     LEFT JOIN products p ON p.id = oi.product_id
     WHERE  oi.order_id = :order_id
     ORDER  BY oi.id ASC'
);
$items_stmt->execute([':order_id' => (int) $order['id']]);
$order_items = $items_stmt->fetchAll();

// ── Status label helper ──────────────────────────────────────────────────────
function status_badge(string $status): string {
    $map = [
        'pending'    => ['label' => 'Order Placed',  'class' => 'status-badge--pending'],
        'processing' => ['label' => 'Processing',    'class' => 'status-badge--processing'],
        'shipped'    => ['label' => 'Shipped',        'class' => 'status-badge--shipped'],
        'delivered'  => ['label' => 'Delivered',      'class' => 'status-badge--delivered'],
        'cancelled'  => ['label' => 'Cancelled',      'class' => 'status-badge--cancelled'],
    ];
    $s = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'status-badge--pending'];
    return '<span class="status-badge ' . $s['class'] . '">' . htmlspecialchars($s['label'], ENT_QUOTES) . '</span>';
}

$page_title = 'Order Confirmed – ' . h($order['order_number']) . ' | Fabloom';
$page_desc  = 'Your Fabloom order ' . $order['order_number'] . ' has been placed successfully.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Success Section ─────────────────────────────────────────────────── -->
<section class="success-section" aria-labelledby="success-heading">
  <div class="container">
    <div class="success-card">

      <!-- ── Hero Banner ──────────────────────────────────────────── -->
      <div class="success-card__hero">
        <div class="success-check-ring" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
               stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h1 id="success-heading">Thank You for Your Order!</h1>
        <p>We have received your order and will begin processing it shortly.</p>
      </div>

      <!-- ── Card Body ────────────────────────────────────────────── -->
      <div class="success-card__body">

        <!-- ── Order meta bar ──────────────────────────────────────── -->
        <div class="order-meta-bar" role="region" aria-label="Order details">
          <div class="order-meta-item">
            <span class="order-meta-item__label">Order Number</span>
            <span class="order-meta-item__value order-meta-item__value--order-num">
              #<?= h($order['order_number']) ?>
            </span>
          </div>
          <div class="order-meta-item">
            <span class="order-meta-item__label">Order Date</span>
            <span class="order-meta-item__value">
              <?= h(date('d M Y', strtotime($order['created_at']))) ?>
            </span>
          </div>
          <div class="order-meta-item">
            <span class="order-meta-item__label">Status</span>
            <span class="order-meta-item__value">
              <?= status_badge($order['status']) ?>
            </span>
          </div>
          <div class="order-meta-item">
            <span class="order-meta-item__label">Payment</span>
            <span class="order-meta-item__value">
              <?= $order['payment_method'] === 'cod'
                  ? 'Cash on Delivery'
                  : h(strtoupper($order['payment_method'])) ?>
            </span>
          </div>
        </div>

        <!-- ── Items ordered ───────────────────────────────────────── -->
        <h2 style="font-family:var(--font-serif);font-size:var(--text-xl);font-weight:700;
                   color:var(--clr-charcoal);margin-bottom:var(--sp-4);">
          Items Ordered
        </h2>

        <?php if (!empty($order_items)): ?>
        <div style="overflow-x:auto;" role="region" aria-label="Ordered items table">
          <table class="success-items-table">
            <thead>
              <tr>
                <th scope="col">Product</th>
                <th scope="col">Price</th>
                <th scope="col">Qty</th>
                <th scope="col">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order_items as $item): ?>
              <?php
                $img_src      = product_img($item['image'] ?? '');
                $product_url  = !empty($item['slug'])
                    ? SITE_URL . '/product/' . rawurlencode($item['slug'])
                    : SITE_URL . '/products';
                $line_total   = $item['price'] * $item['quantity'];
              ?>
              <tr>
                <td>
                  <div class="success-item-cell">
                    <div class="success-item-thumb" aria-hidden="true">
                      <img
                        src="<?= h($img_src) ?>"
                        alt="<?= h($item['name']) ?>"
                        loading="lazy"
                        width="56" height="56"
                        onerror="this.src='<?= SITE_URL ?>/assets/images/linen-fabric-hero.webp'">
                    </div>
                    <span class="success-item-name"><?= h($item['name']) ?></span>
                  </div>
                </td>
                <td><?= h(fmt_price((float) $item['price'])) ?></td>
                <td><?= (int) $item['quantity'] ?></td>
                <td><?= h(fmt_price($line_total)) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <p style="color:var(--clr-text-muted);font-size:var(--text-sm);margin-bottom:var(--sp-6)">
          Item details are being processed.
        </p>
        <?php endif; ?>

        <!-- ── Order Totals ────────────────────────────────────────── -->
        <div class="success-totals" role="region" aria-label="Order totals">
          <div class="success-total-row">
            <span class="label">Subtotal</span>
            <span class="value"><?= h(fmt_price((float) $order['subtotal'])) ?></span>
          </div>
          <div class="success-total-row">
            <span class="label">Shipping</span>
            <span class="value">
              <?php if ((float) $order['shipping'] === 0.0): ?>
                <span class="free-ship-badge">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                  Free
                </span>
              <?php else: ?>
                <?= h(fmt_price((float) $order['shipping'])) ?>
              <?php endif; ?>
            </span>
          </div>
          <div class="success-total-row">
            <span class="label">Grand Total</span>
            <span class="value"><?= h(fmt_price((float) $order['total'])) ?></span>
          </div>
        </div>

        <!-- ── Delivery Address ────────────────────────────────────── -->
        <h2 style="font-family:var(--font-serif);font-size:var(--text-xl);font-weight:700;
                   color:var(--clr-charcoal);margin-bottom:var(--sp-4);">
          Delivery Address
        </h2>

        <div class="delivery-address-box" role="region" aria-label="Delivery address">
          <div class="delivery-address-box__header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
            Shipping To
          </div>
          <address>
            <strong><?= h($order['name']) ?></strong>
            <?php if (!empty($order['phone'])): ?>
              <span>Phone: <?= h($order['phone']) ?></span><br>
            <?php endif; ?>
            <?= h($order['address']) ?><br>
            <?= h($order['city']) ?>,
            <?= h($order['state']) ?> – <?= h($order['pincode']) ?>
          </address>
          <?php if (!empty($order['email'])): ?>
          <p style="margin-top:var(--sp-3);font-size:var(--text-sm);color:var(--clr-text-muted)">
            Confirmation will be sent to:
            <strong style="color:var(--clr-text-primary)"><?= h($order['email']) ?></strong>
          </p>
          <?php endif; ?>
          <?php if (!empty($order['notes'])): ?>
          <div style="margin-top:var(--sp-3);padding-top:var(--sp-3);border-top:1px dashed var(--clr-border)">
            <p style="font-size:var(--text-xs);font-weight:700;color:var(--clr-text-muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px">Order Notes</p>
            <p style="font-size:var(--text-sm);color:var(--clr-text-secondary)"><?= h($order['notes']) ?></p>
          </div>
          <?php endif; ?>
        </div>

        <!-- ── What Happens Next ──────────────────────────────────── -->
        <div style="background:var(--clr-cream);border:1px solid var(--clr-border);
                    border-radius:var(--radius-md);padding:var(--sp-5) var(--sp-6);
                    margin-bottom:var(--sp-8);"
             role="note" aria-label="Next steps">
          <p style="font-size:var(--text-xs);font-weight:700;letter-spacing:0.12em;
                    text-transform:uppercase;color:var(--clr-red);margin-bottom:var(--sp-4)">
            What Happens Next?
          </p>
          <div style="display:flex;flex-direction:column;gap:var(--sp-3)">
            <?php
            $steps = [
              ['icon' => '<polyline points="20 6 9 17 4 12"/>', 'text' => 'Your order has been placed and confirmed.'],
              ['icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', 'text' => 'Our team will verify and prepare your order within 1–2 business days.'],
              ['icon' => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>', 'text' => 'Your fabric will be shipped via courier. You will be notified with a tracking number.'],
              ['icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>', 'text' => 'Delivery within 5–7 business days. Pay on delivery (COD).'],
            ];
            foreach ($steps as $i => $step):
            ?>
            <div style="display:flex;align-items:flex-start;gap:var(--sp-3)">
              <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--clr-red),var(--clr-red-dark));
                          display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                  <?= $step['icon'] ?>
                </svg>
              </div>
              <p style="font-size:var(--text-sm);color:var(--clr-text-secondary);line-height:1.6;padding-top:5px">
                <?= h($step['text']) ?>
              </p>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ── CTA Buttons ────────────────────────────────────────── -->
        <div class="success-cta-group">
          <a href="<?= SITE_URL ?>/account/orders" class="btn btn-primary"
             aria-label="Track your orders">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" aria-hidden="true">
              <path d="M9 17H5a2 2 0 01-2-2V5a2 2 0 012-2h6"/><path d="M13 3h6a2 2 0 012 2v10a2 2 0 01-2 2h-4"/>
              <polyline points="9 11 12 14 22 4"/>
            </svg>
            Track Orders
          </a>
          <a href="<?= SITE_URL ?>/products" class="btn btn-outline"
             aria-label="Continue shopping">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" aria-hidden="true">
              <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
              <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/>
            </svg>
            Continue Shopping
          </a>
        </div>

      </div><!-- /.success-card__body -->
    </div><!-- /.success-card -->
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
