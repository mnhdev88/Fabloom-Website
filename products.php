<?php
/**
 * products.php — Fabloom Product Catalogue
 * Sidebar filter (category, price range, sort) + 3-column grid + pagination
 */

require_once __DIR__ . '/includes/functions.php';

// ── Constants ──────────────────────────────────────────────────────────────
const PER_PAGE = 12;

// ── Sanitise GET params ────────────────────────────────────────────────────
$cat_slug   = trim($_GET['cat']       ?? '');
$min_price  = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price  = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$sort       = $_GET['sort'] ?? 'newest';
$page       = max(1, (int)($_GET['page'] ?? 1));

$allowed_sorts = ['newest', 'price_asc', 'price_desc', 'name_asc'];
if (!in_array($sort, $allowed_sorts, true)) {
    $sort = 'newest';
}

// ── Load categories for sidebar ────────────────────────────────────────────
$categories = db()
    ->query('SELECT id, name, slug FROM categories ORDER BY name ASC')
    ->fetchAll();

// ── Resolve active category ────────────────────────────────────────────────
$active_cat = null;
if ($cat_slug !== '') {
    foreach ($categories as $c) {
        if ($c['slug'] === $cat_slug) {
            $active_cat = $c;
            break;
        }
    }
    // If slug doesn't match any category, clear it
    if ($active_cat === null) {
        $cat_slug = '';
    }
}

// ── Build WHERE clause + bindings ──────────────────────────────────────────
$where    = ["p.status = 'active'"];
$bindings = [];

if ($active_cat !== null) {
    $where[]    = 'p.category_id = :cat_id';
    $bindings[':cat_id'] = (int)$active_cat['id'];
}

if ($min_price !== '') {
    $where[]    = 'COALESCE(NULLIF(p.sale_price, 0), p.price) >= :min_price';
    $bindings[':min_price'] = $min_price;
}

if ($max_price !== '') {
    $where[]    = 'COALESCE(NULLIF(p.sale_price, 0), p.price) <= :max_price';
    $bindings[':max_price'] = $max_price;
}

$where_sql = 'WHERE ' . implode(' AND ', $where);

// ── Sort clause ────────────────────────────────────────────────────────────
$order_sql = match ($sort) {
    'price_asc'  => 'ORDER BY COALESCE(NULLIF(p.sale_price, 0), p.price) ASC',
    'price_desc' => 'ORDER BY COALESCE(NULLIF(p.sale_price, 0), p.price) DESC',
    'name_asc'   => 'ORDER BY p.name ASC',
    default      => 'ORDER BY p.created_at DESC',
};

// ── Count total matching products ──────────────────────────────────────────
$count_sql  = "SELECT COUNT(*) FROM products p $where_sql";
$count_stmt = db()->prepare($count_sql);
$count_stmt->execute($bindings);
$total_products = (int)$count_stmt->fetchColumn();

// ── Pagination ─────────────────────────────────────────────────────────────
$pag    = paginate($total_products, PER_PAGE, $page);
$offset = $pag['offset'];

// ── Fetch products for current page ───────────────────────────────────────
$products_sql = "
    SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.image,
           p.short_desc, p.stock, p.is_featured,
           c.name AS category_name, c.slug AS category_slug
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    $where_sql
    $order_sql
    LIMIT :limit OFFSET :offset
";

$prod_stmt = db()->prepare($products_sql);
foreach ($bindings as $key => $val) {
    $prod_stmt->bindValue($key, $val);
}
$prod_stmt->bindValue(':limit',  PER_PAGE, PDO::PARAM_INT);
$prod_stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$prod_stmt->execute();
$products = $prod_stmt->fetchAll();

// ── Build pagination URL helper ────────────────────────────────────────────
function products_url(array $params = []): string {
    $defaults = [
        'cat'       => $_GET['cat']       ?? '',
        'min_price' => $_GET['min_price'] ?? '',
        'max_price' => $_GET['max_price'] ?? '',
        'sort'      => $_GET['sort']      ?? '',
        'page'      => '',
    ];
    $merged = array_merge($defaults, $params);
    $qs     = array_filter($merged, fn($v) => $v !== '');
    return SITE_URL . '/products' . ($qs ? '?' . http_build_query($qs) : '');
}

// ── Page meta ──────────────────────────────────────────────────────────────
$hero_title = $active_cat ? h($active_cat['name']) : 'Our Products';
// header.php escapes $page_title itself, so the raw name goes in here — h()
// twice turns an "&" in a category name into "&amp;amp;".
$page_title = ($active_cat ? $active_cat['name'] . ' — ' : '') . 'Products | Fabloom';
$page_desc  = $active_cat
    ? $active_cat['name'] . ' fabric direct from our Bhagalpur mill — woven, dyed and finished in house, supplied by the metre to designers, boutiques and exporters.'
    : 'Explore our premium collection of silk, linen and handwoven fabrics from Bhagalpur, Bihar.';

// header.php strips the query string when it builds a canonical, which is right
// for sort/page/price permutations but wrong for ?cat=: all six category views
// were pointing at /products, telling Google to drop the very URLs listed in
// sitemap.xml. A valid category self-canonicalises; everything else does not.
$_canon = $active_cat !== null
    ? SITE_URL . '/products?cat=' . rawurlencode($active_cat['slug'])
    : SITE_URL . '/products';
if ($active_cat !== null) {
    $page_canonical = $_canon;
}
// Sorted, paged and price-filtered views keep the canonical above: they are
// duplicates of the category (or of /products) and consolidate into it. No
// noindex here — noindex plus a canonical pointing elsewhere are contradictory
// instructions, and robots.txt already keeps crawlers off those permutations.

// ── Structured data ────────────────────────────────────────────────────────
// The catalogue and the six category views were the only page types on the
// site emitting no structured data at all: no breadcrumb trail, and nothing
// telling a crawler that the page is a list of specific, priced products.
require_once __DIR__ . '/includes/faq.php';

$_crumbs = [['name' => 'Home', 'item' => SITE_URL . '/'], ['name' => 'Products', 'item' => SITE_URL . '/products']];
if ($active_cat !== null) {
    $_crumbs[] = ['name' => $active_cat['name'], 'item' => $_canon];
}

// ItemList carries the products actually rendered on this page, in the order
// they appear, each with its price — enough for an answer engine to name and
// cost a product without fetching every product page.
$_list = [];
foreach ($products as $i => $_p) {
    $_eff = ($_p['sale_price'] !== null && $_p['sale_price'] > 0) ? $_p['sale_price'] : $_p['price'];
    $_list[] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'item'     => [
            '@type'  => 'Product',
            'name'   => $_p['name'],
            'url'    => SITE_URL . '/product/' . rawurlencode((string)$_p['slug']),
            'image'  => SITE_URL . '/assets/images/' . ltrim((string)$_p['image'], '/'),
            'offers' => [
                '@type'         => 'Offer',
                'price'         => number_format((float)$_eff, 2, '.', ''),
                'priceCurrency' => 'INR',
                'availability'  => 'https://schema.org/InStock',
            ],
        ],
    ];
}

$_faqs = $active_cat !== null
    ? array_merge(faq_for_category((string)$active_cat['slug']), array_slice(faq_general(), 0, 5))
    : faq_general();

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'CollectionPage',
            '@id'         => $_canon,
            'name'        => $active_cat ? $active_cat['name'] . ' Fabric' : 'Fabric Products',
            'url'         => $_canon,
            'description' => $page_desc,
            'isPartOf'    => ['@type' => 'WebSite', '@id' => SITE_URL . '/#website'],
            'about'       => ['@type' => 'Organization', '@id' => SITE_URL . '/#organization'],
            'mainEntity'  => [
                '@type'           => 'ItemList',
                'numberOfItems'   => count($_list),
                'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
                'itemListElement' => $_list,
            ],
        ],
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array_map(
                static fn(int $i, array $c): array => [
                    '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name'], 'item' => $c['item'],
                ],
                array_keys($_crumbs),
                $_crumbs
            ),
        ],
        faq_schema_node($_faqs, $_canon),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════
     PAGE HERO
════════════════════════════════════════════════════════════════════ -->
<section class="page-hero page-hero--products" aria-label="Products catalogue header">
  <div class="page-hero__overlay" aria-hidden="true"></div>
  <div class="container">
    <div class="page-hero__content">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <ol class="breadcrumb__list">
          <li class="breadcrumb__item"><a href="<?= SITE_URL ?>/">Home</a></li>
          <li class="breadcrumb__sep" aria-hidden="true">/</li>
          <?php if ($active_cat): ?>
            <li class="breadcrumb__item"><a href="<?= SITE_URL ?>/products">Products</a></li>
            <li class="breadcrumb__sep" aria-hidden="true">/</li>
            <li class="breadcrumb__item breadcrumb__item--active" aria-current="page"><?= h($active_cat['name']) ?></li>
          <?php else: ?>
            <li class="breadcrumb__item breadcrumb__item--active" aria-current="page">Products</li>
          <?php endif; ?>
        </ol>
      </nav>
      <h1 class="page-hero__title"><?= $hero_title ?></h1>
      <p class="page-hero__subtitle">
        <?php if ($active_cat): ?>
          Browse our <?= h($active_cat['name']) ?> collection — premium fabrics crafted in Bhagalpur.
        <?php else: ?>
          Discover premium silk, linen &amp; handwoven fabrics from the looms of Bhagalpur, Bihar.
        <?php endif; ?>
      </p>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════
     SHOP LAYOUT
════════════════════════════════════════════════════════════════════ -->
<section class="section section--shop" aria-label="Product catalogue">
  <div class="container">
    <div class="shop-layout">

      <!-- ── SIDEBAR ─────────────────────────────────────────────── -->
      <aside class="shop-sidebar" aria-label="Filter products">

        <!-- Mobile: toggle button -->
        <button class="sidebar-toggle" id="sidebarToggle" aria-expanded="false" aria-controls="shopFilters">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
          Filters
          <span class="sidebar-toggle__arrow" aria-hidden="true"></span>
        </button>

        <div class="sidebar-body" id="shopFilters">
          <form class="filter-form" method="GET" action="<?= SITE_URL ?>/products" id="filterForm" aria-label="Filter and sort products">

            <!-- ── Category ───────────────────── -->
            <div class="filter-section">
              <h3 class="filter-section__title">Category</h3>
              <ul class="filter-category-list" role="list">
                <li>
                  <a href="<?= products_url(['cat' => '', 'page' => '']) ?>"
                     class="filter-cat-link <?= $cat_slug === '' ? 'filter-cat-link--active' : '' ?>"
                     aria-current="<?= $cat_slug === '' ? 'true' : 'false' ?>">
                    All Products
                  </a>
                </li>
                <?php foreach ($categories as $cat): ?>
                  <li>
                    <a href="<?= products_url(['cat' => h($cat['slug']), 'page' => '']) ?>"
                       class="filter-cat-link <?= $cat_slug === $cat['slug'] ? 'filter-cat-link--active' : '' ?>"
                       aria-current="<?= $cat_slug === $cat['slug'] ? 'true' : 'false' ?>">
                      <?= h($cat['name']) ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
              <?php if ($cat_slug !== ''): ?>
                <input type="hidden" name="cat" value="<?= h($cat_slug) ?>">
              <?php endif; ?>
            </div>

            <!-- ── Price Range ────────────────── -->
            <div class="filter-section">
              <h3 class="filter-section__title">Price Range</h3>
              <div class="filter-price-inputs">
                <div class="filter-price-field">
                  <label for="min_price" class="filter-label">Min (<?= CURRENCY ?>)</label>
                  <input
                    type="number"
                    id="min_price"
                    name="min_price"
                    class="filter-input"
                    min="0"
                    step="1"
                    placeholder="0"
                    value="<?= $min_price !== '' ? h((string)$min_price) : '' ?>"
                  >
                </div>
                <span class="filter-price-sep" aria-hidden="true">—</span>
                <div class="filter-price-field">
                  <label for="max_price" class="filter-label">Max (<?= CURRENCY ?>)</label>
                  <input
                    type="number"
                    id="max_price"
                    name="max_price"
                    class="filter-input"
                    min="0"
                    step="1"
                    placeholder="Any"
                    value="<?= $max_price !== '' ? h((string)$max_price) : '' ?>"
                  >
                </div>
              </div>
            </div>

            <!-- ── Sort ───────────────────────── -->
            <div class="filter-section">
              <h3 class="filter-section__title">Sort By</h3>
              <select name="sort" id="sortSelect" class="filter-select" aria-label="Sort products by">
                <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest First</option>
                <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="name_asc"   <?= $sort === 'name_asc'   ? 'selected' : '' ?>>Name: A–Z</option>
              </select>
            </div>

            <!-- ── Buttons ────────────────────── -->
            <div class="filter-actions">
              <button type="submit" class="btn btn-primary filter-submit">Apply Filters</button>
              <a href="<?= SITE_URL ?>/products" class="btn btn-outline filter-reset">Clear All</a>
            </div>

          </form>
        </div><!-- /.sidebar-body -->
      </aside><!-- /.shop-sidebar -->

      <!-- ── MAIN CONTENT ────────────────────────────────────────── -->
      <div class="shop-main">

        <!-- Results bar -->
        <div class="shop-results-bar" role="status" aria-live="polite">
          <p class="shop-results-count">
            <?php if ($total_products === 0): ?>
              No products found
            <?php elseif ($total_products === 1): ?>
              1 product found
            <?php else: ?>
              <?= number_format($total_products) ?> products found
              <?php if ($pag['total_pages'] > 1): ?>
                &nbsp;&mdash;&nbsp; Page <?= $page ?> of <?= $pag['total_pages'] ?>
              <?php endif; ?>
            <?php endif; ?>
          </p>

          <!-- Inline sort (mirrors sidebar — updates form & submits) -->
          <div class="shop-sort-inline">
            <label for="sortInline" class="sr-only">Sort by</label>
            <select id="sortInline" class="filter-select filter-select--inline" aria-label="Sort products">
              <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest</option>
              <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Price ↑</option>
              <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price ↓</option>
              <option value="name_asc"   <?= $sort === 'name_asc'   ? 'selected' : '' ?>>A–Z</option>
            </select>
          </div>
        </div>

        <!-- Product grid -->
        <?php if (empty($products)): ?>
          <div class="shop-empty" role="alert">
            <div class="shop-empty__icon" aria-hidden="true">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <h2 class="shop-empty__title">No products found</h2>
            <p class="shop-empty__text">Try adjusting your filters or browse our full collection.</p>
            <a href="<?= SITE_URL ?>/products" class="btn btn-primary">View All Products</a>
          </div>

        <?php else: ?>
          <ul class="product-grid" role="list">
            <?php foreach ($products as $_i => $p):
              // The first row is above the fold on every breakpoint, so those
              // images are the LCP candidate. Lazy-loading them defers the
              // request until after layout, which is exactly backwards: the
              // browser has to discover, queue and fetch them before it can
              // paint. Everything below the first row stays lazy.
              $_eager    = $_i < 4;
              $sale      = !empty($p['sale_price']) && (float)$p['sale_price'] < (float)$p['price'];
              $eff_price = product_price($p);
              $disc      = discount_pct($p);
              $url       = product_url($p);
              $img       = product_img($p['image']);
              $in_stock  = product_in_stock($p);
            ?>
            <li class="product-card <?= !$in_stock ? 'product-card--out-of-stock' : '' ?>">

              <!-- Image wrapper -->
              <a href="<?= h($url) ?>" class="product-card__img-wrap" tabindex="-1" aria-hidden="true">
                <img
                  src="<?= h($img) ?>"
                  alt="<?= h($p['name']) ?>"
                  class="product-card__img"
                  loading="<?= $_eager ? 'eager' : 'lazy' ?>"
                  <?= $_eager ? 'fetchpriority="high"' : '' ?>
                  decoding="<?= $_eager ? 'sync' : 'async' ?>"
                  onerror="this.src='<?= SITE_URL ?>/assets/images/linen-fabric-hero.webp'"
                >
                <?php if ($sale && $disc > 0): ?>
                  <span class="badge badge-sale" aria-label="<?= $disc ?>% off"><?= $disc ?>% OFF</span>
                <?php endif; ?>
                <?php if (!empty($p['is_featured'])): ?>
                  <span class="badge badge-featured">Featured</span>
                <?php endif; ?>
                <?php if (!$in_stock): ?>
                  <span class="badge badge-sold-out">Sold Out</span>
                <?php endif; ?>
              </a>

              <!-- Card body -->
              <div class="product-card__body">
                <?php if (!empty($p['category_name'])): ?>
                  <a href="<?= products_url(['cat' => h($p['category_slug']), 'page' => '']) ?>"
                     class="product-card__category">
                    <?= h($p['category_name']) ?>
                  </a>
                <?php endif; ?>

                <h2 class="product-card__title">
                  <a href="<?= h($url) ?>"><?= h($p['name']) ?></a>
                </h2>

                <?php if (!empty($p['short_desc'])): ?>
                  <p class="product-card__desc"><?= h($p['short_desc']) ?></p>
                <?php endif; ?>

                <div class="product-card__price" aria-label="Price">
                  <?php if ($sale): ?>
                    <span class="price-old" aria-label="Original price <?= h(fmt_price((float)$p['price'])) ?>">
                      <?= h(fmt_price((float)$p['price'])) ?>
                    </span>
                    <span class="price-new"><?= fmt_price_unit($eff_price, $p) ?></span>
                  <?php else: ?>
                    <span class="price-new"><?= fmt_price_unit($eff_price, $p) ?></span>
                  <?php endif; ?>
                </div>

                <?php if ($note = min_order_note($p)): ?>
                  <p class="product-card__minorder"><?= h($note) ?></p>
                <?php endif; ?>

                <div class="product-card__actions">
                  <?php if ($in_stock): ?>
                    <form method="POST" action="<?= SITE_URL ?>/api/cart.php?action=add" class="product-card__cart-form">
                      <?= csrf_field() ?>
                      <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                      <input type="hidden" name="qty" value="<?= product_min_qty($p) ?>">
                      <input type="hidden" name="redirect" value="<?= h(SITE_URL . '/products?' . http_build_query(array_filter([
                        'cat'       => $cat_slug,
                        'min_price' => $min_price !== '' ? $min_price : null,
                        'max_price' => $max_price !== '' ? $max_price : null,
                        'sort'      => $sort !== 'newest' ? $sort : null,
                        'page'      => $page > 1 ? $page : null,
                      ]))) ?>">
                      <button type="submit" class="btn btn-primary product-card__add-btn" aria-label="Add <?= h($p['name']) ?> to cart">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
                        Add to Cart
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="product-card__out-of-stock-label">Out of Stock</span>
                  <?php endif; ?>

                  <a href="<?= h($url) ?>" class="btn btn-outline product-card__view-btn" aria-label="View details for <?= h($p['name']) ?>">View</a>
                </div>
              </div><!-- /.product-card__body -->
            </li>
            <?php endforeach; ?>
          </ul>

          <!-- ── Pagination ──────────────────────────────────────── -->
          <?php if ($pag['total_pages'] > 1): ?>
            <nav class="pagination" aria-label="Products pagination">
              <ul class="pagination__list">

                <!-- Prev -->
                <?php if ($pag['has_prev']): ?>
                  <li>
                    <a href="<?= products_url(['page' => $page - 1]) ?>"
                       class="pagination__btn pagination__btn--prev"
                       rel="prev"
                       aria-label="Previous page">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                      Prev
                    </a>
                  </li>
                <?php else: ?>
                  <li><span class="pagination__btn pagination__btn--disabled" aria-disabled="true">Prev</span></li>
                <?php endif; ?>

                <!-- Numbered pages -->
                <?php
                $start = max(1, $page - 2);
                $end   = min($pag['total_pages'], $page + 2);
                if ($start > 1):
                ?>
                  <li><a href="<?= products_url(['page' => 1]) ?>" class="pagination__btn">1</a></li>
                  <?php if ($start > 2): ?>
                    <li><span class="pagination__ellipsis" aria-hidden="true">&hellip;</span></li>
                  <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                  <li>
                    <?php if ($i === $page): ?>
                      <span class="pagination__btn pagination__btn--active" aria-current="page"><?= $i ?></span>
                    <?php else: ?>
                      <a href="<?= products_url(['page' => $i]) ?>" class="pagination__btn"><?= $i ?></a>
                    <?php endif; ?>
                  </li>
                <?php endfor; ?>

                <?php if ($end < $pag['total_pages']): ?>
                  <?php if ($end < $pag['total_pages'] - 1): ?>
                    <li><span class="pagination__ellipsis" aria-hidden="true">&hellip;</span></li>
                  <?php endif; ?>
                  <li><a href="<?= products_url(['page' => $pag['total_pages']]) ?>" class="pagination__btn"><?= $pag['total_pages'] ?></a></li>
                <?php endif; ?>

                <!-- Next -->
                <?php if ($pag['has_next']): ?>
                  <li>
                    <a href="<?= products_url(['page' => $page + 1]) ?>"
                       class="pagination__btn pagination__btn--next"
                       rel="next"
                       aria-label="Next page">
                      Next
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                  </li>
                <?php else: ?>
                  <li><span class="pagination__btn pagination__btn--disabled" aria-disabled="true">Next</span></li>
                <?php endif; ?>

              </ul>
            </nav>
          <?php endif; ?>
        <?php endif; ?>

      </div><!-- /.shop-main -->
    </div><!-- /.shop-layout -->
  </div><!-- /.container -->
</section>

<!-- Inline sort sync script (keeps sidebar and inline selects in sync) -->
<script>
(function () {
  'use strict';
  var form     = document.getElementById('filterForm');
  var sidebar  = document.getElementById('sortSelect');
  var inline   = document.getElementById('sortInline');
  var toggle   = document.getElementById('sidebarToggle');
  var filtersEl= document.getElementById('shopFilters');

  if (inline && sidebar && form) {
    inline.addEventListener('change', function () {
      sidebar.value = this.value;
      form.submit();
    });
    sidebar.addEventListener('change', function () {
      inline.value = this.value;
    });
  }

  if (toggle && filtersEl) {
    toggle.addEventListener('click', function () {
      var open = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', open ? 'false' : 'true');
      filtersEl.classList.toggle('sidebar-body--open', !open);
    });
  }
}());
</script>

<?php
faq_render(
    $_faqs,
    $active_cat ? 'About ' . $active_cat['name'] . ' Fabric' : 'Buying Fabric from Fabloom',
    $active_cat
        ? 'What buyers ask most often before ordering ' . strtolower($active_cat['name']) . ' from our Bhagalpur unit.'
        : 'Minimum quantities, custom dyeing, delivery and payment — answered by our team at the mill.'
);
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
