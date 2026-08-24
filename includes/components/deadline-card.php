<?php
/**
 * Shared application/deadline presentation component.
 *
 * Countdown behaviour lives in assets/common.js and styling in assets/common.css.
 * PHP syntax intentionally remains compatible with older server runtimes.
 */
if (!function_exists('renderDeadlineCard')) {
    function renderDeadlineCard($config = array())
    {
        if (!is_array($config)) {
            $config = array();
        }

        $title = isset($config['title']) ? (string) $config['title'] : '📅 Προθεσμία';
        $intro = isset($config['intro']) ? (string) $config['intro'] : '';
        $items = isset($config['items']) && is_array($config['items']) ? $config['items'] : array();
        $noteHtml = isset($config['note_html']) ? (string) $config['note_html'] : '';
        $extraClass = isset($config['class']) ? trim((string) $config['class']) : '';
        $headingId = isset($config['heading_id']) ? trim((string) $config['heading_id']) : '';

        if ($headingId === '') {
            static $deadlineCardCounter = 0;
            $deadlineCardCounter++;
            $headingId = 'eduDeadlineCardTitle' . $deadlineCardCounter;
        }

        $flags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $flags = $flags | ENT_SUBSTITUTE;
        }
        $h = function ($value) use ($flags) {
            return htmlspecialchars((string) $value, $flags, 'UTF-8');
        };

        $classes = 'deadline-card edu-deadline-card';
        if ($extraClass !== '') {
            $classes .= ' ' . $extraClass;
        }
        ?>
<section class="<?php echo $h($classes); ?>" aria-labelledby="<?php echo $h($headingId); ?>">
  <h2 id="<?php echo $h($headingId); ?>"><?php echo $h($title); ?></h2>
  <?php if ($intro !== '') { ?>
    <p class="edu-deadline-intro"><?php echo $intro; ?></p>
  <?php } ?>
  <div class="edu-deadline-grid">
    <?php foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $itemTitle = isset($item['title']) ? (string) $item['title'] : 'Προθεσμία';
        $metaHtml = isset($item['meta_html']) ? (string) $item['meta_html'] : '';
        $start = isset($item['start']) ? trim((string) $item['start']) : '';
        $end = isset($item['end']) ? trim((string) $item['end']) : '';
        $endExclusive = isset($item['end_exclusive']) ? trim((string) $item['end_exclusive']) : '';
        $openText = isset($item['open_text']) ? (string) $item['open_text'] : 'Η προθεσμία είναι ανοικτή.';
        $beforeText = isset($item['before_text']) ? (string) $item['before_text'] : 'Η προθεσμία δεν έχει ανοίξει ακόμη.';
        $closedText = isset($item['closed_text']) ? (string) $item['closed_text'] : 'Η προθεσμία έχει λήξει.';
        $sourceUrl = isset($item['source_url']) ? trim((string) $item['source_url']) : '';
        $sourceLabel = isset($item['source_label']) ? (string) $item['source_label'] : 'Επίσημη πηγή ↗';
        $toolUrl = isset($item['tool_url']) ? trim((string) $item['tool_url']) : '';
        $toolLabel = isset($item['tool_label']) ? (string) $item['tool_label'] : 'Σχετικό εργαλείο →';
        ?>
      <article class="edu-deadline-item">
        <h3><?php echo $h($itemTitle); ?></h3>
        <?php if ($metaHtml !== '') { ?>
          <p class="edu-deadline-meta"><?php echo $metaHtml; ?></p>
        <?php } ?>
        <div
          class="edu-deadline-status"
          role="status"
          aria-live="polite"
          data-edu-deadline
          <?php if ($start !== '') { ?>data-deadline-start="<?php echo $h($start); ?>"<?php } ?>
          <?php if ($end !== '') { ?>data-deadline-end="<?php echo $h($end); ?>"<?php } ?>
          <?php if ($endExclusive !== '') { ?>data-deadline-end-exclusive="<?php echo $h($endExclusive); ?>"<?php } ?>
          data-deadline-open-text="<?php echo $h($openText); ?>"
          data-deadline-before-text="<?php echo $h($beforeText); ?>"
          data-deadline-closed-text="<?php echo $h($closedText); ?>"
        >Έλεγχος προθεσμίας…</div>
        <?php if ($sourceUrl !== '' || $toolUrl !== '') { ?>
          <div class="edu-deadline-links">
            <?php if ($sourceUrl !== '') { ?><a href="<?php echo $h($sourceUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo $h($sourceLabel); ?></a><?php } ?>
            <?php if ($toolUrl !== '') { ?><a href="<?php echo $h($toolUrl); ?>"><?php echo $h($toolLabel); ?></a><?php } ?>
          </div>
        <?php } ?>
      </article>
    <?php } ?>
  </div>
  <?php if ($noteHtml !== '') { ?>
    <p class="edu-deadline-note"><?php echo $noteHtml; ?></p>
  <?php } ?>
</section>
        <?php
    }
}
