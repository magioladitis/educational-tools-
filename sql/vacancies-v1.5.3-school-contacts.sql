-- v1.5.3 — Ενημέρωση τηλεφώνων / email σχολικών μονάδων από gridResults (8).xls
-- Πηγή: επίσημο μητρώο σχολικών μονάδων που δόθηκε από τον χρήστη.
START TRANSACTION;

-- Αμυντικά: βεβαιωνόμαστε ότι υπάρχουν τα πεδία επικοινωνίας.
SET @db := DATABASE();
SET @has_email := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_schools' AND COLUMN_NAME='email');
SET @sql := IF(@has_email=0, "ALTER TABLE vacancy_schools ADD COLUMN email VARCHAR(190) NOT NULL DEFAULT '' AFTER address", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @has_phone := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vacancy_schools' AND COLUMN_NAME='phone');
SET @sql := IF(@has_phone=0, "ALTER TABLE vacancy_schools ADD COLUMN phone VARCHAR(80) NOT NULL DEFAULT '' AFTER email", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Το αρχείο περιέχει μία επιπλέον ενεργή μονάδα που δεν υπήρχε στο αρχικό seed της εφαρμογής.
INSERT INTO vacancy_schools (ministry_code,name,school_type,address,email,phone,active) VALUES ('2404000','ΕΝΙΑΙΟ ΕΙΔΙΚΟ ΕΠΑΓΓΕΛΜΑΤΙΚΟ ΓΥΜΝΑΣΙΟ (ΕΝ.Ε.Ε.ΓΥ.-Λ.) ΒΟΡΕΙΑΣ ΚΕΡΚΥΡΑΣ','Ενιαίο Ειδικό Επαγγελματικό Γυμνάσιο - Λύκειο','', '', '', 1) ON DUPLICATE KEY UPDATE name=VALUES(name), school_type=VALUES(school_type), active=1;

-- Ενημέρωση τηλεφώνου και υπηρεσιακού email βάσει Κωδικού Υπουργείου.
UPDATE vacancy_schools SET phone='2663071203', email='mail@gym-agrou.ker.sch.gr' WHERE ministry_code='2404010';
UPDATE vacancy_schools SET phone='2663071202', email='mail@lyk-agrou.ker.sch.gr' WHERE ministry_code='2454010';
UPDATE vacancy_schools SET phone='2661054230', email='mail@gym-kastell.ker.sch.gr' WHERE ministry_code='2407010';
UPDATE vacancy_schools SET phone='2661054404', email='mail@lyk-kastell.ker.sch.gr' WHERE ministry_code='2457010';
UPDATE vacancy_schools SET phone='2663031314', email='mail@gym-karous.ker.sch.gr' WHERE ministry_code='2406010';
UPDATE vacancy_schools SET phone='2663095201', email='mail@gym-amfip.ker.sch.gr' WHERE ministry_code='2406020';
UPDATE vacancy_schools SET phone='2663064069', email='mail@gym-thinal.ker.sch.gr' WHERE ministry_code='2410010';
UPDATE vacancy_schools SET phone='2663081252', email='mail@gym-kassiop.ker.sch.gr' WHERE ministry_code='2409010';
UPDATE vacancy_schools SET phone='2661039590', email='mail@2gym-kerkyr.ker.sch.gr' WHERE ministry_code='2401020';
UPDATE vacancy_schools SET phone='2661040255', email='mail@6gym-kerkyr.ker.sch.gr' WHERE ministry_code='2401070';
UPDATE vacancy_schools SET phone='2661033511', email='mail@3gym-kerkyr.ker.sch.gr' WHERE ministry_code='2401030';
UPDATE vacancy_schools SET phone='2661039982', email='mail@1gym-kerkyr.ker.sch.gr' WHERE ministry_code='2401010';
UPDATE vacancy_schools SET phone='2661360273', email='mail@7gym-kerkyr.ker.sch.gr' WHERE ministry_code='2401055';
UPDATE vacancy_schools SET phone='2661039039', email='mail@1sek-kerkyr.ker.sch.gr' WHERE ministry_code='SEK087';
UPDATE vacancy_schools SET phone='2661032487', email='mail@4gym-kerkyr.ker.sch.gr' WHERE ministry_code='2401040';
UPDATE vacancy_schools SET phone='2661027260', email='mail@epal-esp-kerkyr.ker.sch.gr' WHERE ministry_code='2440045';
UPDATE vacancy_schools SET phone='2661043942', email='mail@gym-ee-kerkyr.ker.sch.gr' WHERE ministry_code='2411001';
UPDATE vacancy_schools SET phone='2661026633', email='mail@epal-prot-kerkyr.ker.sch.gr' WHERE ministry_code='2448000';
UPDATE vacancy_schools SET phone='2661039970', email='mail@gym-esp-kerkyr.ker.sch.gr' WHERE ministry_code='2401060';
UPDATE vacancy_schools SET phone='2661035211', email='1epalkerkyras@sch.gr' WHERE ministry_code='2440030';
UPDATE vacancy_schools SET phone='2661039827', email='mail@1lyk-kerkyr.ker.sch.gr' WHERE ministry_code='2451010';
UPDATE vacancy_schools SET phone='2661043400', email='mail@2lyk-kerkyr.ker.sch.gr' WHERE ministry_code='2451020';
UPDATE vacancy_schools SET phone='2661021644', email='mail@4lyk-kerkyr.ker.sch.gr' WHERE ministry_code='2451040';
UPDATE vacancy_schools SET phone='2661039713', email='mail@3lyk-kerkyr.ker.sch.gr' WHERE ministry_code='2451030';
UPDATE vacancy_schools SET phone='2661041841', email='mail@5lyk-kerkyr.ker.sch.gr' WHERE ministry_code='2490030';
UPDATE vacancy_schools SET phone='2661033090', email='mail@5gym-kerkyr.ker.sch.gr' WHERE ministry_code='2401050';
UPDATE vacancy_schools SET phone='2661041350', email='mail@lyk-esp-kerkyr.ker.sch.gr' WHERE ministry_code='2451060';
UPDATE vacancy_schools SET phone='2661081829', email='mail@eeeek.ker.sch.gr' WHERE ministry_code='2441001';
UPDATE vacancy_schools SET phone='2662052840', email='mail@gym-argyr.ker.sch.gr' WHERE ministry_code='2405010';
UPDATE vacancy_schools SET phone='2662022397', email='mail@gym-lefkimm.ker.sch.gr' WHERE ministry_code='2402010';
UPDATE vacancy_schools SET phone='2662022661', email='mail@lyk-lefkimm.ker.sch.gr' WHERE ministry_code='2452010';
UPDATE vacancy_schools SET phone='2663041440', email='mail@gym-liapad.ker.sch.gr' WHERE ministry_code='2408050';
UPDATE vacancy_schools SET phone='2663022548', email='mail@gym-skrip.ker.sch.gr' WHERE ministry_code='2408010';
UPDATE vacancy_schools SET phone='2661052207', email='mail@gym-ag-ioann.ker.sch.gr' WHERE ministry_code='2402090';
UPDATE vacancy_schools SET phone='2661091573', email='mail@gym-mous-kerkyr.ker.sch.gr' WHERE ministry_code='2401065';
UPDATE vacancy_schools SET phone='2661091910', email='mail@1epal-korak.ker.sch.gr' WHERE ministry_code='2440050';
UPDATE vacancy_schools SET phone='2661097797', email='mail@gym-spart.ker.sch.gr' WHERE ministry_code='2402050';
UPDATE vacancy_schools SET phone='2662031110', email='mail@gym-paxon.ker.sch.gr' WHERE ministry_code='2403010';

COMMIT;

-- Έλεγχοι
SELECT COUNT(*) AS active_schools FROM vacancy_schools WHERE active=1;
SELECT COUNT(*) AS schools_with_phone FROM vacancy_schools WHERE active=1 AND TRIM(phone)<>'';
SELECT COUNT(*) AS schools_with_email FROM vacancy_schools WHERE active=1 AND TRIM(email)<>'';
SELECT ministry_code,name,phone,email FROM vacancy_schools WHERE active=1 ORDER BY name;