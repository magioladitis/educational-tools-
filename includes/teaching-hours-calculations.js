/*
 * Common rules for weekly compulsory teaching hours in public Primary
 * and Secondary Education.
 *
 * Sources encoded by this module:
 * - Primary / Δημοτικό: art. 13(7) n. 1566/1985 as amended, including art. 49(1b) n. 4547/2018 (director hours and -2 hours for vice directors).
 * - Kindergarten: YA 127187/E1/01-08-2016 (FEK B 2524/2016).
 * - Secondary: art. 14(13) n. 1566/1985 as replaced by n. 4152/2013,
 *   together with circular 123948/D2/06-09-2013 and n. 2413/1996.
 * - EK/EPAL laboratory sector/specialty responsibility: art. 27(3) n. 4386/2016.
 */
(function (global) {
  "use strict";

  const RULES = Object.freeze({
    primary: Object.freeze({
      smallSchoolHours: 25,
      teacherBands: Object.freeze([
        Object.freeze({ minMonths: 0, maxMonthsExclusive: 120, hours: 24, label: "κάτω από 10 έτη" }),
        Object.freeze({ minMonths: 120, maxMonthsExclusive: 180, hours: 23, label: "10 έως κάτω από 15 έτη" }),
        Object.freeze({ minMonths: 180, maxMonthsExclusive: 240, hours: 22, label: "15 έως κάτω από 20 έτη" }),
        Object.freeze({ minMonths: 240, maxMonthsExclusive: null, hours: 21, label: "20 έτη και άνω" })
      ])
    }),
    secondary: Object.freeze({
      PE: Object.freeze([
        Object.freeze({ minMonths: 0, maxMonthsExclusive: 84, hours: 23, label: "κάτω από 7 έτη" }),
        Object.freeze({ minMonths: 84, maxMonthsExclusive: 156, hours: 21, label: "7 έως κάτω από 13 έτη" }),
        Object.freeze({ minMonths: 156, maxMonthsExclusive: 240, hours: 20, label: "13 έως κάτω από 20 έτη" }),
        Object.freeze({ minMonths: 240, maxMonthsExclusive: null, hours: 18, label: "20 έτη και άνω" })
      ]),
      TE01: Object.freeze([
        Object.freeze({ minMonths: 0, maxMonthsExclusive: 96, hours: 24, label: "κάτω από 8 έτη" }),
        Object.freeze({ minMonths: 96, maxMonthsExclusive: 168, hours: 21, label: "8 έως κάτω από 14 έτη" }),
        Object.freeze({ minMonths: 168, maxMonthsExclusive: 240, hours: 20, label: "14 έως κάτω από 20 έτη" }),
        Object.freeze({ minMonths: 240, maxMonthsExclusive: null, hours: 18, label: "20 έτη και άνω" })
      ]),
      DE01_ARCH: Object.freeze([
        Object.freeze({ minMonths: 0, maxMonthsExclusive: 240, hours: 28, label: "κάτω από 20 έτη" }),
        Object.freeze({ minMonths: 240, maxMonthsExclusive: null, hours: 26, label: "20 έτη και άνω" })
      ]),
      DE01_TECH: Object.freeze([
        Object.freeze({ minMonths: 0, maxMonthsExclusive: 240, hours: 30, label: "κάτω από 20 έτη" }),
        Object.freeze({ minMonths: 240, maxMonthsExclusive: null, hours: 28, label: "20 έτη και άνω" })
      ])
    })
  });

  function nonNegativeInteger(value) {
    const n = Math.floor(Number(value) || 0);
    return Math.max(0, n);
  }

  function serviceMonths(years, months) {
    const y = Math.min(40, nonNegativeInteger(years));
    const m = Math.min(11, nonNegativeInteger(months));
    return y * 12 + m;
  }

  function findBand(bands, totalMonths) {
    return bands.find(function (band) {
      return totalMonths >= band.minMonths && (band.maxMonthsExclusive === null || totalMonths < band.maxMonthsExclusive);
    }) || bands[bands.length - 1];
  }

  function serviceLabel(totalMonths) {
    const years = Math.floor(totalMonths / 12);
    const months = totalMonths % 12;
    return years + " έτη" + (months ? " και " + months + " μήν." : "");
  }

  function primary(options) {
    options = options || {};
    const schoolType = options.schoolType === "kindergarten" ? "kindergarten" : "primary";
    const role = ["director", "vice_director"].includes(options.role) ? options.role : "teacher";
    const organicity = Math.max(1, nonNegativeInteger(options.organicity || 1));
    const totalMonths = serviceMonths(options.years, options.months);

    if (role === "director") {
      if (organicity < 4) {
        return { valid: false, error: "Η ειδική κλίμακα Διευθυντή εφαρμόζεται από 4/θέσια σχολική μονάδα και άνω. Για ολιγοθέσιο επίλεξε Εκπαιδευτικός / Προϊστάμενος." };
      }
      let hours;
      let rule;
      if (schoolType === "kindergarten") {
        if (organicity > 6) {
          return { valid: false, error: "Η ισχύουσα ειδική κλίμακα Διευθυντή Νηπιαγωγείου προβλέπει 4–5/θέσιο ή 6/θέσιο Νηπιαγωγείο." };
        }
        hours = organicity <= 5 ? 20 : 12;
        rule = organicity <= 5 ? "Διευθυντής/ντρια 4–5/θέσιου Νηπιαγωγείου" : "Διευθυντής/ντρια 6/θέσιου Νηπιαγωγείου";
      } else if (organicity <= 5) {
        hours = 18; rule = "Διευθυντής/ντρια 4–5/θέσιου Δημοτικού";
      } else if (organicity <= 9) {
        hours = 10; rule = "Διευθυντής/ντρια 6–9/θέσιου Δημοτικού";
      } else if (organicity <= 11) {
        hours = 8; rule = "Διευθυντής/ντρια 10–11/θέσιου Δημοτικού";
      } else {
        hours = 6; rule = "Διευθυντής/ντρια 12/θέσιου και άνω Δημοτικού";
      }
      return { valid: true, level: "primary", schoolType: schoolType, role: role, hours: hours, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), rule: rule, thresholdNote: false };
    }

    if (role === "vice_director") {
      if (schoolType !== "primary") {
        return { valid: false, error: "Η συγκεκριμένη μείωση δύο ωρών αφορά Υποδιευθυντές/ντριες Δημοτικών Σχολείων." };
      }
      if (organicity <= 3) {
        return { valid: false, error: "Η επιλογή Υποδιευθυντή/ντριας δεν εφαρμόζεται σε 1–3/θέσιο Δημοτικό στο πλαίσιο αυτού του υπολογισμού." };
      }
      const band = findBand(RULES.primary.teacherBands, totalMonths);
      const hours = Math.max(0, band.hours - 2);
      return {
        valid: true, level: "primary", schoolType: schoolType, role: role, hours: hours,
        serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), baseTeacherHours: band.hours,
        rule: "Υποδιευθυντής/ντρια Δημοτικού: μείωση 2 ωρών από το ατομικό υποχρεωτικό διδακτικό ωράριο (" + band.hours + " → " + hours + ").",
        thresholdNote: true
      };
    }

    if (organicity <= 3) {
      return {
        valid: true, level: "primary", schoolType: schoolType, role: role,
        hours: RULES.primary.smallSchoolHours, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths),
        rule: "Εκπαιδευτικός σε 1–3/θέσια σχολική μονάδα: 25 ώρες ανεξάρτητα από τα έτη υπηρεσίας.",
        thresholdNote: false
      };
    }

    const band = findBand(RULES.primary.teacherBands, totalMonths);
    return {
      valid: true, level: "primary", schoolType: schoolType, role: role,
      hours: band.hours, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths),
      rule: "Εκπαιδευτικός σε 4/θέσια και άνω σχολική μονάδα — " + band.label + ".",
      thresholdNote: true
    };
  }

  function secondaryTeacherHours(branch, totalMonths) {
    const normalized = RULES.secondary[branch] ? branch : "PE";
    return findBand(RULES.secondary[normalized], totalMonths);
  }

  function secondary(options) {
    options = options || {};
    const role = options.role || "teacher";
    const branch = RULES.secondary[options.branch] ? options.branch : "PE";
    const totalMonths = serviceMonths(options.years, options.months);
    const twentyYears = totalMonths >= 240;

    if (role === "director") {
      const sections = options.sections || "3-5";
      const base = { "3-5": 10, "6-9": 9, "10-12": 7, "13+": 5 }[sections] || 10;
      const hours = base - (twentyYears ? 2 : 0);
      return { valid: true, level: "secondary", role: role, branch: branch, hours: hours, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), rule: "Διευθυντής/ντρια Γυμνασίου/Λυκείου με " + sections.replace("13+", "πάνω από 12") + " τμήματα" + (twentyYears ? " και συμπληρωμένα 20 έτη υπηρεσίας." : ".") };
    }

    if (role === "lab_director") {
      return { valid: true, level: "secondary", role: role, branch: branch, hours: twentyYears ? 8 : 10, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), rule: "Διευθυντής/ντρια Εργαστηριακού Κέντρου" + (twentyYears ? " με συμπληρωμένα 20 έτη υπηρεσίας." : ".") };
    }

    if (role === "vice_or_sector") {
      return { valid: true, level: "secondary", role: role, branch: branch, hours: twentyYears ? 14 : 16, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), rule: "Υποδιευθυντής/ντρια ή Υπεύθυνος/η Τομέα" + (twentyYears ? " με συμπληρωμένα 20 έτη υπηρεσίας." : ".") };
    }

    const band = secondaryTeacherHours(branch, totalMonths);
    if (role === "lab_responsible") {
      const roleLimit = twentyYears ? 18 : 20;
      const hours = Math.min(band.hours, roleLimit);
      return { valid: true, level: "secondary", role: role, branch: branch, hours: hours, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), baseTeacherHours: band.hours, roleLimit: roleLimit, rule: "Υπεύθυνος/η Εργαστηρίου: έως " + roleLimit + " ώρες, με εφαρμογή του μικρότερου ατομικού ωραρίου όταν αυτό είναι χαμηλότερο." };
    }

    if (role === "epal_ek_lab_sector") {
      const hours = Math.max(18, band.hours - 2);
      return { valid: true, level: "secondary", role: role, branch: branch, hours: hours, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), baseTeacherHours: band.hours, rule: "Υπεύθυνος/η εργαστηρίου τομέα ή ειδικότητας Ε.Κ./ΕΠΑ.Λ.: μείωση 2 ωρών από το ατομικό υποχρεωτικό ωράριο, με κατώτερο όριο 18 ώρες." };
    }

    return { valid: true, level: "secondary", role: "teacher", branch: branch, hours: band.hours, serviceMonths: totalMonths, serviceLabel: serviceLabel(totalMonths), rule: "Εκπαιδευτικός κλάδου " + branch.replace("DE01_ARCH", "ΔΕ01 — Αρχιτεχνίτης").replace("DE01_TECH", "ΔΕ01 — Τεχνίτης") + " — " + band.label + "." };
  }

  function calculate(options) {
    options = options || {};
    return options.level === "secondary" ? secondary(options) : primary(options);
  }

  global.EducationTeachingHours = Object.freeze({
    RULES: RULES,
    nonNegativeInteger: nonNegativeInteger,
    serviceMonths: serviceMonths,
    serviceLabel: serviceLabel,
    primary: primary,
    secondary: secondary,
    calculate: calculate
  });
})(window);
