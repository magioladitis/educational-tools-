/*
 * Data-driven ειδικοί κανόνες/επισημάνσεις για προτιμήσεις εξωτερικού.
 * Η λογική εδώ είναι DOM-free ώστε να μπορεί να επαναχρησιμοποιηθεί
 * από browser UI ή μελλοντικό mobile client.
 */
(function (global) {
  "use strict";

  const PREFERENCE_RULES = Object.freeze({
    de_mu: Object.freeze({
      notes: Object.freeze([
        'Για τη Γερμανία — Σ.Γ.Ε. Μονάχου ισχύουν ειδικές προϋποθέσεις για τα επιχορηγούμενα σχολεία της Βαυαρίας και προηγούμενη άδεια διδασκαλίας από τη γερμανική υπηρεσία, όπου απαιτείται.'
      ]),
      specialtyNotes: Object.freeze({
        'ΠΕ78': Object.freeze([
          'Στο Σ.Γ.Ε. Μονάχου η ΠΕ78 αφορά ειδικά Κοινωνιολόγους.'
        ]),
        'ΠΕ80': Object.freeze([
          'Στο Σ.Γ.Ε. Μονάχου η ΠΕ80 αφορά ειδικά Οικονομολόγους και, για τα γερμανόφωνα μαθήματα, απαιτείται αυξημένη γερμανομάθεια.'
        ]),
        'ΠΕ82': Object.freeze([
          'Για ΠΕ82 στη Βαυαρία επισημαίνεται αυξημένη γερμανομάθεια (Γ1) για τη διδασκαλία των μαθημάτων στη γερμανική.'
        ]),
        'ΠΕ03': Object.freeze([
          'Στη Βαυαρία τα Μαθηματικά διδάσκονται και στη γερμανική· για διδασκαλία γερμανόφωνων μαθημάτων απαιτείται Γ1.'
        ]),
        'ΠΕ11': Object.freeze([
          'Στα Γυμνάσια Μονάχου/Νυρεμβέργης η Φυσική Αγωγή κατανέμεται ανά φύλο μαθητών και οι αποσπάσεις εξαρτώνται από τις αντίστοιχες κενές θέσεις.'
        ])
      })
    }),
    ch: Object.freeze({
      notes: Object.freeze([
        'Για την Ελβετία απαιτείται τουλάχιστον Β1 στην ομιλούμενη γλώσσα του τόπου εργασίας (προφορικός και γραπτός λόγος), πέρα από τον γενικό έλεγχο του πίνακα.'
      ]),
      specialtyNotes: Object.freeze({})
    })
  });

  function notesFor(preferenceIds, specialty) {
    const notes = [];
    (preferenceIds || []).forEach(function (id) {
      const rule = PREFERENCE_RULES[id];
      if (!rule) return;
      notes.push.apply(notes, rule.notes || []);
      if (specialty && rule.specialtyNotes && rule.specialtyNotes[specialty]) {
        notes.push.apply(notes, rule.specialtyNotes[specialty]);
      }
    });
    return notes;
  }

  global.AbroadPreferenceRules = Object.freeze({
    PREFERENCE_RULES,
    notesFor
  });
})(window);
