<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';
require_admin();

$db = db();

// ── Handle POST actions ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash('error', 'Invalid CSRF token.');
        header('Location: ' . SITE_URL . '/admin/products');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            // Hard delete — remove record
            $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Product deleted.');
        }
    } elseif ($action === 'toggle_featured') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $db->prepare('UPDATE products SET is_featured = NOT is_featured WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Featured status updated.');
        }
    } elseif ($action === 'toggle_status') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $db->prepare("UPDATE products SET status = IF(status='active','inactive','active') WHERE id = ?");
            $stmt->execute([$id]);
            flash('success', 'Product status updated.');
        }
    }

    $qs = '';
    if (!empty($_POST['filter_cat']))    $qs .= '&cat=' . urlencode($_POST['filter_cat']);
    if (!empty($_POST['filter_status'])) $qs .= '&status=' . urlencode($_POST['filter_status']);
    header('Location: ' . SITE_URL . '/admin/products?' . ltrim($qs, '&'));
    exit;
}

// ── Filters ────────────────────────────────────────────────────
$filter_cat    = $_GET['cat']    ?? '';
$filter_status = $_GET['status'] ?? '';

$categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$where = [];
$params = [];

if ($filter_cat !== '') {
    $where[]  = 'p.category_id = ?';
    $params[] = (int)$filter_cat;
}
if ($filter_status !== '') {
    $where[]  = 'p.status = ?';
    $params[] = $filter_status;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$products = $db->prepare(
    "SELECT p.*, c.name AS cat_name
     FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     $where_sql
     ORDER BY p.created_at DESC"
);
$products->execute($params);
$products = $products->fetchAll();

admin_html_open('Products', 'products');
?>

<!-- Filter Bar -->
<div class="filter-bar">
    <form method="GET" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;width:100%;">
        <label for="filter-cat">Category:</label>
        <select name="cat" id="filter-cat" onchange="this.form.submit()">
            <option value="">All</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (string)$filter_cat === (string)$cat['id'] ? 'selected' : '' ?>>
                    <?= h($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="filter-status">Status:</label>
        <select name="status" id="filter-status" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="active"   <?= $filter_status === 'active'   ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <?php if ($filter_cat || $filter_status): ?>
            <a href="<?= h(SITE_URL) ?>/admin/products" class="btn btn-secondary btn-sm">Clear</a>
        <?php endif; ?>

        <div style="margin-left:auto;">
            <a href="<?= h(SITE_URL) ?>/admin/product-edit" class="btn btn-primary">+ Add New Product</a>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h2>Products <span class="text-muted" style="font-weight:400">(<?= count($products) ?>)</span></h2>
    </div>
    <div class="table-wrap">
        <?php if (empty($products)): ?>
            <p style="padding:1.5rem;color:#9ca3af;text-align:center;">No products found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width:50px"></th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Sale Price</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?php if ($p['image']): ?>
                            <img src="<?= h(SITE_URL) ?>/assets/images/<?= h($p['image']) ?>" alt="" class="thumb">
                        <?php else: ?>
                            <div class="img-placeholder">No img</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600"><?= h($p['name']) ?></div>
                        <?php if ($p['sku']): ?>
                            <div class="text-muted" style="font-size:.75rem">SKU: <?= h($p['sku']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= h($p['cat_name'] ?? '—') ?></td>
                    <td><?= fmt_price((float)$p['price']) ?></td>
                    <td><?= $p['sale_price'] ? fmt_price((float)$p['sale_price']) : '<span class="text-muted">—</span>' ?></td>
                    <td>
                        <span style="color:<?= $p['stock'] == 0 ? '#b91c1c' : ($p['stock'] <= 5 ? '#d97706' : '#166534') ?>;font-weight:600">
                            <?= (int)$p['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="toggle_featured">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <?php if (!empty($_GET['cat']))    echo '<input type="hidden" name="filter_cat" value="' . h($filter_cat) . '">'; ?>
                            <?php if (!empty($_GET['status'])) echo '<input type="hidden" name="filter_status" value="' . h($filter_status) . '">'; ?>
                            <button type="submit" class="<?= $p['is_featured'] ? 'toggle-yes' : 'toggle-no' ?>" style="background:none;border:none;cursor:pointer;padding:0;line-height:0;"
                                    title="Toggle featured"
                                    aria-pressed="<?= $p['is_featured'] ? 'true' : 'false' ?>"
                                    aria-label="<?= $p['is_featured'] ? 'Remove' : 'Mark' ?> <?= h($p['name']) ?> <?= $p['is_featured'] ? 'from' : 'as' ?> featured">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="<?= $p['is_featured'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            </button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="toggle_status">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <?php if (!empty($_GET['cat']))    echo '<input type="hidden" name="filter_cat" value="' . h($filter_cat) . '">'; ?>
                            <?php if (!empty($_GET['status'])) echo '<input type="hidden" name="filter_status" value="' . h($filter_status) . '">'; ?>
                            <button type="submit" class="badge badge-<?= h($p['status']) ?>" style="border:none;cursor:pointer;font-family:inherit;" title="Click to toggle">
                                <?= ucfirst(h($p['status'])) ?>
                            </button>
                        </form>
                    </td>
                    <td>
                        <div style="display:flex;gap:.4rem;align-items:center;">
                            <a href="<?= h(SITE_URL) ?>/admin/product-edit?id=<?= (int)$p['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" onsubmit="return confirm('Delete this product permanently?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php admin_html_close(); ?>
