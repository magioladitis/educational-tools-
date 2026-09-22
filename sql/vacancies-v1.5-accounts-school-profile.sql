-- Vacancies v1.5 — λογαριασμοί χρηστών + ταυτότητα σχολικής μονάδας
-- Εκτέλεσέ το μία φορά στο mmagiolad_vacancies πριν ανεβάσεις τα PHP της v1.5.
SET NAMES utf8mb4;
SET @db := DATABASE();

-- 1. Στοιχεία σχολικής μονάδας
SET @has_address := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_schools' AND COLUMN_NAME='address');
SET @sql := IF(@has_address=0, "ALTER TABLE vacancy_schools ADD COLUMN address VARCHAR(255) NOT NULL DEFAULT '' AFTER school_type", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @has_email := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_schools' AND COLUMN_NAME='email');
SET @sql := IF(@has_email=0, "ALTER TABLE vacancy_schools ADD COLUMN email VARCHAR(190) NOT NULL DEFAULT '' AFTER address", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @has_phone := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_schools' AND COLUMN_NAME='phone');
SET @sql := IF(@has_phone=0, "ALTER TABLE vacancy_schools ADD COLUMN phone VARCHAR(80) NOT NULL DEFAULT '' AFTER email", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Διακριτό πεδίο Γενικής / Ειδικής (idempotent, για βάσεις που δεν έχουν περάσει το προηγούμενο migration)
SET @has_scope := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_submissions' AND COLUMN_NAME='education_scope');
SET @sql := IF(@has_scope=0, "ALTER TABLE vacancy_submissions ADD COLUMN education_scope ENUM('general','special') NOT NULL DEFAULT 'general' AFTER school_id", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @has_old_uq := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_submissions' AND INDEX_NAME='uq_vacancy_submission_revision');
SET @sql := IF(@has_old_uq>0, 'ALTER TABLE vacancy_submissions DROP INDEX uq_vacancy_submission_revision', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @has_scope_uq := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_submissions' AND INDEX_NAME='uq_vacancy_submission_scope_revision');
SET @sql := IF(@has_scope_uq=0, 'ALTER TABLE vacancy_submissions ADD UNIQUE KEY uq_vacancy_submission_scope_revision (round_id, school_id, education_scope, revision_no)', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Πραγματικοί λογαριασμοί. Οι κωδικοί αποθηκεύονται μόνο ως password_hash.
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

-- 4. Διευθύνσεις από το μητρώο σχολείων 2026-2027 της Εργαλειοθήκης
UPDATE vacancy_schools SET address='ΡΙΖΟΣΠΑΣΤΩΝ ΒΟΥΛΕΥΤΩΝ ΙΟΝΙΟΥ ΒΟΥΛΗΣ 6, ΤΚ 49100', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2401010';
UPDATE vacancy_schools SET address='ΖΑΜΠΕΛΗ 1, ΤΚ 49100', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2401020';
UPDATE vacancy_schools SET address='ΤΕΡΜΑ ΚΟΛΟΚΟΤΡΩΝΗ, ΤΚ 49100', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2401030';
UPDATE vacancy_schools SET address='ΚΟΛΟΚΟΤΡΩΝΗ ΤΕΡΜΑ, ΤΚ 49100', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2401040';
UPDATE vacancy_schools SET address='ΡΙΖΟΣΠΑΣΤΩΝ ΒΟΥΛΕΥΤΩΝ ΙΟΝΙΟΥ ΒΟΥΛΗΣ 6, ΤΚ 49100', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2401050';
UPDATE vacancy_schools SET address='ΕΛΕΥΘ.ΒΕΝΙΖΕΛΟΥ 42, ΤΚ 49100', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2401070';
UPDATE vacancy_schools SET address='ΑΓΙΟΙ ΘΕΟΔΩΡΟΙ - ΚΕΡΚΥΡΑ, ΤΚ 49100', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2401055';
UPDATE vacancy_schools SET address='ΑΓΙΟΣ ΙΩΑΝΝΗΣ ΚΕΡΚΥΡΑΣ, ΤΚ 49150', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2402090';
UPDATE vacancy_schools SET address='ΠΕΡΟΥΛΑΔΕΣ, ΤΚ 49081', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2406020';
UPDATE vacancy_schools SET address='ΑΡΓΥΡΑΔΕΣ, ΤΚ 49080', school_type='Γυμνάσιο με Λυκειακές Τάξεις' WHERE ministry_code='2405010';
UPDATE vacancy_schools SET address='ΚΑΡΟΥΣΑΔΕΣ, ΤΚ 49081', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2406010';
UPDATE vacancy_schools SET address='ΔΑΣΙΑ - ΚΕΡΚΥΡΑ, ΤΚ 49083', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2402050';
UPDATE vacancy_schools SET address='10η πάροδος, Σπύρου Ραθ 1, Κέρκυρα, ΤΚ 49132', school_type='ΕΝ.Ε.Ε.ΓΥ.-Λ.' WHERE ministry_code='2411001';
UPDATE vacancy_schools SET address='ΣΠΥΡΟΥ ΞΥΝΔΑ 2, ΤΚ 49100', school_type='Εσπερινό Γυμνάσιο' WHERE ministry_code='2401060';
UPDATE vacancy_schools SET address='ΑΓΡΟΣ, ΤΚ 49083', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2404010';
UPDATE vacancy_schools SET address='ΑΧΑΡΑΒΗ ΚΕΡΚΥΡΑ, ΤΚ 49081', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2410010';
UPDATE vacancy_schools SET address='ΚΑΣΤΕΛΛΑΝΟΙ ΜΕΣΗΣ, ΤΚ 49084', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2407010';
UPDATE vacancy_schools SET address='ΠΟΛΥΤΕΧΝΕΙΟΥ 2, ΤΚ 49080', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2402010';
UPDATE vacancy_schools SET address='ΛΙΑΠΑΔΕΣ, ΤΚ 49083', school_type='Ημερήσιο Γυμνάσιο' WHERE ministry_code='2408050';
UPDATE vacancy_schools SET address='ΚΑΣΣΙΟΠΗ, ΤΚ 49081', school_type='Γυμνάσιο με Λυκειακές Τάξεις' WHERE ministry_code='2409010';
UPDATE vacancy_schools SET address='ΠΑΞΟΙ ΚΕΡΚΥΡΑΣ, ΤΚ 49082', school_type='Γυμνάσιο με Λυκειακές Τάξεις' WHERE ministry_code='2403010';
UPDATE vacancy_schools SET address='ΣΚΡΙΠΕΡΟ, ΤΚ 49083', school_type='Γυμνάσιο με Λυκειακές Τάξεις' WHERE ministry_code='2408010';
UPDATE vacancy_schools SET address='ΤΖΑΒΡΟΥ-ΚΑΤΩ ΚΟΡΑΚΙΑΝΑ, ΤΚ 49083', school_type='Μουσικό Σχολείο' WHERE ministry_code='2401065';
UPDATE vacancy_schools SET address='4ο χλμ ΕΘΝΙΚΗΣ ΛΕΥΚΙΜΜΗΣ, ΤΚ 49100', school_type='Ε.Ε.Ε.ΕΚ.' WHERE ministry_code='2441001';
UPDATE vacancy_schools SET address='ΕΥΑΓΓΕΛΟΥ ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49100', school_type='ΕΠΑΛ' WHERE ministry_code='2440030';
UPDATE vacancy_schools SET address='ΚΑΤΩ ΚΟΡΑΚΙΑΝΑ - ΚΕΡΚΥΡΑ, ΤΚ 49083', school_type='ΕΠΑΛ' WHERE ministry_code='2440050';
UPDATE vacancy_schools SET address='ΕΥΑΓΓΕΛΟΥ ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49100', school_type='Εσπερινό ΕΠΑΛ' WHERE ministry_code='2440045';
UPDATE vacancy_schools SET address='ΠΑΓΚΡΑΤΕΪΚΑ, ΤΚ 49100', school_type='Πρότυπο ΕΠΑΛ' WHERE ministry_code='2448000';
UPDATE vacancy_schools SET address='ΣΠΥΡΟΥ ΞΥΝΔΑ 4, ΤΚ 49100', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2451010';
UPDATE vacancy_schools SET address='ΣΠΥΡΟΥ ΞΥΝΔΑ 4, ΤΚ 49100', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2451020';
UPDATE vacancy_schools SET address='ΣΠΥΡΟΥ ΞΥΝΔΑ 2, ΤΚ 49100', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2451030';
UPDATE vacancy_schools SET address='ΠΑΡΟΔΟΣ ΑΓΡΟΚΗΠΙΩΝ 1, ΤΚ 49100', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2451040';
UPDATE vacancy_schools SET address='Ε. ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49132', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2490030';
UPDATE vacancy_schools SET address='Σ. ΞΥΝΔΑ 2, ΤΚ 49100', school_type='Εσπερινό ΓΕΛ' WHERE ministry_code='2451060';
UPDATE vacancy_schools SET address='ΑΓΡΟΣ, ΤΚ 49083', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2454010';
UPDATE vacancy_schools SET address='ΚΑΣΤΕΛΛΑΝΟΙ ΜΕΣΗΣ, ΤΚ 49084', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2457010';
UPDATE vacancy_schools SET address='ΠΟΛΥΤΕΧΝΕΙΟΥ 2, ΤΚ 49080', school_type='Ημερήσιο Γενικό Λύκειο (ΓΕΛ)' WHERE ministry_code='2452010';
UPDATE vacancy_schools SET address='ΕΥΑΓΓΕΛΟΥ ΝΑΠΟΛΕΟΝΤΟΣ 12, ΤΚ 49100', school_type='Εργαστηριακό Κέντρο (Ε.Κ.)' WHERE ministry_code='SEK087';

-- Έλεγχοι
SELECT COUNT(*) AS schools_with_address FROM vacancy_schools WHERE active=1 AND TRIM(address)<>'';
SELECT COUNT(*) AS account_count FROM vacancy_users;
SELECT COUNT(*) AS general_submissions FROM vacancy_submissions WHERE education_scope='general';
