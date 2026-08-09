<?php
/**
 * Disclaimer.
 *
 * Covers the two things this site can most easily be read as promising more
 * than it does: product photography of hand-made cloth, and the guidance in
 * the blog articles.
 */
require_once __DIR__ . '/includes/functions.php';

$page_title     = 'Disclaimer | Fabloom Group of Company';
$page_desc      = 'Important notices about the information, product photographs, colour reproduction and blog guidance published on the Fabloom website.';
$page_canonical = SITE_URL . '/disclaimer';

$legal_eyebrow = 'Please Note';
$legal_title   = 'Disclaimer';
$legal_sub     = 'What the information, images and articles on this website do — and do not — promise.';
$legal_crumb   = 'Disclaimer';

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'WebPage',
            '@id'         => $page_canonical,
            'name'        => 'Disclaimer',
            'url'         => $page_canonical,
            'description' => $page_desc,
            'publisher'   => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => SITE_URL],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Disclaimer', 'item' => $page_canonical],
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

            <h2>General information</h2>
            <p>
              The content on <a href="<?= SITE_URL ?>/"><?= h(preg_replace('~^https?://~', '', SITE_URL)) ?></a>
              is published by <strong><?= h(SITE_NAME) ?></strong> for general information about our
              fabrics and our mill. We prepare it carefully and keep it current, but we give no
              warranty that every detail is complete, accurate or up to date at the moment you read it.
              Any reliance you place on it is at your own risk.
            </p>

            <h2>Product photographs and colour</h2>
            <p>
              Our fabrics are woven, dyed, block-printed and hand-brushed by hand in Bhagalpur.
              Photographs on this site are taken in daylight with minimal correction, but:
            </p>
            <ul>
              <li>colour renders differently on every screen, depending on the display, brightness and ambient light;</li>
              <li>dye lots vary between batches, so a repeat order may differ slightly in shade;</li>
              <li>slubs, minor weave irregularity and small print variation are inherent to handloom cloth, not defects;</li>
              <li>images are shot as flat cloth or drape and are not to scale &mdash; check the stated width, GSM and repeat.</li>
            </ul>
            <p>
              Where an exact shade or finish is critical, please ask for a physical swatch before
              committing to a bulk order. Our
              <a href="<?= SITE_URL ?>/refund-policy">Refund &amp; Cancellation Policy</a> explains
              what variation is and is not covered.
            </p>

            <h2>Specifications, stock and pricing</h2>
            <p>
              Width, GSM, composition, shrinkage and yardage figures are nominal and subject to
              normal mill tolerance. Availability and prices shown on the site can change without
              notice; the figures confirmed to you against your order or quotation are the ones
              that apply.
            </p>

            <h2>Blog and editorial content</h2>
            <p>
              Articles in the <a href="<?= SITE_URL ?>/blog">Fabloom Journal</a> &mdash; on fabric
              care, weaving heritage, printing craft and buying guidance &mdash; are written from
              our own experience at the loom. They are general in nature and are not professional,
              legal, financial or technical advice. Test any care or processing method on a small
              sample before applying it to a full piece; we are not responsible for damage caused
              by following general guidance on a specific fabric.
            </p>

            <h2>External links</h2>
            <p>
              This site links to third-party websites, social media profiles and embedded maps for
              your convenience. We do not control them, do not endorse their content, and are not
              responsible for their accuracy, availability or privacy practices. Visiting them is
              at your own risk.
            </p>

            <h2>Testimonials</h2>
            <p>
              Any client feedback shown on this site reflects the experience of that individual
              buyer. It is not a guarantee that you will get the same result, and it is published
              with the permission of the person quoted.
            </p>

            <h2>No professional relationship</h2>
            <p>
              Reading this site, or sending an enquiry through it, does not by itself create a
              supply contract or any professional relationship. A contract arises only when we
              confirm an order, as set out in our
              <a href="<?= SITE_URL ?>/terms-and-conditions">Terms &amp; Conditions</a>.
            </p>

            <h2>Website availability</h2>
            <p>
              We aim to keep the site available and free of errors, but we do not warrant
              uninterrupted access. The site may be unavailable during maintenance, hosting
              incidents or events outside our control, and we are not liable for any loss arising
              from downtime, or from viruses or harmful code transmitted through the site or a
              linked one.
            </p>

            <h2>Intellectual property</h2>
            <p>
              All photographs, print designs, artwork and text on this site belong to
              <?= h(SITE_NAME) ?> or its licensors. Reproducing our designs on cloth, or using our
              images commercially, without written permission is an infringement of those rights.
            </p>

            <h2>Limitation of liability</h2>
            <p>
              Nothing here excludes any liability that cannot lawfully be excluded, including under
              the Consumer Protection Act, 2019. Subject to that, <?= h(SITE_NAME) ?> is not liable
              for any indirect or consequential loss arising from the use of this website or
              reliance on its content.
            </p>

            <h2>Contact</h2>
            <p>
              If something on this site looks wrong, please tell us and we will correct it.
            </p>
            <p>
              <strong><?= h(SITE_NAME) ?></strong><br>
              Mohiuddin Pur, Post&ndash;Habibpur, Bhagalpur, Bihar &ndash; 813113, India<br>
              Email: <a href="mailto:info@thefabloom.com">info@thefabloom.com</a><br>
              Phone: <a href="tel:+919760058796">+91 97600 58796</a>
            </p>

            <p style="margin-top:2.5rem;">
              See also our <a href="<?= SITE_URL ?>/privacy-policy">Privacy Policy</a>,
              <a href="<?= SITE_URL ?>/terms-and-conditions">Terms &amp; Conditions</a> and
              <a href="<?= SITE_URL ?>/refund-policy">Refund &amp; Cancellation Policy</a>.
            </p>

          </div>
        </div>
      </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
