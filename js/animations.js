/* ============================================================
   FABLOOM — Scroll Animations (IntersectionObserver)
   ============================================================ */

(function () {
  'use strict';

  // ── Reveal on scroll ──────────────────────────────────────
  const revealSelectors = '.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-blur';

  function initReveal() {
    const els = document.querySelectorAll(revealSelectors);
    if (!els.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    els.forEach((el) => observer.observe(el));
  }

  // ── Counter animation ──────────────────────────────────────
  function animateCounter(el, target, duration) {
    const start = performance.now();
    const isLarge = target >= 10000;

    function update(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
      const value = Math.floor(eased * target);

      el.textContent = isLarge
        ? (value >= 1000 ? (value / 1000).toFixed(0) + 'K' : value.toString())
        : value.toString();

      if (progress < 1) requestAnimationFrame(update);
      else el.textContent = isLarge && target >= 1000
        ? (target / 1000).toFixed(0) + 'K'
        : target.toString();
    }

    requestAnimationFrame(update);
  }

  function initCounters() {
    const counters = document.querySelectorAll('.counter-val[data-target]');
    if (!counters.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.dataset.target, 10);
            animateCounter(el, target, 1800);
            observer.unobserve(el);
          }
        });
      },
      { threshold: 0.5 }
    );

    counters.forEach((c) => observer.observe(c));
  }

  // ── Parallax hero ──────────────────────────────────────────
  function initParallax() {
    const heroImg = document.querySelector('.hero-img');
    if (!heroImg) return;

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    let ticking = false;
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const scrollY = window.scrollY;
          heroImg.style.transform = `translateY(${scrollY * 0.2}px)`;
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  // ── Floating badge visibility ──────────────────────────────
  function initFloatingBadge() {
    const badge = document.querySelector('.hero-badge[aria-hidden]');
    if (!badge) return;
    // Show on desktop only
    if (window.innerWidth >= 1024) {
      badge.style.display = 'block';
    }
  }

  // ── Init all ──────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    initReveal();
    initCounters();
    initParallax();
    initFloatingBadge();
  });

  // Re-run reveal for dynamically shown elements
  window.addEventListener('resize', initFloatingBadge);
})();
