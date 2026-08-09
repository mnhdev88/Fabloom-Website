<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';
require_admin();

$db = db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── Load product for edit ─────────────────────────────────────
$product = null;
if ($id) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        flash('error', 'Product not found.');
        header('Location: ' . SITE_URL . '/admin/products');
        exit;
    }
}

$categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$errors = [];
$form = $product ?? [
    'name' => '', 'category_id' => '', 'short_desc' => '', 'description' => '',
    'price' => '', 'sale_price' => '', 'sku' => '', 'stock' => 0,
    'fabric_type' => '', 'weight_gsm' => '', 'width_inches' => '',
    'is_featured' => 0, 'status' => 'active', 'image' => '', 'slug' => '',
];

// ── Handle POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        // Collect fields
        $name        = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $short_desc  = trim($_POST['short_desc'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = trim($_POST['price'] ?? '');
        $sale_price  = trim($_POST['sale_price'] ?? '');
        $sku         = trim($_POST['sku'] ?? '');
        $stock       = (int)($_POST['stock'] ?? 0);
        $fabric_type = trim($_POST['fabric_type'] ?? '');
        $weight_gsm  = trim($_POST['weight_gsm'] ?? '');
        $width_inches= trim($_POST['width_inches'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $status      = $_POST['status'] === 'active' ? 'active' : 'inactive';
        $slug_input  = trim($_POST['slug'] ?? '');

        // Merge into form for re-render
        $form = compact(
            'name','category_id','short_desc','description',
            'price','sale_price','sku','stock',
            'fabric_type','weight_gsm','width_inches',
            'is_featured','status'
        );
        $form['slug']  = $slug_input;
        $form['image'] = $product['image'] ?? '';

        // Validate
        if ($name === '') $errors[] = 'Product name is required.';
        if ($price === '' || !is_numeric($price) || (float)$price < 0) $errors[] = 'Valid price is required.';
        if ($stock < 0) $errors[] = 'Stock cannot be negative.';

        if (empty($errors)) {
            // Slug
            $slug = $slug_input !== '' ? slugify($slug_input) : slugify($name);
            // Ensure unique slug (exclude current product when editing)
            $slug_check = $db->prepare(
                'SELECT id FROM products WHERE slug = ? AND id != ?'
            );
            $slug_check->execute([$slug, $id ?: 0]);
            if ($slug_check->fetch()) {
                $slug = $slug . '-' . time();
            }

            // Image upload
            $image_file = $product['image'] ?? '';
            if (!empty($_FILES['image']['name'])) {
                $allowed_types = ['image/jpeg','image/png','image/webp','image/gif'];
                $file_type = mime_content_type($_FILES['image']['tmp_name']);
                if (!in_array($file_type, $allowed_types, true)) {
                    $errors[] = 'Invalid image type. Allowed: JPG, PNG, WebP, GIF.';
                } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $errors[] = 'Image must be under 5 MB.';
                } else {
                    $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename  = $slug . '-' . time() . '.' . strtolower($ext);
                    $dest      = __DIR__ . '/../assets/images/' . $filename;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        // Remove old image if replacing
                        if (!empty($product['image']) && $product['image'] !== $filename) {
                            $old = __DIR__ . '/../assets/images/' . $product['image'];
                            if (file_exists($old)) @unlink($old);
                        }
                        $image_file = $filename;
                    } else {
                        $errors[] = 'Failed to upload image. Check folder permissions.';
                    }
                }
            }
        }

        if (empty($errors)) {
            $price_val      = (float)$price;
            $sale_price_val = ($sale_price !== '' && is_numeric($sale_price)) ? (float)$sale_price : null;
            $weight_val     = ($weight_gsm !== '' && is_numeric($weight_gsm)) ? (float)$weight_gsm : null;
            $width_val      = ($width_inches !== '' && is_numeric($width_inches)) ? (float)$width_inches : null;

            if ($id) {
                // Update
                $stmt = $db->prepare(
                    'UPDATE products SET
                        category_id = ?, name = ?, slug = ?, short_desc = ?, description = ?,
                        price = ?, sale_price = ?, sku = ?, stock = ?,
                        fabric_type = ?, weight_gsm = ?, width_inches = ?,
                        is_featured = ?, status = ?, image = ?
                     WHERE id = ?'
                );
                $stmt->execute([
                    $category_id ?: null, $name, $slug, $short_desc, $description,
                    $price_val, $sale_price_val, $sku, $stock,
                    $fabric_type, $weight_val, $width_val,
                    $is_featured, $status, $image_file,
                    $id
                ]);
                flash('success', 'Product updated successfully.');
            } else {
                // Insert
                $stmt = $db->prepare(
                    'INSERT INTO products
                        (category_id, name, slug, short_desc, description,
                         price, sale_price, sku, stock,
                         fabric_type, weight_gsm, width_inches,
                         is_featured, status, image, created_at)
                     VALUES (?,?,?,?,?, ?,?,?,?, ?,?,?, ?,?,?, NOW())'
                );
                $stmt->execute([
                    $category_id ?: null, $name, $slug, $short_desc, $description,
                    $price_val, $sale_price_val, $sku, $stock,
                    $fabric_type, $weight_val, $width_val,
                    $is_featured, $status, $image_file,
                ]);
                $id = (int)$db->lastInsertId();
                flash('success', 'Product added successfully.');
            }
            header('Location: ' . SITE_URL . '/admin/product-edit?id=' . $id);
            exit;
        }
    }
}

$page_title = $id ? 'Edit Product' : 'Add New Product';
admin_html_open($page_title, 'products');
?>

<div style="margin-bottom:1rem;">
    <a href="<?= h(SITE_URL) ?>/admin/products" class="btn btn-secondary btn-sm">← Back to Products</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="flash flash-error">
        <?php foreach ($errors as $e): ?>
            <div><?= h($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;align-items:start;">

        <!-- Left column -->
        <div>
            <div class="card" style="padding:1.25rem;">
                <div class="section-title" style="margin-top:0">Basic Information</div>

                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" value="<?= h($form['name']) ?>" required
                           oninput="autoSlug(this.value)">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">— Select category —</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= (string)$form['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>>
                                    <?= h($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="slug">URL Slug</label>
                        <input type="text" id="slug" name="slug" value="<?= h($form['slug']) ?>" placeholder="auto-generated">
                    </div>
                </div>

                <div class="form-group">
                    <label for="short_desc">Short Description</label>
                    <textarea id="short_desc" name="short_desc" style="min-height:60px"><?= h($form['short_desc']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="description">Full Description</label>
                    <textarea id="description" name="description" style="min-height:140px"><?= h($form['description']) ?></textarea>
                </div>
            </div>

            <div class="card" style="padding:1.25rem;">
                <div class="section-title" style="margin-top:0">Fabric Specifications</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fabric_type">Fabric Type</label>
                        <input type="text" id="fabric_type" name="fabric_type" value="<?= h($form['fabric_type']) ?>" placeholder="e.g. Pure Linen">
                    </div>
                    <div class="form-group">
                        <label for="weight_gsm">Weight (GSM)</label>
                        <input type="number" id="weight_gsm" name="weight_gsm" value="<?= h($form['weight_gsm']) ?>" min="0" step="0.01" placeholder="e.g. 150">
                    </div>
                    <div class="form-group">
                        <label for="width_inches">Width (inches)</label>
                        <input type="number" id="width_inches" name="width_inches" value="<?= h($form['width_inches']) ?>" min="0" step="0.01" placeholder="e.g. 58">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right column -->
        <div>
            <div class="card" style="padding:1.25rem;">
                <div class="section-title" style="margin-top:0">Pricing & Inventory</div>

                <div class="form-group">
                    <label for="price">Price (₹) *</label>
                    <input type="number" id="price" name="price" value="<?= h($form['price']) ?>" required min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="sale_price">Sale Price (₹)</label>
                    <input type="number" id="sale_price" name="sale_price" value="<?= h($form['sale_price']) ?>" min="0" step="0.01" placeholder="Leave blank for no sale">
                </div>
                <div class="form-group">
                    <label for="sku">SKU</label>
                    <input type="text" id="sku" name="sku" value="<?= h($form['sku']) ?>" placeholder="e.g. LIN-001">
                </div>
                <div class="form-group">
                    <label for="stock">Stock Quantity *</label>
                    <input type="number" id="stock" name="stock" value="<?= h($form['stock']) ?>" required min="0">
                </div>
            </div>

            <div class="card" style="padding:1.25rem;">
                <div class="section-title" style="margin-top:0">Image</div>
                <?php if (!empty($form['image'])): ?>
                    <img src="<?= h(SITE_URL) ?>/assets/images/<?= h($form['image']) ?>" alt="" style="width:100%;border-radius:6px;margin-bottom:.75rem;object-fit:cover;max-height:180px;border:1px solid #e5e7eb;">
                    <p style="font-size:.75rem;color:#6b7280;margin-bottom:.75rem;">Current: <?= h($form['image']) ?></p>
                <?php endif; ?>
                <div class="form-group" style="margin-bottom:0">
                    <label for="image"><?= !empty($form['image']) ? 'Replace Image' : 'Upload Image' ?></label>
                    <input type="file" id="image" name="image" accept="image/*" style="width:100%;font-size:.85rem;">
                    <p style="font-size:.72rem;color:#9ca3af;margin-top:.3rem;">JPG, PNG, WebP — max 5 MB</p>
                </div>
            </div>

            <div class="card" style="padding:1.25rem;">
                <div class="section-title" style="margin-top:0">Settings</div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active"   <?= $form['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $form['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= $form['is_featured'] ? 'checked' : '' ?>>
                        <label for="is_featured" style="text-transform:none;letter-spacing:0;font-size:.875rem;font-weight:500;">Mark as Featured</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;">
                <?= $id ? 'Update Product' : 'Add Product' ?>
            </button>
        </div>
    </div>
</form>

<script>
function slugify(s) {
    return s.toLowerCase().trim()
        .replace(/[^a-z0-9\-\s]/g, '')
        .replace(/[\s]+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
}
var slugManuallyEdited = false;
document.getElementById('slug').addEventListener('input', function() {
    slugManuallyEdited = this.value.trim() !== '';
});
function autoSlug(name) {
    if (!slugManuallyEdited) {
        document.getElementById('slug').value = slugify(name);
    }
}
</script>

<?php admin_html_close(); ?>
