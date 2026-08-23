<?php
/**
 * Κοινό πεδίο εκπαιδευτικής υπηρεσίας στο Ψηφιακό Φροντιστήριο.
 */
if (!function_exists('renderAsepDigitalTutoring')) {
    function renderAsepDigitalTutoring($config = array())
    {
        $id = isset($config['id']) ? (string) $config['id'] : 'digitalTutoring';
        $schoolYears = isset($config['school_years']) && is_array($config['school_years'])
            ? $config['school_years']
            : array(
                '2024–2025' => array('months' => 9, 'days' => 16),
                '2025–2026' => array('months' => 8, 'days' => 2),
            );

        if (!$schoolYears) {
            return;
        }

        $rulesJson = htmlspecialchars(json_encode($schoolYears), ENT_QUOTES, 'UTF-8');
        reset($schoolYears);
        $firstYear = (string) key($schoolYears);
        $firstRule = is_array($schoolYears[$firstYear]) ? $schoolYears[$firstYear] : array('months' => (int) $schoolYears[$firstYear], 'days' => 29);
        $firstMaxMonths = isset($firstRule['months']) ? (int) $firstRule['months'] : 0;
        $firstMaxDays = isset($firstRule['days']) ? (int) $firstRule['days'] : 29;
        ?>
        <div class="note asep-digital-tutoring" id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" data-digital-tutoring data-school-years="<?= $rulesJson ?>">
          <strong>Ψηφιακό Φροντιστήριο</strong>
          <div>1,5 μόρια ανά μήνα απασχόλησης, με ανώτατο όριο 15 μόρια <strong>ανά σχολικό έτος</strong>.</div>
          <small>Καταχώρισε κάθε σχολικό έτος χωριστά. Τα υπόλοιπα ημερών αθροίζονται μεταξύ των σχολικών ετών και κάθε 30 ημέρες μετατρέπονται σε έναν επιπλέον μήνα.</small>

          <div class="digital-school-years edu-mt-14" data-digital-rows>
            <div class="digital-school-year" data-digital-row>
              <div class="field-grid">
                <div class="field">
                  <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>Year1" data-digital-year-caption>1ο σχολικό έτος</label>
                  <select id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>Year1" class="digital-year-label" data-digital-year>
                    <?php foreach ($schoolYears as $year => $yearRule): ?>
                      <option value="<?= htmlspecialchars((string) $year, ENT_QUOTES, 'UTF-8') ?>"<?= (string) $year === $firstYear ? ' selected' : '' ?>><?= htmlspecialchars((string) $year, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="field">
                  <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>Months1" data-digital-months-caption>Πλήρεις μήνες<small>Μέγιστη διάρκεια έτους: <?= $firstMaxMonths ?> μήνες και <?= $firstMaxDays ?> ημέρες</small></label>
                  <input id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>Months1" class="digital-months service-months" data-digital-months type="number" min="0" max="<?= $firstMaxMonths ?>" step="1" value="0" inputmode="numeric">
                </div>
                <div class="field">
                  <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>Days1" data-digital-days-caption>Υπόλοιπο ημερών<small>Έως <?= $firstMaxDays ?> ημέρες όταν δηλωθούν <?= $firstMaxMonths ?> μήνες</small></label>
                  <input id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>Days1" class="digital-days service-months" data-digital-days type="number" min="0" max="29" step="1" value="0" inputmode="numeric">
                </div>
              </div>
            </div>
          </div>

          <div class="actions edu-mt-14">
            <button type="button" class="secondary" data-digital-add>+ Προσθήκη σχολικού έτους</button>
          </div>
        </div>
        <?php
    }
}
