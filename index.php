<?php
require_once __DIR__ . '/includes/functions.php';

// Title was 82 characters and the description 217 — both truncated in the
// SERP, so the words past the cut were doing no work. Trimmed to ~60 and ~155,
// with the two things a buyer actually filters on kept in front: what we make
// and where we make it.
$page_title = 'Silk & Linen Fabric Manufacturer, Bhagalpur | Fabloom';
$page_desc  = "Silk and linen fabric direct from our Bhagalpur mill — handloom, block print, digital print and custom shades, by the metre from 50 m. Request a swatch.";
$page_canonical = SITE_URL . '/';
// First hero slide — the LCP element on this page.
$page_preload_image = SITE_URL . '/assets/images/slide1-jpg.webp';
$page_schema = <<<'JSONLD'
{
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "https://www.thefabloom.com/#organization",
        "name": "Fabloom Group of Company",
        "url": "https://www.thefabloom.com",
        "logo": "https://www.thefabloom.com/assets/images/logo-1.webp",
        "description": "India's premier silk and linen fabric manufacturer based in Bhagalpur, Bihar. Specialising in handloom fabrics, block printing, digital printing, and custom textile solutions.",
        "foundingLocation": "Bhagalpur, Bihar, India",
        "sameAs": [
          "https://www.facebook.com/fabloom86/",
          "https://www.instagram.com/thefabloom/",
          "https://x.com/fabloomsilk",
          "https://www.linkedin.com/in/anas-sami-967639132/"
        ],
        "contactPoint": [
          { "@type": "ContactPoint", "telephone": "+91-97600-58796", "contactType": "customer service", "areaServed": "IN", "availableLanguage": ["English","Hindi"] }
        ]
      },
      {
        "@type": "LocalBusiness",
        "@id": "https://www.thefabloom.com/#localbusiness",
        "name": "Fabloom Group of Company",
        "image": "https://www.thefabloom.com/assets/images/logo-1.webp",
        "url": "https://www.thefabloom.com",
        "telephone": "+919760058796",
        "email": "info@thefabloom.com",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "Mohiuddin Pur, Post–Habibpur",
          "addressLocality": "Bhagalpur",
          "addressRegion": "Bihar",
          "postalCode": "813113",
          "addressCountry": "IN"
        },
        "geo": { "@type": "GeoCoordinates", "latitude": 25.2260481, "longitude": 86.9755285 },
        "openingHoursSpecification": [
          { "@type": "OpeningHoursSpecification", "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"], "opens": "09:00", "closes": "18:00" }
        ],
        "aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": "4.8",
          "reviewCount": "47",
          "bestRating": "5"
        },
        "priceRange": "₹₹",
        "currenciesAccepted": "INR",
        "paymentAccepted": "Cash, Bank Transfer, UPI",
        "hasMap": "https://maps.app.goo.gl/TUQdwxNjSqC5y5e77"
      },
      {
        "@type": "WebSite",
        "@id": "https://www.thefabloom.com/#website",
        "url": "https://www.thefabloom.com",
        "name": "Fabloom Group of Company",
        "publisher": { "@id": "https://www.thefabloom.com/#organization" }
      }
    ]
  }
JSONLD;

// ── FAQ ──────────────────────────────────────────────────────
// Folded into the existing @graph rather than emitted as a second <script>,
// so the Organization, LocalBusiness and FAQPage nodes stay in one connected
// graph — that is what lets an answer engine attribute the answers to the
// business rather than to a loose page.
require_once __DIR__ . '/includes/faq.php';

$home_faqs = faq_general();

$_graph = json_decode($page_schema, true);
if (is_array($_graph) && isset($_graph['@graph'])) {
    $_faq_node = faq_schema_node($home_faqs, SITE_URL . '/');
    $_faq_node['about'] = ['@id' => SITE_URL . '/#organization'];
    $_graph['@graph'][] = $_faq_node;
    $page_schema = json_encode($_graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

// ── Featured products for the grid below ─────────────────────
$featured = [];
try {
    $pdo = db_optional();
    if ($pdo) {
        $featured = $pdo->query(
            'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.is_featured = 1 AND p.status = "active"
             ORDER BY p.id DESC
             LIMIT 6'
        )->fetchAll();
    }
} catch (\PDOException $e) {
    error_log('Featured products query failed: ' . $e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>
<!-- ── HERO ── -->
    <section class="hero-section" aria-label="Hero" style="position:relative;min-height:100svh;display:flex;align-items:center;overflow:hidden;background:linear-gradient(135deg,#0F0F0F 0%,#1C1917 50%,#2D1A1A 100%);">

      <!-- Hero Slideshow (3 original slides) -->
      <div class="hero-slideshow" aria-hidden="true">
        <div class="hero-slide active" style="background-image:url('assets/images/slide1-jpg.webp');"></div>
        <div class="hero-slide" data-bg="assets/images/slide3-jpg.webp"></div>
        <div class="hero-slide" data-bg="assets/images/slide2-jpg.webp"></div>
      </div>

      <!-- Slide Progress Dots + pause control -->
      <div class="hero-dots" role="group" aria-label="Hero slideshow controls">
        <button type="button" class="hero-dot active" aria-pressed="true"  aria-label="Show slide 1"></button>
        <button type="button" class="hero-dot"        aria-pressed="false" aria-label="Show slide 2"></button>
        <button type="button" class="hero-dot"        aria-pressed="false" aria-label="Show slide 3"></button>
        <button type="button" class="hero-play-toggle" id="hero-play-toggle" aria-pressed="false" aria-label="Pause slideshow">
          <svg class="icon-pause" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
          <svg class="icon-play"  viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
        </button>
      </div>

      <!-- Gradient Overlay -->
      <div style="position:absolute;inset:0;background:linear-gradient(to right,rgba(10,5,5,0.88) 0%,rgba(10,5,5,0.65) 55%,rgba(10,5,5,0.25) 100%);z-index:1;"></div>

      <!-- Floating texture pattern -->
      <div style="position:absolute;inset:0;background-image:url('data:image/svg+xml,%3Csvg width=60 height=60 viewBox=0 0 60 60 xmlns=http://www.w3.org/2000/svg%3E%3Cg fill=none%3E%3Cg fill=%23C0282A fill-opacity=0.04%3E%3Cpath d=M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');opacity:0.5;z-index:1;"></div>

      <!-- Floating decorative badge -->
      <div class="hero-badge" style="position:absolute;top:25%;right:8%;z-index:3;background:rgba(192,40,42,0.12);border:1px solid rgba(192,40,42,0.3);backdrop-filter:blur(10px);border-radius:20px;padding:1.5rem 2rem;text-align:center;display:none;" aria-hidden="true">
        <div style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#D93B3D;line-height:1;">15+</div>
        <div style="font-size:0.7rem;color:rgba(255,255,255,0.6);letter-spacing:0.1em;text-transform:uppercase;margin-top:4px;">Years of Excellence</div>
      </div>

      <!-- Hero Content -->
      <div class="container" style="position:relative;z-index:2;padding-top:6rem;padding-bottom:6rem;">
        <div style="max-width:680px;">
          <div class="hero-anim-1">
            <span class="script-text" style="font-size:1.8rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">An Identity of Crafted Fabrics</span>
          </div>

          <h1 class="hero-anim-2" style="font-family:'Playfair Display',serif;font-size:clamp(2.5rem,6vw,4.5rem);font-weight:800;color:#FFFFFF;line-height:1.1;margin-bottom:1.5rem;">
            Crafting Excellence<br>
            <span style="background:linear-gradient(90deg,#C0282A,#D93B3D,#C0282A);background-size:200% auto;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">in Silk &amp; Linen</span>
          </h1>

          <p class="hero-anim-3" style="font-size:clamp(1rem,2vw,1.2rem);color:rgba(255,255,255,0.75);line-height:1.8;max-width:520px;margin-bottom:2.5rem;">
            From the handlooms of Bhagalpur to wardrobes worldwide — Fabloom Group brings you India's finest pure silk, pure linen, and artisanal printed fabrics crafted with generations of expertise.
          </p>

          <div class="hero-ctas hero-anim-4" style="display:flex;gap:1rem;flex-wrap:wrap;">
            <a href="products" class="btn btn-primary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
              Explore Products
            </a>
            <a href="enquiry" class="btn btn-outline-white">
              Request Enquiry
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>

          <!-- Trust indicators -->
          <div class="hero-anim-5" style="display:flex;align-items:center;gap:2rem;margin-top:3rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,0.1);">
            <div style="text-align:center;">
              <div style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:#D93B3D;">100%</div>
              <div style="font-size:0.7rem;color:rgba(255,255,255,0.5);letter-spacing:0.06em;text-transform:uppercase;">Natural Fibres</div>
            </div>
            <div style="width:1px;height:36px;background:rgba(255,255,255,0.1);"></div>
            <div style="text-align:center;">
              <div style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:#D93B3D;">500+</div>
              <div style="font-size:0.7rem;color:rgba(255,255,255,0.5);letter-spacing:0.06em;text-transform:uppercase;">Products</div>
            </div>
            <div style="width:1px;height:36px;background:rgba(255,255,255,0.1);"></div>
            <div style="text-align:center;">
              <div style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:#D93B3D;">PAN India</div>
              <div style="font-size:0.7rem;color:rgba(255,255,255,0.5);letter-spacing:0.06em;text-transform:uppercase;">Delivery</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Scroll Indicator -->
      <div class="scroll-indicator" style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);z-index:2;display:flex;flex-direction:column;align-items:center;gap:0.5rem;" aria-hidden="true">
        <span style="font-size:0.65rem;letter-spacing:0.15em;text-transform:uppercase;color:rgba(255,255,255,0.4);">Scroll</span>
        <div style="width:1px;height:40px;background:linear-gradient(to bottom,rgba(192,40,42,0.6),transparent);"></div>
      </div>
    </section>

    <!-- ── STATS BAR ── -->
    <section class="stats-bar" aria-label="Company statistics">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item reveal" aria-label="15 plus years of experience">
            <div class="stat-number">
              <span class="counter-val" data-target="15">0</span><span class="stat-suffix">+</span>
            </div>
            <div class="stat-label">Years of Experience</div>
          </div>
          <div class="stat-item reveal stagger-2" aria-label="500 plus products">
            <div class="stat-number">
              <span class="counter-val" data-target="500">0</span><span class="stat-suffix">+</span>
            </div>
            <div class="stat-label">Products &amp; Variants</div>
          </div>
          <div class="stat-item reveal stagger-3" aria-label="1000 plus happy clients">
            <div class="stat-number">
              <span class="counter-val" data-target="1000">0</span><span class="stat-suffix">+</span>
            </div>
            <div class="stat-label">Happy Clients</div>
          </div>
          <div class="stat-item reveal stagger-4" aria-label="50000 plus metres exported">
            <div class="stat-number">
              <span class="counter-val" data-target="50000">0</span><span class="stat-suffix">M+</span>
            </div>
            <div class="stat-label">Metres Delivered</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── ABOUT ── -->
    <section class="section bg-white" id="about" aria-labelledby="about-heading">
      <div class="container">
        <div class="about-split">
          <div class="about-split__img reveal-left">
            <img src="assets/images/infra-facility-1.jpg"
                 alt="Fabloom artisans working on handloom fabric in Bhagalpur"
                 loading="lazy"
                 width="600" height="560"
                 onerror="this.src='https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=600&q=80'">
            <div class="about-img-badge hero-badge">
              <span class="badge-num">15+</span>
              <div class="badge-text">Years of Craft</div>
            </div>
          </div>

          <div class="about-split__content reveal-right">
            <span class="section-label">Our Story</span>
            <h2 class="section-title" id="about-heading">About Fabloom Group</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Fabloom Group of Company is a distinguished name in India's textile heritage, rooted in the handloom capital of Bhagalpur, Bihar. We bring together centuries-old weaving traditions and modern dyeing and printing technologies to produce fabrics of unparalleled quality.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:2rem;">
              From pure silk to premium linen, from block-printed masterpieces to digitally rendered contemporary designs — every fabric that leaves our facility carries the mark of rigorous craftsmanship, eco-conscious production, and a commitment to excellence that is simply unmatched.
            </p>
            <ul class="usp-list">
              <li class="usp-item">
                <div class="usp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
                <div class="usp-text"><h3>Handloom Craftsmanship</h3><p>Each fabric woven by skilled artisans with decades of generational expertise.</p></div>
              </li>
              <li class="usp-item">
                <div class="usp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <div class="usp-text"><h3>Eco-Friendly Dyes</h3><p>All dyeing and finishing processes use sustainable, non-toxic materials.</p></div>
              </li>
              <li class="usp-item">
                <div class="usp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>
                <div class="usp-text"><h3>Direct Manufacturer</h3><p>No middlemen — order directly from our factory for best prices and quality.</p></div>
              </li>
              <li class="usp-item">
                <div class="usp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></div>
                <div class="usp-text"><h3>Custom Orders Welcome</h3><p>Bespoke colour, weave, and print specifications for bulk and boutique orders.</p></div>
              </li>
            </ul>
            <a href="about" class="btn btn-primary" style="margin-top:2rem;">
              Learn Our Story
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ── SERVICES ── -->
    <section class="section bg-texture" aria-labelledby="services-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">What We Offer</span>
          <h2 class="section-title" id="services-heading">Our Core Offerings</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">From raw fibre to finished fabric — every process under one roof, every product a testament to quality.</p>
        </div>

        <div class="grid-3 stagger-children mt-12">
          <article class="service-card reveal">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3 3h18v5H3zM3 8h18v13H3z"/><line x1="12" y1="8" x2="12" y2="21"/></svg>
            </div>
            <h3>Pure Linen Fabric</h3>
            <p>Breathable, durable, and naturally lustrous — our linen collection is woven from the finest quality flax fibres sourced ethically.</p>
            <div class="service-card__arrow">
              Explore Linen
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </article>

          <article class="service-card reveal">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            </div>
            <h3>Pure Silk Fabric</h3>
            <p>The legacy of Bhagalpur silk — naturally produced, richly textured, and available in an exquisite range of weaves and finishes.</p>
            <div class="service-card__arrow">
              Explore Silk
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </article>

          <article class="service-card reveal">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6v6H9z"/></svg>
            </div>
            <h3>Block Printing</h3>
            <p>Hand-carved wooden blocks pressed with vibrant, eco-safe dyes — creating unique patterns that are impossible to replicate by machine.</p>
            <div class="service-card__arrow">
              View Prints
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </article>

          <article class="service-card reveal">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><polyline points="8 21 12 17 16 21"/></svg>
            </div>
            <h3>Digital Printing</h3>
            <p>Photographic precision meets textile artistry — our digital printing achieves vivid, detailed designs on silk and linen substrates.</p>
            <div class="service-card__arrow">
              View Digital
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </article>

          <article class="service-card reveal">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </div>
            <h3>Hand Brush Work</h3>
            <p>Individually painted brush strokes create one-of-a-kind textile artworks — each piece a wearable canvas of colour and expression.</p>
            <div class="service-card__arrow">
              View Handwork
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </article>

          <article class="service-card reveal">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            </div>
            <h3>Dyeing &amp; Finishing</h3>
            <p>Expert dyeing and surface finishing processes that enhance colour fastness, drape, and hand-feel — making your fabric truly exceptional.</p>
            <div class="service-card__arrow">
              Learn More
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ── PRODUCTS SHOWCASE ── -->
    <section class="section bg-white" aria-labelledby="products-heading">
      <div class="container">
        <div class="flex-between" style="flex-wrap:wrap;gap:1.5rem;margin-bottom:3rem;">
          <div class="reveal">
            <span class="section-label">Our Collection</span>
            <h2 class="section-title" id="products-heading">Featured Products</h2>
            <div class="gold-divider"></div>
          </div>
          <a href="products" class="btn btn-outline reveal" style="align-self:flex-end;">
            View All Products
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>

        <?php if (!empty($featured)): ?>
      <div class="product-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;">
        <?php foreach ($featured as $p):
          $price       = product_price($p);
          $disc        = discount_pct($p);
          $in_stock    = product_in_stock($p);
        ?>
        <article class="product-card reveal" style="background:#fff;border-radius:1rem;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);transition:transform 0.25s,box-shadow 0.25s;">
          <div class="product-card__img-wrap" style="position:relative;overflow:hidden;aspect-ratio:3/4;background:#f5f0e8;">
            <?php if ($disc > 0): ?>
              <span style="position:absolute;top:0.75rem;left:0.75rem;z-index:2;background:var(--clr-red);color:#fff;font-size:0.7rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:0.25rem;letter-spacing:0.03em;"><?= $disc ?>% OFF</span>
            <?php endif; ?>
            <?php if ($p['cat_name']): ?>
              <span style="position:absolute;top:0.75rem;right:0.75rem;z-index:2;background:rgba(15,15,15,0.75);color:#fff;font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:0.25rem;backdrop-filter:blur(4px);"><?= h($p['cat_name']) ?></span>
            <?php endif; ?>
            <a href="<?= product_url($p) ?>">
              <img src="<?= product_img($p['image']) ?>"
                   alt="<?= h($p['name']) ?>"
                   loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease;">
            </a>
          </div>
          <div class="product-card__body" style="padding:1.25rem;">
            <h3 style="font-family:var(--font-serif);font-size:1rem;font-weight:600;margin-bottom:0.5rem;"><a href="<?= product_url($p) ?>" style="color:var(--clr-charcoal);"><?= h($p['name']) ?></a></h3>
            <?php if ($p['short_desc']): ?>
              <p style="font-size:0.8rem;color:var(--clr-text-muted);margin-bottom:0.75rem;line-height:1.6;"><?= h(substr($p['short_desc'], 0, 80)) ?>…</p>
            <?php endif; ?>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;">
              <div>
                <span style="font-family:var(--font-serif);font-size:1.1rem;font-weight:700;color:var(--clr-red);"><?= fmt_price($price) ?></span>
                <?php if ($disc > 0): ?>
                  <span style="font-size:0.8rem;color:var(--clr-text-muted);text-decoration:line-through;margin-left:0.4rem;"><?= fmt_price($p['price']) ?></span>
                <?php endif; ?>
              </div>
              <?php if ($in_stock): ?>
              <form class="add-to-cart-form" method="POST" action="<?= SITE_URL ?>/api/cart.php?action=add" style="margin:0;">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="qty" value="<?= product_min_qty($p) ?>">
                <button type="submit" class="btn btn-primary" style="padding:0.5rem 1rem;font-size:0.78rem;" aria-label="Add <?= h($p['name']) ?> to cart">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.95-1.57L23 6H6"/></svg>
                  Add
                </button>
              </form>
              <?php else: ?>
              <span style="font-size:0.75rem;color:var(--clr-text-muted);background:var(--clr-cream);padding:0.3rem 0.75rem;border-radius:0.25rem;">Out of Stock</span>
              <?php endif; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <!-- Fallback to static cards if DB is not connected -->
      <div class="grid-3 stagger-children">
        <article class="product-card reveal"><div class="product-card__img-wrap"><img src="<?= SITE_URL ?>/assets/images/linen-fabric-hero.webp" alt="Pure Linen Fabric" loading="lazy"></div><div class="product-card__body"><div class="product-card__name">Natural Linen Weave</div></div></article>
        <article class="product-card reveal"><div class="product-card__img-wrap"><img src="<?= SITE_URL ?>/assets/images/silk-fabric-hero.webp" alt="Pure Silk Fabric" loading="lazy"></div><div class="product-card__body"><div class="product-card__name">Bhagalpur Heritage Silk</div></div></article>
        <article class="product-card reveal"><div class="product-card__img-wrap"><img src="<?= SITE_URL ?>/assets/images/furnishing-linen-hero.webp" alt="Block Print Fabric" loading="lazy"></div><div class="product-card__body"><div class="product-card__name">Artisan Block Print</div></div></article>
      </div>
      <?php endif; ?>
      </div>
    </section>

    <!-- ── VIDEO SECTION ── -->
    <section class="video-section" aria-labelledby="video-heading">
      <div class="container" style="position:relative;z-index:1;">
        <div class="text-center reveal">
          <span class="section-label" style="color:var(--clr-red-light);">In Action</span>
          <h2 class="section-title" id="video-heading" style="color:var(--clr-white);">See Our Craftsmanship</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4" style="color:rgba(255,255,255,0.6);">Step inside our Bhagalpur facility — watch the ancient art of silk weaving come to life on modern handlooms.</p>
        </div>

        <!-- Fabloom's own film, shot at the Bhagalpur looms and carrying the
             brand watermark. It is portrait footage, so the frame is 9:16
             rather than the 16:9 box the placeholder embed used — a vertical
             film letterboxed into a widescreen well is mostly black bars.
             preload="none" keeps 4.9MB off the wire until someone presses
             play; the poster carries the section on its own. -->
        <div class="video-wrap video-wrap--portrait reveal mt-12">
          <video
            class="video-player"
            controls
            playsinline
            preload="none"
            width="720" height="1280"
            poster="<?= asset('assets/images/fabloom-craftsmanship-poster.webp') ?>"
            aria-label="Handloom weaving at the Fabloom mills in Bhagalpur">
            <source src="<?= asset('assets/video/fabloom-craftsmanship.mp4') ?>" type="video/mp4">
            <p>Your browser cannot play this video.
              <a href="<?= asset('assets/video/fabloom-craftsmanship.mp4') ?>">Download it instead</a>.</p>
          </video>
        </div>
      </div>
    </section>

    <!-- ── PROCESS TIMELINE ── -->
    <section class="section bg-ivory" aria-labelledby="process-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Our Process</span>
          <h2 class="section-title" id="process-heading">From Fibre to Fabric</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Five meticulous steps that transform raw silk and linen into extraordinary textiles.</p>
        </div>

        <div class="process-steps mt-12 stagger-children">
          <div class="process-step reveal">
            <div class="process-step__num">01</div>
            <h3>Design</h3>
            <p>Conceptualising patterns — from traditional motifs to contemporary prints.</p>
          </div>
          <div class="process-step reveal">
            <div class="process-step__num">02</div>
            <h3>Weave</h3>
            <p>Master weavers craft fabric on handlooms and power looms with precision.</p>
          </div>
          <div class="process-step reveal">
            <div class="process-step__num">03</div>
            <h3>Dye &amp; Print</h3>
            <p>Eco-safe dyes, block printing, and digital printing applied with expertise.</p>
          </div>
          <div class="process-step reveal">
            <div class="process-step__num">04</div>
            <h3>Finish</h3>
            <p>Washing, pressing, and surface treatment for the perfect hand-feel.</p>
          </div>
          <div class="process-step reveal">
            <div class="process-step__num">05</div>
            <h3>Deliver</h3>
            <p>Quality-checked and dispatched directly from factory to your doorstep.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ── GOOGLE REVIEWS ── -->
    <section class="section bg-white" aria-labelledby="reviews-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Client Love</span>
          <h2 class="section-title" id="reviews-heading">What Our Clients Say</h2>
          <div class="gold-divider"></div>

          <!-- Rating Badge -->
          <div style="display:flex;justify-content:center;margin-top:1.5rem;">
            <div class="rating-badge">
              <div class="rating-badge__num">4.8</div>
              <div>
                <div class="rating-badge__stars">
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div class="rating-badge__info"><strong>47 Google Reviews</strong>Verified Customers</div>
              </div>
              <!-- Google coloured G logo -->
              <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            </div>
          </div>
        </div>

        <!-- Reviews Carousel -->
        <div class="carousel-wrap mt-8" id="reviews-carousel">
          <div class="carousel-track" id="reviews-track">

            <div class="carousel-slide" style="width:calc(33.333% - 1rem);min-width:300px;">
              <article class="review-card" itemscope itemtype="https://schema.org/Review">
                <div class="review-stars" role="img" aria-label="5 out of 5 stars">
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="review-text" itemprop="reviewBody">"Absolutely stunning quality! The pure linen fabric I ordered exceeded all my expectations. The weave is consistent, the colour is rich, and delivery was very prompt. Fabloom is now my go-to for all textile requirements."</p>
                <div class="review-meta">
                  <div class="review-avatar" aria-hidden="true">R</div>
                  <div class="review-author">
                    <strong itemprop="author">Riya Sharma</strong>
                    <span>Verified Google Review · 3 months ago</span>
                  </div>
                </div>
              </article>
            </div>

            <div class="carousel-slide" style="width:calc(33.333% - 1rem);min-width:300px;">
              <article class="review-card" itemscope itemtype="https://schema.org/Review">
                <div class="review-stars" role="img" aria-label="5 out of 5 stars">
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="review-text" itemprop="reviewBody">"I've been sourcing silk from Fabloom for my boutique for two years now. The Bhagalpur silk is unmatched — rich texture, excellent drape, and the custom dyeing options are a game changer. Highly recommended for bulk buyers."</p>
                <div class="review-meta">
                  <div class="review-avatar" aria-hidden="true">A</div>
                  <div class="review-author">
                    <strong itemprop="author">Arjun Mehta</strong>
                    <span>Verified Google Review · 5 months ago</span>
                  </div>
                </div>
              </article>
            </div>

            <div class="carousel-slide" style="width:calc(33.333% - 1rem);min-width:300px;">
              <article class="review-card" itemscope itemtype="https://schema.org/Review">
                <div class="review-stars" role="img" aria-label="5 out of 5 stars">
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="review-text" itemprop="reviewBody">"Block prints are extraordinary! Each piece looks handcrafted and unique. The team at Fabloom is very responsive and helped me with exactly the colour palette I needed for my fashion line. Superb quality at factory prices!"</p>
                <div class="review-meta">
                  <div class="review-avatar" aria-hidden="true">P</div>
                  <div class="review-author">
                    <strong itemprop="author">Priya Nair</strong>
                    <span>Verified Google Review · 2 months ago</span>
                  </div>
                </div>
              </article>
            </div>

            <div class="carousel-slide" style="width:calc(33.333% - 1rem);min-width:300px;">
              <article class="review-card" itemscope itemtype="https://schema.org/Review">
                <div class="review-stars" role="img" aria-label="5 out of 5 stars">
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="review-text" itemprop="reviewBody">"Digital printing quality is truly impressive. The colours stay vibrant even after multiple washes. As a saree designer, finding a reliable manufacturer like Fabloom has been a blessing. Will definitely order again."</p>
                <div class="review-meta">
                  <div class="review-avatar" aria-hidden="true">S</div>
                  <div class="review-author">
                    <strong itemprop="author">Shobha Iyengar</strong>
                    <span>Verified Google Review · 4 months ago</span>
                  </div>
                </div>
              </article>
            </div>

            <div class="carousel-slide" style="width:calc(33.333% - 1rem);min-width:300px;">
              <article class="review-card" itemscope itemtype="https://schema.org/Review">
                <div class="review-stars" role="img" aria-label="4 out of 5 stars">
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="review-text" itemprop="reviewBody">"Great manufacturer with excellent product range. The linen prints I ordered for my home décor brand were perfect. Communication was smooth and delivery was on time. Will come back for larger orders."</p>
                <div class="review-meta">
                  <div class="review-avatar" aria-hidden="true">V</div>
                  <div class="review-author">
                    <strong itemprop="author">Vikram Choudhary</strong>
                    <span>Verified Google Review · 6 months ago</span>
                  </div>
                </div>
              </article>
            </div>

          </div><!-- end carousel-track -->

          <div class="carousel-controls" role="group" aria-label="Review carousel controls">
            <button class="carousel-btn" id="rev-prev" aria-label="Previous review">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="carousel-dots" role="tablist" aria-label="Go to review">
              <button class="carousel-dot active" role="tab" aria-selected="true" aria-label="Review set 1"></button>
              <button class="carousel-dot" role="tab" aria-selected="false" aria-label="Review set 2"></button>
              <button class="carousel-dot" role="tab" aria-selected="false" aria-label="Review set 3"></button>
            </div>
            <button class="carousel-btn" id="rev-next" aria-label="Next review">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>

          <div style="text-align:center;margin-top:2rem;">
            <a href="https://maps.app.goo.gl/TUQdwxNjSqC5y5e77" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
              Read All Reviews on Google
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ── BLOG PREVIEW ── -->
    <section class="section bg-cream" aria-labelledby="blog-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Insights</span>
          <h2 class="section-title" id="blog-heading">Latest Articles</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Tips, stories, and textile wisdom from the heart of India's weaving heritage.</p>
        </div>

        <div class="grid-3 mt-12 stagger-children">
          <article class="blog-card reveal">
            <div class="blog-card__img">
              <img src="assets/images/blog1.jpeg"
                   alt="How to Care for Silk Fabric – Fabloom Blog"
                   loading="lazy" width="400" height="250"
                   onerror="this.src='https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=250&q=80'">
            </div>
            <div class="blog-card__body">
              <div class="blog-card__meta">
                <span class="tag tag-gold">Silk Care</span>
                <span class="blog-card__date">April 12, 2025</span>
              </div>
              <h3 class="blog-card__title">How to Care for Pure Silk Fabric at Home</h3>
              <p class="blog-card__excerpt">Pure silk is a delicate and luxurious fibre that requires gentle care. Learn the right washing, drying, and storage techniques to keep your silk fabrics in pristine condition for years.</p>
              <a href="blog-post" class="blog-card__read">
                Read More
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </a>
            </div>
          </article>

          <article class="blog-card reveal">
            <div class="blog-card__img">
              <img src="assets/images/blog2.jpeg"
                   alt="The Heritage of Bhagalpur Silk – Fabloom Blog"
                   loading="lazy" width="400" height="250"
                   onerror="this.src='https://images.unsplash.com/photo-1524678714210-9917a6c619c2?w=400&h=250&q=80'">
            </div>
            <div class="blog-card__body">
              <div class="blog-card__meta">
                <span class="tag tag-gold">Heritage</span>
                <span class="blog-card__date">March 28, 2025</span>
              </div>
              <h3 class="blog-card__title">The Glorious Heritage of Bhagalpur Silk Weaving</h3>
              <p class="blog-card__excerpt">Bhagalpur – the Silk City of India – has been producing some of the world's finest textured silk for over a millennium. Discover the history, the artisans, and the craft behind this legendary tradition.</p>
              <a href="blog-post" class="blog-card__read">
                Read More
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </a>
            </div>
          </article>

          <article class="blog-card reveal">
            <div class="blog-card__img">
              <img src="assets/images/blog3.webp"
                   alt="Linen vs Cotton – Which Fabric to Choose – Fabloom Blog"
                   loading="lazy" width="400" height="250"
                   onerror="this.src='https://images.unsplash.com/photo-1612460627030-3b78c1cf0e57?w=400&h=250&q=80'">
            </div>
            <div class="blog-card__body">
              <div class="blog-card__meta">
                <span class="tag tag-gold">Linen</span>
                <span class="blog-card__date">February 15, 2025</span>
              </div>
              <h3 class="blog-card__title">Linen vs Cotton: Which Fabric is Right for You?</h3>
              <p class="blog-card__excerpt">Both linen and cotton are natural, breathable fabrics — but they differ significantly in texture, durability, and use. This guide breaks down everything you need to know to choose the right fabric for your project.</p>
              <a href="blog-post" class="blog-card__read">
                Read More
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </a>
            </div>
          </article>
        </div>

        <div style="text-align:center;margin-top:3rem;">
          <a href="blog" class="btn btn-outline reveal">
            View All Articles
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>
      </div>
    </section>

    <!-- ── FAQ ── -->
    <?php faq_render(
        $home_faqs,
        'Frequently Asked Questions',
        'The questions buyers ask before ordering silk and linen direct from a Bhagalpur mill.'
    ); ?>

    <!-- ── INQUIRY CTA ── -->
    <section class="section bg-texture" aria-labelledby="inquiry-heading">
      <div class="container">
        <div class="grid-2" style="gap:4rem;align-items:start;">
          <div class="reveal-left">
            <span class="section-label">Quick Enquiry</span>
            <h2 class="section-title" id="inquiry-heading">Ready to Order? Let's Talk Fabric.</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.8;">
              Whether you need a small sample or a bulk order, our textile experts are ready to help. Send us your requirements and we'll get back within 24 hours.
            </p>

            <div style="margin-top:2rem;display:flex;flex-direction:column;gap:1rem;">
              <a href="tel:+919760058796" style="display:flex;align-items:center;gap:1rem;padding:1rem 1.5rem;background:white;border-radius:12px;box-shadow:var(--shadow-sm);transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s;text-decoration:none;">
                <div style="width:44px;height:44px;background:rgba(192,40,42,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
                <div>
                  <div style="font-size:0.75rem;color:var(--clr-text-muted);letter-spacing:0.05em;text-transform:uppercase;font-weight:600;">Call Us</div>
                  <div style="font-weight:700;color:var(--clr-charcoal);">+91 97600 58796</div>
                </div>
              </a>
              <a href="https://wa.me/919760058796" target="_blank" rel="noopener" style="display:flex;align-items:center;gap:1rem;padding:1rem 1.5rem;background:white;border-radius:12px;box-shadow:var(--shadow-sm);transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s;text-decoration:none;">
                <div style="width:44px;height:44px;background:#E7F9EF;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="#25D366" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div>
                  <div style="font-size:0.75rem;color:var(--clr-text-muted);letter-spacing:0.05em;text-transform:uppercase;font-weight:600;">WhatsApp</div>
                  <div style="font-weight:700;color:var(--clr-charcoal);">Chat with us instantly</div>
                </div>
              </a>
            </div>
          </div>

          <!-- Inquiry Form -->
          <div class="reveal-right">
            <form id="home-inquiry-form" data-enquiry-form="home" data-success-id="inq-success" data-submit-id="inq-submit" method="POST" action="api/enquiry.php" novalidate style="background:white;border-radius:20px;padding:2.5rem;box-shadow:var(--shadow-lg);">
              <h3 style="font-family:'Playfair Display',serif;font-size:1.5rem;margin-bottom:0.5rem;">Send Enquiry</h3>
              <p style="font-size:0.875rem;color:var(--clr-text-muted);margin-bottom:2rem;">We'll respond within 24 working hours.</p>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                <div class="form-group">
                  <label class="form-label" for="inq-name">Full Name <span class="required">*</span></label>
                  <input type="text" id="inq-name" name="name" class="form-control" placeholder="Your name" required autocomplete="name">
                  <span class="form-error" id="err-name">Please enter your name.</span>
                </div>
                <div class="form-group">
                  <label class="form-label" for="inq-phone">Phone <span class="required">*</span></label>
                  <input type="tel" id="inq-phone" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX" required autocomplete="tel">
                  <span class="form-error" id="err-phone">Please enter a valid phone number.</span>
                </div>
              </div>

              <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" for="inq-email">Email Address</label>
                <input type="email" id="inq-email" name="email" class="form-control" placeholder="you@example.com" autocomplete="email">
              </div>

              <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" for="inq-product">Product Interest <span class="required">*</span></label>
                <select id="inq-product" name="product" class="form-control" required>
                  <option value="" disabled selected>Select product category</option>
                  <option>Pure Linen Fabric</option>
                  <option>Pure Silk Fabric</option>
                  <option>Block Print Fabric</option>
                  <option>Digital Print Fabric</option>
                  <option>Hand Brush Work</option>
                  <option>Dyeing &amp; Finishing</option>
                  <option>Custom / Other</option>
                </select>
                <span class="form-error" id="err-product">Please select a product.</span>
              </div>

              <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" for="inq-message">Message</label>
                <textarea id="inq-message" name="message" class="form-control" placeholder="Describe your requirements — quantity, colour, size, etc." rows="4"></textarea>
              </div>

              <button type="submit" class="btn btn-primary" id="inq-submit" style="width:100%;justify-content:center;">
                <span class="btn-text">Send Enquiry</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </button>

              <div id="inq-success" style="display:none;text-align:center;padding:2rem;color:#27AE60;" role="alert" aria-live="polite">
                <svg class="success-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#27AE60" stroke-width="2" style="margin:0 auto 1rem;display:block;" aria-hidden="true">
                  <circle cx="12" cy="12" r="10"/>
                  <path d="M9 12l2 2 4-4"/>
                </svg>
                <strong>Enquiry Sent!</strong> We'll contact you within 24 hours.
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
