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
