<?php
/**
 * Shared ASEP presentation component for Digital Tutoring service.
 *
 * Business rules and school-year limits live exclusively in
 * includes/service-calculations.js. Dynamic UI behaviour, details and
 * summaries live in includes/asep-digital-tutoring.js.
 *
 * Conservative PHP syntax is intentional for compatibility with older
 * server runtimes; the runtime version is not part of the filename.
 */
if (!function_exists('renderAsepDigitalTutoringService')) {
    function renderAsepDigitalTutoringService($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $containerId = isset($config['container_id']) ? (string) $config['container_id'] : 'digitalTutoring';
        $inputClass = isset($config['input_class']) ? trim((string) $config['input_class']) : 'service-months';
        $title = isset($config['title']) ? (string) $config['title'] : 'Ψηφιακό Φροντιστήριο';

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };
        ?>
<div
  id="<?php echo $h($containerId); ?>"
  class="asep-digital-tutoring-service"
  data-component="asep-digital-tutoring-service"
  data-service-role="digital-tutoring"
  data-input-class="<?php echo $h($inputClass); ?>"
>
  <h3><?php echo $h($title); ?></h3>
  <div class="note">
    <strong>1,5 μόρια ανά μήνα απασχόλησης</strong>, με ανώτατο όριο
    <strong>15 μόρια ανά σχολικό έτος</strong>.
  </div>
  <div class="note">
    Καταχώρισε κάθε σχολικό έτος χωριστά. Τα υπόλοιπα ημερών αθροίζονται
    μεταξύ των σχολικών ετών και κάθε 30 ημέρες μετατρέπονται σε έναν
    επιπλέον μήνα.
  </div>

  <div data-digital-tutoring-rows></div>

  <div class="actions asep-digital-tutoring-actions">
    <button type="button" class="secondary" data-digital-tutoring-add>+ Προσθήκη σχολικού έτους</button>
  </div>

  <div class="note hidden" data-digital-tutoring-status aria-live="polite"></div>
</div>
        <?php
    }
}
