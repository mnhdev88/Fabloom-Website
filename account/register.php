<?php
/**
 * Fabloom – Account Registration
 */
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

// Redirect logged-in users
if (is_logged_in()) {
    header('Location: ' . SITE_URL . '/account/dashboard');
    exit;
}

$errors = [];
$form   = ['name' => '', 'email' => '', 'phone' => ''];

// ── Handle POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        $errors['general'] = 'Security token mismatch. Please refresh and try again.';
    } else {
        $form = [
            'name'  => trim($_POST['name']  ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
        ];
        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        // ── Validation ────────────────────────────────────────────────
        if ($form['name'] === '') {
            $errors['name'] = 'Full name is required.';
        } elseif (mb_strlen($form['name']) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        } elseif (mb_strlen($form['name']) > 100) {
            $errors['name'] = 'Name must not exceed 100 characters.';
        }

        if ($form['email'] === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (mb_strlen($form['email']) > 150) {
            $errors['email'] = 'Email address is too long.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (mb_strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        } elseif (mb_strlen($password) > 255) {
            $errors['password'] = 'Password is too long.';
        }

        if ($password2 === '') {
            $errors['password2'] = 'Please confirm your password.';
        } elseif ($password !== $password2) {
            $errors['password2'] = 'Passwords do not match.';
        }

        if ($form['phone'] !== '') {
            if (!preg_match('/^[\+\d\s\-\(\)]{7,20}$/', $form['phone'])) {
                $errors['phone'] = 'Please enter a valid phone number.';
            }
        }

        // ── Check if email already registered ─────────────────────────
        if (empty($errors['email'])) {
            $chk = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $chk->execute([$form['email']]);
            if ($chk->fetch()) {
                $errors['email'] = 'An account with this email already exists. Please login instead.';
            }
        }

        // ── Insert user ───────────────────────────────────────────────
        if (empty($errors)) {
            try {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = db()->prepare(
                    'INSERT INTO users (name, email, password, phone, is_admin, created_at)
                     VALUES (:name, :email, :password, :phone, 0, NOW())'
                );
                $stmt->execute([
                    ':name'     => $form['name'],
                    ':email'    => $form['email'],
                    ':password' => $hash,
                    ':phone'    => $form['phone'] !== '' ? $form['phone'] : null,
                ]);

                $new_id = (int) db()->lastInsertId();

                // Auto-login
                session_regenerate_id(true);
                $_SESSION['user_id'] = $new_id;

                flash('success', 'Welcome to Fabloom, ' . $form['name'] . '! Your account has been created successfully.');
                header('Location: ' . SITE_URL . '/account/dashboard');
                exit;

            } catch (\PDOException $e) {
                error_log('Register failed: ' . $e->getMessage());
                $errors['general'] = 'Registration failed due to a server error. Please try again later.';
            }
        }
    }
}

$page_title = 'Create Account – Fabloom';
$page_desc  = 'Create a free Fabloom account to track orders, save addresses and shop premium silk & linen fabrics.';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ── Page Hero ─────────────────────────────────────────────────────────── -->
<section class="page-hero" aria-labelledby="register-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">Join Fabloom</span>
    <h1 class="page-hero__title" id="register-hero-title">Create Your Account</h1>
    <p class="page-hero__subtitle">Free membership — start shopping premium fabrics today</p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <a href="<?= SITE_URL ?>/account/login">Login</a>
      <span class="sep" aria-hidden="true">›</span>
      <span class="current" aria-current="page">Register</span>
    </nav>
  </div>
</section>

<!-- ── Register Section ───────────────────────────────────────────────────── -->
<section class="section section--sm" aria-label="Registration form">
  <div class="container">
    <div class="auth-layout auth-layout--wide">

      <!-- Decorative Side Panel -->
      <div class="auth-panel" aria-hidden="true">
        <div class="auth-panel__inner">
          <div class="auth-panel__icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <line x1="19" y1="8" x2="19" y2="14"/>
              <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
          </div>
          <h2>Join<br>Our Community</h2>
          <p>Become a member and unlock the full Fabloom experience — from exclusive collections to personalised order management.</p>
          <ul class="auth-panel__benefits">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Free to join, no hidden fees
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Track every order live
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Early access to new arrivals
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Dedicated support team
            </li>
          </ul>
        </div>
      </div>

      <!-- Registration Card -->
      <div class="auth-card">
        <div class="auth-card__header">
          <h2>Create Account</h2>
          <p>Fill in your details below to get started</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
        <div class="form-alert form-alert--error" role="alert" aria-live="assertive">
          <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <div class="form-alert__content">
            <p><?= h($errors['general']) ?></p>
          </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= h(SITE_URL . '/account/register') ?>" novalidate aria-label="Registration form" id="register-form">
          <?= csrf_field() ?>

          <!-- Full Name -->
          <div class="auth-form-body">
            <div class="form-group <?= isset($errors['name']) ? 'form-group--error' : '' ?>">
              <label for="reg_name" class="form-label">
                Full Name <span class="required" aria-hidden="true">*</span>
              </label>
              <div class="form-input-wrap">
                <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                <input
                  type="text"
                  id="reg_name"
                  name="name"
                  class="form-control form-control--icon <?= isset($errors['name']) ? 'error' : '' ?>"
                  value="<?= h($form['name']) ?>"
                  required
                  autocomplete="name"
                  maxlength="100"
                  placeholder="Your full name"
                  aria-required="true"
                  <?= isset($errors['name']) ? 'aria-describedby="err-name"' : '' ?>>
              </div>
              <?php if (isset($errors['name'])): ?>
              <p id="err-name" class="field-error" role="alert"><?= h($errors['name']) ?></p>
              <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group <?= isset($errors['email']) ? 'form-group--error' : '' ?>">
              <label for="reg_email" class="form-label">
                Email Address <span class="required" aria-hidden="true">*</span>
              </label>
              <div class="form-input-wrap">
                <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
                <input
                  type="email"
                  id="reg_email"
                  name="email"
                  class="form-control form-control--icon <?= isset($errors['email']) ? 'error' : '' ?>"
                  value="<?= h($form['email']) ?>"
                  required
                  autocomplete="email"
                  maxlength="150"
                  placeholder="you@example.com"
                  aria-required="true"
                  <?= isset($errors['email']) ? 'aria-describedby="err-email"' : '' ?>>
              </div>
              <?php if (isset($errors['email'])): ?>
              <p id="err-email" class="field-error" role="alert"><?= h($errors['email']) ?></p>
              <?php endif; ?>
            </div>

            <!-- Phone (optional) -->
            <div class="form-group <?= isset($errors['phone']) ? 'form-group--error' : '' ?>">
              <label for="reg_phone" class="form-label">
                Phone Number
                <span style="font-weight:400;color:var(--clr-warm-gray)">(optional)</span>
              </label>
              <div class="form-input-wrap">
                <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.28-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                </svg>
                <input
                  type="tel"
                  id="reg_phone"
                  name="phone"
                  class="form-control form-control--icon <?= isset($errors['phone']) ? 'error' : '' ?>"
                  value="<?= h($form['phone']) ?>"
                  autocomplete="tel"
                  maxlength="20"
                  placeholder="+91 00000 00000"
                  <?= isset($errors['phone']) ? 'aria-describedby="err-phone"' : '' ?>>
              </div>
              <?php if (isset($errors['phone'])): ?>
              <p id="err-phone" class="field-error" role="alert"><?= h($errors['phone']) ?></p>
              <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="form-group <?= isset($errors['password']) ? 'form-group--error' : '' ?>">
              <label for="reg_password" class="form-label">
                Password <span class="required" aria-hidden="true">*</span>
              </label>
              <div class="form-input-wrap">
                <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                <input
                  type="password"
                  id="reg_password"
                  name="password"
                  class="form-control form-control--icon <?= isset($errors['password']) ? 'error' : '' ?>"
                  required
                  autocomplete="new-password"
                  minlength="8"
                  maxlength="255"
                  placeholder="Minimum 8 characters"
                  aria-required="true"
                  <?= isset($errors['password']) ? 'aria-describedby="err-password"' : '' ?>>
                <button type="button" class="form-password-toggle" aria-label="Show password" onclick="togglePassword(this)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
              <?php if (isset($errors['password'])): ?>
              <p id="err-password" class="field-error" role="alert"><?= h($errors['password']) ?></p>
              <?php else: ?>
              <p class="field-hint">Must be at least 8 characters long.</p>
              <?php endif; ?>
              <!-- Password Strength Bar -->
              <div class="pwd-strength" id="pwd-strength" aria-live="polite" aria-label="Password strength">
                <div class="pwd-strength__bar"><div class="pwd-strength__fill" id="pwd-fill"></div></div>
                <span class="pwd-strength__label" id="pwd-label"></span>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group <?= isset($errors['password2']) ? 'form-group--error' : '' ?>">
              <label for="reg_password2" class="form-label">
                Confirm Password <span class="required" aria-hidden="true">*</span>
              </label>
              <div class="form-input-wrap">
                <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <input
                  type="password"
                  id="reg_password2"
                  name="password2"
                  class="form-control form-control--icon <?= isset($errors['password2']) ? 'error' : '' ?>"
                  required
                  autocomplete="new-password"
                  maxlength="255"
                  placeholder="Re-enter your password"
                  aria-required="true"
                  <?= isset($errors['password2']) ? 'aria-describedby="err-password2"' : '' ?>>
                <button type="button" class="form-password-toggle" aria-label="Show confirm password" onclick="togglePassword(this)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
              <?php if (isset($errors['password2'])): ?>
              <p id="err-password2" class="field-error" role="alert"><?= h($errors['password2']) ?></p>
              <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary auth-submit-btn" id="register-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/>
                <line x1="22" y1="11" x2="16" y2="11"/>
              </svg>
              Create My Account
            </button>

            <p class="auth-terms">
              By registering you agree to our
              <a href="<?= SITE_URL ?>/terms-and-conditions" style="color:var(--clr-red)">Terms &amp; Conditions</a>
              and
              <a href="<?= SITE_URL ?>/privacy-policy" style="color:var(--clr-red)">Privacy Policy</a>.
            </p>
          </div><!-- /.auth-form-body -->
        </form>

        <div class="auth-card__footer">
          <p>Already have an account?
            <a href="<?= SITE_URL ?>/account/login" class="auth-link">Sign In</a>
          </p>
          <a href="<?= SITE_URL ?>/products" class="auth-link auth-link--muted" style="margin-top:var(--sp-2);display:inline-block">
            ← Continue Shopping
          </a>
        </div>
      </div><!-- /.auth-card -->

    </div><!-- /.auth-layout -->
  </div>
</section>

<style>
/* ── Auth Layout ── */
.auth-layout {
  display: grid;
  grid-template-columns: 1fr 520px;
  gap: var(--sp-8);
  align-items: start;
  max-width: 1020px;
  margin-inline: auto;
}

.auth-layout--wide { grid-template-columns: 1fr 560px; }

@media (max-width: 840px) {
  .auth-layout,
  .auth-layout--wide { grid-template-columns: 1fr; }
  .auth-panel { display: none; }
}

/* ── Panel ── */
.auth-panel {
  border-radius: var(--radius-xl);
  background: linear-gradient(145deg, var(--clr-charcoal) 0%, #2a0e0f 60%, var(--clr-red-deep) 100%);
  padding: var(--sp-10) var(--sp-8);
  position: relative;
  overflow: hidden;
  min-height: 400px;
  display: flex;
  align-items: center;
}

.auth-panel::before {
  content: '';
  position: absolute;
  top: -80px; right: -80px;
  width: 300px; height: 300px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(192,40,42,0.25) 0%, transparent 70%);
}

.auth-panel::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -60px;
  width: 250px; height: 250px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(184,150,46,0.12) 0%, transparent 70%);
}

.auth-panel__inner { position: relative; z-index: 1; }

.auth-panel__icon {
  width: 80px; height: 80px;
  background: rgba(192,40,42,0.2);
  border: 1px solid rgba(192,40,42,0.35);
  border-radius: var(--radius-full);
  display: flex; align-items: center; justify-content: center;
  color: var(--clr-red-light);
  margin-bottom: var(--sp-6);
}

.auth-panel__inner h2 {
  font-family: var(--font-serif);
  font-size: var(--text-3xl);
  color: var(--clr-white);
  line-height: 1.2;
  margin-bottom: var(--sp-4);
}

.auth-panel__inner p {
  font-size: var(--text-sm);
  color: rgba(255,255,255,0.6);
  line-height: 1.75;
  margin-bottom: var(--sp-6);
}

.auth-panel__benefits { display: flex; flex-direction: column; gap: var(--sp-3); }

.auth-panel__benefits li {
  display: flex; align-items: center; gap: var(--sp-3);
  font-size: var(--text-sm); color: rgba(255,255,255,0.8);
}

.auth-panel__benefits li svg { color: var(--clr-red-light); flex-shrink: 0; }

/* ── Auth Card ── */
.auth-card {
  background: var(--clr-white);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--clr-border);
  overflow: hidden;
}

.auth-card__header {
  background: linear-gradient(135deg, var(--clr-cream) 0%, var(--clr-ivory) 100%);
  padding: var(--sp-8) var(--sp-8) var(--sp-6);
  border-bottom: 1px solid var(--clr-border);
}

.auth-card__header h2 {
  font-family: var(--font-serif);
  font-size: var(--text-2xl);
  color: var(--clr-charcoal);
  margin-bottom: var(--sp-1);
}

.auth-card__header p { font-size: var(--text-sm); color: var(--clr-text-muted); }

.auth-form-body {
  padding: var(--sp-6) var(--sp-8) var(--sp-6);
  display: flex;
  flex-direction: column;
  gap: var(--sp-4);
}

.auth-submit-btn { width: 100%; justify-content: center; margin-top: var(--sp-2); }

.auth-card__footer {
  padding: var(--sp-5) var(--sp-8) var(--sp-8);
  border-top: 1px solid var(--clr-border);
  background: var(--clr-cream);
  text-align: center;
  font-size: var(--text-sm);
  color: var(--clr-text-muted);
}

.auth-link { color: var(--clr-red); font-weight: 600; transition: color var(--dur-fast); }
.auth-link:hover { color: var(--clr-red-dark); }
.auth-link--muted { color: var(--clr-text-muted); font-weight: 400; }
.auth-link--muted:hover { color: var(--clr-red); }

.auth-terms {
  text-align: center;
  font-size: var(--text-xs);
  color: var(--clr-text-muted);
  margin-top: var(--sp-3);
}

/* Form helpers */
.form-input-wrap { position: relative; }

.form-input-icon {
  position: absolute; left: 1rem; top: 50%;
  transform: translateY(-50%);
  color: var(--clr-text-muted);
  pointer-events: none;
}

.form-control--icon { padding-left: 2.75rem; }

.form-password-toggle {
  position: absolute; right: 1rem; top: 50%;
  transform: translateY(-50%);
  color: var(--clr-text-muted);
  display: flex; align-items: center;
  padding: 0.25rem;
  border-radius: var(--radius-sm);
  transition: color var(--dur-fast);
}

.form-password-toggle:hover { color: var(--clr-red); }

.field-error {
  font-size: var(--text-xs);
  color: #C0392B;
  margin-top: var(--sp-1);
  display: flex;
  align-items: center;
  gap: 4px;
}

.field-error::before {
  content: '!';
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 14px; height: 14px;
  background: #C0392B;
  color: white;
  border-radius: 50%;
  font-size: 10px;
  font-weight: 700;
  flex-shrink: 0;
}

.field-hint {
  font-size: var(--text-xs);
  color: var(--clr-text-muted);
  margin-top: var(--sp-1);
}

.form-group--error .form-control { border-color: #C0392B; }
.form-group--error .form-control:focus { box-shadow: 0 0 0 3px rgba(192,57,43,0.15); }

/* Alert */
.form-alert {
  display: flex; gap: var(--sp-3);
  padding: var(--sp-4) var(--sp-5);
  border-radius: var(--radius-md);
  margin: var(--sp-4) var(--sp-8);
  font-size: var(--text-sm);
}

.form-alert--error { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
.form-alert__icon { width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px; }
.form-alert__content p { line-height: 1.5; }

/* Password Strength */
.pwd-strength {
  display: flex;
  align-items: center;
  gap: var(--sp-3);
  margin-top: var(--sp-2);
  opacity: 0;
  transition: opacity var(--dur-fast);
}

.pwd-strength.visible { opacity: 1; }

.pwd-strength__bar {
  flex: 1;
  height: 4px;
  background: var(--clr-border);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.pwd-strength__fill {
  height: 100%;
  border-radius: var(--radius-full);
  transition: width 0.3s var(--ease-out), background 0.3s;
  width: 0%;
}

.pwd-strength__label {
  font-size: var(--text-xs);
  font-weight: 600;
  white-space: nowrap;
  color: var(--clr-text-muted);
}
</style>

<script>
function togglePassword(btn) {
  var input = btn.closest('.form-input-wrap').querySelector('input[type="password"], input[type="text"]');
  if (!input) return;
  var isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
}

// Password strength
(function() {
  var pwdInput = document.getElementById('reg_password');
  var fill     = document.getElementById('pwd-fill');
  var label    = document.getElementById('pwd-label');
  var bar      = document.getElementById('pwd-strength');

  if (!pwdInput) return;

  pwdInput.addEventListener('input', function() {
    var val = this.value;
    if (!val) { bar.classList.remove('visible'); return; }
    bar.classList.add('visible');

    var score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    var configs = [
      { pct: '20%', color: '#C0392B', text: 'Very Weak' },
      { pct: '40%', color: '#E67E22', text: 'Weak' },
      { pct: '60%', color: '#F1C40F', text: 'Fair' },
      { pct: '80%', color: '#27AE60', text: 'Strong' },
      { pct: '100%', color: '#1E8449', text: 'Very Strong' },
    ];
    var cfg = configs[Math.min(score, 4)];
    fill.style.width    = cfg.pct;
    fill.style.background = cfg.color;
    label.textContent   = cfg.text;
    label.style.color   = cfg.color;
  });
})();

// Prevent double-submit
document.getElementById('register-btn').addEventListener('click', function() {
  var form = document.getElementById('register-form');
  if (form.checkValidity()) {
    this.disabled = true;
    this.textContent = 'Creating Account…';
    this.style.opacity = '0.7';
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
