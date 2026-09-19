-- Remove the Saree category and its products (2026-09-19)
--
-- Order matters. products.category_id is ON DELETE SET NULL, so dropping the
-- category first would not delete the sarees — it would orphan them into an
-- uncategorised state where they still appear in the catalogue. Products go
-- first, category second.
--
-- order_items.product_id is ON DELETE RESTRICT, so if a real order has ever
-- contained a saree the first DELETE fails and nothing is removed. That is the
-- intended behaviour: order history must not lose the line item it points at.
-- If it does fail, retire the products instead (status = 'inactive'), which
-- takes them off the site while leaving the rows for the order to reference.
--
-- cart_items.product_id is ON DELETE CASCADE, so any live shopper's cart simply
-- loses the saree line.

DELETE p FROM products p
  JOIN categories c ON c.id = p.category_id
 WHERE c.slug = 'saree';

DELETE FROM categories WHERE slug = 'saree';
