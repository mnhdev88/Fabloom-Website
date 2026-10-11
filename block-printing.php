<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/faq.php';

$page_title = 'Custom Hand Block Printing for Brands & Designers | Fabloom';
$page_desc  = 'Custom hand block printing at Fabloom\'s Noida unit — your own print on suits, sarees, dupattas and fabric by the metre, backed by four decades of textile craft from Bhagalpur.';
$page_canonical = SITE_URL . '/block-printing';
$page_og_image  = SITE_URL . '/assets/images/services/og-block-printing.jpg';

// ── FAQ ──────────────────────────────────────────────────────
// Same shape as includes/faq.php: the first sentence answers the question
// outright. No lead times, minimums or per-metre rates here — those are
// confirmed per quotation, as on the custom block design page.
$print_faqs = [
    [
        'q' => 'Does Fabloom do custom block printing for brands?',
        'a' => 'Yes. Fabloom hand block prints custom designs for fashion brands, boutiques, designers and individual clients who want a print of their own. Share your motif or reference and our team plans the blocks, colours and fabric with you before printing begins.',
    ],
    [
        'q' => 'Where is Fabloom\'s block printing done?',
        'a' => 'Fabloom\'s block printing is done at its own block print unit in Noida, Uttar Pradesh, which has been running for around ten years. The unit is part of Fabloom, a textile house that began in Bhagalpur, Bihar more than four decades ago.',
    ],
    [
        'q' => 'What can Fabloom block print on?',
        'a' => 'Fabloom block prints suits, sarees, dupattas, stoles and fabric by the metre, on cotton, mul, linen, chanderi, silk and blended fabrics. Tell us the garment or end use and we will suggest the base fabric and dyes that suit it.',
    ],
    [
        'q' => 'Can I get a block print made from my own design?',
        'a' => 'Yes. Send a sketch, photograph, digital file or a fabric swatch and Fabloom develops it into hand-carved wooden blocks — one for each colour — and then prints it for you. The block-making step is covered in detail on the custom block design page.',
    ],
    [
        'q' => 'Can I print an existing Fabloom block on my own fabric?',
        'a' => 'Yes, in most cases. If you prefer a design from our existing blocks, or want to supply your own fabric, share the details through the enquiry form and our team will confirm whether the fabric takes the print well and what colourways are possible.',
    ],
    [
        'q' => 'How much does custom block printing cost?',
        'a' => 'The price depends on the fabric, the number of colours and blocks in the design, how dense the print is and the quantity you need, so it is confirmed on your quotation. Send your design and requirements through the enquiry form or on WhatsApp for a quote.',
    ],
];

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'Service',
            '@id'         => $page_canonical . '#service',
            'name'        => 'Custom Hand Block Printing',
            'serviceType' => 'Hand block printing on fabric',
            'description' => 'Custom hand block printing for brands, boutiques and designers on suits, sarees, dupattas and fabric by the metre, carried out at Fabloom\'s block print unit in Noida, Uttar Pradesh.',
            'url'         => $page_canonical,
            'image'       => [
                SITE_URL . '/assets/images/services/hand-block-printing-artisans-noida.webp',
                SITE_URL . '/assets/images/services/block-printing-floral-buti-table.webp',
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
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Block Printing', 'item' => $page_canonical],
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
          <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#FFFFFF;line-height:1.15;margin-bottom:1rem;">Custom Hand Block Printing</h1>
          <p style="color:rgba(255,255,255,0.75);font-size:1.1rem;max-width:640px;line-height:1.7;margin-bottom:1.25rem;">Your own print, stamped by hand on suits, sarees and fabric at our Noida block print unit.</p>
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol style="list-style:none;display:flex;align-items:center;gap:0.5rem;padding:0;margin:0;flex-wrap:wrap;">
              <li><a href="<?= SITE_URL ?>/" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.875rem;">Home</a></li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:rgba(255,255,255,0.6);font-size:0.875rem;">Services</li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:#C0282A;font-size:0.875rem;font-weight:600;" aria-current="page">Block Printing</li>
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
            <span class="section-label">From Bhagalpur to Noida</span>
            <h2 class="section-title" id="intro-heading">Four Decades of Craft, Printed by Hand</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Fabloom's journey began more than 40 years ago in Bhagalpur, Bihar — India's Silk City. Around ten years ago we took that expertise further and opened a dedicated block print unit in Noida, so that brands and designers across North India could have their prints made by hand, close to where they work.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Today the Noida unit specialises in custom block printing: prints made for brands and clients who want a design of their own rather than one off the shelf. Every metre is stamped by hand, block by block, on long printing tables by printers who do nothing else.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:2rem;">
              Our methods have grown, but one belief has not changed: give the best quality block work we can, on every order, whatever its size.
            </p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;">
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">40+ Years</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Textile heritage from Bhagalpur</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">10 Years in Noida</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Dedicated block print unit</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">Custom Prints</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">For brands and clients</div>
              </div>
            </div>
          </div>
          <div class="reveal-right">
            <img src="assets/images/services/block-printing-floral-buti-table.webp"
                 alt="Fabloom block printer stamping a floral buti in indigo onto white fabric on a long printing table in Noida"
                 width="738" height="1600" fetchpriority="high"
                 style="width:100%;max-height:560px;border-radius:16px;object-fit:cover;object-position:center 55%;">
          </div>
        </div>
      </div>
    </section>

    <!-- CUSTOM PRINTS FOR BRANDS -->
    <section class="section bg-cream" aria-labelledby="brands-heading">
      <div class="container">
        <div class="about-split" style="gap:4rem;align-items:center;">
          <div class="reveal-left">
            <img src="assets/images/services/hand-block-printing-artisans-noida.webp"
                 alt="Three artisans hand block printing a red paisley motif across a length of fabric at Fabloom's Noida unit"
                 loading="lazy" width="864" height="1152"
                 style="width:100%;max-height:560px;border-radius:16px;object-fit:cover;object-position:center 40%;">
          </div>
          <div class="about-split__content reveal-right">
            <span class="section-label">For Brands &amp; Designers</span>
            <h2 class="section-title" id="brands-heading">A Print That Is Yours Alone</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Fashion labels, boutiques and designers come to us to make their own print. You bring the idea — a sketch, a mood board, an old textile — and we turn it into blocks, choose the colours with you and print it on the fabric your collection needs.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Because the wooden blocks are <a href="<?= SITE_URL ?>/custom-block-design" style="color:var(--clr-red);font-weight:600;">designed and carved in house</a>, your design is not limited to what already exists. A multi-colour print is built up layer by layer — an outline first, then each fill colour — with every block registered by eye and hand.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;">
              That hand placement is what gives block print its character: small, honest variations from one motif to the next that no machine reproduces.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- WHAT WE PRINT -->
    <section class="section bg-white" aria-labelledby="types-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">What We Print</span>
          <h2 class="section-title" id="types-heading">Block Print on Every Kind of Fabric</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Garments, drapes and running fabric, on the base cloth that suits your design.</p>
        </div>

        <div class="grid-3 mt-12 stagger-children">
          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/></svg>
            </div>
            <h3 class="service-card__title">Suits &amp; Suit Sets</h3>
            <p class="service-card__desc">Kurta, bottom and dupatta printed as a coordinated set, with placement borders and yokes where the design calls for them.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="M4 16h16"/><path d="M14 4v16"/></svg>
            </div>
            <h3 class="service-card__title">Sarees</h3>
            <p class="service-card__desc">Body, border and pallu printed in one design, with the border blocks carved to the exact width of your saree.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M3 6c3-2 6 2 9 0s6-2 9 0"/><path d="M3 12c3-2 6 2 9 0s6-2 9 0"/><path d="M3 18c3-2 6 2 9 0s6-2 9 0"/></svg>
            </div>
            <h3 class="service-card__title">Dupattas &amp; Stoles</h3>
            <p class="service-card__desc">Lightweight drapes with all-over butis, borders and corner motifs, on mul, chanderi, silk and cotton.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="2" y="9" width="20" height="6" rx="1"/><line x1="6" y1="12" x2="6.01" y2="12"/><line x1="10" y1="12" x2="10.01" y2="12"/><line x1="14" y1="12" x2="14.01" y2="12"/><line x1="18" y1="12" x2="18.01" y2="12"/></svg>
            </div>
            <h3 class="service-card__title">Fabric by the Metre</h3>
            <p class="service-card__desc">Running fabric for your own cutting and stitching, with the repeat planned to join seamlessly across the width.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.75 1.5-1.65 0-.42-.17-.8-.44-1.1-.27-.3-.44-.67-.44-1.1 0-.92.75-1.65 1.65-1.65H16c3.05 0 5.5-2.45 5.5-5.5C21.5 6.04 17.27 2 12 2z"/></svg>
            </div>
            <h3 class="service-card__title">Multi-Colour Prints</h3>
            <p class="service-card__desc">Layered designs printed with an outline block and separate fill blocks, from two colours to complex multiblock work.</p>
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
    <section class="section bg-cream" aria-labelledby="process-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">How It Works</span>
          <h2 class="section-title" id="process-heading">From Your Idea to Printed Fabric</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Five steps, from the first conversation to the finished print.</p>
        </div>

        <ol class="process-steps mt-12 stagger-children" style="list-style:none;padding:0;">
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">01</div>
            <h3>Share Your Brief</h3>
            <p>Tell us the design, the garment or fabric, the colours you want and the quantity you need.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">02</div>
            <h3>Blocks &amp; Fabric</h3>
            <p>We carve new blocks for your motif or select from existing ones, and agree the base fabric with you.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">03</div>
            <h3>Colour Sampling</h3>
            <p>Dyes are mixed to your shades and a sample is printed so you can approve the look before production.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">04</div>
            <h3>Hand Printing</h3>
            <p>Our printers stamp the fabric by hand on the long tables, one block and one colour at a time.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">05</div>
            <h3>Finish &amp; Dispatch</h3>
            <p>The print is dried and fixed, checked for quality and packed for delivery to you.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- QUALITY -->
    <section class="section bg-white" aria-labelledby="quality-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Our Promise</span>
          <h2 class="section-title" id="quality-heading">We Still Believe in the Best Block Work</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4" style="max-width:720px;margin-left:auto;margin-right:auto;">
            Faster methods exist. We have chosen to keep printing by hand, because the quality of a true block print is what our clients come to us for — clean impressions, colours that sit evenly in the cloth, and repeats that line up across the whole length.
          </p>
        </div>

        <p class="reveal" style="text-align:center;margin-top:2.5rem;color:var(--clr-text-secondary);line-height:1.8;">
          Read more about the craft in our journal:
          <a href="<?= SITE_URL ?>/blog/the-timeless-art-of-block-printing-and-the-magic-of-the-steam-process" style="color:var(--clr-red);font-weight:600;">The Timeless Art of Block Printing</a>
          and
          <a href="<?= SITE_URL ?>/blog/mul-chanderi-the-art-of-multiblock-printing" style="color:var(--clr-red);font-weight:600;">Mul Chanderi &amp; the Art of Multiblock Printing</a>.
        </p>
      </div>
    </section>

<?php faq_render($print_faqs, 'Block Printing — Questions & Answers', 'What brands and clients ask before ordering a custom block print.'); ?>

    <!-- CTA -->
    <section class="section bg-white" aria-labelledby="cta-heading">
      <div class="container">
        <div class="cta-banner reveal">
          <h2 id="cta-heading">Make Your Own Block Print</h2>
          <p>Share your design and requirements, and our team will reply with the blocks, fabric and a quotation for your print.</p>
          <div class="flex-gap-4" style="display:flex;gap:1rem;">
            <a href="<?= SITE_URL ?>/enquiry" class="btn btn-primary">Send a Print Enquiry</a>
            <a href="https://wa.me/919760058796?text=Hello%20Fabloom%2C%20I%20would%20like%20a%20custom%20block%20print%20made%20for%20my%20brand." target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="background:#12803F;border-color:#12803F;">Share on WhatsApp</a>
            <a href="<?= SITE_URL ?>/products?cat=block-print" class="btn btn-outline-white">Browse Block Print Fabric</a>
          </div>
        </div>
      </div>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
