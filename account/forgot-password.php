<?php
/**
 * Fabloom – Forgot Password
 *
 * Step 1 of the reset flow: take an email address, and if it belongs to an
 * account, mail a single-use link. Step 2 is account/reset-password.php.
 *
 * The response is deliberately identical whether or not the address is
 * registered. Saying "no account with that email" turns this form into a
 * membership oracle: anyone could test a list of addresses and learn which of
 * your customers hold accounts.
 */
declare(strict_types=1);
require_once __DIR__ . '/../includes/password-reset.php';

if (is_logged_in()) {
    header('Location: ' . SITE_URL . '/account/dashboard');
    exit;
}

$errors = [];
$sent   = false;
$form   = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        $errors[] = 'Security token mismatch. Please refresh and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $form['email'] = $email;

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } else {
            $st = db()->prepare('SELECT id, name, email FROM users WHERE email = ? LIMIT 1');
            $st->execute([$email]);
            $user = $st->fetch();

            // Same outcome either way — see the note at the top of this file.
            $sent = true;

            if ($user) {
                if (pw_reset_rate_limited((int) $user['id'])) {
                    // Still no distinct message to the visitor; the throttle is
                    // recorded for us, not announced to them.
                    error_log('Password reset throttled for user ' . $user['id']);
                } else {
                    $token = pw_reset_issue((int) $user['id']);
                    [$ok, $err] = pw_reset_send_mail($user, $token);
                    if (!$ok) {
                        error_log('Password reset mail failed: ' . $err);
                    }
                }
            }
        }
    }
}

$page_title   = 'Reset Your Password | Fabloom';
$page_desc    = 'Request a secure link to reset the password on your Fabloom account.';
$page_robots  = 'noindex, follow';
$page_extra_css = ['css/auth.css'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ── Page Hero ──────────────────────────────────────────────────────────── -->
<section class="page-hero" aria-labelledby="forgot-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">My Account</span>
    <h1 class="page-hero__title" id="forgot-hero-title">Reset Your Password</h1>
    <p class="page-hero__subtitle">We will email you a secure link to set a new one</p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <a href="<?= SITE_URL ?>/account/login">Login</a>
      <span class="sep" aria-hidden="true">›</span>
      <span class="current" aria-current="page">Reset Password</span>
    </nav>
  </div>
</section>

<section class="section section--sm" aria-label="Password reset request">
  <div class="container">
    <div class="auth-layout auth-layout--single">

      <div class="auth-card">
        <div class="auth-card__header">
          <h2>Forgot Your Password?</h2>
          <p>Enter the email address on your account and we will send you a link to choose a new password.</p>
        </div>

        <?php if ($sent): ?>
          <div class="form-alert form-alert--success" role="status" aria-live="polite">
            <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <div class="form-alert__content">
              <p><strong>Check your inbox.</strong> If an account exists for
                 <?= h($form['email']) ?>, a reset link is on its way. It is valid for
                 <?= PW_RESET_TTL_MINUTES ?> minutes and can be used once.</p>
              <p style="margin-top:.5rem;font-size:.9em">Nothing after a few minutes? Check
                 your spam folder, or <a href="<?= SITE_URL ?>/account/forgot-password" class="auth-link">try again</a>.</p>
            </div>
          </div>

          <div class="auth-card__footer">
            <p><a href="<?= SITE_URL ?>/account/login" class="auth-link">Back to sign in</a></p>
          </div>

        <?php else: ?>

          <?php if (!empty($errors)): ?>
          <div class="form-alert form-alert--error" role="alert" aria-live="assertive">
            <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="form-alert__content"><p><?= h($errors[0]) ?></p></div>
          </div>
          <?php endif; ?>

          <form method="POST" action="<?= h(SITE_URL . '/account/forgot-password') ?>" novalidate aria-label="Password reset request">
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
                <input type="email" id="email" name="email"
                       class="form-control form-control--icon"
                       value="<?= h($form['email']) ?>"
                       required autocomplete="email" maxlength="150"
                       placeholder="you@example.com" aria-required="true" autofocus>
              </div>
            </div>

            <button type="submit" class="btn btn-primary auth-submit-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              Send Reset Link
            </button>
          </form>

          <div class="auth-card__footer">
            <p>Remembered it?
              <a href="<?= SITE_URL ?>/account/login" class="auth-link">Back to sign in</a>
            </p>
            <p style="margin-top:var(--sp-2)">No account yet?
              <a href="<?= SITE_URL ?>/account/register" class="auth-link">Create one</a>
            </p>
          </div>

        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
