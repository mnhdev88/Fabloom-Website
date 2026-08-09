/**
 * Fabloom – Shop JavaScript
 * Handles cart badge refresh, "Add to Cart" AJAX on product pages,
 * and any shop-specific interactivity.
 */
(function () {
  'use strict';

  /* ── Helpers ──────────────────────────────────────────────────── */

  /**
   * Update all cart-badge elements in the navbar.
   * @param {number} count
   */
  function updateCartBadges(count) {
    document.querySelectorAll('.cart-badge').forEach(function (el) {
      el.textContent = String(count);
      el.style.display = count > 0 ? '' : 'none';
    });

    // Also update text in mobile menu cart link (e.g. "Cart (3)")
    var mobileCartLink = document.querySelector('.mobile-nav-links a[href*="cart"]');
    if (mobileCartLink) {
      mobileCartLink.textContent = count > 0 ? 'Cart (' + count + ')' : 'Cart';
    }

    // The sticky mobile cart carries its count in a .cart-badge span, which
    // the loop above already updated. Writing textContent on the link itself
    // would replace its icon with a bare string.
  }

  /**
   * Show a brief toast-style notification.
   * Falls back to using the existing site-flash mechanism if available.
   * @param {string} message
   * @param {'success'|'error'} type
   */
  function showToast(message, type) {
    type = type || 'success';

    // Remove any existing shop toasts first
    document.querySelectorAll('.shop-toast').forEach(function (el) { el.remove(); });

    var toast = document.createElement('div');
    toast.className = 'site-flash site-flash--' + type + ' shop-toast';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'polite');
    toast.innerHTML =
      (type === 'success'
        ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> '
        : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="1"/></svg> '
      ) +
      message +
      '<button class="site-flash__close" onclick="this.parentElement.remove()" aria-label="Dismiss">' +
      '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';

    document.body.insertBefore(toast, document.body.firstChild);

    // Auto-dismiss after 4 seconds
    setTimeout(function () {
      if (toast.parentNode) {
        toast.style.transition = 'opacity 0.4s';
        toast.style.opacity = '0';
        setTimeout(function () { toast.remove(); }, 420);
      }
    }, 4000);
  }

  /* ── Add-to-cart AJAX (product page / product cards) ─────────── */

  /**
   * Attach AJAX behaviour to all .add-to-cart-form forms.
   * Product pages and cards should use this class on their forms.
   */
  function attachAddToCartForms() {
    document.querySelectorAll('.add-to-cart-form').forEach(function (form) {
      if (form.dataset.shopBound) return;
      form.dataset.shopBound = '1';

      form.addEventListener('submit', function (e) {
        e.preventDefault();

        var fd  = new FormData(form);
        var btn = form.querySelector('[type="submit"]');
        var originalText = btn ? btn.innerHTML : null;

        if (btn) {
          btn.disabled = true;
          btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="animation:spin 0.8s linear infinite"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Adding…';
        }

        fetch(form.action, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-Token': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
          },
          body: fd,
        })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (data.success) {
              updateCartBadges(data.cart_count);
              showToast(data.message || 'Added to cart!', 'success');
              if (btn) {
                btn.innerHTML =
                  '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> Added!';
                setTimeout(function () {
                  btn.disabled   = false;
                  btn.innerHTML  = originalText;
                }, 1800);
              }
            } else {
              showToast(data.message || 'Could not add item.', 'error');
              if (btn) {
                btn.disabled  = false;
                btn.innerHTML = originalText;
              }
            }
          })
          .catch(function () {
            // Network error – fall back to normal form submit
            form.submit();
          });
      });
    });
  }

  /* ── Qty stepper buttons ──────────────────────────────────────── */

  /**
   * Wire up [data-qty-minus] / [data-qty-plus] buttons adjacent to
   * an input[name="qty"] inside .qty-stepper wrappers.
   */
  function attachQtySteppers() {
    document.querySelectorAll('.qty-stepper').forEach(function (wrapper) {
      if (wrapper.dataset.stepperBound) return;
      wrapper.dataset.stepperBound = '1';

      var input = wrapper.querySelector('input[type="number"]');
      if (!input) return;

      wrapper.querySelector('[data-qty-minus]')?.addEventListener('click', function () {
        var v = parseInt(input.value, 10) || 1;
        if (v > 1) { input.value = v - 1; }
      });

      wrapper.querySelector('[data-qty-plus]')?.addEventListener('click', function () {
        var v = parseInt(input.value, 10) || 1;
        var max = parseInt(input.max, 10) || 999;
        if (v < max) { input.value = v + 1; }
      });
    });
  }

  /* ── Initialise ───────────────────────────────────────────────── */

  function init() {
    attachAddToCartForms();
    attachQtySteppers();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Spin keyframe (for loading spinner in button)
  if (!document.getElementById('shop-spin-style')) {
    var style = document.createElement('style');
    style.id  = 'shop-spin-style';
    style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(style);
  }

})();
