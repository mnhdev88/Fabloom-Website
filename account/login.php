<?php
/**
 * Fabloom – Account Login
 */
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

// Redirect logged-in users to dashboard
if (is_logged_in()) {
    header('Location: ' . SITE_URL . '/account/dashboard');
    exit;
}

$errors = [];
$form   = ['email' => '', 'remember' => false];

// ── Handle "Forgot Password" click ─────────────────────────────────────────
if (isset($_GET['forgot'])) {
    flash('info', 'Password reset is not yet available online. Please contact us at info@thefabloom.com and we will help you regain access.');
    header('Location: ' . SITE_URL . '/account/login');
    exit;
}

// ── Handle POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        $errors[] = 'Security token mismatch. Please refresh and try again.';
    } else {
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $remember = !empty($_POST['remember']);

        $form['email']    = $email;
        $form['remember'] = $remember;

        if ($email === '' || $password === '') {
            $errors[] = 'Invalid email or password.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email or password.';
        } else {
            $stmt = db()->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = 'Invalid email or password.';
            } else {
                // Regenerate session to prevent fixation
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];

                if ($remember) {
                    // Extend session cookie lifetime to 30 days
                    $params = session_get_cookie_params();
                    setcookie(
                        session_name(),
                        session_id(),
                        time() + 60 * 60 * 24 * 30,
                        $params['path'],
                        $params['domain'],
                        $params['secure'],
                        $params['httponly']
                    );
                }

                flash('success', 'Welcome back, ' . $user['name'] . '!');

                $redirect = $_SESSION['redirect_after_login'] ?? (SITE_URL . '/account/dashboard');
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
        }
    }
}

$page_title = 'Login – My Account | Fabloom';
$page_desc  = 'Login to your Fabloom account to track orders, manage your profile and shop premium fabrics.';
require_once __DIR__ . '/../includes/header.php';

// Display info flash (forgot password message)
$_flash_info = get_flash('info');
?>

<!-- ── Page Hero ──────────────────────────────────────────────────────────── -->
<section class="page-hero" aria-labelledby="login-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">My Account</span>
    <h1 class="page-hero__title" id="login-hero-title">Welcome Back</h1>
    <p class="page-hero__subtitle">Login to access your orders and account details</p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <span class="current" aria-current="page">Login</span>
    </nav>
  </div>
</section>

<!-- ── Login Section ─────────────────────────────────────────────────────── -->
<section class="section section--sm" aria-label="Login form">
  <div class="container">
    <div class="auth-layout">

      <!-- Decorative side panel -->
      <div class="auth-panel" aria-hidden="true">
        <div class="auth-panel__inner">
          <div class="auth-panel__icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <h2>Fabloom<br>Members</h2>
          <p>Access exclusive fabric collections, track your orders in real time, and enjoy a seamless shopping experience crafted for you.</p>
          <ul class="auth-panel__benefits">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Real-time order tracking
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Saved delivery addresses
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Exclusive member offers
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Priority customer support
            </li>
          </ul>
        </div>
      </div>

      <!-- Login Card -->
      <div class="auth-card">
        <div class="auth-card__header">
          <h2>Sign In</h2>
          <p>Enter your credentials to access your account</p>
        </div>

        <?php if ($_flash_info): ?>
        <div class="form-alert form-alert--info" role="alert" aria-live="polite">
          <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
          <div class="form-alert__content">
            <p><?= h($_flash_info) ?></p>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="form-alert form-alert--error" role="alert" aria-live="assertive">
          <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <div class="form-alert__content">
            <p><?= h($errors[0]) ?></p>
          </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= h(SITE_URL . '/account/login') ?>" novalidate aria-label="Login form" id="login-form">
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="email" class="form-label">
              Email Address <span class="required" aria-hidden="true">*</span>
            </label>
            <div class="form-input-wrap">
              <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              <input
                type="email"
                id="email"
                name="email"
                class="form-control form-control--icon"
                value="<?= h($form['email']) ?>"
                required
                autocomplete="email"
                maxlength="150"
                placeholder="you@example.com"
                aria-required="true">
            </div>
          </div>

          <div class="form-group" style="margin-top:var(--sp-4)">
            <label for="password" class="form-label">
              Password <span class="required" aria-hidden="true">*</span>
            </label>
            <div class="form-input-wrap">
              <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
              </svg>
              <input
                type="password"
                id="password"
                name="password"
                class="form-control form-control--icon"
                required
                autocomplete="current-password"
                maxlength="255"
                placeholder="Your password"
                aria-required="true">
              <button type="button" class="form-password-toggle" aria-label="Show password" onclick="togglePassword(this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="form-row-between" style="margin-top:var(--sp-4)">
            <label class="form-checkbox">
              <input type="checkbox" name="remember" value="1" <?= $form['remember'] ? 'checked' : '' ?>>
              <span class="form-checkbox__mark"></span>
              Remember me
            </label>
            <a href="<?= h(SITE_URL . '/account/login?forgot=1') ?>" class="auth-link auth-link--muted">
              Forgot password?
            </a>
          </div>

          <button type="submit" class="btn btn-primary auth-submit-btn" id="login-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
              <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
              <polyline points="10 17 15 12 10 7"/>
              <line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
            Sign In
          </button>
        </form>

        <div class="auth-card__footer">
          <p>Don't have an account?
            <a href="<?= SITE_URL ?>/account/register" class="auth-link">Create Account</a>
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
  grid-template-columns: 1fr 480px;
  gap: var(--sp-8);
  align-items: start;
  max-width: 960px;
  margin-inline: auto;
}

@media (max-width: 768px) {
  .auth-layout { grid-template-columns: 1fr; }
  .auth-panel  { display: none; }
}

/* ── Decorative Panel ── */
.auth-panel {
  border-radius: var(--radius-xl);
  background: linear-gradient(145deg, var(--clr-charcoal) 0%, #2a0e0f 60%, var(--clr-red-deep) 100%);
  padding: var(--sp-10) var(--sp-8);
  position: relative;
  overflow: hidden;
  min-height: 420px;
  display: flex;
  align-items: center;
}

.auth-panel::before {
  content: '';
  position: absolute;
  top: -80px;
  right: -80px;
  width: 300px;
  height: 300px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(192,40,42,0.25) 0%, transparent 70%);
}

.auth-panel::after {
  content: '';
  position: absolute;
  bottom: -60px;
  left: -60px;
  width: 250px;
  height: 250px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(184,150,46,0.12) 0%, transparent 70%);
}

.auth-panel__inner { position: relative; z-index: 1; }

.auth-panel__icon {
  width: 80px;
  height: 80px;
  background: rgba(192,40,42,0.2);
  border: 1px solid rgba(192,40,42,0.35);
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  justify-content: center;
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

.auth-panel__benefits {
  display: flex;
  flex-direction: column;
  gap: var(--sp-3);
}

.auth-panel__benefits li {
  display: flex;
  align-items: center;
  gap: var(--sp-3);
  font-size: var(--text-sm);
  color: rgba(255,255,255,0.8);
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

.auth-card__header p {
  font-size: var(--text-sm);
  color: var(--clr-text-muted);
}

.auth-card form,
.auth-card .form-alert {
  padding: var(--sp-6) var(--sp-8);
}

.auth-card form { padding-top: var(--sp-6); }

.auth-submit-btn {
  width: 100%;
  justify-content: center;
  margin-top: var(--sp-6);
}

.auth-card__footer {
  padding: var(--sp-5) var(--sp-8) var(--sp-8);
  border-top: 1px solid var(--clr-border);
  background: var(--clr-cream);
  text-align: center;
  font-size: var(--text-sm);
  color: var(--clr-text-muted);
}

.auth-link {
  color: var(--clr-red);
  font-weight: 600;
  transition: color var(--dur-fast);
}

.auth-link:hover { color: var(--clr-red-dark); }
.auth-link--muted { color: var(--clr-text-muted); font-weight: 400; }
.auth-link--muted:hover { color: var(--clr-red); }

/* ── Form Helpers ── */
.form-input-wrap {
  position: relative;
}

.form-input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--clr-text-muted);
  pointer-events: none;
}

.form-control--icon {
  padding-left: 2.75rem;
}

.form-password-toggle {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--clr-text-muted);
  display: flex;
  align-items: center;
  padding: 0.25rem;
  border-radius: var(--radius-sm);
  transition: color var(--dur-fast);
}

.form-password-toggle:hover { color: var(--clr-red); }

.form-row-between {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--sp-4);
}

/* Checkbox */
.form-checkbox {
  display: flex;
  align-items: center;
  gap: var(--sp-2);
  cursor: pointer;
  font-size: var(--text-sm);
  color: var(--clr-text-secondary);
  user-select: none;
}

.form-checkbox input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.form-checkbox__mark {
  width: 18px;
  height: 18px;
  border: 2px solid var(--clr-border-dark);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition-property: color, background-color, border-color, box-shadow, transform, opacity; transition-duration: var(--dur-fast);
}

.form-checkbox input:checked ~ .form-checkbox__mark {
  background: var(--clr-red);
  border-color: var(--clr-red);
}

.form-checkbox__mark::after {
  content: '';
  width: 10px;
  height: 6px;
  border-left: 2px solid white;
  border-bottom: 2px solid white;
  transform: rotate(-45deg) translate(1px, -1px);
  display: none;
}

.form-checkbox input:checked ~ .form-checkbox__mark::after { display: block; }

/* Alert boxes */
.form-alert {
  display: flex;
  gap: var(--sp-3);
  padding: var(--sp-4) var(--sp-5);
  border-radius: var(--radius-md);
  margin: var(--sp-4) var(--sp-8);
  font-size: var(--text-sm);
}

.form-alert--error {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  color: #991B1B;
}

.form-alert--info {
  background: #EFF6FF;
  border: 1px solid #BFDBFE;
  color: #1E40AF;
}

.form-alert__icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  margin-top: 1px;
}

.form-alert__content p { line-height: 1.5; }
</style>

<script>
function togglePassword(btn) {
  var input = btn.closest('.form-input-wrap').querySelector('input[type="password"], input[type="text"]');
  if (!input) return;
  var isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
}

// Prevent double-submit
document.getElementById('login-btn').addEventListener('click', function() {
  var form = document.getElementById('login-form');
  if (form.checkValidity()) {
    this.disabled = true;
    this.textContent = 'Signing In…';
    this.style.opacity = '0.7';
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
