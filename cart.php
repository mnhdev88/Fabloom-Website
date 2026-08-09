<?php
/**
 * Fabloom – Shopping Cart Page
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Your Cart – Fabloom';
$page_desc  = 'Review the items in your Fabloom shopping cart before proceeding to checkout.';

$items     = cart_items();
$has_items = count($items) > 0;
$subtotal  = cart_subtotal();
$shipping  = cart_shipping();
$total     = cart_total();
$count     = cart_count();

// Free-shipping progress (% toward ₹2000 threshold)
$threshold      = FREE_SHIPPING_ABOVE;
$progress_pct   = $shipping > 0
    ? (int) min(100, round(($subtotal / $threshold) * 100))
    : 100;
$amount_to_free = max(0.0, $threshold - $subtotal);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page Hero ──────────────────────────────────────────────────────── -->
<section class="page-hero" aria-labelledby="cart-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">Shopping</span>
    <h1 class="page-hero__title" id="cart-hero-title">Your Cart</h1>
    <p class="page-hero__subtitle">
      <?php if ($has_items): ?>
        <?= $count ?> <?= $count === 1 ? 'item' : 'items' ?> ready for checkout
      <?php else: ?>
        Your cart is currently empty
      <?php endif; ?>
    </p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <a href="<?= SITE_URL ?>/products">Products</a>
      <span class="sep" aria-hidden="true">›</span>
      <span class="current" aria-current="page">Cart</span>
    </nav>
  </div>
</section>

<!-- ── Main Content ───────────────────────────────────────────────────── -->
<section class="section section--sm" aria-label="Cart contents">
  <div class="container">

    <?php if ($has_items): ?>

    <div class="shop-layout">

      <!-- ── Cart Table ─────────────────────────────────────────────── -->
      <div class="shop-layout__main">

        <!-- Free-shipping progress nudge (only show when shipping is chargeable) -->
        <?php if ($shipping > 0): ?>
        <div class="free-ship-progress" role="status" aria-live="polite">
          <p class="free-ship-progress__label">
            Add <strong><?= h(fmt_price($amount_to_free)) ?></strong> more to qualify for <strong>FREE shipping</strong>
          </p>
          <div class="free-ship-progress__bar-bg" role="progressbar"
               aria-valuenow="<?= $progress_pct ?>" aria-valuemin="0" aria-valuemax="100"
               aria-label="Free shipping progress: <?= $progress_pct ?>%">
            <div class="free-ship-progress__bar" style="width:<?= $progress_pct ?>%"></div>
          </div>
        </div>
        <?php endif; ?>

        <div class="cart-table-wrap" role="region" aria-label="Cart items">
          <table class="cart-table" aria-label="Shopping cart">
            <thead>
              <tr>
                <th scope="col">Product</th>
                <th scope="col">Unit Price</th>
                <th scope="col">Quantity</th>
                <th scope="col">Total</th>
                <th scope="col"><span class="sr-only">Remove</span></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $pid => $item): ?>
              <?php
                $line_total   = $item['price'] * $item['qty'];
                $product_link = SITE_URL . '/product?id=' . (int) $item['id'];
                $img_src      = product_img($item['image'] ?? '');
              ?>
              <tr>
                <!-- Product info -->
                <td data-label="Product">
                  <div class="cart-product-cell">
                    <a href="<?= h($product_link) ?>" class="cart-thumb"
                       aria-label="View <?= h($item['name']) ?>">
                      <img
                        src="<?= h($img_src) ?>"
                        alt="<?= h($item['name']) ?>"
                        loading="lazy"
                        width="72" height="72"
                        onerror="this.src='<?= SITE_URL ?>/assets/images/linen-fabric-hero.webp'">
                    </a>
                    <div class="cart-product-info">
                      <a href="<?= h($product_link) ?>" class="cart-product-info__name">
                        <?= h($item['name']) ?>
                      </a>
                      <p class="cart-product-info__sub">
                        <?= h(fmt_price($item['price'])) ?> per <?= h($item['unit'] ?? 'm') ?>
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Unit price -->
                <td data-label="Price">
                  <span class="cart-price"><?= h(fmt_price($item['price'])) ?></span>
                </td>

                <!-- Quantity update form -->
                <td data-label="Quantity">
                  <form class="cart-qty-form" method="POST"
                        action="<?= SITE_URL ?>/api/cart.php"
                        aria-label="Update quantity for <?= h($item['name']) ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                    <?= csrf_field() ?>
                    <label for="qty-<?= (int) $item['id'] ?>" class="sr-only">
                      Quantity for <?= h($item['name']) ?>
                    </label>
                    <input
                      type="number"
                      id="qty-<?= (int) $item['id'] ?>"
                      name="qty"
                      class="cart-qty-input"
                      value="<?= (int) $item['qty'] ?>"
                      min="<?= (int) ($item['min_qty'] ?? 1) ?>"
                      step="1"
                      max="9999"
                      aria-label="Quantity">
                    <button type="submit" class="cart-update-btn" title="Update quantity" aria-label="Update quantity for <?= h($item['name']) ?>">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0115-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 01-15 6.7L3 16"/></svg>
                    </button>
                  </form>
                </td>

                <!-- Line total -->
                <td data-label="Total">
                  <span class="cart-line-total"><?= h(fmt_price($line_total)) ?></span>
                </td>

                <!-- Remove -->
                <td data-label="Remove">
                  <form method="POST" action="<?= SITE_URL ?>/api/cart.php"
                        aria-label="Remove <?= h($item['name']) ?> from cart">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="cart-remove-btn"
                            aria-label="Remove <?= h($item['name']) ?> from cart"
                            title="Remove item"
                            onclick="return confirm('Remove <?= h(addslashes($item['name'])) ?> from your cart?')">
                      &times;
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div><!-- /.cart-table-wrap -->

        <!-- Cart action bar -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-top:1.5rem;">
          <a href="<?= SITE_URL ?>/products" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            Continue Shopping
          </a>

          <form method="POST" action="<?= SITE_URL ?>/api/cart.php"
                aria-label="Clear entire cart">
            <input type="hidden" name="action" value="clear">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline"
                    style="border-color:var(--clr-warm-gray);color:var(--clr-text-muted);"
                    onclick="return confirm('Clear all items from your cart?')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
              Clear Cart
            </button>
          </form>
        </div>

      </div><!-- /.shop-layout__main -->

      <!-- ── Order Summary Sidebar ───────────────────────────────────── -->
      <aside class="shop-layout__sidebar" aria-label="Order summary">
        <div class="order-summary">
          <div class="order-summary__header">
            <h3>Order Summary</h3>
          </div>
          <div class="order-summary__body">
            <!-- Items subtotal -->
            <div class="order-summary__row">
              <span class="order-summary__row--label">
                Subtotal (<?= $count ?> <?= $count === 1 ? 'item' : 'items' ?>)
              </span>
              <span class="order-summary__row--value"><?= h(fmt_price($subtotal)) ?></span>
            </div>

            <!-- Shipping -->
            <div class="order-summary__row <?= $shipping === 0.0 ? 'order-summary__row--shipping-free' : '' ?>">
              <span class="order-summary__row--label">Shipping</span>
              <span class="order-summary__row--value">
                <?php if ($shipping === 0.0): ?>
                  <span class="free-ship-badge">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Free
                  </span>
                <?php else: ?>
                  <span class="ship-cost-badge"><?= h(fmt_price($shipping)) ?></span>
                <?php endif; ?>
              </span>
            </div>

            <div class="order-summary__divider" aria-hidden="true"></div>

            <!-- Grand total -->
            <div class="order-summary__total-row">
              <span class="order-summary__total-label">Grand Total</span>
              <span class="order-summary__total-amount"><?= h(fmt_price($total)) ?></span>
            </div>
          </div>

          <div class="order-summary__footer">
            <a href="<?= SITE_URL ?>/checkout" class="btn btn-primary"
               aria-label="Proceed to checkout">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
              Proceed to Checkout
            </a>
            <p class="order-summary__note">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
              Free shipping on all orders, PAN-India
            </p>
            <p class="order-summary__note">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Secure checkout · Fabloom
            </p>
          </div>
        </div>
      </aside>

    </div><!-- /.shop-layout -->

    <?php else: ?>

    <!-- ── Empty Cart State ────────────────────────────────────────── -->
    <div class="cart-empty" role="status">
      <div class="cart-empty__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/>
        </svg>
      </div>
      <h2>Your cart is empty</h2>
      <p>Looks like you haven't added any of our luxury fabrics yet.<br>Explore our collection and find something beautiful.</p>
      <a href="<?= SITE_URL ?>/products" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
        Continue Shopping
      </a>
    </div>

    <?php endif; ?>

  </div>
</section>

<!-- ── AJAX cart enhancement ──────────────────────────────────────────── -->
<script>
(function () {
  'use strict';

  const CSRF = <?= json_encode(csrf_token()) ?>;
  const API  = '<?= SITE_URL ?>/api/cart.php';

  /**
   * Refresh the cart-badge count shown in the navbar without a page reload.
   */
  function updateNavBadge(count) {
    document.querySelectorAll('.cart-badge').forEach(function (el) {
      el.textContent = count;
      el.style.display = count > 0 ? '' : 'none';
    });
  }

  /**
   * Generic fetch wrapper for cart API calls.
   */
  function cartFetch(formData) {
    return fetch(API, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-Token': CSRF },
      body: formData,
    }).then(function (r) { return r.json(); });
  }

  /**
   * Attach AJAX update to qty-update forms.
   */
  document.querySelectorAll('.cart-qty-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var fd = new FormData(form);
      var btn = form.querySelector('.cart-update-btn');
      if (btn) { btn.disabled = true; btn.textContent = '…'; }

      cartFetch(fd).then(function (data) {
        if (data.success) {
          // Reload page to reflect updated totals (simplest, reliable approach)
          window.location.reload();
        } else {
          alert(data.message || 'Could not update cart. Please try again.');
          if (btn) { btn.disabled = false; btn.textContent = '↻'; }
        }
      }).catch(function () {
        form.submit();
      });
    });
  });

  /**
   * Attach AJAX remove to remove forms.
   */
  document.querySelectorAll('form[aria-label^="Remove"]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var name = form.getAttribute('aria-label').replace('Remove ', '').replace(' from cart', '');
      if (!confirm('Remove ' + name + ' from your cart?')) return;

      var fd = new FormData(form);
      cartFetch(fd).then(function (data) {
        if (data.success) {
          updateNavBadge(data.cart_count);
          // Animate row out then reload
          var row = form.closest('tr');
          if (row) {
            row.style.transition = 'opacity 0.3s, transform 0.3s';
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(function () { window.location.reload(); }, 320);
          } else {
            window.location.reload();
          }
        } else {
          alert(data.message || 'Could not remove item. Please try again.');
        }
      }).catch(function () {
        form.submit();
      });
    });
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
