<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';
require_admin();

$db = db();

// ── Handle POST: status update ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid CSRF token.');
    } else {
        $action   = $_POST['action'] ?? '';
        $order_id = (int)($_POST['order_id'] ?? 0);
        $allowed_statuses = ['pending','processing','shipped','delivered','cancelled'];

        if ($action === 'update_status' && $order_id) {
            $new_status = $_POST['status'] ?? '';
            if (in_array($new_status, $allowed_statuses, true)) {
                $stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
                $stmt->execute([$new_status, $order_id]);
                flash('success', 'Order #' . $order_id . ' status updated to ' . ucfirst($new_status) . '.');
            }
        } elseif ($action === 'save_notes' && $order_id) {
            $notes = trim($_POST['notes'] ?? '');
            $stmt  = $db->prepare('UPDATE orders SET notes = ? WHERE id = ?');
            $stmt->execute([$notes, $order_id]);
            flash('success', 'Notes saved.');
        }
    }
    $qs = '';
    if (!empty($_POST['filter_status'])) $qs = '?status=' . urlencode($_POST['filter_status']);
    if (!empty($_POST['detail_id']))     $qs .= ($qs ? '&' : '?') . 'id=' . (int)$_POST['detail_id'];
    header('Location: ' . SITE_URL . '/admin/orders' . $qs);
    exit;
}

// ── Filters ────────────────────────────────────────────────────
$filter_status = $_GET['status'] ?? '';
$detail_id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$allowed_statuses = ['pending','processing','shipped','delivered','cancelled'];
$where  = '';
$params = [];
if ($filter_status && in_array($filter_status, $allowed_statuses, true)) {
    $where  = 'WHERE o.status = ?';
    $params = [$filter_status];
}

$orders = $db->prepare(
    "SELECT o.id, o.order_number, o.name, o.email, o.phone,
            o.created_at, o.total, o.status, o.payment_method,
            COUNT(oi.id) AS item_count
     FROM orders o
     LEFT JOIN order_items oi ON oi.order_id = o.id
     $where
     GROUP BY o.id
     ORDER BY o.created_at DESC"
);
$orders->execute($params);
$orders = $orders->fetchAll();

// ── Load order detail ─────────────────────────────────────────
$order_detail = null;
$order_items  = [];
if ($detail_id) {
    $stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([$detail_id]);
    $order_detail = $stmt->fetch();

    if ($order_detail) {
        $stmt2 = $db->prepare(
            'SELECT oi.*, p.image FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?'
        );
        $stmt2->execute([$detail_id]);
        $order_items = $stmt2->fetchAll();
    }
}

admin_html_open('Orders', 'orders');
?>

<style>
.order-detail-panel {
    background:#fff;
    border-radius:8px;
    box-shadow:0 1px 4px rgba(0,0,0,.07);
    margin-bottom:1.5rem;
    overflow:hidden;
}
.detail-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:1rem;
    padding:1.25rem;
}
.detail-section h4 {
    font-size:.72rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.8px;
    color:#9ca3af;
    margin-bottom:.6rem;
}
.detail-row {
    display:flex;
    gap:.5rem;
    font-size:.85rem;
    margin-bottom:.3rem;
}
.detail-row strong { color:#374151; min-width:90px; flex-shrink:0; }
</style>

<!-- Filter bar -->
<div class="filter-bar">
    <label>Filter by Status:</label>
    <?php
    $status_labels = ['' => 'All', 'pending' => 'Pending', 'processing' => 'Processing',
                      'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'];
    foreach ($status_labels as $val => $label):
        $active_cls = $filter_status === $val ? 'btn-primary' : 'btn-secondary';
    ?>
        <a href="<?= h(SITE_URL) ?>/admin/orders<?= $val ? '?status=' . $val : '' ?>"
           class="btn <?= $active_cls ?> btn-sm">
            <?= h($label) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if ($order_detail): ?>
<!-- ── Order Detail Panel ──────────────────────────────── -->
<div class="order-detail-panel">
    <div class="card-header" style="background:#f9fafb;">
        <div>
            <h2 style="font-size:1rem">Order <?= h($order_detail['order_number']) ?>
                <span class="badge badge-<?= h($order_detail['status']) ?>" style="margin-left:.5rem">
                    <?= ucfirst(h($order_detail['status'])) ?>
                </span>
            </h2>
            <div class="text-muted" style="font-size:.78rem;margin-top:2px">
                Placed <?= h(date('d M Y, h:i A', strtotime($order_detail['created_at']))) ?>
                &nbsp;·&nbsp; Payment: <?= h(ucfirst($order_detail['payment_method'] ?? 'N/A')) ?>
            </div>
        </div>
        <a href="<?= h(SITE_URL) ?>/admin/orders<?= $filter_status ? '?status=' . urlencode($filter_status) : '' ?>"
           class="btn btn-secondary btn-sm"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Close</a>
    </div>

    <div class="detail-grid">
        <div class="detail-section">
            <h4>Customer</h4>
            <div class="detail-row"><strong>Name:</strong> <?= h($order_detail['name']) ?></div>
            <div class="detail-row"><strong>Email:</strong> <?= h($order_detail['email']) ?></div>
            <div class="detail-row"><strong>Phone:</strong> <?= h($order_detail['phone']) ?></div>
        </div>
        <div class="detail-section">
            <h4>Shipping Address</h4>
            <div style="font-size:.85rem;line-height:1.6">
                <?= h($order_detail['address']) ?><br>
                <?= h($order_detail['city']) ?>, <?= h($order_detail['state']) ?> — <?= h($order_detail['pincode']) ?>
            </div>
        </div>
    </div>

    <!-- Items table -->
    <div style="padding:0 1.25rem 1.25rem;">
        <h4 style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:.75rem;">Order Items</h4>
        <table style="width:100%">
            <thead>
                <tr>
                    <th style="background:transparent;border-bottom:1px solid #e5e7eb;width:44px"></th>
                    <th style="background:transparent;border-bottom:1px solid #e5e7eb;">Product</th>
                    <th style="background:transparent;border-bottom:1px solid #e5e7eb;">Price</th>
                    <th style="background:transparent;border-bottom:1px solid #e5e7eb;">Qty</th>
                    <th style="background:transparent;border-bottom:1px solid #e5e7eb;text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= h(SITE_URL) ?>/assets/images/<?= h($item['image']) ?>" alt="" class="thumb">
                        <?php else: ?>
                            <div class="img-placeholder">—</div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600"><?= h($item['name']) ?></td>
                    <td><?= fmt_price((float)$item['price']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td class="text-right" style="font-weight:600"><?= fmt_price((float)$item['price'] * $item['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right" style="border-top:1px solid #f3f4f6;padding-top:.6rem;color:#6b7280;font-size:.82rem">Subtotal</td>
                    <td class="text-right" style="border-top:1px solid #f3f4f6;padding-top:.6rem"><?= fmt_price((float)$order_detail['subtotal']) ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right" style="color:#6b7280;font-size:.82rem">Shipping</td>
                    <td class="text-right"><?= fmt_price((float)$order_detail['shipping']) ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right" style="font-weight:700;font-size:.95rem">Total</td>
                    <td class="text-right" style="font-weight:800;font-size:1rem;color:#C0282A"><?= fmt_price((float)$order_detail['total']) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Status + Notes forms -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;padding:1.25rem;border-top:1px solid #f3f4f6;background:#fafafa;">
        <div>
            <h4 style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:.6rem;">Update Status</h4>
            <form method="POST" style="display:flex;gap:.5rem;align-items:center;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" value="<?= (int)$order_detail['id'] ?>">
                <input type="hidden" name="detail_id" value="<?= (int)$order_detail['id'] ?>">
                <?php if ($filter_status) echo '<input type="hidden" name="filter_status" value="' . h($filter_status) . '">'; ?>
                <select name="status" aria-label="Order status for order #<?= (int)$order_detail['id'] ?>">
                    <?php foreach ($allowed_statuses as $s): ?>
                        <option value="<?= $s ?>" <?= $order_detail['status'] === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
            </form>
        </div>
        <div>
            <h4 style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:.6rem;">Notes</h4>
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save_notes">
                <input type="hidden" name="order_id" value="<?= (int)$order_detail['id'] ?>">
                <input type="hidden" name="detail_id" value="<?= (int)$order_detail['id'] ?>">
                <?php if ($filter_status) echo '<input type="hidden" name="filter_status" value="' . h($filter_status) . '">'; ?>
                <div style="display:flex;gap:.5rem;align-items:flex-start;">
                    <textarea name="notes" aria-label="Internal notes for order #<?= (int)$order_detail['id'] ?>" style="flex:1;min-height:60px;font-size:.85rem;"><?= h($order_detail['notes'] ?? '') ?></textarea>
                    <button type="submit" class="btn btn-secondary btn-sm" style="white-space:nowrap">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ── Orders Table ─────────────────────────────────────── -->
<div class="card">
    <div class="card-header">
        <h2>
            <?= $filter_status ? ucfirst(h($filter_status)) . ' Orders' : 'All Orders' ?>
            <span class="text-muted" style="font-weight:400">(<?= count($orders) ?>)</span>
        </h2>
    </div>
    <div class="table-wrap">
        <?php if (empty($orders)): ?>
            <p style="padding:1.5rem;color:#9ca3af;text-align:center;">No orders found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Quick Update</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr style="<?= $detail_id === (int)$o['id'] ? 'background:#fff8f8;' : '' ?>">
                    <td style="font-weight:700;color:#C0282A"><?= h($o['order_number']) ?></td>
                    <td>
                        <div style="font-weight:600"><?= h($o['name']) ?></div>
                        <div class="text-muted" style="font-size:.75rem"><?= h($o['email']) ?></div>
                        <?php if ($o['phone']): ?>
                            <div class="text-muted" style="font-size:.75rem"><?= h($o['phone']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted" style="white-space:nowrap"><?= h(date('d M Y', strtotime($o['created_at']))) ?></td>
                    <td><?= (int)$o['item_count'] ?> item<?= $o['item_count'] != 1 ? 's' : '' ?></td>
                    <td style="font-weight:700"><?= fmt_price((float)$o['total']) ?></td>
                    <td><span class="badge badge-<?= h($o['status']) ?>"><?= ucfirst(h($o['status'])) ?></span></td>
                    <td>
                        <form method="POST" style="display:flex;gap:.35rem;align-items:center;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                            <?php if ($filter_status) echo '<input type="hidden" name="filter_status" value="' . h($filter_status) . '">'; ?>
                            <select name="status" aria-label="Order status for order #<?= (int)$o['id'] ?>" style="font-size:.75rem;padding:.25rem .45rem;">
                                <?php foreach ($allowed_statuses as $s): ?>
                                    <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-secondary btn-sm" style="padding:.25rem .55rem;font-size:.75rem;">Go</button>
                        </form>
                    </td>
                    <td>
                        <?php
                        $detail_url = SITE_URL . '/admin/orders?id=' . $o['id'];
                        if ($filter_status) $detail_url .= '&status=' . urlencode($filter_status);
                        ?>
                        <a href="<?= h($detail_url) ?>" class="btn btn-secondary btn-sm">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php admin_html_close(); ?>
