/*
 * Υποχρεωτικές άδειες και βεβαιώσεις για κλάδους ΕΕΠ της 2ΕΑ/2025.
 * Δεν προσθέτουν μόρια. Αποτελούν πρόσθετα τυπικά προσόντα/δικαιολογητικά ένταξης.
 */
(function (global) {
  'use strict';

  const REQUIREMENTS = Object.freeze({
    PE21: Object.freeze({
      label: 'ΠΕ21 — Θεραπευτών Λόγου',
      items: Object.freeze([
        Object.freeze({
          id: 'practice',
          label: 'Βεβαίωση ότι πληρούνται οι νόμιμες προϋποθέσεις για την άσκηση του επαγγέλματος του Λογοθεραπευτή.'
        })
      ])
    }),
    PE22: Object.freeze({
      label: 'ΠΕ22 — Επαγγελματικών Συμβούλων',
      items: Object.freeze([]),
      note: 'Δεν προβλέπεται ειδική άδεια άσκησης επαγγέλματος ή εγγραφή σε επαγγελματικό σύλλογο στην ενότητα «Άδειες και Βεβαιώσεις». Ισχύουν τα απαιτούμενα προσόντα τίτλων σπουδών του Κεφαλαίου Β΄.'
    }),
    PE23: Object.freeze({
      label: 'ΠΕ23 — Ψυχολόγων',
      items: Object.freeze([
        Object.freeze({
          id: 'practice',
          label: 'Άδεια άσκησης επαγγέλματος Ψυχολόγου ή Βεβαίωση ότι πληρούνται όλες οι νόμιμες προϋποθέσεις για την άσκηση του επαγγέλματος.'
        })
      ])
    }),
    PE25: Object.freeze({
      label: 'ΠΕ25 — Σχολικών Νοσηλευτών',
      routes: Object.freeze({
        nursing: Object.freeze({
          label: 'Τίτλος Νοσηλευτικής',
          items: Object.freeze([
            Object.freeze({ id: 'practice', label: 'Άδεια άσκησης επαγγέλματος Νοσηλευτή/Νοσηλεύτριας ή αντίστοιχη Βεβαίωση νόμιμων προϋποθέσεων.' }),
            Object.freeze({ id: 'membership', label: 'Ταυτότητα μέλους Ε.Ν.Ε. σε ισχύ ή προβλεπόμενη Βεβαίωση ανανέωσης/εγγραφής.' })
          ])
        }),
        health_visitor: Object.freeze({
          label: 'Τίτλος Επισκέπτη/Επισκέπτριας Υγείας',
          items: Object.freeze([
            Object.freeze({ id: 'practice', label: 'Άδεια άσκησης επαγγέλματος Επισκέπτη/Επισκέπτριας Υγείας ή αντίστοιχη Βεβαίωση νόμιμων προϋποθέσεων.' }),
            Object.freeze({ id: 'membership', label: 'Ταυτότητα μέλους Π.Σ.Ε.Υ. σε ισχύ ή προβλεπόμενη Βεβαίωση εγγραφής.' })
          ])
        })
      })
    }),
    PE28: Object.freeze({
      label: 'ΠΕ28 — Φυσιοθεραπευτών',
      items: Object.freeze([
        Object.freeze({ id: 'practice', label: 'Άδεια άσκησης επαγγέλματος Φυσικοθεραπευτή ή αντίστοιχη Βεβαίωση νόμιμων προϋποθέσεων.' }),
        Object.freeze({ id: 'membership', label: 'Ταυτότητα μέλους Π.Σ.Φ. σε ισχύ ή προβλεπόμενη Βεβαίωση εγγραφής/ανανέωσης.' })
      ])
    }),
    PE29: Object.freeze({
      label: 'ΠΕ29 — Εργασιοθεραπευτών–Εργοθεραπευτών',
      items: Object.freeze([
        Object.freeze({ id: 'practice', label: 'Άδεια άσκησης επαγγέλματος Εργοθεραπευτή ή αντίστοιχη Βεβαίωση νόμιμων προϋποθέσεων.' }),
        Object.freeze({ id: 'membership', label: 'Ταυτότητα μέλους Π.Σ.Ε. σε ισχύ ή προβλεπόμενη Βεβαίωση εγγραφής/ανανέωσης.' })
      ])
    }),
    PE30: Object.freeze({
      label: 'ΠΕ30 — Κοινωνικών Λειτουργών',
      items: Object.freeze([
        Object.freeze({ id: 'practice', label: 'Άδεια άσκησης επαγγέλματος Κοινωνικού Λειτουργού / Κοινωνικής Εργασίας ή αντίστοιχη Βεβαίωση νόμιμων προϋποθέσεων.' }),
        Object.freeze({ id: 'membership', label: 'Ταυτότητα μέλους Σ.Κ.Λ.Ε. σε ισχύ ή Βεβαίωση εγγραφής–υποβολής ετήσιας δήλωσης στοιχείων σε ισχύ.' })
      ])
    }),
    PE31: Object.freeze({
      label: 'ΠΕ31 — Εξειδικευμένου',
      items: Object.freeze([]),
      note: 'Δεν προβλέπεται ειδική άδεια άσκησης επαγγέλματος ή εγγραφή σε επαγγελματικό σύλλογο στην ενότητα «Άδειες και Βεβαιώσεις». Ισχύουν τα ειδικά προσόντα εξειδίκευσης και εμπειρίας του Κεφαλαίου Β΄.'
    })
  });

  function getRequirements(branch, route) {
    const config = REQUIREMENTS[branch] || null;
    if (!config) return { branch, label: '', routeRequired: false, route: '', routeLabel: '', items: [], note: '' };
    if (config.routes) {
      const selected = config.routes[route] || null;
      return {
        branch,
        label: config.label,
        routeRequired: true,
        route: selected ? route : '',
        routeLabel: selected ? selected.label : '',
        items: selected ? selected.items.slice() : [],
        note: selected ? '' : 'Επίλεξε τη διαδρομή βασικού επαγγελματικού τίτλου για να εμφανιστούν τα σωστά δικαιολογητικά.'
      };
    }
    return {
      branch,
      label: config.label,
      routeRequired: false,
      route: '',
      routeLabel: '',
      items: config.items.slice(),
      note: config.note || ''
    };
  }

  function evaluate(branch, route, checked) {
    const req = getRequirements(branch, route);
    if (!branch || !REQUIREMENTS[branch]) {
      return { status: 'unselected', complete: false, required: 0, checked: 0, missing: [], requirements: req };
    }
    if (req.routeRequired && !req.route) {
      return { status: 'route-required', complete: false, required: 0, checked: 0, missing: [], requirements: req };
    }
    const selected = new Set(Array.isArray(checked) ? checked : []);
    const missing = req.items.filter(item => !selected.has(item.id));
    return {
      status: req.items.length === 0 ? 'not-applicable' : (missing.length === 0 ? 'complete' : 'incomplete'),
      complete: req.items.length === 0 || missing.length === 0,
      required: req.items.length,
      checked: req.items.length - missing.length,
      missing,
      requirements: req
    };
  }

  global.EEPEligibility = Object.freeze({ REQUIREMENTS, getRequirements, evaluate });
})(typeof window !== 'undefined' ? window : globalThis);
