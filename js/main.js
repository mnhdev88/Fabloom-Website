/* ============================================================
   FABLOOM — Main JS: Nav, Scroll, Back-to-top, Ripple, Filter
   ============================================================ */

(function () {
  'use strict';

  // ── Sticky navbar, shrinking on scroll ─────────────────────
  function initNavbar() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    // The `transparent` treatment (white links, white wordmark) only works when
    // the navbar overlays the dark hero. It doesn't — the navbar is sticky and
    // sits in its own band above the hero, so those white links landed on the
    // cream page background at 1.05:1 and were invisible. Keep the navbar solid.

    // Two thresholds, not one. A single value means a scroll that comes to
    // rest right on it toggles the class back and forth on every stray pixel,
    // and the bar visibly flutters. Shrink at 80, restore only below 40.
    const SHRINK_AT  = 80;
    const RESTORE_AT = 40;

    let shrunk = false;
    let ticking = false;

    function apply() {
      ticking = false;
      const y = window.scrollY;
      if (!shrunk && y > SHRINK_AT) {
        shrunk = true;
        navbar.classList.add('scrolled');
      } else if (shrunk && y < RESTORE_AT) {
        shrunk = false;
        navbar.classList.remove('scrolled');
      }
    }

    function onScroll() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(apply);
    }

    apply();                       // reload part-way down the page
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // ── Shared helpers ─────────────────────────────────────────
  const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), ' +
                    'select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function scrollToY(top) {
    window.scrollTo({ top, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
  }

  // ── Mobile Menu ────────────────────────────────────────────
  function initMobileMenu() {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeBtn = document.getElementById('menu-close');

    if (!hamburger || !mobileMenu) return;

    // Keep Tab inside the dialog — aria-modal alone doesn't do this.
    function trapFocus(e) {
      if (e.key !== 'Tab' || !mobileMenu.classList.contains('open')) return;

      const items = Array.from(mobileMenu.querySelectorAll(FOCUSABLE))
        .filter((el) => el.offsetParent !== null);
      if (!items.length) return;

      const first = items[0];
      const last  = items[items.length - 1];

      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }

    function openMenu() {
      hamburger.classList.add('open');
      mobileMenu.classList.add('open');
      hamburger.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
      closeBtn && closeBtn.focus();
    }

    function closeMenu() {
      hamburger.classList.remove('open');
      mobileMenu.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
      hamburger.focus();
    }

    hamburger.addEventListener('click', openMenu);
    closeBtn && closeBtn.addEventListener('click', closeMenu);
    mobileMenu.addEventListener('keydown', trapFocus);

    // Close on outside click
    mobileMenu.addEventListener('click', (e) => {
      if (e.target === mobileMenu) closeMenu();
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mobileMenu.classList.contains('open')) closeMenu();
    });

    // Close on nav link click
    mobileMenu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', closeMenu);
    });
  }

  // ── Back to Top ────────────────────────────────────────────
  function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    window.addEventListener('scroll', () => {
      btn.classList.toggle('visible', window.scrollY > 300);
    }, { passive: true });

    btn.addEventListener('click', () => scrollToY(0));
  }

  // ── Ripple Effect on Buttons ───────────────────────────────
  function initRipple() {
    document.querySelectorAll('.btn').forEach((btn) => {
      btn.addEventListener('click', function (e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);

        ripple.classList.add('ripple');
        ripple.style.cssText = `
          width: ${size}px;
          height: ${size}px;
          left: ${e.clientX - rect.left - size / 2}px;
          top: ${e.clientY - rect.top - size / 2}px;
        `;

        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 700);
      });
    });
  }

  // ── Product Filter ─────────────────────────────────────────
  function initProductFilter() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card[data-category]');

    if (!filterBtns.length || !productCards.length) return;

    filterBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        filterBtns.forEach((b) => { b.classList.remove('active'); b.setAttribute('aria-selected', 'false'); });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        const filter = btn.dataset.filter;

        productCards.forEach((card) => {
          const match = filter === 'all' || card.dataset.category === filter;
          card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          if (match) {
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
            card.style.display = '';
          } else {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => { if (card.dataset.category !== filter && filter !== 'all') card.style.display = 'none'; }, 300);
          }
        });
      });
    });
  }

  // ── Enquiry Tab Switcher ───────────────────────────────────
  function initTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content[data-tab-content]');

    if (!tabBtns.length) return;

    tabBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        const target = btn.dataset.tab;

        tabBtns.forEach((b) => { b.classList.remove('active'); b.setAttribute('aria-selected', 'false'); });
        tabContents.forEach((c) => c.classList.remove('active'));

        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        const content = document.querySelector(`.tab-content[data-tab-content="${target}"]`);
        if (content) content.classList.add('active');
      });
    });

    // Activate first tab by default
    if (tabBtns[0]) tabBtns[0].click();
  }

  // ── Lightbox ───────────────────────────────────────────────
  function initLightbox() {
    const galleryItems = document.querySelectorAll('.gallery-item[data-src]');
    if (!galleryItems.length) return;

    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    lightbox.setAttribute('role', 'dialog');
    lightbox.setAttribute('aria-modal', 'true');
    lightbox.setAttribute('aria-label', 'Image viewer');
    lightbox.innerHTML = `
      <button class="lightbox-close" aria-label="Close image viewer"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      <img src="" alt="">
    `;
    document.body.appendChild(lightbox);

    const lbImg = lightbox.querySelector('img');
    const lbClose = lightbox.querySelector('.lightbox-close');

    galleryItems.forEach((item) => {
      item.addEventListener('click', () => {
        lbImg.src = item.dataset.src;
        lbImg.alt = item.dataset.alt || '';
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
        lbClose.focus();
      });
    });

    function closeLightbox() {
      lightbox.classList.remove('open');
      document.body.style.overflow = '';
    }

    lbClose.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && lightbox.classList.contains('open')) closeLightbox(); });
  }

  // ── Smooth Anchor Scroll ───────────────────────────────────
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener('click', (e) => {
        const id = anchor.getAttribute('href').slice(1);
        const target = document.getElementById(id);
        if (target) {
          e.preventDefault();
          const navHeight = document.getElementById('navbar')?.offsetHeight || 72;
          const top = target.getBoundingClientRect().top + window.scrollY - navHeight - 16;
          scrollToY(top);
        }
      });
    });
  }

  // ── Hash-based tab on load (for enquiry.html#linen etc.) ──
  function initHashTab() {
    const hash = window.location.hash.slice(1);
    if (!hash) return;
    const btn = document.querySelector(`.tab-btn[data-tab="${hash}"]`);
    if (btn) btn.click();
  }

  // ── Hero Slideshow ────────────────────────────────────────
  function initHeroSlideshow() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots   = document.querySelectorAll('.hero-dot');
    const toggle = document.getElementById('hero-play-toggle');
    if (slides.length < 2) return;

    // Slides 2+ carry their image in data-bg so they don't compete with the
    // LCP slide for bandwidth. Attach them once the page has finished loading.
    function loadDeferredSlides() {
      slides.forEach((slide) => {
        const src = slide.getAttribute('data-bg');
        if (!src) return;
        slide.style.backgroundImage = `url('${src}')`;
        slide.removeAttribute('data-bg');
      });
    }

    if (document.readyState === 'complete') loadDeferredSlides();
    else window.addEventListener('load', loadDeferredSlides);

    const INTERVAL = 5000;
    let current = 0;
    let timer   = null;
    // Auto-rotation is motion: honour the OS setting and start paused.
    let paused  = prefersReducedMotion();

    function goTo(index) {
      slides[current].classList.remove('active');
      dots[current] && dots[current].classList.remove('active');
      dots[current] && dots[current].setAttribute('aria-pressed', 'false');
      current = (index + slides.length) % slides.length;
      slides[current].classList.add('active');
      dots[current] && dots[current].classList.add('active');
      dots[current] && dots[current].setAttribute('aria-pressed', 'true');
    }

    function next() { goTo(current + 1); }

    function play() {
      if (paused) return;
      clearInterval(timer);
      timer = setInterval(next, INTERVAL);
    }

    function stop() { clearInterval(timer); timer = null; }

    function setPaused(value) {
      paused = value;
      if (toggle) {
        toggle.setAttribute('aria-pressed', String(paused));
        toggle.setAttribute('aria-label', paused ? 'Play slideshow' : 'Pause slideshow');
      }
      paused ? stop() : play();
    }

    // Dots: jump to slide, and arrow keys move between them
    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => { goTo(i); play(); });
      dot.addEventListener('keydown', (e) => {
        if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
        e.preventDefault();
        const nextIndex = (i + (e.key === 'ArrowRight' ? 1 : -1) + dots.length) % dots.length;
        goTo(nextIndex);
        dots[nextIndex].focus();
        play();
      });
    });

    // Explicit pause/play control — the only one touch and keyboard users get
    if (toggle) toggle.addEventListener('click', () => setPaused(!paused));

    // Pause on hover and on keyboard focus entering the hero
    const hero = document.querySelector('.hero-section');
    if (hero) {
      hero.addEventListener('mouseenter', stop);
      hero.addEventListener('mouseleave', play);
      hero.addEventListener('focusin', stop);
      hero.addEventListener('focusout', play);
    }

    // Don't animate in a background tab
    document.addEventListener('visibilitychange', () => {
      document.hidden ? stop() : play();
    });

    // React if the user flips the OS setting while the page is open
    const mq = window.matchMedia('(prefers-reduced-motion: reduce)');
    const onMotionChange = (e) => setPaused(e.matches);
    mq.addEventListener ? mq.addEventListener('change', onMotionChange)
                        : mq.addListener(onMotionChange);

    setPaused(paused);
  }

  // ── Init all ──────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initMobileMenu();
    initBackToTop();
    initRipple();
    initProductFilter();
    initTabs();
    initLightbox();
    initSmoothScroll();
    initHashTab();
    initHeroSlideshow();
  });
})();
