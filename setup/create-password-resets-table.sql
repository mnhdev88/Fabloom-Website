-- Password reset tokens.
--
-- includes/password-reset.php creates this table itself with the same
-- definition on first use, so you do not normally need to run this file. It is
-- kept so the schema is reviewable in the repository alongside the other
-- table definitions, and so a DBA setting up a fresh environment by hand has
-- the statement to hand.
--
-- Only the SHA-256 of a token is stored. The raw value exists solely inside
-- the emailed link, so a leaked database backup cannot be replayed into an
-- account takeover.

CREATE TABLE IF NOT EXISTS password_resets (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional housekeeping: spent and expired rows have no value once used. Run
-- occasionally, or from a cron job, if the table ever grows large.
-- DELETE FROM password_resets
--  WHERE used_at IS NOT NULL OR expires_at < (NOW() - INTERVAL 7 DAY);
