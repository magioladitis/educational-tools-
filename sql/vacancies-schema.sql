SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS vacancy_schools (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ministry_code VARCHAR(20) NOT NULL,
  name VARCHAR(190) NOT NULL,
  school_type VARCHAR(120) NOT NULL DEFAULT '',
  address VARCHAR(255) NOT NULL DEFAULT '',
  email VARCHAR(190) NOT NULL DEFAULT '',
  phone VARCHAR(80) NOT NULL DEFAULT '',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_vacancy_school_code (ministry_code),
  KEY idx_vacancy_school_active_name (active, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS vacancy_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(80) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','school_director') NOT NULL,
  school_id INT UNSIGNED NULL,
  display_name VARCHAR(190) NOT NULL DEFAULT '',
  active TINYINT(1) NOT NULL DEFAULT 1,
  must_change_password TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_vacancy_user_username (username),
  UNIQUE KEY uq_vacancy_user_school (school_id),
  KEY idx_vacancy_user_role_active (role, active),
  CONSTRAINT fk_vacancy_user_school FOREIGN KEY (school_id) REFERENCES vacancy_schools(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vacancy_specialties (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(40) NOT NULL,
  label VARCHAR(190) NOT NULL,
  sort_order INT NOT NULL DEFAULT 1000,
  active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_vacancy_specialty_code (code),
  KEY idx_vacancy_specialty_sort (active, sort_order, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vacancy_rounds (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(190) NOT NULL,
  reference_date DATE NOT NULL,
  school_year VARCHAR(20) NOT NULL,
  status ENUM('draft','open','closed') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_vacancy_round_date (reference_date, school_year),
  KEY idx_vacancy_round_status (status, reference_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vacancy_submissions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  round_id INT UNSIGNED NOT NULL,
  school_id INT UNSIGNED NOT NULL,
  education_scope ENUM('general','special') NOT NULL DEFAULT 'general',
  revision_no INT UNSIGNED NOT NULL DEFAULT 1,
  status ENUM('draft','submitted') NOT NULL DEFAULT 'draft',
  school_note TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  submitted_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_vacancy_submission_scope_revision (round_id, school_id, education_scope, revision_no),
  KEY idx_vacancy_submission_round_status (round_id, status),
  KEY idx_vacancy_submission_school (school_id, round_id),
  CONSTRAINT fk_vacancy_submission_round FOREIGN KEY (round_id) REFERENCES vacancy_rounds(id),
  CONSTRAINT fk_vacancy_submission_school FOREIGN KEY (school_id) REFERENCES vacancy_schools(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vacancy_entries (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  submission_id BIGINT UNSIGNED NOT NULL,
  specialty_id INT UNSIGNED NOT NULL,
  balance_type ENUM('vacancy','surplus') NOT NULL,
  hours SMALLINT UNSIGNED NOT NULL,
  change_reason ENUM('','new_section','timetable_change','leave_absence','teacher_move','correction','other') NOT NULL DEFAULT '',
  change_note VARCHAR(500) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY uq_vacancy_entry_specialty (submission_id, specialty_id),
  KEY idx_vacancy_entry_type (balance_type, specialty_id),
  KEY idx_vacancy_entry_reason (change_reason),
  CONSTRAINT fk_vacancy_entry_submission FOREIGN KEY (submission_id) REFERENCES vacancy_submissions(id) ON DELETE CASCADE,
  CONSTRAINT fk_vacancy_entry_specialty FOREIGN KEY (specialty_id) REFERENCES vacancy_specialties(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
