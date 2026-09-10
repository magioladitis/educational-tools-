/* Extracted UI/controller logic from posa-paravola.php. */
(function(){
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
  
})();
