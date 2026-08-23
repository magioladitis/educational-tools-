<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Υπολογισμός μορίων απόσπασης μονίμων εκπαιδευτικών στα Σχολεία Δεύτερης Ευκαιρίας (ΣΔΕ) και έλεγχος αποδεκτών ειδικοτήτων/γραμματισμών σύμφωνα με το ΦΕΚ Β' 4088/03.07.2026.">
  <title>Υπολογισμός μορίων απόσπασης στα ΣΔΕ</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.9-b3">
</head>
<body class="edu-ui edu-calc-sde edu-page-sde-apospasis">
<main class="page-shell">
  <?php require_once __DIR__ . '/includes/header.php'; ?>

  <section class="hero">
    <h1>Υπολογισμός μορίων απόσπασης στα ΣΔΕ</h1>
    <p>Ενδεικτικός υπολογισμός μορίων και έλεγχος αποδεκτής ειδικότητας/γραμματισμού για απόσπαση μονίμων εκπαιδευτικών στα Σχολεία Δεύτερης Ευκαιρίας.</p>
    <div class="hero-meta">
      <span>Σύνολο: 40 μόρια</span>
      <span>Εκπαίδευση: 22</span>
      <span>Διδακτική εμπειρία: 13</span>
      <span>Άλλα προσόντα: 5</span>
    </div>
  </section>

  <div class="success sde-update-banner"><strong>Ενημερωμένος πίνακας μοριοδότησης:</strong> ο υπολογισμός εφαρμόζει τη Διόρθωση Σφάλματος του ΦΕΚ Β΄ 4199/10.07.2026 (Εκπαίδευση 22, Άλλα προσόντα 5, Η/Υ 2 και μοριοδότηση τυπικής εκπαίδευσης από το 1ο έτος).</div>

  <div class="layout">
    <div>
      <section class="card">
        <div class="section-head">
          <div><h2>1. Ειδικότητα &amp; αποδεκτοί γραμματισμοί</h2><p class="subtitle">Επίλεξε τον κλάδο σου για να εμφανιστούν οι Α΄/Β΄ αναθέσεις που προβλέπονται στο άρθρο 5.</p></div>
        </div>
        <div class="field-grid">
          <div class="field">
            <label for="specialty">Κλάδος / ειδικότητα</label>
            <select id="specialty" onchange="specialtyChanged()">
              <option value="">— Επιλογή —</option>
              <option value="PE01">ΠΕ01 Θεολόγων</option>
              <option value="PE02">ΠΕ02 Φιλολόγων</option>
              <option value="PE03">ΠΕ03 Μαθηματικών</option>
              <option value="PE04.01">ΠΕ04.01 Φυσικών</option>
              <option value="PE04.02">ΠΕ04.02 Χημικών</option>
              <option value="PE04.03">ΠΕ04.03 Φυσιογνωστών</option>
              <option value="PE04.04">ΠΕ04.04 Βιολόγων</option>
              <option value="PE04.05">ΠΕ04.05 Γεωλόγων</option>
              <option value="PE06">ΠΕ06 Αγγλικής</option>
              <option value="PE70">ΠΕ70 Δασκάλων</option>
              <option value="PE78">ΠΕ78 Κοινωνικών Επιστημών</option>
              <option value="PE80">ΠΕ80 Οικονομίας</option>
              <option value="PE85">ΠΕ85 Χημικών Μηχανικών</option>
              <option value="PE86">ΠΕ86 Πληροφορικής</option>
              <option value="PE87.01">ΠΕ87.01 Ιατρικής</option>
              <option value="PE88.01">ΠΕ88.01 Γεωπονίας</option>
              <option value="PE88.05">ΠΕ88.05 Φυσικού Περιβάλλοντος</option>
              <option value="OTHER">Άλλη ειδικότητα</option>
            </select>
          </div>
          <div class="field">
            <label for="eligibilitySchoolYears">Διδακτική υπηρεσία σε σχολεία Πρωτοβάθμιας ή Δευτεροβάθμιας Εκπαίδευσης <small>Για δικαίωμα αίτησης απαιτούνται τουλάχιστον 2 χρόνια. Το πεδίο αυτό χρησιμοποιείται μόνο για τον έλεγχο επιλεξιμότητας.</small></label>
            <input type="number" id="eligibilitySchoolYears" min="0" step="0.01" value="" inputmode="decimal" placeholder="π.χ. 2" oninput="calculate()">
          </div>
          <div class="field">
            <label for="formalEducationYears">Συνολικά πλήρη σχολικά έτη διδακτικού έργου στην τυπική εκπαίδευση <small>Πρωτοβάθμια / Δευτεροβάθμια / Τριτοβάθμια. Με τη διόρθωση Β΄ 4199/2026: 1 μόριο από το 1ο πλήρες σχολικό έτος, έως 4.</small></label>
            <input type="number" id="formalEducationYears" min="0" step="1" value="" inputmode="numeric" placeholder="π.χ. 6" oninput="calculate()">
          </div>
          <div class="field hidden" id="mathInfoDegreeWrap">
            <label for="mathInfoDegree">Πληροίς την προϋπόθεση «πτυχίο Μαθηματικών ή Πληροφορικής» που αναγράφεται για τη Β΄ ανάθεση στα Μαθηματικά;</label>
            <select id="mathInfoDegree" onchange="calculate()"><option value="no">Όχι / δεν είμαι βέβαιος</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field hidden" id="formerPE09Wrap">
            <label for="formerPE09">Το πτυχίο σου αντιστοιχεί σε πρώην ΠΕ09 ή ΠΕ15; <small>Δίνει προτεραιότητα στην Κοινωνική Εκπαίδευση για ΠΕ80.</small></label>
            <select id="formerPE09" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field hidden" id="formerPE1208Wrap">
            <label for="formerPE1208">Το πτυχίο σου αντιστοιχεί σε πρώην ΠΕ12.08; <small>Δίνει προτεραιότητα στους σχετικούς γραμματισμούς για ΠΕ85.</small></label>
            <select id="formerPE1208" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="teleEducation">Αποδέχεσαι παροχή εκπαιδευτικού έργου με σύγχρονη τηλεκπαίδευση; <small>Δεν δίνει μόρια, αλλά χρησιμοποιείται ως πρώτο κριτήριο σε περίπτωση ισοβαθμίας.</small></label>
            <select id="teleEducation" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="yes">Ναι</option><option value="no">Όχι</option></select>
          </div>
          <div class="field full">
            <label for="blockingIssue">Υπάρχει κάποιο κώλυμα υποβολής αίτησης του άρθρου 4;</label>
            <select id="blockingIssue" onchange="calculate()"><option value="">— Επίλεξε —</option><option value="no">Όχι</option><option value="yes">Ναι / πιθανόν</option></select>
            <details><summary>Ενδεικτικά κωλύματα</summary><ul class="criteria-list"><li>δοκιμαστική υπηρεσία χωρίς πράξη μονιμοποίησης, διαθεσιμότητα ή αργία,</li><li>απαγόρευση υπηρεσιακών μεταβολών ή υποχρεωτική υπηρεσία,</li><li>υποχρεωτική υπηρεσία λόγω διορισμού σε δυσπρόσιτο,</li><li>θέση στελέχους ή θέση με θητεία,</li><li>ανάκληση/διακοπή απόσπασης σε δομή της Γ.Γ.Ε.Ε.Κ. &amp; Δ.Β.Μ. μέσα στην τελευταία τριετία, όπου εφαρμόζεται.</li></ul></details>
          </div>
        </div>
        <div id="assignmentBox" class="info">Επίλεξε ειδικότητα για να δεις τους αποδεκτούς γραμματισμούς.</div>
        <div class="note"><strong>Σειρά επιλογής:</strong> στις θέσεις των ΣΔΕ εφαρμόζεται η σειρά προτεραιότητας των γραμματισμών και της Α΄/Β΄ ανάθεσης, ανεξάρτητα από το συνολικό σκορ. Στα εκτός έδρας τμήματα λαμβάνεται υπόψη μόνο το σύνολο των μορίων. Σε ισοβαθμία προηγείται αρχικά όποιος έχει αποδεχτεί σύγχρονη τηλεκπαίδευση.</div>
        <details>
          <summary>Προβολή όλων των αποδεκτών ειδικοτήτων / αναθέσεων</summary>
          <div class="mapping-wrap">
            <table class="mapping-table">
              <thead><tr><th>Γνωστικό αντικείμενο</th><th>Α΄ ανάθεση</th><th>Β΄ ανάθεση</th></tr></thead>
              <tbody>
                <tr><td>Ελληνική Γλώσσα</td><td>ΠΕ02</td><td>—</td></tr>
                <tr><td>Μαθηματικά</td><td>ΠΕ03</td><td>ΠΕ04, ΠΕ86 (με πτυχίο Μαθηματικών ή Πληροφορικής)</td></tr>
                <tr><td>Πληροφορική</td><td>ΠΕ86</td><td>—</td></tr>
                <tr><td>Αγγλική Γλώσσα</td><td>ΠΕ06</td><td>—</td></tr>
                <tr><td>Κοινωνική Εκπαίδευση</td><td>ΠΕ78</td><td>ΠΕ01, ΠΕ02, ΠΕ80 (προτεραιότητα σε πτυχία πρώην ΠΕ09 και ΠΕ15)</td></tr>
                <tr><td>Επιστημονικός Γραμματισμός</td><td>ΠΕ04, ΠΕ85 (προτεραιότητα σε πτυχία πρώην ΠΕ12.08)</td><td>ΠΕ03, ΠΕ87.01, ΠΕ88.01</td></tr>
                <tr><td>Περιβαλλοντική Εκπαίδευση</td><td>ΠΕ04.05, ΠΕ88.01, ΠΕ88.05</td><td>ΠΕ04.01, ΠΕ04.02, ΠΕ04.03, ΠΕ04.04, ΠΕ85 (προτεραιότητα σε πτυχία πρώην ΠΕ12.08)</td></tr>
                <tr><td>Τμήματα προετοιμασίας για απολυτήριο Δημοτικού</td><td>ΠΕ70</td><td>—</td></tr>
              </tbody>
            </table>
          </div>
        </details>
      </section>

      <section class="card">
        <div class="section-head"><div><h2>2. Εκπαίδευση</h2><p class="subtitle">Τυπικά προσόντα έως 18 + επιμόρφωση έως 4.</p></div><div class="max">έως 22</div></div>
        <div class="field-grid">
          <div class="field">
            <label for="phd">Διδακτορικό <small>11 μόρια στην Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου, 10 σε άλλη κατεύθυνση.</small></label>
            <select id="phd" onchange="calculate()"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 11</option><option value="other">Άλλη κατεύθυνση — 10</option></select>
          </div>
          <div class="field">
            <label for="master">Μεταπτυχιακό <small>8 μόρια στην Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου, 7 σε άλλη κατεύθυνση. Αν δηλωθεί και διδακτορικό, αυτός ο τίτλος δεν προσμετράται.</small></label>
            <select id="master" onchange="calculate()"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 8</option><option value="other">Άλλη κατεύθυνση — 7</option></select>
          </div>
          <div class="field">
            <label for="secondDegree">Δεύτερο πτυχίο Τριτοβάθμιας Εκπαίδευσης <small>+4 μόρια. Δήλωσέ το μόνο αν δεν αποτέλεσε προσόν διορισμού.</small></label>
            <select id="secondDegree" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
          <div class="field">
            <label for="secondPhd">Δεύτερο διδακτορικό <small>2 μόρια στην Εκπαίδευση Ενηλίκων κ.λπ., 1 σε άλλη κατεύθυνση.</small></label>
            <select id="secondPhd" onchange="calculate()"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 2</option><option value="other">Άλλη κατεύθυνση — 1</option></select>
          </div>
          <div class="field">
            <label for="secondMaster">Δεύτερο μεταπτυχιακό <small>1 μόριο στην Εκπαίδευση Ενηλίκων κ.λπ.· σε άλλη κατεύθυνση εφαρμόζεται η μείωση κατά 1 μόριο, άρα 0. Χρησιμοποίησέ το μόνο για διακριτό δεύτερο μεταπτυχιακό τίτλο.</small></label>
            <select id="secondMaster" onchange="calculate()"><option value="none">Κανένα</option><option value="adult">Εκπαίδευση Ενηλίκων / Συνεχιζόμενη / Διά Βίου — 1</option><option value="other">Άλλη κατεύθυνση — 0</option></select>
          </div>
          <div class="field">
            <label for="sdeTrainingHours">Ώρες ολοκληρωμένης επιμόρφωσης σε θέματα ΣΔΕ <small>0,25 μόρια ανά 100 ώρες, έως 2. Κάθε επιμέρους επιμόρφωση κάτω των 15 ωρών λαμβάνει 0 μόρια.</small></label>
            <input type="number" id="sdeTrainingHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="adultTrainingHours">Ώρες ολοκληρωμένης επιμόρφωσης στις αρχές Εκπαίδευσης Ενηλίκων <small>0,25 μόρια ανά 100 ώρες, έως 2. Κάθε επιμέρους επιμόρφωση κάτω των 15 ωρών λαμβάνει 0 μόρια.</small></label>
            <input type="number" id="adultTrainingHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
        </div>
        <div class="note"><strong>Επιμόρφωση:</strong> δήλωσε μόνο ολοκληρωμένες επιμορφώσεις από δημόσιους ή ιδιωτικούς φορείς εκπαίδευσης. Δεν μοριοδοτούνται επιμορφώσεις κάτω των 15 ωρών, ημερίδες/διημερίδες/συνέδρια ούτε επιμόρφωση που ήταν προαπαιτούμενη για πιστοποίηση εκπαιδευτών Μητρώου ΕΟΠΠΕΠ/ΕΚΕΠΙΣ.</div>
        <div class="warning">Δεν μοριοδοτείται τίτλος σπουδών που αποτέλεσε προσόν διορισμού. Αν υπάρχει διδακτορικό και μεταπτυχιακός τίτλος, το ΦΕΚ ορίζει ότι μοριοδοτείται μόνο το διδακτορικό. Το εργαλείο δεν προσμετρά τον πρώτο μεταπτυχιακό όταν δηλώνεται διδακτορικό. Το «δεύτερο μεταπτυχιακό» παραμένει ξεχωριστό πεδίο, όπως στον πίνακα του ΦΕΚ.</div>
      </section>

      <section class="card">
        <div class="section-head"><div><h2>3. Διδακτική εμπειρία</h2><p class="subtitle">Μοριοδοτείται μόνο διδακτική εμπειρία σύμφωνα με τους ειδικούς κανόνες του άρθρου 6.</p></div><div class="max">έως 13</div></div>
        <div class="field-grid">
          <div class="field">
            <label for="sdeYears">Πλήρη σχολικά έτη διδακτικής εμπειρίας σε ΣΔΕ <small>1 μόριο ανά σχολικό έτος, έως 5.</small></label>
            <input type="number" id="sdeYears" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="sdeHourlyHours">Ώρες ωρομίσθιας απασχόλησης σε ΣΔΕ <small>650 ώρες = 1 έτος = 1 μόριο. Μην καταχωρίζεις εδώ χρόνο που έχεις ήδη δηλώσει ως πλήρες έτος.</small></label>
            <input type="number" id="sdeHourlyHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label for="adultEducationHours">Ώρες διδακτικού έργου στην Εκπαίδευση Ενηλίκων εκτός ΣΔΕ <small>0,5 μόριο ανά 100 ώρες, έως 4.</small></label>
            <input type="number" id="adultEducationHours" min="0" step="1" value="0" oninput="calculate()">
          </div>
          <div class="field">
            <label>Τυπική εκπαίδευση <small>Με τη διόρθωση σφάλματος Β΄ 4199/2026: 1 μόριο ανά πλήρες σχολικό έτος, από το 1ο έτος, έως 4.</small></label>
            <div id="formalExperiencePreview" class="success">0 μόρια</div>
          </div>
        </div>
        <div class="note">Δεν προσμετράται χρόνος άδειας άνευ αποδοχών, εκπαιδευτικής άδειας ή απόσπασης σε θέση με διοικητικά καθήκοντα. Επίσης δεν μοριοδοτείται προϋπηρεσία που αναγνωρίστηκε κατά τον διορισμό στην τυπική εκπαίδευση.</div>
      </section>

      <section class="card">
        <div class="section-head"><div><h2>4. Άλλα προσόντα</h2><p class="subtitle">Έως δύο ξένες γλώσσες και γνώση χειρισμού Η/Υ.</p></div><div class="max">έως 5</div></div>
        <div class="field-grid">
          <div class="field">
            <label for="language1">Ξένη γλώσσα 1</label>
            <select id="language1" onchange="calculate()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other1">Άλλη γλώσσα</option></select>
            <label for="languageLevel1" class="edu-tools-sr-only">Επίπεδο ξένης γλώσσας 1</label><select id="languageLevel1" class="sde-language-level" onchange="calculate()"><option value="none">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
          </div>
          <div class="field">
            <label for="language2">Ξένη γλώσσα 2</label>
            <select id="language2" onchange="calculate()"><option value="">— Καμία —</option><option value="english">Αγγλικά</option><option value="french">Γαλλικά</option><option value="german">Γερμανικά</option><option value="italian">Ιταλικά</option><option value="spanish">Ισπανικά</option><option value="other2">Άλλη γλώσσα</option></select>
            <label for="languageLevel2" class="edu-tools-sr-only">Επίπεδο ξένης γλώσσας 2</label><select id="languageLevel2" class="sde-language-level" onchange="calculate()"><option value="none">— Επίπεδο —</option><option value="B2">Β2 — Καλή</option><option value="C1">C1 — Πολύ καλή</option><option value="C2">C2 — Άριστη</option></select>
          </div>
          <div class="field full">
            <label for="computer">Πιστοποιημένη γνώση Η/Υ / ΤΠΕ Α΄ επιπέδου ή πιστοποιητικό γνώσης Η/Υ σύμφωνα με ΑΣΕΠ <small>+2 μόρια. Για ΠΕ86 η γνώση τεκμαίρεται και τα μόρια αποδίδονται αυτόματα.</small></label>
            <select id="computer" onchange="calculate()"><option value="no">Όχι</option><option value="yes">Ναι</option></select>
          </div>
        </div>
        <div class="note">Το εργαλείο κατατάσσει αυτόματα την ισχυρότερη από τις δύο γλώσσες ως «1η ξένη γλώσσα». Για εκπαιδευτικό ΠΕ06 δεν προσμετρά την Αγγλική.</div>
      </section>
    </div>

    <aside class="results" aria-live="polite">
      <section class="card">
        <h2>Αποτέλεσμα</h2>
        <div class="big-total"><div class="number" id="totalScore">0</div><div class="outof">από 40 μόρια</div></div>
        <div class="bar"><div id="totalBar"></div></div>
        <div class="result-row"><span>Εκπαίδευση</span><strong id="educationScore">0 / 22</strong></div>
        <div class="result-row"><span>Διδακτική εμπειρία</span><strong id="experienceScore">0 / 13</strong></div>
        <div class="result-row"><span>Άλλα προσόντα</span><strong id="otherScore">0 / 5</strong></div>
        <div id="eligibilityStatus" role="status" aria-live="polite"></div>
        <div class="actions"><button class="primary" type="button" onclick="calculate()">Υπολογισμός</button><button class="secondary" type="button" onclick="resetForm()">Καθαρισμός</button></div>
      </section>

      <section class="card">
        <h2>Αποδεκτοί γραμματισμοί</h2>
        <div id="assignmentResult" class="subtitle">Επίλεξε ειδικότητα.</div>
      </section>

      <section class="card">
        <h2>Ανάλυση μορίων</h2>
        <div id="breakdown" class="subtitle">Συμπλήρωσε τα στοιχεία σου.</div>
      </section>
    </aside>
  </div>

  <p class="source-note"><strong>Πηγές / Νομική βάση:</strong><br><strong>Πηγή:</strong> Υ.Α. 88422/Κ1, ΦΕΚ Β΄ 4088/03.07.2026, <strong>όπως διορθώθηκε με τη Διόρθωση Σφάλματος ΦΕΚ Β΄ 4199/10.07.2026</strong>. Το εργαλείο είναι ενδεικτικό. Οι τελικοί πίνακες καταρτίζονται από την αρμόδια Επιτροπή και ισχύουν οι όροι της εκάστοτε πρόσκλησης.</p>
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>

<script src="includes/sde-calculations.js?v=3.16"></script>
<script>
  const $ = id => document.getElementById(id);
  const yes = id => $(id).value === 'yes';
  const value = id => $(id).value;
  const numberValue = id => Math.max(0, Number($(id).value || 0));

  function fmt(n) {
    const rounded = Math.round((Number(n) || 0) * 100) / 100;
    return Number.isInteger(rounded) ? String(rounded) : rounded.toFixed(2).replace('.', ',');
  }

  function normalizeWholeYears(id) {
    const el = $(id);
    if (!el || el.value === '') return;
    el.value = String(Math.max(0, Math.floor(Number(el.value) || 0)));
  }

  function specialtyChanged() {
    const sp = value('specialty');
    const needsMathCondition = sp === 'PE86' || sp.startsWith('PE04.');
    $('mathInfoDegreeWrap').classList.toggle('hidden', !needsMathCondition);
    $('formerPE09Wrap').classList.toggle('hidden', sp !== 'PE80');
    $('formerPE1208Wrap').classList.toggle('hidden', sp !== 'PE85');
    if(!needsMathCondition) $('mathInfoDegree').value = 'no';
    if(sp !== 'PE80') $('formerPE09').value = 'no';
    if(sp !== 'PE85') $('formerPE1208').value = 'no';
    if (sp === 'PE86') {
      $('computer').value = 'yes';
      $('computer').disabled = true;
    } else {
      $('computer').disabled = false;
    }
    calculate();
  }

  function getData() {
    return {
      specialty: value('specialty'),
      phd: value('phd'),
      master: value('master'),
      secondDegree: yes('secondDegree'),
      secondPhd: value('secondPhd'),
      secondMaster: value('secondMaster'),
      sdeTrainingHours: numberValue('sdeTrainingHours'),
      adultTrainingHours: numberValue('adultTrainingHours'),
      sdeYears: numberValue('sdeYears'),
      sdeHourlyHours: numberValue('sdeHourlyHours'),
      adultEducationHours: numberValue('adultEducationHours'),
      formalEducationYears: numberValue('formalEducationYears'),
      eligibilitySchoolYears: numberValue('eligibilitySchoolYears'),
      language1: value('language1'),
      languageLevel1: value('languageLevel1'),
      language2: value('language2'),
      languageLevel2: value('languageLevel2'),
      computer: yes('computer'),
      flags: {
        mathOrInformaticsDegree: yes('mathInfoDegree'),
        formerPE09or15: yes('formerPE09'),
        formerPE1208: yes('formerPE1208')
      }
    };
  }

  function renderAssignments(assignments) {
    if (!value('specialty')) return '<span class="subtitle">Επίλεξε ειδικότητα.</span>';
    if (!assignments.length) return '<div class="danger">Η επιλεγμένη ειδικότητα δεν περιλαμβάνεται στους κλάδους του άρθρου 5 για τα γνωστικά αντικείμενα/τμήματα που αναφέρονται στην απόφαση.</div>';
    return assignments.map(item => '<div class="assignment"><strong>' + item.literacy + '</strong><span class="badge">' + item.assignment + '</span>' + (item.note ? '<small>' + item.note + '</small>' : '') + '</div>').join('');
  }

  function detailRows(title, obj) {
    let html = '<h3>' + title + '</h3>';
    if (!obj.details || !obj.details.length) return html + '<div class="subtitle">—</div>';
    obj.details.forEach(d => { html += '<div class="result-row"><span>' + d.label + '</span><strong>' + fmt(d.points) + '</strong></div>'; });
    return html;
  }

  function calculate() {
    normalizeWholeYears('formalEducationYears');
    normalizeWholeYears('sdeYears');
    const result = SDECalculator.calculateAll(getData());
    $('totalScore').textContent = fmt(result.total);
    $('totalBar').style.width = Math.min(100, result.total / 40 * 100) + '%';
    $('educationScore').textContent = fmt(result.education.total) + ' / 22';
    $('experienceScore').textContent = fmt(result.experience.total) + ' / 13';
    $('otherScore').textContent = fmt(result.other.total) + ' / 5';
    $('formalExperiencePreview').textContent = fmt(result.experience.formalPoints) + ' μόρια';

    const assignmentsHtml = renderAssignments(result.assignments);
    $('assignmentResult').innerHTML = assignmentsHtml;
    $('assignmentBox').innerHTML = assignmentsHtml;
    $('assignmentBox').className = result.assignments.length ? 'success' : (value('specialty') ? 'danger' : 'info');

    const messages = [];
    const specialtySelected = Boolean(value('specialty'));
    const blockingValue = value('blockingIssue');
    const teleValue = value('teleEducation');

    if (!specialtySelected) messages.push('<div class="info"><strong>Επίλεξε ειδικότητα</strong> για να ελεγχθούν οι αποδεκτοί γραμματισμοί.</div>');
    const eligibilityYearsAnswered = $('eligibilitySchoolYears').value !== '';
    if (!eligibilityYearsAnswered) {
      messages.push('<div class="info"><strong>Δικαίωμα αίτησης:</strong> συμπλήρωσε τη διδακτική υπηρεσία σε σχολεία Πρωτοβάθμιας/Δευτεροβάθμιας.</div>');
    } else if (!result.eligibleByTwoYears) {
      messages.push('<div class="danger"><strong>Δεν συμπληρώνονται τα 2 απαιτούμενα έτη διδακτικής υπηρεσίας.</strong></div>');
    }

    if (blockingValue === '') {
      messages.push('<div class="warning"><strong>Κωλύματα:</strong> δήλωσε αν υπάρχει πιθανό κώλυμα του άρθρου 4.</div>');
    } else if (blockingValue === 'yes') {
      messages.push('<div class="danger"><strong>Δήλωσες πιθανό κώλυμα του άρθρου 4.</strong> Απαιτείται έλεγχος πριν την αίτηση.</div>');
    } else if (specialtySelected && eligibilityYearsAnswered && result.eligibleByTwoYears) {
      messages.push('<div class="success"><strong>Ο βασικός έλεγχος των 2 ετών/κωλύματος είναι θετικός.</strong> Έλεγξε πάντως όλες τις προϋποθέσεις της πρόσκλησης.</div>');
    }

    if ($('formalEducationYears').value !== '' && eligibilityYearsAnswered && Math.floor(numberValue('formalEducationYears')) < Math.floor(numberValue('eligibilitySchoolYears'))) {
      messages.push('<div class="warning"><strong>Έλεγχος ετών:</strong> τα συνολικά πλήρη έτη τυπικής εκπαίδευσης που δήλωσες για μοριοδότηση είναι λιγότερα από τα έτη Πρωτοβάθμιας/Δευτεροβάθμιας που δήλωσες για επιλεξιμότητα. Έλεγξε τις καταχωρίσεις.</div>');
    }

    if (specialtySelected && !result.assignments.length) messages.push('<div class="warning">Δεν εντοπίζεται αποδεκτός γραμματισμός για την επιλεγμένη ειδικότητα στο άρθρο 5.</div>');

    if (teleValue === '') {
      messages.push('<div class="info">Δήλωσε αν αποδέχεσαι σύγχρονη τηλεκπαίδευση· χρησιμοποιείται ως πρώτο κριτήριο ισοβαθμίας.</div>');
    } else if (teleValue === 'no') {
      messages.push('<div class="warning">Σε ισοβαθμία προηγείται υποψήφιος που έχει αποδεχτεί τη σύγχρονη τηλεκπαίδευση.</div>');
    }
    $('eligibilityStatus').innerHTML = messages.join('');

    let breakdown = detailRows('Εκπαίδευση', result.education) + detailRows('Διδακτική εμπειρία', result.experience) + detailRows('Άλλα προσόντα', result.other);
    [...result.education.warnings, ...result.other.warnings].forEach(w => { breakdown += '<div class="warning">' + w + '</div>'; });
    $('breakdown').innerHTML = breakdown;
  }

  function resetForm() {
    document.querySelectorAll('input[type="number"]').forEach(el => el.value = '0');
    $('formalEducationYears').value = '';
    $('eligibilitySchoolYears').value = '';
    document.querySelectorAll('select').forEach(el => {
      if (el.id === 'teleEducation' || el.id === 'blockingIssue') el.value = '';
      else el.selectedIndex = 0;
    });
    specialtyChanged(); calculate();
  }

  specialtyChanged();
  calculate();
</script>
  <script src="assets/common.js?v=3.20.9-b3"></script>
</body>
</html>
