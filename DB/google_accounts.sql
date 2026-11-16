-- Run once before installing the Google sign-in files. Existing records are unchanged.
CREATE TABLE IF NOT EXISTS google_identities (
  firebase_uid VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  user_id INT(11) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_google_user (user_id),
  CONSTRAINT fk_google_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS google_link_attempts (
  user_id INT(11) NOT NULL PRIMARY KEY,
  attempts INT NOT NULL DEFAULT 0,
  window_start DATETIME NOT NULL,
  CONSTRAINT fk_google_attempt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_number VARCHAR(30) NOT NULL DEFAULT '';
