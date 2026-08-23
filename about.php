<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = 'About Us | Fabloom Group – India\'s Premier Textile Manufacturer';
$page_desc  = 'Learn about Fabloom Group of Company – our story, vision and commitment to handcrafted silk and linen fabrics from Bhagalpur, Bihar.';
$page_canonical = SITE_URL . '/about';
$page_og_image = SITE_URL . '/assets/images/og-about.jpg';
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
        "foundingDate": "2009",
        "description": "India's premier silk and linen fabric manufacturer based in Bhagalpur, Bihar. Specialising in handloom fabrics, block printing, digital printing, and custom textile solutions.",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "Mohiuddin Pur, Post–Habibpur",
          "addressLocality": "Bhagalpur",
          "addressRegion": "Bihar",
          "postalCode": "813113",
          "addressCountry": "IN"
        },
        "geo": { "@type": "GeoCoordinates", "latitude": 25.2260481, "longitude": 86.9755285 },
        "sameAs": [
          "https://www.facebook.com/fabloom86/",
          "https://www.instagram.com/thefabloom/",
          "https://x.com/fabloomsilk",
          "https://www.linkedin.com/in/anas-sami-967639132/"
        ]
      },
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://www.thefabloom.com/" },
          { "@type": "ListItem", "position": 2, "name": "About Us", "item": "https://www.thefabloom.com/about.html" }
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
          <span class="script-text" style="font-size:1.5rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">Who We Are</span>
          <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#FFFFFF;line-height:1.15;margin-bottom:1.25rem;">Our Story</h1>
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol style="list-style:none;display:flex;align-items:center;gap:0.5rem;padding:0;margin:0;flex-wrap:wrap;">
              <li><a href="index" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.875rem;">Home</a></li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:#C0282A;font-size:0.875rem;font-weight:600;" aria-current="page">About</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- COMPANY STORY -->
    <section class="section bg-white" aria-labelledby="story-heading">
      <div class="container">
        <div class="about-split">
          <div class="about-split__img reveal-left">
            <img src="assets/images/infra-facility-1.jpg"
                 alt="Fabloom artisans at work on handlooms in Bhagalpur Bihar"
                 loading="lazy" width="600" height="560"
                 onerror="this.src='https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=600&q=80'">
            <div class="about-img-badge hero-badge">
              <span class="badge-num">45+</span>
              <div class="badge-text">Years of Craft</div>
            </div>
          </div>

          <div class="about-split__content reveal-right">
            <span class="section-label">Est. 2009</span>
            <h2 class="section-title" id="story-heading">The Fabloom Story</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Fabloom Group of Company was founded in 2009 in the heart of Bhagalpur, Bihar — India's legendary Silk City. What began as a small handloom workshop driven by a passion for authentic textiles has grown into one of India's most trusted fabric manufacturers, blending centuries-old weaving traditions with contemporary design sensibilities.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Our roots run deep in the handloom heritage of Bhagalpur. Every artisan who works at Fabloom carries knowledge passed down through generations — knowledge of warp and weft, of natural dyes, of the subtle art of creating fabrics that breathe, drape, and endure. We honour this heritage every day, while continuously innovating to meet the demands of modern fashion, home décor, and export markets.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:2rem;">
              From pure silk and premium linen to block-printed masterpieces and digitally rendered contemporary designs — every fabric that leaves our Bhagalpur facility carries the mark of rigorous craftsmanship, eco-conscious production, and an unwavering commitment to excellence that is simply unmatched.
            </p>
            <ul class="usp-list">
              <li class="usp-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                100% natural fibres — pure silk and pure linen
              </li>
              <li class="usp-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                Direct manufacturer — factory to buyer pricing
              </li>
              <li class="usp-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                Custom dyeing, printing and finishing available
              </li>
              <li class="usp-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                PAN India delivery and export capability
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- VISION & MISSION -->
    <section class="section bg-cream" aria-labelledby="vm-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Our Purpose</span>
          <h2 class="section-title" id="vm-heading">Vision &amp; Mission</h2>
          <div class="gold-divider"></div>
        </div>

        <div class="grid-2 mt-12" style="gap:2rem;">
          <div class="card reveal-left" style="padding:2.5rem;border-top:3px solid var(--clr-red);border-radius:16px;">
            <div style="width:64px;height:64px;background:rgba(192,40,42,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.5rem;color:var(--clr-charcoal);margin-bottom:1rem;">Our Vision</h3>
            <p style="color:var(--clr-text-secondary);line-height:1.85;">
              To be recognised globally as India's most trusted handloom fabric manufacturer — a brand synonymous with quality, authenticity, and sustainable textile craftsmanship. We envision Bhagalpur's weaving heritage reaching every corner of the world through fabrics that carry the soul of India.
            </p>
          </div>

          <div class="card reveal-right" style="padding:2.5rem;border-top:3px solid var(--clr-red);border-radius:16px;">
            <div style="width:64px;height:64px;background:rgba(192,40,42,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.5rem;color:var(--clr-charcoal);margin-bottom:1rem;">Our Mission</h3>
            <p style="color:var(--clr-text-secondary);line-height:1.85;">
              To empower artisans, uphold weaving traditions, and deliver premium fabrics that exceed client expectations — through responsible manufacturing, continuous innovation, and a commitment to fair trade practices. We exist to make beautiful, sustainable textiles accessible to businesses and creators across India and beyond.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- OUR VALUES -->
    <section class="section bg-white" aria-labelledby="values-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">What Drives Us</span>
          <h2 class="section-title" id="values-heading">Our Core Values</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">The principles that guide every thread we weave.</p>
        </div>

        <div class="grid-4 mt-12 stagger-children">
          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <h3 class="service-card__title">Quality</h3>
            <p class="service-card__desc">Every metre of fabric undergoes rigorous quality checks. We never compromise on raw material purity, weave density, or finishing standards.</p>
          </div>

          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 class="service-card__title">Sustainability</h3>
            <p class="service-card__desc">We use eco-safe dyes, minimise water waste, and support natural fibre farming practices that protect the environment for future generations.</p>
          </div>

          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="2"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </div>
            <h3 class="service-card__title">Innovation</h3>
            <p class="service-card__desc">From digital textile printing to experimental weave structures, we constantly push the boundaries of what's possible in Indian textiles.</p>
          </div>

          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <h3 class="service-card__title">Trust</h3>
            <p class="service-card__desc">Over 1,000 satisfied clients across India trust Fabloom for consistent quality, transparent pricing, and on-time delivery — every single time.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- MANUFACTURING JOURNEY / TIMELINE -->
    <section class="section bg-texture" aria-labelledby="timeline-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Our Journey</span>
          <h2 class="section-title" id="timeline-heading">Manufacturing Milestones</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Fifteen years of weaving excellence — one milestone at a time.</p>
        </div>

        <div class="timeline mt-12" style="position:relative;max-width:800px;margin-left:auto;margin-right:auto;">
          <!-- Vertical line -->
          <div style="position:absolute;left:50%;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,var(--clr-red),rgba(192,40,42,0.2));transform:translateX(-50%);z-index:0;" aria-hidden="true"></div>

          <!-- 2009 -->
          <div class="timeline-item reveal" style="display:flex;align-items:flex-start;gap:2rem;margin-bottom:3rem;position:relative;z-index:1;">
            <div style="flex:1;text-align:right;padding-right:2rem;">
              <div class="tag tag-gold" style="display:inline-block;margin-bottom:0.75rem;">2009</div>
              <h3 style="font-family:'Playfair Display',serif;font-size:1.25rem;color:var(--clr-charcoal);margin-bottom:0.5rem;">The Founding</h3>
              <p style="color:var(--clr-text-secondary);line-height:1.75;font-size:0.9375rem;">Fabloom was established in Mohiuddin Pur, Bhagalpur with a small team of five master weavers and a clear vision: to make authentic Bhagalpur silk and linen accessible to the world.</p>
            </div>
            <div style="width:20px;height:20px;background:var(--clr-red);border-radius:50%;border:3px solid white;box-shadow:0 0 0 3px var(--clr-red);flex-shrink:0;margin-top:0.5rem;" aria-hidden="true"></div>
            <div style="flex:1;padding-left:2rem;"></div>
          </div>

          <!-- 2015 -->
          <div class="timeline-item reveal" style="display:flex;align-items:flex-start;gap:2rem;margin-bottom:3rem;position:relative;z-index:1;">
            <div style="flex:1;padding-right:2rem;"></div>
            <div style="width:20px;height:20px;background:var(--clr-red);border-radius:50%;border:3px solid white;box-shadow:0 0 0 3px var(--clr-red);flex-shrink:0;margin-top:0.5rem;" aria-hidden="true"></div>
            <div style="flex:1;padding-left:2rem;">
              <div class="tag tag-gold" style="display:inline-block;margin-bottom:0.75rem;">2015</div>
              <h3 style="font-family:'Playfair Display',serif;font-size:1.25rem;color:var(--clr-charcoal);margin-bottom:0.5rem;">Infrastructure Expansion</h3>
              <p style="color:var(--clr-text-secondary);line-height:1.75;font-size:0.9375rem;">Expanded production facility with 30+ handlooms and a dedicated dye unit. Launched premium linen range to complement the existing silk collection, doubling our product catalogue.</p>
            </div>
          </div>

          <!-- 2020 -->
          <div class="timeline-item reveal" style="display:flex;align-items:flex-start;gap:2rem;margin-bottom:3rem;position:relative;z-index:1;">
            <div style="flex:1;text-align:right;padding-right:2rem;">
              <div class="tag tag-gold" style="display:inline-block;margin-bottom:0.75rem;">2020</div>
              <h3 style="font-family:'Playfair Display',serif;font-size:1.25rem;color:var(--clr-charcoal);margin-bottom:0.5rem;">Digital Printing Era</h3>
              <p style="color:var(--clr-text-secondary);line-height:1.75;font-size:0.9375rem;">Invested in state-of-the-art digital textile printing technology, enabling us to offer unlimited colour palettes, photorealistic prints, and faster turnaround times for fashion clients.</p>
            </div>
            <div style="width:20px;height:20px;background:var(--clr-red);border-radius:50%;border:3px solid white;box-shadow:0 0 0 3px var(--clr-red);flex-shrink:0;margin-top:0.5rem;" aria-hidden="true"></div>
            <div style="flex:1;padding-left:2rem;"></div>
          </div>

          <!-- 2024 -->
          <div class="timeline-item reveal" style="display:flex;align-items:flex-start;gap:2rem;margin-bottom:3rem;position:relative;z-index:1;">
            <div style="flex:1;padding-right:2rem;"></div>
            <div style="width:20px;height:20px;background:var(--clr-red);border-radius:50%;border:3px solid white;box-shadow:0 0 0 3px var(--clr-red);flex-shrink:0;margin-top:0.5rem;" aria-hidden="true"></div>
            <div style="flex:1;padding-left:2rem;">
              <div class="tag tag-gold" style="display:inline-block;margin-bottom:0.75rem;">2024</div>
              <h3 style="font-family:'Playfair Display',serif;font-size:1.25rem;color:var(--clr-charcoal);margin-bottom:0.5rem;">Export Growth &amp; Noida Branch</h3>
              <p style="color:var(--clr-text-secondary);line-height:1.75;font-size:0.9375rem;">Opened our Noida (Sec-15) branch office to serve North India's fashion and design community. Initiated fabric exports and onboarded international design houses as clients.</p>
            </div>
          </div>

          <!-- 2026 -->
          <div class="timeline-item reveal" style="display:flex;align-items:flex-start;gap:2rem;position:relative;z-index:1;">
            <div style="flex:1;text-align:right;padding-right:2rem;">
              <div class="tag tag-gold" style="display:inline-block;margin-bottom:0.75rem;">2026 – Present</div>
              <h3 style="font-family:'Playfair Display',serif;font-size:1.25rem;color:var(--clr-charcoal);margin-bottom:0.5rem;">A New Digital Chapter</h3>
              <p style="color:var(--clr-text-secondary);line-height:1.75;font-size:0.9375rem;">Launching our redesigned website and expanding our online presence to serve clients across India and globally — bringing authentic Bhagalpur textiles directly to designers, boutiques, and conscious consumers.</p>
            </div>
            <div style="width:20px;height:20px;background:var(--clr-red);border-radius:50%;border:3px solid white;box-shadow:0 0 0 3px var(--clr-red);flex-shrink:0;margin-top:0.5rem;" aria-hidden="true"></div>
            <div style="flex:1;padding-left:2rem;"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="section bg-white" aria-labelledby="why-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Our Advantage</span>
          <h2 class="section-title" id="why-heading">Why Choose Fabloom?</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Six reasons our clients keep coming back — and refer us to others.</p>
        </div>

        <div class="grid-3 mt-12 stagger-children">
          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <h3 class="service-card__title">Direct from Manufacturer</h3>
            <p class="service-card__desc">No middlemen, no markups. You buy directly from our Bhagalpur factory, ensuring the best price for premium quality fabric — whether you need 50 metres or 5,000.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <h3 class="service-card__title">100+ Fabric Variants</h3>
            <p class="service-card__desc">From natural weaves to digitally printed designs, our catalogue of over 100 fabric variants ensures you find the perfect textile for any application or aesthetic.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 class="service-card__title">Strict Quality Control</h3>
            <p class="service-card__desc">Every batch goes through a four-stage quality inspection: raw material, in-process, finished goods, and pre-dispatch. Only perfect fabric leaves our unit.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="service-card__title">Fast Turnaround</h3>
            <p class="service-card__desc">Our in-house production capability means minimal lead times. Standard orders dispatched in 5–7 working days; custom orders in 10–15 days depending on complexity.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <h3 class="service-card__title">PAN India Delivery</h3>
            <p class="service-card__desc">We deliver across India with reliable logistics partners. Fragile silk and bulk linen shipments are packed with care to ensure your fabric arrives in perfect condition.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <h3 class="service-card__title">Dedicated Support</h3>
            <p class="service-card__desc">Our textile experts guide you through selection, customisation, and bulk ordering. Available Monday–Saturday via phone, email, and WhatsApp for a seamless experience.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- TEAM SECTION -->
    <section class="section bg-cream" aria-labelledby="team-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">The People Behind Fabloom</span>
          <h2 class="section-title" id="team-heading">Meet Our Leadership Team</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">A family-rooted organisation with deep textile heritage — led by visionaries who blend traditional craftsmanship with modern business excellence.</p>
        </div>

        <div class="team-grid stagger-children" style="display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;margin-top:3.5rem;">

          <!-- Card 1: CEO & Founder -->
          <article class="reveal" style="background:#fff;border:1px solid var(--clr-border);border-radius:20px;padding:2.5rem 2rem;text-align:center;transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s; transition-timing-function:var(--ease-out);position:relative;overflow:hidden;" onmouseenter="this.style.transform='translateY(-8px)';this.style.boxShadow='var(--shadow-xl)';this.style.borderColor='var(--clr-red)'" onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--clr-border)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--clr-red-dark),var(--clr-red-light));border-radius:20px 20px 0 0;"></div>
            <div style="width:120px;height:120px;border-radius:50%;margin:0 auto 1.25rem;border:3px solid var(--clr-red);padding:3px;box-shadow:var(--shadow-red);overflow:hidden;">
              <img src="assets/images/Mr-Iqbal-Hossain-jpg-300x300.webp"
                   alt="Mr. Iqbal Hossain – CEO & Founder, Fabloom Group"
                   width="120" height="120"
                   loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                   onerror="this.parentElement.style.background='linear-gradient(135deg,#C0282A,#8B1A1C)';this.style.display='none';this.parentElement.innerHTML+='<span style=\'font-family:Playfair Display,serif;font-size:2.5rem;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;height:100%;\'>IH</span>'">
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--clr-charcoal);margin-bottom:0.4rem;">Mr. Iqbal Hossain</h3>
            <div style="display:inline-block;background:rgba(192,40,42,0.1);color:var(--clr-red-dark);font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;padding:0.3rem 0.9rem;border-radius:999px;margin-bottom:1rem;">CEO &amp; Founder</div>
            <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.75;">The visionary who founded Fabloom, building on the legacy of Chand Silk Emporium (est. 1978). His four-decade passion for handloom craftsmanship drives the company's global ambitions.</p>
          </article>

          <!-- Card 2: Director of Design -->
          <article class="reveal" style="background:#fff;border:1px solid var(--clr-border);border-radius:20px;padding:2.5rem 2rem;text-align:center;transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s; transition-timing-function:var(--ease-out);position:relative;overflow:hidden;" onmouseenter="this.style.transform='translateY(-8px)';this.style.boxShadow='var(--shadow-xl)';this.style.borderColor='var(--clr-red)'" onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--clr-border)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--clr-red-dark),var(--clr-red-light));border-radius:20px 20px 0 0;"></div>
            <div style="width:120px;height:120px;border-radius:50%;margin:0 auto 1.25rem;border:3px solid var(--clr-red);padding:3px;box-shadow:var(--shadow-red);overflow:hidden;">
              <img src="assets/images/Mr-Zakir-Hossain-jpg-300x300.webp"
                   alt="Mr. Zakir Hossain – Director of Design & Product Development, Fabloom"
                   width="120" height="120"
                   loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                   onerror="this.parentElement.style.background='linear-gradient(135deg,#C0282A,#8B1A1C)';this.style.display='none';this.parentElement.innerHTML+='<span style=\'font-family:Playfair Display,serif;font-size:2.5rem;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;height:100%;\'>ZH</span>'">
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--clr-charcoal);margin-bottom:0.4rem;">Mr. Zakir Hossain</h3>
            <div style="display:inline-block;background:rgba(192,40,42,0.1);color:var(--clr-red-dark);font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;padding:0.3rem 0.9rem;border-radius:999px;margin-bottom:1rem;">Director – Design &amp; Product Dev.</div>
            <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.75;">The creative force behind Fabloom's product range. He oversees design direction, pattern development, and product innovation — ensuring every collection reflects both tradition and contemporary relevance.</p>
          </article>

          <!-- Card 3: Director of Operations -->
          <article class="reveal" style="background:#fff;border:1px solid var(--clr-border);border-radius:20px;padding:2.5rem 2rem;text-align:center;transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s; transition-timing-function:var(--ease-out);position:relative;overflow:hidden;" onmouseenter="this.style.transform='translateY(-8px)';this.style.boxShadow='var(--shadow-xl)';this.style.borderColor='var(--clr-red)'" onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--clr-border)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--clr-red-dark),var(--clr-red-light));border-radius:20px 20px 0 0;"></div>
            <div style="width:120px;height:120px;border-radius:50%;margin:0 auto 1.25rem;border:3px solid var(--clr-red);padding:3px;box-shadow:var(--shadow-red);overflow:hidden;">
              <img src="assets/images/Mr-Anas-sami-jpg-300x300.webp"
                   alt="Mr. Anas Sami – Director of Operations & Marketing, Fabloom"
                   width="120" height="120"
                   loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                   onerror="this.parentElement.style.background='linear-gradient(135deg,#C0282A,#8B1A1C)';this.style.display='none';this.parentElement.innerHTML+='<span style=\'font-family:Playfair Display,serif;font-size:2.5rem;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;height:100%;\'>AS</span>'">
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--clr-charcoal);margin-bottom:0.4rem;">Mr. Anas Sami</h3>
            <div style="display:inline-block;background:rgba(192,40,42,0.1);color:var(--clr-red-dark);font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;padding:0.3rem 0.9rem;border-radius:999px;margin-bottom:1rem;">Director – Operations &amp; Marketing</div>
            <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.75;">Driving Fabloom's market reach and operational excellence. Anas manages client relationships, business development, and digital marketing — connecting Fabloom's heritage crafts with global buyers.</p>
            <a href="https://www.linkedin.com/in/anas-sami-967639132/" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:0.4rem;margin-top:1rem;min-height:44px;font-size:0.75rem;font-weight:600;color:#0A66C2;text-decoration:none;transition:opacity 0.2s;" aria-label="Anas Sami on LinkedIn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
              LinkedIn Profile
            </a>
          </article>

          <!-- Card 4: Purchase Head -->
          <article class="reveal" style="background:#fff;border:1px solid var(--clr-border);border-radius:20px;padding:2.5rem 2rem;text-align:center;transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s; transition-timing-function:var(--ease-out);position:relative;overflow:hidden;" onmouseenter="this.style.transform='translateY(-8px)';this.style.boxShadow='var(--shadow-xl)';this.style.borderColor='var(--clr-red)'" onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--clr-border)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--clr-red-dark),var(--clr-red-light));border-radius:20px 20px 0 0;"></div>
            <div style="width:120px;height:120px;border-radius:50%;margin:0 auto 1.25rem;border:3px solid var(--clr-red);padding:3px;box-shadow:var(--shadow-red);overflow:hidden;">
              <img src="assets/images/Mr-Asmat-sami-jpg-300x300.webp"
                   alt="Mr. Asmat Sami – Purchase Head, Fabloom Group"
                   width="120" height="120"
                   loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                   onerror="this.parentElement.style.background='linear-gradient(135deg,#C0282A,#8B1A1C)';this.style.display='none';this.parentElement.innerHTML+='<span style=\'font-family:Playfair Display,serif;font-size:2.5rem;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;height:100%;\'>AS</span>'">
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--clr-charcoal);margin-bottom:0.4rem;">Mr. Asmat Sami</h3>
            <div style="display:inline-block;background:rgba(192,40,42,0.1);color:var(--clr-red-dark);font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;padding:0.3rem 0.9rem;border-radius:999px;margin-bottom:1rem;">Purchase Head</div>
            <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.75;">Responsible for sourcing the finest raw materials — pure silk yarn, quality linen fibre, eco-safe dyes, and printing materials — ensuring every input meets Fabloom's exacting quality standards.</p>
          </article>

          <!-- Card 5: Logistics Head -->
          <article class="reveal" style="background:#fff;border:1px solid var(--clr-border);border-radius:20px;padding:2.5rem 2rem;text-align:center;transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s; transition-timing-function:var(--ease-out);position:relative;overflow:hidden;" onmouseenter="this.style.transform='translateY(-8px)';this.style.boxShadow='var(--shadow-xl)';this.style.borderColor='var(--clr-red)'" onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--clr-border)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--clr-red-dark),var(--clr-red-light));border-radius:20px 20px 0 0;"></div>
            <div style="width:120px;height:120px;border-radius:50%;margin:0 auto 1.25rem;border:3px solid var(--clr-red);padding:3px;box-shadow:var(--shadow-red);overflow:hidden;">
              <img src="assets/images/Mr-Naimat-sami-jpg-300x300.webp"
                   alt="Mr. Naimat Sami – Logistics Head, Fabloom Group"
                   width="120" height="120"
                   loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                   onerror="this.parentElement.style.background='linear-gradient(135deg,#C0282A,#8B1A1C)';this.style.display='none';this.parentElement.innerHTML+='<span style=\'font-family:Playfair Display,serif;font-size:2.5rem;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;height:100%;\'>NS</span>'">
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--clr-charcoal);margin-bottom:0.4rem;">Mr. Naimat Sami</h3>
            <div style="display:inline-block;background:rgba(192,40,42,0.1);color:var(--clr-red-dark);font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;padding:0.3rem 0.9rem;border-radius:999px;margin-bottom:1rem;">Logistics Head</div>
            <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.75;">Overseeing end-to-end supply chain and delivery operations — from factory dispatch to doorstep arrival across India and internationally. Ensuring every order arrives on time and in perfect condition.</p>
          </article>

          <!-- Card 6: IT Head -->
          <article class="reveal" style="background:#fff;border:1px solid var(--clr-border);border-radius:20px;padding:2.5rem 2rem;text-align:center;transition-property:color, background-color, border-color, box-shadow, transform, opacity;transition-duration:0.25s; transition-timing-function:var(--ease-out);position:relative;overflow:hidden;" onmouseenter="this.style.transform='translateY(-8px)';this.style.boxShadow='var(--shadow-xl)';this.style.borderColor='var(--clr-red)'" onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--clr-border)'">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--clr-red-dark),var(--clr-red-light));border-radius:20px 20px 0 0;"></div>
            <div style="width:120px;height:120px;border-radius:50%;margin:0 auto 1.25rem;border:3px solid var(--clr-red);padding:3px;box-shadow:var(--shadow-red);overflow:hidden;">
              <img src="assets/images/20210818_200713-Copy-jpg-e1732563728731-300x300.webp"
                   alt="Mr. Najeeb Hussain – IT Head, Fabloom Group"
                   width="120" height="120"
                   loading="lazy"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%;"
                   onerror="this.parentElement.style.background='linear-gradient(135deg,#C0282A,#8B1A1C)';this.style.display='none';this.parentElement.innerHTML+='<span style=\'font-family:Playfair Display,serif;font-size:2.5rem;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;height:100%;\'>NH</span>'">
            </div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--clr-charcoal);margin-bottom:0.4rem;">Mr. Najeeb Hussain</h3>
            <div style="display:inline-block;background:rgba(192,40,42,0.1);color:var(--clr-red-dark);font-size:0.75rem;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;padding:0.3rem 0.9rem;border-radius:999px;margin-bottom:1rem;">IT Head</div>
            <p style="font-size:0.875rem;color:var(--clr-text-secondary);line-height:1.75;">Leading Fabloom's technology infrastructure — from e-commerce platform management and digital systems to website operations — ensuring the brand's online presence matches the quality of its products.</p>
          </article>

        </div><!-- end team grid -->

        <!-- Artisan note -->
        <div style="text-align:center;margin-top:3.5rem;padding:2rem;background:white;border-radius:16px;border:1px solid var(--clr-border);" class="reveal">
          <span class="script-text" style="font-size:1.4rem;">Backed by 50+ Master Artisans</span>
          <p style="margin-top:0.75rem;color:var(--clr-text-secondary);font-size:0.9375rem;line-height:1.8;max-width:680px;margin-inline:auto;">
            Behind our leadership team are over 50 skilled master weavers, dyers, block printers, and finishing specialists — many from multi-generational handloom families in Bhagalpur. Their hands craft every fabric we produce.
          </p>
        </div>

      </div>
    </section>

    <!-- CTA BANNER -->
    <section class="section bg-texture" aria-labelledby="cta-heading" style="background:linear-gradient(135deg,#1C0E0E,#0F0F0F);">
      <div class="container">
        <div style="text-align:center;" class="reveal">
          <span class="script-text" style="font-size:1.8rem;color:#D93B3D;display:block;margin-bottom:0.75rem;">Let's Create Together</span>
          <h2 class="section-title" id="cta-heading" style="color:white;">Ready to Create Something Beautiful?</h2>
          <div class="gold-divider"></div>
          <p style="margin-top:1.5rem;color:rgba(255,255,255,0.7);max-width:560px;margin-left:auto;margin-right:auto;line-height:1.85;">
            Whether you're a fashion designer, boutique owner, home décor brand, or conscious consumer — we'd love to help you find the perfect fabric. Get in touch today for a free consultation and sample.
          </p>
          <div style="margin-top:2.5rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="enquiry" class="btn btn-primary">Submit an Enquiry</a>
            <a href="contact" class="btn btn-outline-white">Contact Us</a>
          </div>
        </div>
      </div>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
