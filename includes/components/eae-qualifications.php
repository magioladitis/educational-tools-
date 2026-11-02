<?php
/** Shared EAE qualification fields. IDs and values are stable integration contracts.
 * Catalogue matching can attach to data-eae-key="msc"; no recognition inferred here.
 */
function eaeQualificationLabel($key, $profile) {
    $labels = array(
        'phd' => 'Διδακτορικό στην ΕΑΕ ή στη Σχολική Ψυχολογία',
        'msc' => 'Μεταπτυχιακό στην ΕΑΕ ή στη Σχολική Ψυχολογία',
        'retraining' => 'Πτυχίο διετούς μετεκπαίδευσης στην ΕΑΕ των Διδασκαλείων',
        'fiveYear' => 'Πτυχίο ΑΕΙ με τουλάχιστον πενταετή αποδεδειγμένη προϋπηρεσία στην ΕΑΕ',
        'pe11' => 'ΠΕ11: πτυχίο ΤΕΦΑΑ με προβλεπόμενη κύρια ειδικότητα στην ΕΑΕ / Ειδική ή Προσαρμοσμένη Φυσική Αγωγή',
    );
    $label = $labels[$key];
    if (($profile === 'asep-te' || $profile === 'asep-de') && in_array($key, array('phd','msc','retraining'), true)) {
        $label .= ', με βασικές σπουδές σε ΑΕΙ';
    }
    return htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
}

function renderEaeQualifications($profile) {
    switch ($profile) {
        case 'asep-pe': ?>
<div class="check"><input type="checkbox" id="phdEae" data-eae-main="phd" data-eae-key="phd"><label for="phdEae"><?php echo eaeQualificationLabel('phd', $profile); ?><small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως διδακτορικό.</small></label></div>
<div class="check"><input type="checkbox" id="masterEae" data-eae-main="msc" data-eae-key="msc"><label for="masterEae"><?php echo eaeQualificationLabel('msc', $profile); ?><small>Αποτελεί κριτήριο ένταξης στον κύριο πίνακα και μοριοδοτείται ως μεταπτυχιακό.</small></label></div>
<div class="check"><input type="checkbox" id="didaskaleio" data-eae-main="retraining" data-eae-key="retraining"><label for="didaskaleio"><?php echo eaeQualificationLabel('retraining', $profile); ?><small>Κριτήριο ένταξης. Η προκήρυξη δεν ορίζει χωριστή πρόσθετη μοριοδότηση γι’ αυτό το πτυχίο στον πίνακα κριτηρίων.</small></label></div>
<div class="check"><input type="checkbox" id="fiveYearEae" data-eae-main="fiveYear" data-eae-key="fiveYear"><label for="fiveYearEae"><?php echo eaeQualificationLabel('fiveYear', $profile); ?><small>Χρησιμοποιείται για τον έλεγχο ένταξης. Τους μήνες προϋπηρεσίας τους δηλώνεις χωριστά παρακάτω για τη μοριοδότηση.</small></label></div>
<div class="check hidden" id="pe11QualWrap"><input type="checkbox" id="pe11Qual" data-eae-main="pe11" data-eae-key="pe11"><label for="pe11Qual"><?php echo eaeQualificationLabel('pe11', $profile); ?><small>Κριτήριο ένταξης και +8 μόρια.</small></label></div>
<?php break;
        case 'asep-de':
        case 'asep-te': ?>
<select id="mainCriterion" data-eae-main-select>
            <option value="none">Δεν διαθέτω κάποιο από τα παρακάτω</option>
            <option value="phd" data-eae-key="phd"><?php echo eaeQualificationLabel('phd', $profile); ?></option>
            <option value="msc" data-eae-key="msc"><?php echo eaeQualificationLabel('msc', $profile); ?></option>
            <option value="retraining" data-eae-key="retraining"><?php echo eaeQualificationLabel('retraining', $profile); ?></option>
            <option value="aei5years" data-eae-key="fiveYear"><?php echo eaeQualificationLabel('fiveYear', $profile); ?></option>
          </select>
<?php break;
        case 'transfer-secondary': ?>
<div class="transfer-eae-qualification-list">
              <label><input type="checkbox" data-eae-qualification="doctorate" data-eae-key="phd"><?php echo eaeQualificationLabel('phd', $profile); ?></label>
              <label><input type="checkbox" data-eae-qualification="masters" data-eae-key="msc"><?php echo eaeQualificationLabel('msc', $profile); ?></label>
              <label><input type="checkbox" data-eae-qualification="two_year" data-eae-key="retraining"><?php echo eaeQualificationLabel('retraining', $profile); ?></label>
              <label><input type="checkbox" data-eae-qualification="experience" data-eae-key="fiveYear"><?php echo eaeQualificationLabel('fiveYear', $profile); ?></label>
              <label id="transferEaePe11QualificationRow"><input type="checkbox" data-eae-qualification="pe11" data-eae-key="pe11"><?php echo eaeQualificationLabel('pe11', $profile); ?></label>
            </div>
<?php break;
        case 'transfer-primary': ?>
<label><input type="checkbox" id="primaryEaePhd" data-eae-key="phd"><?php echo eaeQualificationLabel('phd', $profile); ?></label>
            <label><input type="checkbox" id="primaryEaeMasters" data-eae-key="msc"><?php echo eaeQualificationLabel('msc', $profile); ?></label>
            <label><input type="checkbox" id="primaryEaeRetraining" data-eae-key="retraining"><?php echo eaeQualificationLabel('retraining', $profile); ?></label>
            <label><input type="checkbox" id="primaryEaeExperience" data-eae-key="fiveYear"><?php echo eaeQualificationLabel('fiveYear', $profile); ?></label>
            <label><input type="checkbox" id="primaryEaePe11" data-eae-key="pe11"><?php echo eaeQualificationLabel('pe11', $profile); ?></label>
<?php break;
        default: throw new InvalidArgumentException('Unknown EAE qualification profile');
    }
}
