<?php
/**
 * Blog index — original .html design, driven by includes/posts.php so a card
 * can only exist for a post that exists and each link carries its own slug.
 */
require_once __DIR__ . '/includes/functions.php';

$posts     = require __DIR__ . '/includes/posts.php';
$published = array_values(array_filter($posts, fn($p) => $p['published']));

$featured  = $published[0] ?? null;
$rest      = array_slice($published, 1);
$sidebar_posts = array_slice($published, 0, 3);

$page_title     = 'Fabloom Journal — Silk, Linen & Handloom Insights';
$page_desc      = 'Stories and guides on silk and linen from the looms of Bhagalpur — khadi linen, mul chanderi, hand brush work, block printing and tussar silk heritage.';
$page_canonical = SITE_URL . '/blog';
if ($featured) { $page_og_image = SITE_URL . '/assets/images/blog/' . $featured['image']; }

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'Blog',
            '@id'         => SITE_URL . '/blog',
            'name'        => 'Fabloom Journal',
            'description' => 'Stories and guides on silk and linen from Bhagalpur.',
            'publisher'   => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => SITE_URL],
            'blogPost'    => array_map(fn($p) => [
                '@type'         => 'BlogPosting',
                'headline'      => $p['title'],
                'url'           => SITE_URL . '/blog/' . $p['slug'],
                'datePublished' => $p['date'],
                'image'         => SITE_URL . '/assets/images/blog/' . $p['image'],
            ], $published),
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => SITE_URL . '/blog'],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

require_once __DIR__ . '/includes/header.php';
?>

    <!-- ── PAGE HERO ── -->
    <section class="page-hero" aria-label="Page header" style="position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;">
      <div style="position:absolute;inset:0;background-image:url('<?= SITE_URL ?>/assets/images/products/silk-bhagalpur.webp');background-size:cover;background-position:center;opacity:0.18;" aria-hidden="true"></div>
      <div class="container" style="position:relative;z-index:1;padding-top:5rem;padding-bottom:4rem;">
        <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom:1rem;">
          <a href="<?= SITE_URL ?>/">Home</a>
          <span aria-hidden="true">/</span>
          <span class="current">Blog</span>
        </nav>
        <span class="script-text" style="font-size:1.6rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">From the Mill</span>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2.25rem,5vw,3.5rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:1rem;">Insights &amp; Stories</h1>
        <p style="color:rgba(255,255,255,0.8);max-width:560px;line-height:1.8;">
          Fabric guides, weaving heritage and printing craft &mdash; written from our Bhagalpur unit.
        </p>
      </div>
    </section>

    <!-- ── BLOG MAIN ── -->
    <section class="section bg-cream" aria-labelledby="blog-main-heading">
      <div class="container">
        <div class="blog-layout">

          <!-- MAIN COLUMN -->
          <div>
            <?php if (!$featured): ?>
              <p style="color:var(--clr-text-secondary);">
                New articles are on the way. Meanwhile, <a href="<?= SITE_URL ?>/products">browse the fabric range</a>.
              </p>
            <?php else: ?>

            <div class="reveal" style="margin-bottom:3rem;">
              <span class="section-label">Featured</span>
              <h2 class="section-title" id="blog-main-heading">Latest Article</h2>
              <div class="gold-divider"></div>
            </div>

            <article class="blog-card blog-card--featured reveal">
              <a href="<?= SITE_URL ?>/blog/<?= h($featured['slug']) ?>" class="blog-card__img" style="height:100%;min-height:320px;display:block;">
                <img src="<?= SITE_URL ?>/assets/images/blog/<?= h($featured['image']) ?>"
                     alt="<?= h($featured['title']) ?>" loading="lazy" width="600" height="400"
                     style="width:100%;height:100%;object-fit:cover;">
              </a>
              <div class="blog-card__body" style="padding:2.5rem;display:flex;flex-direction:column;justify-content:center;">
                <div class="blog-card__meta">
                  <span class="blog-card__date"><time datetime="<?= h($featured['date']) ?>"><?= h($featured['display']) ?></time></span>
                  <span class="blog-card__date"><?= h($featured['read']) ?></span>
                </div>
                <h3 class="blog-card__title" style="font-size:1.6rem;">
                  <a href="<?= SITE_URL ?>/blog/<?= h($featured['slug']) ?>"><?= h($featured['title']) ?></a>
                </h3>
                <p class="blog-card__excerpt"><?= h($featured['excerpt']) ?></p>
                <a href="<?= SITE_URL ?>/blog/<?= h($featured['slug']) ?>" class="blog-card__read">
                  Read article
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
              </div>
            </article>

            <?php if ($rest): ?>
            <div class="text-center reveal" style="margin-bottom:2rem;">
              <h2 class="section-title" style="font-size:1.75rem;">More Articles</h2>
              <div class="gold-divider"></div>
            </div>

            <div class="blog-grid stagger-children">
              <?php foreach ($rest as $p): ?>
              <article class="blog-card reveal">
                <a href="<?= SITE_URL ?>/blog/<?= h($p['slug']) ?>" class="blog-card__img" style="display:block;">
                  <img src="<?= SITE_URL ?>/assets/images/blog/<?= h($p['image']) ?>"
                       alt="<?= h($p['title']) ?>" loading="lazy" width="400" height="250">
                </a>
                <div class="blog-card__body">
                  <div class="blog-card__meta">
                    <span class="blog-card__date"><time datetime="<?= h($p['date']) ?>"><?= h($p['display']) ?></time></span>
                    <span class="blog-card__date"><?= h($p['read']) ?></span>
                  </div>
                  <h3 class="blog-card__title"><a href="<?= SITE_URL ?>/blog/<?= h($p['slug']) ?>"><?= h($p['title']) ?></a></h3>
                  <p class="blog-card__excerpt"><?= h($p['excerpt']) ?></p>
                  <a href="<?= SITE_URL ?>/blog/<?= h($p['slug']) ?>" class="blog-card__read">
                    Read article
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                  </a>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
          </div>

          <!-- SIDEBAR -->
          <aside class="blog-sidebar" aria-label="Blog sidebar">

            <div class="card" style="padding:1.75rem;margin-bottom:2rem;border-radius:16px;">
              <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:1.25rem;color:var(--clr-charcoal);border-bottom:2px solid var(--clr-red);padding-bottom:0.75rem;">Topics</h3>
              <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:0.5rem;">
                <?php foreach ([
                    'Pure Silk' => 'silk', 'Pure Linen' => 'linen',
                    'Block Print' => 'block-print', 'Digital Print' => 'digital-print',
                    'Hand Brush Work' => 'hand-brush', 'Sarees' => 'saree',
                ] as $label => $cat): ?>
                <li>
                  <a href="<?= SITE_URL ?>/products?cat=<?= h($cat) ?>"
                     style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;min-height:44px;color:var(--clr-text-secondary);text-decoration:none;border-bottom:1px solid var(--clr-border);">
                    <span><?= h($label) ?></span>
                    <span aria-hidden="true" style="color:var(--clr-red);">&rsaquo;</span>
                  </a>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>

            <?php if ($sidebar_posts): ?>
            <div class="card" style="padding:1.75rem;margin-bottom:2rem;border-radius:16px;">
              <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:1.25rem;color:var(--clr-charcoal);border-bottom:2px solid var(--clr-red);padding-bottom:0.75rem;">Popular Posts</h3>
              <div style="display:flex;flex-direction:column;gap:1.25rem;">
                <?php foreach ($sidebar_posts as $sp): ?>
                <a href="<?= SITE_URL ?>/blog/<?= h($sp['slug']) ?>" style="display:flex;gap:1rem;text-decoration:none;align-items:flex-start;">
                  <img src="<?= SITE_URL ?>/assets/images/blog/<?= h($sp['image']) ?>" alt=""
                       loading="lazy" width="70" height="70"
                       style="width:70px;height:70px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                  <div>
                    <div style="font-size:0.875rem;font-weight:600;color:var(--clr-charcoal);line-height:1.4;margin-bottom:0.25rem;"><?= h($sp['title']) ?></div>
                    <div style="font-size:0.75rem;color:var(--clr-text-muted);"><?= h($sp['display']) ?></div>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

            <div style="padding:1.75rem;border-radius:16px;background:linear-gradient(135deg,#0F0F0F,#1C0E0E);color:white;text-align:center;">
              <h4 style="font-family:'Playfair Display',serif;font-size:1.1rem;margin-bottom:0.5rem;color:#fff;">Have a Fabric Question?</h4>
              <p style="font-size:0.875rem;color:rgba(255,255,255,0.75);line-height:1.7;margin-bottom:1.25rem;">
                Talk to our team about silk, linen and custom orders direct from the Bhagalpur mill.
              </p>
              <a href="<?= SITE_URL ?>/enquiry" class="btn btn-primary" style="width:100%;justify-content:center;">Send an Enquiry</a>
              <a href="tel:+919760058796" style="display:block;margin-top:0.9rem;color:#F08587;font-size:0.875rem;">+91 97600 58796</a>
            </div>

          </aside>

        </div>
      </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
