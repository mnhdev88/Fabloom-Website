<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';
require_admin();

$db = db();

// ── Stats ─────────────────────────────────────────────────────
$total_orders   = $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pending_orders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$total_products = $db->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
$total_revenue  = $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status = 'delivered'")->fetchColumn();

// ── Recent orders (last 10) ────────────────────────────────────
$recent_orders = $db->query(
    "SELECT id, order_number, name, email, created_at, total, status
     FROM orders ORDER BY created_at DESC LIMIT 10"
)->fetchAll();

// ── Low stock products ────────────────────────────────────────
$low_stock = $db->query(
    "SELECT id, name, stock, sku FROM products WHERE stock <= 5 AND status = 'active' ORDER BY stock ASC"
)->fetchAll();

// ── Quick status update ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (!csrf_verify()) {
        flash('error', 'Invalid CSRF token.');
    } else {
        $order_id  = (int)($_POST['order_id'] ?? 0);
        $status    = $_POST['status'] ?? '';
        $allowed   = ['pending','processing','shipped','delivered','cancelled'];
        if ($order_id && in_array($status, $allowed, true)) {
            $stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $stmt->execute([$status, $order_id]);
            flash('success', 'Order status updated.');
        }
    }
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

admin_html_open('Dashboard', 'dashboard');
?>

<!-- Stats -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value"><?= (int)$total_orders ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Orders</div>
        <div class="stat-value stat-accent"><?= (int)$pending_orders ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Products</div>
        <div class="stat-value"><?= (int)$total_products ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Revenue (Delivered)</div>
        <div class="stat-value" style="font-size:1.2rem"><?= fmt_price((float)$total_revenue) ?></div>
    </div>
</div>

<?php if (!empty($low_stock)): ?>
<div class="alert alert-warning">
    <strong>Low Stock Alert:</strong>
    <?php foreach ($low_stock as $i => $p): ?>
        <?= h($p['name']) ?> (<?= (int)$p['stock'] ?> left)<?= $i < count($low_stock) - 1 ? ', ' : '' ?>
    <?php endforeach; ?>
    — <a href="<?= h(SITE_URL) ?>/admin/products" style="color:inherit;font-weight:700;">Manage Products</a>
</div>
<?php endif; ?>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header">
        <h2>Recent Orders</h2>
        <a href="<?= h(SITE_URL) ?>/admin/orders" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="table-wrap">
        <?php if (empty($recent_orders)): ?>
            <p style="padding:1.5rem;color:#9ca3af;text-align:center;">No orders yet.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recent_orders as $o): ?>
                <tr>
                    <td><a href="<?= h(SITE_URL) ?>/admin/orders?id=<?= $o['id'] ?>" style="color:#C0282A;font-weight:600;text-decoration:none;"><?= h($o['order_number']) ?></a></td>
                    <td>
                        <div style="font-weight:600"><?= h($o['name']) ?></div>
                        <div class="text-muted" style="font-size:.78rem"><?= h($o['email']) ?></div>
                    </td>
                    <td class="text-muted"><?= h(date('d M Y', strtotime($o['created_at']))) ?></td>
                    <td style="font-weight:600"><?= fmt_price((float)$o['total']) ?></td>
                    <td>
                        <span class="badge badge-<?= h($o['status']) ?>"><?= h(ucfirst($o['status'])) ?></span>
                    </td>
                    <td>
                        <form method="POST" style="display:flex;gap:.4rem;align-items:center;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                            <select name="status" aria-label="Order status for order #<?= (int)$o['id'] ?>" style="font-size:.78rem;padding:.3rem .5rem;">
                                <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($low_stock)): ?>
<!-- Low Stock Table -->
<div class="card">
    <div class="card-header">
        <h2>Low Stock Products (≤ 5 units)</h2>
        <a href="<?= h(SITE_URL) ?>/admin/products" class="btn btn-secondary btn-sm">Manage Products</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Product</th><th>SKU</th><th>Stock</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($low_stock as $p): ?>
                <tr>
                    <td style="font-weight:600"><?= h($p['name']) ?></td>
                    <td class="text-muted"><?= h($p['sku'] ?? '—') ?></td>
                    <td>
                        <span style="color:<?= $p['stock'] == 0 ? '#b91c1c' : '#d97706' ?>;font-weight:700">
                            <?= (int)$p['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= h(SITE_URL) ?>/admin/product-edit?id=<?= (int)$p['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php admin_html_close(); ?>
