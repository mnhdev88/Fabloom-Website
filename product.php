<?php
/**
 * product.php — Fabloom Single Product Detail Page
 * Loads product by ?slug=, shows gallery, specs, add-to-cart, related products.
 */

require_once __DIR__ . '/includes/functions.php';

// ── Load product by slug ───────────────────────────────────────────────────
$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    // Permanent: /product has no content of its own and never will.
    header('Location: ' . SITE_URL . '/products', true, 301);
    exit;
}

$stmt = db()->prepare("
    SELECT p.*,
           c.name AS category_name,
           c.slug AS category_slug
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.slug = :slug
      AND p.status = 'active'
    LIMIT 1
");
$stmt->execute([':slug' => $slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $page_title = '404 – Product Not Found | Fabloom';
    $page_desc  = 'The requested product could not be found.';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="section" aria-label="Product not found">
      <div class="container">
        <div class="not-found">
          <div class="not-found__icon" aria-hidden="true">
            <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          </div>
          <h1 class="not-found__title">Product Not Found</h1>
          <p class="not-found__text">The product you are looking for is unavailable or has been removed.</p>
          <a href="<?= SITE_URL ?>/products" class="btn btn-primary">Browse Products</a>
        </div>
      </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ── Derived product values ─────────────────────────────────────────────────
$sale       = !empty($product['sale_price']) && (float)$product['sale_price'] < (float)$product['price'];
$eff_price  = product_price($product);
$disc       = discount_pct($product);
$stock      = (int)$product['stock'];

// Fabric sells by the metre against a per-category minimum (50 m for loom-lot
// yardage, 5 m for block print), so the old "max 10" cap would have put the
// ceiling below the floor and made ordering impossible.
$min_qty    = product_min_qty($product);
$max_qty    = product_max_qty($product);
$can_order  = product_in_stock($product);
$img        = product_img($product['image']);

// ── Additional gallery images ──────────────────────────────────────────────
$gallery_stmt = db()->prepare("
    SELECT image_path
    FROM product_images
    WHERE product_id = :pid
    ORDER BY is_primary DESC, sort_order ASC
    LIMIT 8
");
$gallery_stmt->execute([':pid' => (int)$product['id']]);
$gallery = $gallery_stmt->fetchAll();

// ── Related products (same category, exclude current) ─────────────────────
$related = [];
if (!empty($product['category_id'])) {
    $rel_stmt = db()->prepare("
        SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.image, p.short_desc, p.stock,
               c.name AS category_name, c.slug AS category_slug
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE p.category_id = :cat_id
          AND p.id <> :pid
          AND p.status = 'active'
        ORDER BY p.is_featured DESC, p.created_at DESC
        LIMIT 4
    ");
    $rel_stmt->execute([
        ':cat_id' => (int)$product['category_id'],
        ':pid'    => (int)$product['id'],
    ]);
    $related = $rel_stmt->fetchAll();
}

// ── Page meta ──────────────────────────────────────────────────────────────
$page_title = h($product['name']) . ' | Fabloom';
$page_desc  = !empty($product['short_desc'])
    ? h($product['short_desc'])
    : 'Premium fabric from Fabloom – crafted in Bhagalpur, Bihar.';

// header.php builds the canonical from REQUEST_URI with the query string
// stripped — correct for filtered listings, wrong here: it collapsed all 29
// products onto https://…/product, a URL that only redirects. Set it from the
// product itself.
$canonical      = product_url($product);
$page_canonical = $canonical;
$page_og_image  = $img;

// ── Structured data ────────────────────────────────────────────────────────
// Every value below is read off the product row; nothing is asserted that the
// page does not also show. No rating is emitted because there are no reviews.
$schema_product = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Product',
    '@id'         => $canonical . '#product',
    'name'        => $product['name'],
    'url'         => $canonical,
    'image'       => $img,
    'description' => (string)($product['description'] ?: $product['short_desc']),
    'sku'         => (string)($product['sku'] ?: 'FB-' . $product['id']),
    'brand'       => ['@type' => 'Brand', 'name' => BRAND_NAME],
    'manufacturer'=> ['@type' => 'Organization', 'name' => SITE_NAME],
];

if (!empty($product['category_name'])) {
    $schema_product['category'] = $product['category_name'];
}

// Weave/width live in free-text columns ("150 GSM", '44"'), so they are
// emitted as additionalProperty rather than forced into typed fields.
$props = [];
foreach (['Fabric' => 'fabric_type', 'Weight' => 'weight_gsm', 'Width' => 'width_inches'] as $label => $col) {
    $val = trim((string)($product[$col] ?? ''));
    if ($val !== '' && $val !== '–' && $val !== '-') {
        $props[] = ['@type' => 'PropertyValue', 'name' => $label, 'value' => $val];
    }
}
if ($props) {
    $schema_product['additionalProperty'] = $props;
}

$schema_product['offers'] = [
    '@type'           => 'Offer',
    'url'             => $canonical,
    'price'           => number_format($eff_price, 2, '.', ''),
    'priceCurrency'   => 'INR',
    'availability'    => $can_order
        ? 'https://schema.org/InStock'
        : 'https://schema.org/OutOfStock',
    'itemCondition'   => 'https://schema.org/NewCondition',
    'seller'          => ['@type' => 'Organization', 'name' => SITE_NAME],
    // Fabric is quoted per metre and sold in 50 m lots; sarees are pieces.
    'eligibleQuantity'=> [
        '@type'    => 'QuantitativeValue',
        'value'    => $min_qty,
        'unitCode' => product_is_metre($product) ? 'MTR' : 'C62',
    ],
];

$crumbs = [
    ['name' => 'Home',     'item' => SITE_URL . '/'],
    ['name' => 'Products', 'item' => SITE_URL . '/products'],
];
if (!empty($product['category_name'])) {
    $crumbs[] = [
        'name' => $product['category_name'],
        'item' => SITE_URL . '/products?cat=' . rawurlencode((string)$product['category_slug']),
    ];
}
$crumbs[] = ['name' => $product['name'], 'item' => $canonical];

$schema_crumbs = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => array_map(
        static fn(int $i, array $c): array => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $c['name'],
            'item'     => $c['item'],
        ],
        array_keys($crumbs),
        $crumbs
    ),
];

// ── FAQ ────────────────────────────────────────────────────────────────────
// Product-specific questions first (they carry this product's real width, GSM
// and minimum), then the category set, then the general buying questions. The
// same array feeds the visible block below and the FAQPage node here, so the
// markup can never claim an answer the page does not show.
require_once __DIR__ . '/includes/faq.php';

$product_faqs = array_merge(
    faq_for_product($product),
    faq_for_category((string)($product['category_slug'] ?? '')),
    array_slice(faq_general(), 0, 4)
);

$page_schema = json_encode(
    [$schema_product, $schema_crumbs, ['@context' => 'https://schema.org'] + faq_schema_node($product_faqs, $canonical)],
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════
     BREADCRUMB + HERO STRIP
════════════════════════════════════════════════════════════════════ -->
<div class="product-breadcrumb-bar">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <ol class="breadcrumb__list">
        <li class="breadcrumb__item"><a href="<?= SITE_URL ?>/">Home</a></li>
        <li class="breadcrumb__sep" aria-hidden="true">/</li>
        <li class="breadcrumb__item"><a href="<?= SITE_URL ?>/products">Products</a></li>
        <?php if (!empty($product['category_name'])): ?>
          <li class="breadcrumb__sep" aria-hidden="true">/</li>
          <li class="breadcrumb__item">
            <a href="<?= SITE_URL ?>/products?cat=<?= h($product['category_slug']) ?>">
              <?= h($product['category_name']) ?>
            </a>
          </li>
        <?php endif; ?>
        <li class="breadcrumb__sep" aria-hidden="true">/</li>
        <li class="breadcrumb__item breadcrumb__item--active" aria-current="page"><?= h($product['name']) ?></li>
      </ol>
    </nav>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════
     PRODUCT DETAIL
════════════════════════════════════════════════════════════════════ -->
<section class="section section--product-detail" aria-label="Product detail">
  <div class="container">
    <div class="product-detail">

      <!-- ── LEFT: Image Gallery ─────────────────────────────────── -->
      <div class="product-gallery" aria-label="Product images">

        <!-- Main image -->
        <div class="product-gallery__main">
          <img
            id="mainProductImg"
            src="<?= h($img) ?>"
            alt="<?= h($product['name']) ?>"
            class="product-gallery__main-img"
            loading="eager"
            decoding="async"
            onerror="this.src='<?= SITE_URL ?>/assets/images/linen-fabric-hero.webp'"
          >
          <?php if ($sale && $disc > 0): ?>
            <span class="badge badge-sale badge-sale--lg" aria-label="<?= $disc ?>% off"><?= $disc ?>% OFF</span>
          <?php endif; ?>
        </div>

        <!-- Thumbnails (only when extra images exist) -->
        <?php if (!empty($gallery)): ?>
          <div class="product-gallery__thumbs" role="list" aria-label="Product image thumbnails">
            <!-- Primary image thumb -->
            <button
              type="button"
              class="product-gallery__thumb product-gallery__thumb--active"
              aria-label="Main product image"
              data-src="<?= h($img) ?>"
            >
              <img src="<?= h($img) ?>" alt="" loading="lazy" aria-hidden="true">
            </button>
            <!-- Gallery thumbs -->
            <?php foreach ($gallery as $gi):
              $gsrc = SITE_URL . '/assets/images/' . htmlspecialchars($gi['image_path']);
            ?>
              <button
                type="button"
                class="product-gallery__thumb"
                aria-label="Alternate product image"
                data-src="<?= h($gsrc) ?>"
              >
                <img src="<?= h($gsrc) ?>" alt="" loading="lazy" aria-hidden="true"
                     onerror="this.parentElement.style.display='none'">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div><!-- /.product-gallery -->

      <!-- ── RIGHT: Product Info ─────────────────────────────────── -->
      <div class="product-info">

        <!-- Category badge -->
        <?php if (!empty($product['category_name'])): ?>
          <a href="<?= SITE_URL ?>/products?cat=<?= h($product['category_slug']) ?>"
             class="product-info__category-badge">
            <?= h($product['category_name']) ?>
          </a>
        <?php endif; ?>

        <!-- Title -->
        <h1 class="product-info__title"><?= h($product['name']) ?></h1>

        <!-- SKU -->
        <?php if (!empty($product['sku'])): ?>
          <p class="product-info__sku">SKU: <span><?= h($product['sku']) ?></span></p>
        <?php endif; ?>

        <!-- Price block -->
        <div class="product-info__price-block" aria-label="Pricing">
          <?php if ($sale): ?>
            <span class="price-new price-new--lg"><?= fmt_price_unit($eff_price, $product) ?></span>
            <span class="price-old price-old--lg" aria-label="Original price"><?= h(fmt_price((float)$product['price'])) ?></span>
            <span class="price-savings">You save <?= h(fmt_price((float)$product['price'] - $eff_price)) ?> (<?= $disc ?>%)</span>
          <?php else: ?>
            <span class="price-new price-new--lg"><?= fmt_price_unit($eff_price, $product) ?></span>
          <?php endif; ?>
        </div>

        <!-- Stock badge -->
        <div class="product-info__stock" aria-live="polite">
          <?php if (!$can_order): ?>
            <span class="stock-badge stock-badge--out">Out of Stock</span>
          <?php elseif (TRACK_STOCK && $stock <= 5): ?>
            <span class="stock-badge stock-badge--low">Only <?= $stock ?> left!</span>
          <?php else: ?>
            <span class="stock-badge stock-badge--in">
              <?= TRACK_STOCK ? 'In Stock (' . (int)$stock . ' available)' : 'In Stock &middot; woven to order' ?>
            </span>
          <?php endif; ?>
        </div>

        <!-- Short description -->
        <?php if (!empty($product['short_desc'])): ?>
          <p class="product-info__short-desc"><?= h($product['short_desc']) ?></p>
        <?php endif; ?>

        <!-- Add to Cart form -->
        <?php if ($can_order): ?>
          <form
            method="POST"
            action="<?= SITE_URL ?>/api/cart.php?action=add"
            class="product-info__cart-form"
            aria-label="Add to cart"
          >
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="redirect" value="<?= h(product_url($product)) ?>">

            <!-- Quantity selector -->
            <div class="product-info__qty-row">
              <label for="qty" class="product-info__qty-label">
                Quantity<?= product_is_metre($product) ? ' (metres)' : '' ?>
              </label>
              <div class="qty-control" role="group" aria-label="Quantity selector">
                <button type="button" class="qty-btn qty-btn--dec" aria-label="Decrease quantity">−</button>
                <input
                  type="number"
                  id="qty"
                  name="qty"
                  class="qty-input"
                  value="<?= $min_qty ?>"
                  min="<?= $min_qty ?>"
                  step="1"
                  max="<?= $max_qty ?>"
                  aria-label="Quantity"
                  readonly
                >
                <button type="button" class="qty-btn qty-btn--inc" aria-label="Increase quantity" data-max="<?= $max_qty ?>" data-min="<?= $min_qty ?>">+</button>
              </div>
            </div>

            <?php if ($note = min_order_note($product)): ?>
              <p class="product-info__minorder"><?= h($note) ?> &middot; priced per metre</p>
            <?php endif; ?>

            <!-- CTA buttons -->
            <div class="product-info__cta-row">
              <button type="submit" class="btn btn-primary product-info__add-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
                Add to Cart
              </button>
              <a href="<?= SITE_URL ?>/enquiry?product=<?= h(urlencode($product['name'])) ?>"
                 class="btn btn-outline product-info__enquire-btn">
                Enquire Now
              </a>
            </div>
          </form>
        <?php else: ?>
          <div class="product-info__out-of-stock-cta">
            <p class="product-info__oos-msg">This product is currently out of stock.</p>
            <a href="<?= SITE_URL ?>/enquiry?product=<?= h(urlencode($product['name'])) ?>"
               class="btn btn-outline">Enquire for Availability</a>
          </div>
        <?php endif; ?>

        <!-- Trust badges -->
        <ul class="product-info__trust" aria-label="Purchase assurances">
          <li class="trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Secure Checkout
          </li>
          <li class="trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Pan-India Shipping
          </li>
          <li class="trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
            Premium Quality Fabric
          </li>
        </ul>

      </div><!-- /.product-info -->
    </div><!-- /.product-detail -->

    <!-- ── TABS: Description + Specifications ─────────────────────── -->
    <div class="product-tabs" aria-label="Product details tabs">
      <div class="product-tabs__nav" role="tablist">
        <button
          class="product-tabs__tab product-tabs__tab--active"
          role="tab"
          id="tab-desc"
          aria-controls="tabpanel-desc"
          aria-selected="true"
        >Description</button>
        <button
          class="product-tabs__tab"
          role="tab"
          id="tab-specs"
          aria-controls="tabpanel-specs"
          aria-selected="false"
        >Specifications</button>
        <button
          class="product-tabs__tab"
          role="tab"
          id="tab-shipping"
          aria-controls="tabpanel-shipping"
          aria-selected="false"
        >Shipping &amp; Returns</button>
      </div>

      <!-- Description tab -->
      <div
        class="product-tabs__panel product-tabs__panel--active"
        role="tabpanel"
        id="tabpanel-desc"
        aria-labelledby="tab-desc"
      >
        <?php if (!empty($product['description'])): ?>
          <div class="product-description">
            <?= nl2br(h($product['description'])) ?>
          </div>
        <?php else: ?>
          <p class="product-tabs__empty">No detailed description available for this product.</p>
        <?php endif; ?>
      </div>

      <!-- Specifications tab -->
      <div
        class="product-tabs__panel"
        role="tabpanel"
        id="tabpanel-specs"
        aria-labelledby="tab-specs"
        hidden
      >
        <table class="product-specs-table">
          <caption class="sr-only">Product Specifications</caption>
          <tbody>
            <?php if (!empty($product['sku'])): ?>
              <tr>
                <th scope="row">SKU</th>
                <td><?= h($product['sku']) ?></td>
              </tr>
            <?php endif; ?>

            <?php if (!empty($product['fabric_type'])): ?>
              <tr>
                <th scope="row">Fabric Type</th>
                <td><?= h($product['fabric_type']) ?></td>
              </tr>
            <?php endif; ?>

            <?php if (!empty($product['weight_gsm'])): ?>
              <tr>
                <th scope="row">Weight</th>
                <td><?= h($product['weight_gsm']) ?> GSM</td>
              </tr>
            <?php endif; ?>

            <?php if (!empty($product['width_inches'])): ?>
              <tr>
                <th scope="row">Width</th>
                <td><?= h($product['width_inches']) ?> inches</td>
              </tr>
            <?php endif; ?>

            <?php if (!empty($product['category_name'])): ?>
              <tr>
                <th scope="row">Category</th>
                <td>
                  <a href="<?= SITE_URL ?>/products?cat=<?= h($product['category_slug']) ?>">
                    <?= h($product['category_name']) ?>
                  </a>
                </td>
              </tr>
            <?php endif; ?>

            <tr>
              <th scope="row">Availability</th>
              <td>
                <?php if (!$can_order): ?>
                  <span class="stock-badge stock-badge--out">Out of Stock</span>
                <?php elseif ($stock <= 5): ?>
                  <span class="stock-badge stock-badge--low">Only <?= $stock ?> left</span>
                <?php else: ?>
                  <span class="stock-badge stock-badge--in">In Stock</span>
                <?php endif; ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Shipping tab -->
      <div
        class="product-tabs__panel"
        role="tabpanel"
        id="tabpanel-shipping"
        aria-labelledby="tab-shipping"
        hidden
      >
        <div class="shipping-info">
          <div class="shipping-info__item">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <div>
              <strong>Standard Shipping</strong>
              <p>Orders are dispatched within 2–3 business days. Delivery in 5–7 business days across India.</p>
            </div>
          </div>
          <div class="shipping-info__item">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            <div>
              <strong>Free shipping on all orders</strong>
              <p>Despatched PAN-India from our Bhagalpur unit at no delivery cost.</p>
            </div>
          </div>
          <div class="shipping-info__item">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
            <div>
              <strong>Easy Returns</strong>
              <p>For any quality issues, please contact us within 7 days of delivery. Fabric cut to order is non-returnable.</p>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.product-tabs -->

  </div><!-- /.container -->
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     RELATED PRODUCTS
════════════════════════════════════════════════════════════════════ -->
<?php if (!empty($related)): ?>
<section class="section section--related" aria-label="Related products">
  <div class="container">
    <h2 class="section-title section-title--center">You May Also Like</h2>
    <?php if (!empty($product['category_name'])): ?>
      <p class="related-section__subtitle">More from our <?= h($product['category_name']) ?> collection</p>
    <?php endif; ?>

    <ul class="product-grid product-grid--related" role="list">
      <?php foreach ($related as $rp):
        $r_sale      = !empty($rp['sale_price']) && (float)$rp['sale_price'] < (float)$rp['price'];
        $r_eff_price = product_price($rp);
        $r_disc      = discount_pct($rp);
        $r_url       = product_url($rp);
        $r_img       = product_img($rp['image']);
        $r_in_stock  = product_in_stock($rp);
      ?>
      <li class="product-card <?= !$r_in_stock ? 'product-card--out-of-stock' : '' ?>">

        <a href="<?= h($r_url) ?>" class="product-card__img-wrap" tabindex="-1" aria-hidden="true">
          <img
            src="<?= h($r_img) ?>"
            alt="<?= h($rp['name']) ?>"
            class="product-card__img"
            loading="lazy"
            decoding="async"
            onerror="this.src='<?= SITE_URL ?>/assets/images/linen-fabric-hero.webp'"
          >
          <?php if ($r_sale && $r_disc > 0): ?>
            <span class="badge badge-sale" aria-label="<?= $r_disc ?>% off"><?= $r_disc ?>% OFF</span>
          <?php endif; ?>
          <?php if (!$r_in_stock): ?>
            <span class="badge badge-sold-out">Sold Out</span>
          <?php endif; ?>
        </a>

        <div class="product-card__body">
          <?php if (!empty($rp['category_name'])): ?>
            <span class="product-card__category"><?= h($rp['category_name']) ?></span>
          <?php endif; ?>

          <h3 class="product-card__title">
            <a href="<?= h($r_url) ?>"><?= h($rp['name']) ?></a>
          </h3>

          <div class="product-card__price" aria-label="Price">
            <?php if ($r_sale): ?>
              <span class="price-old" aria-label="Original price"><?= h(fmt_price((float)$rp['price'])) ?></span>
              <span class="price-new"><?= fmt_price_unit($r_eff_price, $rp) ?></span>
            <?php else: ?>
              <span class="price-new"><?= fmt_price_unit($r_eff_price, $rp) ?></span>
            <?php endif; ?>
          </div>

          <div class="product-card__actions">
            <?php if ($r_in_stock): ?>
              <form method="POST" action="<?= SITE_URL ?>/api/cart.php?action=add" class="product-card__cart-form">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$rp['id'] ?>">
                <input type="hidden" name="qty" value="<?= product_min_qty($rp) ?>">
                <input type="hidden" name="redirect" value="<?= h(product_url($product)) ?>">
                <button type="submit" class="btn btn-primary product-card__add-btn" aria-label="Add <?= h($rp['name']) ?> to cart">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
                  Add to Cart
                </button>
              </form>
            <?php else: ?>
              <span class="product-card__out-of-stock-label">Out of Stock</span>
            <?php endif; ?>
            <a href="<?= h($r_url) ?>" class="btn btn-outline product-card__view-btn">View</a>
          </div>
        </div><!-- /.product-card__body -->

      </li>
      <?php endforeach; ?>
    </ul>

    <div class="related-section__cta">
      <a href="<?= SITE_URL ?>/products<?= !empty($product['category_slug']) ? '?cat=' . h($product['category_slug']) : '' ?>"
         class="btn btn-outline">
        View All <?= !empty($product['category_name']) ? h($product['category_name']) : 'Products' ?>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
// Product questions carrying this product's own width, GSM and minimum, plus
// the category set. Answer engines quote a passage that stands alone, so the
// numbers have to be in the answer rather than in a spec table above it.
faq_render(
    $product_faqs,
    'Questions about ' . $product['name'],
    'Answered by our team in Bhagalpur. Ask anything not covered here and we will reply from the mill.'
);
?>

<!-- Quantity control + tab switcher script -->
<script>
(function () {
  'use strict';

  /* ── Quantity control ────────────────────────────────────────── */
  var qtyInput = document.getElementById('qty');
  if (qtyInput) {
    var maxQty = parseInt(qtyInput.getAttribute('max'), 10) || 10;
    var minQty = parseInt(qtyInput.getAttribute('min'), 10) || 1;
    /* Step in round multiples of the item's own minimum: 10 m for the 50 m
       loom-lot fabrics, 5 m for block print, 1 for sarees. Stepping a 5 m
       minimum by 10 would land on 5, 15, 25 and never a round length. */
    var stepQty = minQty >= 50 ? 10 : (minQty > 1 ? minQty : 1);

    function syncButtons() {
      var v = parseInt(qtyInput.value, 10) || minQty;
      document.querySelector('.qty-btn--dec').disabled = v <= minQty;
      document.querySelector('.qty-btn--inc').disabled = v >= maxQty;
    }

    document.querySelectorAll('.qty-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var current = parseInt(qtyInput.value, 10) || minQty;
        if (this.classList.contains('qty-btn--inc')) {
          qtyInput.value = Math.min(maxQty, current + stepQty);
        } else {
          qtyInput.value = Math.max(minQty, current - stepQty);
        }
        syncButtons();
      });
    });
    syncButtons();
  }

  /* ── Image gallery ───────────────────────────────────────────── */
  var mainImg = document.getElementById('mainProductImg');
  if (mainImg) {
    document.querySelectorAll('.product-gallery__thumb').forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        mainImg.src = this.getAttribute('data-src');
        document.querySelectorAll('.product-gallery__thumb').forEach(function (t) {
          t.classList.remove('product-gallery__thumb--active');
        });
        this.classList.add('product-gallery__thumb--active');
      });
    });
  }

  /* ── Tab switcher ────────────────────────────────────────────── */
  var tabs     = document.querySelectorAll('.product-tabs__tab');
  var panels   = document.querySelectorAll('.product-tabs__panel');

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var targetId = this.getAttribute('aria-controls');

      tabs.forEach(function (t) {
        t.classList.remove('product-tabs__tab--active');
        t.setAttribute('aria-selected', 'false');
      });
      panels.forEach(function (p) {
        p.classList.remove('product-tabs__panel--active');
        p.hidden = true;
      });

      this.classList.add('product-tabs__tab--active');
      this.setAttribute('aria-selected', 'true');

      var target = document.getElementById(targetId);
      if (target) {
        target.classList.add('product-tabs__panel--active');
        target.hidden = false;
      }
    });
  });

}());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
