<?php
/** Shared renderer for one directory tool card. PHP 5.6-compatible. */
if (!function_exists('renderDirectoryToolCard')) {
    function renderDirectoryToolCard($tool)
    {
        if (!is_array($tool)) {
            return;
        }

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };

        $number = isset($tool['number']) ? (int) $tool['number'] : 0;
        $href = isset($tool['href']) ? (string) $tool['href'] : '#';
        $group = isset($tool['group']) ? (string) $tool['group'] : '';
        $categories = isset($tool['categories']) ? (string) $tool['categories'] : '';
        $search = isset($tool['search']) ? (string) $tool['search'] : '';
        $tag = isset($tool['tag']) ? (string) $tool['tag'] : '';
        $tagClass = isset($tool['tag_class']) ? trim((string) $tool['tag_class']) : '';
        $title = isset($tool['title']) ? (string) $tool['title'] : '';
        $description = isset($tool['description']) ? (string) $tool['description'] : '';
        $isNew = !empty($tool['new']);
        ?>
<a
  class="tool-card"
  data-group="<?php echo $h($group); ?>"
  data-category="<?php echo $h($categories); ?>"
  data-search="<?php echo $h($search); ?>"
  href="<?php echo $h($href); ?>"
>
  <?php if ($isNew) { ?><span class="new-badge">ΝΕΟ</span><?php } ?>
  <div class="card-top">
    <span class="tool-number"><?php echo $number; ?></span>
    <?php if ($tag !== '') { ?>
      <span class="category-tag<?php echo $tagClass !== '' ? ' ' . $h($tagClass) : ''; ?>"><?php echo $h($tag); ?></span>
    <?php } ?>
  </div>
  <h3><?php echo $h($title); ?></h3>
  <p><?php echo $h($description); ?></p>
  <span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
        <?php
    }
}
