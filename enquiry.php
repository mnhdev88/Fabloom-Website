<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Fabric Enquiry | Fabloom – Linen & Silk Enquiry Form';
$page_desc  = 'Submit your linen or silk fabric enquiry directly to Fabloom\'s manufacturing team. Custom orders, bulk requirements, and samples welcome.';
$page_canonical = SITE_URL . '/enquiry';
$page_og_image = SITE_URL . '/assets/images/og-enquiry.jpg';
$page_schema = <<<'JSONLD'
{
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "ContactPage",
        "@id": "https://www.thefabloom.com/enquiry.html",
        "name": "Fabric Enquiry – Fabloom Group of Company",
        "description": "Submit linen or silk fabric enquiries directly to Fabloom's manufacturing team in Bhagalpur, Bihar.",
        "url": "https://www.thefabloom.com/enquiry.html",
        "mainEntity": {
          "@type": "LocalBusiness",
          "name": "Fabloom Group of Company",
          "telephone": "+919760058796",
          "email": "info@thefabloom.com"
        }
      },
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://www.thefabloom.com/" },
          { "@type": "ListItem", "position": 2, "name": "Enquiry", "item": "https://www.thefabloom.com/enquiry.html" }
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
          <span class="script-text" style="font-size:1.5rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">Let's Work Together</span>
          <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#FFFFFF;line-height:1.15;margin-bottom:1.25rem;">Submit Your Fabric Enquiry</h1>
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol style="list-style:none;display:flex;align-items:center;gap:0.5rem;padding:0;margin:0;flex-wrap:wrap;">
              <li><a href="index" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.875rem;">Home</a></li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:#C0282A;font-size:0.875rem;font-weight:600;" aria-current="page">Enquiry</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- TAB SWITCHER + FORMS -->
    <section class="section bg-cream" aria-labelledby="enquiry-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Choose Your Category</span>
          <h2 class="section-title" id="enquiry-heading">Fabric Enquiry Form</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Select the fabric type below and fill in your requirements. We respond within 24 working hours.</p>
        </div>

        <!-- Tab Buttons -->
        <div class="mt-8 reveal" style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;" role="tablist" aria-label="Enquiry form tabs">
          <button class="tab-btn active" id="tab-linen-btn" role="tab" aria-selected="true" aria-controls="tab-linen" onclick="switchTab('linen')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Linen Enquiry
          </button>
          <button class="tab-btn" id="tab-silk-btn" role="tab" aria-selected="false" aria-controls="tab-silk" onclick="switchTab('silk')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            Silk Enquiry
          </button>
        </div>

        <div class="mt-8">
          <!-- LINEN ENQUIRY FORM -->
          <div class="tab-content active" id="tab-linen" role="tabpanel" aria-labelledby="tab-linen-btn">
            <div style="max-width:760px;margin:0 auto;">
              <form id="linen-enquiry-form" data-enquiry-form="linen" method="POST" action="api/enquiry.php" enctype="multipart/form-data" novalidate style="background:white;border-radius:24px;padding:2.5rem;box-shadow:var(--shadow-lg);" aria-label="Linen fabric enquiry form">
                <div style="margin-bottom:2rem;">
                  <h3 style="font-family:'Playfair Display',serif;font-size:1.5rem;color:var(--clr-charcoal);margin-bottom:0.4rem;">Linen Fabric Enquiry</h3>
                  <p style="font-size:0.875rem;color:var(--clr-text-muted);">Tell us about your linen requirements. All fields marked * are required.</p>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="l-name">Full Name <span class="required">*</span></label>
                    <input type="text" id="l-name" name="name" class="form-control" placeholder="Your full name" required autocomplete="name">
                    <span class="form-error" id="err-l-name">Please enter your name.</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="l-company">Company Name</label>
                    <input type="text" id="l-company" name="company" class="form-control" placeholder="Your company / brand name" autocomplete="organization">
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="l-phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="l-phone" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX" required autocomplete="tel">
                    <span class="form-error" id="err-l-phone">Please enter a valid phone number.</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="l-email">Email Address</label>
                    <input type="email" id="l-email" name="email" class="form-control" placeholder="you@example.com" autocomplete="email">
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="l-type">Linen Type <span class="required">*</span></label>
                    <select id="l-type" name="linen_type" class="form-control" required>
                      <option value="" disabled selected>Select linen type</option>
                      <option>Natural / Undyed Linen</option>
                      <option>Bleached Linen</option>
                      <option>Dyed Linen (Solid)</option>
                      <option>Dyed Linen (Ombre/Gradient)</option>
                      <option>Block Printed Linen</option>
                      <option>Digital Printed Linen</option>
                      <option>Hand Brush Linen</option>
                      <option>Cotton-Linen Blend</option>
                      <option>Not Sure / Need Guidance</option>
                    </select>
                    <span class="form-error" id="err-l-type">Please select linen type.</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="l-width">Fabric Width</label>
                    <select id="l-width" name="width" class="form-control">
                      <option value="" disabled selected>Select width</option>
                      <option>44 inches</option>
                      <option>54 inches</option>
                      <option>58 inches / 60 inches</option>
                      <option>Custom Width</option>
                      <option>Not Sure</option>
                    </select>
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="l-gsm">GSM (Grams per Square Metre)</label>
                    <select id="l-gsm" name="gsm" class="form-control">
                      <option value="" disabled selected>Select GSM range</option>
                      <option>80–120 GSM (Lightweight)</option>
                      <option>120–180 GSM (Medium Weight)</option>
                      <option>180–250 GSM (Heavy Weight)</option>
                      <option>Custom GSM</option>
                      <option>Not Sure</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="l-qty">Quantity Required (metres)</label>
                    <select id="l-qty" name="quantity" class="form-control">
                      <option value="" disabled selected>Select quantity</option>
                      <option>Sample (1–10 metres)</option>
                      <option>Small Order (10–50 metres)</option>
                      <option>Medium Order (50–200 metres)</option>
                      <option>Bulk Order (200–1000 metres)</option>
                      <option>Large Bulk (1000+ metres)</option>
                    </select>
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="l-colour">Colour Requirement</label>
                    <input type="text" id="l-colour" name="colour" class="form-control" placeholder="e.g., Ivory, Navy, Custom Pantone">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="l-finish">Finish Type</label>
                    <select id="l-finish" name="finish" class="form-control">
                      <option value="" disabled selected>Select finish</option>
                      <option>Natural (No Finish)</option>
                      <option>Soft Washed</option>
                      <option>Stone Washed</option>
                      <option>Enzyme Washed</option>
                      <option>Mercerised</option>
                      <option>Not Sure</option>
                    </select>
                  </div>
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                  <label class="form-label" for="l-message">Additional Details / Message</label>
                  <textarea id="l-message" name="message" class="form-control" rows="4" placeholder="Describe your requirements — end use, design reference, delivery timeline, etc."></textarea>
                </div>

                <div class="form-group" style="margin-bottom:1.5rem;">
                  <label class="form-label" for="l-file">Attach Reference File (Optional)</label>
                  <input type="file" id="l-file" name="reference_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="padding:0.75rem;">
                  <span style="font-size:0.75rem;color:var(--clr-text-muted);">Accepted: JPG, PNG, PDF, DOC. Max 5MB.</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;">
                  <span class="btn-text">Submit Linen Enquiry</span>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>

                <div id="linen-success" style="display:none;text-align:center;padding:2rem;color:#27AE60;" role="alert" aria-live="polite">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#27AE60" stroke-width="2" style="margin:0 auto 1rem;display:block;" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                  <strong>Linen Enquiry Submitted!</strong> Our team will get back to you within 24 working hours.
                </div>
              </form>
            </div>
          </div>

          <!-- SILK ENQUIRY FORM -->
          <div class="tab-content" id="tab-silk" role="tabpanel" aria-labelledby="tab-silk-btn">
            <div style="max-width:760px;margin:0 auto;">
              <form id="silk-enquiry-form" data-enquiry-form="silk" method="POST" action="api/enquiry.php" enctype="multipart/form-data" novalidate style="background:white;border-radius:24px;padding:2.5rem;box-shadow:var(--shadow-lg);" aria-label="Silk fabric enquiry form">
                <div style="margin-bottom:2rem;">
                  <h3 style="font-family:'Playfair Display',serif;font-size:1.5rem;color:var(--clr-charcoal);margin-bottom:0.4rem;">Silk Fabric Enquiry</h3>
                  <p style="font-size:0.875rem;color:var(--clr-text-muted);">Tell us about your silk requirements. All fields marked * are required.</p>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="s-name">Full Name <span class="required">*</span></label>
                    <input type="text" id="s-name" name="name" class="form-control" placeholder="Your full name" required autocomplete="name">
                    <span class="form-error" id="err-s-name">Please enter your name.</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="s-company">Company Name</label>
                    <input type="text" id="s-company" name="company" class="form-control" placeholder="Your company / brand name" autocomplete="organization">
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="s-phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="s-phone" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX" required autocomplete="tel">
                    <span class="form-error" id="err-s-phone">Please enter a valid phone number.</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="s-email">Email Address</label>
                    <input type="email" id="s-email" name="email" class="form-control" placeholder="you@example.com" autocomplete="email">
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="s-type">Silk Type <span class="required">*</span></label>
                    <select id="s-type" name="silk_type" class="form-control" required>
                      <option value="" disabled selected>Select silk type</option>
                      <option>Pure Bhagalpur Silk</option>
                      <option>Katan Silk</option>
                      <option>Georgette Silk</option>
                      <option>Dupion Silk</option>
                      <option>Tussar Silk</option>
                      <option>Silk-Linen Blend</option>
                      <option>Not Sure / Need Guidance</option>
                    </select>
                    <span class="form-error" id="err-s-type">Please select silk type.</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="s-weave">Weave Type</label>
                    <select id="s-weave" name="weave_type" class="form-control">
                      <option value="" disabled selected>Select weave</option>
                      <option>Plain Weave</option>
                      <option>Twill Weave</option>
                      <option>Satin Weave</option>
                      <option>Handloom Slub Weave</option>
                      <option>Jacquard</option>
                      <option>Not Sure</option>
                    </select>
                  </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                  <div class="form-group">
                    <label class="form-label" for="s-zari">Zari / Brocade Work</label>
                    <select id="s-zari" name="zari_work" class="form-control">
                      <option value="" disabled selected>Zari work required?</option>
                      <option>Yes – Gold Zari</option>
                      <option>Yes – Silver Zari</option>
                      <option>Yes – Copper Zari</option>
                      <option>No – Plain Weave Only</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="s-dyeing">Dyeing Preference</label>
                    <select id="s-dyeing" name="dyeing_preference" class="form-control">
                      <option value="" disabled selected>Select dyeing</option>
                      <option>Natural / Undyed</option>
                      <option>Solid Dyed – One Colour</option>
                      <option>Ombre / Gradient Dyed</option>
                      <option>Resist Dyed / Shibori</option>
                      <option>Block Printed</option>
                      <option>Digital Printed</option>
                      <option>Not Sure</option>
                    </select>
                  </div>
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                  <label class="form-label" for="s-qty">Quantity Required (metres)</label>
                  <select id="s-qty" name="quantity" class="form-control">
                    <option value="" disabled selected>Select quantity</option>
                    <option>Sample (1–10 metres)</option>
                    <option>Small Order (10–50 metres)</option>
                    <option>Medium Order (50–200 metres)</option>
                    <option>Bulk Order (200–500 metres)</option>
                    <option>Large Bulk (500+ metres)</option>
                  </select>
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                  <label class="form-label" for="s-message">Additional Details / Message</label>
                  <textarea id="s-message" name="message" class="form-control" rows="4" placeholder="Describe your silk requirements — end use (saree, dress, dupatta, etc.), design inspiration, delivery timeline, colour references..."></textarea>
                </div>

                <div class="form-group" style="margin-bottom:1.5rem;">
                  <label class="form-label" for="s-file">Attach Reference File (Optional)</label>
                  <input type="file" id="s-file" name="reference_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="padding:0.75rem;">
                  <span style="font-size:0.75rem;color:var(--clr-text-muted);">Accepted: JPG, PNG, PDF, DOC. Max 5MB.</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;">
                  <span class="btn-text">Submit Silk Enquiry</span>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>

                <div id="silk-success" style="display:none;text-align:center;padding:2rem;color:#27AE60;" role="alert" aria-live="polite">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#27AE60" stroke-width="2" style="margin:0 auto 1rem;display:block;" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                  <strong>Silk Enquiry Submitted!</strong> Our team will get back to you within 24 working hours.
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- WHY ENQUIRE WITH US -->
    <section class="section bg-white" aria-labelledby="why-enquire-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Our Promise</span>
          <h2 class="section-title" id="why-enquire-heading">Why Enquire with Fabloom?</h2>
          <div class="gold-divider"></div>
        </div>

        <div class="grid-4 mt-12 stagger-children">
          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="service-card__title">24-Hour Response</h3>
            <p class="service-card__desc">Our team responds to every enquiry within 24 working hours — often much faster. You'll never be left waiting for important fabric decisions.</p>
          </div>
          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <h3 class="service-card__title">Factory Direct</h3>
            <p class="service-card__desc">You're enquiring directly with the manufacturer — no middlemen, no inflated quotes. Get the best price for the best quality, straight from our Bhagalpur facility.</p>
          </div>
          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <h3 class="service-card__title">Free Samples</h3>
            <p class="service-card__desc">We'll send complimentary fabric samples so you can feel the quality before committing to a larger order. Up to 3 swatches per request, delivered across India.</p>
          </div>
          <div class="service-card reveal" style="text-align:center;">
            <div class="service-card__icon" style="margin:0 auto 1.25rem;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <h3 class="service-card__title">Expert Guidance</h3>
            <p class="service-card__desc">Not sure what fabric is right for your project? Our textile experts will guide you through selection, pricing, customisation options, and lead times with no pressure.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- WHATSAPP QUICK CTA -->
    <section class="section bg-texture" aria-labelledby="wa-cta-heading" style="background:linear-gradient(135deg,#0F0F0F,#1C0E0E);">
      <div class="container">
        <div style="text-align:center;max-width:640px;margin:0 auto;" class="reveal">
          <svg width="60" height="60" viewBox="0 0 24 24" fill="#25D366" style="margin:0 auto 1.5rem;display:block;" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          <h2 class="section-title" id="wa-cta-heading" style="color:white;">Prefer to Chat on WhatsApp?</h2>
          <div class="gold-divider"></div>
          <p style="margin-top:1.5rem;color:rgba(255,255,255,0.75);line-height:1.85;margin-bottom:2.5rem;">
            Send us a direct message on WhatsApp with your fabric requirements — include quantity, colour, and end use. Our textile experts will respond with pricing and availability within hours.
          </p>
          <a href="https://wa.me/919760058796?text=Hello%20Fabloom%2C%20I%20am%20interested%20in%20enquiring%20about%20your%20fabric%20collection.%20Please%20guide%20me." target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="font-size:1rem;padding:1rem 2.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Chat on WhatsApp Now
          </a>
          <div style="margin-top:1.25rem;color:rgba(255,255,255,0.5);font-size:0.875rem;">+91 97600 58796 · Mon–Sat, 9:00 AM – 6:00 PM IST</div>
        </div>
      </div>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
