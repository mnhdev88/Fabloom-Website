<?php
/**
 * Single blog post — original .html design (dark hero, article + sticky
 * sidebar, related posts). The slug selects the entry from includes/posts.php.
 */
require_once __DIR__ . '/includes/functions.php';

$posts = require __DIR__ . '/includes/posts.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$post = null;
foreach ($posts as $p) {
    if ($p['slug'] === $slug && $p['published']) { $post = $p; break; }
}

// Unknown or unpublished slug: back to the index rather than an empty shell.
if ($post === null) {
    header('Location: ' . SITE_URL . '/blog', true, 302);
    exit;
}

$others        = array_values(array_filter($posts, fn($p) => $p['published'] && $p['slug'] !== $post['slug']));
$sidebar_posts = array_slice($others, 0, 3);
$related       = array_slice($others, 0, 3);

$page_title     = $post['title'] . ' | Fabloom Journal';
$page_desc      = $post['excerpt'];
$page_canonical = SITE_URL . '/blog/' . $post['slug'];
$page_og_image  = SITE_URL . '/assets/images/blog/' . $post['image'];

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'            => 'BlogPosting',
            'headline'         => $post['title'],
            'description'      => $post['excerpt'],
            'image'            => $page_og_image,
            'datePublished'    => $post['date'],
            'dateModified'     => $post['date'],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $page_canonical],
            'author'    => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => SITE_URL],
            'publisher' => [
                '@type' => 'Organization',
                'name'  => SITE_NAME,
                'logo'  => ['@type' => 'ImageObject', 'url' => SITE_URL . '/assets/images/logo-1.webp'],
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => SITE_URL . '/blog'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $post['title'], 'item' => $page_canonical],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$share_url = rawurlencode($page_canonical);
$share_txt = rawurlencode($post['title']);

require_once __DIR__ . '/includes/header.php';
?>

    <!-- ── PAGE HERO ── -->
    <section class="page-hero" aria-label="Page header" style="position:relative;min-height:360px;display:flex;align-items:center;overflow:hidden;">
      <div style="position:absolute;inset:0;background-image:url('<?= SITE_URL ?>/assets/images/blog/<?= h($post['image']) ?>');background-size:cover;background-position:center;opacity:0.16;" aria-hidden="true"></div>
      <div class="container" style="position:relative;z-index:1;padding-top:5rem;padding-bottom:4rem;">
        <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom:1rem;">
          <a href="<?= SITE_URL ?>/">Home</a>
          <span aria-hidden="true">/</span>
          <a href="<?= SITE_URL ?>/blog">Blog</a>
          <span aria-hidden="true">/</span>
          <span class="current"><?= h($post['title']) ?></span>
        </nav>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(1.75rem,4.5vw,3rem);font-weight:800;color:#fff;line-height:1.2;max-width:820px;margin-bottom:1rem;"><?= h($post['title']) ?></h1>
        <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;color:rgba(255,255,255,0.75);font-size:0.9rem;">
          <time datetime="<?= h($post['date']) ?>"><?= h($post['display']) ?></time>
          <span aria-hidden="true">&middot;</span>
          <span><?= h($post['read']) ?></span>
        </div>
      </div>
    </section>

    <!-- ── ARTICLE ── -->
    <section class="section bg-cream" aria-label="Blog post content">
      <div class="container">
        <div class="blog-layout">

          <div>
            <article class="card article-card">
              <!-- The lead photo is cropped to a fixed band. Left to
                   height:auto these portrait sources (900x1600, 1200x1600)
                   rendered ~1550px tall — taller than the viewport, so the
                   article opened on a wall of fabric with no text in sight. -->
              <img class="article-lead"
                   src="<?= SITE_URL ?>/assets/images/blog/<?= h($post['image']) ?>"
                   alt="<?= h($post['title']) ?>" width="1200" height="675"
                   fetchpriority="high" decoding="async">

              <div class="article-body">
<?= $post['body'] ?>
              </div>

              <div style="margin-top:2.5rem;padding-top:1.5rem;border-top:1px solid var(--clr-border);display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
                <span style="font-size:0.85rem;font-weight:700;color:var(--clr-text-secondary);">Share:</span>
                <a href="https://wa.me/?text=<?= $share_txt ?>%20<?= $share_url ?>" target="_blank" rel="noopener noreferrer"
                   style="display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;padding:0.5rem 1rem;min-height:44px;background:#12803F;color:#fff;border-radius:8px;font-size:0.8125rem;font-weight:600;text-decoration:none;">WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>" target="_blank" rel="noopener noreferrer"
                   style="display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;padding:0.5rem 1rem;min-height:44px;background:#1877F2;color:#fff;border-radius:8px;font-size:0.8125rem;font-weight:600;text-decoration:none;">Facebook</a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?= $share_url ?>&amp;title=<?= $share_txt ?>" target="_blank" rel="noopener noreferrer"
                   style="display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;padding:0.5rem 1rem;min-height:44px;background:#0A66C2;color:#fff;border-radius:8px;font-size:0.8125rem;font-weight:600;text-decoration:none;">LinkedIn</a>
              </div>
            </article>

            <div style="margin-top:2rem;">
              <a href="<?= SITE_URL ?>/blog" class="btn btn-outline">&larr; All articles</a>
            </div>
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

<?php if ($related): ?>
    <!-- ── RELATED ── -->
    <section class="section bg-white" aria-labelledby="more-posts">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Keep Reading</span>
          <h2 class="section-title" id="more-posts">More Articles</h2>
          <div class="gold-divider"></div>
        </div>
        <div class="grid-3 mt-12 stagger-children">
          <?php foreach ($related as $m): ?>
          <article class="blog-card reveal">
            <a href="<?= SITE_URL ?>/blog/<?= h($m['slug']) ?>" class="blog-card__img" style="display:block;">
              <img src="<?= SITE_URL ?>/assets/images/blog/<?= h($m['image']) ?>"
                   alt="<?= h($m['title']) ?>" loading="lazy" width="400" height="250">
            </a>
            <div class="blog-card__body">
              <div class="blog-card__meta"><span class="blog-card__date"><?= h($m['display']) ?></span></div>
              <h3 class="blog-card__title"><a href="<?= SITE_URL ?>/blog/<?= h($m['slug']) ?>"><?= h($m['title']) ?></a></h3>
              <p class="blog-card__excerpt"><?= h($m['excerpt']) ?></p>
              <a href="<?= SITE_URL ?>/blog/<?= h($m['slug']) ?>" class="blog-card__read">Read article</a>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
