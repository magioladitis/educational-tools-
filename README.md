## 2026-09-26 — Mobile legal-source disclosure hardening (v3.22.15)

- Οι κάρτες «Πηγές / Νομική βάση» αποδίδονται πλέον κλειστές στο αρχικό HTML, ώστε σε iPhone/PWA να μην εμφανίζονται στιγμιαία ανοιχτές πριν τρέξει το JavaScript.
- Το κοινό `assets/common.js` τις ανοίγει προοδευτικά μόνο σε desktop συσκευές. Σε μικρό viewport ή touch/coarse-pointer συσκευή παραμένουν κλειστές, ακόμη και σε landscape κινητού.
- Διατηρείται η πλήρης ανάπτυξη στην εκτύπωση μέσω του υπάρχοντος print layer.
- Προστέθηκαν regression checks για default-closed markup, touch/mobile collapse και desktop progressive expansion.
- Έκδοση/cache busting: `EDU_TOOLS_VERSION = 3.22.15`.

> Αρχιτεκτονική και canonical sources: [`ARCHITECTURE.md`](ARCHITECTURE.md)
## 2026-09-26 — Accessibility + production PWA hardening (v3.22.14)

- Shared skip-to-content navigation, `aria-current`, stronger focus-visible styling and reduced-motion support.
- Fixed accessible names/label associations in staffing profile fields, including the Ηθική/Θρησκευτικά panel.
- Service-worker registration now uses `updateViaCache: none` for reliable release discovery.
- Added `tests/accessibility-production-contract.py` and `PRODUCTION-AUDIT.md`; the accessibility contract is part of the release gate.


## 2026-09-26 — Specialty normalization + personnel workload Phase 3C (v3.22.13)

- Ενοποιήθηκε η κανονικοποίηση κωδικών ειδικότητας σε κοινό browser module `includes/specialty-code-normalization.js` με schema `teacher_specialty_code_normalization_v1`.
- PHP και JS ακολουθούν κοινά contract vectors: mixed Greek/Latin prefixes, εσωτερικά κενά, zero-padding (`PE3` → `ΠΕ03`) και υποκωδικοί με `. - _ /` (`TE1/4` → `ΤΕ01.04`).
- Το CSV import κρατά permissive εξαγωγή από ελεύθερο κείμενο, αλλά το τελικό code canonicalization γίνεται αποκλειστικά από το κοινό module.
- Ο optimizer policy δηλώνει πλέον `specialty_code_normalization_schema`. Αν browser/server schema διαφέρουν, ο client optimizer απενεργοποιείται και γίνεται ασφαλές PHP fallback.
- Το `allocation_auto` server optimizer εκτελείται μόνο όταν `client_optimizer_capable=0`. Σε client exception το UI μηδενίζει ρητά το capability πριν αφήσει το submit fallback.
- Ενισχύθηκε το release gate με PHP↔JS normalization parity, canonicalization audit, EducationCore regression και personnel CSV import regression.
- Έκδοση/cache busting: `EDU_TOOLS_VERSION = 3.22.13`.

## 2026-09-26 — Personnel workload performance benchmark (v3.22.12)
- Προστέθηκε `tests/personnel-workload-performance-benchmark.py` με κοινά synthetic small / typical / large workloads για τον PHP reference optimizer και τον browser JS optimizer.
- Το quick `--contract` μπήκε στο `pre-pwa-regression.sh`: ελέγχει objective parity, certification, ίδιο search-node count, valid allocation fixtures, γενναιόδωρα wall-clock ceilings και relative scaling large/typical. Τα όρια είναι σκόπιμα χαλαρά ώστε να πιάνουν καταστροφικές regressions και όχι φυσιολογικό CI noise.
- Το πλήρες benchmark μετρά επίσης default PHP GET rendering, Node/V8 validation και, όπου υπάρχει λειτουργικό headless Chromium, payload hydration / validation / optimizer / representative DOM row rendering. Chromium failure/absence δεν μπλοκάρει το correctness gate.
- Baseline στο τρέχον container: default PHP GET 24.42 ms / 65,190 B HTML· optimizer PHP vs Node/V8: small 0.088/0.211 ms, typical 0.519/1.052 ms, large 2.163/3.229 ms· client validation 0.014/0.041/0.107 ms. Το PHP core είναι ελαφρώς ταχύτερο ως αλγόριθμος, αλλά το client path αποφεύγει ολόκληρο HTTP/PHP/full-page round trip, που είναι το ουσιαστικό UX κέρδος.
- Έκδοση/cache busting: `EDU_TOOLS_VERSION = 3.22.12`.

## 2026-09-26 — Personnel workload browser optimizer · Phase 3B (v3.22.11)

- Κεντρικοποιήθηκε η πολιτική του optimizer στο server-side `personnelWorkloadOptimizerPolicy()` και αποστέλλεται στον browser ως `optimizerPolicy`: σειρά/βαθμοί προτεραιότητας, objective order, όριο Β΄ ανάθεσης και safety budgets δεν διατηρούνται πλέον ως ανεξάρτητη production πηγή στο UI.
- Η επιλεξιμότητα παραμένει data-driven από τα server-built `allocationSlots[].eligible_by_priority`. Ο browser optimizer δεν περιέχει hard-coded πίνακα κλάδων/αναθέσεων. Προστέθηκε ειδικό TE16 regression fixture που αποδεικνύει ότι αλλαγή tier στο canonical slot data περνά στον client χωρίς αλλαγή JavaScript.
- Ευθυγραμμίστηκαν τα client safety limits με τον server: global node budget 30.000, component limit 12.000, complexity cutoffs και nominal time budget 1.250 ms. Και οι δύο engines δηλώνουν ρητά `certified_optimum` ή `best_known_fallback`.
- Τεκμηριώθηκε ότι η ταυτότητα των rows δεν είναι μέρος του objective: διαφορετικές αναθέσεις επιτρέπονται μόνο όταν έχουν το ίδιο λεξικογραφικό objective και τηρούν όλα τα invariants. Το differential harness ταξινομεί πλέον `exact` έναντι `equivalent-optimum` αντί να κρύβει τις ισοπαλίες.
- Προστέθηκε πραγματικό school-profile golden fixture: Γυμνάσιο 2026 + ΤΕ16/1 ώρα πρέπει και σε PHP και σε JS να καταλήγει ακριβώς στη Μουσική Α1 ως Α΄ ανάθεση. Προστέθηκε επίσης explicit large-component fallback fixture.
- Η Καρτέλα 6 παραμένει client-first. JS-capable forms δηλώνουν `client_optimizer_capable=1`, ενώ ο βαρύς server specialty report κρατιέται μόνο ως no-JS/direct fallback. Το `allocation_auto` PHP path παραμένει ως progressive fallback αν ο browser optimizer αποτύχει.
- Τα νέα policy/source checks εντάχθηκαν στο `pre-pwa-regression.sh`, ενώ διατηρήθηκαν χωρίς αύξηση τα staffing performance budgets.

## 2026-09-26 — Personnel workload browser optimizer · Phase 3A (v3.22.10)

- Εξήχθη ο ήδη υπάρχων client-side atomic optimizer από το `staffing-simulator-ui.js` στο pure module `includes/personnel-workload-calculations.js`. Το UI πλέον καλεί `PersonnelWorkloadCalculations.optimizeRemaining()` αντί να διατηρεί δικό του branch-and-bound/DP implementation.
- Η «Αυτόματη πρόταση κάλυψης» και ο «Έλεγχος κατανομής» εκτελούνται client-side χωρίς POST όταν το shared module είναι διαθέσιμο. Τα υπάρχοντα submit/PHP paths παραμένουν ως progressive fallback/reference.
- Προστέθηκαν ενισχυμένα safety contracts: 86 deterministic PHP↔JS differential scenarios, invariants για atomic slots/capacity/B΄≤10/eligibility, 30 randomized tiny cases απέναντι σε ανεξάρτητο brute-force oracle, 4 real-school-profile parity cases και mutation tests για B΄ hard limit και SPECIAL-vs-B objective.
- Προστέθηκε architecture contract που επιβεβαιώνει ότι τα client allocation actions προλαβαίνουν το submit/compact path και ότι δεν εισάγεται fetch/XHR.
- Διατηρήθηκαν τα performance budgets του staffing controller χωρίς αύξηση ορίων.
- Όλα τα νέα optimizer/client-action contracts εντάχθηκαν στο `tests/pre-pwa-regression.sh`.

## 2026-09-26 — Personnel workload browser parity · Phase 2 (v3.22.9)

- Μεταφέρθηκαν στο `includes/personnel-workload-calculations.js` οι pure συναρτήσεις slot eligibility: `priorityForSlotCode()`, `priorityRank()` και `bestAssignmentForSlot()`.
- Προστέθηκε browser-side `validateRosterSlotAllocations()` με parity προς `personnelWorkloadRosterSlotPlan()` για atomic slot hours, eligibility κύριας/2ης ειδικότητας, lower-priority warnings, cross-row over-allocation και συνολικό όριο 10 ωρών Β΄ ανάθεσης.
- Το `staffing-simulator-ui.js` δεν διατηρεί πλέον δεύτερη ανεξάρτητη υλοποίηση της επιλογής καλύτερης ανάθεσης ή του slot-level validation.
- Διορθώθηκε parity κενό στο UI: μερική κατανομή ενός atomic slot (π.χ. 1 από 2 ώρες) επισημαίνεται πλέον ως μη έγκυρη, όπως ήδη έκανε η PHP reference implementation.
- Νέο `tests/personnel-workload-allocation-client-parity-contract.py` συγκρίνει PHP ↔ JS πάνω στα ίδια synthetic slots και καλύπτει κύρια/2η ειδικότητα, atomic partial, ineligible specialty, άγνωστα IDs, over-allocation, lower-priority και >10 ώρες Β΄ ανάθεσης.
- Το νέο parity contract προστέθηκε στο `tests/pre-pwa-regression.sh`. Ο optimizer/automatic balance παραμένει server/reference για Phase 3.

## 2026-09-26 — Personnel workload browser parity · Phase 1 (v3.22.8)

- Προστέθηκε `includes/personnel-workload-calculations.js` με browser-side pure calculations που αντιστοιχούν στις person-level συναρτήσεις του `includes/personnel-workload.php`: service days/labels, branch resolution, director section bands, secondary base hours, obligation και person normalization.
- Η καρτέλα προσωπικού χρησιμοποιεί πλέον το browser-side parity module τόσο για την κλίμακα τμημάτων όσο και για τον υπολογισμό Υ.Ω. Διευθυντή/Υποδιευθυντή, αντί για ξεχωριστή frontend υλοποίηση. Η σελίδα δεν χρειάζεται πλέον να φορτώνει το γενικό `teaching-hours-calculations.js`.
- Προστέθηκε `tests/personnel-workload-client-parity-contract.py` με PHP↔JS parity vectors για ΠΕ/ΤΕ/ΔΕ, διοικητικούς ρόλους, myschool override, χειροκίνητο Υ.Ω. και εξωτερικές ώρες.
- Το allocation/slot/optimizer layer παραμένει προσωρινά server-side reference μέχρι να αποκτήσει ξεχωριστό parity contract· δεν έγινε αλλαγή στη λογική κενών/πλεονασμάτων σε αυτή τη φάση.
- Το parity contract εντάχθηκε στο κεντρικό pre-PWA regression gate.

## 2026-09-26 — Shared PWA head & service worker (v3.22.7)

- Συγκεντρώθηκαν manifest/favicon/Apple-touch metadata στο `includes/head-pwa.php` αντί να επαναλαμβάνονται σε κάθε PHP σελίδα.
- Προστέθηκε `assets/pwa.js` για κοινή εγγραφή του root `service-worker.js` μόνο σε HTTPS/localhost.
- Ο service worker χρησιμοποιεί συντηρητικό network-first cache μόνο για στατικά assets· δεν cache-άρει PHP/HTML/navigation responses.
- Προστέθηκε `.htaccess` MIME mapping για `.webmanifest`/`.js` και revalidation του manifest/service worker.
- Το `tests/pwa-manifest-contract.py` ελέγχει πλέον shared-head adoption, service-worker registration/cache policy και Apache MIME rules.

## 2026-09-26 — Browser favicon & ΠΕ15 assignment cleanup (v3.22.6)

- Προστέθηκαν browser icons (`favicon.ico`, 16×16, 32×32) και `apple-touch-icon` 180×180 σε όλες τις δημόσιες σελίδες, πέρα από τα PWA icons του manifest.
- Στις αναθέσεις ΕΝ.Ε.Ε.ΓΥ.-Λ. ο παλαιός κλάδος ΠΕ15 αφαιρέθηκε από την ενεργή Β΄ ανάθεση. Η ισχύουσα επιλογή παραμένει ΠΕ80 και η ιστορική πληροφορία εμφανίζεται μόνο ως σημείωση: «Προτεραιότητα: πρώην ΠΕ09 και ΠΕ15».
- Το `teachingAssignmentKnownSpecialties()` φιλτράρει αμυντικά τον ΠΕ15 και νέο regression contract αποτυγχάνει αν παλαιός κλάδος ξαναμπεί σε A/B/C/special_codes.
- Το PWA/browser-icon contract ελέγχει πλέον ύπαρξη/διαστάσεις favicon και παρουσία των icon links σε όλες τις δημόσιες σελίδες.

## 2026-09-26 — Legal sources deep-link & contract hardening (v3.22.5)

- Τα ΦΕΚ Β΄ 5733/22-09-2026 για ΕΝ.Ε.Ε.ΓΥ.-Λ. και Ε.Ε.Ε.ΕΚ. δείχνουν πλέον στο συγκεκριμένο record του Εθνικού Τυπογραφείου (`fekId=805734`) αντί για τη γενική αναζήτηση.
- Συμπληρώθηκαν οι 5 ελλείπουσες ακριβείς ημερομηνίες αποφάσεων (`date`) και τα αντίστοιχα dated `decision` strings για ΕΠΑ.Λ./Π.ΕΠΑ.Λ. 2026 και Ναυτιλιακά ΕΠΑ.Λ. 2018.
- Προστέθηκε ισχυρό `tests/legal-sources-contract-test.php`: duplicate authored keys χωρίς εξάρτηση από indentation, πλήρες schema/date/HTTPS/deep-link validation, αμφίδρομος έλεγχος σχέσεων και από τις δύο κατευθύνσεις, alternate URL validation, pinned Εθνικό Τυπογραφείο deep links, helper API smoke tests και έλεγχος consumer source-key mappings.
- Το legal-sources contract εντάχθηκε στο `tests/pre-pwa-regression.sh`, ώστε generic FEK links ή dangling legal-source mappings να μπλοκάρουν release.
- Αφαιρέθηκε ένα νέο stale hard-coded version assertion από το Digital Tutoring contract· ελέγχεται ξανά semantic `EDU_TOOLS_VERSION` αντί για συγκεκριμένο release number.

## 2026-09-26 — Mobile polish & PWA installability icons (v3.22.4)

- Προστέθηκαν πραγματικά PWA icons 192×192 και 512×512 στο `assets/icons/` και δηλώθηκαν στο `manifest.webmanifest`, ώστε το manifest να καλύπτει τα βασικά Chromium installability icon requirements.
- Προστέθηκε ξεχωριστό full-bleed `icon-maskable-512.png` για adaptive Android masks, ενώ διατηρείται και το SVG source για μελλοντικές εξαγωγές.
- Το manifest απέκτησε 3 χρήσιμα app shortcuts (Διδακτικές Ανάγκες, Μισθοδοσία, Προθεσμίες).
- Η σελίδα «Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου» εντάχθηκε στο κοινό visual/responsive family των European Schools calculators.
- Στα Ωνάσεια τα mode radio labels αποκτούν mobile touch target τουλάχιστον 44px.
- Ενημερώθηκαν τα stale regression expectations για τα νόμιμα structural/admin `<details>` της εφαρμογής «Κενά σχολείων» και για τον 21ο calculator που χρησιμοποιεί το κοινό action helper.
- Το PWA manifest contract ελέγχει πλέον ότι τα icons υπάρχουν πραγματικά και ότι οι διαστάσεις τους συμφωνούν με το manifest.
- `tests/pre-pwa-regression.sh`: PASS.

## 2026-09-25 — Σχολείο Ευρωπαϊκής Παιδείας Ηρακλείου

- Προστέθηκε νέος υπολογιστής για την πρόσκληση 69163/Η2/28-05-2026 (σχολικό έτος 2026–2027).
- Η μοριοδότηση εφαρμόζει 45 μόρια επιστημονικής/παιδαγωγικής κατάρτισης, 25 μόρια εμπειρίας και 30 μόρια συνέντευξης (σύνολο 100).
- Ενσωματώθηκαν οι ειδικοί κανόνες για master στο ίδιο αντικείμενο με διδακτορικό, έως 3 πρόσθετες γλώσσες και έως 5 έτη υπηρεσίας σε Ευρωπαϊκό Σχολείο/Σ.Ε.Π.
- Προστέθηκε βασικός έλεγχος της κανονικής και της εξαιρετικής διαδρομής μη φυσικού ομιλητή, δυναμικό checklist δικαιολογητικών και επίσημος σύνδεσμος της πρόσκλησης.

## 2026-09-23 — Διόρθωση άμεσου συνδέσμου ΦΕΚ Β΄ 5710 (v3.22.2)

- Ο σύνδεσμος του ΦΕΚ Β΄ 5710/22-09-2026 στις Αναθέσεις Μαθημάτων οδηγεί πλέον απευθείας στην εγγραφή του Εθνικού Τυπογραφείου (`fekId=805702`).
- Η ίδια διόρθωση εφαρμόστηκε τόσο στην τροποποίηση της Γ΄ ΕΠΑ.Λ. όσο και της Γ΄ Π.ΕΠΑ.Λ., επειδή οι δύο αποφάσεις δημοσιεύονται στο ίδιο ΦΕΚ.

## 2026-09-23 — Regression gate cleanup (v3.22.1)

- Regression tests no longer pin the historical `3.21.14` release; they validate the centralized semantic `EDU_TOOLS_VERSION`.
- All public «Κενά σχολείων» pages now expose `manifest.webmanifest`.
- Remaining inline handlers/JavaScript in vacancy administration were moved to `assets/vacancies.js`.
- `tests/pre-pwa-regression.sh`: PASS.

## 2026-09-23 — ΦΕΚ Β΄ 5710 & 5733 / επικαιροποίηση αναθέσεων ΤΕ16
- Ενσωματώθηκαν οι τροποποιήσεις των ΦΕΚ Β΄ 5710/22-09-2026 και Β΄ 5733/22-09-2026, με ισχύ από το σχολικό έτος 2026-2027.
- Ο ΤΕ16 μεταφέρθηκε σε **Α΄ ανάθεση** στα ακριβώς επηρεαζόμενα μαθήματα: «Μουσικοκινητική Αγωγή» Γ΄ ΕΠΑ.Λ., Γ΄ Π.ΕΠΑ.Λ. και Δ΄ Λυκείου ΕΝ.Ε.Ε.ΓΥ.-Λ., καθώς και «Μουσική» στα Ε.Ε.Ε.ΕΚ. και στο Γυμνάσιο ΕΝ.Ε.Ε.ΓΥ.-Λ.
- Δεν μεταβλήθηκαν άλλες αναθέσεις, σύμφωνα με τη ρητή διατύπωση των νέων αποφάσεων.
- Ενημερώθηκαν το κεντρικό `legal-sources.php`, οι source cards και τα αντίστοιχα regression contracts. Το source-registry audit ολοκληρώνεται με 0 errors και τα στοχευμένα assignment/workload tests παραμένουν πράσινα.

## 2026-09-13 — Mobile-browser usability pass (360 / 390 / 430px)
- Η εμπειρία από browser στο κινητό παραμένει η βασική προτεραιότητα, ανεξάρτητα από μελλοντική εγκατάσταση ως PWA.
- Η κεντρική `ergaleia.php` έγινε πιο συμπαγής σε κινητό: οι κάρτες δεν κρατούν desktop `min-height`, μειώθηκαν κενά/τυπογραφική πυκνότητα και τα φίλτρα έγιναν οριζόντια scrollable chips αντί να καταλαμβάνουν πολλές γραμμές.
- Τα mobile φίλτρα, hero actions, workflow tabs και βασικά option controls διατηρούν touch target τουλάχιστον 44px.
- Στις Διδακτικές Ανάγκες τα 6 workflow tabs λειτουργούν σε οριζόντιο touch strip σε <=760px αντί να τυλίγονται σε πολλές σειρές.
- Ενισχύθηκαν τα `.check-row`, `.checkrow`, checkbox-group labels και segmented choices για πιο άνετη αφή, χωρίς αλλαγή στους υπολογισμούς.
- Διατηρείται η αρχή ότι οι πραγματικά φαρδιοί πίνακες κάνουν ελεγχόμενο horizontal scroll· δεν εισάγεται `overflow-x:hidden`.
- Προστέθηκε `tests/mobile-browser-usability-contract.py` και εντάχθηκε στο `tests/pre-pwa-regression.sh`.
- Τελικός έλεγχος: 177/177 test files PASS σε απομονωμένο test copy, PRE-PWA REGRESSION GATE PASS, 107/107 production PHP lint, 76/76 JS syntax, inline-JS separation 6/6 PASS.

## 2026-09-13 — Mobile hardening pass (v3.21.14)
- Τα κοινά form controls γίνονται 16px σε οθόνες έως 700px, ώστε να αποφεύγεται το ανεπιθύμητο focus zoom του iOS Safari.
- Τα βασικά mobile touch targets (navigation, buttons, summaries) διατηρούν ελάχιστο ύψος 44px, ακόμη και στα 320–360px.
- Προστέθηκε κοινό shrink/wrap containment για grids, cards και μεγάλα ελληνικά labels, χωρίς `overflow-x:hidden` που θα έκρυβε πραγματικά προβλήματα.
- Οι σκόπιμα φαρδιοί πίνακες αποκτούν ομαλότερο touch scrolling. Στις Διδακτικές Ανάγκες ο πίνακας Καρτέλας 6 διατηρεί αναγνώσιμο ελάχιστο πλάτος και κάνει ελεγχόμενο οριζόντιο scroll αντί να συμπιέζει έξι στήλες.
- Στις Διδακτικές Ανάγκες σκληρύνθηκαν επίσης τα toolbars, το help popover, τα context chips και τα compact delete controls για πολύ στενές οθόνες.
- Οι Αναθέσεις Μαθημάτων και το Ωρολόγιο Πρόγραμμα επιτρέπουν ασφαλές wrapping σε μεγάλους τίτλους/συνθήκες.
- Δεν άλλαξε καμία λογική υπολογισμού, κανένα dataset και καμία desktop διάταξη.

## 2026-09-09 — Audit ενεργειών & κοινή εκτύπωση calculators
- Αφαιρέθηκε από το `ypologismos-morion.php` το περιττό κουμπί «Έλεγχος & υπολογισμός», επειδή ο υπολογισμός/έλεγχος ενημερώνεται ήδη live σε `input`/`change`.
- Ενοποιήθηκε η ονομασία επαναφοράς από «Μηδενισμός» σε **«Καθαρισμός»** στα εργαλεία.
- Προστέθηκε κοινό, opt-in print helper `includes/education-print.js` για 23 αριθμητικούς/πίνακες calculators. Δημιουργεί Α4 αναφορά με δηλωμένα στοιχεία, τρέχον αποτέλεσμα, πηγές και ενημερωτική αποποίηση.
- Οι ώριμες ειδικές εκτυπώσεις **Μισθοδοσίας**, **Διδακτικών Αναγκών** και **Μετατροπής Κλίμακας** δεν αντικαταστάθηκαν ούτε τροποποιήθηκαν από τον κοινό helper.
- Προστέθηκε regression contract για την εγγραφή των εργαλείων, τη διατήρηση των ειδικών print reports και την αφαίρεση του περιττού manual calculate action.

- Μετονομάστηκε το εργαλείο σε **Υπολογισμός Μισθολογικού Κλιμακίου (Μ.Κ.) / Μισθοδοσίας** και ενημερώθηκαν ο τίτλος σελίδας, η κάρτα της Εργαλειοθήκης, η αναζήτηση και το εκτυπώσιμο σημείωμα.
## 2026-09-09 — Εκτίμηση καθαρών αποδοχών & φορολογία 2026 στον υπολογιστή Μ.Κ.
- Διατηρήθηκε το υπάρχον layout του `ypologismos-misthologikou-klimakiou.php` και προστέθηκε προαιρετική εκτίμηση καθαρών αποδοχών ακριβώς κάτω από τα στοιχεία μισθολογικής κατάταξης.
- Ο υπολογισμός ξεκινά από τον επίσημο βασικό μικτό μισθό του τελικού Μ.Κ. και υποστηρίζει τρία ενδεικτικά προφίλ κρατήσεων: μόνιμος δημόσιος υπάλληλος, νεοδιόριστος 1ου έτους ΜΤΠΥ και αναπληρωτής/ΙΔΟΧ ΚΠΚ 101.
- Ενσωματώθηκε η φορολογική κλίμακα μισθωτών από το φορολογικό έτος 2026, οι ειδικές κλίμακες για ηλικίες έως 25 και 26–30 ετών, οι συντελεστές ανά αριθμό εξαρτώμενων τέκνων και η μείωση φόρου του άρθρου 16 ΚΦΕ.
- Στο αποτέλεσμα εμφανίζονται βασικός μικτός μισθός, τακτικές κρατήσεις, τυχόν δόση δικαιώματος εγγραφής ΜΤΠΥ, ετήσιο φορολογητέο, φόρος κλίμακας, μείωση φόρου, ετήσιος/μηνιαίος φόρος και εκτιμώμενο καθαρό ποσό.
- Η εκτίμηση αφορά αποκλειστικά τον βασικό μισθό και 12μηνη αναγωγή· δεν προσθέτει οικογενειακή παροχή, επίδομα θέσης, παραμεθόριο, προσωπική διαφορά ή άλλες αποδοχές.
- Η νέα λογική βρίσκεται στο `includes/salary-net-calculations.js` και καλύπτεται από regression tests φορολογίας/κρατήσεων και UI contracts.

## 2026-09-09 — Βασικός μικτός μισθός στον υπολογιστή Μ.Κ.
- Το `ypologismos-misthologikou-klimakiou.php` εμφανίζει πλέον μαζί με το τελικό Μ.Κ. και τον αντίστοιχο **βασικό μισθό (μικτά)** από 01/04/2026.
- Τα ποσά ΠΕ/ΤΕ/ΔΕ/ΥΕ προέρχονται από την εγκύκλιο ΥΠΕΘΟΟ 54692 ΕΞ 2026/03-04-2026 (ΑΔΑ: ΨΕ7ΨΗ-ΚΧΧ), Παράρτημα Πίνακες 1–4.
- Ο βασικός μισθός διακρίνεται ρητά από τις συνολικές μικτές αποδοχές· η νεότερη ενότητα καθαρών υπολογίζει ενδεικτικά μόνο πάνω σε αυτόν και δεν προσθέτει επιδόματα ή προσωπική διαφορά.
- Η αντιστοίχιση Μ.Κ. → βασικού μισθού έχει μεταφερθεί στο κοινό `includes/salary-scale-calculations.js` και καλύπτεται από regression test.

## Κεντρική Εργαλειοθήκη — κατηγορίες & ξεχωριστές προθεσμίες (2026-09-08)
- Η `ergaleia.php` οργανώνει πλέον τα 32 εργαλεία σε 5 βασικές οικογένειες, με category cards, ομαδοποιημένες ενότητες και διατήρηση της αναζήτησης.
- Προστέθηκε single source of truth `includes/tools-catalog.php` και κοινός renderer `includes/components/tool-card.php`.
- Οι προθεσμίες μεταφέρθηκαν σε αυτοτελή `prothesmies.php`, με δεδομένα στο `includes/deadlines.php`.
- Το κοινό header διαθέτει πλέον «Όλα τα εργαλεία», «Προθεσμίες» και menu «Κατηγορίες».
- Τεκμηρίωση: `docs/audits/TOOLS-DIRECTORY-NAVIGATION-2026-09-08.md`.

## 2026-09-06 — όριο 120 βασικών τμημάτων / καθαρότερη ένδειξη Β΄ ανάθεσης
- Το ενεργό τεχνικό όριο ασφαλείας του `ypologismos-didaktikon-anagkon.php` μειώθηκε από 150 σε **120 βασικά τμήματα συνολικά (Α΄ + Β΄ + Γ΄)**, περίπου 3.500 μαθητές.
- Το ίδιο όριο εφαρμόζεται backend, στα native `max` των πεδίων, στον live frontend guard και στον importer μητρώου CSV.
- Η τεχνική προειδοποίηση «χαμηλότερη προτεραιότητα» δεν εμφανίζεται πλέον δίπλα στη Β΄/Γ΄ ανάθεση· η ίδια η ένδειξη της ανάθεσης είναι επαρκής. Οι εσωτερικές πληροφορίες προτεραιότητας διατηρούνται για audits/optimizer.

## 2026-09-06 — καθαρισμός ειδικών γραμμών Καρτέλας 6
- Οι εσωτερικοί κωδικοί `GYM_SKILLS` / `GYM_TECHNOLOGY` παραμένουν μόνο ως machine keys (`report_key`, data attributes) και δεν εμφανίζονται πλέον στον πίνακα ή στην εκτύπωση.
- Οι γραμμές εμφανίζονται με τα ανθρώπινα labels «ΔΕΞΙΟΤΗΤΕΣ ΓΥΜΝΑΣΙΟΥ» / «ΤΕΧΝΟΛΟΓΙΑ ΓΥΜΝΑΣΙΟΥ».
- Αφαιρέθηκε το bold από ολόκληρη την ειδική γραμμή· τονίζεται μόνο η πρώτη στήλη, ώστε η τυπογραφία να είναι συνεπής με τις κανονικές ειδικότητες.

## 2026-09-06 — hard cap 150 βασικών τμημάτων / frontend overflow guard
- Το τεχνικό όριο ασφαλείας μειώθηκε από 200 σε **150 βασικά τμήματα συνολικά (Α΄ + Β΄ + Γ΄)**, περίπου 4.500 μαθητές.
- Το όριο εφαρμόζεται σε backend, `school_registry_v1` validation και session restore.
- Τα πεδία βασικών τμημάτων έχουν native `max=150`, `inputmode=numeric` και client-side guard που απορρίπτει εκθετική/προσημασμένη/δεκαδική εισαγωγή.
- Πολύ μεγάλοι ακέραιοι περιορίζονται πριν περάσουν από `parseInt`, και η ενεργή τιμή κάθε πεδίου περιορίζεται δυναμικά στο υπόλοιπο του συνολικού ορίου των 150. Έτσι δεν μπορεί να εμφανιστεί overflow/scientific-notation total όπως `2e+35`.


## ypologismos-didaktikon-anagkon — ενεργό Εσπερινό ΓΕΛ (2026-09-06)

- Ενεργοποιήθηκε το `esperino_gel` ως πλήρες `school_profile` με κοινό personnel pool, workload matrix και αυτόματη πρόταση κατανομής.
- Η Β΄ τάξη χειρίζεται ρητά το επιλεγμένο τετράμηνο, ώστε Χημεία / Βιολογία να υπολογίζονται ως **1/2** ή **2/1** ώρες χωρίς αυθαίρετο ετήσιο μέσο όρο.
- Οι ομάδες προσανατολισμού Β΄/Γ΄, η επιλογή Μαθηματικών ή Βιολογίας στη Γ΄ Θετικών/Υγείας και τα υπό προϋποθέσεις Μαθηματικά/Ιστορία της Γ΄ μοντελοποιούνται ως πραγματικές ομάδες διδασκαλίας.
- Το Εσπερινό ΓΕΛ δεν ζητά δεύτερη ξένη γλώσσα.
- Πηγή ωρολογίου: Υ.Α. 43706/Δ2/07-04-2026, ΦΕΚ Β΄ 2102/09-04-2026.

## ypologismos-didaktikon-anagkon — άμεσος importer myschool stat4_8 προσωπικού (2026-09-06)
- Η Καρτέλα 3 δέχεται πλέον **απευθείας το αυθεντικό `stat4_8` σε ZIP ή CSV**. Δεν απαιτείται προεπεξεργασία του export και δεν γίνεται upload στον server.
- Το ZIP αποσυμπιέζεται client-side, το CSV αποκωδικοποιείται με UTF-8 / Windows-1253 fallback και μετατρέπεται σε προσωρινό `dde_staff_registry_v1` στο `sessionStorage`.
- Από το αρχικό myschool διατηρούνται μόνο τα απολύτως απαραίτητα στοιχεία για στελέχωση: `school_code`, ονοματεπώνυμο, κύρια/2η ειδικότητα, ρόλος και στοιχεία ωραρίου. Α.Μ., Α.Φ.Μ., στοιχεία επικοινωνίας, διευθύνσεις και υπηρεσιακές πράξεις δεν αποθηκεύονται στο normalized registry.
- Για κάθε σχολείο το `Υ.Ω.` της εφαρμογής προκύπτει από `Υποχρεωτικό Διδακτικό Ωράριο Υπηρέτησης − Μείωση Ωραρίου`, ενώ οι «Ώρες αλλού» προκύπτουν από τη διαφορά του αποτελεσματικού Υ.Ω. και των ωρών Υ.Ω. στον συγκεκριμένο φορέα. Ώρες φορέα πάνω από το αποτελεσματικό Υ.Ω. δεν θεωρούνται αυτόματα υπερωρία/διαθεσιμότητα.
- Διευθυντής/Υποδιευθυντής διατηρεί τον ρόλο του, αλλά όταν η προέλευση είναι `myschool_stat4_8` χρησιμοποιείται ρητά το πραγματικό αποτελεσματικό Υ.Ω. της πηγής και εμφανίζεται η προέλευση. Η χειροκίνητη καταχώριση εξακολουθεί να χρησιμοποιεί την υπάρχουσα αυτόματη κανονιστική λογική.
- Μετά τη μία εισαγωγή του αρχείου ΔΔΕ, η εφαρμογή φιλτράρει το μητρώο με τον πραγματικό `school_code` και φορτώνει με ένα κλικ το προσωπικό της τρέχουσας σχολικής μονάδας.
- Προστέθηκε προαιρετική λήψη καθαρισμένου `dde_staff_registry_v1` CSV χωρίς τα άχρηστα/ευαίσθητα πεδία.
- Το πραγματικό αρχείο 06-09-2026 ελέγχθηκε end-to-end: 858 τοποθετήσεις, 781 μοναδικοί εκπαιδευτικοί και 39 μονάδες. Το raw dataset δεν ενσωματώνεται στο project.

## ypologismos-didaktikon-anagkon — ενσωμάτωση myschool stat3_10 ξένων γλωσσών (2026-09-06)
- Ο ενσωματωμένος «Κατάλογος ΔΔΕ Κέρκυρας 2026-27» εμπλουτίστηκε με τις πραγματικές ομάδες Γαλλικών/Γερμανικών (και ρητό 0 όπου δεν υπάρχει ομάδα) από το `stat3_10_2026-09-06-140018.csv`.
- Και οι 35 σχολικές μονάδες του stat3_10 αντιστοιχίστηκαν μοναδικά στους πραγματικούς κωδικούς του school registry. Και τα 24 σήμερα υποστηριζόμενα Ημερήσια Γυμνάσια/ΓΕΛ καλύπτονται.
- Στα 16 Ημερήσια Γυμνάσια δεν παραμένει πλέον εκκρεμότητα 2ης ξένης γλώσσας. Στα 8 Ημερήσια ΓΕΛ οι Α΄/Β΄ ομάδες ξένης γλώσσας είναι πλήρεις και παραμένουν μόνο οι ειδικές ομάδες Γ΄ που δεν δίνονται από το stat3_10.
- Τα Αγγλικά του stat3_10 χρησιμοποιήθηκαν ως cross-check: στις 24 υποστηριζόμενες μονάδες συμφωνούν με τα βασικά τμήματα ανά τάξη και δεν διπλοαποθηκεύονται στο schema.
- Προστέθηκε καθαρό, code-resolved audit dataset `data/myschool-stat3_10-language-groups-dde-kerkyras-2026-09-06.csv` χωρίς προσωπικά δεδομένα.

## ypologismos-didaktikon-anagkon — Καρτέλα 5 «Κενά μαθημάτων» (2026-09-06)
- Προστέθηκε 5η καρτέλα που εμφανίζει ζωντανά τις ακάλυπτες ώρες ανά πραγματικό τμήμα/ομάδα και μάθημα μετά την τρέχουσα κατανομή.
- Η καρτέλα δείχνει τους κλάδους Α΄/Β΄/Γ΄/ειδικής ανάθεσης και αν υπάρχει στο τρέχον μητρώο επιλέξιμος εκπαιδευτικός με διαθέσιμο υπόλοιπο ωραρίου.
- Τα slots χωρίς επιλέξιμο εκπαιδευτικό, τα οποία δεν εμφανίζονται στις επιλογές της Καρτέλας 4, παραμένουν ορατά στην Καρτέλα 5 ως ακάλυπτες ώρες.
- Η λίστα ενημερώνεται client-side χωρίς νέο server request και διαθέτει φίλτρο τάξης/μαθήματος/κλάδου.
- Οι απλές αναθέσεις μέσω κύριας ειδικότητας εμφανίζονται πλέον λιτά ως `Α΄/Β΄/Γ΄ ανάθεση`. Η ένδειξη `μέσω 2ης ειδικότητας ΠΕxx` εμφανίζεται μόνο όταν πράγματι χρησιμοποιείται η 2η ειδικότητα.
- Δεν άλλαξε η κανονιστική λογική αναθέσεων. Η Καρτέλα 5 παραμένει εργαλείο ελέγχου/αναζήτησης και όχι επίσημη πράξη προσδιορισμού λειτουργικών κενών.

## ypologismos-didaktikon-anagkon — school_registry_v1 / multi-school CSV (2026-09-06)
- Η Καρτέλα 1 μπορεί να διαβάζει τοπικά CSV με πολλαπλές σχολικές μονάδες, μία γραμμή ανά σχολείο, χωρίς upload ή server request.
- Προστέθηκε portable schema `school_registry_v1` και λήψη προτύπου CSV από το UI.
- Στην τρέχουσα έκδοση φορτώνεται μία σχολική μονάδα κάθε φορά στη φόρμα, ενώ όλο το μητρώο του CSV παραμένει διαθέσιμο στον browser για εναλλαγή σχολείου.
- Υποστηριζόμενοι τύποι για υπολογισμό: Ημερήσιο Γυμνάσιο και Ημερήσιο ΓΕΛ.
- Τα **Εσπερινό Γυμνάσιο** και **Εσπερινό ΓΕΛ** είναι πλέον ενεργά profiles του υπολογιστή και μπορούν να φορτωθούν και από `school_registry_v1`. Παραμένουν προσωρινά ανενεργά placeholders για ΕΠΑΛ, Π.ΕΠΑΛ, ΕΝ.Ε.Ε.ΓΥ.-Λ., Ε.Ε.Ε.ΕΚ., Μουσικό και Καλλιτεχνικό Σχολείο.

## myschool validation ΕΠΑ.Λ./Π.ΕΠΑ.Λ. (v21)
- Επιχειρησιακή διασταύρωση με εκτυπώσεις myschool ΕΠΑ.Λ. και Π.ΕΠΑ.Λ. Κέρκυρας 2026-2027. Το myschool χρησιμοποιείται μόνο ως έλεγχος ονοματολογίας/δομής και όχι ως κανονιστική πηγή.
- Διορθώθηκε στην Β΄ ΕΠΑ.Λ. ο τίτλος `Ιστορία τέχνης` σε `Ιστορία της Τέχνης`, σύμφωνα με το dataset αναθέσεων.
- Προστέθηκαν context-scoped aliases στη Β΄ Π.ΕΠΑ.Λ. Ηλεκτρολογίας για `Ηλεκτροτεχνία` και `Εισαγωγή στα Υπολογιστικά Συστήματα και Δίκτυα`, χωρίς αλλαγή των δημόσιων τίτλων κάθε ΦΕΚ.
- Τα placeholders Β΄ ΕΠΑ.Λ./Εσπερινού ΕΠΑ.Λ. (`Ειδικό Εργαστηριακό Μάθημα`, `Ειδικό Μάθημα Α/Β`) και Β΄ Π.ΕΠΑ.Λ. (`Ειδικό Μάθημα Α/Β`, ναυτικό `Ειδικό Μάθημα (1 από 2)`) ταξινομούνται ως `choice_dependent` με πραγματικές επιλογές.
- Κάθε choice target ελέγχεται από το cross-audit ότι οδηγεί σε υπαρκτή γραμμή αναθέσεων στο ίδιο σχολείο/τάξη.
- Cross-audit: 1.956/2.023 resolved, 16 choice-dependent, 17 regulatory gaps, 1.989/2.023 συνολικά ταξινομημένες περιπτώσεις.
- `teaching-timetable-cross-audit-contract.py`: 796/796 PASS. Πλήρες project suite: 42/42 scripts PASS.

## Cross-audit ΕΝ.Ε.Ε.ΓΥ.-Λ. (v20)
- Διασταύρωση ωρολογίου ΦΕΚ Β΄ 2149/2026 με αναθέσεις ΦΕΚ Β΄ 3216/2026 και επιχειρησιακό έλεγχο ονοματολογίας μέσω λίστας myschool (μη κανονιστική πηγή).
- Διορθώθηκαν `EXCELL` → `EXCEL` και η Δ΄ ειδικότητα Υπαλλήλου Τουριστικών Επιχειρήσεων σε `Γαλλικά ή Γερμανικά`.
- Προστέθηκαν context-scoped aliases για `Στοιχεία Δικαίου` και `Σχέδιο Δομικών Έργων με χρήση Η/Υ`.
- Τα `Ειδικό εργαστηριακό μάθημα` και `Ειδικό Μάθημα Α/Β` καταγράφονται ως choice-dependent slots.
- 17 περιπτώσεις της Γ΄ τάξης καταγράφονται ρητά ως κανονιστικό κενό μεταξύ νέου ωρολογίου και πίνακα αναθέσεων Β΄/Γ΄, χωρίς αυθαίρετη κληρονόμηση αναθέσεων από τη Δ΄.
- Νέο `eneegyl-2026-crosswalk-contract.py`.

# v3.20.14-rc2 — Public educational service 120-month cap

Upload the six PHP files and `includes/service-calculations.js`.

This hotfix makes the public/regular educational service limit explicit and enforced at 120 months in all six ASEP calculators. It does not change the existing total service cap of 120 points and does not touch SDE/Onaseia/secondment tools.


## v2.16 — ΕΠΑ.Λ. phase 3
- Προστέθηκε ο Τομέας Διοίκησης και Οικονομίας της Β΄ ΕΠΑ.Λ. (8 μαθήματα), με ειδικές προτεραιότητες του ΦΕΚ Β΄ 1664/2018.
- Ημερήσιο και Εσπερινό ΕΠΑ.Λ. χρησιμοποιούν το ίδιο dataset μέσω του υπάρχοντος mirror.
- Ενημερώθηκαν οι ενδείξεις κάλυψης στο UI.

## Ωρολόγιο πρόγραμμα — Γ΄ ΕΠΑ.Λ./Π.ΕΠΑ.Λ. (v14)
- Προστέθηκε η Γ΄ τάξη Ημερήσιου ΕΠΑ.Λ., τριετούς Εσπερινού ΕΠΑ.Λ. και Π.ΕΠΑ.Λ.
- Προστέθηκε δυναμική ακολουθία επιλογών Τομέας → Ειδικότητα για 35 ειδικότητες.
- Οι ώρες ειδικότητας ελέγχονται σε 23 ώρες (Ημερήσιο ΕΠΑ.Λ./Π.ΕΠΑ.Λ.) και 20 ώρες (Εσπερινό ΕΠΑ.Λ.), με συνολικά προγράμματα 35/30/35 ωρών αντίστοιχα.
- Διατηρούνται οι ενδείξεις Θ/Ε/Σ/ΠΑ και οι ειδικές σημειώσεις Πρακτικής Άσκησης των Π.ΕΠΑ.Λ.
- Η μελλοντική σύνδεση με αναθέσεις/υποχρεωτικό ωράριο παραμένει αποκλειστικά ως εσωτερικό σχόλιο και δεν εμφανίζεται στο public UI.

## myschool validation βασικών δομών (v24)
- Αντιπαραβολή με εκτυπώσεις myschool 2026-2027 για Ημερήσιο Γυμνάσιο, Ημερήσιο ΓΕΛ, Εσπερινό Γυμνάσιο, Εσπερινό ΓΕΛ και τριετές Εσπερινό ΕΠΑ.Λ. Κέρκυρας. Το myschool χρησιμοποιείται μόνο ως operational cross-check και όχι ως κανονιστική πηγή.
- Η 2η ξένη γλώσσα Ημερήσιου Γυμνασίου ταξινομείται πλέον ως `choice_dependent`: Γαλλικά→ΠΕ05, Γερμανικά→ΠΕ07, Ιταλικά→ΠΕ34, με διατήρηση της ειδικής προϋπόθεσης ΠΕ34 του ΦΕΚ Β΄ 2132/2026.
- Η 2η ξένη γλώσσα Ημερήσιου ΓΕΛ ταξινομείται ως `choice_dependent`: Γαλλικά→ΠΕ05, Γερμανικά→ΠΕ07.
- Στο Εσπερινό Γυμνάσιο διατηρούνται τα Αγγλικά 2/2/2 του ΦΕΚ Β΄ 2106/2026, παρότι απουσιάζουν από τη συγκεκριμένη τοπική εκτύπωση myschool.
- Στο Εσπερινό ΓΕΛ διατηρούνται ακριβώς οι κατανομές Β΄ τάξης Χημείας 1/2 και Βιολογίας 2/1 ανά τετράμηνο.
- Στη Γ΄ Εσπερινού ΕΠΑ.Λ. / Υπάλληλο Τουριστικών Επιχειρήσεων η δεύτερη ξένη γλώσσα ταξινομείται ως `choice_dependent` με τις τέσσερις επιλογές του ΦΕΚ Β΄ 2122/2018: Γαλλικά→ΠΕ05, Γερμανικά→ΠΕ07, Ισπανικά→ΠΕ40, Ιταλικά→ΠΕ34.
- Το ΦΕΚ Β΄ 2122/2018 προστέθηκε στις δημόσιες πηγές Ωρολογίου και Αναθέσεων. Το myschool δεν προστίθεται ως δημόσια/κανονιστική πηγή.
- Νέο `general-structures-2026-myschool-cross-audit-contract.py` για προστασία των παραπάνω invariants.
- Cross-audit μετά την ταξινόμηση των language slots: 1.960/2.023 resolved, 28 choice-dependent, 29 regulatory gaps, 2.017/2.023 συνολικά ταξινομημένες περιπτώσεις. Παραμένουν μόνο τα 6 blocks της Α΄ Π.ΕΠΑ.Λ.
- `general-structures-2026-myschool-cross-audit-contract.py`: 70/70 PASS. `teaching-timetable-cross-audit-contract.py`: 905/905 PASS. Πλήρες project suite: 45/45 scripts PASS, PHP lint: 64/64.

## Cross-audit Α΄ Π.ΕΠΑ.Λ. — θεματικές ενότητες (v25)
- Τα έξι Μαθήματα Επαγγελματικής Κατεύθυνσης Προσανατολιστικού Χαρακτήρα της Α΄ Π.ΕΠΑ.Λ. ταξινομούνται πλέον ως `thematic_dependent` και όχι ως απλά aliases ή αταξινόμητες εγγραφές.
- Το ΦΕΚ Β΄ 3470/2021 διατηρεί τα έξι ενιαία ωρολογιακά blocks (2Ε/3Ε, σύνολο 13 ώρες). Δεν επινοούνται ώρες ανά θεματική ενότητα.
- Η σύνδεση με τις Αναθέσεις γίνεται μέσω του ακριβούς `assignment_section` του ΦΕΚ Β΄ 4367/2021. Το ΦΕΚ Β΄ 7403/2023 διευκρινίζει ότι η ανάθεση γίνεται με βάση τη θεματική ενότητα, τη συνάφεια βασικού τίτλου/εξειδικευμένων προσόντων και από τον Σύλλογο Διδασκόντων μετά από εισήγηση του/της Διευθυντή/ντριας, ενώ προβλέπεται και δυνατότητα συνδιδασκαλίας για διεπιστημονική προσέγγιση.
- Η εκτύπωση myschool Π.ΕΠΑ.Λ. Κέρκυρας επιβεβαιώνει επιχειρησιακά ότι τα έξι αντικείμενα εμφανίζονται ως ενιαία blocks· χρησιμοποιείται μόνο ως operational cross-check και όχι ως κανονιστική πηγή.
- Στις δημόσιες πηγές του Ωρολογίου προστέθηκαν τα ΦΕΚ Β΄ 4367/2021 και Β΄ 7403/2023 για τη διασταύρωση των αναθέσεων της Α΄ Π.ΕΠΑ.Λ.
- Τα νέα `assignment_*` metadata παραμένουν αποκλειστικά server-side μέσω `weeklyTimetablePublicRows()`.
- Το cross-audit ταξινομεί πλέον και τις 2.197/2.197 περιπτώσεις. Η πλήρης ταξινόμηση δεν σημαίνει ότι όλες έχουν διαθέσιμη ανάθεση: τα 32 επιβεβαιωμένα κανονιστικά `regulatory_gap` παραμένουν σκόπιμα κενά και φέρουν machine-readable metadata που απαγορεύει αυθαίρετο δανεισμό ανάθεσης από άλλη τάξη/ειδικότητα ή από καταργημένη απόφαση.

## Εσωτερικό Teaching Workload Model (2026-09-05)
- Προστέθηκε το server-side `includes/teaching-workload-model.php`, χωρίς καμία αλλαγή στο public UI των `anatheseis-mathimaton.php` και `orologio-programma-mathimaton.php`.
- Το μοντέλο δημιουργεί μία σταθερή εγγραφή ανά `course_id@τάξη` και ενώνει: ωρολόγιο πρόγραμμα → ώρες → κανονιστικό context → Α΄/Β΄/Γ΄ ανάθεση.
- Και οι 2.197/2.197 περιπτώσεις τάξης ταξινομούνται χωρίς unresolved/ambiguous mapping: 1.931 `direct`, 99 `alias`, 80 `components`, 48 `choice_dependent`, 7 `thematic_dependent`, 32 `regulatory_gap`.
- Οι 80 συνδυασμένες Θ/Ε γραμμές διατηρούνται ως 160 ξεχωριστά assignment targets, όλα επιλυμένα. Δεν εφαρμόζεται μία ανάθεση αυθαίρετα σε ολόκληρο το Θ+Ε.
- Οι 48 choice-dependent περιπτώσεις παράγουν 770 πραγματικές επιλογές και 770/770 επιλύονται σε υπαρκτές αναθέσεις. Οι branch-specific ξένες γλώσσες περιορίζονται στον πραγματικό κλάδο της επιλογής.
- Τα 6 blocks της Α΄ Π.ΕΠΑ.Λ. παραμένουν `thematic_dependent`: συνδέονται με τις πραγματικές θεματικές αναθέσεις, χωρίς επινοημένη κατανομή ωρών ανά υποενότητα.
- Τα 32 επιβεβαιωμένα `regulatory_gap` παραμένουν hard stop χωρίς assignment payload και διατηρούν inference guard.
- Για 6 περιπτώσεις με ωράριο που αλλάζει ανά τετράμηνο διατηρείται `period_hours` και `hours_mode=periodic`. Δεν εκτίθεται παραπλανητικό σταθερό `hours_total`.
- Η context resolution λαμβάνει υπόψη ειδικότητα/τομέα/ομάδα και όχι μόνο τον τίτλο. Έτσι ομώνυμα μαθήματα, όπως «Στοιχεία Ψύξης - Κλιματισμού», επιλύονται στη σωστή ανάθεση της συγκεκριμένης ειδικότητας.
- Νέο `tests/teaching-workload-model-contract.py`: 7383/7383 PASS. Στοχευμένα cross-audits/regressions παραμένουν πράσινα και PHP lint 66/66.

## Εσωτερικό Teaching Workload Aggregation ανά κλάδο (2026-09-05)
- Προστέθηκε το server-side `includes/teaching-workload-aggregation.php`, χωρίς καμία αλλαγή στο public UI.
- Κάθε κλάδος μπορεί πλέον να ερωτηθεί για τα curriculum slots στα οποία έχει Α΄/Β΄/Γ΄ ή ειδική ανάθεση, με context-aware αντιστοίχιση.
- Αριθμητικά αθροίζονται μόνο ασφαλή `fixed` slots. `choice`, `variant`, `condition`, `periodic` και `thematic` claims διατηρούνται χωριστά και δεν διογκώνουν τα totals.
- Υποστηρίζονται `A_all_pe`, `special_all_pe`, `B_all_others`, `C_all_others` και family matching (π.χ. ΠΕ87 → ΠΕ87.01) με ρητό `code_match_mode`.
- Οι Θ/Ε αναθέσεις κρατούν ακριβείς component ώρες. Οι choices διατηρούν `slot_hours` και δεν αντιγράφονται σε κάθε component όταν η κατανομή δεν είναι θεσμικά καθορισμένη.
- Τα variants Υγείας αποκτούν `variant_scope_key`, ενώ η Β΄ Π.ΕΠΑ.Λ. Υγείας διατηρεί `required=2`, `distinct=true`.
- Τα `special_notes` της Α΄ Π.ΕΠΑ.Λ. διατηρούνται πλέον στο workload payload.
- 78 ρητοί κωδικοί αναθέσεων παράγουν 11.389 aggregation claims: 8.494 fixed, 372 variant, 2.124 choice, 180 condition, 25 periodic και 194 thematic. Τα 32 regulatory gaps δεν παράγουν assignment claim.
- Νέο `tests/teaching-workload-aggregation-contract.py`: 364/364 PASS. Τα προηγούμενα workload/cross-audit regressions παραμένουν πράσινα και PHP lint 67/67.

## Ε.Ε.Ε.ΕΚ. — Ωρολόγιο, Αναθέσεις και cross-audit (2026-09-05)
- Προστέθηκε αυτοτελής δομή `eeeek` στα Αναθέσεις Μαθημάτων και στο Ωρολόγιο Πρόγραμμα, με τάξεις Α΄–ΣΤ΄.
- Το ωρολόγιο Α΄–Ε΄ βασίζεται στην Υ.Α. 57523/Γ6/2002 (ΦΕΚ Β΄ 765): 30 ώρες ανά τάξη, με 14/14/15/16/17 ώρες εργαστηριακών εξειδικεύσεων αντίστοιχα.
- Η ΣΤ΄ αποτυπώνεται ως `dynamic`/`thematic_dependent`, όχι ως ψευδές σταθερό 0ωρο: πρακτική άσκηση και εξατομικευμένη συμπλήρωση προγράμματος βάσει ν. 4415/2016.
- Οι αναθέσεις βασίζονται στην Υ.Α. 71105/Δ3/2018 (ΦΕΚ Β΄ 1761): 7 γενικά μαθήματα + 42 επίσημες ονομασίες εργαστηρίων.
- Τα 14 grade-level εργαστηριακά slots είναι `choice_dependent` απέναντι στα 42 εργαστήρια και δίνουν 588/588 resolved workshop choices.
- Νέο `tests/eeeek-2026-cross-audit-contract.py`: 50/50 PASS. Το συνολικό cross-audit είναι 2.084/2.084 classified και το workload model 7.383/7.383 PASS.
- Το αρχικό αρχείο που παραδόθηκε ως `ΕΕΕΕΚ.pdf` αφορούσε στην πραγματικότητα το ΕΝ.Ε.Ε.ΓΥ.-Λ. Κέρκυρας. Στη συνέχεια παραδόθηκε σωστό screenshot myschool του Ε.Ε.Ε.ΕΚ. Κέρκυρας και προστέθηκε το internal `includes/school-profile-eeeek-kerkyra-2026.php` (κωδικός 2441001). Οι «Εκτιμήσεις myschool» διατηρούνται ως παρατηρούμενα δεδομένα, χωρίς αυτόματη συναγωγή αριθμού τμημάτων ή τελικών ωρών στελέχωσης.
- Πλήρης τεχνική τεκμηρίωση: `docs/audits/EEEEEK-2026-CROSS-AUDIT-2026-09-05.md` και `docs/audits/EEEEEK-KERKYRA-SCHOOL-PROFILE-2026-09-05.md`.

## School Profile layer + κανόνες Ηθικής (2026-09-05)
- Προστέθηκε το εσωτερικό `includes/school-profile.php`, το οποίο μετατρέπει το γενικό workload catalog σε αποτύπωση συγκεκριμένης σχολικής μονάδας με πραγματικά τμήματα, τομείς, ειδικότητες και επιλογές. Δεν φορτώνεται από τις δημόσιες σελίδες.
- Πρώτο profile: `includes/school-profile-eneegyl-kerkyra-2026.php` για το ΕΝ.Ε.Ε.ΓΥ.-Λ. Κέρκυρας (`2411001`) με βάση snapshot myschool 2026-2027. Οι τιμές «Εκτίμηση myschool» χρησιμοποιούνται μόνο για δομική inference και **όχι** ως τελικές ώρες στελέχωσης.
- Δεύτερο profile: `includes/school-profile-eeeek-kerkyra-2026.php` για το Ε.Ε.Ε.ΕΚ. Κέρκυρας (`2441001`). Καταγράφει τις ενεργές τάξεις Α΄–ΣΤ΄, την κύρια εξειδίκευση Γεωπονίας–Τροφίμων–Περιβάλλοντος και τη Β΄ εξειδίκευση Μαγειρικής–Ζαχαροπλαστικής, χωρίς να εξάγει αριθμό τμημάτων από τις εκτιμήσεις myschool.
- Profile Γυμνασίου: Α΄/Β΄/Γ΄ = 2 τμήματα, Δ΄ = 1. Profile Λυκείου: Α΄ = 2 κοινά τμήματα, Β΄/Γ΄/Δ΄ = 1 κοινό curriculum group, με τομείς Γεωπονίας + Διοίκησης στη Β΄/Γ΄ και ειδικότητες Τεχνικός Φυτικής Παραγωγής + Υπάλληλος Τουριστικών Επιχειρήσεων στη Δ΄.
- Στην Α΄ Λυκείου οι πραγματικές επιλογές του profile είναι Αρχές Οικονομίας, Βασικές Αρχές Σύνθεσης και Γεωπονία και Αειφόρος Ανάπτυξη, και οι τρεις για τα δύο τμήματα.
- Το realization του profile έχει 158 ενεργά instances: 144 fixed staffing-eligible, 10 Ethics/Religion dependencies και 4 ενεργά regulatory gaps. Τα ασφαλή resolved curriculum hours είναι 396 και τα regulatory-gap curriculum hours 8. Με μία μη διπλομετρημένη θέση Θρησκευτικά/Ηθική ανά πραγματικό τμήμα/τάξη ανακατασκευάζεται δομικό σύνολο 417 ωρών.
- Προστέθηκε `schoolProfileAggregateByCode()` για school-specific eligibility hours ανά κλάδο. Τα αποτελέσματα παραμένουν eligibility και όχι τελική ανάθεση σε συγκεκριμένο εκπαιδευτικό.
- Προστέθηκε reusable `includes/ethics-class-formation.php` για Υ.Α. 108070/Δ2/13-08-2026 (ΦΕΚ Β΄ 5231/18-08-2026): όριο 10 απαλλασσομένων ανά τάξη, έως την πέμπτη ημέρα, παράλληλη διδασκαλία σε διακριτές αίθουσες όπου προβλέπεται και συντηρητική διαχείριση της «ισοδυναμίας» πολλαπλών τμημάτων χωρίς επινοημένο αριθμητικό αλγόριθμο.
- Ο κανόνας των 10 εφαρμόζεται στο εσωτερικό taxonomy σε Γυμνάσια/Γενικά Λύκεια (ημερήσια, εσπερινά, Μουσικά, Καλλιτεχνικά). Δεν επεκτείνεται αυτομάτως σε ΕΠΑ.Λ./Π.ΕΠΑ.Λ./ΕΝ.Ε.Ε.ΓΥ.-Λ. χωρίς ειδική κανονιστική βάση.
- Το `orologio-programma-mathimaton.php` εμφανίζει πλέον μικρή πληροφοριακή σημείωση για τον κανόνα της Ηθικής και την επίσημη Υ.Α., χωρίς αλλαγή layout. Στις εκτός επιβεβαιωμένου scope δομές εμφανίζεται ρητό guard.
- Νέα tests: `ethics-class-formation-2026-contract.py` 24/24 PASS, `school-profile-eneegyl-kerkyra-2026-contract.py` 30/30 PASS και `school-profile-eeeek-kerkyra-2026-contract.py` 47/47 PASS.

## School Profile Workload Matrix ανά κλάδο (2026-09-05)
- Προστέθηκε το εσωτερικό `includes/school-profile-workload.php`, χωρίς καμία αλλαγή στο public UI.
- Το layer μετατρέπει fixed resolved slots συγκεκριμένου school profile σε staffing eligibility matrix ανά πραγματικό leaf κλάδο, χωρίς να εμφανίζει parent/family κωδικούς ως ξεχωριστές staffing γραμμές.
- Διαχωρίζει `ordered_exclusive_top_priority_hours`, `ordered_shared_top_priority_hours`, `special_top_priority_hours` και `fallback_hours`, ώστε να μην ερμηνεύεται ένα απλό eligibility total ως τελική ανάθεση εκπαιδευτικού.
- Στο ΕΝ.Ε.Ε.ΓΥ.-Λ. Κέρκυρας οι 396 resolved ώρες χωρίζονται σε 353 ώρες κανονικής Α΄/Β΄/Γ΄ ιεραρχίας και 43 ειδικές. Από τις 353, οι 216 έχουν έναν μόνο leaf κλάδο στην κορυφαία ανάθεση και οι 137 έχουν περισσότερους από έναν ισότιμους κλάδους.
- Τα 10 active dependencies και τα 4 active regulatory gaps (8 ώρες) παραμένουν εκτός της staffing matrix.
- Το Ε.Ε.Ε.ΕΚ. Κέρκυρας παραμένει `structure_only`: επειδή δεν υπάρχει ασφαλής αριθμός τμημάτων, δεν κατασκευάζονται ψευδείς ώρες ανά κλάδο από τις «Εκτιμήσεις myschool».
- Νέο `tests/school-profile-workload-matrix-contract.py`: 49/49 PASS. Σχετικά regressions πράσινα και PHP lint 75/75.
- Τεκμηρίωση: `docs/audits/SCHOOL-PROFILE-WORKLOAD-MATRIX-2026-09-05.md`.

## Personnel workload layer — 2026-09-05

- Προστέθηκε το εσωτερικό `includes/personnel-workload.php`, χωρίς αλλαγή στο public UI.
- Συνδέει πραγματικό εκπαιδευτικό με `school_profile → workload matrix`: κλάδος, υποχρεωτικό ωράριο, ήδη δεσμευμένες ώρες, συγκεκριμένα curriculum units, προτεραιότητα ανάθεσης και υπόλοιπο.
- Το υποχρεωτικό ωράριο ΠΕ/ΤΕ και οι βασικοί ρόλοι ελέγχονται με parity απέναντι στο υπάρχον `teaching-hours-calculations.js`. Για ΔΕ δεν επιλέγεται αυθαίρετα κλίμακα Αρχιτεχνίτη/Τεχνίτη· απαιτείται ρητό input.
- Το roster plan ελέγχει eligibility, fallback αναθέσεις, ατομική υπέρβαση ωραρίου και συνολικό over-allocation ανά `unit_id`.
- Κάθε εκπαιδευτικός μπορεί να πάρει `open_eligible_units` μετά τις ήδη δοσμένες αναθέσεις, αλλά οι ευκαιρίες αυτές δηλώνονται ρητά ως επικαλυπτόμενες και όχι ως επίσημα κενά/ανάγκες.
- Το Ε.Ε.Ε.ΕΚ. Κέρκυρας παραμένει `structure_only`: καμία συναγωγή staffing hours από τις «Εκτιμήσεις myschool» χωρίς ασφαλή αριθμό τμημάτων.
- Νέο `tests/personnel-workload-contract.py`: 42/42 PASS. Συνολικό cross-audit 2191/2191, workload model 7383/7383, PHP lint 76/76.

## General Gymnasium / GEL school-profile hardening — 2026-09-05

- Προστέθηκε το internal `includes/school-profile-general-education.php` για τυπικό Ημερήσιο Γυμνάσιο και Ημερήσιο ΓΕΛ, χωρίς αλλαγή στο public UI.
- Τα school profiles ξεχωρίζουν πλέον `general_sections`, ομάδες 2ης ξένης γλώσσας, `track_sections`, επιλογές 2ου/3ου επιστημονικού πεδίου, conditional groups και πραγματικά groups Ηθικής.
- Οι Ομάδες Προσανατολισμού του ΓΕΛ δεν συναγάγονται από τα κανονικά τμήματα τάξης. Η Γ΄ Θετικών/Υγείας απαιτεί ρητό πλήθος ομάδων Μαθηματικών/Βιολογίας.
- Η Ηθική συνδέθηκε με το school-profile realization: dedicated equivalent και parallel/consolidated groups επηρεάζουν πλέον τις πραγματικές staffing ώρες, ενώ ελλιπή inputs παραμένουν dependency.
- Νέο `tests/general-gymnasium-gel-school-profile-contract.py`: 35/35 PASS. Τα προηγούμενα workload/personnel/cross-audit regressions παραμένουν πράσινα και PHP lint 77/77.

## Frontend — Υπολογισμός διδακτικών αναγκών σχολικής μονάδας (2026-09-05)
- Προστέθηκε η δημόσια σελίδα `ypologismos-didaktikon-anagkon.php` ως πρώτο simulator/test harness του `school_profile → workload` pipeline.
- Η πρώτη έκδοση υποστηρίζει Ημερήσιο Γυμνάσιο και Ημερήσιο ΓΕΛ με πραγματικά `general_sections`, ομάδες 2ης ξένης γλώσσας, Ομάδες Προσανατολισμού, επιλογές 2ου/3ου πεδίου, conditional groups της Γ΄ ΓΕΛ και inputs Ηθικής.
- Τα αποτελέσματα εμφανίζουν στον χρήστη μόνο τις ώρες επιλεξιμότητας Α΄/Β΄/Γ΄ ανά κλάδο, με αναλυτικό drill-down ανά μάθημα. Οι τεχνικές μετρικές κορυφαίας/fallback ανάθεσης παραμένουν στο εσωτερικό μοντέλο αλλά δεν προβάλλονται ως στήλες.
- Το UI δηλώνει ρητά ότι τα αθροίσματα δεν είναι επίσημα λειτουργικά κενά και δεν εκτελεί αυτόματες τοποθετήσεις. Το personnel layer παραμένει επόμενο στάδιο.
- Προστέθηκε νέα κάρτα #32 στην `ergaleia.php`.
- Νέο `tests/school-staffing-simulator-frontend-contract.py` για προστασία του frontend και των synthetic totals Γυμνασίου/ΓΕΛ.

## Frontend tab «Εκπαιδευτικοί» — 2026-09-05
- Το `ypologismos-didaktikon-anagkon.php` έχει πλέον πραγματικό τρίτο tab «Εκπαιδευτικοί» και όχι placeholder.
- Κάθε εκπαιδευτικός καταχωρίζεται σε μία συμπαγή γραμμή με κλάδο, ονοματεπώνυμο, υποχρεωτικό ωράριο, ώρες άλλης μονάδας και διαθέσιμο υπόλοιπο. Προϋπηρεσία/ρόλος ανοίγουν πτυσσόμενα.
- Το υποχρεωτικό ωράριο υπολογίζεται server-side από `personnelWorkloadNormalizePerson()` και live στο browser από τον κοινό `teaching-hours-calculations.js`.
- Υπάρχει απλή σύνοψη προσωπικού ανά κλάδο με το διαθέσιμο ωράριο στη μονάδα. Η αντιστοίχιση με πραγματικά μαθήματα γίνεται στην καρτέλα κατανομής, χωρίς πρόωρο χαρακτηρισμό «κενού/πλεονάσματος».
- Νέο `tests/staffing-personnel-tab-contract.py`. Η αυτόματη κατανομή παραμένει σκόπιμα εκτός UI.
- Τεκμηρίωση: `docs/audits/PERSONNEL-TAB-FRONTEND-2026-09-05.md`.

## 2026-09-05 — Tab «Κατανομή μαθημάτων»

Προστέθηκε χειροκίνητη κατανομή πραγματικών μαθημάτων/τμημάτων στους καταχωρισμένους εκπαιδευτικούς, με slot-level έλεγχο Α1/Α2/ομάδων, έλεγχο Α΄/Β΄/Γ΄/ειδικής ανάθεσης, ατομικού ωραρίου και διπλοκατανομής. Δεν υπάρχει ακόμη αυτόματη πρόταση τοποθέτησης. Βλ. `docs/audits/MANUAL-COURSE-ALLOCATION-TAB-2026-09-05.md`.

## Ενσωματωμένος κατάλογος ΔΔΕ Κέρκυρας + contract cleanup — 2026-09-06
- Το `school_registry_v1` διαθέτει πλέον ενσωματωμένο κατάλογο **38 σχολικών μονάδων της ΔΔΕ Κέρκυρας** με πραγματικό κωδικό Υπουργείου, ονομασία, κανονικοποιημένο τύπο και δημόσια ταχυδρομική διεύθυνση.
- Ο κατάλογος φορτώνεται client-side από το ήδη υπάρχον `school-profile-csv-import.js`, χωρίς νέο HTTP request, και μπορεί να συγχωνευθεί με υπάρχον browser registry χωρίς να χαθούν ήδη συμπληρωμένα στοιχεία τμημάτων.
- Προστέθηκε αναζήτηση με όνομα/κωδικό/τύπο/διεύθυνση και λήψη του καταλόγου ως `school_registry_v1-dde-kerkyras-2026.csv`.
- Τα σχολεία που δεν υποστηρίζει ακόμη ο υπολογιστής παραμένουν ορατά αλλά ανενεργά. Προστέθηκαν placeholders για Γυμνάσιο με Λυκειακές Τάξεις, Εσπερινό ΕΠΑΛ και Εργαστηριακό Κέντρο.
- Διορθώθηκε η κανονικοποίηση ειδικών τύπων ώστε Μουσικό, ΕΝ.Ε.Ε.ΓΥ.-Λ., Ε.Ε.Ε.ΕΚ., Π.ΕΠΑ.Λ. κ.ά. να μην αναγνωρίζονται κατά λάθος ως απλό Γυμνάσιο/ΓΕΛ. Το Γυμνάσιο Παξών (`2403010`) καταγράφεται ως Γυμνάσιο με Λυκειακές Τάξεις βάσει του επίσημου μητρώου ΠΣΔ.
- Το `id-normalization-contract.py` είναι πλέον schema-aware: τα ρητά δηλωμένα snake_case IDs του portable school-profile API θεωρούνται canonical και δεν παράγουν ψευδή failure.
- Το `service-tools-r12-contract.py` και τα άλλα contracts που έλεγχαν το πλήθος εργαλείων δεν έχουν πλέον hard-coded `31`: ελέγχουν ότι οι εμφανιζόμενοι μετρητές συμφωνούν με τον πραγματικό αριθμό `.tool-card`. Η `ergaleia.php` διορθώθηκε σε **32 εργαλεία**.
- Νέο `tests/staffing-corfu-school-directory-contract.py`: 25/25 PASS. Πλήρες Python contract suite: 66/66 PASS, JS regressions: 10/10 PASS, PHP lint: 78/78, JS syntax: 47/47.

## 2026-09-06 — preventive yellow audit fixes

- `school_registry_v1`: `school_id` and `school_code` are now validated independently for uniqueness, so the same internal ID cannot be reused with a different ministry code (and vice versa for ministry codes).
- Safety cap: at most 120 basic sections total (`Α + Β + Γ`) per school. The cap is enforced in the form (live/native validation), CSV registry import/session restore, and backend before the workload/profile model is built.
- Oversized direct POST requests do not unlock result tabs and do not build the workload matrix.
- Added `tests/staffing-school-safety-limits-contract.py` and expanded the school-registry contract for duplicate-ID/code and 120/121 boundary checks.

## 2026-09-06 — πλήρης ενσωματωμένος κατάλογος Κέρκυρας 2026-2027
- Το κουμπί «Κατάλογος ΔΔΕ Κέρκυρας 2026-27» φορτώνει πλέον τον εμπλουτισμένο `school_registry_v1` 38 σχολικών μονάδων και όχι το παλιό identity-only directory.
- Για τα υποστηριζόμενα Ημερήσια Γυμνάσια/ΓΕΛ προφορτώνονται τα διαθέσιμα βασικά τμήματα, οι χωρισμοί Πληροφορικής–Τεχνολογίας και οι Ομάδες Προσανατολισμού από το dataset 2026-2027. Τα μη τεκμηριωμένα ειδικότερα πεδία παραμένουν κενά και εμφανίζονται ως εκκρεμότητες.
- Το πλήρες dataset (62 στήλες) είναι ενσωματωμένο στο client-side JS, άρα η φόρτωση παραμένει χωρίς server request. Το κουμπί λήψης εξάγει το ίδιο πλήρες CSV ως `school_registry_v1-dde-kerkyras-2026-2027-full.csv`.
- Παλιό αποθηκευμένο identity-only registry της ίδιας ενσωματωμένης πηγής ανανεώνεται αυτόματα στο νέο dataset, ενώ πραγματικά εισαγμένο/συμπληρωμένο CSV του χρήστη διατηρεί τις τιμές του.
