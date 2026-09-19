-- Block print repricing (2026-09-19)
-- Every block printed design is quoted at a flat 1799.00 per metre and the
-- category cuts from 5 metres instead of the standard 50-metre loom lot.
-- The 5-metre rule itself lives in MIN_ORDER_BY_CATEGORY in includes/functions.php;
-- this file only fixes the price and the minimum quoted in the prose.

UPDATE products p
  JOIN categories c ON c.id = p.category_id
   SET p.price = 1799.00,
       p.sale_price = NULL
 WHERE c.slug = 'block-print';

UPDATE products p
  JOIN categories c ON c.id = p.category_id
   SET p.description = REPLACE(p.description, 'Minimum 50 metres', 'Minimum 5 metres')
 WHERE c.slug = 'block-print';

-- ── Digital print (2026-09-19) ──────────────────────────────────────────────
-- Flat 1249.00 per metre and the same 5-metre cut as block print.
-- The two sale prices of 945.00 are cleared so the category carries one price.
UPDATE products p
  JOIN categories c ON c.id = p.category_id
   SET p.price = 1249.00,
       p.sale_price = NULL
 WHERE c.slug = 'digital-print';

-- The 19 descriptions phrase the minimum ten different ways ("Minimum order 50
-- metres", "a 50 metre minimum", "in a minimum of 50 metres" and so on), so a
-- single literal REPLACE would miss most of them. These four cover all 19.
-- Each pattern deliberately starts at "inimum" rather than "Minimum" so one
-- pass catches both the capitalised and the lower-case forms: REPLACE() is
-- case-sensitive. "order" and "of" are matched before the bare form, and
-- neither contains the bare form, so the order of nesting cannot double-apply.
UPDATE products p
  JOIN categories c ON c.id = p.category_id
   SET p.description =
         REPLACE(
           REPLACE(
             REPLACE(
               REPLACE(p.description,
                 'inimum order 50 metres', 'inimum order 5 metres'),
               'inimum of 50 metres',    'inimum of 5 metres'),
             'inimum 50 metres',         'inimum 5 metres'),
           '50 metre minimum',           '5 metre minimum')
 WHERE c.slug = 'digital-print';
