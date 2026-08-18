# Κοινό UI — Εργαλειοθήκη Εκπαιδευτικού

Το `assets/common.css` είναι πλέον το κοινό design system όλων των εργαλείων και το `assets/common.js` προσθέτει μικρές κοινές βελτιώσεις UX.

## Για νέο εργαλείο

Προτιμώμενες canonical κλάσεις:

```html
<div class="edu-tool-shell">
  <section class="edu-card">
    <div class="edu-field-grid">
      <div class="edu-field">...</div>
      <div class="edu-field edu-field--full">...</div>
    </div>

    <div class="edu-message edu-message--info">...</div>
    <div class="edu-message edu-message--warning">...</div>
    <div class="edu-message edu-message--success">...</div>
    <div class="edu-message edu-message--danger">...</div>

    <div class="edu-actions">
      <button class="edu-btn edu-btn--primary">Υπολογισμός</button>
      <button class="edu-btn edu-btn--secondary">Μηδενισμός</button>
    </div>
  </section>
</div>
```

## Συμβατότητα με τα υπάρχοντα εργαλεία

Το κοινό UI αναγνωρίζει ήδη τις υπάρχουσες κλάσεις χωρίς να απαιτεί άμεσο refactor:

- containers: `app-box`, `card`, `section`, `panel`, `question`
- messages: `note`, `note-box`, `info`, `warning`, `warning-box`, `success`, `success-box`, `danger`, `danger-box`
- actions: `actions`, `calc-actions`, `button-row`, `secondary`, `reset-button`
- results: `result`, `results`, `result-card`

## Κοινά στοιχεία

- ίδια χρώματα / radii / borders / shadows
- ίδιο focus state σε inputs, selects, buttons και links
- κοινό primary/secondary button styling
- κοινά info / warning / success / danger boxes
- responsive header / footer
- κοινό print mode
- floating «↑» για επιστροφή στην αρχή σε μεγάλες σελίδες
- αυτόματο `aria-live="polite"` σε result περιοχές όπου λείπει
- αυτόματη αναγνώριση Copy / Print / Reset ως secondary actions

## Include

Κάθε PHP πρέπει να έχει:

```html
<link rel="stylesheet" href="assets/common.css">
```

και πριν από το `</body>`:

```html
<script src="assets/common.js"></script>
```

Το `header.php` και το `footer.php` παραμένουν κοινά για όλο το project.
