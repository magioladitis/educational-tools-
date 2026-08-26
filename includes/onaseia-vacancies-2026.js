(function (global) {
  "use strict";

  // Λειτουργικές ανάγκες ΔΗΜ.Ω.Σ. πλήρους ωραρίου, αποφάσεις 55/ΔΕΔΗΜΩΣ και 56/ΔΕΔΗΜΩΣ, 26-08-2026.
  const schools = [{"code":"3701010","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Ξάνθης","area":"Ξάνθη","level":"Γυμνάσιο","general":{"ΠΕ01":1,"ΠΕ02":2,"ΠΕ03":1,"ΠΕ04.05":1,"ΠΕ05":1,"ΠΕ06":2,"ΠΕ07":1,"ΠΕ08":1,"ΠΕ11":1,"ΠΕ80":1,"ΠΕ86":1},"eae":{}},{"code":"3751020","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Ξάνθης","area":"Ξάνθη","level":"Λύκειο","general":{"ΠΕ01":1,"ΠΕ02":4,"ΠΕ03":3,"ΠΕ11":1,"ΠΕ80":1,"ΠΕ86":1},"eae":{}},{"code":"0501072","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Κολωνού Δήμου Αθηναίων","area":"Κολωνός","level":"Γυμνάσιο","general":{"ΠΕ01":2,"ΠΕ03":3,"ΠΕ04.02":1,"ΠΕ06":2,"ΠΕ08":1,"ΠΕ11":1,"ΠΕ86":1},"eae":{}},{"code":"0551072","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Κολωνού Δήμου Αθηναίων","area":"Κολωνός","level":"Λύκειο","general":{"ΠΕ01":1,"ΠΕ03":3,"ΠΕ11":2},"eae":{}},{"code":"0501720","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Περιστερίου","area":"Περιστέρι","level":"Γυμνάσιο","general":{"ΠΕ02":1,"ΠΕ04.04":1,"ΠΕ06":1,"ΠΕ86":2},"eae":{"ΠΕ03.50":1}},{"code":"0551720","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Περιστερίου «Οδυσσέας Ελύτης»","area":"Περιστέρι","level":"Λύκειο","general":{"ΠΕ02":2,"ΠΕ04.01":1,"ΠΕ05":1},"eae":{}},{"code":"0501444","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Αιγάλεω","area":"Αιγάλεω","level":"Γυμνάσιο","general":{"ΠΕ01":1,"ΠΕ05":1,"ΠΕ06":2,"ΠΕ11":1},"eae":{"ΠΕ02.50":1,"ΠΕ03.50":1}},{"code":"0551450","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Αιγάλεω","area":"Αιγάλεω","level":"Λύκειο","general":{"ΠΕ02":5,"ΠΕ03":3,"ΠΕ11":2,"ΠΕ81":1},"eae":{}},{"code":"0502088","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Αχαρνών","area":"Αχαρνές","level":"Γυμνάσιο","general":{"ΠΕ01":1,"ΠΕ02":3,"ΠΕ03":3,"ΠΕ04.04":1,"ΠΕ04.05":1,"ΠΕ08":1,"ΠΕ11":1,"ΠΕ79.01":1,"ΠΕ86":1},"eae":{"ΠΕ02.50":1,"ΠΕ03.50":1,"ΠΕ04.50":1}},{"code":"0552093","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Αχαρνών","area":"Αχαρνές","level":"Λύκειο","general":{"ΠΕ01":8,"ΠΕ02":3,"ΠΕ04.04":1,"ΠΕ05":1,"ΠΕ06":1,"ΠΕ11":2,"ΠΕ80":1},"eae":{"ΠΕ02.50":2,"ΠΕ03.50":1,"ΠΕ04.50":1}},{"code":"2701020","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Κοζάνης","area":"Κοζάνη","level":"Γυμνάσιο","general":{"ΠΕ05":1},"eae":{"ΠΕ02.50":1}},{"code":"2751020","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Κοζάνης","area":"Κοζάνη","level":"Λύκειο","general":{"ΠΕ02":3,"ΠΕ04.01":1,"ΠΕ04.04":1,"ΠΕ11":1},"eae":{}},{"code":"1901182","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Θεσσαλονίκης","area":"Θεσσαλονίκη","level":"Γυμνάσιο","general":{},"eae":{}},{"code":"1951130","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Θεσσαλονίκης «Περικλής Στεφανίδης»","area":"Θεσσαλονίκη","level":"Λύκειο","general":{"ΠΕ80":1},"eae":{}},{"code":"1001030","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Ρόδου","area":"Ρόδος","level":"Γυμνάσιο","general":{"ΠΕ01":2,"ΠΕ02":4,"ΠΕ08":1,"ΠΕ11":1,"ΠΕ81":1},"eae":{"ΠΕ02.50":1,"ΠΕ03.50":1,"ΠΕ04.50":1}},{"code":"1051030","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Ρόδου","area":"Ρόδος","level":"Λύκειο","general":{"ΠΕ02":3,"ΠΕ03":3,"ΠΕ04.02":2},"eae":{}},{"code":"1701053","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Ηρακλείου","area":"Ηράκλειο","level":"Γυμνάσιο","general":{"ΠΕ01":1,"ΠΕ08":1},"eae":{}},{"code":"1790070","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Ηρακλείου","area":"Ηράκλειο","level":"Λύκειο","general":{},"eae":{}},{"code":"1501020","label":"ΔΗΜ.Ω.Σ. Γυμνάσιο Πύργου Ηλείας","area":"Πύργος Ηλείας","level":"Γυμνάσιο","general":{"ΠΕ01":4,"ΠΕ02":1,"ΠΕ03":1,"ΠΕ05":1,"ΠΕ06":1,"ΠΕ08":1,"ΠΕ11":1,"ΠΕ78":1,"ΠΕ81":1,"ΠΕ86":1},"eae":{"ΠΕ02.50":1,"ΠΕ03.50":1}},{"code":"1551020","label":"ΔΗΜ.Ω.Σ. Γενικό Λύκειο Πύργου Ηλείας","area":"Πύργος Ηλείας","level":"Λύκειο","general":{"ΠΕ01":2,"ΠΕ02":1,"ΠΕ03":1,"ΠΕ06":1,"ΠΕ11":1,"ΠΕ80":1},"eae":{}}];

  const EAE_BY_BASE = { "ΠΕ02": "ΠΕ02.50", "ΠΕ03": "ΠΕ03.50", "ΠΕ04": "ΠΕ04.50" };
  const PE04_GENERAL = new Set(["ΠΕ04.01", "ΠΕ04.02", "ΠΕ04.04", "ΠΕ04.05"]);

  function vacancies(kind, code) {
    if (!code) return [];
    return schools.map(school => ({
      code: school.code,
      label: school.label,
      area: school.area,
      level: school.level,
      positions: Number((school[kind] || {})[code] || 0)
    })).filter(item => item.positions > 0);
  }

  function total(items) {
    return items.reduce((sum, item) => sum + item.positions, 0);
  }

  function generalCodeFor(specialty, pe04Specialty) {
    if (specialty === "ΠΕ04") return PE04_GENERAL.has(pe04Specialty) ? pe04Specialty : "";
    if (specialty === "ΤΕ16") return "ΠΕ79.01";
    return specialty || "";
  }

  function selection(specialty, pe04Specialty) {
    const generalCode = generalCodeFor(specialty, pe04Specialty);
    const general = vacancies("general", generalCode);
    const eaeCode = EAE_BY_BASE[specialty] || "";
    const eae = vacancies("eae", eaeCode);
    return {
      sourceDate: "26/08/2026",
      generalDecision: "55/ΔΕΔΗΜΩΣ",
      eaeDecision: "56/ΔΕΔΗΜΩΣ",
      generalCode,
      general,
      generalTotal: total(general),
      eaeCode,
      eae,
      eaeTotal: total(eae),
      needsPe04Specialty: specialty === "ΠΕ04" && !generalCode,
      te16UsesPe79: specialty === "ΤΕ16"
    };
  }

  global.OnaseiaVacancies2026 = { schools, vacancies, total, selection };
})(typeof window !== "undefined" ? window : globalThis);
