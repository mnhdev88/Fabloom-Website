<?php
/**
 * Refund & Cancellation Policy.
 *
 * Orders on this site are Cash on Delivery, so "refund" in practice means a
 * bank transfer against a verified claim rather than a card reversal. Keep
 * that in step with checkout.php if a prepaid gateway is ever added.
 */
require_once __DIR__ . '/includes/functions.php';

$page_title     = 'Refund & Cancellation Policy | Fabloom Group of Company';
$page_desc      = 'How to cancel an order, report a damaged or wrong fabric delivery, and how Fabloom processes replacements and refunds — with timelines and what is not covered.';
$page_canonical = SITE_URL . '/refund-policy';

$legal_eyebrow = 'Straight Answers';
$legal_title   = 'Refund & Cancellation Policy';
$legal_sub     = 'When an order can be cancelled, what we replace or refund, and how long each step takes.';
$legal_crumb   = 'Refund Policy';

$page_schema = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'WebPage',
            '@id'         => $page_canonical,
            'name'        => 'Refund & Cancellation Policy',
            'url'         => $page_canonical,
            'description' => $page_desc,
            'publisher'   => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => SITE_URL],
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Refund & Cancellation Policy', 'item' => $page_canonical],
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
              We cut, pack and dispatch fabric to order. This policy sets out exactly when an
              order can be cancelled, what we will replace or refund, and how quickly. It applies
              to orders placed with <strong><?= h(SITE_NAME) ?></strong> through this website.
            </p>

            <h2>1. Cancelling an order</h2>
            <ul>
              <li><strong>Before dispatch:</strong> you may cancel free of charge. Call or WhatsApp <a href="tel:+919760058796">+91 97600 58796</a>, or email <a href="mailto:info@thefabloom.com">info@thefabloom.com</a> with your order number.</li>
              <li><strong>After dispatch:</strong> the order can no longer be cancelled. If you refuse the parcel at delivery, please tell us the same day so we can trace the return.</li>
              <li><strong>Custom orders:</strong> fabric that is being custom dyed, printed, hand-brushed or woven to your specification can only be cancelled before production begins. Once the yarn is dyed or the screen is set, the order is final.</li>
              <li><strong>Cancelled by us:</strong> if we cannot fulfil an order &mdash; loom capacity, stock or a delivery area we cannot reach &mdash; we will tell you and refund anything already paid in full.</li>
            </ul>

            <h2>2. When we replace or refund</h2>
            <p>We will replace the fabric, or refund it, where:</p>
            <ul>
              <li>the parcel arrived <strong>damaged, wet or torn</strong> in transit;</li>
              <li>you received the <strong>wrong fabric, colour or design</strong> against your confirmed order;</li>
              <li>the <strong>metrage is short</strong> of what was billed;</li>
              <li>the cloth has a <strong>genuine manufacturing defect</strong> &mdash; a running weaving fault, a hole, a print misprint or a dye patch across the piece.</li>
            </ul>
            <p>
              Raise the claim within <strong>48 hours of delivery</strong>, before the fabric is
              cut, washed or processed in any way.
            </p>

            <h2>3. What is not covered</h2>
            <ul>
              <li><strong>Handloom character.</strong> Slubs, minor weave irregularity, small variations in texture and slight print misregistration are inherent to hand-woven and hand-printed cloth.</li>
              <li><strong>Shade variation.</strong> Screens render colour differently, and dye lots vary. A reasonable difference between the photograph, an earlier lot and your delivery is not a defect. Order a swatch first where the exact shade matters.</li>
              <li><strong>Fabric that has been cut, washed, stitched, dyed or processed.</strong> Please inspect the full piece before you cut it.</li>
              <li><strong>Custom dyed, printed or specially woven orders</strong>, except where there is a genuine manufacturing defect.</li>
              <li><strong>Change of mind</strong>, wrong quantity ordered, or the fabric not suiting a use it was not sold for.</li>
              <li><strong>Damage after delivery</strong>, including from storage, washing against care instructions, or handling.</li>
              <li><strong>Claims raised after 48 hours</strong> of delivery, or without the supporting evidence described below.</li>
            </ul>

            <h2>4. How to raise a claim</h2>
            <ol>
              <li><strong>Record the parcel.</strong> Film a single unbroken video while opening the sealed package. This is the one piece of evidence a courier will accept for a transit damage or shortage claim, and we cannot pursue it without one.</li>
              <li><strong>Photograph the problem</strong> &mdash; the outer packing, the shipping label, and clear close-up and full-piece shots of the fault.</li>
              <li><strong>Write to us within 48 hours</strong> at <a href="mailto:info@thefabloom.com">info@thefabloom.com</a>, or WhatsApp <a href="tel:+919760058796">+91 97600 58796</a>, with your order number, what is wrong, and the video and photographs.</li>
              <li><strong>Keep the fabric as delivered</strong>, in its original packing, until the claim is settled. Do not cut or wash it.</li>
            </ol>
            <p>
              We acknowledge every claim within 2 working days and complete the assessment within
              7 working days of receiving your evidence, or of the returned fabric reaching our
              unit, whichever is later.
            </p>

            <h2>5. Returning the fabric</h2>
            <ul>
              <li>Do not send anything back before we approve the return &mdash; unapproved returns cannot be processed and may be refused at our gate.</li>
              <li>Once approved, we will confirm the return address and arrange a reverse pickup where our courier partner serves your pincode.</li>
              <li><strong>We bear the return freight</strong> when the fault is ours &mdash; damage in transit, wrong goods, shortage or a manufacturing defect.</li>
              <li>Fabric must come back unused, uncut, unwashed and in its original packing, with the invoice.</li>
            </ul>

            <h2>6. Refunds</h2>
            <p>
              Orders placed on this website are <strong>Cash on Delivery</strong>, so an approved
              refund is paid by <strong>bank transfer (NEFT/IMPS) or UPI</strong> to an account in
              the name of the person who placed the order. We will ask you for those details in
              writing once the claim is approved.
            </p>
            <ul>
              <li>Refunds are initiated within <strong>3&ndash;5 working days</strong> of approval and normally reach your account within <strong>7&ndash;10 working days</strong>, depending on your bank.</li>
              <li>Where you prefer, we will send a <strong>replacement piece</strong> or issue a <strong>credit note</strong> against a future order instead.</li>
              <li>Where only part of an order is affected, only that part is refunded.</li>
              <li>Freight actually paid on a delivered order is refunded only where the fault is ours.</li>
              <li>For prepaid bulk and export orders, refunds are made to the originating bank account, net of any bank or remittance charges levied by the intermediary banks.</li>
            </ul>

            <h2>7. Failed or refused deliveries</h2>
            <p>
              If a Cash on Delivery parcel is refused without a valid reason, or delivery fails
              because the address or phone number given was wrong, we may recover the freight we
              actually incurred before accepting a further order from the same address.
            </p>

            <h2>8. Bulk, wholesale and export orders</h2>
            <p>
              Bulk and export consignments are supplied against a separately agreed contract or
              proforma invoice. Where that agreement sets out its own inspection, claim or payment
              terms, those terms apply in place of this page.
            </p>

            <h2>9. Contact</h2>
            <p>
              <strong><?= h(SITE_NAME) ?></strong><br>
              Mohiuddin Pur, Post&ndash;Habibpur, Bhagalpur, Bihar &ndash; 813113, India<br>
              Email: <a href="mailto:info@thefabloom.com">info@thefabloom.com</a><br>
              Phone / WhatsApp: <a href="tel:+919760058796">+91 97600 58796</a><br>
              Support hours: Monday to Saturday, 10:00 AM &ndash; 7:00 PM IST
            </p>

            <p style="margin-top:2.5rem;">
              This policy forms part of our <a href="<?= SITE_URL ?>/terms-and-conditions">Terms &amp; Conditions</a>.
              See also our <a href="<?= SITE_URL ?>/privacy-policy">Privacy Policy</a> and
              <a href="<?= SITE_URL ?>/disclaimer">Disclaimer</a>.
            </p>

          </div>
        </div>
      </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
