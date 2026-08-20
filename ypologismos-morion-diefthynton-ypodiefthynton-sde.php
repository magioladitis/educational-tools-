<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων επιλογής Διευθυντών και Υποδιευθυντών Σχολείων Δεύτερης Ευκαιρίας (ΣΔΕ) βάσει της Υ.Α. 70621/Κ1, ΦΕΚ Β' 3037/19.06.2025.">
  <title>Μόρια Διευθυντών & Υποδιευθυντών ΣΔΕ</title>
  <style>
    :root{
      --bg:#f4f7fb;--card:#fff;--text:#18202b;--muted:#5f6b7a;--border:#dfe5ec;
      --sde:#176b55;--sde-dark:#134e40;--sde-soft:#eaf7f1;--blue:#1f6feb;--orange:#9a4d00;
      --orange-soft:#fff4e5;--red:#b42318;--red-soft:#fff0ee;--shadow:0 10px 30px rgba(28,39,55,.09)
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:var(--bg);color:var(--text);line-height:1.55}
    .page-shell{max-width:1240px;margin:0 auto;padding:28px 22px 50px}
    .hero{background:linear-gradient(135deg,#173f37 0%,#176b55 58%,#2a9474 100%);color:#fff;border-radius:20px;padding:30px;box-shadow:var(--shadow);margin-bottom:20px}
    .hero h1{margin:0 0 9px;font-size:clamp(28px,4vw,40px);line-height:1.15}
    .hero p{margin:5px 0;color:rgba(255,255,255,.94);max-width:980px}
    .hero-meta{display:flex;gap:9px;flex-wrap:wrap;margin-top:17px}.hero-meta span{background:rgba(0,0,0,.16);padding:6px 10px;border-radius:999px;font-size:13px;font-weight:bold}
    .layout{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:18px;align-items:start}
    .card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:18px;margin-bottom:16px;box-shadow:0 5px 18px rgba(28,39,55,.05)}
    .card h2{margin:0 0 5px;font-size:20px}.card h3{margin:18px 0 8px;font-size:16px}.subtitle{margin:0 0 15px;color:var(--muted);font-size:14px}
    .field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.field{border:1px solid #e5e9ef;border-radius:12px;padding:13px;background:#fbfcfe}.field.full{grid-column:1/-1}
    label{display:block;font-weight:700;margin-bottom:7px}label small{display:block;font-weight:400;color:var(--muted);margin-top:3px;line-height:1.4}
    input[type="number"],select{width:100%;padding:10px 11px;border:1px solid #cfd7e2;border-radius:9px;font-size:15px;background:#fff;color:var(--text)}
    select{cursor:pointer}.hidden{display:none!important}
    .note,.warning,.danger,.success{margin-top:13px;padding:12px 13px;border-radius:11px;font-size:14px;line-height:1.5}.note{background:#eef4ff;border:1px solid #d6e4ff;color:#174ea6}.warning{background:var(--orange-soft);border:1px solid #f0d4a8;color:#7b4900}.danger{background:var(--red-soft);border:1px solid #f3c1bc;color:#8f1f17}.success{background:var(--sde-soft);border:1px solid #b7e3cf;color:#12543f}
    .section-head{display:flex;justify-content:space-between;gap:12px;align-items:start;margin-bottom:12px}.max{font-weight:bold;color:var(--sde);white-space:nowrap}
    .results{position:sticky;top:14px}.big-total{text-align:center;padding:8px 0 13px}.big-total .number{font-size:58px;font-weight:800;line-height:1;color:var(--sde);font-variant-numeric:tabular-nums}.big-total .outof{color:var(--muted);margin-top:5px}.big-total .context{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#667085;font-weight:bold;margin-bottom:8px}
    .bar{height:11px;background:#e5e7eb;border-radius:999px;overflow:hidden;margin:12px 0}.bar div{height:100%;width:0;background:linear-gradient(90deg,#176b55,#1f6feb);transition:width .2s ease}
    .result-row{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-top:1px solid #edf0f4;font-size:14px}.result-row strong{font-variant-numeric:tabular-nums}.result-row.emphasis{font-size:15px;color:#134e40}
    .actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px}button{border:0;border-radius:10px;padding:11px 12px;font-weight:bold;cursor:pointer;font-size:14px}.primary{background:var(--sde);color:#fff}.secondary{background:#e8edf4;color:#253247}
    .breakdown-list{list-style:none;margin:0;padding:0}.breakdown-list li{display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid #edf0f4;font-size:13.5px}.breakdown-list li:last-child{border-bottom:0}.breakdown-list strong{font-variant-numeric:tabular-nums}
    .overflow-box{margin-top:12px;padding:11px;border:1px dashed #d6b980;border-radius:10px;background:#fffaf0;color:#7b4900;font-size:13px}
    .source-note{font-size:13px;color:#667085;margin-top:18px;text-align:justify}.role-chip{display:inline-block;padding:4px 9px;border-radius:999px;background:var(--sde-soft);color:var(--sde-dark);font-size:12px;font-weight:bold;margin-bottom:7px}
    details{margin-top:10px}summary{cursor:pointer;font-weight:bold;color:#174ea6}.criteria-list{margin:8px 0 0;padding-left:20px;color:#4f5967;font-size:14px}.criteria-list li{margin:5px 0}
    @media(max-width:940px){.layout{grid-template-columns:1fr}.results{position:static}}@media(max-width:650px){.page-shell{padding:16px 12px 34px}.hero{padding:24px 19px}.field-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.actions{grid-template-columns:1fr}.section-head{display:block}.max{margin-top:5px}}
  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>
<body class="edu-ui">
<main class="page-shell">
  <?php require_once __DIR__ . '/includes/header.php'; ?>

  <section class="hero">
    <h1>Μόρια Διευθυντών &amp; Υποδιευθυντών ΣΔΕ</h1>
    <p>Υπολόγισε τα μόρια επιλογής για θέσεις ευθύνης στα Σχολεία Δεύτερης Ευκαιρίας και κάνε βασικό έλεγχο των προϋποθέσεων συμμετοχής.</p>
    <div class="hero-meta">
      <span>Διευθυντής: έως 100</span>
      <span>Υποδιευθυντής: έως 75</span>
      <span>Τυπικά προσόντα: 25</span>
      <span>Συνέντευξη: μόνο Διευθυντές, έως 25</span>
      <span>ΦΕΚ Β΄ 3037/19.06.2025</span>
    </div>
  </section>

  <div class="layout">
    <div>
      <section class="card">
        <div class="section-head"><div><h2>1. Θέση &amp; βασικές προϋποθέσεις</h2><p class="subtitle">Η θέση αλλάζει αυτόματα τα ελάχιστα έτη υπηρεσίας, τις απαιτούμενες ώρες διδακτικού έργου και τα επιμέρους πλαφόν.</p></div></div>
        <div class="field-grid">
          <div class="field full">
            <label for="role">Θέση υποψηφιότητας</label>
            <select id="role" onchange="roleChanged()">
              <option value="">— Επίλεξε —</option>
              <option value="director">Διευθυντής Σ.Δ.Ε.</option>
              <option value="deputy">Υποδιευθυντής Σ.Δ.Ε.</option>
            </select>
          </div>
          <div class="field">
            <label for="permanentTeacher">Είσαι εν ενεργεία μόνιμος/η εκπαιδευτικός Π/θμιας ή Δ/θμιας;</label>
            <select id="permanentTeacher" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="educationalServiceYears">Συνολική εκπαιδευτική υπηρεσία <small id="serviceRequirement">Επίλεξε θέση για να εμφανιστεί το ελάχιστο.</small></label>
            <input type="number" id="educationalServiceYears" min="0" step="0.01" placeholder="π.χ. 12" oninput="calculate()">
          </div>
          <div class="field hidden" id="tertiaryDegreeWrap">
            <label for="tertiaryDegree">Πτυχίο τριτοβάθμιας εκπαίδευσης ή ισότιμος τίτλος</label>
            <select id="tertiaryDegree" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="assignmentEligible">Μπορείς να καλύψεις το απαιτούμενο διδακτικό έργο στα γνωστικά αντικείμενα των ΣΔΕ; <small id="assignmentRequirement">Επίλεξε θέση.</small></label>
            <select id="assignmentEligible" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field">
            <label for="computerKnowledge">Γνώση πληροφορικής / χειρισμού Η/Υ <small>Τεκμαίρεται για τον κλάδο ΠΕ86.</small></label>
            <select id="computerKnowledge" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="pe86">Ναι — είμαι ΠΕ86</option><option value="no">Όχι</option></select>
          </div>
          <div class="field">
            <label for="adultEducationExperience">Γνώση και εμπειρία στην εκπαίδευση ενηλίκων</label>
            <select id="adultEducationExperience" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field hidden" id="adminQualificationsWrap">
            <label for="adminQualifications">Σημαντικά διοικητικά προσόντα <small>Προϋπόθεση που αναφέρεται ειδικά για τους υποψήφιους Διευθυντές.</small></label>
            <select id="adminQualifications" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι / δεν είμαι βέβαιος</option></select>
          </div>
          <div class="field full">
            <label for="blockingIssue">Συντρέχει κάποιο κώλυμα συμμετοχής του άρθρου 3;</label>
            <select id="blockingIssue" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="no">Όχι</option><option value="yes">Ναι / πιθανόν</option></select>
            <details><summary>Ενδεικτικά κωλύματα</summary><ul class="criteria-list"><li>δοκιμαστική υπηρεσία, διαθεσιμότητα ή αργία,</li><li>ορισμένες τελεσίδικες ποινικές ή πειθαρχικές καταδίκες,</li><li>υποχρεωτική υπηρεσία / απαγόρευση υπηρεσιακών μεταβολών,</li><li>θητεία σε άλλη θέση που λήγει μετά την έναρξη της νέας θητείας,</li><li>ορισμένες περιπτώσεις πρόσφατης ανάκλησης/διακοπής απόσπασης,</li><li>υποχρεωτική αποχώρηση λόγω συνταξιοδότησης εντός του προβλεπόμενου έτους.</li></ul></details>
          </div>
        </div>
      </section>

      <section class="card">
        <div class="section-head"><div><h2>2. Τυπικά προσόντα</h2><p class="subtitle">Οι συναφείς μεταπτυχιακές σπουδές σε Εκπαίδευση Ενηλίκων, Συνεχιζόμενη Εκπαίδευση, Διά Βίου Μάθηση ή Διοίκηση Εκπαιδευτικών Μονάδων λαμβάνουν το μέγιστο.</p></div><div class="max">έως 25</div></div>
        <div class="field-grid">
          <div class="field">
            <label for="phd">Διδακτορικό</label>
            <select id="phd" onchange="calculate()"><option value="none">Κανένα</option><option value="relevant">Συναφές στις προβλεπόμενες κατευθύνσεις — 8</option><option value="other">Άλλη ειδίκευση — 6</option></select>
          </div>
          <div class="field">
            <label for="master">Μεταπτυχιακό / integrated master</label>
            <select id="master" onchange="calculate()"><option value="none">Κανένα</option><option value="relevant">Συναφές στις προβλεπόμενες κατευθύνσεις — 4</option><option value="other">Άλλη ειδίκευση — 2</option></select>
          </div>
          <div class="field">
            <label for="esdda">Απόφοιτος ΕΣΔΔΑ <small>+5 μόρια</small></label>
            <select id="esdda" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="secondDegree">Δεύτερο πτυχίο τριτοβάθμιας <small>+3 μόρια. Δεν λαμβάνεται υπόψη ΑΣΠΑΙΤΕ/ΣΕΛΕΤΕ μονοετούς φοίτησης.</small></label>
            <select id="secondDegree" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="language1">Ξένη γλώσσα 1</label>
            <select id="language1" onchange="languageChanged()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other1">Άλλη</option></select>
            <label class="edu-tools-sr-only" for="languageLevel1">Επίπεδο γλώσσας 1</label><select id="languageLevel1" onchange="calculate()" style="margin-top:8px"><option value="">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
            <label class="edu-tools-sr-only" for="languageAppointment1">Προσόν διορισμού γλώσσας 1</label><select id="languageAppointment1" onchange="calculate()" style="margin-top:8px"><option value="no">Δεν αποτέλεσε προσόν διορισμού</option><option value="yes">Αποτέλεσε προσόν διορισμού — 0 μόρια</option></select>
          </div>
          <div class="field">
            <label for="language2">Ξένη γλώσσα 2</label>
            <select id="language2" onchange="languageChanged()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other2">Άλλη</option></select>
            <label class="edu-tools-sr-only" for="languageLevel2">Επίπεδο γλώσσας 2</label><select id="languageLevel2" onchange="calculate()" style="margin-top:8px"><option value="">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
            <label class="edu-tools-sr-only" for="languageAppointment2">Προσόν διορισμού γλώσσας 2</label><select id="languageAppointment2" onchange="calculate()" style="margin-top:8px"><option value="no">Δεν αποτέλεσε προσόν διορισμού</option><option value="yes">Αποτέλεσε προσόν διορισμού — 0 μόρια</option></select>
          </div>
        </div>
        <div class="note">Το εργαλείο ταξινομεί αυτόματα τις δύο επιλέξιμες γλώσσες ώστε η ισχυρότερη να μοριοδοτείται ως 1η. Η ίδια γλώσσα λαμβάνεται μόνο μία φορά, στο ανώτερο επίπεδο.</div>
      </section>

      <section class="card">
        <div class="section-head"><div><h2>3. Διδακτική εμπειρία</h2><p class="subtitle">Τα όρια αλλάζουν ανά θέση. Στην ωρομίσθια απασχόληση στα ΣΔΕ και στις σχολικές μονάδες/ΣΑΕΚ/ΕΣΚ, 650 ώρες αντιστοιχούν σε ένα έτος.</p></div><div class="max" id="teachingMax">έως —</div></div>
        <div class="field-grid">
          <div class="field">
            <label for="sdeTeachingYears">Διδακτικό έργο στα ΣΔΕ — πλήρη έτη <small>Μην περιλαμβάνεις εδώ χρόνο διοικητικής θητείας που παίρνει μόρια στην ενότητα 4.</small></label>
            <input type="number" id="sdeTeachingYears" min="0" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="sdeTeachingHours">Διδακτικό έργο στα ΣΔΕ — ώρες ωρομίσθιας απασχόλησης</label>
            <input type="number" id="sdeTeachingHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="sdeTransferredYears">Έτη διοικητικής θητείας σε ΣΔΕ που δεν πήραν διοικητικά μόρια λόγω πλαφόν <small>Χρησιμοποίησέ το μόνο αν ο συγκεκριμένος χρόνος επιτρέπεται να μεταφερθεί στη διδακτική εμπειρία.</small></label>
            <input type="number" id="sdeTransferredYears" min="0" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="adultNonformalHours">Μη τυπική εκπαίδευση ενηλίκων — ώρες <small>ΝΕΛΕ, ΚΕΕ, ΚΔΒΜ, ΚΕΔΙΒΙΜ, ΚΕΚ, ΠΕΚ, ΠΕΚΕΣ, ΕΚΔΔΑ. 0,5 μόριο ανά 100 ώρες.</small></label>
            <input type="number" id="adultNonformalHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="schoolTeachingYears">Π/θμια – Δ/θμια – ΣΑΕΚ – ΕΣΚ — πλήρη διδακτικά έτη <small>Χωρίς περίοδο διοικητικής θητείας που μοριοδοτείται στην ενότητα 4.</small></label>
            <input type="number" id="schoolTeachingYears" min="0" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="schoolTeachingHours">Π/θμια – Δ/θμια – ΣΑΕΚ – ΕΣΚ — ώρες ωρομίσθιας απασχόλησης</label>
            <input type="number" id="schoolTeachingHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field full">
            <label for="schoolTransferredYears">Έτη διοικητικής θητείας που δεν πήραν διοικητικά μόρια λόγω πλαφόν και μπορούν να προσμετρηθούν ως διδακτικά <small>Μην δηλώσεις χρόνο που έχει ήδη μοριοδοτηθεί διοικητικά.</small></label>
            <input type="number" id="schoolTransferredYears" min="0" step="0.01" value="0" oninput="calculate()">
          </div>
        </div>
        <div class="warning">Δεν προσμετράται χρόνος άδειας άνευ αποδοχών, εκπαιδευτικής άδειας ή απόσπασης σε θέση με διοικητικά καθήκοντα, ούτε προϋπηρεσία που αναγνωρίστηκε κατά τον διορισμό στην τυπική εκπαίδευση. Το διδακτικό έργο στην τριτοβάθμια εκπαίδευση δεν θεωρείται διδακτικό έργο στην Εκπαίδευση Ενηλίκων.</div>
      </section>

      <section class="card">
        <div class="section-head"><div><h2>4. Διοικητική εμπειρία</h2><p class="subtitle">Η ίδια χρονική περίοδος δεν μπορεί να μοριοδοτηθεί και ως διοικητική και ως διδακτική εμπειρία.</p></div><div class="max" id="adminMax">έως —</div></div>
        <div class="field-grid">
          <div class="field">
            <label for="sdeDirectorYears">Στα ΣΔΕ ως Διευθυντής — σχολικά έτη <small>2 μόρια ανά έτος.</small></label>
            <input type="number" id="sdeDirectorYears" min="0" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="sdeDeputyYears">Στα ΣΔΕ ως Υποδιευθυντής — σχολικά έτη <small>1 μόριο ανά έτος.</small></label>
            <input type="number" id="sdeDeputyYears" min="0" step="0.01" value="0" oninput="calculate()">
          </div>
          <div class="field full">
            <label for="otherAdminYears">Σε σχολικές μονάδες Π/θμιας ή Δ/θμιας, ΣΑΕΚ ή ΕΣΚ ως Διευθυντής/Υποδιευθυντής — σχολικά έτη <small>1 μόριο ανά έτος.</small></label>
            <input type="number" id="otherAdminYears" min="0" step="0.01" value="0" oninput="calculate()">
          </div>
        </div>
        <div class="overflow-box" id="overflowHint">Επίλεξε θέση για να υπολογιστούν τα επιμέρους πλαφόν και τυχόν διοικητικός χρόνος που μένει εκτός μοριοδότησης.</div>
      </section>

      <section class="card">
        <div class="section-head"><div><h2>5. Επιμόρφωση</h2><p class="subtitle">Επιμόρφωση στις αρχές Εκπαίδευσης Ενηλίκων, σε θέματα ΣΔΕ ή στη Διοίκηση Εκπαιδευτικών Μονάδων από φορείς του δημόσιου ή ευρύτερου δημόσιου τομέα.</p></div><div class="max">έως 5</div></div>
        <div class="field-grid"><div class="field full"><label for="trainingHours">Συνολικές επιλέξιμες ώρες ολοκληρωμένων επιμορφώσεων <small>0,5 μόρια ανά 100 ώρες. Μην συμπεριλαμβάνεις επιμορφώσεις κάτω των 15 ωρών, ημερίδες, διημερίδες ή συνέδρια.</small></label><input type="number" id="trainingHours" min="0" step="1" value="0" oninput="calculate()"></div></div>
      </section>

      <section class="card hidden" id="interviewCard">
        <div class="section-head"><div><h2>6. Συνέντευξη</h2><p class="subtitle">Μόνο για τους υποψήφιους Διευθυντές. Αν δεν έχει πραγματοποιηθεί ακόμη, άφησε το πεδίο κενό για να δεις το σύνολο πριν από τη συνέντευξη.</p></div><div class="max">έως 25</div></div>
        <div class="field-grid"><div class="field full"><label for="interviewScore">Βαθμολογία συνέντευξης</label><input type="number" id="interviewScore" min="0" max="25" step="0.01" placeholder="0–25" oninput="calculate()"></div></div>
      </section>
    </div>

    <aside class="results" aria-live="polite">
      <section class="card">
        <div class="role-chip" id="roleChip">Επίλεξε θέση</div>
        <h2>Αποτέλεσμα</h2>
        <div class="big-total"><div class="context" id="totalContext">Μόρια κριτηρίων</div><div class="number" id="totalScore">0</div><div class="outof" id="totalOutOf">από 75 μόρια</div></div>
        <div class="bar"><div id="totalBar"></div></div>
        <div class="result-row"><span>Τυπικά προσόντα</span><strong id="formalScore">0 / 25</strong></div>
        <div class="result-row"><span>Διδακτική εμπειρία</span><strong id="teachingScore">0 / —</strong></div>
        <div class="result-row"><span>Διοικητική εμπειρία</span><strong id="adminScore">0 / —</strong></div>
        <div class="result-row"><span>Επιμόρφωση</span><strong id="trainingScore">0 / 5</strong></div>
        <div class="result-row hidden" id="interviewRow"><span>Συνέντευξη</span><strong id="interviewResult">— / 25</strong></div>
        <div class="result-row emphasis" id="criteriaRow"><span>Σύνολο πριν συνέντευξη</span><strong id="criteriaScore">0 / 75</strong></div>
        <div id="eligibilityStatus" role="status" aria-live="polite"></div>
        <div class="actions"><button class="primary" type="button" onclick="copySummary(this)">Αντιγραφή αποτελέσματος</button><button class="secondary" type="button" onclick="resetForm()">Μηδενισμός</button></div>
      </section>

      <section class="card"><h2>Ανάλυση μορίων</h2><div id="breakdown" class="subtitle">Συμπλήρωσε τα στοιχεία σου.</div></section>
      <section class="card"><h2>Σημαντικός κανόνας</h2><p class="subtitle">Χρόνος Διευθυντή/Υποδιευθυντή που λογίζεται και ως διδακτικός δεν μοριοδοτείται και στις δύο κατηγορίες για την ίδια περίοδο. Αν διοικητικός χρόνος μένει εκτός λόγω πλαφόν, μπορεί να προσμετρηθεί στο αντίστοιχο πεδίο διδακτικής εμπειρίας όπου προβλέπεται.</p></section>
      <section class="card"><h2>Σε περίπτωση ισοβαθμίας</h2><p class="subtitle">Στον τελικό πίνακα προηγείται ο υποψήφιος με περισσότερες μονάδες στη συνέντευξη, όπου αυτή προβλέπεται. Αν εξακολουθεί η ισοβαθμία ή δεν προβλέπεται συνέντευξη, εξετάζονται τα κριτήρια με τη σειρά που αναγράφονται στο άρθρο 4.</p></section>
    </aside>
  </div>

  <p class="source-note"><strong>Πηγές / Νομική βάση:</strong><br><strong>Πηγή:</strong> Υ.Α. 70621/Κ1, ΦΕΚ Β΄ 3037/19.06.2025 «Καθορισμός κριτηρίων και διαδικασίας επιλογής Διευθυντών και Υποδιευθυντών Σχολείων Δεύτερης Ευκαιρίας (Σ.Δ.Ε.)». Το εργαλείο παρέχει ενδεικτικό υπολογισμό· η τελική κρίση ανήκει στην αρμόδια Επιτροπή Επιλογής.</p>
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
<script src="includes/sde-leadership-calculations.js"></script>
<script>
  const $ = id => document.getElementById(id);
  const val = id => $(id).value;
  const num = id => Math.max(0, Number($(id).value || 0));
  const yes = id => val(id) === 'yes';
  const fmt = value => {
    const x = Number(value || 0);
    return x.toLocaleString('el-GR', { maximumFractionDigits: 2, minimumFractionDigits: Number.isInteger(x) ? 0 : 1 });
  };

  function collectData(){
    return {
      role: val('role'), permanentTeacher: val('permanentTeacher'), educationalServiceYears: val('educationalServiceYears'),
      tertiaryDegree: val('tertiaryDegree'), assignmentEligible: val('assignmentEligible'),
      computerKnowledge: val('computerKnowledge') === 'pe86' ? 'yes' : val('computerKnowledge'),
      adultEducationExperience: val('adultEducationExperience'), adminQualifications: val('adminQualifications'), blockingIssue: val('blockingIssue'),
      phd: val('phd'), master: val('master'), esdda: yes('esdda'), secondDegree: yes('secondDegree'),
      language1: val('language1'), languageLevel1: val('languageLevel1'), languageAppointment1: yes('languageAppointment1'),
      language2: val('language2'), languageLevel2: val('languageLevel2'), languageAppointment2: yes('languageAppointment2'),
      sdeTeachingYears: num('sdeTeachingYears'), sdeTeachingHours: num('sdeTeachingHours'), sdeTransferredYears: num('sdeTransferredYears'),
      adultNonformalHours: num('adultNonformalHours'), schoolTeachingYears: num('schoolTeachingYears'), schoolTeachingHours: num('schoolTeachingHours'), schoolTransferredYears: num('schoolTransferredYears'),
      sdeDirectorYears: num('sdeDirectorYears'), sdeDeputyYears: num('sdeDeputyYears'), otherAdminYears: num('otherAdminYears'), trainingHours: num('trainingHours'),
      interviewScore: val('interviewScore')
    };
  }

  function roleChanged(){
    const role = val('role');
    const cfg = SDELeadership.ROLE[role];
    $('tertiaryDegreeWrap').classList.toggle('hidden', role !== 'director');
    $('adminQualificationsWrap').classList.toggle('hidden', role !== 'director');
    $('interviewCard').classList.toggle('hidden', role !== 'director');
    $('interviewRow').classList.toggle('hidden', role !== 'director');
    if (role !== 'director') $('interviewScore').value = '';
    if (cfg){
      $('serviceRequirement').textContent = 'Απαιτούνται τουλάχιστον ' + cfg.minServiceYears + ' έτη.';
      $('assignmentRequirement').textContent = 'Απαιτείται δυνατότητα κάλυψης ' + cfg.requiredTeachingHours + ' ωρών διδακτικού έργου.';
      $('teachingMax').textContent = 'έως ' + cfg.max.teaching;
      $('adminMax').textContent = 'έως ' + cfg.max.admin;
    } else {
      $('serviceRequirement').textContent = 'Επίλεξε θέση για να εμφανιστεί το ελάχιστο.';
      $('assignmentRequirement').textContent = 'Επίλεξε θέση.';
      $('teachingMax').textContent = 'έως —'; $('adminMax').textContent = 'έως —';
    }
    calculate();
  }

  function languageChanged(){
    const l1 = val('language1'); const l2 = val('language2');
    if (l1 && l2 && l1 === l2 && !l1.startsWith('other')) {
      $('language2').value = '';
      $('languageLevel2').value = '';
    }
    calculate();
  }

  function renderEligibility(result){
    const box = $('eligibilityStatus');
    const e = result.eligibility;
    if (!result.role){ box.className='warning'; box.innerHTML='<strong>Επίλεξε θέση υποψηφιότητας.</strong>'; return; }
    if (e.status === 'eligible') { box.className='success'; box.innerHTML='<strong>✅ Οι βασικές προϋποθέσεις φαίνεται να πληρούνται.</strong>'; return; }
    if (e.status === 'not-eligible') { box.className='danger'; box.innerHTML='<strong>⚠️ Έλεγχος βασικών προϋποθέσεων:</strong><ul class="criteria-list">'+e.issues.map(x=>'<li>'+x+'</li>').join('')+'</ul>'; return; }
    box.className='warning'; box.innerHTML='<strong>Χρειάζονται ακόμη στοιχεία:</strong><ul class="criteria-list">'+e.missing.map(x=>'<li>'+x+'</li>').join('')+'</ul>';
  }

  function renderBreakdown(result){
    const groups = [
      ['Τυπικά προσόντα', result.formal.details], ['Διδακτική εμπειρία', result.teaching.details],
      ['Διοικητική εμπειρία', result.admin.details], ['Επιμόρφωση', result.training.details]
    ];
    let html = '';
    groups.forEach(([title, items]) => {
      html += '<h3>'+title+'</h3>';
      if (!items.length) html += '<p class="subtitle">0 μόρια</p>';
      else html += '<ul class="breakdown-list">'+items.map(i=>'<li><span>'+i.label+'</span><strong>'+fmt(i.points)+'</strong></li>').join('')+'</ul>';
    });
    if (result.role === 'director') html += '<h3>Συνέντευξη</h3><p class="subtitle">'+(result.interviewEntered ? fmt(result.interview)+' / 25' : 'Δεν έχει καταχωριστεί ακόμη.')+'</p>';
    if (result.warnings.length) html += '<div class="warning">'+result.warnings.join('<br>')+'</div>';
    $('breakdown').innerHTML = html || 'Συμπλήρωσε τα στοιχεία σου.';
  }

  function renderOverflow(result){
    if (!result.config){ $('overflowHint').textContent='Επίλεξε θέση για να υπολογιστούν τα επιμέρους πλαφόν και τυχόν διοικητικός χρόνος που μένει εκτός μοριοδότησης.'; return; }
    const o = result.admin.overflow;
    const sde = o.sdeDirector + o.sdeDeputy;
    if (!sde && !o.other){
      $('overflowHint').innerHTML='<strong>Δεν προκύπτει διοικητικός χρόνος πάνω από τα επιμέρους πλαφόν.</strong> Θυμήσου ότι ίδια περίοδος δεν δηλώνεται ταυτόχρονα ως διοικητική και διδακτική.';
    } else {
      $('overflowHint').innerHTML='<strong>Πιθανός χρόνος εκτός διοικητικής μοριοδότησης λόγω πλαφόν:</strong><br>ΣΔΕ: '+fmt(sde)+' έτη · Λοιπές δομές: '+fmt(o.other)+' έτη.<br><small>Ο χρόνος δεν μεταφέρεται αυτόματα. Εφόσον πληροί τον κανόνα του ΦΕΚ, δήλωσέ τον μόνο στο αντίστοιχο πεδίο μεταφοράς της διδακτικής εμπειρίας.</small>';
    }
  }

  function calculate(){
    const result = SDELeadership.calculate(collectData());
    const cfg = result.config;
    $('roleChip').textContent = cfg ? cfg.label : 'Επίλεξε θέση';
    $('formalScore').textContent = fmt(result.formal.total) + ' / 25';
    $('teachingScore').textContent = fmt(result.teaching.total) + ' / ' + (cfg ? cfg.max.teaching : '—');
    $('adminScore').textContent = fmt(result.admin.total) + ' / ' + (cfg ? cfg.max.admin : '—');
    $('trainingScore').textContent = fmt(result.training.total) + ' / 5';
    $('criteriaScore').textContent = fmt(result.criteria) + ' / 75';

    let main = result.criteria, denom = 75, context='Μόρια κριτηρίων';
    if (result.role === 'director') {
      $('interviewResult').textContent = result.interviewEntered ? fmt(result.interview) + ' / 25' : '— / 25';
      if (result.interviewEntered) { main = result.final; denom = 100; context='Τελικό σύνολο'; }
      else context='Πριν από τη συνέντευξη';
      $('criteriaRow').classList.remove('hidden');
    } else if (result.role === 'deputy') {
      context='Τελικό σύνολο';
      $('criteriaRow').classList.add('hidden');
    } else {
      $('criteriaRow').classList.remove('hidden');
    }
    $('totalScore').textContent = fmt(main);
    $('totalOutOf').textContent = 'από ' + denom + ' μόρια';
    $('totalContext').textContent = context;
    $('totalBar').style.width = Math.min(100, denom ? (main/denom*100) : 0) + '%';
    renderEligibility(result); renderBreakdown(result); renderOverflow(result);
    return result;
  }

  async function copySummary(button){
    const r = calculate();
    const cfg = r.config;
    const lines = ['Μόρια Διευθυντών / Υποδιευθυντών ΣΔΕ', cfg ? 'Θέση: '+cfg.label : 'Θέση: —', 'Τυπικά προσόντα: '+fmt(r.formal.total)+'/25', 'Διδακτική εμπειρία: '+fmt(r.teaching.total)+'/'+(cfg?cfg.max.teaching:'—'), 'Διοικητική εμπειρία: '+fmt(r.admin.total)+'/'+(cfg?cfg.max.admin:'—'), 'Επιμόρφωση: '+fmt(r.training.total)+'/5', 'Σύνολο κριτηρίων: '+fmt(r.criteria)+'/75'];
    if (r.role === 'director') lines.push('Συνέντευξη: '+(r.interviewEntered ? fmt(r.interview)+'/25' : 'δεν έχει καταχωριστεί'), 'Τελικό: '+(r.final == null ? '— /100' : fmt(r.final)+'/100'));
    else if (r.role === 'deputy') lines.push('Τελικό: '+fmt(r.criteria)+'/75');
    lines.push('Πηγή: Υ.Α. 70621/Κ1, ΦΕΚ Β΄ 3037/19.06.2025');
    try { await navigator.clipboard.writeText(lines.join('\n')); if(button){const old=button.textContent;button.textContent='Αντιγράφηκε ✓';setTimeout(()=>button.textContent=old,1200);} } catch(e){ alert(lines.join('\n')); }
  }

  function resetForm(){
    document.querySelectorAll('input[type="number"]').forEach(i => i.value = (i.id === 'educationalServiceYears' || i.id === 'interviewScore') ? '' : '0');
    document.querySelectorAll('select').forEach(s => {
      if (['phd','master'].includes(s.id)) s.value='none';
      else if (['esdda','secondDegree','languageAppointment1','languageAppointment2'].includes(s.id)) s.value='no';
      else s.selectedIndex=0;
    });
    roleChanged(); window.scrollTo({top:0,behavior:'smooth'});
  }

  roleChanged();
</script>
<script src="assets/common.js"></script>
</body>
</html>
