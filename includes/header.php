<?php
require_once __DIR__ . '/../includes/functions.php';
$_cart_count = cart_count();
$_user       = current_user();
$_flash_success = get_flash('success');
$_flash_error   = get_flash('error');
// Pages set these before including this file. All are optional.
//   $page_title, $page_desc, $page_canonical, $page_robots,
//   $page_og_image, $page_schema (raw JSON-LD string)
$page_title = $page_title ?? 'Fabloom – Premium Silk & Linen Fabric Manufacturer';
$page_desc  = $page_desc  ?? 'India\'s finest silk and linen fabric manufacturers from Bhagalpur, Bihar.';

// Canonical must never carry the query string: products.php?cat=silk&sort=price
// would otherwise declare itself canonical, minting a new "canonical" URL for
// every filter combination.
$_path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
if ($_path === '' || $_path === false) { $_path = '/'; }
$page_canonical = $page_canonical ?? (SITE_URL . $_path);

$page_robots   = $page_robots   ?? 'index, follow';
$page_og_image = $page_og_image ?? SITE_URL . '/assets/images/products/silk-bhagalpur.webp';
$page_schema   = $page_schema   ?? '';
?>
<!DOCTYPE html>
<html lang="en-IN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="<?= h($page_robots) ?>">
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_desc) ?>">
  <link rel="canonical" href="<?= h($page_canonical) ?>">

  <!-- Open Graph / Twitter -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= h($page_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($page_canonical) ?>">
  <meta property="og:image" content="<?= h($page_og_image) ?>">
  <meta property="og:site_name" content="<?= h(SITE_NAME) ?>">
  <meta property="og:locale" content="en_IN">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= h($page_title) ?>">
  <meta name="twitter:description" content="<?= h($page_desc) ?>">
  <meta name="twitter:image" content="<?= h($page_og_image) ?>">

<?php if (!empty($page_preload_image)): ?>
  <!-- LCP image. A CSS background is only discovered after the stylesheet has
       downloaded and parsed, by which point the browser has already lost the
       time it would have spent fetching it. Pages set $page_preload_image to
       hand it to the preload scanner in the first bytes of the response. -->
  <link rel="preload" as="image" href="<?= h($page_preload_image) ?>" fetchpriority="high">
<?php endif; ?>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=Great+Vibes&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/animations.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/shop.css') ?>">
<?php foreach (($page_extra_css ?? []) as $_css): ?>
  <!-- Page-specific stylesheet, requested via $page_extra_css. Keeps sheets
       that only two or three pages need out of the site-wide bundle. -->
  <link rel="stylesheet" href="<?= asset($_css) ?>">
<?php endforeach; ?>

  <link rel="icon" type="image/x-icon" href="<?= SITE_URL ?>/assets/favicon/favicon.ico">
  <link rel="apple-touch-icon" href="<?= SITE_URL ?>/assets/favicon/apple-touch-icon.png">

  <!-- Scroll-reveal starts content at opacity:0 and JavaScript fades it in.
       If a script fails to load or JS is off, the page would otherwise render
       completely blank, so make the content visible in that case. -->
  <noscript>
    <style>
      .reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-blur {
        opacity: 1 !important;
        transform: none !important;
        filter: none !important;
      }
    </style>
  </noscript>
<?php if ($page_schema !== ''): ?>

  <script type="application/ld+json">
<?= $page_schema ?>

  </script>
<?php endif; ?>
</head>
<body>

<?php if ($_flash_success): ?>
<div class="site-flash site-flash--success" role="alert" aria-live="polite">
  <?= h($_flash_success) ?>
  <button class="site-flash__close" onclick="this.parentElement.remove()" aria-label="Dismiss"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
</div>
<?php endif; ?>
<?php if ($_flash_error): ?>
<div class="site-flash site-flash--error" role="alert" aria-live="polite">
  <?= h($_flash_error) ?>
  <button class="site-flash__close" onclick="this.parentElement.remove()" aria-label="Dismiss"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
</div>
<?php endif; ?>

  <!-- Skip Link -->
  <a href="#main-content" class="skip-link">Skip to main content</a>

  <!-- TOP BAR -->
  <div class="topbar" role="complementary" aria-label="Contact and social links">
    <div class="topbar__inner">
      <div class="topbar__contact">
        <a href="tel:+919760058796" aria-label="Call us">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
          +91 97600 58796
        </a>
        <a href="mailto:info@thefabloom.com" class="topbar-email" aria-label="Email us">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          info@thefabloom.com
        </a>
      </div>
      <div class="topbar__socials">
        <a href="https://www.facebook.com/fabloom86/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
        <a href="https://www.instagram.com/thefabloom/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
        <a href="https://x.com/fabloomsilk" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
        <a href="https://www.linkedin.com/in/anas-sami-967639132/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg></a>
      </div>
    </div>
  </div>

  <!-- NAVBAR -->
  <header class="navbar" id="navbar" role="banner">
    <div class="navbar__inner">
      <a href="<?= SITE_URL ?>/" class="navbar__logo" aria-label="<?= h(SITE_NAME) ?> Home">
        <img src="<?= SITE_URL ?>/assets/images/logo-1.webp" alt="<?= h(SITE_NAME) ?>" height="52" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
        <div class="navbar__logo-text" style="display:none">
          <span class="brand-name"><?= h(BRAND_NAME) ?></span>
          <span class="brand-tagline"><?= h(BRAND_TAGLINE) ?></span>
        </div>
      </a>

      <nav class="nav-menu" role="navigation" aria-label="Main navigation">
        <div class="nav-item"><a href="<?= SITE_URL ?>/" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Home</a></div>
        <div class="nav-item"><a href="<?= SITE_URL ?>/about" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>">About</a></div>
        <div class="nav-item"><a href="<?= SITE_URL ?>/products" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">Products</a></div>
        <div class="nav-item"><a href="<?= SITE_URL ?>/infrastructure" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'infrastructure.php' ? 'active' : '' ?>">Infrastructure</a></div>
        <div class="nav-item"><a href="<?= SITE_URL ?>/blog" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'blog.php' ? 'active' : '' ?>">Blog</a></div>
        <div class="nav-item">
          <a href="<?= SITE_URL ?>/enquiry" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'enquiry.php' ? 'active' : '' ?>" aria-haspopup="true">
            Enquiry
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
          </a>
          <div class="nav-dropdown" role="menu">
            <a href="<?= SITE_URL ?>/enquiry#linen" role="menuitem">Linen Enquiry</a>
            <a href="<?= SITE_URL ?>/enquiry#silk" role="menuitem">Silk Enquiry</a>
          </div>
        </div>
        <div class="nav-item"><a href="<?= SITE_URL ?>/contact" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : '' ?>">Contact</a></div>
      </nav>

      <!-- Cart + Auth -->
      <div class="nav-actions">
        <a href="<?= SITE_URL ?>/cart" class="nav-cart" aria-label="Shopping cart (<?= $_cart_count ?> items)">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
          <?php if ($_cart_count > 0): ?>
            <span class="cart-badge"><?= $_cart_count ?></span>
          <?php endif; ?>
        </a>

        <?php if ($_user): ?>
          <div class="nav-item nav-item--user">
            <a href="<?= SITE_URL ?>/account/dashboard" class="nav-link nav-user-link">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <?= h(explode(' ', $_user['name'])[0]) ?>
            </a>
            <div class="nav-dropdown" role="menu">
              <a href="<?= SITE_URL ?>/account/dashboard" role="menuitem">My Account</a>
              <a href="<?= SITE_URL ?>/account/orders" role="menuitem">My Orders</a>
              <a href="<?= SITE_URL ?>/account/logout" role="menuitem">Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= SITE_URL ?>/account/login" class="btn btn-primary nav-cta desktop-only">Login / Register</a>
        <?php endif; ?>
      </div>

      <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Navigation menu">
    <div class="mobile-menu__header">
      <div class="navbar__logo-text"><span class="brand-name" style="color:white;font-size:1.5rem"><?= h(BRAND_NAME) ?></span><span class="brand-tagline"><?= h(BRAND_TAGLINE) ?></span></div>
      <button class="mobile-menu__close" id="menu-close" aria-label="Close menu"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <nav class="mobile-nav-links" aria-label="Mobile navigation">
      <a href="<?= SITE_URL ?>/">Home</a>
      <a href="<?= SITE_URL ?>/about">About Us</a>
      <a href="<?= SITE_URL ?>/products">Products</a>
      <a href="<?= SITE_URL ?>/infrastructure">Infrastructure</a>
      <a href="<?= SITE_URL ?>/blog">Blog</a>
      <a href="<?= SITE_URL ?>/enquiry">Enquiry</a>
      <div class="mobile-nav-sub">
        <a href="<?= SITE_URL ?>/enquiry#linen">Linen Enquiry</a>
        <a href="<?= SITE_URL ?>/enquiry#silk">Silk Enquiry</a>
      </div>
      <a href="<?= SITE_URL ?>/contact">Contact</a>
      <a href="<?= SITE_URL ?>/cart">Cart (<?= $_cart_count ?>)</a>
      <?php if ($_user): ?>
        <a href="<?= SITE_URL ?>/account/dashboard">My Account</a>
        <a href="<?= SITE_URL ?>/account/logout">Logout</a>
      <?php else: ?>
        <a href="<?= SITE_URL ?>/account/login">Login / Register</a>
      <?php endif; ?>
    </nav>
    <div class="mobile-menu__footer">
      <a href="<?= SITE_URL ?>/cart" class="btn btn-primary" style="text-align:center;justify-content:center">View Cart <?= $_cart_count > 0 ? "($($_cart_count))" : '' ?></a>
    </div>
  </div>

  <main id="main-content">
