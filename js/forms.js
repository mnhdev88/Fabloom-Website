/* ============================================================
   FABLOOM — Form Validation & Submission
   ============================================================ */

(function () {
  'use strict';

  // ── Validation helpers ────────────────────────────────────
  const validators = {
    required: (val) => val.trim() !== '',
    email: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim()),
    phone: (val) => /^[+\d\s\-()]{7,15}$/.test(val.trim()),
    minLength: (val, len) => val.trim().length >= len,
  };

  function showError(input, errorEl, message) {
    input.classList.add('error');
    input.setAttribute('aria-invalid', 'true');
    if (errorEl) {
      errorEl.textContent = message;
      errorEl.classList.add('visible');
    }
  }

  function clearError(input, errorEl) {
    input.classList.remove('error');
    input.setAttribute('aria-invalid', 'false');
    if (errorEl) errorEl.classList.remove('visible');
  }

  // Error spans are named err-<field>, but some pages prefix the input id
  // (inq-name, s-phone…) and others don't. Try the id verbatim first, then
  // the de-prefixed form, so both conventions resolve.
  function errorIdFor(input) {
    const id = input.id || '';
    if (document.getElementById('err-' + id)) return 'err-' + id;
    const stripped = id.replace(/^(inq|linen|silk|contact|s)-/, '');
    return 'err-' + stripped;
  }

  function validateField(input) {
    const errorEl = document.getElementById(errorIdFor(input));
    const val = input.value;

    if (input.required && !validators.required(val)) {
      showError(input, errorEl, input.dataset.errorRequired || 'This field is required.');
      return false;
    }
    if (input.type === 'email' && val && !validators.email(val)) {
      showError(input, errorEl, 'Please enter a valid email address.');
      return false;
    }
    if (input.type === 'tel' && val && !validators.phone(val)) {
      showError(input, errorEl, 'Please enter a valid phone number.');
      return false;
    }

    clearError(input, errorEl);
    return true;
  }

  // ── Validate full form ────────────────────────────────────
  function validateForm(form) {
    const fields = form.querySelectorAll('input[required], select[required], textarea[required]');
    let valid = true;
    let firstInvalid = null;

    fields.forEach((field) => {
      if (!validateField(field)) {
        valid = false;
        if (!firstInvalid) firstInvalid = field;
      }
    });

    if (firstInvalid) firstInvalid.focus();
    return valid;
  }

  // ── Blur validation (after user leaves field) ─────────────
  function attachBlurValidation(form) {
    form.querySelectorAll('input, select, textarea').forEach((input) => {
      input.addEventListener('blur', () => {
        if (input.required || input.type === 'email' || input.type === 'tel') {
          validateField(input);
        }
      });
    });
  }

  // ── Real submission ───────────────────────────────────────
  // Posts to api/enquiry.php, which stores the lead and emails it to
  // every address in MAIL_TO (info@thefabloom.com, fabloom86@gmail.com).
  const ENDPOINT = 'api/enquiry.php';

  function submitForm(form, submitBtn, successEl) {
    const originalContent = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.setAttribute('aria-busy', 'true');
    submitBtn.innerHTML = '<span class="spinner" aria-hidden="true"></span> Sending…';

    const data = new FormData(form);
    data.set('form_type', form.dataset.enquiryForm);
    data.set('page_url', window.location.href);
    data.set('started_at', form.dataset.startedAt || '0');

    function restore() {
      submitBtn.disabled = false;
      submitBtn.removeAttribute('aria-busy');
      submitBtn.innerHTML = originalContent;
    }

    fetch(ENDPOINT, {
      method: 'POST',
      body: data,
      headers: { Accept: 'application/json' },
credentials: 'same-origin',
    })
      .then((r) => r.json().then((body) => ({ ok: r.ok, body })))
      .then(({ ok, body }) => {
        if (!ok || !body.success) {
          restore();
          // Re-mark any fields the server rejected
          if (body.errors) {
            Object.keys(body.errors).forEach((field) => {
              const input = form.querySelector('[name="' + field + '"]');
              if (input) {
                showError(input, document.getElementById(errorIdFor(input)), body.errors[field]);
              }
            });
            const first = form.querySelector('.error');
            if (first) first.focus();
          }
          showToast(body.message || 'Something went wrong. Please try again.', 'error');
          return;
        }

        form.reset();
        if (successEl) {
          form.querySelectorAll('.form-group, .form-row, .form-full').forEach((el) => {
            el.style.display = 'none';
          });
          submitBtn.style.display = 'none';
          successEl.style.display = 'block';
          successEl.setAttribute('tabindex', '-1');
          successEl.focus();
        } else {
          restore();
          showToast(body.message, 'success');
        }
      })
      .catch(() => {
        restore();
        showToast(
          'We could not reach the server. Please email info@thefabloom.com or call +91 97600 58796.',
          'error'
        );
      });
  }

  // ── Toast notification ────────────────────────────────────
  function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'polite');

    const icon = type === 'success'
      ? `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#27AE60" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>`
      : `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C0392B" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;

    toast.innerHTML = `${icon}<span>${message}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'toast-out 0.4s ease both';
      setTimeout(() => toast.remove(), 400);
    }, 4000);
  }

  // ── Wire up every enquiry form ────────────────────────────
  // Forms opt in with data-enquiry-form="<type>" rather than being matched
  // by id — the previous per-id lookups had drifted out of sync with the
  // markup, leaving three forms with no handler at all.
  function initEnquiryForms() {
    document.querySelectorAll('[data-enquiry-form]').forEach((form) => {
      attachBlurValidation(form);

      // Timestamp + honeypot for the server's spam checks
      form.dataset.startedAt = String(Date.now());
      if (!form.querySelector('[name="website"]')) {
        const trap = document.createElement('div');
        trap.setAttribute('aria-hidden', 'true');
        trap.style.cssText =
          'position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden';
        trap.innerHTML =
          '<label>Leave this field empty' +
          '<input type="text" name="website" tabindex="-1" autocomplete="off"></label>';
        form.appendChild(trap);
      }

      form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!validateForm(form)) return;

        const submitBtn =
          form.querySelector('[type="submit"]') ||
          document.getElementById(form.dataset.submitId);
        if (!submitBtn) return;

        const successEl = form.dataset.successId
          ? document.getElementById(form.dataset.successId)
          : null;

        submitForm(form, submitBtn, successEl);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', initEnquiryForms);

  // Export showToast for use in other scripts
  window.FabloomForms = { showToast };
})();
