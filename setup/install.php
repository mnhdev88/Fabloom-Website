<?php
// ============================================================
// FABLOOM — Web Installer
// Visit: http://yoursite.com/setup/install.php
// DELETE this file after successful installation!
// ============================================================

define('DB_HOST_I', $_POST['db_host'] ?? 'localhost');
define('DB_USER_I', $_POST['db_user'] ?? '');
define('DB_PASS_I', $_POST['db_pass'] ?? '');
define('DB_NAME_I', $_POST['db_name'] ?? 'fabloom_db');

$step   = (int)($_GET['step'] ?? 1);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    try {
        // Connect without selecting a DB first
        $pdo = new PDO('mysql:host=' . DB_HOST_I . ';charset=utf8mb4', DB_USER_I, DB_PASS_I, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME_I . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `" . DB_NAME_I . "`");

        // Execute each statement individually — avoids all SQL parsing issues
        $statements = [
            "CREATE TABLE IF NOT EXISTS users (
                id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name       VARCHAR(100)  NOT NULL,
                email      VARCHAR(150)  NOT NULL UNIQUE,
                password   VARCHAR(255)  NOT NULL,
                phone      VARCHAR(20)   DEFAULT NULL,
                is_admin   TINYINT(1)    NOT NULL DEFAULT 0,
                created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",

            "CREATE TABLE IF NOT EXISTS categories (
                id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL UNIQUE
            ) ENGINE=InnoDB",

            "CREATE TABLE IF NOT EXISTS products (
                id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                category_id  INT UNSIGNED DEFAULT NULL,
                name         VARCHAR(200) NOT NULL,
                slug         VARCHAR(200) NOT NULL UNIQUE,
                short_desc   VARCHAR(500) DEFAULT NULL,
                description  TEXT         DEFAULT NULL,
                price        DECIMAL(10,2) NOT NULL,
                sale_price   DECIMAL(10,2) DEFAULT NULL,
                sku          VARCHAR(100)  DEFAULT NULL,
                stock        INT          NOT NULL DEFAULT 0,
                fabric_type  VARCHAR(100) DEFAULT NULL,
                weight_gsm   VARCHAR(50)  DEFAULT NULL,
                width_inches VARCHAR(50)  DEFAULT NULL,
                is_featured  TINYINT(1)   NOT NULL DEFAULT 0,
                status       ENUM('active','inactive') NOT NULL DEFAULT 'active',
                image        VARCHAR(500) DEFAULT NULL,
                created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
            ) ENGINE=InnoDB",

            "CREATE TABLE IF NOT EXISTS product_images (
                id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NOT NULL,
                image_path VARCHAR(500) NOT NULL,
                is_primary TINYINT(1)   NOT NULL DEFAULT 0,
                sort_order INT          NOT NULL DEFAULT 0,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB",

            "CREATE TABLE IF NOT EXISTS orders (
                id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id        INT UNSIGNED DEFAULT NULL,
                order_number   VARCHAR(50)  NOT NULL UNIQUE,
                status         ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
                subtotal       DECIMAL(10,2) NOT NULL,
                shipping       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                total          DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(50)   NOT NULL DEFAULT 'cod',
                payment_status ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
                name           VARCHAR(100)  NOT NULL,
                email          VARCHAR(150)  NOT NULL,
                phone          VARCHAR(20)   DEFAULT NULL,
                address        TEXT          NOT NULL,
                city           VARCHAR(100)  NOT NULL,
                state          VARCHAR(100)  NOT NULL,
                pincode        VARCHAR(10)   NOT NULL,
                notes          TEXT          DEFAULT NULL,
                created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB",

            "CREATE TABLE IF NOT EXISTS order_items (
                id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id   INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                name       VARCHAR(200) NOT NULL,
                price      DECIMAL(10,2) NOT NULL,
                quantity   INT          NOT NULL DEFAULT 1,
                FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB",

            "CREATE TABLE IF NOT EXISTS cart_items (
                id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id    INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                quantity   INT          NOT NULL DEFAULT 1,
                created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_product (user_id, product_id),
                FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB",

            // Seed categories
            "INSERT IGNORE INTO categories (name, slug) VALUES
                ('Pure Silk','silk'),
                ('Pure Linen','linen'),
                ('Block Print','block-print'),
                ('Digital Print','digital-print'),
                ('Hand Brush Work','hand-brush'),
                ('Saree','saree')",

            // Seed products
            "INSERT IGNORE INTO products (category_id,name,slug,short_desc,price,sale_price,stock,fabric_type,weight_gsm,width_inches,is_featured,image) VALUES
                (1,'Pure Mulberry Silk Fabric','pure-mulberry-silk','Finest mulberry silk woven on traditional handlooms of Bhagalpur.',2499.00,NULL,50,'Mulberry Silk','14 momme','44\"',1,'silk-fabric-hero.webp'),
                (1,'Raw Tussar Silk','raw-tussar-silk','Authentic Bhagalpuri Tussar silk with natural golden sheen.',1899.00,1699.00,30,'Tussar Silk','16 momme','44\"',1,'silk-fabric-hero.webp'),
                (2,'Pure Belgian Linen','pure-belgian-linen','Premium Belgian linen with a crisp, cool feel.',1299.00,NULL,80,'Pure Linen','150 GSM','58\"',1,'linen-fabric-hero.webp'),
                (2,'Linen Cotton Blend','linen-cotton-blend','Soft linen-cotton blend, breathable and easy-care.',899.00,799.00,60,'Linen Cotton','130 GSM','58\"',0,'linen-fabric-hero.webp'),
                (3,'Block Print Linen Indigo','block-print-linen-indigo','Hand block-printed on pure linen using natural indigo dye.',1599.00,NULL,40,'Block Print Linen','140 GSM','44\"',1,'furnishing-linen-hero.webp'),
                (3,'Floral Block Print Cotton','floral-block-print-cotton','Delicate floral motifs hand-stamped on fine cotton.',699.00,NULL,55,'Block Print Cotton','120 GSM','44\"',0,'furnishing-linen-hero.webp'),
                (4,'Digital Print Silk Georgette','digital-print-silk-georgette','Vibrant digital prints on feather-light silk georgette.',3299.00,2999.00,25,'Silk Georgette','12 momme','44\"',1,'silk-fabric-hero.webp'),
                (5,'Hand Brush Linen','hand-brush-linen','Artistic hand-brush strokes on pure linen.',1799.00,NULL,20,'Pure Linen','160 GSM','44\"',0,'linen-fabric-hero.webp'),
                (6,'Banarasi Silk Saree','banarasi-silk-saree','Handwoven Banarasi silk saree with zari border.',8999.00,7999.00,15,'Banarasi Silk','-','-',1,'saree-hero.webp'),
                (6,'Linen Saree Handloom','linen-saree-handloom','Elegant handloom linen saree, light and breathable.',3499.00,NULL,20,'Pure Linen','-','-',1,'saree-hero.webp')",

            // Seed admin user (password: Admin@123)
            "INSERT IGNORE INTO users (name,email,password,is_admin) VALUES
                ('Fabloom Admin','admin@thefabloom.com','\$2y\$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1)",
        ];

        foreach ($statements as $stmt) {
            $pdo->exec($stmt);
        }

        // Update config/database.php with entered values
        $config = file_get_contents(__DIR__ . '/../config/database.php');
        $config = preg_replace("/define\('DB_HOST',.*?\);/", "define('DB_HOST', '" . addslashes(DB_HOST_I) . "');", $config);
        $config = preg_replace("/define\('DB_USER',.*?\);/", "define('DB_USER', '" . addslashes(DB_USER_I) . "');", $config);
        $config = preg_replace("/define\('DB_PASS',.*?\);/", "define('DB_PASS', '" . addslashes(DB_PASS_I) . "');", $config);
        $config = preg_replace("/define\('DB_NAME',.*?\);/", "define('DB_NAME', '" . addslashes(DB_NAME_I) . "');", $config);

        // Update SITE_URL
        $site_url = rtrim($_POST['site_url'] ?? 'http://localhost/fabloom', '/');
        $config = preg_replace("/define\('SITE_URL',.*?\);/", "define('SITE_URL', '" . addslashes($site_url) . "');", $config);

        file_put_contents(__DIR__ . '/../config/database.php', $config);

        $success = true;
    } catch (PDOException $e) {
        $errors[] = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fabloom – Installer</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; background: #0F0F0F; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
    .card { background: #1C1917; border-radius: 1rem; padding: 2.5rem; max-width: 560px; width: 100%; border: 1px solid #2D2927; }
    h1 { font-size: 1.75rem; margin-bottom: 0.5rem; }
    .red { color: #C0282A; }
    .sub { color: rgba(255,255,255,0.5); font-size: 0.9rem; margin-bottom: 2rem; }
    label { display: block; font-size: 0.85rem; color: rgba(255,255,255,0.6); margin-bottom: 0.35rem; margin-top: 1rem; }
    input { width: 100%; padding: 0.65rem 0.9rem; background: #0F0F0F; border: 1px solid #3D3937; border-radius: 0.5rem; color: #fff; font-size: 0.95rem; }
    input:focus { outline: none; border-color: #C0282A; }
    button { margin-top: 1.5rem; width: 100%; padding: 0.85rem; background: #C0282A; color: #fff; border: none; border-radius: 0.5rem; font-size: 1rem; cursor: pointer; font-weight: 600; }
    button:hover { background: #D93B3D; }
    .error { background: rgba(192,40,42,0.15); border: 1px solid rgba(192,40,42,0.4); border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 1rem; font-size: 0.9rem; color: #ff8080; }
    .success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 0.5rem; padding: 1.5rem; text-align: center; }
    .success h2 { color: #10B981; margin-bottom: 0.5rem; }
    .warn { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 1rem; font-size: 0.85rem; color: #fbbf24; }
  </style>
</head>
<body>
<div class="card">
  <h1>Fabloom <span class="red">Installer</span></h1>
  <p class="sub">Set up the database for your ecommerce store</p>

  <?php if ($success): ?>
  <div class="success">
    <h2>✓ Installation Complete!</h2>
    <p style="color:rgba(255,255,255,0.6);margin:0.5rem 0 1rem;">Database created and configured successfully.</p>
    <p style="font-size:0.85rem;color:rgba(255,255,255,0.5);">Default admin credentials:<br><strong>Email:</strong> admin@thefabloom.com<br><strong>Password:</strong> Admin@123</p>
  </div>
  <div class="warn" style="margin-top:1rem;">
    ⚠ <strong>Important:</strong> Delete or rename <code>setup/install.php</code> immediately after installation to prevent security risks.
  </div>
  <a href="../index.php" style="display:block;text-align:center;margin-top:1.5rem;padding:0.75rem;background:#C0282A;color:#fff;border-radius:0.5rem;text-decoration:none;font-weight:600;">Go to Website →</a>
  <a href="../admin/login.php" style="display:block;text-align:center;margin-top:0.75rem;padding:0.75rem;background:#2D2927;color:#fff;border-radius:0.5rem;text-decoration:none;">Admin Panel →</a>

  <?php else: ?>
  <?php foreach ($errors as $e): ?><div class="error">⚠ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>

  <form method="POST" action="?step=2">
    <label for="db_host">Database Host</label>
    <input type="text" id="db_host" name="db_host" value="localhost" required>

    <label for="db_name">Database Name</label>
    <input type="text" id="db_name" name="db_name" value="fabloom_db" required>

    <label for="db_user">Database Username</label>
    <input type="text" id="db_user" name="db_user" placeholder="e.g. root" required>

    <label for="db_pass">Database Password</label>
    <input type="password" id="db_pass" name="db_pass" placeholder="Leave blank if no password">

    <label for="site_url">Site URL (no trailing slash)</label>
    <input type="url" id="site_url" name="site_url" placeholder="http://localhost/fabloom" required>

    <button type="submit">Install Fabloom Ecommerce →</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>
