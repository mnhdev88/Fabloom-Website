<?php
/**
 * Fabloom – My Orders
 * Lists all user orders; shows inline detail when ?id=N is passed.
 */
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

require_login('/account/login.php');

$user = current_user();
$db   = db();

// ── Status badge helper ────────────────────────────────────────────────────
function status_badge(string $status): array {
    return match ($status) {
        'pending'    => ['label' => 'Pending',    'color' => '#D97706', 'bg' => '#FFFBEB', 'border' => '#FDE68A'],
        'processing' => ['label' => 'Processing', 'color' => '#1D4ED8', 'bg' => '#EFF6FF', 'border' => '#BFDBFE'],
        'shipped'    => ['label' => 'Shipped',    'color' => '#6D28D9', 'bg' => '#F5F3FF', 'border' => '#DDD6FE'],
        'delivered'  => ['label' => 'Delivered',  'color' => '#065F46', 'bg' => '#ECFDF5', 'border' => '#A7F3D0'],
        'cancelled'  => ['label' => 'Cancelled',  'color' => '#991B1B', 'bg' => '#FEF2F2', 'border' => '#FECACA'],
        default      => ['label' => ucfirst($status), 'color' => '#374151', 'bg' => '#F9FAFB', 'border' => '#E5E7EB'],
    };
}

// ── Order Detail view ─────────────────────────────────────────────────────
$detail_order = null;
$detail_items = [];

if (isset($_GET['id'])) {
    $order_id = (int) $_GET['id'];
    $ostmt = $db->prepare(
        'SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1'
    );
    $ostmt->execute([$order_id, $user['id']]);
    $detail_order = $ostmt->fetch();

    if ($detail_order) {
        $istmt = $db->prepare(
            'SELECT oi.*, p.slug AS product_slug
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY oi.id ASC'
        );
        $istmt->execute([$order_id]);
        $detail_items = $istmt->fetchAll();
    }
}

// ── Fetch ALL orders (list view) ──────────────────────────────────────────
$orders_stmt = $db->prepare(
    'SELECT
       o.id, o.order_number, o.status, o.total, o.created_at,
       COUNT(oi.id) AS item_count
     FROM orders o
     LEFT JOIN order_items oi ON oi.order_id = o.id
     WHERE o.user_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC'
);
$orders_stmt->execute([$user['id']]);
$orders = $orders_stmt->fetchAll();

// ── Status timeline stages ────────────────────────────────────────────────
function timeline_stages(string $status): array {
    $all = ['pending', 'processing', 'shipped', 'delivered'];
    if ($status === 'cancelled') {
        return ['cancelled'];
    }
    $reached = false;
    $stages  = [];
    foreach ($all as $s) {
        $stages[$s] = ['done' => false, 'active' => false];
        if ($s === $status) { $stages[$s]['active'] = true; $reached = true; }
        if (!$reached) { $stages[$s]['done'] = true; }
    }
    // Mark previous stages done
    $hitActive = false;
    foreach ($stages as $k => &$v) {
        if ($v['active']) { $hitActive = true; continue; }
        if (!$hitActive) { $v['done'] = true; }
    }
    return $stages;
}

$page_title = 'My Orders | Fabloom';
$page_desc  = 'View and track all your Fabloom orders in one place.';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ── Page Hero ─────────────────────────────────────────────────────────── -->
<section class="page-hero" aria-labelledby="orders-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">My Account</span>
    <h1 class="page-hero__title" id="orders-hero-title">
      <?= $detail_order ? 'Order #' . h($detail_order['order_number']) : 'My Orders' ?>
    </h1>
    <p class="page-hero__subtitle">
      <?= $detail_order
        ? 'Placed on ' . h(date('d F Y', strtotime($detail_order['created_at'])))
        : count($orders) . ' order' . (count($orders) !== 1 ? 's' : '') . ' in total' ?>
    </p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <a href="<?= SITE_URL ?>/account/dashboard">My Account</a>
      <span class="sep" aria-hidden="true">›</span>
      <?php if ($detail_order): ?>
        <a href="<?= SITE_URL ?>/account/orders">My Orders</a>
        <span class="sep" aria-hidden="true">›</span>
        <span class="current" aria-current="page">Order Detail</span>
      <?php else: ?>
        <span class="current" aria-current="page">My Orders</span>
      <?php endif; ?>
    </nav>
  </div>
</section>

<!-- ── Content ─────────────────────────────────────────────────────────────── -->
<section class="section section--sm" aria-label="Orders">
  <div class="container">
    <div class="dash-layout">

      <!-- ── Sidebar ─────────────────────────────────────────────────── -->
      <aside class="dash-sidebar" aria-label="Account navigation">
        <div class="dash-user-card">
          <div class="dash-avatar" aria-hidden="true">
            <?= h(mb_substr($user['name'], 0, 1)) ?>
          </div>
          <div class="dash-user-info">
            <strong><?= h($user['name']) ?></strong>
            <span><?= h($user['email']) ?></span>
          </div>
        </div>
        <nav class="dash-nav" aria-label="Account sections">
          <a href="<?= SITE_URL ?>/account/dashboard" class="dash-nav__link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
          </a>
          <a href="<?= SITE_URL ?>/account/orders" class="dash-nav__link dash-nav__link--active" aria-current="page">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            My Orders
            <?php if (count($orders) > 0): ?>
            <span class="dash-nav__count"><?= count($orders) ?></span>
            <?php endif; ?>
          </a>
          <a href="#" class="dash-nav__link dash-nav__link--soon" title="Coming soon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            Edit Profile
            <span class="dash-nav__soon-badge">Soon</span>
          </a>
          <a href="<?= SITE_URL ?>/account/logout" class="dash-nav__link dash-nav__link--logout">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
          </a>
        </nav>
      </aside>

      <!-- ── Main ───────────────────────────────────────────────────────── -->
      <div class="dash-main">

        <?php if ($detail_order): ?>
        <!-- ══════════════════════ ORDER DETAIL VIEW ══════════════════════ -->

        <!-- Back Button -->
        <a href="<?= SITE_URL ?>/account/orders" class="orders-back-link" aria-label="Back to all orders">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
          Back to My Orders
        </a>

        <!-- Order Status Header -->
        <?php $badge = status_badge($detail_order['status']); ?>
        <div class="order-detail-header">
          <div>
            <div class="order-detail-num">Order #<?= h($detail_order['order_number']) ?></div>
            <div class="order-detail-meta">
              Placed on <?= h(date('d F Y, g:i A', strtotime($detail_order['created_at']))) ?>
              &nbsp;·&nbsp;
              Payment: <?= h(strtoupper($detail_order['payment_method'] ?? 'COD')) ?>
            </div>
          </div>
          <span class="status-badge status-badge--lg"
            style="color:<?= h($badge['color']) ?>;background:<?= h($badge['bg']) ?>;border-color:<?= h($badge['border']) ?>">
            <?= h($badge['label']) ?>
          </span>
        </div>

        <!-- Status Timeline -->
        <?php if ($detail_order['status'] !== 'cancelled'): ?>
        <?php $stages = timeline_stages($detail_order['status']); ?>
        <div class="order-timeline" role="list" aria-label="Order progress">
          <?php
          $stage_icons = [
            'pending'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            'processing' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            'shipped'    => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
            'delivered'  => '<polyline points="20 6 9 17 4 12"/>',
          ];
          foreach ($stages as $stage => $info):
            $is_done   = $info['done'];
            $is_active = $info['active'];
            $badge_c   = status_badge($stage);
          ?>
          <div class="order-timeline__step <?= $is_done ? 'done' : '' ?> <?= $is_active ? 'active' : '' ?>"
               role="listitem" aria-label="<?= h(ucfirst($stage)) ?> — <?= $is_done ? 'complete' : ($is_active ? 'current' : 'pending') ?>">
            <div class="order-timeline__icon" style="<?= ($is_done || $is_active) ? 'background:' . h($badge_c['color']) . ';color:#fff;border-color:' . h($badge_c['color']) : '' ?>">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <?= $stage_icons[$stage] ?? '' ?>
              </svg>
            </div>
            <div class="order-timeline__label"><?= h(ucfirst($stage)) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="order-cancelled-notice" role="alert">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          This order has been cancelled. Contact us at
          <a href="mailto:info@thefabloom.com">info@thefabloom.com</a> for assistance.
        </div>
        <?php endif; ?>

        <!-- Two Column Detail -->
        <div class="order-detail-grid">

          <!-- Items List -->
          <div class="dash-card">
            <div class="dash-card__header">
              <h2>Items Ordered (<?= count($detail_items) ?>)</h2>
            </div>
            <?php if (empty($detail_items)): ?>
            <p style="padding:var(--sp-6);color:var(--clr-text-muted);font-size:var(--text-sm)">No items found for this order.</p>
            <?php else: ?>
            <div class="order-items-table">
              <?php foreach ($detail_items as $item): ?>
              <div class="order-item-row">
                <div class="order-item-row__info">
                  <div class="order-item-row__name">
                    <?php if (!empty($item['product_slug'])): ?>
                      <a href="<?= SITE_URL ?>/product/<?= h(rawurlencode($item['product_slug'])) ?>">
                        <?= h($item['name']) ?>
                      </a>
                    <?php else: ?>
                      <span><?= h($item['name']) ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="order-item-row__meta">
                    <?= h(fmt_price((float)$item['price'])) ?> &times; <?= (int)$item['quantity'] ?> unit<?= $item['quantity'] > 1 ? 's' : '' ?>
                  </div>
                </div>
                <div class="order-item-row__subtotal">
                  <?= h(fmt_price((float)$item['price'] * (int)$item['quantity'])) ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>

            <!-- Totals -->
            <div class="order-totals">
              <div class="order-totals__row">
                <span>Subtotal</span>
                <span><?= h(fmt_price((float)$detail_order['subtotal'])) ?></span>
              </div>
              <div class="order-totals__row">
                <span>Shipping</span>
                <span>
                  <?php if ((float)$detail_order['shipping'] === 0.0): ?>
                    <span style="color:#065F46;font-weight:600">Free</span>
                  <?php else: ?>
                    <?= h(fmt_price((float)$detail_order['shipping'])) ?>
                  <?php endif; ?>
                </span>
              </div>
              <div class="order-totals__row order-totals__row--total">
                <span>Total</span>
                <span><?= h(fmt_price((float)$detail_order['total'])) ?></span>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <!-- Delivery Address -->
          <div class="dash-card">
            <div class="dash-card__header">
              <h2>Delivery Address</h2>
            </div>
            <div class="order-address">
              <div class="order-address__name">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <?= h($detail_order['name']) ?>
              </div>
              <?php if ($detail_order['phone']): ?>
              <div class="order-address__row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                <?= h($detail_order['phone']) ?>
              </div>
              <?php endif; ?>
              <div class="order-address__row">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <?= h($detail_order['email']) ?>
              </div>
              <div class="order-address__block">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <address>
                  <?= h($detail_order['address']) ?><br>
                  <?= h($detail_order['city']) ?>, <?= h($detail_order['state']) ?> – <?= h($detail_order['pincode']) ?>
                </address>
              </div>
              <?php if (!empty($detail_order['notes'])): ?>
              <div class="order-address__notes">
                <strong>Order Notes:</strong>
                <?= h($detail_order['notes']) ?>
              </div>
              <?php endif; ?>
            </div>

            <!-- Help -->
            <div class="order-help">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              Need help with this order?
              <a href="mailto:info@thefabloom.com">Contact support</a>
            </div>
          </div>

        </div><!-- /.order-detail-grid -->

        <!-- Actions -->
        <div class="order-detail-actions">
          <a href="<?= SITE_URL ?>/account/orders" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            All Orders
          </a>
          <a href="<?= SITE_URL ?>/products" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
            Shop Again
          </a>
        </div>

        <?php else: ?>
        <!-- ══════════════════════ ORDERS LIST VIEW ══════════════════════ -->

        <div class="dash-card" role="region" aria-labelledby="orders-list-title">
          <div class="dash-card__header">
            <h2 id="orders-list-title">All Orders</h2>
            <span style="font-size:var(--text-sm);color:var(--clr-text-muted)"><?= count($orders) ?> total</span>
          </div>

          <?php if (empty($orders)): ?>
          <div class="dash-empty">
            <div class="dash-empty__icon" aria-hidden="true">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
              </svg>
            </div>
            <h3>No orders yet</h3>
            <p>You haven't placed any orders. Start exploring our premium fabric collections today.</p>
            <a href="<?= SITE_URL ?>/products" class="btn btn-primary">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
              Browse Products
            </a>
          </div>

          <?php else: ?>
          <div class="dash-table-wrap">
            <table class="dash-table" aria-label="Your orders">
              <thead>
                <tr>
                  <th scope="col">Order #</th>
                  <th scope="col">Date</th>
                  <th scope="col">Items</th>
                  <th scope="col">Total</th>
                  <th scope="col">Status</th>
                  <th scope="col"><span class="sr-only">View</span></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order):
                  $badge = status_badge($order['status']);
                ?>
                <tr>
                  <td class="dash-table__order-num">
                    <a href="<?= SITE_URL ?>/account/orders?id=<?= (int) $order['id'] ?>"
                       aria-label="View order <?= h($order['order_number']) ?>">
                      #<?= h($order['order_number']) ?>
                    </a>
                  </td>
                  <td class="dash-table__date">
                    <?= h(date('d M Y', strtotime($order['created_at']))) ?>
                  </td>
                  <td style="color:var(--clr-text-secondary)">
                    <?= (int) $order['item_count'] ?> item<?= $order['item_count'] != 1 ? 's' : '' ?>
                  </td>
                  <td class="dash-table__total">
                    <strong><?= h(fmt_price((float) $order['total'])) ?></strong>
                  </td>
                  <td>
                    <span class="status-badge"
                      style="color:<?= h($badge['color']) ?>;background:<?= h($badge['bg']) ?>;border-color:<?= h($badge['border']) ?>">
                      <?= h($badge['label']) ?>
                    </span>
                  </td>
                  <td class="dash-table__action">
                    <a href="<?= SITE_URL ?>/account/orders?id=<?= (int) $order['id'] ?>"
                       class="btn-text-link"
                       aria-label="View details for order <?= h($order['order_number']) ?>">
                      View
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div><!-- /.dash-card -->

        <!-- Quick continue shopping -->
        <div style="text-align:center;margin-top:var(--sp-4)">
          <a href="<?= SITE_URL ?>/products" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
            Continue Shopping
          </a>
        </div>

        <?php endif; ?>
      </div><!-- /.dash-main -->
    </div><!-- /.dash-layout -->
  </div>
</section>

<style>
/* ─────── Shared Dashboard Layout (mirrors dashboard.php) ─────── */
.dash-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: var(--sp-8);
  align-items: start;
}

@media (max-width: 900px) {
  .dash-layout { grid-template-columns: 1fr; }
}

.dash-sidebar {
  position: sticky;
  top: calc(var(--nav-height) + var(--sp-4));
}

.dash-user-card {
  display: flex; align-items: center; gap: var(--sp-3);
  background: var(--clr-white); border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl); padding: var(--sp-5);
  margin-bottom: var(--sp-3); box-shadow: var(--shadow-sm);
}

.dash-avatar {
  width: 48px; height: 48px; border-radius: var(--radius-full);
  background: linear-gradient(135deg, var(--clr-red), var(--clr-red-dark));
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-serif); font-size: var(--text-xl); font-weight: 700;
  color: var(--clr-white); flex-shrink: 0;
}

.dash-user-info { overflow: hidden; }
.dash-user-info strong { display: block; font-size: var(--text-sm); font-weight: 600; color: var(--clr-charcoal); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dash-user-info span   { font-size: var(--text-xs); color: var(--clr-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }

.dash-nav {
  background: var(--clr-white); border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl); padding: var(--sp-2);
  box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: var(--sp-1);
}

.dash-nav__link {
  display: flex; align-items: center; gap: var(--sp-3);
  padding: var(--sp-3) var(--sp-4); border-radius: var(--radius-md);
  font-size: var(--text-sm); font-weight: 500;
  color: var(--clr-text-secondary); transition-property: color, background-color, border-color, box-shadow, transform, opacity; transition-duration: var(--dur-fast);
}

.dash-nav__link:hover { background: var(--clr-cream); color: var(--clr-red); }
.dash-nav__link--active { background: var(--clr-red-bg); color: var(--clr-red); font-weight: 600; }
.dash-nav__link--logout { color: #991B1B; }
.dash-nav__link--logout:hover { background: #FEF2F2; color: #7F1D1D; }
.dash-nav__link--soon { opacity: 0.55; cursor: not-allowed; }
.dash-nav__link--soon:hover { background: none; color: var(--clr-text-secondary); }

.dash-nav__count {
  margin-left: auto; background: var(--clr-red); color: white;
  font-size: 11px; font-weight: 700; border-radius: var(--radius-full);
  padding: 1px 7px; min-width: 20px; text-align: center;
}

.dash-nav__soon-badge {
  margin-left: auto; background: var(--clr-border); color: var(--clr-text-muted);
  font-size: 10px; font-weight: 700; border-radius: var(--radius-full);
  padding: 1px 7px; letter-spacing: 0.05em; text-transform: uppercase;
}

.dash-card {
  background: var(--clr-white); border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl); box-shadow: var(--shadow-sm);
  overflow: hidden; margin-bottom: var(--sp-6);
}

.dash-card__header {
  display: flex; align-items: center; justify-content: space-between;
  padding: var(--sp-5) var(--sp-6); border-bottom: 1px solid var(--clr-border);
  background: linear-gradient(135deg, var(--clr-cream) 0%, var(--clr-ivory) 100%);
}

.dash-card__header h2 {
  font-family: var(--font-serif); font-size: var(--text-xl); color: var(--clr-charcoal);
}

.dash-table-wrap { overflow-x: auto; }

.dash-table { width: 100%; border-collapse: collapse; }

.dash-table thead { background: linear-gradient(135deg, var(--clr-charcoal) 0%, #1C1917 100%); }

.dash-table thead th {
  padding: var(--sp-3) var(--sp-5);
  font-size: var(--text-xs); font-weight: 700; letter-spacing: 0.1em;
  text-transform: uppercase; color: rgba(255,255,255,0.7); text-align: left; white-space: nowrap;
}

.dash-table tbody tr { border-bottom: 1px solid var(--clr-border); transition: background var(--dur-fast); }
.dash-table tbody tr:last-child { border-bottom: none; }
.dash-table tbody tr:hover { background: var(--clr-cream); }

.dash-table td {
  padding: var(--sp-4) var(--sp-5); font-size: var(--text-sm);
  color: var(--clr-text-primary); vertical-align: middle;
}

.dash-table__order-num a { font-weight: 700; color: var(--clr-red); transition: color var(--dur-fast); }
.dash-table__order-num a:hover { color: var(--clr-red-dark); }
.dash-table__date { color: var(--clr-text-muted); }
.dash-table__total strong { font-weight: 700; }
.dash-table__action { text-align: right; }

.status-badge {
  display: inline-flex; align-items: center;
  padding: 0.25rem 0.75rem; border-radius: var(--radius-full); border: 1px solid;
  font-size: var(--text-xs); font-weight: 700; letter-spacing: 0.04em;
  text-transform: uppercase; white-space: nowrap;
}

.status-badge--lg {
  padding: 0.4rem 1rem; font-size: var(--text-sm);
}

.btn-text-link {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: var(--text-sm); font-weight: 600; color: var(--clr-red); transition: gap var(--dur-fast);
}
.btn-text-link:hover { gap: var(--sp-2); }

.dash-empty { padding: var(--sp-16) var(--sp-8); text-align: center; }
.dash-empty__icon {
  width: 80px; height: 80px; background: var(--clr-cream); border-radius: var(--radius-full);
  display: flex; align-items: center; justify-content: center; margin: 0 auto var(--sp-5); color: var(--clr-text-muted);
}
.dash-empty h3 { font-family: var(--font-serif); font-size: var(--text-xl); color: var(--clr-charcoal); margin-bottom: var(--sp-2); }
.dash-empty p { font-size: var(--text-sm); color: var(--clr-text-muted); margin-bottom: var(--sp-6); max-width: 320px; margin-inline: auto; line-height: 1.7; }

/* ─────── Order Detail ─────── */
.orders-back-link {
  display: inline-flex; align-items: center; gap: var(--sp-2);
  font-size: var(--text-sm); font-weight: 600; color: var(--clr-red);
  margin-bottom: var(--sp-5); transition: gap var(--dur-fast);
}
.orders-back-link:hover { gap: var(--sp-3); }

.order-detail-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  gap: var(--sp-4); flex-wrap: wrap;
  background: var(--clr-white); border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl); padding: var(--sp-6); margin-bottom: var(--sp-5);
  box-shadow: var(--shadow-sm);
}

.order-detail-num {
  font-family: var(--font-serif); font-size: var(--text-2xl);
  font-weight: 700; color: var(--clr-charcoal); margin-bottom: var(--sp-1);
}

.order-detail-meta { font-size: var(--text-sm); color: var(--clr-text-muted); }

/* Status Timeline */
.order-timeline {
  display: flex; align-items: flex-start; justify-content: space-between;
  position: relative; margin-bottom: var(--sp-6);
  background: var(--clr-white); border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl); padding: var(--sp-6); box-shadow: var(--shadow-sm);
}

.order-timeline::before {
  content: '';
  position: absolute;
  top: calc(var(--sp-6) + 24px);
  left: calc(var(--sp-6) + 24px);
  right: calc(var(--sp-6) + 24px);
  height: 2px;
  background: var(--clr-border);
  z-index: 0;
}

.order-timeline__step {
  display: flex; flex-direction: column; align-items: center; gap: var(--sp-2);
  flex: 1; z-index: 1; position: relative;
}

.order-timeline__icon {
  width: 48px; height: 48px; border-radius: var(--radius-full);
  border: 2px solid var(--clr-border); background: var(--clr-cream);
  display: flex; align-items: center; justify-content: center;
  color: var(--clr-text-muted); transition-property: color, background-color, border-color, box-shadow, transform, opacity; transition-duration: var(--dur-base);
}

.order-timeline__step.done .order-timeline__icon   { background: #065F46; color: white; border-color: #065F46; }
.order-timeline__step.active .order-timeline__icon { animation: pulse-icon 2s ease infinite; }

@keyframes pulse-icon {
  0%, 100% { box-shadow: 0 0 0 0 rgba(192,40,42,0.3); }
  50%       { box-shadow: 0 0 0 8px rgba(192,40,42,0); }
}

.order-timeline__label {
  font-size: var(--text-xs); font-weight: 600; text-transform: uppercase;
  letter-spacing: 0.06em; color: var(--clr-text-muted);
}

.order-timeline__step.done .order-timeline__label   { color: #065F46; }
.order-timeline__step.active .order-timeline__label { color: var(--clr-red); }

@media (max-width: 560px) {
  .order-timeline { flex-direction: column; gap: var(--sp-3); }
  .order-timeline::before { display: none; }
  .order-timeline__step { flex-direction: row; justify-content: flex-start; gap: var(--sp-4); }
}

.order-cancelled-notice {
  display: flex; align-items: center; gap: var(--sp-3);
  background: #FEF2F2; border: 1px solid #FECACA;
  border-radius: var(--radius-lg); padding: var(--sp-4) var(--sp-5);
  margin-bottom: var(--sp-5); font-size: var(--text-sm); color: #991B1B;
}

.order-cancelled-notice a { color: var(--clr-red); font-weight: 600; }

/* Order Detail Grid */
.order-detail-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: var(--sp-5);
}

@media (max-width: 700px) { .order-detail-grid { grid-template-columns: 1fr; } }

/* Items in detail */
.order-items-table { padding: 0 var(--sp-6); }

.order-item-row {
  display: flex; align-items: center; justify-content: space-between;
  gap: var(--sp-4); padding: var(--sp-4) 0;
  border-bottom: 1px solid var(--clr-border);
}

.order-item-row:last-of-type { border-bottom: none; }

.order-item-row__name a { color: var(--clr-red); font-weight: 600; transition: color var(--dur-fast); }
.order-item-row__name a:hover { color: var(--clr-red-dark); }
.order-item-row__name span { font-weight: 600; color: var(--clr-charcoal); }
.order-item-row__meta { font-size: var(--text-xs); color: var(--clr-text-muted); margin-top: 2px; }
.order-item-row__subtotal { font-weight: 700; color: var(--clr-charcoal); white-space: nowrap; }

/* Totals */
.order-totals {
  border-top: 2px solid var(--clr-border);
  padding: var(--sp-4) var(--sp-6) var(--sp-5);
  display: flex; flex-direction: column; gap: var(--sp-2);
}

.order-totals__row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: var(--text-sm); color: var(--clr-text-secondary);
}

.order-totals__row--total {
  border-top: 1px solid var(--clr-border); margin-top: var(--sp-1);
  padding-top: var(--sp-2);
  font-size: var(--text-base); font-weight: 700; color: var(--clr-charcoal);
}

/* Address */
.order-address { padding: var(--sp-5) var(--sp-6); display: flex; flex-direction: column; gap: var(--sp-3); }

.order-address__name {
  display: flex; align-items: center; gap: var(--sp-2);
  font-weight: 700; font-size: var(--text-base); color: var(--clr-charcoal);
}

.order-address__name svg { color: var(--clr-red); flex-shrink: 0; }

.order-address__row {
  display: flex; align-items: center; gap: var(--sp-2);
  font-size: var(--text-sm); color: var(--clr-text-secondary);
}

.order-address__row svg { color: var(--clr-text-muted); flex-shrink: 0; }

.order-address__block {
  display: flex; align-items: flex-start; gap: var(--sp-2);
  font-size: var(--text-sm); color: var(--clr-text-secondary);
}

.order-address__block svg { color: var(--clr-text-muted); flex-shrink: 0; margin-top: 3px; }

.order-address__block address { font-style: normal; line-height: 1.6; }

.order-address__notes {
  background: var(--clr-cream); border-radius: var(--radius-md);
  padding: var(--sp-3) var(--sp-4);
  font-size: var(--text-sm); color: var(--clr-text-secondary); line-height: 1.6;
}

.order-address__notes strong { color: var(--clr-charcoal); display: block; margin-bottom: 2px; }

.order-help {
  display: flex; align-items: center; gap: var(--sp-2);
  padding: var(--sp-4) var(--sp-6);
  border-top: 1px solid var(--clr-border);
  background: var(--clr-cream);
  font-size: var(--text-sm); color: var(--clr-text-muted);
}

.order-help svg { color: var(--clr-red); flex-shrink: 0; }
.order-help a { color: var(--clr-red); font-weight: 600; margin-left: 4px; }
.order-help a:hover { color: var(--clr-red-dark); }

/* Detail actions */
.order-detail-actions {
  display: flex; gap: var(--sp-4); flex-wrap: wrap;
  margin-bottom: var(--sp-6);
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
