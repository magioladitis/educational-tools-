<?php
/**
 * Shared presentation for the historical ASEP three-month service contracts
 * of school years 2020–2021 and 2021–2022.
 * Calculation remains in includes/service-calculations.js.
 */
if (!function_exists('renderAsepThreeMonthService')) {
    function renderAsepThreeMonthService($config)
    {
        $required = array('regular_2020_id', 'difficult_2020_id', 'regular_2021_id', 'difficult_2021_id');
        foreach ($required as $key) {
            if (!isset($config[$key]) || $config[$key] === '') {
                return;
            }
        }
        $escape = function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };
        $ids = array(
            'regular2020' => $escape($config['regular_2020_id']),
            'difficult2020' => $escape($config['difficult_2020_id']),
            'regular2021' => $escape($config['regular_2021_id']),
            'difficult2021' => $escape($config['difficult_2021_id'])
        );
        $inputClass = isset($config['input_class']) ? trim((string) $config['input_class']) : 'service-months';
        $inputClass = $escape($inputClass);
        ?>
<div class="asep-three-month-service" data-component="asep-three-month-service">
  <h3>Τρίμηνες συμβάσεις 2020–2021</h3>
  <div class="field-grid">
    <div class="field">
      <label for="<?php echo $ids['regular2020']; ?>">Λοιπές τρίμηνες συμβάσεις — μήνες<small>1,5 μόριο ανά μήνα · ιστορικά έως 8 πλήρεις μήνες · έως 10 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['regular2020']; ?>" class="<?php echo $inputClass; ?>" data-service-role="three-month-regular-2020" type="number" min="0" max="8" step="1" inputmode="numeric" value="0">
    </div>
    <div class="field">
      <label for="<?php echo $ids['difficult2020']; ?>">Τρίμηνες σε δυσπρόσιτα / καταστήματα κράτησης — μήνες<small>3 μόρια ανά μήνα · ιστορικά έως 8 πλήρεις μήνες · έως 20 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['difficult2020']; ?>" class="<?php echo $inputClass; ?>" data-service-role="three-month-difficult-2020" type="number" min="0" max="8" step="1" inputmode="numeric" value="0">
    </div>
  </div>
  <h3>Τρίμηνες συμβάσεις 2021–2022</h3>
  <div class="field-grid">
    <div class="field">
      <label for="<?php echo $ids['regular2021']; ?>">Λοιπές τρίμηνες συμβάσεις — μήνες<small>1,5 μόριο ανά μήνα · ιστορικά έως 7 πλήρεις μήνες · έως 10 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['regular2021']; ?>" class="<?php echo $inputClass; ?>" data-service-role="three-month-regular-2021" type="number" min="0" max="7" step="1" inputmode="numeric" value="0">
    </div>
    <div class="field">
      <label for="<?php echo $ids['difficult2021']; ?>">Τρίμηνες σε δυσπρόσιτα / καταστήματα κράτησης — μήνες<small>3 μόρια ανά μήνα · ιστορικά έως 7 πλήρεις μήνες · έως 20 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['difficult2021']; ?>" class="<?php echo $inputClass; ?>" data-service-role="three-month-difficult-2021" type="number" min="0" max="7" step="1" inputmode="numeric" value="0">
    </div>
  </div>
  <div class="note asep-three-month-note">Οι μήνες των τρίμηνων συμβάσεων δηλώνονται μόνο στα αντίστοιχα πεδία και δεν πρέπει να δηλώνονται ξανά στη λοιπή ή στη δυσπρόσιτη προϋπηρεσία. Για το <strong>2020–2021</strong> το ιστορικό μέγιστο είναι <strong>8 πλήρεις μήνες</strong> και για το <strong>2021–2022</strong> <strong>7 πλήρεις μήνες</strong>, επειδή οι συγκεκριμένες προσλήψεις δεν πραγματοποιήθηκαν από την αρχή του σχολικού έτους. Τα όρια 8/7 μηνών αποτυπώνουν τη μέγιστη πραγματικά δυνατή υπηρεσία των συγκεκριμένων κύκλων και δεν αποτελούν αυτοτελές ανώτατο όριο μοριοδότησης της προκήρυξης. Τα ανώτατα όρια μορίων παραμένουν 10 ή 20 ανά σχολικό έτος, ανά περίπτωση.</div>
</div>
<?php
    }
}
