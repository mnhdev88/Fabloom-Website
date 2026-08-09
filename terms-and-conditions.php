<?php
/**
 * Terms & Conditions.
 *
 * The commercial clauses here mirror the real checkout: Cash on Delivery is
 * the only payment method (checkout.php writes payment_method = "cod"), and
 * MIN_ORDER_METRES sets the minimum cut length. Both are read from the
 * constants so the page cannot drift away from what the cart enforces.
 */
require_once __DIR__ . '/includes/functions.php';

$page_title     = 'Terms & Conditions | Fabloom Group of Company';
$page_desc      = 'The terms on which Fabloom sells silk and linen fabric — orders, pricing, minimum quantity, dispatch, colour variation, intellectual property and governing law.';
$page_canonical = SITE_URL . '/terms-and-conditions';

$legal_eyebrow = 'The Fine Print';
$legal_title   = 'Terms & Conditions';
$legal_sub     = 'The agreement between you and Fabloom when you browse this website, send an enquiry or place an order.';
$legal_crumb   = 'Terms & Conditions';

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'WebPage',
            '@id'         => $page_canonical,
            'name'        => 'Terms & Conditions',
            'url'         => $page_canonical,
            'description' => $page_desc,
            'publisher'   => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => SITE_URL],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Terms & Conditions', 'item' => $page_canonical],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

require_once __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/legal-hero.php';
?>

    <section class="section bg-cream">
      <div class="container">
        <div class="article-card reveal" style="max-width:900px;margin:0 auto;box-shadow:var(--shadow-sm);">
          <div class="article-body">

            <p style="font-size:0.875rem;color:var(--clr-text-secondary);margin-bottom:2rem;">
              <strong>Last updated:</strong> <?= h(LEGAL_UPDATED) ?>
            </p>

            <p>
              These Terms &amp; Conditions govern your use of
              <a href="<?= SITE_URL ?>/"><?= h(preg_replace('~^https?://~', '', SITE_URL)) ?></a>
              and any order you place with <strong><?= h(SITE_NAME) ?></strong>
              (&ldquo;Fabloom&rdquo;, &ldquo;we&rdquo;, &ldquo;us&rdquo;), a fabric manufacturing
              business based at Mohiuddin Pur, Post&ndash;Habibpur, Bhagalpur, Bihar &ndash; 813113, India.
            </p>
            <p>
              By browsing this website, submitting an enquiry or placing an order, you confirm
              that you have read and accepted these terms. If you do not accept them, please do
              not use the site.
            </p>

            <h2>1. Eligibility</h2>
            <p>
              You must be at least 18 years old and legally able to enter into a contract to place
              an order. If you are ordering on behalf of a business, you confirm that you are
              authorised to bind that business.
            </p>

            <h2>2. Products, colour and handloom variation</h2>
            <p>
              We manufacture and supply silk, linen, blended and printed fabrics. Much of what we
              sell is woven, dyed, block-printed or hand-brushed by hand in Bhagalpur.
            </p>
            <ul>
              <li>Slight irregularities in weave, slub, texture and print registration are inherent to handloom and hand-printed cloth. They are marks of craft, not defects.</li>
              <li>Colour on your screen depends on your device, and dye lots vary between batches. A small variation between the photograph, a swatch and the delivered fabric is normal and is not a ground for rejection.</li>
              <li>Where an exact shade is essential, please request a physical swatch before ordering, or specify the shade reference in your enquiry.</li>
              <li>Width, GSM and shrinkage figures on product pages are nominal and subject to normal mill tolerance.</li>
            </ul>

            <h2>3. Pricing and minimum order</h2>
            <ul>
              <li>Prices are shown in Indian Rupees (<?= h(CURRENCY) ?>) and are quoted per metre unless stated otherwise.</li>
              <li>The minimum order for a fabric is <strong><?= (int)MIN_ORDER_METRES ?> metres</strong> per design or colour, unless we agree otherwise in writing.</li>
              <li>We may correct an obvious pricing or typographical error at any time before dispatch. If a price has been listed incorrectly, we will tell you and you may confirm the corrected price or cancel the order at no cost.</li>
              <li>Prices may change without notice. The price applicable to your order is the one confirmed at the time the order is placed.</li>
              <li>Bulk, wholesale and export pricing is quoted separately against an <a href="<?= SITE_URL ?>/enquiry">enquiry</a> and may carry its own agreed terms.</li>
            </ul>

            <h2>4. Orders and acceptance</h2>
            <p>
              Product listings on this site are an invitation to order, not a binding offer. Your
              order is an offer to buy. A contract is formed only when we confirm the order, and we
              may decline or cancel an order &mdash; refunding anything already paid &mdash; where:
            </p>
            <ul>
              <li>the fabric or colourway is no longer available or the loom capacity is committed;</li>
              <li>the delivery address is outside the area our courier partners serve;</li>
              <li>the order appears fraudulent, or previous orders to the same address have been refused on delivery;</li>
              <li>the details you supplied are incomplete or cannot be verified.</li>
            </ul>

            <h2>5. Payment</h2>
            <p>
              Orders placed through this website are handled on a <strong>Cash on Delivery</strong>
              basis: you pay the courier in full when the parcel is handed over to you. Please keep
              the exact amount ready and check that the packaging is intact before you accept it.
            </p>
            <p>
              For bulk, wholesale and export orders we invoice separately, and payment terms
              (advance, part-advance or bank transfer against proforma invoice) are agreed in
              writing before production begins. We do not ask for payment through any personal
              account, wallet or link other than the details on our official invoice &mdash; please
              verify with us on <a href="tel:+919760058796">+91 97600 58796</a> if in doubt.
            </p>

            <h2>6. Dispatch and delivery</h2>
            <ul>
              <li>Ready stock is normally dispatched within 2&ndash;5 working days of order confirmation. Custom dyeing, printing or weaving takes longer, and we confirm the lead time on your order.</li>
              <li>Delivery timelines quoted by us or by the courier are estimates. We are not liable for delays caused by the courier, weather, transport strikes, festivals or other events beyond our control.</li>
              <li>Risk in the goods passes to you on delivery. Title passes once the order has been paid in full.</li>
              <li>Please ensure someone is available at the delivery address. Where a parcel is returned to us undelivered after failed attempts, we may recover the freight cost incurred on any replacement dispatch.</li>
            </ul>

            <h2>7. Cancellation, returns and refunds</h2>
            <p>
              Cancellations, returns and refunds are governed by our
              <a href="<?= SITE_URL ?>/refund-policy">Refund &amp; Cancellation Policy</a>, which
              forms part of these terms.
            </p>

            <h2>8. Accounts</h2>
            <p>
              You are responsible for keeping your account password confidential and for all
              activity under your account. Tell us immediately if you suspect unauthorised use. We
              may suspend or close an account that is used for fraud, abuse, scraping or any
              activity that breaches these terms.
            </p>

            <h2>9. Acceptable use</h2>
            <p>You agree not to:</p>
            <ul>
              <li>copy, scrape or republish our photographs, designs, product descriptions or catalogue for commercial use;</li>
              <li>attempt to gain unauthorised access to the site, its servers or its database;</li>
              <li>submit false enquiries, spam or malicious code through our forms;</li>
              <li>use the site in a way that breaches any applicable law or infringes anyone&rsquo;s rights.</li>
            </ul>

            <h2>10. Intellectual property</h2>
            <p>
              The Fabloom name and logo, and all photographs, print designs, artwork, text and
              layout on this site, belong to <?= h(SITE_NAME) ?> or its licensors and are protected
              by Indian and international law. You may view and print pages for your own reference.
              Any other reproduction, adaptation or commercial use &mdash; including reproducing our
              print designs on cloth &mdash; requires our written permission.
            </p>

            <h2>11. Limitation of liability</h2>
            <p>
              Nothing in these terms limits any liability that cannot be limited under Indian law,
              including the Consumer Protection Act, 2019. Subject to that, our total liability in
              connection with an order is limited to the amount you paid for that order, and we are
              not liable for indirect or consequential loss, including loss of profit, loss of
              production, or the cost of garments cut from fabric you accepted without inspection.
            </p>
            <p>
              Fabric is supplied for you to inspect and test before cutting. Please check every
              piece on receipt; claims raised after cutting or processing cannot be entertained.
            </p>

            <h2>12. Force majeure</h2>
            <p>
              We are not in breach of these terms if performance is prevented or delayed by events
              beyond our reasonable control, including natural disaster, flood, fire, epidemic,
              power failure, yarn shortage, labour unrest, transport disruption or government action.
            </p>

            <h2>13. Governing law and jurisdiction</h2>
            <p>
              These terms are governed by the laws of India. Subject to any right you have as a
              consumer to approach a forum near you, the courts at Bhagalpur, Bihar shall have
              jurisdiction over any dispute arising from these terms or from an order.
            </p>

            <h2>14. Changes to these terms</h2>
            <p>
              We may revise these terms from time to time. The version published on this page when
              you place an order is the version that applies to that order.
            </p>

            <h2>15. Contact</h2>
            <p>
              <strong><?= h(SITE_NAME) ?></strong><br>
              Mohiuddin Pur, Post&ndash;Habibpur, Bhagalpur, Bihar &ndash; 813113, India<br>
              Email: <a href="mailto:info@thefabloom.com">info@thefabloom.com</a><br>
              Phone: <a href="tel:+919760058796">+91 97600 58796</a>
            </p>

            <p style="margin-top:2.5rem;">
              See also our <a href="<?= SITE_URL ?>/privacy-policy">Privacy Policy</a>,
              <a href="<?= SITE_URL ?>/refund-policy">Refund &amp; Cancellation Policy</a> and
              <a href="<?= SITE_URL ?>/disclaimer">Disclaimer</a>.
            </p>

          </div>
        </div>
      </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
