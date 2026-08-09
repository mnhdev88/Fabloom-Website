-- ============================================================
--  Fabloom — repoint seeded products at the authentic imagery
--  sourced from thefabloom.com (assets/images/products/).
--
--  Run this ONLY if you already ran setup/install.php and have
--  the original 10 sample products in your database. A fresh
--  install of setup/install.sql already contains these values.
--
--  Usage:  mysql -u root -p fabloom_db < setup/update-product-images.sql
-- ============================================================

-- Silk ---------------------------------------------------------
UPDATE products SET
  name       = 'Bhagalpur Heritage Silk',
  slug       = 'bhagalpur-heritage-silk',
  short_desc = 'The signature textured Bhagalpur silk — slubbed weave, natural lustre and extraordinary drape, woven on traditional handlooms.',
  fabric_type= 'Bhagalpur Silk',
  image      = 'products/silk-bhagalpur.webp'
WHERE slug = 'pure-mulberry-silk';

UPDATE products SET image = 'products/silk-tussar.webp'
WHERE slug = 'raw-tussar-silk';

-- Linen --------------------------------------------------------
UPDATE products SET
  name       = 'Hand-Woven Khadi Linen',
  slug       = 'hand-woven-khadi-linen',
  short_desc = 'Pure hand-woven khadi linen with a soft irregular slub and natural undyed finish. Breathable and hard-wearing.',
  image      = 'products/linen-khadi.webp'
WHERE slug = 'pure-belgian-linen';

UPDATE products SET
  name       = 'Texture Furnishing Linen',
  slug       = 'texture-furnishing-linen',
  short_desc = 'Heavyweight open-weave linen developed for furnishing and upholstery. Deep natural texture with excellent drape.',
  fabric_type= 'Pure Linen',
  weight_gsm = '220 GSM',
  image      = 'products/linen-furnishing.webp'
WHERE slug = 'linen-cotton-blend';

-- Block print --------------------------------------------------
UPDATE products SET
  name       = 'Kota Doria Block Print',
  slug       = 'kota-doria-block-print',
  short_desc = 'Pure Kota Doria hand block printed with chemical-free dyes. The open square-check ground keeps it feather-light.',
  fabric_type= 'Kota Doria',
  weight_gsm = '90 GSM',
  image      = 'products/block-kota-doria.webp'
WHERE slug = 'block-print-linen-indigo';

UPDATE products SET
  name       = 'Mul Chanderi Multiblock Print',
  slug       = 'mul-chanderi-multiblock-print',
  short_desc = 'Fine sheer mul chanderi built up through multiblock printing — each colour laid from a separate carved block.',
  fabric_type= 'Mul Chanderi',
  weight_gsm = '80 GSM',
  image      = 'products/block-mul-chanderi.webp'
WHERE slug = 'floral-block-print-cotton';

-- Digital print ------------------------------------------------
UPDATE products SET
  name       = 'Linen Print #5 — Scattered Posy on Slub',
  slug       = 'linen-print-5',
  short_desc = 'Small mixed posies scattered across a natural slub linen. Soft, muted and highly versatile.',
  price      = 1050.00,
  sale_price = NULL,
  fabric_type= 'Pure Linen',
  weight_gsm = '140 GSM',
  image      = 'products/print-linen-05.webp'
WHERE slug = 'digital-print-silk-georgette';

-- Hand brush ---------------------------------------------------
UPDATE products SET
  name       = 'Hand Brush Floral Work',
  slug       = 'hand-brush-floral-work',
  short_desc = 'Hand brush work: every stem and petal painted directly onto the cloth by our artisans. No two lengths are identical.',
  image      = 'products/hand-brush-floral.webp'
WHERE slug = 'hand-brush-linen';

-- Saree --------------------------------------------------------
UPDATE products SET
  name       = 'Hand-Crafted Silk Saree — Zari Border',
  slug       = 'hand-crafted-silk-saree-zari',
  short_desc = 'Handwoven silk saree finished with a traditional gold zari border. Made to order in custom body and border combinations.',
  fabric_type= 'Pure Silk',
  image      = 'products/saree-zari.webp'
WHERE slug = 'banarasi-silk-saree';

UPDATE products SET
  name       = 'Hand-Crafted Saree — Embroidered Border',
  slug       = 'hand-crafted-saree-embroidered',
  short_desc = 'Handwoven saree with a hand-embroidered multicolour border. Light, breathable and made in limited runs.',
  image      = 'products/saree-embroidered.webp'
WHERE slug = 'linen-saree-handloom';

-- Products with no equivalent in the original 10 seeds ---------
INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  2, 'Solid Natural Linen — Colour Range', 'solid-natural-linen-colour-range',
  'Our solid linen programme in natural, ecru and dyed colourways. Consistent shade depth batch to batch, dyed using eco-safe processes.',
  1199.00, NULL, 90, 'Pure Linen', '150 GSM', '58"', 1, 'products/linen-solid-dyed.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'solid-natural-linen-colour-range');




-- ── The full Linen Print range from thefabloom.com ────────────
--  19 digital prints, ₹1,050/m, inserted only if not already present.

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #1 — Coral Blossom on Slate', 'linen-print-1',
  'Dense coral and peach blossom clusters over a slate-blue ground. A full-cover repeat with plenty of movement.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-01.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-1');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #2 — Dressed Bear Childrenswear', 'linen-print-2',
  'Storybook bear characters scattered on natural linen. Developed for childrenswear and nursery furnishing.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-02.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-2');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #3 — Berry Vine on Slate', 'linen-print-3',
  'A trailing leaf-and-berry vine in ecru over slate blue. Quiet, directional and easy to cut.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-03.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-3');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #4 — Scooter Novelty Print', 'linen-print-4',
  'Orange scooters on a pale cream ground. A conversational novelty print for shirting and kidswear.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-04.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-4');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #5 — Scattered Posy on Slub', 'linen-print-5',
  'Small mixed posies scattered across a natural slub linen. Soft, muted and highly versatile.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-05.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-5');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #6 — Painterly Bloom on Periwinkle', 'linen-print-6',
  'Large painterly blooms in rose, saffron and jade over periwinkle. Our boldest print in the range.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-06.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-6');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #7 — Botanical Trail on Cream', 'linen-print-7',
  'A dense botanical trail in magenta and green on cream. Rich all-over coverage with fine line detail.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-07.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-7');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #8 — Character Print on Mint', 'linen-print-8',
  'A cartoon character repeat on soft mint linen. Licensed artwork - please confirm availability when enquiring.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-08.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-8');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #9 — Ochre Sprig on Texture', 'linen-print-9',
  'Ochre sprigs over a textured cream ground with a fine script overlay. Subtle at a distance, detailed up close.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-09.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-9');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #10 — Mixed Posy on White', 'linen-print-10',
  'Red, blue and yellow posies on a bright white linen. Crisp and fresh for summer shirting.',
  1050.00, 945.00, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-10.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-10');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #11 — Rose Bouquet on Blush', 'linen-print-11',
  'Rose and foliage bouquets on a blush ground. Soft-focus painterly rendering.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-11.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-11');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #12 — Goose Novelty on Brown', 'linen-print-12',
  'White geese marching across chocolate-brown linen. A characterful conversational print.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-12.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-12');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #13 — Blue Buti Repeat', 'linen-print-13',
  'A neat blue flower-head buti repeat on natural linen. Traditional layout, digitally printed for register accuracy.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-13.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-13');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #14 — Daisy Sprig on Taupe', 'linen-print-14',
  'Small white daisy sprigs on a warm taupe ground. Understated and easy to coordinate.',
  1050.00, 945.00, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-14.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-14');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #15 — Tulip Posy on Oatmeal', 'linen-print-15',
  'Tulip and forget-me-not posies on oatmeal linen. Gentle vintage palette.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-15.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-15');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #16 — Hen Novelty on Cream', 'linen-print-16',
  'Hens and roosters across a cream ground. A country-kitchen novelty print popular for furnishing.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-16.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-16');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #17 — Floral on Chrome Yellow', 'linen-print-17',
  'White and blue florals over a saturated chrome-yellow ground. High-impact colour.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-17.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-17');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #18 — Rose Bouquet on White', 'linen-print-18',
  'Classic pink rose bouquets on white linen. A perennial best-seller for dresses and blouses.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 1, 'products/print-linen-18.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-18');

INSERT INTO products
  (category_id, name, slug, short_desc, price, sale_price, stock, fabric_type, weight_gsm, width_inches, is_featured, image)
SELECT * FROM (SELECT
  4, 'Linen Print #19 — Watercolour Petal on Cream', 'linen-print-19',
  'Loose watercolour petals in coral and blush on cream. Soft, painterly and light.',
  1050.00, NULL, 60, 'Pure Linen', '140 GSM', '44"', 0, 'products/print-linen-19.webp') AS t
WHERE NOT EXISTS (SELECT 1 FROM products WHERE slug = 'linen-print-19');

-- Anything still pointing at a shared hero image falls back to
-- a real product photo rather than a duplicate.
UPDATE products SET image = 'products/linen-khadi.webp'
WHERE image IN ('linen-fabric-hero.webp', 'silk-fabric-hero.webp',
                'furnishing-linen-hero.webp', 'saree-hero.webp')
   OR image IS NULL OR image = '';
