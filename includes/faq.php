<?php
/**
 * FAQ content, rendering and FAQPage schema.
 *
 * Answer engines (AI Overviews, ChatGPT search, Perplexity) lift a passage
 * when it is short, self-contained and sits directly under the question it
 * answers. Every answer below is written to that shape: the first sentence
 * answers the question outright and carries the specific number, so it can be
 * quoted on its own without the surrounding page.
 *
 * The visible markup and the JSON-LD are generated from the SAME array. They
 * cannot drift apart, which is what Google requires of FAQPage markup — the
 * answer in the schema has to be on the page.
 *
 * A note on rich results: Google restricted FAQ rich snippets to government
 * and health sites in 2023, so this markup is not expected to draw an
 * accordion in the SERP. It is here because LLM-based search still parses it,
 * and because the visible Q&A blocks earn the citation either way.
 */

require_once __DIR__ . '/functions.php';

/**
 * Questions every fabric buyer asks, regardless of which page they land on.
 * Keep the answers factual — these are read as commitments.
 */
function faq_general(): array
{
    $min = (int) MIN_ORDER_METRES;

    return [
        [
            'q' => 'What is the minimum order quantity for Fabloom fabric?',
            'a' => "The minimum order is {$min} metres per design and colour on woven fabric, because a shorter length cannot be cut from the loom lot. Printed fabric is a different case: block print and digital print are both applied to cloth that is already woven, so they are supplied from " . (int) (MIN_ORDER_BY_CATEGORY['block-print'] ?? $min) . " metres. Sarees are sold as individual pieces, so the metre minimum does not apply to them.",
        ],
        [
            'q' => 'Does Fabloom manufacture the fabric itself?',
            'a' => 'Yes. Fabloom weaves, dyes, prints and finishes in its own unit at Mohiuddin Pur, Bhagalpur, Bihar. There is no trading middleman between the loom and the order, which is why custom shades and prints can be run against a single buyer\'s specification.',
        ],
        [
            'q' => 'Can I order a swatch before placing a bulk order?',
            'a' => 'Yes. Request a physical swatch through the enquiry form before committing to a bulk order. This is strongly recommended when an exact shade matters, because screen colour and dye lots both vary and a small difference between the photograph and the delivered cloth is normal for hand-dyed fabric.',
        ],
        [
            'q' => 'How do I pay for an order?',
            'a' => 'Orders placed on this website are Cash on Delivery — you pay the courier in full when the parcel is handed to you. Bulk, wholesale and export orders are invoiced separately, with payment terms agreed in writing before production starts.',
        ],
        [
            'q' => 'Does Fabloom ship across India?',
            'a' => 'Yes. Fabloom dispatches to every pincode its courier partners serve, across all Indian states. Ready stock normally leaves the unit within 2 to 5 working days of order confirmation; custom dyeing, printing or weaving takes longer and the lead time is confirmed on the order.',
        ],
        [
            'q' => 'Is a slub or a slight shade variation a defect?',
            'a' => 'No. Slubs, minor weave irregularity and small variations in print registration are inherent to handloom and hand-printed cloth, and dye lots vary between batches. These are marks of hand production, not defects. Genuine faults — a running weaving fault, a hole, a dye patch across the piece or short metrage — are replaced or refunded when reported within 48 hours of delivery.',
        ],
        [
            'q' => 'Does Fabloom supply fabric for export?',
            'a' => 'Yes. Fabloom supplies exporters, buying houses and overseas labels, with bulk and export consignments quoted against a separate proforma invoice. Send the composition, width, GSM and quantity you need through the enquiry form for a quotation.',
        ],
        [
            'q' => 'Can Fabloom develop a custom print or shade?',
            'a' => 'Yes. Custom dyeing to a shade reference, custom block and digital prints from your artwork, and custom widths and GSM are all produced in house. Custom development runs against the same ' . $min . '-metre minimum per colourway, and the design becomes exclusive to the buyer where agreed in writing.',
        ],
    ];
}

/**
 * Category-specific questions, keyed by the category slug used in the
 * `categories` table. A slug with no entry simply falls back to the general
 * set — no page ever renders an empty FAQ block.
 */
function faq_for_category(string $slug): array
{
    $sets = [
        'silk' => [
            [
                'q' => 'What is momme, and what weight is Fabloom silk?',
                'a' => 'Momme (mm) is the traditional unit of silk weight — one momme is roughly 4.34 grams per square metre. Fabloom silks run from about 12 to 16 momme: 12 to 14 momme is fluid and suits shirting, scarves and linings, while 16 momme and above holds a shape well enough for structured garments and furnishing.',
            ],
            [
                'q' => 'What makes Bhagalpur silk different from other Indian silks?',
                'a' => 'Bhagalpur silk is woven from tussar and mulberry yarn that keeps its natural irregularity, giving the cloth a fine slub and a matte-to-lustrous depth that flat power-loom silk does not have. Bhagalpur has been known as India\'s Silk City for over a century, and Fabloom weaves on traditional handlooms within that cluster.',
            ],
            [
                'q' => 'How should pure silk fabric be washed?',
                'a' => 'Dry clean pure silk for the first wash, then hand wash in cold water with a mild pH-neutral detergent if you wash it at home. Do not wring, bleach or dry in direct sunlight, and press on the reverse at a low setting while slightly damp. Always test on a sample before washing a full piece.',
            ],
            [
                'q' => 'Is tussar silk the same as mulberry silk?',
                'a' => 'No. Tussar is a wild silk with a natural golden tone, a coarser and more textured hand, and a slub running through it; mulberry silk is cultivated, smoother, whiter in its raw state and takes dye more evenly. Fabloom supplies both, and tussar is the yarn most associated with Bhagalpur.',
            ],
        ],
        'linen' => [
            [
                'q' => 'What GSM linen should I choose?',
                'a' => 'Choose 140 to 150 GSM for shirting, dresses and light summer garments, and 200 GSM and above for trousers, jackets, upholstery and furnishing. GSM measures grams per square metre, so a higher number means a heavier, more opaque and more structured cloth. Fabloom runs linen from roughly 90 to 220 GSM.',
            ],
            [
                'q' => 'Does pure linen wrinkle, and is that a fault?',
                'a' => 'Pure linen creases readily, and that is a property of the flax fibre rather than a fault. The crease softens with each wash as the fibre relaxes. If a crisper finish is needed, ask about a linen blend or an enzyme-washed finish when you enquire.',
            ],
            [
                'q' => 'What is khadi linen?',
                'a' => 'Khadi linen is linen hand-spun and hand-woven on a traditional loom rather than a power loom, which gives it an irregular slub and a softer, more breathable hand. Fabloom weaves khadi linen in its Bhagalpur unit, typically undyed or in natural and ecru shades.',
            ],
            [
                'q' => 'How much does linen shrink?',
                'a' => 'Expect roughly 3 to 5 per cent shrinkage on the first wash for unwashed pure linen. Pre-wash the fabric before cutting, or add that allowance to the pattern. Shrinkage figures quoted on product pages are nominal and subject to normal mill tolerance.',
            ],
        ],
        'block-print' => [
            [
                'q' => 'What is the minimum order for block printed fabric?',
                'a' => 'Block printed fabric is supplied from ' . (int) (MIN_ORDER_BY_CATEGORY['block-print'] ?? MIN_ORDER_METRES) . ' metres per design and colour, well below the ' . (int) MIN_ORDER_METRES . '-metre minimum that applies to woven yardage. The print is struck by hand a repeat at a time onto cloth that is already woven, so a short length does not have to come off a full loom lot.',
            ],
            [
                'q' => 'How is hand block printing different from screen printing?',
                'a' => 'Hand block printing presses dye into the cloth with a hand-carved wooden block, one colour and one impression at a time, so each repeat differs very slightly and the print sits into the weave. Screen printing pushes ink through a mesh mechanically, producing a flatter and perfectly uniform repeat. Fabloom block prints are struck by hand in Bhagalpur.',
            ],
            [
                'q' => 'Are Fabloom block prints chemical-free?',
                'a' => 'Fabloom runs a chemical-free block print programme using natural and azo-free dyes, and the printed cloth is steam-fixed rather than resin-set. Specify the chemical-free line in your enquiry, as not every design in the catalogue is printed to that process.',
            ],
            [
                'q' => 'Will a block printed fabric bleed colour when washed?',
                'a' => 'Steam-fixed block prints are colour-fast in normal washing, though the first wash may release a small amount of surplus dye. Wash separately in cold water the first time, do not soak, and dry in shade. Slight print misregistration between repeats is inherent to hand printing and is not a defect.',
            ],
        ],
        'digital-print' => [
            [
                'q' => 'What is the minimum order for digitally printed fabric?',
                'a' => 'Digitally printed fabric is supplied from ' . (int) (MIN_ORDER_BY_CATEGORY['digital-print'] ?? MIN_ORDER_METRES) . ' metres per design, against the ' . (int) MIN_ORDER_METRES . '-metre minimum that applies to woven yardage. The design is printed straight from the file onto cloth that is already woven, so there is no loom lot to cut from and a short run costs no more per metre to set up.',
            ],
            [
                'q' => 'How accurate is the colour in a digital print?',
                'a' => 'Digital printing reproduces artwork far more precisely than block or screen printing, holding fine gradients, photographic detail and unlimited colours in a single pass. A strike-off on your chosen base cloth is still the only reliable way to confirm shade, because the same file prints differently on linen, silk and cotton.',
            ],
            [
                'q' => 'Can Fabloom digitally print my own artwork?',
                'a' => 'Yes. Supply print-ready artwork at 300 DPI with the repeat size specified, and Fabloom will run a strike-off before production. Custom digital prints run against the ' . (int) (MIN_ORDER_BY_CATEGORY['digital-print'] ?? MIN_ORDER_METRES) . '-metre digital print minimum per design, and the artwork remains yours.',
            ],
            [
                'q' => 'Which fabrics can be digitally printed?',
                'a' => 'Fabloom digitally prints on pure linen, silk, linen-cotton blends and mul cotton. Base cloth affects the result: linen gives a matte, textured print, while silk holds deeper saturation and higher contrast.',
            ],
        ],
        'hand-brush' => [
            [
                'q' => 'What is hand brush work on fabric?',
                'a' => 'Hand brush work is painting applied to woven cloth with a brush, stroke by stroke, rather than printed from a block or a screen. Because each piece is painted individually, no two lengths are identical and the design can flow across the cloth instead of repeating on a fixed pitch. Fabloom does this in house in Bhagalpur.',
            ],
            [
                'q' => 'Is hand brush work durable in washing?',
                'a' => 'Yes, when the pigment is properly fixed, as Fabloom fixes it before dispatch. Hand wash in cold water with a mild detergent, do not scrub the painted area, and dry in shade. Dry cleaning is safest for heavily worked pieces.',
            ],
            [
                'q' => 'Can hand brush work be done in a custom design?',
                'a' => 'Yes. Hand brush work is commissioned to your motif, colour palette and placement, which is why it is used for exclusive collections and one-off pieces. Lead times are longer than printed fabric because each length is painted by hand.',
            ],
        ],
        'saree' => [
            [
                'q' => 'Are Fabloom sarees sold by the piece or by the metre?',
                'a' => 'Sarees are sold as complete individual pieces, not by the metre, so the ' . (int) MIN_ORDER_METRES . '-metre fabric minimum does not apply. Each saree is woven and finished as a single unit with its pallu and border.',
            ],
            [
                'q' => 'What kind of sarees does Fabloom weave?',
                'a' => 'Fabloom weaves handloom tussar and mulberry silk sarees in Bhagalpur, including plain and zari-bordered pieces, block printed and hand brush worked sarees, and embroidered designs. Bulk and wholesale saree orders are quoted against enquiry.',
            ],
            [
                'q' => 'How should a handloom silk saree be stored?',
                'a' => 'Store a handloom silk saree wrapped in cotton muslin, not plastic, and refold it along a different line every few months so a permanent crease does not set into the zari. Keep it away from direct sunlight and damp.',
            ],
        ],
    ];

    return $sets[$slug] ?? [];
}

/**
 * Product-page questions built from the row itself, so the answer carries the
 * actual width, GSM and price rather than a generic sentence. Anything the
 * database leaves blank is simply skipped — an answer that says "our fabric is
 * available in various widths" is worth nothing to an answer engine.
 */
function faq_for_product(array $p): array
{
    $out     = [];
    $name    = (string) $p['name'];
    $isMetre = function_exists('product_is_metre') ? product_is_metre($p) : true;
    // The minimum is per category (both print lines cut far shorter), so read
    // it from the product rather than assuming the standard fabric lot.
    $min     = function_exists('product_min_qty') && $isMetre
        ? product_min_qty($p)
        : (int) MIN_ORDER_METRES;

    $width = trim((string) ($p['width_inches'] ?? ''));
    $gsm   = trim((string) ($p['weight_gsm'] ?? ''));
    $fab   = trim((string) ($p['fabric_type'] ?? ''));
    $blank = static fn(string $v): bool => $v === '' || $v === '–' || $v === '-';

    if (!$blank($width)) {
        $out[] = [
            'q' => "What width is {$name} available in?",
            'a' => "{$name} is woven {$width} wide. Width is nominal and subject to normal mill tolerance, so allow for a small variation when planning a cutting layout.",
        ];
    }

    if (!$blank($gsm)) {
        $weight_word = str_contains(strtolower($gsm), 'momme') ? 'weight' : 'GSM';
        $out[] = [
            'q' => "What is the {$weight_word} of {$name}?",
            'a' => "{$name} is {$gsm}." . (!$blank($fab) ? " It is woven in {$fab}." : '') . ' Please request a swatch if the exact hand and weight are critical to your product.',
        ];
    }

    if ($isMetre) {
        $out[] = [
            'q' => "What is the minimum order for {$name}?",
            'a' => "{$name} is supplied by the metre with a minimum of {$min} metres per colour. Bulk and wholesale pricing is quoted separately against an enquiry.",
        ];
    }

    $out[] = [
        'q' => "Is {$name} available in custom colours?",
        'a' => "Yes. {$name} can be custom dyed to a shade reference at Fabloom's own dye unit in Bhagalpur, against the {$min}-metre minimum per colourway. Send the shade reference through the enquiry form and ask for a swatch before bulk production.",
    ];

    $out[] = [
        'q' => "How long does delivery of {$name} take?",
        'a' => 'Ready stock is dispatched within 2 to 5 working days of order confirmation and delivered by courier across India, payable Cash on Delivery. Custom dyed or printed lengths take longer, and the lead time is confirmed when the order is placed.',
    ];

    return $out;
}

/**
 * Build the FAQPage node. Returned as an array so a caller can drop it into an
 * existing @graph instead of emitting a second, competing <script> block.
 */
function faq_schema_node(array $faqs, string $page_url): array
{
    return [
        '@type'      => 'FAQPage',
        '@id'        => $page_url . '#faq',
        'mainEntity' => array_map(static fn(array $f): array => [
            '@type'          => 'Question',
            'name'           => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
        ], $faqs),
    ];
}

/** Stand-alone JSON-LD string, for pages that have no @graph to extend. */
function faq_schema_json(array $faqs, string $page_url): string
{
    $node = ['@context' => 'https://schema.org'] + faq_schema_node($faqs, $page_url);

    return json_encode($node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * Visible Q&A block.
 *
 * <details>/<summary> is deliberate: the answer text is in the DOM whether or
 * not the panel is open, so a crawler reads all of it, keyboard and screen
 * reader support come free, and it needs no JavaScript. The first entry is
 * open on load so the section never reads as an empty stack of bars.
 */
function faq_render(array $faqs, string $heading = 'Frequently Asked Questions', string $intro = ''): void
{
    if (!$faqs) {
        return;
    }
    $id = 'faq-' . substr(md5($heading), 0, 6);
    ?>
    <section class="section faq-section" aria-labelledby="<?= $id ?>">
      <div class="container">
        <div class="faq-head reveal">
          <span class="section-label">Answers</span>
          <h2 class="section-title" id="<?= $id ?>"><?= h($heading) ?></h2>
          <div class="gold-divider"></div>
          <?php if ($intro !== ''): ?>
            <p class="faq-intro"><?= h($intro) ?></p>
          <?php endif; ?>
        </div>

        <div class="faq-list reveal">
          <?php foreach ($faqs as $i => $f): ?>
            <details class="faq-item"<?= $i === 0 ? ' open' : '' ?>>
              <summary class="faq-q">
                <span><?= h($f['q']) ?></span>
                <svg class="faq-chevron" viewBox="0 0 24 24" width="20" height="20" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
              </summary>
              <div class="faq-a"><p><?= h($f['a']) ?></p></div>
            </details>
          <?php endforeach; ?>
        </div>

        <p class="faq-cta">
          Still have a question?
          <a href="<?= SITE_URL ?>/enquiry">Send an enquiry</a> or call
          <a href="tel:+919760058796">+91 97600 58796</a> — our team answers from the mill.
        </p>
      </div>
    </section>
    <?php
}
