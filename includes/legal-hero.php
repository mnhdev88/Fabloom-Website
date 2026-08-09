<?php
/**
 * Shared hero + opening card for the four legal pages (privacy, terms,
 * refund, disclaimer). The pages set these before including it:
 *
 *   $legal_eyebrow  script line above the title
 *   $legal_title    <h1>
 *   $legal_sub      one-line summary under the title
 *   $legal_crumb    breadcrumb label (defaults to the title)
 *
 * LEGAL_UPDATED is the single place the "Last updated" line is edited, so a
 * revision to one policy does not leave the other three claiming a stale date.
 */
if (!defined('LEGAL_UPDATED')) {
    define('LEGAL_UPDATED', '9 August 2026');
}
$legal_crumb = $legal_crumb ?? $legal_title;
?>
    <!-- ── PAGE HERO ── -->
    <section class="page-hero" aria-label="Page header" style="position:relative;min-height:360px;display:flex;align-items:center;overflow:hidden;">
      <div style="position:absolute;inset:0;background-image:url('<?= SITE_URL ?>/assets/images/products/silk-bhagalpur.webp');background-size:cover;background-position:center;opacity:0.18;" aria-hidden="true"></div>
      <div class="container" style="position:relative;z-index:1;padding-top:5rem;padding-bottom:3.5rem;">
        <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom:1rem;">
          <a href="<?= SITE_URL ?>/">Home</a>
          <span aria-hidden="true">/</span>
          <span class="current"><?= h($legal_crumb) ?></span>
        </nav>
        <span class="script-text" style="font-size:1.6rem;color:#D93B3D;display:block;margin-bottom:0.5rem;"><?= h($legal_eyebrow) ?></span>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,4.5vw,3.25rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:1rem;"><?= h($legal_title) ?></h1>
        <p style="color:rgba(255,255,255,0.8);max-width:620px;line-height:1.8;"><?= h($legal_sub) ?></p>
      </div>
    </section>
