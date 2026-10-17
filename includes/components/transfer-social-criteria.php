<?php
/**
 * Shared social-criteria VIEW for 2026–27 P.E. and D.E. transfers.
 * Secondary markup is the stable reference and is rendered without alteration.
 * The common EducationTransfer scoring engine supplies the numeric rules;
 * eligibility, documentary routes and official correspondence stay grade-specific.
 */
function transferSocialCriteriaCard($grade, $extraHtml = '') {
    if ($grade !== 'primary' && $grade !== 'secondary') {
        throw new InvalidArgumentException('Unknown transfer education grade');
    }
    $primary = $grade === 'primary';
    $ids = array('eligibleChildren' => 'primaryChildren', 'familyStatusEligible' => 'primaryFamily', 'coService' => 'primaryCoservice', 'transferCoServiceCaseField' => 'primaryCoServiceCaseField', 'transferCoServiceCase' => 'primaryCoServiceCase', 'transferCoServiceAdvice' => 'primaryCoServiceAdvice', 'transferSpecialCaseField' => 'primarySpecialCaseField', 'transferSpecialCase' => 'primarySpecialCase', 'locality' => 'primaryLocality', 'firstPreferenceRow' => 'primaryFirstPreferenceRow', 'firstPreference' => 'primaryFirst', 'modeNote' => 'primaryModeNote');
    ob_start();
?>
        <div class="field-grid">
          <div class="field">
            <label for="eligibleChildren">Τέκνα που μοριοδοτούνται <small>1ο–2ο: 4 · 3ο: 6 · από το 4ο: 7 μόρια/τέκνο</small></label>
            <input id="eligibleChildren" type="number" min="0" max="20" step="1" value="0" inputmode="numeric">
          </div>
        </div>
        <div class="check-row"><label><input id="familyStatusEligible" type="checkbox"> Γάμος/Σύμφωνο Συμβίωσης/Γονέας με επιμέλεια (+4)</label></div>
        <div class="check-row"><label><input id="coService" type="checkbox"> Συνυπηρέτηση στην περιοχή/το δήμο που εξετάζω (+4)</label></div>
        <div class="field" id="transferCoServiceCaseField" style="display:none;margin:10px 0 14px 0;">
          <label for="transferCoServiceCase">Καθεστώς εργασίας συζύγου / συμβίου</label>
          <select id="transferCoServiceCase">
            <option value="unspecified">Επιλέξτε την περίπτωση για τα αντίστοιχα δικαιολογητικά</option>
            <option value="public_organic">Δημόσιος υπάλληλος / λειτουργός με οργανική θέση</option>
            <option value="teacher_organic">Μόνιμος εκπαιδευτικός με οργανική θέση</option>
            <option value="teacher_disposal">Μόνιμος εκπαιδευτικός στη διάθεση ΠΥΣΠΕ / ΠΥΣΔΕ</option>
            <option value="private_employee">Ιδιωτικός υπάλληλος</option>
            <option value="self_employed">Ελεύθερος επαγγελματίας / ατομική επιχείρηση</option>
            <option value="remote_employee">Αποκλειστική τηλεργασία</option>
            <option value="lawyer">Δικηγόρος</option>
            <option value="private_doctor">Ιατρός εκτός Δημοσίου</option>
            <option value="substitute_teacher">Αναπληρωτής εκπαιδευτικός</option>
            <option value="public_contract">Εργαζόμενος στο Δημόσιο χωρίς οργανική θέση, με σύμβαση</option>
            <option value="other">Άλλη / σύνθετη περίπτωση</option>
          </select>
          <div class="field-hint" id="transferCoServiceAdvice" role="status" aria-live="polite"></div>
        </div>
        <div class="field" id="transferSpecialCaseField" style="margin:12px 0">
          <label for="transferSpecialCase">Χρειάζομαι οδηγίες για ειδική κατηγορία ή προτεραιότητα; (χωρίς αυτόματη μοριοδότηση)</label>
          <select id="transferSpecialCase">
            <option value="none">Όχι / δεν έχω επιλέξει</option>
            <option value="health_committee">Ειδική κατηγορία υγείας — γνωμάτευση Β/θμιας ή Ανώτατης Υγειονομικής Επιτροπής</option>
            <option value="health_kepa">Ειδική κατηγορία υγείας — πιστοποίηση ΚΕΠΑ</option>
            <option value="judicial_spouse">Κατά προτεραιότητα μετάθεση συζύγου δικαστικού λειτουργού</option>
          </select>
          <div class="field-hint">Για μετάθεση από περιοχή σε περιοχή. Η επιλογή δείχνει μόνο τις ιδιαίτερες οδηγίες δικαιολογητικών· η αρμόδια υπηρεσία κρίνει αν συντρέχουν οι νόμιμες προϋποθέσεις.</div>
        </div>
        <div class="check-row"><label><input id="locality" type="checkbox"> Εντοπιότητα στην περιοχή/το δήμο που εξετάζω (+4)</label></div>
        <div class="check-row" id="firstPreferenceRow"><label><input id="firstPreference" type="checkbox"> Η περιοχή είναι η πρώτη προτίμησή μου (+2)</label></div>

<?php
    $html = ob_get_clean();
    if ($primary) {
        // Replace complete HTML id= / for= references only. Never change labels.
        foreach ($ids as $old => $new) {
            $html = str_replace('id="'.$old.'"', 'id="'.$new.'"', $html);
            $html = str_replace('for="'.$old.'"', 'for="'.$new.'"', $html);
        }
        // Additional evidence toggle is specific to the Primary document workflow;
        // keep it beside children, not among unrelated social criteria.
        $html = str_replace(
            '<div class="check-row"><label><input id="primaryFamily"',
            '<div class="check-row"><label><input type="checkbox" id="primaryChildrenExtra"> Χρειάζεται να αποστείλω πρόσθετο αποδεικτικό για τέκνο (ειδική περίπτωση)</label></div>' . "\n        " . '<div class="check-row"><label><input id="primaryFamily"',
            $html
        );
    }
    calculatorCardStart(array('title' => 'Γ. Κοινωνικά κριτήρια'));
    echo $html;
    // Primary-only procedure note and additional proof switch, not general-law rules.
    if ($primary) {
        echo $extraHtml;
    }
    calculatorDisclosure(array(

          'id' => $primary ? 'primaryModeNote' : 'modeNote',
          'summary' => 'Πώς αλλάζουν συνυπηρέτηση, εντοπιότητα και πρώτη προτίμηση;',
          'html' => 'Στη μετάθεση από περιοχή σε περιοχή, συνυπηρέτηση και εντοπιότητα εξετάζονται για την <strong>περιοχή μετάθεσης</strong>. Στην οριστική τοποθέτηση/βελτίωση συνδέονται με τον <strong>συγκεκριμένο Δήμο</strong> όπου βρίσκεται το σχολείο. Η πρώτη προτίμηση μοριοδοτείται μόνο στη μετάθεση από περιοχή σε περιοχή.',
          'open' => true,
          'attrs' => array('data-mobile-collapsed' => 'true')
        ));
    calculatorCardEnd();
}
