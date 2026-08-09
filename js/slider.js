/* ============================================================
   FABLOOM — Carousel / Slider
   ============================================================ */

(function () {
  'use strict';

  // ── Generic Carousel Factory ──────────────────────────────
  function createCarousel(options) {
    const {
      trackId,
      prevBtnId,
      nextBtnId,
      dotsSelector,
      slideSelector,
      slidesPerView = 3,
      autoplayDelay = 5000,
    } = options;

    const track = document.getElementById(trackId);
    if (!track) return;

    const slides = track.querySelectorAll(slideSelector || '.carousel-slide');
    if (slides.length <= 1) return;

    const prevBtn = document.getElementById(prevBtnId);
    const nextBtn = document.getElementById(nextBtnId);
    const dots = dotsSelector ? document.querySelectorAll(dotsSelector) : [];

    let current = 0;
    let autoplay;
    const total = slides.length;

    // Calculate slide width dynamically
    function getSlideWidth() {
      const container = track.parentElement;
      const gap = 24; // 1.5rem gap
      const perView = window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : slidesPerView;
      return (container.offsetWidth - (gap * (perView - 1))) / perView;
    }

    function setSlideWidths() {
      const w = getSlideWidth();
      slides.forEach((s) => {
        s.style.minWidth = w + 'px';
        s.style.width = w + 'px';
        s.style.flexShrink = '0';
      });
    }

    function goTo(index) {
      const perView = window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : slidesPerView;
      const maxIndex = Math.max(0, total - perView);
      current = Math.max(0, Math.min(index, maxIndex));

      const slideW = getSlideWidth();
      const gap = 24;
      const offset = current * (slideW + gap);
      track.style.transform = `translateX(-${offset}px)`;

      // Update dots
      const dotIndex = Math.floor(current * dots.length / (maxIndex + 1));
      dots.forEach((d, i) => {
        d.classList.toggle('active', i === Math.min(dotIndex, dots.length - 1));
        d.setAttribute('aria-selected', i === Math.min(dotIndex, dots.length - 1) ? 'true' : 'false');
      });
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    nextBtn && nextBtn.addEventListener('click', () => { next(); resetAutoplay(); });
    prevBtn && prevBtn.addEventListener('click', () => { prev(); resetAutoplay(); });

    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => {
        const perView = window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : slidesPerView;
        const maxIndex = Math.max(0, total - perView);
        const targetIndex = Math.floor(i * (maxIndex + 1) / dots.length);
        goTo(targetIndex);
        resetAutoplay();
      });
    });

    // Touch/swipe support
    let startX = 0;
    let isDragging = false;

    track.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      isDragging = true;
    }, { passive: true });

    track.addEventListener('touchend', (e) => {
      if (!isDragging) return;
      const deltaX = e.changedTouches[0].clientX - startX;
      if (Math.abs(deltaX) > 50) {
        deltaX < 0 ? next() : prev();
        resetAutoplay();
      }
      isDragging = false;
    }, { passive: true });

    // Autoplay
    function startAutoplay() {
      if (!autoplayDelay) return;
      autoplay = setInterval(() => {
        const perView = window.innerWidth < 768 ? 1 : window.innerWidth < 1024 ? 2 : slidesPerView;
        const maxIndex = Math.max(0, total - perView);
        if (current >= maxIndex) goTo(0); else next();
      }, autoplayDelay);
    }

    function resetAutoplay() {
      clearInterval(autoplay);
      startAutoplay();
    }

    // Pause on hover
    track.closest('.carousel-wrap')?.addEventListener('mouseenter', () => clearInterval(autoplay));
    track.closest('.carousel-wrap')?.addEventListener('mouseleave', () => startAutoplay());

    // Resize handler
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        setSlideWidths();
        goTo(current);
      }, 200);
    });

    // Init
    setSlideWidths();
    goTo(0);
    startAutoplay();
  }

  // ── Reviews Carousel ──────────────────────────────────────
  function initReviewsCarousel() {
    createCarousel({
      trackId: 'reviews-track',
      prevBtnId: 'rev-prev',
      nextBtnId: 'rev-next',
      dotsSelector: '#reviews-carousel .carousel-dot',
      slidesPerView: 3,
      autoplayDelay: 6000,
    });
  }

  // ── Product gallery carousel (if used on products page) ───
  function initProductCarousel() {
    createCarousel({
      trackId: 'product-track',
      prevBtnId: 'prod-prev',
      nextBtnId: 'prod-next',
      dotsSelector: '#product-carousel .carousel-dot',
      slidesPerView: 3,
      autoplayDelay: 0,
    });
  }

  // ── Init all ──────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    initReviewsCarousel();
    initProductCarousel();
  });
})();
