<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Μάθε σε ποια προκήρυξη μπορείς να συμμετέχεις και πόσα παράβολα χρειάζεσαι</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 30px;
    }

    .app-box {
      max-width: 520px;
      margin: auto;
      background: #ffffff;
      padding: 25px;
      border-radius: 14px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }

    .back-tools {
      display: inline-block;
      margin-bottom: 18px;
      color: #1f6feb;
      font-weight: bold;
      text-decoration: none;
    }

    .back-tools:hover {
      text-decoration: underline;
    }

    h1 {
      text-align: center;
      font-size: 26px;
      margin-bottom: 10px;
    }

    p {
      text-align: center;
      color: #555;
    }

    label {
      display: block;
      margin-top: 18px;
      font-weight: bold;
    }

    select {
      width: 100%;
      padding: 12px;
      margin-top: 6px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    button {
      width: 100%;
      margin-top: 24px;
      padding: 14px;
      border: none;
      border-radius: 8px;
      font-size: 17px;
      font-weight: bold;
      cursor: pointer;
      background: #1f6feb;
      color: white;
    }

    button:hover {
      background: #1558c0;
    }

    .result {
      margin-top: 22px;
      padding: 16px;
      border-radius: 10px;
      font-size: 20px;
      font-weight: bold;
      text-align: center;
      display: none;
    }

    .one {
      background: #e6f4ea;
      color: #137333;
    }

    .two {
      background: #fff4e5;
      color: #b06000;
    }

    .error {
      background: #fdecea;
      color: #b3261e;
    }

	.credits {
	  margin-top: 24px;
	  text-align: center;
	  font-size: 13px;
	  color: #777;
	}
	
	.instructions-box {
	  margin-top: 22px;
	  border: 1px solid #ddd;
	  border-radius: 10px;
	  background: #fafafa;
	  padding: 14px 16px;
	}

	.instructions-box summary {
	  cursor: pointer;
	  font-weight: bold;
	  font-size: 16px;
	  color: #1f6feb;
	}

	.instructions-content {
	  margin-top: 14px;
	  font-size: 15px;
	  line-height: 1.55;
	  color: #444;
	}

	.instructions-content p {
	  text-align: left;
	  margin: 10px 0;
	}

	.path {
	  background: #eef4ff;
	  padding: 10px;
	  border-radius: 8px;
	  font-weight: bold;
	  color: #174ea6;
	}

	.note {
	  background: #fff4e5;
	  padding: 10px;
	  border-radius: 8px;
	  color: #9a5b00;
	  font-weight: bold;
	}
	
	.optional {
	  font-weight: normal;
	  color: #777;
	  font-size: 14px;
	}

  </style>
  <link rel="stylesheet" href="assets/common.css">
</head>

<body>
<?php require_once __DIR__ . '/includes/header.php'; ?>

  <div class="app-box">
<h1>Μάθε σε ποια προκήρυξη μπορείς να συμμετέχεις και πόσα παράβολα χρειάζεσαι</h1>
    <p>  Επίλεξε την 1η ειδικότητα και, προαιρετικά, τη 2η ειδικότητα.</p>

    <label for="specialty1">1η ειδικότητα</label>
    <select id="specialty1">
      <option value="">-- Επιλογή ειδικότητας --</option>
    </select>

    <label for="specialty2">2η ειδικότητα <span class="optional">(προαιρετική)</span></label>
	<select id="specialty2">
	  <option value="">-- Δεν έχω 2η ειδικότητα --</option>
	</select>

    <button onclick="calculateParavola()">Υπολογισμός</button>

    <div id="result" class="result"></div>
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

    <p class="note">
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

    function getGroup(code) {
      if (group1.includes(code)) return 1;
      if (group2.includes(code)) return 2;
      return null;
    }

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

	  result.style.display = "block";
	  result.className = "result";

	  if (!specialty1) {
		result.textContent = "Παρακαλώ επίλεξε τουλάχιστον την 1η ειδικότητα.";
		result.classList.add("error");
		return;
	  }

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
		  ${proclamationText}
		`;

	  if (paravolaCount === 1) {
		result.classList.add("one");
	  } else {
	 result.classList.add("two");
	  }   
	}
  </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>