<?php
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Contact Us | Fabloom Group of Company';
$page_desc  = 'Get in touch with Fabloom Group. Visit our manufacturing unit in Bhagalpur, Bihar or branch in Noida. Call, email or fill our contact form.';
$page_canonical = SITE_URL . '/contact';
$page_og_image = SITE_URL . '/assets/images/og-contact.jpg';
$page_schema = <<<'JSONLD'
{
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "LocalBusiness",
        "@id": "https://www.thefabloom.com/#localbusiness-bhagalpur",
        "name": "Fabloom Group of Company – Manufacturing Unit",
        "image": "https://www.thefabloom.com/assets/images/logo-1.webp",
        "url": "https://www.thefabloom.com",
        "telephone": "+919760058796",
        "email": "info@thefabloom.com",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "Mohiuddin Pur, Post–Habibpur",
          "addressLocality": "Bhagalpur",
          "addressRegion": "Bihar",
          "postalCode": "813113",
          "addressCountry": "IN"
        },
        "geo": { "@type": "GeoCoordinates", "latitude": 25.2260481, "longitude": 86.9755285 },
        "openingHoursSpecification": [
          { "@type": "OpeningHoursSpecification", "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"], "opens": "09:00", "closes": "18:00" }
        ],
        "hasMap": "https://maps.app.goo.gl/TUQdwxNjSqC5y5e77"
      },
      {
        "@type": "LocalBusiness",
        "@id": "https://www.thefabloom.com/#localbusiness-noida",
        "name": "Fabloom Group of Company – Noida Branch",
        "image": "https://www.thefabloom.com/assets/images/logo-1.webp",
        "url": "https://www.thefabloom.com",
        "telephone": "+919760058796",
        "email": "info@thefabloom.com",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "Sector 15",
          "addressLocality": "Noida",
          "addressRegion": "Uttar Pradesh",
          "postalCode": "201301",
          "addressCountry": "IN"
        },
        "openingHoursSpecification": [
          { "@type": "OpeningHoursSpecification", "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"], "opens": "09:00", "closes": "18:00" }
        ]
      },
      {
        "@type": "BreadcrumbList",
        "itemListElement": [
          { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://www.thefabloom.com/" },
          { "@type": "ListItem", "position": 2, "name": "Contact", "item": "https://www.thefabloom.com/contact.html" }
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
          <span class="script-text" style="font-size:1.5rem;color:#D93B3D;display:block;margin-bottom:0.5rem;">We're Here to Help</span>
          <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:800;color:#FFFFFF;line-height:1.15;margin-bottom:1.25rem;">Get In Touch</h1>
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol style="list-style:none;display:flex;align-items:center;gap:0.5rem;padding:0;margin:0;flex-wrap:wrap;">
              <li><a href="index" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.875rem;">Home</a></li>
              <li style="color:rgba(255,255,255,0.4);font-size:0.875rem;" aria-hidden="true">/</li>
              <li style="color:#C0282A;font-size:0.875rem;font-weight:600;" aria-current="page">Contact</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- CONTACT CARDS -->
    <section class="section bg-cream" aria-labelledby="contact-channels-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Reach Us</span>
          <h2 class="section-title" id="contact-channels-heading">Contact Channels</h2>
          <div class="gold-divider"></div>
          <p class="section-subtitle mt-4">Reach us by phone, email, or WhatsApp — whichever is most convenient for you.</p>
        </div>

        <div class="grid-3 mt-12 stagger-children">
          <!-- Phone -->
          <div class="service-card reveal" style="text-align:center;padding:2.5rem 2rem;">
            <div class="service-card__icon" style="margin:0 auto 1.5rem;width:72px;height:72px;background:rgba(192,40,42,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
            </div>
            <h3 class="service-card__title">Call Us</h3>
            <p class="service-card__desc" style="margin-bottom:1.5rem;">Speak directly with our team for immediate assistance on orders, samples, or general enquiries.</p>
            <a href="tel:+919760058796" class="btn btn-primary" style="width:100%;justify-content:center;">
              +91 97600 58796
            </a>
            <div style="font-size:0.8rem;color:var(--clr-text-muted);margin-top:0.75rem;">Mon–Sat · 9:00 AM – 6:00 PM IST</div>
          </div>

          <!-- Email -->
          <div class="service-card reveal" style="text-align:center;padding:2.5rem 2rem;">
            <div class="service-card__icon" style="margin:0 auto 1.5rem;width:72px;height:72px;background:rgba(192,40,42,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <h3 class="service-card__title">Email Us</h3>
            <p class="service-card__desc" style="margin-bottom:1.5rem;">Send your detailed requirements by email. We respond to all emails within 24 working hours.</p>
            <a href="mailto:info@thefabloom.com" class="btn btn-primary" style="width:100%;justify-content:center;font-size:0.875rem;">
              info@thefabloom.com
            </a>
            <div style="font-size:0.8rem;color:var(--clr-text-muted);margin-top:0.75rem;">Also: fabloom86@gmail.com</div>
          </div>

          <!-- WhatsApp -->
          <div class="service-card reveal" style="text-align:center;padding:2.5rem 2rem;">
            <div class="service-card__icon" style="margin:0 auto 1.5rem;width:72px;height:72px;background:#E7F9EF;border-radius:50%;display:flex;align-items:center;justify-content:center;">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="#25D366" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <h3 class="service-card__title">WhatsApp</h3>
            <p class="service-card__desc" style="margin-bottom:1.5rem;">Chat directly with our textile experts for the fastest response. Share images, specifications, and get instant guidance.</p>
            <a href="https://wa.me/919760058796?text=Hello%20Fabloom%2C%20I%20would%20like%20to%20enquire%20about%20your%20fabrics." target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width:100%;justify-content:center;background:#12803F;border-color:#12803F;">
              Chat on WhatsApp
            </a>
            <div style="font-size:0.8rem;color:var(--clr-text-muted);margin-top:0.75rem;">Fastest response channel</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ADDRESSES -->
    <section class="section bg-white" aria-labelledby="addresses-heading">
      <div class="container">
        <div class="text-center reveal">
          <span class="section-label">Our Locations</span>
          <h2 class="section-title" id="addresses-heading">Find Us</h2>
          <div class="gold-divider"></div>
        </div>

        <div class="grid-2 mt-12" style="gap:2rem;">
          <!-- Manufacturing Unit -->
          <div class="card reveal-left" style="padding:2.5rem;border-radius:20px;border-top:4px solid var(--clr-red);">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
              <div style="width:52px;height:52px;background:rgba(192,40,42,0.12);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              </div>
              <div>
                <div style="font-size:0.7rem;color:var(--clr-red);text-transform:uppercase;letter-spacing:0.1em;font-weight:700;margin-bottom:0.2rem;">Manufacturing Unit</div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;color:var(--clr-charcoal);">Bhagalpur Factory</h3>
              </div>
            </div>
            <address style="font-style:normal;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1.5rem;">
              Mohiuddin Pur, Post–Habibpur,<br>
              Bhagalpur, Bihar – 813113<br>
              India
            </address>
            <div style="display:flex;flex-direction:column;gap:0.75rem;margin-bottom:1.5rem;">
              <a href="tel:+919760058796" style="display:flex;align-items:center;gap:0.75rem;min-height:44px;color:var(--clr-charcoal);text-decoration:none;font-weight:500;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                +91 97600 58796
              </a>
              <a href="mailto:info@thefabloom.com" style="display:flex;align-items:center;gap:0.75rem;min-height:44px;color:var(--clr-charcoal);text-decoration:none;font-weight:500;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                info@thefabloom.com
              </a>
            </div>
            <div style="padding:0.875rem 1.25rem;background:var(--clr-cream);border-radius:10px;margin-bottom:1.5rem;">
              <div style="font-size:0.75rem;color:var(--clr-red);font-weight:700;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.3rem;">Business Hours</div>
              <div style="color:var(--clr-charcoal);font-weight:600;">Mon – Sat: 9:00 AM – 6:00 PM IST</div>
              <div style="color:var(--clr-text-muted);font-size:0.875rem;">Sunday: Closed</div>
            </div>
            <a href="https://maps.app.goo.gl/TUQdwxNjSqC5y5e77" target="_blank" rel="noopener noreferrer" class="btn btn-outline" style="width:100%;justify-content:center;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              View on Google Maps
            </a>
          </div>

          <!-- Branch Office -->
          <div class="card reveal-right" style="padding:2.5rem;border-radius:20px;border-top:4px solid var(--clr-red);">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
              <div style="width:52px;height:52px;background:rgba(192,40,42,0.12);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="1.8" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="17"/><line x1="9.5" y1="14.5" x2="14.5" y2="14.5"/></svg>
              </div>
              <div>
                <div style="font-size:0.7rem;color:var(--clr-red);text-transform:uppercase;letter-spacing:0.1em;font-weight:700;margin-bottom:0.2rem;">Branch Office</div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;color:var(--clr-charcoal);">Noida Office</h3>
              </div>
            </div>
            <address style="font-style:normal;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:1.5rem;">
              Sector 15,<br>
              Noida, Uttar Pradesh – 201301<br>
              India
            </address>
            <div style="display:flex;flex-direction:column;gap:0.75rem;margin-bottom:1.5rem;">
              <a href="tel:+919760058796" style="display:flex;align-items:center;gap:0.75rem;min-height:44px;color:var(--clr-charcoal);text-decoration:none;font-weight:500;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                +91 97600 58796
              </a>
              <a href="mailto:fabloom86@gmail.com" style="display:flex;align-items:center;gap:0.75rem;min-height:44px;color:var(--clr-charcoal);text-decoration:none;font-weight:500;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                fabloom86@gmail.com
              </a>
            </div>
            <div style="padding:0.875rem 1.25rem;background:var(--clr-cream);border-radius:10px;margin-bottom:1.5rem;">
              <div style="font-size:0.75rem;color:var(--clr-red);font-weight:700;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.3rem;">Business Hours</div>
              <div style="color:var(--clr-charcoal);font-weight:600;">Mon – Sat: 9:00 AM – 6:00 PM IST</div>
              <div style="color:var(--clr-text-muted);font-size:0.875rem;">Sunday: Closed</div>
            </div>
            <a href="https://wa.me/919760058796" target="_blank" rel="noopener noreferrer" class="btn btn-outline" style="width:100%;justify-content:center;border-color:#12803F;color:#12803F;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="#25D366" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              WhatsApp Us
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- CONTACT FORM -->
    <section class="section bg-cream" aria-labelledby="contact-form-heading">
      <div class="container">
        <div class="grid-2" style="gap:4rem;align-items:start;">
          <div class="reveal-left">
            <span class="section-label">Write to Us</span>
            <h2 class="section-title" id="contact-form-heading">Send a Message</h2>
            <div class="gold-divider"></div>
            <p style="margin-top:1.5rem;color:var(--clr-text-secondary);line-height:1.85;margin-bottom:2rem;">
              Use the form to send us a message. Whether it's a fabric enquiry, bulk order query, or a general question — our team will get back to you within 24 working hours.
            </p>

            <div style="display:flex;flex-direction:column;gap:1.25rem;">
              <div style="display:flex;align-items:flex-start;gap:1.25rem;">
                <div style="width:48px;height:48px;background:rgba(192,40,42,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
                <div>
                  <div style="font-weight:700;color:var(--clr-charcoal);margin-bottom:0.2rem;">Phone</div>
                  <a href="tel:+919760058796" style="color:var(--clr-text-secondary);text-decoration:none;">+91 97600 58796</a>
                </div>
              </div>
              <div style="display:flex;align-items:flex-start;gap:1.25rem;">
                <div style="width:48px;height:48px;background:rgba(192,40,42,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                  <div style="font-weight:700;color:var(--clr-charcoal);margin-bottom:0.2rem;">Email</div>
                  <a href="mailto:info@thefabloom.com" style="color:var(--clr-text-secondary);text-decoration:none;">info@thefabloom.com</a><br>
                  <a href="mailto:fabloom86@gmail.com" style="color:var(--clr-text-secondary);text-decoration:none;">fabloom86@gmail.com</a>
                </div>
              </div>
              <div style="display:flex;align-items:flex-start;gap:1.25rem;">
                <div style="width:48px;height:48px;background:rgba(192,40,42,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--clr-red)" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <div style="font-weight:700;color:var(--clr-charcoal);margin-bottom:0.2rem;">Business Hours</div>
                  <div style="color:var(--clr-text-secondary);">Monday – Saturday</div>
                  <div style="color:var(--clr-text-secondary);">9:00 AM – 6:00 PM IST</div>
                </div>
              </div>
            </div>
          </div>

          <div class="reveal-right">
            <form id="contact-form" data-enquiry-form="contact" data-success-id="contact-success" method="POST" action="api/enquiry.php" novalidate style="background:white;border-radius:20px;padding:2.5rem;box-shadow:var(--shadow-lg);" aria-label="Contact form">
              <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;margin-bottom:0.4rem;color:var(--clr-charcoal);">Contact Form</h3>
              <p style="font-size:0.875rem;color:var(--clr-text-muted);margin-bottom:2rem;">We'll respond within 24 working hours.</p>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                <div class="form-group">
                  <label class="form-label" for="c-name">Full Name <span class="required">*</span></label>
                  <input type="text" id="c-name" name="name" class="form-control" placeholder="Your name" required autocomplete="name">
                  <span class="form-error" id="err-c-name">Please enter your name.</span>
                </div>
                <div class="form-group">
                  <label class="form-label" for="c-phone">Phone <span class="required">*</span></label>
                  <input type="tel" id="c-phone" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX" required autocomplete="tel">
                  <span class="form-error" id="err-c-phone">Please enter a valid phone number.</span>
                </div>
              </div>

              <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" for="c-email">Email Address</label>
                <input type="email" id="c-email" name="email" class="form-control" placeholder="you@example.com" autocomplete="email">
              </div>

              <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" for="c-subject">Subject</label>
                <input type="text" id="c-subject" name="subject" class="form-control" placeholder="How can we help you?">
              </div>

              <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" for="c-message">Message <span class="required">*</span></label>
                <textarea id="c-message" name="message" class="form-control" rows="5" placeholder="Write your message here — fabric requirements, queries, or anything else..." required></textarea>
                <span class="form-error" id="err-c-message">Please write a message.</span>
              </div>

              <button type="submit" class="btn btn-primary" id="contact-submit" style="width:100%;justify-content:center;">
                <span class="btn-text">Send Message</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </button>

              <div id="contact-success" style="display:none;text-align:center;padding:2rem;color:#27AE60;" role="alert" aria-live="polite">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#27AE60" stroke-width="2" style="margin:0 auto 1rem;display:block;" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                <strong>Message Sent!</strong> We'll get back to you within 24 working hours.
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- FULL-WIDTH GOOGLE MAP -->
    <section aria-label="Bhagalpur location map" style="display:block;height:460px;overflow:hidden;">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3618.5!2d86.9755285!3d25.2260481!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39f04a12daaaaaab%3A0x189eb628e374c268!2sFABLOOM!5e0!3m2!1sen!2sin!4v1716000000000"
        title="Fabloom Group of Company manufacturing unit – Bhagalpur, Bihar"
        width="100%"
        height="460"
        style="border:0;display:block;"
        loading="lazy"
        allowfullscreen
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
