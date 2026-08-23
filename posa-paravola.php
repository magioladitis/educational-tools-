<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Μάθε σε ποια προκήρυξη μπορείς να συμμετέχεις και πόσα παράβολα χρειάζεσαι</title>
<link rel="stylesheet" href="assets/common.css?v=3.20.13">
</head>

<body class="edu-ui edu-guide-standard edu-guide-paravolo">
<?php require_once __DIR__ . '/includes/header.php'; ?>

  <div class="app-box edu-modernized">
<section class="hero edu-legacy-hero">
<h1>Μάθε σε ποια προκήρυξη μπορείς να συμμετέχεις και πόσα παράβολα χρειάζεσαι</h1>
<p>Επίλεξε την 1η ειδικότητα και, προαιρετικά, τη 2η ειδικότητα. Το αποτέλεσμα ενημερώνεται αυτόματα.</p>
</section>

    <section class="card edu-legacy-main-card">
    <div class="paravolo-rule">
      ℹ️ Το παράβολο υπολογίζεται <strong>ανά προκήρυξη και όχι ανά ειδικότητα</strong>.
      Αν δύο ειδικότητες ανήκουν στην ίδια προκήρυξη, χρειάζεται ένα μόνο παράβολο.
    </div>

    <label for="specialty1">1η ειδικότητα</label>
    <select id="specialty1">
      <option value="">-- Επιλογή ειδικότητας --</option>
    </select>

    <label for="specialty2">2η ειδικότητα <span class="optional">(προαιρετική)</span></label>
	<select id="specialty2">
	  <option value="">-- Δεν έχω 2η ειδικότητα --</option>
	</select>

    <div id="duplicateWarning" class="duplicate-warning" role="alert"></div>
    <div id="result" class="result" role="status" aria-live="polite"></div>
<details class="instructions-box">
  <summary>Οδηγίες για την έκδοση και πληρωμή παραβόλου</summary>

<div class="instructions-content">
    <p>
      Ο/Η υποψήφιος/α πρέπει να εκδώσει ηλεκτρονικό παράβολο αξίας
      <strong>δεκαπέντε (15) ευρώ</strong> για κάθε απαιτούμενο παράβολο.
    </p>

    <p>
      Η έκδοση γίνεται μέσω της εφαρμογής
      <strong><a href="https://www1.gsis.gr/sgsisapps/eparavolo/public/welcome.htm" target="_blank" rel="noopener">e-Παράβολο</a></strong>
      ή, αν υπάρχουν κωδικοί TAXISnet, μέσω της υπηρεσίας
      <strong><a href="https://www.aade.gr/e-parabolo-me-kodikoys-taxisnet" target="_blank" rel="noopener">e-Παράβολο με κωδικούς TAXISnet</a></strong>.
    </p>

    <p>
      Στην εφαρμογή επιλέγεται:
    </p>

    <p class="path">
      Φορέας Δημοσίου → Ανώτατο Συμβούλιο Επιλογής Προσωπικού (Α.Σ.Ε.Π.)
    </p>

    <p>
      Ο/Η υποψήφιος/α πρέπει να αναγράψει τον <strong>20ψήφιο κωδικό παραβόλου</strong> στο κατάλληλο πεδίο της ηλεκτρονικής αίτησης.
    </p>

    <p>
      Η ηλεκτρονική υποβολή της αίτησης στο Α.Σ.Ε.Π. ολοκληρώνεται με την επιλογή
      <strong>«Οριστικοποίηση»</strong>, μόνο εφόσον ο κωδικός του παραβόλου
      βρίσκεται σε κατάσταση <strong>«ΠΛΗΡΩΜΕΝΟ»</strong>, ώστε να δεσμευτεί.
    </p>

    <p class="warning">
      Προσοχή: Πλήρωσε τον ίδιο ακριβώς κωδικό παραβόλου που έχεις αναγράψει
      στην ηλεκτρονική αίτηση και φρόντισε να έχει πληρωθεί πριν την υποβολή της.
    </p>

    <p>
      Περισσότερες πληροφορίες για το e-Παράβολο:
      <br>
      <a href="https://www.gsis.gr/polites-epiheiriseis/pliromes-kai-eispraxeis/e-paravolo" target="_blank" rel="noopener">
        https://www.gsis.gr/polites-epiheiriseis/pliromes-kai-eispraxeis/e-paravolo
      </a>
    </p>
  </div>
</details>
    </section>
  </div>

  <script>
    const group1 = [
      "ΠΕ60",
      "ΠΕ70",
      "ΠΕ73",
      "ΠΕ79.01",
      "ΠΕ79.02"
    ];

    const group2 = [
      "ΠΕ01",
      "ΠΕ02",
      "ΠΕ03",
      "ΠΕ04",
      "ΠΕ05",
      "ΠΕ06",
      "ΠΕ07",
      "ΠΕ08",
      "ΠΕ11",
      "ΠΕ33",
      "ΠΕ34",
      "ΠΕ40",
      "ΠΕ41",
      "ΠΕ78",
      "ΠΕ80",
      "ΠΕ81",
      "ΠΕ82",
      "ΠΕ83",
      "ΠΕ84",
      "ΠΕ85",
      "ΠΕ86",
      "ΠΕ87",
      "ΠΕ88",
      "ΠΕ89",
      "ΠΕ90",
      "ΠΕ91"
    ];

    const allSpecialties = [
      ...group1.map(code => ({ code, group: 1 })),
      ...group2.map(code => ({ code, group: 2 }))
    ];

    function fillSelect(selectId) {
      const select = document.getElementById(selectId);

      allSpecialties.forEach(item => {
        const option = document.createElement("option");
        option.value = item.code;
        option.textContent = item.code;
        option.dataset.group = item.group;
        select.appendChild(option);
      });
    }

    fillSelect("specialty1");
    fillSelect("specialty2");

    const specialty1Select = document.getElementById("specialty1");
    const specialty2Select = document.getElementById("specialty2");
    const duplicateWarning = document.getElementById("duplicateWarning");

    function updateDuplicateProtection() {
      const specialty1 = specialty1Select.value;
      const specialty2 = specialty2Select.value;

      [...specialty2Select.options].forEach(option => {
        option.disabled = Boolean(specialty1 && option.value === specialty1);
      });

      if (specialty1 && specialty2 && specialty1 === specialty2) {
        specialty2Select.value = "";
        duplicateWarning.textContent = "Η ίδια ειδικότητα δεν μπορεί να επιλεγεί δύο φορές. Η 2η επιλογή αφαιρέθηκε αυτόματα.";
        duplicateWarning.style.display = "block";
      } else {
        duplicateWarning.style.display = "none";
        duplicateWarning.textContent = "";
      }

      calculateParavola();
    }

    specialty1Select.addEventListener("change", updateDuplicateProtection);
    specialty2Select.addEventListener("change", updateDuplicateProtection);

    function getGroup(code) {
      if (group1.includes(code)) return 1;
      if (group2.includes(code)) return 2;
      return null;
    }


	function getProclamation(group) {
	  if (group === 1) return "1ΓΕ/2026";
	  if (group === 2) return "2ΓΕ/2026";
	  return "";
	}

	function calculateParavola() {
	  const specialty1 = document.getElementById("specialty1").value;
	  const specialty2 = document.getElementById("specialty2").value;
	  const result = document.getElementById("result");

	  result.className = "result";

	  if (!specialty1) {
        result.style.display = "none";
        result.innerHTML = "";
		return;
	  }

      result.style.display = "block";

	  const selectedSpecialties = [specialty1];

	  if (specialty2) {
		selectedSpecialties.push(specialty2);
	  }

	  const selectedGroups = selectedSpecialties.map(code => getGroup(code));
	  const uniqueGroups = [...new Set(selectedGroups)];

	  const paravolaCount = uniqueGroups.length;
	  const paravolaText = paravolaCount === 1 ? "1 παράβολο" : "2 παράβολα";
	  
	  const costPerParavolo = 15;
	  const totalCost = paravolaCount * costPerParavolo;

	  const proclamations = uniqueGroups.map(group => getProclamation(group));

	  let proclamationText = "";

	  if (proclamations.length === 1) {
		proclamationText = `Έχεις δικαίωμα συμμετοχής στην προκήρυξη <strong>${proclamations[0]}</strong>.`;
	  } else {
		proclamationText = `Έχεις δικαίωμα συμμετοχής στις προκηρύξεις <strong>${proclamations.join(" και ")}</strong>.`;
	  }

		result.innerHTML = `
		  Χρειάζεσαι <strong>${paravolaText}</strong>.<br>
		  Κάθε παράβολο έχει αξία <strong>15 ευρώ</strong>.<br>
		  Συνολικό κόστος: <strong>${totalCost} ευρώ</strong>.<br><br>
		  ${proclamationText}<br><br>
          <span class="paravolo-result-note">
            Το παράβολο είναι <strong>ανά προκήρυξη, όχι ανά ειδικότητα</strong>.
          </span>
		`;

	  if (paravolaCount === 1) {
		result.classList.add("one");
	  } else {
	 result.classList.add("two");
	  }   
	}
  </script>


<section class="edu-source-card" aria-labelledby="sourcesTitle">
  <h2 id="sourcesTitle">Πηγές / Νομική βάση</h2>
  <p>Προκηρύξεις Α.Σ.Ε.Π. <strong>1ΓΕ/2026</strong> και <strong>2ΓΕ/2026</strong>. Η υποχρέωση έκδοσης e-Παραβόλου και το ποσό των <strong>15 € ανά προκήρυξη</strong> προκύπτουν από τους όρους υποβολής της αντίστοιχης αίτησης.</p>
  <div class="source-links"><a href="https://info.asep.gr/node/78737" target="_blank" rel="noopener noreferrer">Έκδοση ΦΕΚ 1ΓΕ/2026 &amp; 2ΓΕ/2026 — ΑΣΕΠ ↗</a> <a href="https://info.asep.gr/node/78799" target="_blank" rel="noopener noreferrer">Υποβολή αιτήσεων — ΑΣΕΠ ↗</a></div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="assets/common.js?v=3.20.13"></script>
</body>
</html>