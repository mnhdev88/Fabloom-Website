<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Infrastructure | Fabloom\'s State-of-the-Art Manufacturing Unit';
$page_desc  = 'Explore Fabloom\'s manufacturing facility in Bhagalpur, Bihar – advanced looms, eco-friendly dye units, printing machines and skilled artisans.';
$page_canonical = SITE_URL . '/infrastructure';
$page_og_image = SITE_URL . '/assets/images/og-infrastructure.jpg';
$page_schema = <<<'JSONLD'
{
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "LocalBusiness",
        "@id": "https://www.thefabloom.com/#localbusiness",
        "name": "Fabloom Group of Company",
        "image": "https://www.thefabloom.com/assets/images/logo-1.webp",
        "url": "https://www.thefabloom.com",
        "telephone": "+919760058796",
        "email": "info@thefabloom.com",
        "description": "State-of-the-art textile manufacturing facility in Bhagalpur, Bihar featuring handlooms, power looms, digital printers, eco-friendly dye units, and quality control laboratory.",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "Mohiuddin Pur, Post–Habibpur",
          "addressLocality": "Bhagalpur",
          "addressRegion": "Bihar",
          "postalCode": "813113",
          "addressCountry": "IN"
        },
        "geo": { "@type": "GeoCoordinates", "latitude": 25.2260481, "longitude": 86.9755285 },
        "hasMap": "https://maps.app.goo.gl/TUQdwxNjSqC5y5e77"
      },
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://www.thefabloom.com/" },
          { "@type": "ListItem", "position": 2, "name": "Infrastructure", "item": "https://www.thefabloom.com/infrastructure.html" }
        ]
      }
    ]
  }
JSONLD;
require_once __DIR__ . '/includes/header.php';
?>
<!-- PAGE HERO -->
    <section class="page-hero" aria-label="Page header" style="position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;">
      <div class="container" style="position:relative;z-index:1;padding-top:5rem;padding-bottom:4rem;">
        <div class="reveal">
          <span class="script-text" style="font-size:1.5rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">Built to Excel</span>
          <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#FFFFFF;line-height:1.15;margin-bottom:1.25rem;">Our Manufacturing Infrastructure</h1>
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol style="list-style:none;display:flex;align-items:center;gap:0.5rem;padding:0;margin:0;flex-wrap:wrap;">
              <li><a href="index" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.875rem;">Home</a></li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:#C0282A;font-size:0.875rem;font-weight:600;" aria-current="page">Infrastructure</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- FACILITY OVERVIEW -->
    <section class="section bg-white" aria-labelledby="facility-heading">
      <div class="container">
        <div class="about-split" style="gap:4rem;align-items:center;">
          <div class="about-split__content reveal-left">
            <span class="section-label">Our Facility</span>
            <h2 class="section-title" id="facility-heading">Bhagalpur Manufacturing Unit</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Located in the heart of Bhagalpur — India's Silk City — Fabloom's manufacturing facility spans a dedicated production campus housing handlooms, power looms, a digital printing wing, an eco-friendly dye unit, block-print tables, and a fully equipped quality control laboratory.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Every aspect of our infrastructure is designed with two priorities in mind: craft excellence and environmental responsibility. Our dye unit uses low-impact, eco-certified dyes and water-recycling systems. Our weaving halls are optimised for natural light and ergonomic comfort for our artisans.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:2rem;">
              The facility is capable of producing over 10,000 metres of finished fabric every month — across silk, linen, and blended categories — while maintaining the artisanal quality standards that Fabloom is renowned for.
            </p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem;">
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">Mohiuddin Pur</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Habibpur, Bhagalpur, Bihar 813113</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">Block Print Office</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Sector 62, Noida, Uttar Pradesh</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">Est. 1979</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">45+ years of manufacturing excellence</div>
              </div>
            </div>
          </div>
          <div class="reveal-right">
            <img src="assets/images/infra-facility-2.jpg"
                 alt="Fabloom manufacturing facility in Bhagalpur Bihar – aerial view"
                 loading="lazy" width="560" height="460"
                 style="width:100%;border-radius:16px;object-fit:cover;"
                 onerror="this.src='https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=560&h=460&q=80'">
          </div>
        </div>
      </div>
    </section>

    <!-- MACHINERY GRID -->
    <section class="section bg-cream" aria-labelledby="machinery-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Equipment &amp; Technology</span>
          <h2 class="section-title" id="machinery-heading">Our Machinery</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Purpose-built machinery and traditional tools working in harmony to produce exceptional fabrics.</p>
        </div>

        <div class="grid-3 mt-12 stagger-children">
          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <h3 class="service-card__title">Handlooms</h3>
            <p class="service-card__desc">Over 50 traditional pit looms and frame looms operated by our master weavers. Capable of producing Bhagalpur silk, Katan silk, and fine linen with authentic hand-crafted textures that power looms cannot replicate.</p>
            <div class="tag tag-gold" style="margin-top:1rem;display:inline-block;">50+ Units</div>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <h3 class="service-card__title">Power Looms</h3>
            <p class="service-card__desc">Our 20+ semi-automatic power looms enable high-volume production of consistent weave structures for bulk linen and silk orders, while maintaining thread density and fabric weight specifications to the millimetre.</p>
            <div class="tag tag-gold" style="margin-top:1rem;display:inline-block;">20+ Units</div>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
            </div>
            <h3 class="service-card__title">Digital Printers</h3>
            <p class="service-card__desc">State-of-the-art high-resolution digital textile printers capable of printing directly on silk and linen with photographic precision, unlimited colour palettes, and fade-resistant reactive inks.</p>
            <div class="tag tag-gold" style="margin-top:1rem;display:inline-block;">HD Printing</div>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <h3 class="service-card__title">Dye Vats</h3>
            <p class="service-card__desc">Eco-certified dye vats with precise temperature and pH control ensure consistent, vibrant colour results across batches. We use low-impact dyes and operate water-recycling systems to minimise environmental impact.</p>
            <div class="tag tag-gold" style="margin-top:1rem;display:inline-block;">Eco-Safe</div>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>
            </div>
            <h3 class="service-card__title">Block Print Tables</h3>
            <p class="service-card__desc">Dedicated block-printing tables and drying racks for our artisans to stamp intricate designs using hand-carved wooden blocks. Our block-print unit handles both production runs and bespoke custom design work.</p>
            <div class="tag tag-gold" style="margin-top:1rem;display:inline-block;">Handcrafted</div>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h3 class="service-card__title">Quality Lab</h3>
            <p class="service-card__desc">A fully equipped QC laboratory for thread count analysis, colour fastness testing, wash shrinkage testing, and dimensional stability checks. Every batch is certified before leaving our facility.</p>
            <div class="tag tag-gold" style="margin-top:1rem;display:inline-block;">ISO Standards</div>
          </div>
        </div>
      </div>
    </section>

    <!-- PHOTO GALLERY -->
    <section class="section bg-white" aria-labelledby="gallery-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Photo Gallery</span>
          <h2 class="section-title" id="gallery-heading">Inside Our Facility</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">A glimpse into where your fabrics are born.</p>
        </div>

        <div class="mt-12" style="display:grid;grid-template-columns:repeat(3,1fr);grid-auto-rows:220px;gap:1rem;" aria-label="Facility gallery">
          <div style="grid-row:span 2;border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-loom-1.jpg" alt="Master weaver at handloom in Fabloom Bhagalpur facility" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-1.jpg" alt="Fabloom weaving unit floor" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-4.jpg" alt="Digital textile printing machine at Fabloom" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-5.jpg" alt="Dye vat unit at Fabloom manufacturing plant" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-6.jpg" alt="Block printing artisan at work at Fabloom" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="grid-row:span 2;border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-loom-2.jpg" alt="Traditional loom operation at Fabloom facility" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-7.jpg" alt="Quality control inspection of finished linen fabric" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-8.jpg" alt="Fabric rolls ready for dispatch at Fabloom" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-9.jpg" alt="Silk weaving process at Fabloom Bhagalpur" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-10.jpg" alt="Loom threading and warping process" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-11a.jpg" alt="Fabric finishing and folding unit" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
          <div style="border-radius:16px;overflow:hidden;">
            <img src="assets/images/infra-12.jpg" alt="Raw material storage at Fabloom" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          </div>
        </div>
      </div>
    </section>

    <!-- QUALITY CONTROL PROCESS -->
    <section class="section bg-cream" aria-labelledby="qc-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Zero Compromise</span>
          <h2 class="section-title" id="qc-heading">Quality Control Process</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Four rigorous stages ensure every metre of fabric meets our exacting standards.</p>
        </div>

        <div class="process-steps mt-12 stagger-children">
          <div class="process-step reveal">
            <div class="process-step__num">01</div>
            <h3>Raw Material Check</h3>
            <p>All incoming silk yarn, linen fibre, and dye materials are tested for purity, GSM, moisture content, and colour consistency before entering production.</p>
          </div>
          <div class="process-step reveal">
            <div class="process-step__num">02</div>
            <h3>In-Process QC</h3>
            <p>On-floor supervisors inspect weave density, pattern alignment, and dye uniformity at every stage of production. Issues are corrected immediately.</p>
          </div>
          <div class="process-step reveal">
            <div class="process-step__num">03</div>
            <h3>Finished Goods QC</h3>
            <p>Completed fabric rolls undergo lab tests: wash fastness, colour bleeding, shrinkage percentage, thread count, and dimensional stability checks.</p>
          </div>
          <div class="process-step reveal">
            <div class="process-step__num">04</div>
            <h3>Dispatch Approval</h3>
            <p>Only QC-certified fabric receives a dispatch clearance. Each roll is labelled with batch ID, GSM, width, and composition before careful packaging.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CAPACITY STATS (Animated Counters) -->
    <section class="stats-bar" aria-label="Manufacturing capacity statistics">
      <div class="container">
        <div class="text-center reveal" style="margin-bottom:2.5rem;">
          <span class="section-label" style="color:rgba(192,40,42,0.8);">Our Capacity</span>
          <h2 class="section-title" style="color:white;">Manufacturing Numbers</h2>
        </div>
        <div class="stats-grid">
          <div class="stat-item reveal" aria-label="50 plus handlooms">
            <div class="stat-number">
              <span class="counter-val" data-target="50">0</span><span class="stat-suffix">+</span>
            </div>
            <div class="stat-label">Handlooms</div>
          </div>
          <div class="stat-item reveal stagger-2" aria-label="20 plus power looms">
            <div class="stat-number">
              <span class="counter-val" data-target="20">0</span><span class="stat-suffix">+</span>
            </div>
            <div class="stat-label">Power Looms</div>
          </div>
          <div class="stat-item reveal stagger-3" aria-label="10000 plus metres per month">
            <div class="stat-number">
              <span class="counter-val" data-target="10000">0</span><span class="stat-suffix">+</span>
            </div>
            <div class="stat-label">Metres / Month</div>
          </div>
          <div class="stat-item reveal stagger-4" aria-label="50 plus skilled artisans">
            <div class="stat-number">
              <span class="counter-val" data-target="50">0</span><span class="stat-suffix">+</span>
            </div>
            <div class="stat-label">Skilled Artisans</div>
          </div>
        </div>
      </div>
    </section>

    <!-- LOCATION MAP -->
    <section class="section bg-white" aria-labelledby="location-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Visit Us</span>
          <h2 class="section-title" id="location-heading">Find Our Factory</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Located in Mohiuddin Pur, Habibpur, Bhagalpur, Bihar 813113.</p>
        </div>
        <div class="mt-8 reveal" style="border-radius:16px;overflow:hidden;box-shadow:var(--shadow-lg);height:420px;">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3618.5!2d86.9755285!3d25.2260481!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39f04a12daaaaaab%3A0x189eb628e374c268!2sFABLOOM!5e0!3m2!1sen!2sin!4v1716000000000"
            title="Fabloom manufacturing unit location in Bhagalpur Bihar"
            width="100%" height="420"
            style="border:0;display:block;"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
        <div style="text-align:center;margin-top:2rem;">
          <a href="https://maps.app.goo.gl/TUQdwxNjSqC5y5e77" target="_blank" rel="noopener noreferrer" class="btn btn-outline reveal">
            Open in Google Maps
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          </a>
        </div>
      </div>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
