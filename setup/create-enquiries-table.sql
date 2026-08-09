-- ============================================================
--  Fabloom — enquiries table
--
--  Every website form submission (contact, linen, silk, sample,
--  homepage) is written here BEFORE the notification email is
--  attempted, so a mail outage can never lose a lead.
--
--  Run this once against an existing database. A fresh install of
--  setup/install.sql already creates the table.
--
--  Usage:  mysql -u root -p fabloom_db < setup/create-enquiries-table.sql
-- ============================================================

CREATE TABLE IF NOT EXISTS enquiries (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_type   VARCHAR(40)  NOT NULL,          -- contact | linen | silk | sample | home
  name        VARCHAR(150) NOT NULL,
  email       VARCHAR(190) DEFAULT NULL,
  phone       VARCHAR(40)  DEFAULT NULL,
  company     VARCHAR(190) DEFAULT NULL,
  subject     VARCHAR(200) DEFAULT NULL,
  message     TEXT         DEFAULT NULL,
  details     TEXT         DEFAULT NULL,      -- JSON of the form-specific fields
  page_url    VARCHAR(500) DEFAULT NULL,
  ip_address  VARCHAR(45)  DEFAULT NULL,
  user_agent  VARCHAR(255) DEFAULT NULL,
  mail_sent   TINYINT(1)   NOT NULL DEFAULT 0,
  mail_error  VARCHAR(255) DEFAULT NULL,
  is_read     TINYINT(1)   NOT NULL DEFAULT 0,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_created (created_at),
  KEY idx_type    (form_type),
  KEY idx_unsent  (mail_sent)
) ENGINE=InnoDB;

-- Handy checks once it is live:
--
--   -- anything that failed to email?
--   SELECT id, created_at, form_type, name, email, mail_error
--   FROM enquiries WHERE mail_sent = 0 ORDER BY created_at DESC;
--
--   -- last 20 enquiries
--   SELECT created_at, form_type, name, email, phone, LEFT(message, 60)
--   FROM enquiries ORDER BY created_at DESC LIMIT 20;
