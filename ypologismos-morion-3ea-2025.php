<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Υπολογισμός μορίων 3ΕΑ/2025 και ενδεικτικός έλεγχος ένταξης στον Αξιολογικό Πίνακα Β΄ ή στον Επικουρικό Πίνακα Ειδικής Αγωγής.">
<title>Υπολογισμός μορίων 3ΕΑ/2025</title>
<style>
:root{--bg:#f4f7fb;--card:#fff;--text:#18202b;--muted:#5f6b7a;--border:#dfe5ec;--blue:#1f6feb;--blue-dark:#174ea6;--green:#18794e;--green-soft:#eaf7f0;--orange:#9a5b00;--orange-soft:#fff4e5;--purple:#6941c6;--purple-soft:#f1edff;--red:#b3261e;--red-soft:#fdecea;--shadow:0 10px 30px rgba(28,39,55,.09)}
*{box-sizing:border-box} body{font-family:Arial,Helvetica,sans-serif;background:var(--bg);margin:0;color:var(--text);line-height:1.55}.page{max-width:1100px;margin:auto;padding:28px 20px 50px}.hero{background:linear-gradient(135deg,#174ea6,#1f6feb 60%,#3b82f6);color:#fff;border-radius:22px;padding:32px;box-shadow:var(--shadow);position:relative;overflow:hidden}.hero:after{content:"";position:absolute;width:230px;height:230px;border-radius:50%;right:-70px;top:-100px;background:rgba(255,255,255,.08)}.hero h1{margin:0 0 10px;font-size:clamp(28px,5vw,42px);position:relative;z-index:1}.hero p{margin:0;max-width:820px;color:rgba(255,255,255,.93);position:relative;z-index:1}.hero-tags{display:flex;gap:8px;flex-wrap:wrap;margin-top:18px;position:relative;z-index:1}.hero-tags span{background:rgba(0,0,0,.14);padding:7px 10px;border-radius:999px;font-size:13px;font-weight:700}.back{display:inline-block;margin-bottom:15px;color:var(--blue);font-weight:700;text-decoration:none}.grid{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:18px;margin-top:20px;align-items:start}.card{background:var(--card);border:1px solid var(--border);border-radius:17px;padding:20px;box-shadow:0 4px 16px rgba(28,39,55,.05);margin-bottom:16px}.card h2{margin:0 0 6px;color:var(--blue-dark);font-size:21px}.card h3{font-size:16px;margin:20px 0 9px}.cap{margin:0 0 14px;color:var(--muted);font-size:14px}.field{display:grid;grid-template-columns:minmax(0,1fr) 180px;gap:12px;align-items:center;padding:10px 0;border-top:1px solid #eef1f6}.field:first-of-type{border-top:0}.field label{font-weight:700}.field small,.check small{display:block;color:var(--muted);font-weight:400;margin-top:2px}.field input,.field select{width:100%;padding:10px 11px;border:1px solid #cfd7e2;border-radius:10px;background:#fff;font-size:15px}.check{display:grid;grid-template-columns:26px 1fr;gap:10px;align-items:start;padding:9px 0;border-top:1px solid #eef1f6}.check input{width:19px;height:19px;margin-top:2px}.check label{font-weight:700}.note{padding:11px 12px;border-radius:11px;background:var(--orange-soft);color:#7b4900;border:1px solid #f0d4a8;font-size:13px;margin:10px 0}.info{padding:11px 12px;border-radius:11px;background:#eef4ff;color:var(--blue-dark);border:1px solid #d6e4ff;font-size:13px;margin:10px 0}.result-card{position:sticky;top:15px}.table{width:100%;border-collapse:collapse;margin-top:10px;font-size:14px}.table td{padding:8px 4px;border-top:1px solid #edf0f4}.table td:last-child{text-align:right;font-weight:700}.total{font-size:44px;font-weight:800;color:var(--blue);text-align:center;line-height:1;margin:12px 0 4px}.total-label{text-align:center;color:var(--muted);font-size:14px;margin-bottom:16px}.status{padding:14px;border-radius:12px;text-align:center;font-weight:800;margin:10px 0}.status.main{background:var(--green-soft);color:var(--green);border:1px solid #b9e3ca}.status.aux{background:var(--purple-soft);color:var(--purple);border:1px solid #d8cdf7}.status.none{background:var(--red-soft);color:var(--red);border:1px solid #f3c4bf}.priority{margin-top:10px;padding:10px;border-radius:10px;background:#eef4ff;color:var(--blue-dark);font-size:13px}.actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px}.actions button{border:0;border-radius:10px;padding:11px;font-weight:700;cursor:pointer;background:var(--blue);color:#fff}.actions button.secondary{background:#e8edf4;color:#263445}.footer{text-align:center;color:#7a8490;font-size:13px;margin-top:22px}.hidden{display:none!important}.inline-result{font-size:13px;font-weight:700;color:var(--blue-dark);margin-top:7px}.eligibility-box{margin-top:12px;padding:12px;border:1px dashed #b7c5d8;border-radius:12px;background:#fbfcfe;font-size:13px}.eligibility-box strong{display:block;margin-bottom:4px}@media(max-width:900px){.grid{grid-template-columns:1fr}.result-card{position:static}}@media(max-width:650px){.page{padding:16px 12px 35px}.hero{padding:25px 20px;border-radius:17px}.field{grid-template-columns:1fr}.actions{grid-template-columns:1fr}}
</style>
  <link rel="stylesheet" href="assets/common.css">
</head>
<body class="edu-ui">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<div class="page">
<section class="hero">
<h1>Υπολογισμός μορίων 3ΕΑ/2025</h1>
<p>Ενδεικτικός υπολογισμός μορίων για εκπαιδευτικούς ΠΕ Ειδικής Αγωγής και ταυτόχρονος έλεγχος ένταξης στον <strong>ΚΥΡΙΟ – Αξιολογικό Πίνακα Β΄</strong> ή στον <strong>ΕΠΙΚΟΥΡΙΚΟ Πίνακα</strong>.</p>
<div class="hero-tags"><span>Ακαδημαϊκά έως 120</span><span>Προϋπηρεσία έως 120</span><span>Κύριος / Επικουρικός</span><span>3ΕΑ/2025</span></div>
</section>

<div class="grid">
<main>
<section class="card">
<h2>1. Κλάδος και ένταξη σε πίνακα</h2>
<p class="cap">Ο κλάδος επηρεάζει ειδικούς κανόνες (ΠΕ61/ΠΕ71, ΠΕ11, ΠΕ86 και κλάδοι ξένων γλωσσών).</p>
<div class="field"><label for="specialty">Κλάδος / ειδικότητα</label><select id="specialty"><option value="">-- Επιλογή --</option>
<option>ΠΕ01</option><option>ΠΕ02</option><option>ΠΕ03</option><option>ΠΕ04.01</option><option>ΠΕ04.02</option><option>ΠΕ04.03</option><option>ΠΕ04.04</option><option>ΠΕ04.05</option><option>ΠΕ05</option><option>ΠΕ06</option><option>ΠΕ07</option><option>ΠΕ08</option><option>ΠΕ11</option><option>ΠΕ33</option><option>ΠΕ34</option><option>ΠΕ40</option><option>ΠΕ41</option><option>ΠΕ60</option><option>ΠΕ61</option><option>ΠΕ70</option><option>ΠΕ71</option><option>ΠΕ78</option><option>ΠΕ79.01</option><option>ΠΕ80</option><option>ΠΕ81</option><option>ΠΕ82</option><option>ΠΕ83</option><option>ΠΕ84</option><option>ΠΕ85</option><option>ΠΕ86</option><option>ΠΕ87</option><option>ΠΕ88</option><option>ΠΕ89</option><option>ΠΕ90</option><option>ΠΕ91</option>
</select></div>
<div class="info">Για ΠΕ61 και ΠΕ71 ο βασικός κλάδος είναι κλάδος Ε.Α.Ε. και οδηγεί στον Αξιολογικό Πίνακα Β΄. Για τους λοιπούς κλάδους απαιτείται προσόν εξειδίκευσης στην Ε.Α.Ε.</div>

<h3>Προσόντα για ΚΥΡΙΟ – Αξιολογικό Πίνακα Β΄</h3>
<div class="check"><input type="checkbox" id="phdEae"><label for="phdEae">Διδακτορικό στην Ε.Α.Ε. ή στη Σχολική Ψυχολογία<small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως διδακτορικό.</small></label></div>
<div class="check"><input type="checkbox" id="masterEae"><label for="masterEae">Μεταπτυχιακό στην Ε.Α.Ε. ή στη Σχολική Ψυχολογία<small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως μεταπτυχιακό.</small></label></div>
<div class="check"><input type="checkbox" id="didaskaleio"><label for="didaskaleio">Πτυχίο διετούς μετεκπαίδευσης στην Ε.Α.Ε. (Διδασκαλείο)<small>Κριτήριο ένταξης. Η προκήρυξη δεν ορίζει χωριστή πρόσθετη μοριοδότηση γι’ αυτό το πτυχίο στον πίνακα κριτηρίων.</small></label></div>
<div class="check"><input type="checkbox" id="fiveYearEae"><label for="fiveYearEae">Τουλάχιστον πενταετής αποδεδειγμένη προϋπηρεσία στην Ε.Α.Ε.<small>Χρησιμοποιείται για τον έλεγχο ένταξης. Τους μήνες προϋπηρεσίας τους δηλώνεις χωριστά παρακάτω για τη μοριοδότηση.</small></label></div>
<div class="check hidden" id="pe11QualWrap"><input type="checkbox" id="pe11Qual"><label for="pe11Qual">ΠΕ11 με προβλεπόμενη κύρια ειδικότητα Ε.Α.Ε. / Ειδικής Φυσικής Αγωγής κ.λπ.<small>Κριτήριο ένταξης και +8 μόρια.</small></label></div>

<h3>Προσόντα για ΕΠΙΚΟΥΡΙΚΟ πίνακα</h3>
<div class="check"><input type="checkbox" id="seminar400"><label for="seminar400">Σεμινάριο εξειδίκευσης Ε.Α.Ε. ≥400 ωρών και ≥7 μηνών<small>Κριτήριο ένταξης στον επικουρικό. Καλύπτει παράλληλα και το γενικό κριτήριο επιμόρφωσης ≥300 ωρών / ≥7 μηνών (+2).</small></label></div>
<div class="field"><label for="eaeMonths">Αναγνωρισμένοι μήνες προϋπηρεσίας ειδικά στην Ε.Α.Ε.<small>Μόνο για έλεγχο του ορίου των 10 μηνών του επικουρικού· δεν προστίθενται δεύτερη φορά στα μόρια.</small></label><input id="eaeMonths" class="service-months" type="number" min="0" step="1" inputmode="numeric" value="0"></div>
</section>

<section class="card">
<h2>2. Ακαδημαϊκά προσόντα</h2><p class="cap">Μέγιστο κατηγορίας Α: 120 μόρια.</p><div id="degreeValidation" class="note hidden">Ο βαθμός βασικού πτυχίου πρέπει να είναι από 5,00 έως 10,00.</div>
<div class="field"><label for="degree">Βαθμός βασικού πτυχίου (5–10)<small>Βαθμός × 2,5 · ανώτατο 25.</small></label><input id="degree" type="number" min="5" max="10" step="0.01" value="" placeholder="π.χ. 7,50"></div>
<div class="check"><input type="checkbox" id="secondDegree"><label for="secondDegree">Δεύτερο πτυχίο ΑΕΙ <small>+7 μόρια, εφόσον δεν αποτελεί τυπικό προσόν διορισμού.</small></label></div>
<div class="check"><input type="checkbox" id="phd"><label for="phd">Διδακτορικό δίπλωμα <small>+40 μόρια. Αν τσεκάρεις «Διδακτορικό ΕΑΕ» παραπάνω, ενεργοποιείται αυτόματα.</small></label></div>
<div class="field"><label for="masters">Μεταπτυχιακοί τίτλοι / integrated master<small>1 τίτλος: 20 · 2 ή περισσότεροι: 28 συνολικά. Για ΠΕ61/ΠΕ71 το βασικό πτυχίο δίνει αυτοδικαίως 20 και με επιπλέον μεταπτυχιακό η σχετική μοριοδότηση γίνεται 28.</small></label><select id="masters"><option value="0">Κανένας</option><option value="1">Ένας</option><option value="2">Δύο ή περισσότεροι</option></select></div>
<div id="pe6171Auto" class="info hidden">ΠΕ61/ΠΕ71: προστίθενται αυτοδικαίως 20 μόρια λόγω βασικού πτυχίου Ειδικής Αγωγής· με έναν ή περισσότερους επιπλέον μεταπτυχιακούς, η συγκεκριμένη μοριοδότηση γίνεται 28.</div>

<h3>Ξένες γλώσσες — έως δύο</h3>
<div class="field"><label for="lang1">1η ξένη γλώσσα</label><select id="lang1"><option value="0">Καμία</option><option value="3">Καλή (Β2) — 3</option><option value="5">Πολύ καλή (Γ1) — 5</option><option value="7">Άριστη (Γ2) — 7</option></select></div>
<div class="field"><label for="lang2">2η ξένη γλώσσα</label><select id="lang2"><option value="0">Καμία</option><option value="3">Καλή (Β2) — 3</option><option value="5">Πολύ καλή (Γ1) — 5</option><option value="7">Άριστη (Γ2) — 7</option></select></div>
<div id="languageWarning" class="note hidden"></div>
<div class="check"><input type="checkbox" id="computer"><label for="computer">Πιστοποιημένη γνώση Η/Υ <small>+4 μόρια. Δεν μοριοδοτείται στον ΠΕ86.</small></label></div>
<div class="check"><input type="checkbox" id="training"><label for="training">Επιμόρφωση ≥300 ωρών και ≥7 μηνών <small>+2 μόρια. Μοριοδοτείται μόνο μία επιμόρφωση. Το 400ωρο ΕΑΕ του επικουρικού καλύπτει αυτό το κριτήριο.</small></label></div>
</section>

<section class="card">
<h2>3. Εκπαιδευτική προϋπηρεσία</h2><p class="cap">Μέγιστο κατηγορίας Β: 120 μόρια. Δήλωσε τους μήνες χωρίς επικάλυψη μεταξύ των ειδικών κατηγοριών.</p>
<div class="note"><strong>Σημείωση 3ΕΑ/2025:</strong> Λαμβάνεται υπόψη η εκπαιδευτική προϋπηρεσία σε <strong>μήνες</strong>, χωρίς να υπολογίζονται τα υπόλοιπα ημερών. Για τον λόγο αυτό όλα τα πεδία προϋπηρεσίας δέχονται μόνο ακέραιους μήνες.</div>
<div class="note">Οι μήνες δυσπρόσιτων, τρίμηνων συμβάσεων και Ψηφιακού Φροντιστηρίου πρέπει να δηλώνονται στις αντίστοιχες ειδικές γραμμές και όχι ξανά ως κανονική δημόσια προϋπηρεσία.</div>
<div class="field"><label for="publicMonths">Κανονική δημόσια προϋπηρεσία<small>1 μόριο ανά μήνα.</small></label><input id="publicMonths" class="service-months" type="number" min="0" step="1" inputmode="numeric" value="0"></div>
<div class="field"><label for="hardMonths">Δυσπρόσιτα / καταστήματα κράτησης από 2020–21<small>2 μόρια ανά μήνα · έως 60 μήνες.</small></label><input id="hardMonths" class="service-months" type="number" min="0" max="60" step="1" inputmode="numeric" value="0"></div>

<h3>Τρίμηνες συμβάσεις — κανονική προϋπηρεσία</h3>
<div class="field"><label for="covid2020Months">Τρίμηνες συμβάσεις 2020–2021<small>1,5 μόριο ανά μήνα · έως 8 μήνες · έως 10 μόρια για το σχολικό έτος.</small></label><input id="covid2020Months" class="service-months" type="number" min="0" max="8" step="1" inputmode="numeric" value="0"></div>
<div class="field"><label for="covid2021Months">Τρίμηνες συμβάσεις 2021–2022<small>1,5 μόριο ανά μήνα · έως 7 μήνες · έως 10 μόρια για το σχολικό έτος.</small></label><input id="covid2021Months" class="service-months" type="number" min="0" max="7" step="1" inputmode="numeric" value="0"></div>

<h3>Τρίμηνες συμβάσεις σε δυσπρόσιτα / καταστήματα κράτησης</h3>
<div class="field"><label for="covidHard2020Months">Τρίμηνες σε δυσπρόσιτα 2020–2021<small>3 μόρια ανά μήνα · έως 8 μήνες · έως 20 μόρια για το σχολικό έτος.</small></label><input id="covidHard2020Months" class="service-months" type="number" min="0" max="8" step="1" inputmode="numeric" value="0"></div>
<div class="field"><label for="covidHard2021Months">Τρίμηνες σε δυσπρόσιτα 2021–2022<small>3 μόρια ανά μήνα · έως 7 μήνες · έως 20 μόρια για το σχολικό έτος.</small></label><input id="covidHard2021Months" class="service-months" type="number" min="0" max="7" step="1" inputmode="numeric" value="0"></div>

<div class="field"><label for="privateMonths">Ιδιωτική εκπαιδευτική προϋπηρεσία<small>0,9 μόρια ανά μήνα, εφόσον πληρούνται οι νόμιμες προϋποθέσεις.</small></label><input id="privateMonths" class="service-months" type="number" min="0" step="1" inputmode="numeric" value="0"></div>
<div class="field"><label for="digitalMonths">Ψηφιακό Φροντιστήριο<small>1,5 μόριο ανά μήνα · έως 15 μόρια ανά σχολικό έτος.</small></label><input id="digitalMonths" class="service-months" type="number" min="0" max="10" step="1" inputmode="numeric" value="0"></div>
</section>

<section class="card">
<h2>4. Κοινωνικά κριτήρια</h2>
<div class="field"><label for="children">Αριθμός επιλέξιμων τέκνων<small>3 μόρια ανά τέκνο.</small></label><input id="children" type="number" min="0" step="1" value="0"></div>
<div class="field"><label for="candidateDisability">Αναπηρία υποψηφίου (%)<small>Μοριοδοτείται από 50% και άνω, εφόσον πληρούνται οι προϋποθέσεις της προκήρυξης.</small></label><input id="candidateDisability" type="number" min="0" max="100" step="1" value="0"></div>
<div class="field"><label for="spouseDisability">Αναπηρία συζύγου (%)<small>Από 50% και άνω και με έγγαμο βίο τουλάχιστον 4 ετών.</small></label><input id="spouseDisability" type="number" min="0" max="100" step="1" value="0"></div>
<div class="field"><label for="childDisability">Υψηλότερο ποσοστό αναπηρίας τέκνου (%)<small>Από 50% και άνω. Αν είναι ≥67%, μπορεί να θεμελιώνει και ένταξη στον επικουρικό πίνακα.</small></label><input id="childDisability" type="number" min="0" max="100" step="1" value="0"></div>
<div class="info">Για τη μοριοδότηση αναπηρίας λαμβάνεται μόνο το υψηλότερο επιλέξιμο ποσοστό και υπολογίζεται ως ποσοστό × 0,4.</div>
<div class="check"><input id="marriageYears4Plus" type="checkbox"><label for="marriageYears4Plus">Ο έγγαμος βίος έχει διαρκέσει τουλάχιστον 4 έτη<small>Απαιτείται για τη μοριοδότηση αναπηρίας συζύγου.</small></label></div>
<div class="check"><input id="candidateMentalCondition" type="checkbox"><label for="candidateMentalCondition">Η αναπηρία του/της υποψηφίου οφείλεται, έστω και κατά ποσοστό, σε ψυχική πάθηση<small>Αν επιλεγεί, η αναπηρία του/της υποψηφίου δεν μοριοδοτείται.</small></label></div>

<div id="socialWarnings" class="note hidden"></div>
</section>

<section class="card">
<h2>5. Προτάξεις / ειδικές προτεραιότητες</h2>
<div class="check"><input type="checkbox" id="pde"><label for="pde">Πιστοποιημένη Παιδαγωγική και Διδακτική Επάρκεια<small>Δεν προσθέτει μόρια· ο υποψήφιος προτάσσεται έναντι υποψηφίων που δεν τη διαθέτουν.</small></label></div>
<div class="check"><input type="checkbox" id="braille"><label for="braille">Πιστοποιημένη επάρκεια Ελληνικής γραφής Braille<small>Προτεραιότητα για εκπαίδευση μαθητών με προβλήματα όρασης.</small></label></div>
<div class="check"><input type="checkbox" id="sign"><label for="sign">Πιστοποιημένη επάρκεια Ελληνικής Νοηματικής Γλώσσας (Ε.Ν.Γ.)<small>Προτεραιότητα για εκπαίδευση κωφών και βαρήκοων μαθητών.</small></label></div>
</section>
</main>

<aside class="card result-card" aria-live="polite">
<h2 style="text-align:center">Αποτέλεσμα</h2>
<div class="total" id="grandTotal">0.00</div><div class="total-label">συνολικά μόρια</div>
<div id="tableStatus" class="status none">Επίλεξε κλάδο</div>
<div id="eligibilityWhy" class="eligibility-box"><strong>Έλεγχος ένταξης</strong>Συμπλήρωσε τα προσόντα σου.</div>
<table class="table"><tr><td>Ακαδημαϊκά</td><td id="resAcademic">0.00 / 120</td></tr><tr><td>Προϋπηρεσία</td><td id="resService">0.00 / 120</td></tr><tr><td>Κοινωνικά</td><td id="resSocial">0.00</td></tr></table>
<div id="priorities"></div>
<div class="actions"><button type="button" id="copyBtn">Αντιγραφή</button><button type="button" class="secondary" id="resetBtn">Μηδενισμός</button></div>
<div class="note" style="margin-top:14px">Ενημερωτικός υπολογισμός βάσει της 3ΕΑ/2025. Η τελική ένταξη και μοριοδότηση προκύπτει από τον έλεγχο ΑΣΕΠ/ΟΠΣΥΔ και τα επίσημα δικαιολογητικά.</div>
</aside>
</div>
</div>
<script src="includes/service-calculations.js"></script>
<script src="includes/social-calculations.js"></script>
<script>
(function(){
 const $=id=>document.getElementById(id); const num=id=>Math.max(0,Number($(id)?.value||0)); const cap=(v,m)=>Math.min(v,m); const fmt=v=>(Math.round((v+Number.EPSILON)*100)/100).toFixed(2);
 const languageOwn={"ΠΕ05":"Γαλλική","ΠΕ06":"Αγγλική","ΠΕ07":"Γερμανική","ΠΕ34":"Ιταλική","ΠΕ40":"Ισπανική"};
 function syncSpecial(){
   const sp=$('specialty').value;
   $('pe11QualWrap').classList.toggle('hidden',sp!=='ΠΕ11');
   $('pe6171Auto').classList.toggle('hidden',!(sp==='ΠΕ61'||sp==='ΠΕ71'));
   $('computer').disabled=(sp==='ΠΕ86'); if(sp==='ΠΕ86') $('computer').checked=false;
   $('languageWarning').classList.toggle('hidden',!languageOwn[sp]);
   if(languageOwn[sp]) $('languageWarning').textContent=`Στον ${sp} δεν μοριοδοτείται η ${languageOwn[sp]} γλώσσα που αποτελεί προσόν του κλάδου. Στα πεδία γλωσσών δήλωσε μόνο άλλες μοριοδοτούμενες γλώσσες.`;
 }
 function calcAcademic(){
   const sp=$('specialty').value;
   const hasPhd = $('phd').checked || $('phdEae').checked;
   const selectedMasters = Number($('masters').value||0);
   const masters = Math.max(selectedMasters, $('masterEae').checked ? 1 : 0);
   const hasTraining = $('training').checked || $('seminar400').checked;
   const degreeGrade=num('degree');
   const validDegreeGrade=degreeGrade>=5 && degreeGrade<=10;
   let pts=validDegreeGrade ? cap(degreeGrade*2.5,25) : 0;
   if($('secondDegree').checked) pts+=7;
   if(hasPhd) pts+=40;
   if(sp==='ΠΕ61'||sp==='ΠΕ71') pts += masters>=1 ? 28 : 20;
   else pts += masters===1?20:(masters>=2?28:0);
   if(sp==='ΠΕ11' && $('pe11Qual').checked) pts+=8;
   pts += Number($('lang1').value||0)+Number($('lang2').value||0);
   if($('computer').checked && sp!=='ΠΕ86') pts+=4;
   if(hasTraining) pts+=2;
   return cap(pts,120);
 }
 function calcService(){
   let raw=0;
   raw += EducationService.regularPublic(num('publicMonths')).points;
   raw += EducationService.difficult(num('hardMonths')).points;
   raw += EducationService.threeMonthRegular2020(num('covid2020Months')).points;
   raw += EducationService.threeMonthRegular2021(num('covid2021Months')).points;
   raw += EducationService.threeMonthDifficult2020(num('covidHard2020Months')).points;
   raw += EducationService.threeMonthDifficult2021(num('covidHard2021Months')).points;
   raw += EducationService.privateSchool(num('privateMonths')).points;
   raw += EducationService.digitalPerSchoolYear(num('digitalMonths')).points;
   return cap(raw,120);
 }
 function calcSocial(){
   return EducationSocial.calculate({
     children:num('children'),
     candidateDisability:num('candidateDisability'),
     spouseDisability:num('spouseDisability'),
     childDisability:num('childDisability'),
     marriageYears4Plus:$('marriageYears4Plus').checked,
     candidateMentalCondition:$('candidateMentalCondition').checked
   });
 }
 function eligibility(socialResult){
   const sp=$('specialty').value;
   if(!sp) return {type:'none',label:'Επίλεξε κλάδο',why:'Δεν έχει επιλεγεί κλάδος / ειδικότητα.'};
   let mainReasons=[];
   if(sp==='ΠΕ61'||sp==='ΠΕ71') mainReasons.push(`${sp}: βασικός κλάδος Ε.Α.Ε.`);
   if($('phdEae').checked) mainReasons.push('διδακτορικό στην Ε.Α.Ε./Σχολική Ψυχολογία');
   if($('masterEae').checked) mainReasons.push('μεταπτυχιακό στην Ε.Α.Ε./Σχολική Ψυχολογία');
   if($('didaskaleio').checked) mainReasons.push('διετής μετεκπαίδευση στην Ε.Α.Ε.');
   if($('fiveYearEae').checked) mainReasons.push('πενταετής αποδεδειγμένη προϋπηρεσία στην Ε.Α.Ε.');
   if(sp==='ΠΕ11' && $('pe11Qual').checked) mainReasons.push('προβλεπόμενη κύρια ειδικότητα ΠΕ11 στην Ε.Α.Ε.');
   if(mainReasons.length) return {type:'main',label:'ΚΥΡΙΟΣ – Αξιολογικός Πίνακας Β΄',why:'Κριτήριο/α ένταξης: '+mainReasons.join(', ')+'.'};
   let auxReasons=[];
   if($('seminar400').checked) auxReasons.push('σεμινάριο Ε.Α.Ε. ≥400 ωρών / ≥7 μηνών');
   if(num('eaeMonths')>=10) auxReasons.push('τουλάχιστον 10 μήνες προϋπηρεσίας στην Ε.Α.Ε.');
   if(socialResult.childDisability67) auxReasons.push('γονέας παιδιού με αναπηρία ≥67%');
   if(auxReasons.length) return {type:'aux',label:'ΕΠΙΚΟΥΡΙΚΟΣ Πίνακας Ε.Α.Ε.',why:'Κριτήριο/α ένταξης: '+auxReasons.join(', ')+'.'};
   return {type:'none',label:'Δεν προκύπτει ένταξη',why:'Με τα στοιχεία που δηλώθηκαν δεν προκύπτει προσόν ένταξης ούτε στον Αξιολογικό Πίνακα Β΄ ούτε στον Επικουρικό.'};
 }
 function render(){
   syncSpecial();
   const degreeGrade=num('degree');
   const degreeInvalid=degreeGrade>0 && (degreeGrade<5 || degreeGrade>10);
   $('degreeValidation').classList.toggle('hidden', !degreeInvalid);
   const a=calcAcademic(), b=calcService(), socialResult=calcSocial(), c=socialResult.total, t=a+b+c, e=eligibility(socialResult);
   $('socialWarnings').classList.toggle('hidden', socialResult.warnings.length===0);
   $('socialWarnings').innerHTML=socialResult.warnings.map(w=>'• '+w).join('<br>');
   $('grandTotal').textContent=fmt(t); $('resAcademic').textContent=fmt(a)+' / 120'; $('resService').textContent=fmt(b)+' / 120'; $('resSocial').textContent=fmt(c);
   $('tableStatus').className='status '+e.type; $('tableStatus').textContent=e.label; $('eligibilityWhy').innerHTML='<strong>Έλεγχος ένταξης</strong>'+e.why;
   let p=[]; if($('pde').checked) p.push('Πρόταξη λόγω Παιδαγωγικής & Διδακτικής Επάρκειας'); if($('braille').checked) p.push('Προτεραιότητα Braille για μαθητές με προβλήματα όρασης'); if($('sign').checked) p.push('Προτεραιότητα Ε.Ν.Γ. για κωφούς/βαρήκοους μαθητές');
   $('priorities').innerHTML=p.map(x=>'<div class="priority">✓ '+x+'</div>').join('');
   return {a,b,c,t,e,p};
 }
 function summary(v){return ['Υπολογισμός μορίων 3ΕΑ/2025',`Πίνακας: ${v.e.label}`,v.e.why,`Ακαδημαϊκά: ${fmt(v.a)} / 120`,`Προϋπηρεσία: ${fmt(v.b)} / 120`,`Κοινωνικά: ${fmt(v.c)}`,`ΣΥΝΟΛΟ: ${fmt(v.t)}`,v.p.length?'Προτάξεις/προτεραιότητες: '+v.p.join(' · '):''].filter(Boolean).join('\n');}
 function sanitizeServiceMonthInput(el){
   if(!el || !el.classList.contains('service-months')) return;
   const maxAttr=el.getAttribute('max');
   let v=el.value === '' ? 0 : Number(el.value);
   if(!Number.isFinite(v)) v=0;
   v=Math.max(0,Math.trunc(v));
   if(maxAttr!==null && maxAttr!=='') v=Math.min(v,Number(maxAttr));
   if(String(v)!==el.value) el.value=String(v);
 }
 document.addEventListener('input',e=>{
   sanitizeServiceMonthInput(e.target);
   if(e.target && e.target.id==='children' && e.target.value!==''){
     e.target.value=String(Math.max(0,Math.floor(Number(e.target.value)||0)));
   }
   render();
 });
 document.addEventListener('change',e=>{
   sanitizeServiceMonthInput(e.target);
   if(e.target && e.target.id==='degree' && e.target.value!==''){
     let v=Number(String(e.target.value).replace(',', '.'));
     if(Number.isFinite(v)) e.target.value=String(Math.min(10,Math.max(5,v)));
     else e.target.value='';
   }
   render();
 });
 $('copyBtn').addEventListener('click',async()=>{const txt=summary(render());try{await navigator.clipboard.writeText(txt);$('copyBtn').textContent='Αντιγράφηκε';setTimeout(()=>$('copyBtn').textContent='Αντιγραφή',1200)}catch(e){alert(txt)}});
 $('resetBtn').addEventListener('click',()=>{document.querySelectorAll('input[type=number]').forEach(x=>x.value=0);$('degree').value='';document.querySelectorAll('input[type=checkbox]').forEach(x=>x.checked=false);document.querySelectorAll('select').forEach(x=>x.selectedIndex=0);render();});
 render();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js"></script>
</body>
</html>
