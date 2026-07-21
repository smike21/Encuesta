-- Respaldo de estructura: Página de Encuestas (Laravel + MySQL)
-- Generado el 2026-07-21. No contiene datos personales ni respuestas.
-- Para restaurar: mysql -u USUARIO -p NOMBRE_BASE < pagina_encuestas_schema.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  remember_token VARCHAR(100) NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  PRIMARY KEY (id), UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
  email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL,
  PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
  id VARCHAR(255) NOT NULL, user_id BIGINT UNSIGNED NULL, ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL, payload LONGTEXT NOT NULL, last_activity INT NOT NULL,
  PRIMARY KEY (id), KEY sessions_user_id_index (user_id), KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache (
  `key` VARCHAR(255) NOT NULL, value MEDIUMTEXT NOT NULL, expiration INT NOT NULL, PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS jobs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, queue VARCHAR(255) NOT NULL, payload LONGTEXT NOT NULL,
  attempts TINYINT UNSIGNED NOT NULL, reserved_at INT UNSIGNED NULL, available_at INT UNSIGNED NOT NULL,
  created_at INT UNSIGNED NOT NULL, PRIMARY KEY (id), KEY jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS surveys (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, user_id BIGINT UNSIGNED NULL,
  title VARCHAR(200) NOT NULL, description TEXT NULL, collect_location TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  PRIMARY KEY (id), CONSTRAINT surveys_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS questions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, survey_id BIGINT UNSIGNED NOT NULL,
  text VARCHAR(500) NOT NULL, type VARCHAR(30) NOT NULL DEFAULT 'text', options JSON NULL,
  position INT UNSIGNED NOT NULL DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  PRIMARY KEY (id), CONSTRAINT questions_survey_id_foreign FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS survey_submissions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, survey_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL, latitude DECIMAL(10,7) NULL, longitude DECIMAL(10,7) NULL,
  created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, PRIMARY KEY (id),
  CONSTRAINT survey_submissions_survey_id_foreign FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
  CONSTRAINT survey_submissions_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS answers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, question_id BIGINT UNSIGNED NOT NULL, submission_id BIGINT UNSIGNED NOT NULL,
  value TEXT NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, PRIMARY KEY (id),
  CONSTRAINT answers_question_id_foreign FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  CONSTRAINT answers_submission_id_foreign FOREIGN KEY (submission_id) REFERENCES survey_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
