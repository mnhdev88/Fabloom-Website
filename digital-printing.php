<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/faq.php';

$page_title = 'Digital Textile Printing for Brands & Designers | Fabloom';
$page_desc  = 'Custom digital printing on silk, linen and cotton at Fabloom — your artwork printed on an industrial Konica Minolta Nassenger 8, with in-house design, strike-offs and runs from 5 metres per design.';
$page_canonical = SITE_URL . '/digital-printing';
$page_og_image  = SITE_URL . '/assets/images/services/og-digital-printing.jpg';

$min_digital = (int) (MIN_ORDER_BY_CATEGORY['digital-print'] ?? MIN_ORDER_METRES);

// ── FAQ ──────────────────────────────────────────────────────
// Service questions first, then the digital-print set from includes/faq.php,
// so minimums, artwork specs and base fabrics are stated in one place only.
// No lead times or per-metre rates here — those are confirmed per quotation.
$print_faqs = array_merge([
    [
        'q' => 'Does Fabloom do custom digital printing for brands?',
        'a' => 'Yes. Fabloom digitally prints custom designs for fashion brands, boutiques, designers and home-textile labels. Send your artwork, or a reference for our design team to develop, and we run a strike-off on your chosen fabric before production.',
    ],
    [
        'q' => 'What machine does Fabloom use for digital printing?',
        'a' => 'Fabloom prints on an industrial Konica Minolta Nassenger 8 textile inkjet printer, run through colour-managed RIP software by a dedicated operator. Each job is matched against its approved strike-off before the full length is printed.',
    ],
    [
        'q' => 'How much does custom digital printing cost?',
        'a' => 'The price depends on the base fabric, the quantity, and whether the artwork is supplied print-ready or needs design work, so it is confirmed on your quotation. Send your design and requirements through the enquiry form or on WhatsApp for a quote.',
    ],
], faq_for_category('digital-print'));

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'Service',
            '@id'         => $page_canonical . '#service',
            'name'        => 'Custom Digital Textile Printing',
            'serviceType' => 'Digital printing on fabric',
            'description' => 'Custom digital textile printing for brands, boutiques and designers on silk, linen, linen-cotton and mul cotton, printed on an industrial Konica Minolta Nassenger 8 with in-house artwork development and strike-offs.',
            'url'         => $page_canonical,
            'image'       => [
                SITE_URL . '/assets/images/services/digital-printed-peony-fabric.webp',
                SITE_URL . '/assets/images/services/digital-textile-printer.webp',
                SITE_URL . '/assets/images/services/textile-designer-floral-repeat.webp',
            ],
            'provider'    => [
                '@type' => 'Organization',
                '@id'   => SITE_URL . '/#organization',
                'name'  => 'Fabloom Group of Company',
                'url'   => SITE_URL,
            ],
            'areaServed'  => ['@type' => 'Country', 'name' => 'India'],
            'availableChannel' => [
                '@type'        => 'ServiceChannel',
                'serviceUrl'   => SITE_URL . '/enquiry',
                'servicePhone' => '+919760058796',
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Digital Printing', 'item' => $page_canonical],
            ],
        ],
        faq_schema_node($print_faqs, $page_canonical) + ['about' => ['@id' => $page_canonical . '#service']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

require_once __DIR__ . '/includes/header.php';
?>
<!-- PAGE HERO -->
    <section class="page-hero" aria-label="Page header" style="position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;">
      <div class="container" style="position:relative;z-index:1;padding-top:5rem;padding-bottom:4rem;">
        <div class="reveal">
          <span class="script-text" style="font-size:1.5rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">Our Services</span>
          <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#FFFFFF;line-height:1.15;margin-bottom:1rem;">Custom Digital Printing</h1>
          <p style="color:rgba(255,255,255,0.75);font-size:1.1rem;max-width:640px;line-height:1.7;margin-bottom:1.25rem;">Your artwork printed straight onto silk, linen and cotton — every colour, every gradient, from <?= $min_digital ?> metres per design.</p>
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol style="list-style:none;display:flex;align-items:center;gap:0.5rem;padding:0;margin:0;flex-wrap:wrap;">
              <li><a href="<?= SITE_URL ?>/" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.875rem;">Home</a></li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:rgba(255,255,255,0.6);font-size:0.875rem;">Services</li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:#C0282A;font-size:0.875rem;font-weight:600;" aria-current="page">Digital Printing</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- INTRO -->
    <section class="section bg-white" aria-labelledby="intro-heading">
      <div class="container">
        <div class="about-split" style="gap:4rem;align-items:center;">
          <div class="about-split__content reveal-left">
            <span class="section-label">Print Straight from the File</span>
            <h2 class="section-title" id="intro-heading">Any Design, Any Colour, on Fabric</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Digital printing puts your artwork onto cloth exactly as it was drawn. Painterly florals, photographic detail, soft watercolour washes and fine gradients that no block or screen can hold are printed in a single pass, in as many colours as the design needs.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Because there are no screens or blocks to make, a new design costs nothing extra to set up. That lets brands sample a print, test a colourway or run a short capsule collection without committing to a large quantity.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:2rem;">
              It sits alongside the craft Fabloom has practised for more than four decades: the same eye for fabric and colour, applied with modern textile printing technology.
            </p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;">
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">Nassenger 8</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Konica Minolta industrial printer</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">Unlimited Colours</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Gradients and photo detail</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">From <?= $min_digital ?> Metres</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Per design</div>
              </div>
            </div>
          </div>
          <div class="reveal-right">
            <img src="assets/images/services/digital-printed-peony-fabric.webp"
                 alt="Lengths of digitally printed fabric with large painterly pink peonies hanging on Fabloom's lit inspection frame"
                 width="738" height="1600" fetchpriority="high"
                 style="width:100%;max-height:560px;border-radius:16px;object-fit:cover;object-position:center 35%;">
          </div>
        </div>
      </div>
    </section>

    <!-- DESIGN STUDIO -->
    <section class="section bg-cream" aria-labelledby="studio-heading">
      <div class="container">
        <div class="about-split" style="gap:4rem;align-items:center;">
          <div class="reveal-left">
            <img src="assets/images/services/textile-designer-floral-repeat.webp"
                 alt="Fabloom textile designer working on a rose floral repeat with a pen tablet in the design studio"
                 loading="lazy" width="1160" height="738"
                 style="width:100%;aspect-ratio:4/3;border-radius:16px;object-fit:cover;object-position:40% center;">
          </div>
          <div class="about-split__content reveal-right">
            <span class="section-label">In-House Design Studio</span>
            <h2 class="section-title" id="studio-heading">Artwork Made Ready to Print</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Not every design arrives print-ready, and it does not need to. Our textile designers take a sketch, a mood board, a photograph or a swatch and develop it into a print file — building the repeat so it joins seamlessly across the width, cleaning up the artwork and preparing colourways for you to choose from.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              If your file is already finished, we check its resolution and repeat before printing and tell you if anything will not print well. Print-ready artwork should be supplied at 300 DPI with the repeat size specified.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;">
              Custom artwork developed for your brand stays yours, and is not added to our own range.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- THE PRINTER -->
    <section class="section bg-white" aria-labelledby="machine-heading">
      <div class="container">
        <div class="about-split" style="gap:4rem;align-items:center;">
          <div class="about-split__content reveal-left">
            <span class="section-label">The Technology</span>
            <h2 class="section-title" id="machine-heading">Printed on a Konica Minolta Nassenger 8</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Production runs on an industrial Konica Minolta Nassenger 8, a textile inkjet printer built for continuous fabric rather than paper. The cloth travels on a belt beneath the print heads, so long lengths are printed evenly from the first metre to the last.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              A dedicated operator prepares every job in colour-managed RIP software, matching it to the strike-off you approved and checking the colour against shade cards as the fabric comes off the machine.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;">
              Printed lengths are then fixed, finished and inspected under light, panel by panel, before they are packed.
            </p>
          </div>
          <div class="reveal-right">
            <img src="assets/images/services/digital-print-operator-rip.webp"
                 alt="Operator preparing a print job in RIP software beside Fabloom's Konica Minolta Nassenger 8 digital textile printer"
                 loading="lazy" width="568" height="1600"
                 style="width:100%;max-height:560px;border-radius:16px;object-fit:cover;object-position:center 55%;">
          </div>
        </div>

        <img class="reveal" src="assets/images/services/digital-textile-printer.webp"
             alt="Printed fabric leaving the belt of Fabloom's Konica Minolta Nassenger 8 digital textile printer"
             loading="lazy" width="1600" height="575"
             style="display:block;width:100%;margin-top:3rem;border-radius:16px;aspect-ratio:1600/575;object-fit:cover;">
      </div>
    </section>

    <!-- WHAT WE PRINT -->
    <section class="section bg-cream" aria-labelledby="types-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">What We Print</span>
          <h2 class="section-title" id="types-heading">Digital Prints for Fashion and Home</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">On pure linen, silk, linen-cotton blends and mul cotton.</p>
        </div>

        <div class="grid-3 mt-12 stagger-children">
          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="2" y="9" width="20" height="6" rx="1"/><line x1="6" y1="12" x2="6.01" y2="12"/><line x1="10" y1="12" x2="10.01" y2="12"/><line x1="14" y1="12" x2="14.01" y2="12"/><line x1="18" y1="12" x2="18.01" y2="12"/></svg>
            </div>
            <h3 class="service-card__title">Fabric by the Metre</h3>
            <p class="service-card__desc">All-over prints on running fabric for your own cutting and stitching, with the repeat joined seamlessly across the width.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.75 1.5-1.65 0-.42-.17-.8-.44-1.1-.27-.3-.44-.67-.44-1.1 0-.92.75-1.65 1.65-1.65H16c3.05 0 5.5-2.45 5.5-5.5C21.5 6.04 17.27 2 12 2z"/></svg>
            </div>
            <h3 class="service-card__title">Painterly &amp; Photo Prints</h3>
            <p class="service-card__desc">Watercolour florals, brushwork, photographs and soft gradients reproduced with the detail of the original artwork.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="M4 16h16"/><path d="M14 4v16"/></svg>
            </div>
            <h3 class="service-card__title">Placement &amp; Panel Prints</h3>
            <p class="service-card__desc">Engineered layouts printed to size — saree borders and pallus, dupatta panels, yokes and tablecloth or scarf designs.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1z"/><path d="M9 21v-7h6v7"/></svg>
            </div>
            <h3 class="service-card__title">Home Textiles</h3>
            <p class="service-card__desc">Cushion covers, table linen, curtains and upholstery panels printed on linen and cotton bases that suit the room.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M9 3h6v4l4 13H5L9 7z"/><line x1="7" y1="14" x2="17" y2="14"/></svg>
            </div>
            <h3 class="service-card__title">Sampling &amp; Short Runs</h3>
            <p class="service-card__desc">Strike-offs and runs from <?= $min_digital ?> metres per design, so you can test a print or colourway before committing to volume.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <h3 class="service-card__title">Brand Collections</h3>
            <p class="service-card__desc">Exclusive prints developed for a label's season or collection, kept for that client and not sold in our own range.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PROCESS -->
    <section class="section bg-white" aria-labelledby="process-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">How It Works</span>
          <h2 class="section-title" id="process-heading">From Your Artwork to Printed Fabric</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Five steps, from the first file to the finished length.</p>
        </div>

        <ol class="process-steps mt-12 stagger-children" style="list-style:none;padding:0;">
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">01</div>
            <h3>Share Your Brief</h3>
            <p>Send the artwork or reference, the base fabric, the end use and the quantity you need.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">02</div>
            <h3>Artwork &amp; Repeat</h3>
            <p>Our designers prepare the print file, set the repeat and offer colourways if you want them.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">03</div>
            <h3>Strike-Off</h3>
            <p>A sample is printed on your chosen fabric so you can approve colour and scale before production.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">04</div>
            <h3>Digital Printing</h3>
            <p>The approved file is printed on the Nassenger 8, colour-checked against the strike-off.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">05</div>
            <h3>Finish &amp; Dispatch</h3>
            <p>The print is fixed and finished, inspected under light and packed for delivery to you.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- GALLERY -->
    <section class="section bg-cream" aria-labelledby="gallery-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Inside the Unit</span>
          <h2 class="section-title" id="gallery-heading">From Screen to Cloth</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Real photographs from our design studio and print floor.</p>
        </div>

        <div class="mt-12" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.25rem;">
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/print-artwork-lotus-photoshop.webp" alt="Blue water lily print artwork being built in layers on a designer's monitor" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;object-position:center 45%;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Building the artwork</figcaption>
          </figure>
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/textile-designer-mustard-floral.webp" alt="Designer recolouring a floral print on a mustard ground, with fabric swatches on the desk" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;object-position:center 60%;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Developing colourways</figcaption>
          </figure>
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/printed-fabric-checking-table.webp" alt="Team checking a length of brightly printed fabric as it comes off the roller" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;object-position:center 45%;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Checking the print</figcaption>
          </figure>
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/digital-print-fabric-inspection.webp" alt="Workers rolling digitally printed peony fabric off the inspection frame" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;object-position:center 45%;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Inspection under light</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- DIGITAL OR BLOCK -->
    <section class="section bg-white" aria-labelledby="compare-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Choosing a Print</span>
          <h2 class="section-title" id="compare-heading">Digital Print or Block Print?</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4" style="max-width:720px;margin-left:auto;margin-right:auto;">
            Choose digital printing when the design depends on fine detail, many colours or photographic shading, or when you want to sample quickly. Choose hand block printing when you want the texture and small variations of a print stamped by hand. We do both, and will tell you honestly which suits your design.
          </p>
        </div>

        <p class="reveal" style="text-align:center;margin-top:2.5rem;color:var(--clr-text-secondary);line-height:1.8;">
          See our <a href="<?= SITE_URL ?>/block-printing" style="color:var(--clr-red);font-weight:600;">custom hand block printing</a> service, or browse ready <a href="<?= SITE_URL ?>/products?cat=digital-print" style="color:var(--clr-red);font-weight:600;">digital print fabric</a>.
        </p>
      </div>
    </section>

<?php faq_render($print_faqs, 'Digital Printing — Questions & Answers', 'What brands and clients ask before ordering a custom digital print.'); ?>

    <!-- CTA -->
    <section class="section bg-white" aria-labelledby="cta-heading">
      <div class="container">
        <div class="cta-banner reveal">
          <h2 id="cta-heading">Print Your Own Design</h2>
          <p>Send your artwork and requirements, and our team will reply with a strike-off plan and a quotation for your print.</p>
          <div class="flex-gap-4" style="display:flex;gap:1rem;">
            <a href="<?= SITE_URL ?>/enquiry" class="btn btn-primary">Send a Print Enquiry</a>
            <a href="https://wa.me/919760058796?text=Hello%20Fabloom%2C%20I%20would%20like%20a%20custom%20digital%20print%20made%20for%20my%20brand." target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="background:#12803F;border-color:#12803F;">Share on WhatsApp</a>
            <a href="<?= SITE_URL ?>/products?cat=digital-print" class="btn btn-outline-white">Browse Digital Print Fabric</a>
          </div>
        </div>
      </div>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
