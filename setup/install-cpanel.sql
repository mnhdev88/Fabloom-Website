-- ============================================================
-- FABLOOM — cPanel / shared-hosting import file
--
-- Identical to install.sql, minus the CREATE DATABASE and USE
-- statements. Shared hosting does not grant the privilege to
-- create databases from SQL — you create it in cPanel first
-- (MySQL Databases), then import this file into it from
-- phpMyAdmin with that database already selected.
--
-- Importing install.sql instead will fail with:
--   #1044 Access denied for user '...' to database 'fabloom_db'
-- ============================================================

-- ── Users ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100)  NOT NULL,
  email        VARCHAR(150)  NOT NULL UNIQUE,
  password     VARCHAR(255)  NOT NULL,
  phone        VARCHAR(20)   DEFAULT NULL,
  is_admin     TINYINT(1)    NOT NULL DEFAULT 0,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── Categories ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL,
  slug  VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ── Products ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id   INT UNSIGNED DEFAULT NULL,
  name          VARCHAR(200) NOT NULL,
  slug          VARCHAR(200) NOT NULL UNIQUE,
  short_desc    VARCHAR(500) DEFAULT NULL,
  description   TEXT         DEFAULT NULL,
  price         DECIMAL(10,2) NOT NULL,
  sale_price    DECIMAL(10,2) DEFAULT NULL,
  sku           VARCHAR(100)  DEFAULT NULL,
  stock         INT          NOT NULL DEFAULT 0,
  fabric_type   VARCHAR(100) DEFAULT NULL,
  weight_gsm    VARCHAR(50)  DEFAULT NULL,
  width_inches  VARCHAR(50)  DEFAULT NULL,
  is_featured   TINYINT(1)   NOT NULL DEFAULT 0,
  status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
  image         VARCHAR(500) DEFAULT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Product Images ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_images (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id  INT UNSIGNED NOT NULL,
  image_path  VARCHAR(500) NOT NULL,
  is_primary  TINYINT(1)   NOT NULL DEFAULT 0,
  sort_order  INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Orders ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
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
) ENGINE=InnoDB;

-- ── Order Items ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id    INT UNSIGNED NOT NULL,
  product_id  INT UNSIGNED NOT NULL,
  name        VARCHAR(200) NOT NULL,
  price       DECIMAL(10,2) NOT NULL,
  quantity    INT          NOT NULL DEFAULT 1,
  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ── Cart (persistent for logged-in users) ────────────────────
CREATE TABLE IF NOT EXISTS cart_items (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  product_id  INT UNSIGNED NOT NULL,
  quantity    INT          NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_user_product (user_id, product_id),
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Every website form submission is written here BEFORE the notification
-- email is attempted, so an enquiry is never lost to a mail failure.
CREATE TABLE IF NOT EXISTS enquiries (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_type   VARCHAR(40)  NOT NULL,          -- contact | linen | silk | sample | home
  name        VARCHAR(150) NOT NULL,
  email       VARCHAR(190) DEFAULT NULL,
  phone       VARCHAR(40)  DEFAULT NULL,
  company     VARCHAR(190) DEFAULT NULL,
  subject     VARCHAR(200) DEFAULT NULL,
  message     TEXT         DEFAULT NULL,
  details     TEXT         DEFAULT NULL,      -- JSON of the form-specific fields
  page_url    VARCHAR(500) DEFAULT NULL,
  ip_address  VARCHAR(45)  DEFAULT NULL,
  user_agent  VARCHAR(255) DEFAULT NULL,
  mail_sent   TINYINT(1)   NOT NULL DEFAULT 0,
  mail_error  VARCHAR(255) DEFAULT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_created (created_at),
  KEY idx_type    (form_type),
  KEY idx_unsent  (mail_sent)
) ENGINE=InnoDB;

-- ── Seed: Categories ─────────────────────────────────────────
INSERT INTO categories (name, slug) VALUES
  ('Pure Silk',      'silk'),
  ('Pure Linen',     'linen'),
  ('Block Print',    'block-print'),
  ('Digital Print',  'digital-print'),
  ('Hand Brush Work','hand-brush'),
  ('Saree',          'saree');

-- ── Seed: Sample Products ────────────────────────────────────
INSERT INTO products (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image) VALUES
(1, 'Bhagalpur Heritage Silk', 'bhagalpur-heritage-silk', 'The signature textured Bhagalpur silk — slubbed weave, natural lustre and extraordinary drape, woven on traditional handlooms.', 2499.00, NULL, 50, 'Bhagalpur Silk', '14 momme', '44"', 1, 'products/silk-bhagalpur.webp'),
(1, 'Raw Tussar Silk', 'raw-tussar-silk', 'Authentic Bhagalpuri Tussar silk with a natural golden sheen. Perfect for ethnic and fusion wear.', 1899.00, 1699.00, 30, 'Tussar Silk', '16 momme', '44"', 1, 'products/silk-tussar.webp'),
(2, 'Hand-Woven Khadi Linen', 'hand-woven-khadi-linen', 'Pure hand-woven khadi linen with a soft irregular slub and natural undyed finish. Breathable and hard-wearing.', 1299.00, NULL, 80, 'Pure Linen', '150 GSM', '58"', 1, 'products/linen-khadi.webp'),
(2, 'Texture Furnishing Linen', 'texture-furnishing-linen', 'Heavyweight open-weave linen developed for furnishing and upholstery. Deep natural texture with excellent drape.', 1499.00, 1349.00, 60, 'Pure Linen', '220 GSM', '58"', 0, 'products/linen-furnishing.webp'),
(2, 'Solid Natural Linen — Colour Range', 'solid-natural-linen-colour-range', 'Our solid linen programme in natural, ecru and dyed colourways. Consistent shade depth batch to batch, dyed using eco-safe processes.', 1199.00, NULL, 90, 'Pure Linen', '150 GSM', '58"', 1, 'products/linen-solid-dyed.webp'),
(3, 'Kota Doria Block Print', 'kota-doria-block-print', 'Pure Kota Doria hand block printed with chemical-free dyes. The open square-check ground keeps it feather-light.', 1599.00, NULL, 40, 'Kota Doria', '90 GSM', '44"', 1, 'products/block-kota-doria.webp'),
(3, 'Mul Chanderi Multiblock Print', 'mul-chanderi-multiblock-print', 'Fine sheer mul chanderi built up through multiblock printing — each colour laid from a separate carved block.', 1799.00, NULL, 35, 'Mul Chanderi', '80 GSM', '44"', 0, 'products/block-mul-chanderi.webp'),
(4, 'Linen Print #1 — Coral Blossom on Slate', 'linen-print-1', 'Dense coral and peach blossom clusters over a slate-blue ground. A full-cover repeat with plenty of movement.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-01.webp'),
(4, 'Linen Print #2 — Dressed Bear Childrenswear', 'linen-print-2', 'Storybook bear characters scattered on natural linen. Developed for childrenswear and nursery furnishing.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-02.webp'),
(4, 'Linen Print #3 — Berry Vine on Slate', 'linen-print-3', 'A trailing leaf-and-berry vine in ecru over slate blue. Quiet, directional and easy to cut.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-03.webp'),
(4, 'Linen Print #4 — Scooter Novelty Print', 'linen-print-4', 'Orange scooters on a pale cream ground. A conversational novelty print for shirting and kidswear.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-04.webp'),
(4, 'Linen Print #5 — Scattered Posy on Slub', 'linen-print-5', 'Small mixed posies scattered across a natural slub linen. Soft, muted and highly versatile.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-05.webp'),
(4, 'Linen Print #6 — Painterly Bloom on Periwinkle', 'linen-print-6', 'Large painterly blooms in rose, saffron and jade over periwinkle. Our boldest print in the range.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-06.webp'),
(4, 'Linen Print #7 — Botanical Trail on Cream', 'linen-print-7', 'A dense botanical trail in magenta and green on cream. Rich all-over coverage with fine line detail.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-07.webp'),
(4, 'Linen Print #8 — Character Print on Mint', 'linen-print-8', 'A cartoon character repeat on soft mint linen. Licensed artwork - please confirm availability when enquiring.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-08.webp'),
(4, 'Linen Print #9 — Ochre Sprig on Texture', 'linen-print-9', 'Ochre sprigs over a textured cream ground with a fine script overlay. Subtle at a distance, detailed up close.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-09.webp'),
(4, 'Linen Print #10 — Mixed Posy on White', 'linen-print-10', 'Red, blue and yellow posies on a bright white linen. Crisp and fresh for summer shirting.', 1050.00, 945.00, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-10.webp'),
(4, 'Linen Print #11 — Rose Bouquet on Blush', 'linen-print-11', 'Rose and foliage bouquets on a blush ground. Soft-focus painterly rendering.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-11.webp'),
(4, 'Linen Print #12 — Goose Novelty on Brown', 'linen-print-12', 'White geese marching across chocolate-brown linen. A characterful conversational print.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-12.webp'),
(4, 'Linen Print #13 — Blue Buti Repeat', 'linen-print-13', 'A neat blue flower-head buti repeat on natural linen. Traditional layout, digitally printed for register accuracy.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-13.webp'),
(4, 'Linen Print #14 — Daisy Sprig on Taupe', 'linen-print-14', 'Small white daisy sprigs on a warm taupe ground. Understated and easy to coordinate.', 1050.00, 945.00, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-14.webp'),
(4, 'Linen Print #15 — Tulip Posy on Oatmeal', 'linen-print-15', 'Tulip and forget-me-not posies on oatmeal linen. Gentle vintage palette.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-15.webp'),
(4, 'Linen Print #16 — Hen Novelty on Cream', 'linen-print-16', 'Hens and roosters across a cream ground. A country-kitchen novelty print popular for furnishing.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-16.webp'),
(4, 'Linen Print #17 — Floral on Chrome Yellow', 'linen-print-17', 'White and blue florals over a saturated chrome-yellow ground. High-impact colour.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-17.webp'),
(4, 'Linen Print #18 — Rose Bouquet on White', 'linen-print-18', 'Classic pink rose bouquets on white linen. A perennial best-seller for dresses and blouses.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-18.webp'),
(4, 'Linen Print #19 — Watercolour Petal on Cream', 'linen-print-19', 'Loose watercolour petals in coral and blush on cream. Soft, painterly and light.', 1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-19.webp'),
(5, 'Hand Brush Floral Work', 'hand-brush-floral-work', 'Hand brush work: every stem and petal painted directly onto the cloth by our artisans. No two lengths are identical.', 1799.00, NULL, 20, 'Pure Linen', '160 GSM', '44"', 1, 'products/hand-brush-floral.webp'),
(6, 'Hand-Crafted Silk Saree — Zari Border', 'hand-crafted-silk-saree-zari', 'Handwoven silk saree finished with a traditional gold zari border. Made to order in custom body and border combinations.', 8999.00, 7999.00, 15, 'Pure Silk', '–', '–', 1, 'products/saree-zari.webp'),
(6, 'Hand-Crafted Saree — Embroidered Border', 'hand-crafted-saree-embroidered', 'Handwoven saree with a hand-embroidered multicolour border. Light, breathable and made in limited runs.', 3499.00, NULL, 20, 'Pure Linen', '–', '–', 1, 'products/saree-embroidered.webp');

-- ── Seed: Admin User (password: Admin@123) ───────────────────
INSERT INTO users (name, email, password, is_admin) VALUES
('Fabloom Admin', 'admin@thefabloom.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
