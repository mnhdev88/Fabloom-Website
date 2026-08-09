<?php
/**
 * Fabloom – Account Dashboard
 */
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

require_login('/account/login.php');

$user = current_user();

// ── Fetch account stats ────────────────────────────────────────────────────
$db = db();

$stats_stmt = $db->prepare(
    'SELECT
       COUNT(*)                     AS total_orders,
       COALESCE(SUM(total), 0)      AS total_spent,
       SUM(status = "delivered")    AS delivered,
       SUM(status = "pending")      AS pending,
       SUM(status = "processing")   AS processing,
       SUM(status = "shipped")      AS shipped
     FROM orders
     WHERE user_id = ?'
);
$stats_stmt->execute([$user['id']]);
$stats = $stats_stmt->fetch();

// ── Fetch recent orders (last 5) ───────────────────────────────────────────
$recent_stmt = $db->prepare(
    'SELECT id, order_number, status, total, created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC
     LIMIT 5'
);
$recent_stmt->execute([$user['id']]);
$recent_orders = $recent_stmt->fetchAll();

// Helper: status badge config
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

$page_title = 'My Account – Dashboard | Fabloom';
$page_desc  = 'Manage your Fabloom account, view orders and track your deliveries.';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ── Page Hero ─────────────────────────────────────────────────────────── -->
<section class="page-hero" aria-labelledby="dash-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">My Account</span>
    <h1 class="page-hero__title" id="dash-hero-title">
      Welcome back, <?= h(explode(' ', $user['name'])[0]) ?>
    </h1>
    <p class="page-hero__subtitle">Manage your orders, profile and shopping preferences</p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <span class="current" aria-current="page">My Account</span>
    </nav>
  </div>
</section>

<!-- ── Dashboard Content ──────────────────────────────────────────────────── -->
<section class="section section--sm" aria-label="Account dashboard">
  <div class="container">
    <div class="dash-layout">

      <!-- ── Sidebar Navigation ─────────────────────────────────────── -->
      <aside class="dash-sidebar" aria-label="Account navigation">
        <!-- User Avatar -->
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
          <a href="<?= SITE_URL ?>/account/dashboard" class="dash-nav__link dash-nav__link--active" aria-current="page">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
          </a>
          <a href="<?= SITE_URL ?>/account/orders" class="dash-nav__link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            My Orders
            <?php if ($stats['total_orders'] > 0): ?>
            <span class="dash-nav__count"><?= (int) $stats['total_orders'] ?></span>
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

      <!-- ── Main Content ─────────────────────────────────────────────── -->
      <div class="dash-main">

        <!-- Stats Row -->
        <div class="dash-stats-row" role="region" aria-label="Account statistics">
          <div class="dash-stat-card">
            <div class="dash-stat-icon" style="--icon-bg:#FEF2F2;--icon-color:#C0282A">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
              </svg>
            </div>
            <div class="dash-stat-body">
              <span class="dash-stat-num"><?= (int) $stats['total_orders'] ?></span>
              <span class="dash-stat-label">Total Orders</span>
            </div>
          </div>

          <div class="dash-stat-card">
            <div class="dash-stat-icon" style="--icon-bg:#ECFDF5;--icon-color:#065F46">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
              </svg>
            </div>
            <div class="dash-stat-body">
              <span class="dash-stat-num"><?= h(fmt_price((float) $stats['total_spent'])) ?></span>
              <span class="dash-stat-label">Total Spent</span>
            </div>
          </div>

          <div class="dash-stat-card">
            <div class="dash-stat-icon" style="--icon-bg:#F5F3FF;--icon-color:#6D28D9">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
            </div>
            <div class="dash-stat-body">
              <span class="dash-stat-num"><?= (int) ($stats['pending'] + $stats['processing'] + $stats['shipped']) ?></span>
              <span class="dash-stat-label">Active Orders</span>
            </div>
          </div>

          <div class="dash-stat-card">
            <div class="dash-stat-icon" style="--icon-bg:#ECFDF5;--icon-color:#065F46">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
            <div class="dash-stat-body">
              <span class="dash-stat-num"><?= (int) $stats['delivered'] ?></span>
              <span class="dash-stat-label">Delivered</span>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="dash-card" role="region" aria-labelledby="recent-orders-title">
          <div class="dash-card__header">
            <h2 id="recent-orders-title">Recent Orders</h2>
            <?php if ($stats['total_orders'] > 0): ?>
            <a href="<?= SITE_URL ?>/account/orders" class="dash-card__header-link">
              View All
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <?php endif; ?>
          </div>

          <?php if (empty($recent_orders)): ?>
          <div class="dash-empty">
            <div class="dash-empty__icon" aria-hidden="true">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
              </svg>
            </div>
            <h3>No orders yet</h3>
            <p>Start exploring our premium fabric collections and place your first order.</p>
            <a href="<?= SITE_URL ?>/products" class="btn btn-primary">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
              Shop Now
            </a>
          </div>

          <?php else: ?>
          <div class="dash-table-wrap">
            <table class="dash-table" aria-label="Recent orders">
              <thead>
                <tr>
                  <th scope="col">Order #</th>
                  <th scope="col">Date</th>
                  <th scope="col">Total</th>
                  <th scope="col">Status</th>
                  <th scope="col"><span class="sr-only">Actions</span></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_orders as $order):
                  $badge = status_badge($order['status']);
                ?>
                <tr>
                  <td class="dash-table__order-num">
                    <a href="<?= SITE_URL ?>/account/orders?id=<?= (int) $order['id'] ?>">
                      #<?= h($order['order_number']) ?>
                    </a>
                  </td>
                  <td class="dash-table__date">
                    <?= h(date('d M Y', strtotime($order['created_at']))) ?>
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
                       class="btn-text-link">
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

        <!-- Quick Links -->
        <div class="dash-quick-links" role="region" aria-label="Quick actions">
          <a href="<?= SITE_URL ?>/account/orders" class="dash-quick-card">
            <div class="dash-quick-card__icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
              </svg>
            </div>
            <div>
              <strong>My Orders</strong>
              <span>Track and manage all your orders</span>
            </div>
            <svg class="dash-quick-card__arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
          </a>

          <a href="#" class="dash-quick-card dash-quick-card--muted" title="Coming soon">
            <div class="dash-quick-card__icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <div>
              <strong>Edit Profile</strong>
              <span>Update your name, email &amp; phone</span>
            </div>
            <span class="dash-quick-card__soon">Coming Soon</span>
          </a>

          <a href="<?= SITE_URL ?>/products" class="dash-quick-card">
            <div class="dash-quick-card__icon" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/>
              </svg>
            </div>
            <div>
              <strong>Continue Shopping</strong>
              <span>Explore our premium fabric collections</span>
            </div>
            <svg class="dash-quick-card__arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        </div>

      </div><!-- /.dash-main -->
    </div><!-- /.dash-layout -->
  </div>
</section>

<style>
/* ── Dashboard Layout ── */
.dash-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: var(--sp-8);
  align-items: start;
}

@media (max-width: 900px) {
  .dash-layout { grid-template-columns: 1fr; }
}

/* ── Sidebar ── */
.dash-sidebar {
  position: sticky;
  top: calc(var(--nav-height) + var(--sp-4));
}

.dash-user-card {
  display: flex;
  align-items: center;
  gap: var(--sp-3);
  background: var(--clr-white);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl);
  padding: var(--sp-5) var(--sp-5);
  margin-bottom: var(--sp-3);
  box-shadow: var(--shadow-sm);
}

.dash-avatar {
  width: 48px; height: 48px;
  border-radius: var(--radius-full);
  background: linear-gradient(135deg, var(--clr-red), var(--clr-red-dark));
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-serif);
  font-size: var(--text-xl); font-weight: 700;
  color: var(--clr-white);
  flex-shrink: 0;
}

.dash-user-info { overflow: hidden; }
.dash-user-info strong { display: block; font-size: var(--text-sm); font-weight: 600; color: var(--clr-charcoal); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dash-user-info span  { font-size: var(--text-xs); color: var(--clr-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }

.dash-nav {
  background: var(--clr-white);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl);
  padding: var(--sp-2);
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  gap: var(--sp-1);
}

.dash-nav__link {
  display: flex;
  align-items: center;
  gap: var(--sp-3);
  padding: var(--sp-3) var(--sp-4);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--clr-text-secondary);
  transition-property: color, background-color, border-color, box-shadow, transform, opacity; transition-duration: var(--dur-fast);
}

.dash-nav__link:hover { background: var(--clr-cream); color: var(--clr-red); }

.dash-nav__link--active {
  background: var(--clr-red-bg);
  color: var(--clr-red);
  font-weight: 600;
}

.dash-nav__link--logout { color: #991B1B; }
.dash-nav__link--logout:hover { background: #FEF2F2; color: #7F1D1D; }

.dash-nav__link--soon { opacity: 0.55; cursor: not-allowed; }
.dash-nav__link--soon:hover { background: none; color: var(--clr-text-secondary); }

.dash-nav__count {
  margin-left: auto;
  background: var(--clr-red);
  color: white;
  font-size: 11px;
  font-weight: 700;
  border-radius: var(--radius-full);
  padding: 1px 7px;
  min-width: 20px;
  text-align: center;
}

.dash-nav__soon-badge {
  margin-left: auto;
  background: var(--clr-border);
  color: var(--clr-text-muted);
  font-size: 10px;
  font-weight: 700;
  border-radius: var(--radius-full);
  padding: 1px 7px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

/* ── Stats Row ── */
.dash-stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--sp-4);
  margin-bottom: var(--sp-6);
}

@media (max-width: 700px) { .dash-stats-row { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 400px) { .dash-stats-row { grid-template-columns: 1fr; } }

.dash-stat-card {
  background: var(--clr-white);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl);
  padding: var(--sp-5);
  display: flex;
  align-items: center;
  gap: var(--sp-4);
  box-shadow: var(--shadow-sm);
  transition: box-shadow var(--dur-fast);
}

.dash-stat-card:hover { box-shadow: var(--shadow-md); }

.dash-stat-icon {
  width: 48px; height: 48px;
  border-radius: var(--radius-lg);
  background: var(--icon-bg, var(--clr-red-bg));
  color: var(--icon-color, var(--clr-red));
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}

.dash-stat-body { display: flex; flex-direction: column; }
.dash-stat-num  { font-family: var(--font-serif); font-size: var(--text-2xl); font-weight: 700; color: var(--clr-charcoal); line-height: 1.1; }
.dash-stat-label { font-size: var(--text-xs); color: var(--clr-text-muted); margin-top: 2px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em; }

/* ── Dashboard Card ── */
.dash-card {
  background: var(--clr-white);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  margin-bottom: var(--sp-6);
}

.dash-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--sp-5) var(--sp-6);
  border-bottom: 1px solid var(--clr-border);
  background: linear-gradient(135deg, var(--clr-cream) 0%, var(--clr-ivory) 100%);
}

.dash-card__header h2 {
  font-family: var(--font-serif);
  font-size: var(--text-xl);
  color: var(--clr-charcoal);
}

.dash-card__header-link {
  display: flex; align-items: center; gap: var(--sp-1);
  font-size: var(--text-sm); font-weight: 600;
  color: var(--clr-red);
  transition: gap var(--dur-fast);
}

.dash-card__header-link:hover { gap: var(--sp-2); }

/* ── Orders Table ── */
.dash-table-wrap { overflow-x: auto; }

.dash-table {
  width: 100%;
  border-collapse: collapse;
}

.dash-table thead {
  background: linear-gradient(135deg, var(--clr-charcoal) 0%, var(--clr-dark-surface, #1C1917) 100%);
}

.dash-table thead th {
  padding: var(--sp-3) var(--sp-5);
  font-size: var(--text-xs);
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.7);
  text-align: left;
  white-space: nowrap;
}

.dash-table tbody tr {
  border-bottom: 1px solid var(--clr-border);
  transition: background var(--dur-fast);
}

.dash-table tbody tr:last-child { border-bottom: none; }
.dash-table tbody tr:hover { background: var(--clr-cream); }

.dash-table td {
  padding: var(--sp-4) var(--sp-5);
  font-size: var(--text-sm);
  color: var(--clr-text-primary);
  vertical-align: middle;
}

.dash-table__order-num a {
  font-weight: 700;
  color: var(--clr-red);
  font-family: var(--font-sans);
  transition: color var(--dur-fast);
}

.dash-table__order-num a:hover { color: var(--clr-red-dark); }

.dash-table__date { color: var(--clr-text-muted); }
.dash-table__total strong { font-weight: 700; }
.dash-table__action { text-align: right; }

/* Status Badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: var(--radius-full);
  border: 1px solid;
  font-size: var(--text-xs);
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  white-space: nowrap;
}

/* Text link */
.btn-text-link {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: var(--text-sm); font-weight: 600;
  color: var(--clr-red);
  transition: gap var(--dur-fast);
}

.btn-text-link:hover { gap: var(--sp-2); }

/* Empty State */
.dash-empty {
  padding: var(--sp-16) var(--sp-8);
  text-align: center;
}

.dash-empty__icon {
  width: 80px; height: 80px;
  background: var(--clr-cream);
  border-radius: var(--radius-full);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto var(--sp-5);
  color: var(--clr-text-muted);
}

.dash-empty h3 {
  font-family: var(--font-serif);
  font-size: var(--text-xl);
  color: var(--clr-charcoal);
  margin-bottom: var(--sp-2);
}

.dash-empty p {
  font-size: var(--text-sm);
  color: var(--clr-text-muted);
  margin-bottom: var(--sp-6);
  max-width: 320px;
  margin-inline: auto;
  line-height: 1.7;
}

/* ── Quick Links ── */
.dash-quick-links {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--sp-4);
}

@media (max-width: 640px) { .dash-quick-links { grid-template-columns: 1fr; } }

.dash-quick-card {
  display: flex;
  align-items: center;
  gap: var(--sp-4);
  padding: var(--sp-5);
  background: var(--clr-white);
  border: 1px solid var(--clr-border);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  transition-property: color, background-color, border-color, box-shadow, transform, opacity; transition-duration: var(--dur-base);
  cursor: pointer;
}

.dash-quick-card:hover {
  box-shadow: var(--shadow-md);
  border-color: var(--clr-red);
  transform: translateY(-3px);
}

.dash-quick-card--muted { opacity: 0.6; cursor: not-allowed; }
.dash-quick-card--muted:hover { transform: none; box-shadow: var(--shadow-sm); border-color: var(--clr-border); }

.dash-quick-card__icon {
  width: 52px; height: 52px;
  background: var(--clr-red-bg);
  border-radius: var(--radius-lg);
  display: flex; align-items: center; justify-content: center;
  color: var(--clr-red);
  flex-shrink: 0;
  transition: background var(--dur-fast);
}

.dash-quick-card:hover .dash-quick-card__icon { background: var(--clr-red); color: white; }
.dash-quick-card--muted:hover .dash-quick-card__icon { background: var(--clr-red-bg); color: var(--clr-red); }

.dash-quick-card > div:not(.dash-quick-card__icon) { flex: 1; min-width: 0; }
.dash-quick-card strong { display: block; font-size: var(--text-sm); font-weight: 600; color: var(--clr-charcoal); }
.dash-quick-card span  { font-size: var(--text-xs); color: var(--clr-text-muted); display: block; margin-top: 2px; }

.dash-quick-card__arrow { color: var(--clr-red); flex-shrink: 0; opacity: 0; transition: opacity var(--dur-fast); }
.dash-quick-card:hover .dash-quick-card__arrow { opacity: 1; }

.dash-quick-card__soon {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  background: var(--clr-border);
  color: var(--clr-text-muted);
  padding: 2px 8px;
  border-radius: var(--radius-full);
  flex-shrink: 0;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
