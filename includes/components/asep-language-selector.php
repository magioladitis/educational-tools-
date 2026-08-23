<?php
/**
 * Shared presentation shell for ASEP foreign-language scoring.
 *
 * All scoring/max-language/specialty-exclusion rules live in
 * includes/language-calculations.js. Dynamic fields and duplicate locks live in
 * includes/asep-language-selector.js.
 */
if (!function_exists('renderAsepLanguageSelector')) {
    function renderAsepLanguageSelector($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $id = isset($config['id']) ? (string) $config['id'] : 'asepLanguages';
        $profile = isset($config['profile']) ? (string) $config['profile'] : 'pe';
        $specialtyId = isset($config['specialty_id']) ? (string) $config['specialty_id'] : '';
        $fieldClass = isset($config['field_class']) ? trim((string) $config['field_class']) : 'field';

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };
        ?>
<div
  id="<?php echo $h($id); ?>"
  class="asep-language-selector"
  data-component="asep-language-selector"
  data-profile="<?php echo $h($profile); ?>"
  data-specialty-id="<?php echo $h($specialtyId); ?>"
  data-field-class="<?php echo $h($fieldClass); ?>"
>
  <h3 data-language-title>Ξένες γλώσσες</h3>
  <div class="note" data-language-intro></div>
  <div data-language-fields></div>
  <div class="note hidden" data-language-context aria-live="polite"></div>
  <div class="note hidden" data-language-status aria-live="polite"></div>
</div>
        <?php
    }
}
