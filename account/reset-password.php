<?php
/**
 * Fabloom – Set a New Password
 *
 * Step 2 of the reset flow. The account is identified by the token alone, so
 * there is no email or user id in the form for someone to tamper with.
 *
 * The token is validated on GET as well as POST: showing the form for a token
 * that has already expired, only to reject it after the visitor has typed a
 * password twice, is a poor way to tell them.
 */
declare(strict_types=1);
require_once __DIR__ . '/../includes/password-reset.php';

if (is_logged_in()) {
    header('Location: ' . SITE_URL . '/account/dashboard');
    exit;
}

$errors = [];
$done   = false;

$token   = (string) ($_POST['token'] ?? $_GET['token'] ?? '');
$account = pw_reset_lookup($token);

if ($account !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_verify()) {
        $errors[] = 'Security token mismatch. Please refresh and try again.';
    } else {
        $pw  = (string) ($_POST['password'] ?? '');
        $pw2 = (string) ($_POST['password_confirm'] ?? '');

        if (strlen($pw) < 8) {
            $errors[] = 'Your password must be at least 8 characters.';
        } elseif (!preg_match('/[A-Za-z]/', $pw) || !preg_match('/\d/', $pw)) {
            $errors[] = 'Use at least one letter and one number.';
        } elseif ($pw !== $pw2) {
            $errors[] = 'The two passwords do not match.';
        } else {
            pw_reset_complete((int) $account['reset_id'], (int) $account['id'], $pw);
            $done = true;

            // Deliberately not signing them in here. Completing a reset and
            // then typing the new password once is the step that proves it was
            // stored as intended, and it keeps the session boundary clean.
            flash('success', 'Your password has been updated. Please sign in.');
        }
    }
}

$page_title  = 'Set a New Password | Fabloom';
$page_desc   = 'Choose a new password for your Fabloom account.';
$page_robots = 'noindex, nofollow';
$page_extra_css = ['css/auth.css'];
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero" aria-labelledby="reset-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">My Account</span>
    <h1 class="page-hero__title" id="reset-hero-title">Set a New Password</h1>
    <p class="page-hero__subtitle">Choose something you have not used here before</p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <a href="<?= SITE_URL ?>/account/login">Login</a>
      <span class="sep" aria-hidden="true">›</span>
      <span class="current" aria-current="page">New Password</span>
    </nav>
  </div>
</section>

<section class="section section--sm" aria-label="Set a new password">
  <div class="container">
    <div class="auth-layout auth-layout--single">
      <div class="auth-card">

        <?php if ($done): ?>

          <div class="auth-card__header">
            <h2>Password Updated</h2>
            <p>You can now sign in with your new password.</p>
          </div>
          <div class="form-alert form-alert--success" role="status" aria-live="polite">
            <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <div class="form-alert__content">
              <p>The reset link has now been used and will not work again. Any other reset
                 links sent for this account have also stopped working.</p>
            </div>
          </div>
          <a href="<?= SITE_URL ?>/account/login" class="btn btn-primary auth-submit-btn">
            Sign In
          </a>

        <?php elseif ($account === null): ?>

          <div class="auth-card__header">
            <h2>This Link Has Expired</h2>
            <p>Reset links last <?= PW_RESET_TTL_MINUTES ?> minutes and work once.</p>
          </div>
          <div class="form-alert form-alert--error" role="alert">
            <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="form-alert__content">
              <p>This link is no longer valid — it may have expired, already been used,
                 or been replaced by a newer request. Ask for a fresh one and it will
                 arrive within a minute or two.</p>
            </div>
          </div>
          <a href="<?= SITE_URL ?>/account/forgot-password" class="btn btn-primary auth-submit-btn">
            Request a New Link
          </a>
          <div class="auth-card__footer">
            <p><a href="<?= SITE_URL ?>/account/login" class="auth-link">Back to sign in</a></p>
          </div>

        <?php else: ?>

          <div class="auth-card__header">
            <h2>Choose a New Password</h2>
            <p>Setting a new password for <strong><?= h((string) $account['email']) ?></strong></p>
          </div>

          <?php if (!empty($errors)): ?>
          <div class="form-alert form-alert--error" role="alert" aria-live="assertive">
            <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="form-alert__content"><p><?= h($errors[0]) ?></p></div>
          </div>
          <?php endif; ?>

          <form method="POST" action="<?= h(SITE_URL . '/account/reset-password') ?>" novalidate aria-label="Set a new password">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= h($token) ?>">

            <div class="form-group">
              <label for="password" class="form-label">
                New Password <span class="required" aria-hidden="true">*</span>
              </label>
              <div class="form-input-wrap">
                <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                <input type="password" id="password" name="password"
                       class="form-control form-control--icon"
                       required autocomplete="new-password" minlength="8" maxlength="255"
                       placeholder="At least 8 characters" aria-required="true"
                       aria-describedby="pw-hint" autofocus>
                <button type="button" class="form-password-toggle" aria-label="Show password" onclick="togglePassword(this)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
              <p id="pw-hint" class="form-hint">At least 8 characters, including a letter and a number.</p>
            </div>

            <div class="form-group" style="margin-top:var(--sp-4)">
              <label for="password_confirm" class="form-label">
                Repeat New Password <span class="required" aria-hidden="true">*</span>
              </label>
              <div class="form-input-wrap">
                <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                <input type="password" id="password_confirm" name="password_confirm"
                       class="form-control form-control--icon"
                       required autocomplete="new-password" minlength="8" maxlength="255"
                       placeholder="Type it again" aria-required="true">
                <button type="button" class="form-password-toggle" aria-label="Show password" onclick="togglePassword(this)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary auth-submit-btn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              Update Password
            </button>
          </form>

        <?php endif; ?>

      </div>
    </div>
  </div>
</section>

<script>
// togglePassword() is defined inline in login.php and register.php rather than
// in a shared script, so this page has to carry its own copy — without it the
// eye button throws a ReferenceError on click.
function togglePassword(btn) {
  var input = btn.closest('.form-input-wrap').querySelector('input[type="password"], input[type="text"]');
  if (!input) return;
  var isHidden = input.type === 'password';
  input.type = isHidden ? 'text' : 'password';
  btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
