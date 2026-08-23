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
      <label for="<?php echo $ids['regular2020']; ?>">Λοιπές τρίμηνες συμβάσεις — μήνες<small>1,5 μόριο ανά μήνα · έως 8 μήνες · έως 10 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['regular2020']; ?>" class="<?php echo $inputClass; ?>" type="number" min="0" max="8" step="1" inputmode="numeric" value="0">
    </div>
    <div class="field">
      <label for="<?php echo $ids['difficult2020']; ?>">Τρίμηνες σε δυσπρόσιτα / καταστήματα κράτησης — μήνες<small>3 μόρια ανά μήνα · έως 8 μήνες · έως 20 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['difficult2020']; ?>" class="<?php echo $inputClass; ?>" type="number" min="0" max="8" step="1" inputmode="numeric" value="0">
    </div>
  </div>
  <h3>Τρίμηνες συμβάσεις 2021–2022</h3>
  <div class="field-grid">
    <div class="field">
      <label for="<?php echo $ids['regular2021']; ?>">Λοιπές τρίμηνες συμβάσεις — μήνες<small>1,5 μόριο ανά μήνα · έως 7 μήνες · έως 10 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['regular2021']; ?>" class="<?php echo $inputClass; ?>" type="number" min="0" max="7" step="1" inputmode="numeric" value="0">
    </div>
    <div class="field">
      <label for="<?php echo $ids['difficult2021']; ?>">Τρίμηνες σε δυσπρόσιτα / καταστήματα κράτησης — μήνες<small>3 μόρια ανά μήνα · έως 7 μήνες · έως 20 μόρια για το σχολικό έτος.</small></label>
      <input id="<?php echo $ids['difficult2021']; ?>" class="<?php echo $inputClass; ?>" type="number" min="0" max="7" step="1" inputmode="numeric" value="0">
    </div>
  </div>
  <div class="note asep-three-month-note">Οι μήνες των τρίμηνων συμβάσεων δηλώνονται μόνο στα αντίστοιχα πεδία και δεν πρέπει να δηλώνονται ξανά στη λοιπή ή στη δυσπρόσιτη προϋπηρεσία. Τα ανώτατα όρια εφαρμόζονται χωριστά ανά σχολικό έτος.</div>
</div>
<?php
    }
}
