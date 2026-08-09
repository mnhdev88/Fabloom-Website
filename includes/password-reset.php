<?php
/**
 * Password reset — token storage, issuing and verification.
 *
 * Shared by account/forgot-password.php (issues a token and mails the link)
 * and account/reset-password.php (verifies it and sets the new password).
 *
 * Design notes, because each of these is a decision that matters:
 *
 *  - The raw token is never stored. The database holds sha256(token), so a
 *    leaked database backup cannot be replayed into account takeover. The raw
 *    value exists only in the emailed URL.
 *  - Tokens expire in 60 minutes and are single use. Completing a reset
 *    invalidates every other outstanding token for that user, so an older
 *    email cannot be replayed after the account has been recovered.
 *  - Lookup is by token, not by email. A reset URL therefore cannot be pointed
 *    at somebody else's account by editing a query parameter.
 *  - hash_equals() is used for the comparison to keep it timing-safe.
 */

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

/** How long a reset link stays valid. */
const PW_RESET_TTL_MINUTES = 60;

/** Most reset emails a single account may trigger per hour. */
const PW_RESET_MAX_PER_HOUR = 3;

/**
 * Create the table on first use.
 *
 * The site has no migration runner and no shell access, so a new feature that
 * needs a table would otherwise require someone to paste SQL into phpMyAdmin
 * before the page worked at all. IF NOT EXISTS makes this idempotent and it
 * costs one cheap query per reset request — not per page view.
 */
function pw_reset_init(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    db()->exec(
        'CREATE TABLE IF NOT EXISTS password_resets (
            id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id     INT UNSIGNED NOT NULL,
            token_hash  CHAR(64)     NOT NULL,
            expires_at  DATETIME     NOT NULL,
            used_at     DATETIME     DEFAULT NULL,
            created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            request_ip  VARCHAR(45)  DEFAULT NULL,
            UNIQUE KEY uniq_token (token_hash),
            KEY idx_user (user_id),
            CONSTRAINT fk_pwreset_user FOREIGN KEY (user_id)
                REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    $done = true;
}

/** Has this account already asked for too many links this hour? */
function pw_reset_rate_limited(int $user_id): bool
{
    pw_reset_init();
    $st = db()->prepare(
        'SELECT COUNT(*) FROM password_resets
         WHERE user_id = ? AND created_at > (NOW() - INTERVAL 1 HOUR)'
    );
    $st->execute([$user_id]);

    return (int) $st->fetchColumn() >= PW_RESET_MAX_PER_HOUR;
}

/**
 * Issue a token for a user and return the raw value for the emailed link.
 * Only the hash is written to the database.
 */
function pw_reset_issue(int $user_id): string
{
    pw_reset_init();

    // Any earlier link for this account stops working the moment a new one is
    // requested — otherwise every request leaves another live key lying in an
    // inbox.
    $inv = db()->prepare('UPDATE password_resets SET used_at = NOW()
                          WHERE user_id = ? AND used_at IS NULL');
    $inv->execute([$user_id]);

    $token = bin2hex(random_bytes(32));

    $ins = db()->prepare(
        'INSERT INTO password_resets (user_id, token_hash, expires_at, request_ip)
         VALUES (?, ?, (NOW() + INTERVAL ? MINUTE), ?)'
    );
    $ins->execute([
        $user_id,
        hash('sha256', $token),
        PW_RESET_TTL_MINUTES,
        substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
    ]);

    return $token;
}

/**
 * Resolve a raw token to its user, or null when it is unknown, expired or
 * already spent. Returns ['reset_id' => int, 'id' => int, 'name' => ...].
 */
function pw_reset_lookup(string $token): ?array
{
    if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }
    pw_reset_init();

    $st = db()->prepare(
        'SELECT r.id AS reset_id, r.token_hash, u.id, u.name, u.email
         FROM password_resets r
         JOIN users u ON u.id = r.user_id
         WHERE r.token_hash = ? AND r.used_at IS NULL AND r.expires_at > NOW()
         LIMIT 1'
    );
    $st->execute([hash('sha256', $token)]);
    $row = $st->fetch();

    if (!$row) {
        return null;
    }

    // Belt and braces: the WHERE clause already matched on the hash, but the
    // comparison is repeated in constant time so the shape of this function
    // does not depend on the database's own comparison behaviour.
    if (!hash_equals((string) $row['token_hash'], hash('sha256', $token))) {
        return null;
    }
    unset($row['token_hash']);

    return $row;
}

/**
 * Set the new password and burn every outstanding token for the account.
 * Wrapped in a transaction so a failure cannot leave the password changed
 * while the token stays live.
 */
function pw_reset_complete(int $reset_id, int $user_id, string $new_password): void
{
    pw_reset_init();
    $pdo = db();

    $pdo->beginTransaction();
    try {
        $up = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $up->execute([password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]), $user_id]);

        $burn = $pdo->prepare('UPDATE password_resets SET used_at = NOW()
                               WHERE user_id = ? AND used_at IS NULL');
        $burn->execute([$user_id]);

        $pdo->commit();
    } catch (\Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/** The branded email carrying the link. */
function pw_reset_send_mail(array $user, string $token): array
{
    $link = SITE_URL . '/account/reset-password?token=' . $token;

    $inner = '<p>Hello ' . h((string) $user['name']) . ',</p>'
        . '<p>We received a request to reset the password on your Fabloom account. '
        . 'Click the button below to choose a new one. The link is valid for '
        . PW_RESET_TTL_MINUTES . ' minutes and can be used once.</p>'
        . '<p style="margin:24px 0"><a href="' . h($link) . '" '
        . 'style="background:#C0282A;color:#fff;text-decoration:none;padding:12px 22px;'
        . 'border-radius:6px;font-weight:700;display:inline-block">Reset my password</a></p>'
        . '<p style="font-size:12px;color:#57534E">If the button does not work, paste this '
        . 'address into your browser:<br><span style="word-break:break-all">' . h($link) . '</span></p>'
        . '<p>If you did not ask for this, you can ignore this email — your password '
        . 'stays as it is.</p>';

    return send_notification(
        'Reset your Fabloom password',
        mail_wrap('Password reset', $inner, 'Fabloom Group of Company · Bhagalpur, Bihar'),
        '',
        '',
        [],
        (string) $user['email']   // send to the account holder, not the office inbox
    );
}
