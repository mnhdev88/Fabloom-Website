<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/faq.php';

$page_title = 'Custom Wooden Block Design & Carving for Block Printing | Fabloom';
$page_desc  = 'Custom wooden print blocks designed and hand carved in house at Fabloom, Bhagalpur, by 20 master block carvers. Send your motif — we make it ready for print.';
$page_canonical = SITE_URL . '/custom-block-design';
$page_og_image  = SITE_URL . '/assets/images/services/og-custom-block-design.jpg';

// ── FAQ ──────────────────────────────────────────────────────
// Written to the same shape as includes/faq.php: the first sentence answers
// the question outright so it can be quoted on its own. No lead times or
// minimums here — those are confirmed per quotation, and the general FAQ
// already carries the site-wide order rules.
$block_faqs = [
    [
        'q' => 'Can Fabloom make a custom wooden print block from my design?',
        'a' => 'Yes. Fabloom designs and hand carves wooden print blocks in house to each client\'s own motif, so the block is made for your design rather than picked from an existing stock of blocks. Send a sketch, photograph, digital file or a piece of reference fabric through the enquiry form or on WhatsApp.',
    ],
    [
        'q' => 'Who carves the blocks at Fabloom?',
        'a' => 'Fabloom\'s blocks are carved by a team of 20 master block carvers who have practised the craft since childhood. They work in house at Fabloom\'s Bhagalpur unit, alongside the designers who prepare the artwork, so a design moves from paper to finished block without leaving the workshop.',
    ],
    [
        'q' => 'What should I send to get a custom block made?',
        'a' => 'Send the motif in any form you have — a hand sketch, a photograph, a JPG, PDF or vector file, or a fabric swatch to match — along with the approximate size of the motif and the number of colours you want to print. Our designers redraw it to scale for carving and confirm the details with you before any wood is cut.',
    ],
    [
        'q' => 'Can a design with more than one colour be block printed?',
        'a' => 'Yes. A multi-colour design is carved as a set of blocks, one for each colour: an outline block for the fine lines and separate fill blocks for the colour areas. The set is carved to register together so each colour lands in place when the fabric is printed by hand.',
    ],
    [
        'q' => 'How much does a custom block cost and how long does it take?',
        'a' => 'The cost and time depend on the size of the motif, how fine the detail is and how many colour blocks the design needs, so both are confirmed on your quotation. Share your design through the enquiry form and our team will reply with the block set required and a price.',
    ],
    [
        'q' => 'Can Fabloom also print my fabric with the custom block?',
        'a' => 'Yes. Fabloom block prints in house on its own silk, linen, cotton and blended fabrics, so the block you commission can go straight to the print tables. You can see the existing range on the block print fabric page.',
    ],
];

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'Service',
            '@id'         => $page_canonical . '#service',
            'name'        => 'Custom Wooden Block Design & Carving',
            'serviceType' => 'Hand block print design development',
            'description' => 'In-house design and hand carving of custom wooden print blocks for block printing on fabric, made to the client\'s own motif by 20 master block carvers in Bhagalpur, Bihar.',
            'url'         => $page_canonical,
            'image'       => [
                SITE_URL . '/assets/images/services/wooden-block-carving-artisans.webp',
                SITE_URL . '/assets/images/services/master-block-carver-bhagalpur.webp',
                SITE_URL . '/assets/images/services/block-design-sketching.webp',
            ],
            'provider'    => [
                '@type' => 'Organization',
                '@id'   => SITE_URL . '/#organization',
                'name'  => 'Fabloom Group of Company',
                'url'   => SITE_URL,
            ],
            'areaServed'  => ['@type' => 'Country', 'name' => 'India'],
            'availableChannel' => [
                '@type'      => 'ServiceChannel',
                'serviceUrl' => SITE_URL . '/enquiry',
                'servicePhone' => '+919760058796',
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Custom Block Design', 'item' => $page_canonical],
            ],
        ],
        faq_schema_node($block_faqs, $page_canonical) + ['about' => ['@id' => $page_canonical . '#service']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

require_once __DIR__ . '/includes/header.php';
?>
<!-- PAGE HERO -->
    <section class="page-hero" aria-label="Page header" style="position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;">
      <div class="container" style="position:relative;z-index:1;padding-top:5rem;padding-bottom:4rem;">
        <div class="reveal">
          <span class="script-text" style="font-size:1.5rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">Our Services</span>
          <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#FFFFFF;line-height:1.15;margin-bottom:1rem;">Custom Wooden Block Design &amp; Carving</h1>
          <p style="color:rgba(255,255,255,0.75);font-size:1.1rem;max-width:640px;line-height:1.7;margin-bottom:1.25rem;">Hand-carved print blocks, designed in house to your motif by our 20 master block carvers — ready for print.</p>
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol style="list-style:none;display:flex;align-items:center;gap:0.5rem;padding:0;margin:0;flex-wrap:wrap;">
              <li><a href="<?= SITE_URL ?>/" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.875rem;">Home</a></li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:rgba(255,255,255,0.6);font-size:0.875rem;">Services</li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:#C0282A;font-size:0.875rem;font-weight:600;" aria-current="page">Custom Block Design</li>
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
            <span class="section-label">Block Design Development</span>
            <h2 class="section-title" id="intro-heading">Your Design, Carved in Wood</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Every hand block print begins with a wooden block. At Fabloom we design and carve those blocks in house — we do not buy them in or print from a generic stock. The block that prints your fabric is drawn and cut in our own workshop in Bhagalpur, Bihar.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              That lets us customise every design to the client's need. Bring us a sketch, a photograph, a digital file or a piece of old fabric you love, and we develop it into a block — or a full set of blocks for a multi-colour print — sized, repeated and carved for your fabric.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:2rem;">
              It suits fashion labels, boutiques, designers and exporters who want a print that is theirs alone, as well as anyone recreating a heritage motif that no longer exists as a block.
            </p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;">
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">100% In House</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Design, carving and printing</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">20 Master Carvers</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Trained in the craft since childhood</div>
              </div>
              <div style="padding:1rem 1.25rem;background:var(--clr-cream);border-radius:10px;border-left:3px solid var(--clr-red);">
                <div style="font-weight:700;color:var(--clr-charcoal);font-size:1.1rem;">Custom Designs</div>
                <div style="font-size:0.85rem;color:var(--clr-text-secondary);">Made to your motif and size</div>
              </div>
            </div>
          </div>
          <div class="reveal-right">
            <img src="assets/images/services/wooden-block-carving-artisans.webp"
                 alt="Two Fabloom artisans hand carving custom wooden print blocks with chisel and mallet in Bhagalpur"
                 width="738" height="1600" fetchpriority="high"
                 style="width:100%;max-height:560px;border-radius:16px;object-fit:cover;object-position:center 35%;">
          </div>
        </div>
      </div>
    </section>

    <!-- MASTER CARVERS -->
    <section class="section bg-cream" aria-labelledby="artisans-heading">
      <div class="container">
        <div class="about-split" style="gap:4rem;align-items:center;">
          <div class="reveal-left">
            <img src="assets/images/services/master-block-carver-bhagalpur.webp"
                 alt="Master block carver at Fabloom cutting fine detail into a wooden printing block"
                 loading="lazy" width="738" height="1600"
                 style="width:100%;max-height:560px;border-radius:16px;object-fit:cover;object-position:center 45%;">
          </div>
          <div class="about-split__content reveal-right">
            <span class="section-label">The People Behind the Block</span>
            <h2 class="section-title" id="artisans-heading">20 Master Block Carvers</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              Our block-making team is 20 master craftsmen who have worked with wood and chisel since childhood. Many learned the craft at home, from fathers and elders, long before they joined us — and have spent a lifetime cutting motifs by hand.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1rem;">
              That experience is what turns your vision into a block that is ready for print. A master carver knows how deep to cut so the dye sits evenly, how fine a line the wood will hold, and how to match a set of colour blocks so every layer registers cleanly on the cloth.
            </p>
            <p style="color:var(--clr-text-secondary);line-height:1.85;">
              They work alongside our designers and block printers in the same unit, so questions about your design are answered at the carving bench — not passed between suppliers.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- PROCESS -->
    <section class="section bg-white" aria-labelledby="process-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">How It Works</span>
          <h2 class="section-title" id="process-heading">From Your Idea to a Print-Ready Block</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Five steps, all carried out under one roof at our Bhagalpur unit.</p>
        </div>

        <ol class="process-steps mt-12 stagger-children" style="list-style:none;padding:0;">
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">01</div>
            <h3>Share Your Design</h3>
            <p>Send a sketch, photo, digital file or fabric sample, with the motif size and number of colours you have in mind.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">02</div>
            <h3>Design &amp; Repeat</h3>
            <p>Our designers redraw the motif to scale, plan the repeat, and separate it into one layer per colour.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">03</div>
            <h3>Transfer to Wood</h3>
            <p>The final drawing is pasted onto a smoothed block of seasoned hardwood to guide the carver's chisel.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">04</div>
            <h3>Hand Carving</h3>
            <p>A master carver cuts the design by hand with chisel and mallet, one block for each colour in the print.</p>
          </li>
          <li class="process-step reveal">
            <div class="process-step__num" aria-hidden="true">05</div>
            <h3>Ready for Print</h3>
            <p>The finished block is checked for depth and registration, then goes to our print tables for your fabric.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- WHAT WE CARVE -->
    <section class="section bg-cream" aria-labelledby="types-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">What We Make</span>
          <h2 class="section-title" id="types-heading">Blocks for Every Kind of Print</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">From a single small buti to a complete multi-colour set.</p>
        </div>

        <div class="grid-3 mt-12 stagger-children">
          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>
            </div>
            <h3 class="service-card__title">Outline Blocks</h3>
            <p class="service-card__desc">Finely cut blocks that print the outline of a design — the first and most detailed block in any multi-colour set.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><rect x="7" y="7" width="10" height="10" rx="1" fill="rgba(192,40,42,0.15)"/></svg>
            </div>
            <h3 class="service-card__title">Fill &amp; Colour Blocks</h3>
            <p class="service-card__desc">Blocks that lay each colour inside the outline, carved to register exactly with the outline block.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><circle cx="6" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><circle cx="12" cy="12" r="2"/></svg>
            </div>
            <h3 class="service-card__title">Buti &amp; All-Over Blocks</h3>
            <p class="service-card__desc">Small motif blocks and repeating all-over patterns, planned so the repeat joins seamlessly across the width of the fabric.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="2" y="9" width="20" height="6" rx="1"/><line x1="6" y1="12" x2="6.01" y2="12"/><line x1="10" y1="12" x2="10.01" y2="12"/><line x1="14" y1="12" x2="14.01" y2="12"/><line x1="18" y1="12" x2="18.01" y2="12"/></svg>
            </div>
            <h3 class="service-card__title">Border &amp; Pallu Blocks</h3>
            <p class="service-card__desc">Border, corner and pallu blocks for dupattas, stoles and furnishing, carved to your exact width.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            </div>
            <h3 class="service-card__title">Multi-Block Sets</h3>
            <p class="service-card__desc">Complete sets of three, four or more blocks for layered designs like our multiblock-printed Mul Chanderi.</p>
          </div>

          <div class="service-card reveal">
            <div class="service-card__icon">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M3 12a9 9 0 1018 0 9 9 0 00-18 0z"/><path d="M12 7v5l3 3"/></svg>
            </div>
            <h3 class="service-card__title">Heritage Recreations</h3>
            <p class="service-card__desc">Traditional and vintage motifs redrawn from old fabric or photographs and carved as new blocks.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- GALLERY -->
    <section class="section bg-white" aria-labelledby="gallery-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Inside the Workshop</span>
          <h2 class="section-title" id="gallery-heading">From Sketch to Carved Block</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Real photographs from our block-making workshop.</p>
        </div>

        <div class="mt-12" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.25rem;">
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/block-design-sketching.webp" alt="Fabloom designer sketching a custom motif for a new wooden print block" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Sketching the motif</figcaption>
          </figure>
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/block-design-repeat-layout.webp" alt="Designer laying out a repeat pattern on paper while a craftsman carves a block behind him" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;object-position:center 60%;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Planning the repeat</figcaption>
          </figure>
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/hand-carving-print-blocks.webp" alt="Artisans hand carving border and motif print blocks from seasoned wood" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;object-position:center 55%;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Carving by hand</figcaption>
          </figure>
          <figure class="reveal" style="margin:0;">
            <img src="assets/images/services/master-block-carver-bhagalpur.webp" alt="Experienced block carver finishing the detail on a wooden printing block" loading="lazy" width="738" height="1600" style="width:100%;aspect-ratio:3/4;object-fit:cover;object-position:center 50%;border-radius:16px;">
            <figcaption style="margin-top:0.75rem;font-size:0.9rem;color:var(--clr-text-secondary);text-align:center;">Finishing the detail</figcaption>
          </figure>
        </div>

        <p class="reveal" style="text-align:center;margin-top:2.5rem;color:var(--clr-text-secondary);line-height:1.8;">
          Read more about the craft in our journal:
          <a href="<?= SITE_URL ?>/blog/the-timeless-art-of-block-printing-and-the-magic-of-the-steam-process" style="color:var(--clr-red);font-weight:600;">The Timeless Art of Block Printing</a>
          and
          <a href="<?= SITE_URL ?>/blog/mul-chanderi-the-art-of-multiblock-printing" style="color:var(--clr-red);font-weight:600;">Mul Chanderi &amp; the Art of Multiblock Printing</a>.
        </p>
      </div>
    </section>

<?php faq_render($block_faqs, 'Custom Block Design — Questions & Answers', 'What buyers ask before commissioning a wooden print block.'); ?>

    <!-- CTA -->
    <section class="section bg-white" aria-labelledby="cta-heading">
      <div class="container">
        <div class="cta-banner reveal">
          <h2 id="cta-heading">Get Your Custom Block Made</h2>
          <p>Share your motif and our team will reply with the block set it needs and a quotation — then print it on silk, linen or cotton in house.</p>
          <div class="flex-gap-4" style="display:flex;gap:1rem;">
            <a href="<?= SITE_URL ?>/enquiry" class="btn btn-primary">Send a Design Enquiry</a>
            <a href="https://wa.me/919760058796?text=Hello%20Fabloom%2C%20I%20would%20like%20a%20custom%20wooden%20block%20made%20for%20my%20design." target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="background:#12803F;border-color:#12803F;">Share on WhatsApp</a>
            <a href="<?= SITE_URL ?>/products?cat=block-print" class="btn btn-outline-white">Browse Block Print Fabric</a>
          </div>
        </div>
      </div>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
