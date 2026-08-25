<?php
/**
 * Shared structural helpers for calculator pages.
 *
 * Presentation only: this component must not contain scoring/business rules.
 * Conservative PHP syntax is intentional for compatibility with the server runtime.
 */

if (!function_exists('calculatorLayoutEscape')) {
    function calculatorLayoutEscape($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('calculatorLayoutAttributes')) {
    function calculatorLayoutAttributes($attrs) {
        if (!is_array($attrs) || count($attrs) === 0) return '';
        $out = '';
        foreach ($attrs as $name => $value) {
            if (!preg_match('/^[a-zA-Z_:][-a-zA-Z0-9_:.]*$/', (string)$name)) continue;
            if ($value === null || $value === false) continue;
            if ($value === true) {
                $out .= ' ' . calculatorLayoutEscape($name);
            } else {
                $out .= ' ' . calculatorLayoutEscape($name) . '="' . calculatorLayoutEscape($value) . '"';
            }
        }
        return $out;
    }
}

if (!function_exists('calculatorLayoutOpenTag')) {
    function calculatorLayoutOpenTag($tag, $class, $id, $attrs) {
        $tag = preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string)$tag) ? $tag : 'div';
        $all = is_array($attrs) ? $attrs : array();
        if ($id !== null && $id !== '') $all['id'] = $id;
        if ($class !== null && $class !== '') $all['class'] = $class;
        echo '<' . $tag . calculatorLayoutAttributes($all) . '>';
        return $tag;
    }
}

if (!function_exists('calculatorLayoutStackPush')) {
    function calculatorLayoutStackPush($name, $tags) {
        if (!isset($GLOBALS['calculatorLayoutStacks'])) $GLOBALS['calculatorLayoutStacks'] = array();
        if (!isset($GLOBALS['calculatorLayoutStacks'][$name])) $GLOBALS['calculatorLayoutStacks'][$name] = array();
        $GLOBALS['calculatorLayoutStacks'][$name][] = $tags;
    }
}

if (!function_exists('calculatorLayoutStackPop')) {
    function calculatorLayoutStackPop($name) {
        if (!isset($GLOBALS['calculatorLayoutStacks'][$name]) || count($GLOBALS['calculatorLayoutStacks'][$name]) === 0) return array();
        return array_pop($GLOBALS['calculatorLayoutStacks'][$name]);
    }
}

if (!function_exists('calculatorLayoutTextOrHtml')) {
    function calculatorLayoutTextOrHtml($config, $textKey, $htmlKey) {
        if (isset($config[$htmlKey]) && $config[$htmlKey] !== '') return (string)$config[$htmlKey];
        if (isset($config[$textKey])) return calculatorLayoutEscape($config[$textKey]);
        return '';
    }
}


if (!function_exists('calculatorContainerStart')) {
    function calculatorContainerStart($config = array()) {
        $config = is_array($config) ? $config : array();
        $tag = isset($config['tag']) ? $config['tag'] : 'div';
        $class = isset($config['class']) ? $config['class'] : '';
        $id = isset($config['id']) ? $config['id'] : null;
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        $opened = calculatorLayoutOpenTag($tag, $class, $id, $attrs);
        calculatorLayoutStackPush('container', array($opened));
    }
}

if (!function_exists('calculatorContainerEnd')) {
    function calculatorContainerEnd() {
        $tags = calculatorLayoutStackPop('container');
        foreach (array_reverse($tags) as $tag) echo '</' . $tag . '>';
    }
}

if (!function_exists('calculatorInlineResult')) {
    function calculatorInlineResult($config = array()) {
        $config = is_array($config) ? $config : array();
        $tag = isset($config['tag']) ? $config['tag'] : 'div';
        $class = isset($config['class']) ? $config['class'] : 'result';
        $id = isset($config['id']) ? $config['id'] : 'result';
        $attrs = isset($config['attrs']) && is_array($config['attrs']) ? $config['attrs'] : array();
        calculatorLayoutOpenTag($tag, $class, $id, $attrs);
        echo '</' . (preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string)$tag) ? $tag : 'div') . '>';
    }
}

if (!function_exists('calculatorHeroStart')) {
    function calculatorHeroStart($config = array()) {
        $config = is_array($config) ? $config : array();
        $class = isset($config['class']) ? $config['class'] : 'hero';
        $id = isset($config['id']) ? $config['id'] : null;
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        $opened = calculatorLayoutOpenTag('section', $class, $id, $attrs);
        calculatorLayoutStackPush('hero', array($opened));
    }
}

if (!function_exists('calculatorHeroEnd')) {
    function calculatorHeroEnd() {
        $tags = calculatorLayoutStackPop('hero');
        foreach (array_reverse($tags) as $tag) echo '</' . $tag . '>';
    }
}

if (!function_exists('calculatorHero')) {
    function calculatorHero($config = array()) {
        $config = is_array($config) ? $config : array();
        $class = isset($config['class']) ? $config['class'] : 'hero';
        $id = isset($config['id']) ? $config['id'] : null;
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        calculatorLayoutOpenTag('section', $class, $id, $attrs);

        $title = calculatorLayoutTextOrHtml($config, 'title', 'title_html');
        if ($title !== '') echo '<h1>' . $title . '</h1>';

        $intro = calculatorLayoutTextOrHtml($config, 'intro', 'intro_html');
        if ($intro !== '') {
            $introAttrs = isset($config['intro_attrs']) && is_array($config['intro_attrs']) ? $config['intro_attrs'] : array();
            echo '<p' . calculatorLayoutAttributes($introAttrs) . '>' . $intro . '</p>';
        }

        $badges = isset($config['badges']) && is_array($config['badges']) ? $config['badges'] : array();
        if (count($badges) > 0) {
            $metaClass = isset($config['meta_class']) ? $config['meta_class'] : 'meta';
            echo '<div class="' . calculatorLayoutEscape($metaClass) . '">';
            foreach ($badges as $badge) {
                if (is_array($badge)) {
                    $badgeAttrs = isset($badge['attrs']) ? $badge['attrs'] : array();
                    $badgeHtml = isset($badge['html']) ? (string)$badge['html'] : calculatorLayoutEscape(isset($badge['text']) ? $badge['text'] : '');
                    echo '<span' . calculatorLayoutAttributes($badgeAttrs) . '>' . $badgeHtml . '</span>';
                } else {
                    echo '<span>' . calculatorLayoutEscape($badge) . '</span>';
                }
            }
            echo '</div>';
        }
        echo '</section>';
    }
}

if (!function_exists('calculatorColumnsStart')) {
    function calculatorColumnsStart($config = array()) {
        $config = is_array($config) ? $config : array();
        $tag = isset($config['tag']) ? $config['tag'] : 'div';
        $class = isset($config['class']) ? $config['class'] : 'layout';
        $id = isset($config['id']) ? $config['id'] : null;
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        $opened = calculatorLayoutOpenTag($tag, $class, $id, $attrs);
        calculatorLayoutStackPush('columns', array($opened));
    }
}

if (!function_exists('calculatorColumnsEnd')) {
    function calculatorColumnsEnd() {
        $tags = calculatorLayoutStackPop('columns');
        foreach (array_reverse($tags) as $tag) echo '</' . $tag . '>';
    }
}

if (!function_exists('calculatorMainStart')) {
    function calculatorMainStart($config = array()) {
        $config = is_array($config) ? $config : array();
        $tag = isset($config['tag']) ? $config['tag'] : 'div';
        $class = isset($config['class']) ? $config['class'] : '';
        $id = isset($config['id']) ? $config['id'] : null;
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        $opened = calculatorLayoutOpenTag($tag, $class, $id, $attrs);
        calculatorLayoutStackPush('main', array($opened));
    }
}

if (!function_exists('calculatorMainEnd')) {
    function calculatorMainEnd() {
        $tags = calculatorLayoutStackPop('main');
        foreach (array_reverse($tags) as $tag) echo '</' . $tag . '>';
    }
}

if (!function_exists('calculatorCardStart')) {
    function calculatorCardStart($config = array()) {
        $config = is_array($config) ? $config : array();
        $tag = isset($config['tag']) ? $config['tag'] : 'section';
        $class = isset($config['class']) ? $config['class'] : 'card';
        $id = isset($config['id']) ? $config['id'] : null;
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        $opened = calculatorLayoutOpenTag($tag, $class, $id, $attrs);
        calculatorLayoutStackPush('card', array($opened));

        $title = calculatorLayoutTextOrHtml($config, 'title', 'title_html');
        $subtitle = calculatorLayoutTextOrHtml($config, 'subtitle', 'subtitle_html');
        $cap = calculatorLayoutTextOrHtml($config, 'cap', 'cap_html');
        $headerVariant = isset($config['header_variant']) ? $config['header_variant'] : 'plain';

        if ($headerVariant === 'section-head') {
            if ($title !== '' || $subtitle !== '' || $cap !== '') {
                echo '<div class="section-head"><div>';
                if ($title !== '') {
                    $titleAttrs = isset($config['title_attrs']) && is_array($config['title_attrs']) ? $config['title_attrs'] : array();
                    echo '<h2' . calculatorLayoutAttributes($titleAttrs) . '>' . $title . '</h2>';
                }
                if ($subtitle !== '') {
                    $subtitleAttrs = isset($config['subtitle_attrs']) && is_array($config['subtitle_attrs']) ? $config['subtitle_attrs'] : array();
                    if (!isset($subtitleAttrs['class'])) $subtitleAttrs['class'] = 'subtitle';
                    echo '<p' . calculatorLayoutAttributes($subtitleAttrs) . '>' . $subtitle . '</p>';
                }
                echo '</div>';
                if ($cap !== '') {
                    $capAttrs = isset($config['cap_attrs']) && is_array($config['cap_attrs']) ? $config['cap_attrs'] : array();
                    if (!isset($capAttrs['class'])) $capAttrs['class'] = 'max';
                    echo '<div' . calculatorLayoutAttributes($capAttrs) . '>' . $cap . '</div>';
                }
                echo '</div>';
            }
        } else {
            if ($title !== '') {
                $titleAttrs = isset($config['title_attrs']) && is_array($config['title_attrs']) ? $config['title_attrs'] : array();
                echo '<h2' . calculatorLayoutAttributes($titleAttrs) . '>' . $title . '</h2>';
            }
            if ($subtitle !== '') {
                $subtitleAttrs = isset($config['subtitle_attrs']) && is_array($config['subtitle_attrs']) ? $config['subtitle_attrs'] : array();
                if (!isset($subtitleAttrs['class'])) $subtitleAttrs['class'] = isset($config['subtitle_class']) ? $config['subtitle_class'] : 'subtitle';
                echo '<p' . calculatorLayoutAttributes($subtitleAttrs) . '>' . $subtitle . '</p>';
            }
            if ($cap !== '') {
                $capAttrs = isset($config['cap_attrs']) && is_array($config['cap_attrs']) ? $config['cap_attrs'] : array();
                if (!isset($capAttrs['class'])) $capAttrs['class'] = isset($config['cap_class']) ? $config['cap_class'] : 'cap';
                echo '<p' . calculatorLayoutAttributes($capAttrs) . '>' . $cap . '</p>';
            }
        }
    }
}

if (!function_exists('calculatorCardEnd')) {
    function calculatorCardEnd() {
        $tags = calculatorLayoutStackPop('card');
        foreach (array_reverse($tags) as $tag) echo '</' . $tag . '>';
    }
}

if (!function_exists('calculatorResultsStart')) {
    function calculatorResultsStart($config = array()) {
        $config = is_array($config) ? $config : array();
        $variant = isset($config['variant']) ? $config['variant'] : 'card-aside';
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        if (isset($config['aria_live']) && $config['aria_live'] !== '') $attrs['aria-live'] = $config['aria_live'];
        $id = isset($config['id']) ? $config['id'] : null;
        $tags = array();

        if ($variant === 'nested-card') {
            $asideClass = isset($config['class']) ? $config['class'] : 'results';
            $tags[] = calculatorLayoutOpenTag('aside', $asideClass, $id, $attrs);
            $nestedClass = isset($config['nested_class']) ? $config['nested_class'] : 'card';
            $nestedAttrs = isset($config['nested_attrs']) ? $config['nested_attrs'] : array();
            $tags[] = calculatorLayoutOpenTag('section', $nestedClass, null, $nestedAttrs);
        } else {
            $class = isset($config['class']) ? $config['class'] : ($variant === 'result-card' ? 'card result-card' : 'card results');
            $tags[] = calculatorLayoutOpenTag('aside', $class, $id, $attrs);
        }
        calculatorLayoutStackPush('results', $tags);
    }
}

if (!function_exists('calculatorResultsEnd')) {
    function calculatorResultsEnd() {
        $tags = calculatorLayoutStackPop('results');
        foreach (array_reverse($tags) as $tag) echo '</' . $tag . '>';
    }
}



if (!function_exists('calculatorSubtotalRow')) {
    function calculatorSubtotalRow($config = array()) {
        $config = is_array($config) ? $config : array();
        $class = isset($config['class']) ? $config['class'] : 'subtot';
        $attrs = isset($config['attrs']) && is_array($config['attrs']) ? $config['attrs'] : array();
        $id = isset($config['id']) ? $config['id'] : null;
        calculatorLayoutOpenTag('div', $class, $id, $attrs);

        $labelAttrs = isset($config['label_attrs']) && is_array($config['label_attrs']) ? $config['label_attrs'] : array();
        if (isset($config['label_id'])) $labelAttrs['id'] = $config['label_id'];
        $label = calculatorLayoutTextOrHtml($config, 'label', 'label_html');
        echo '<span' . calculatorLayoutAttributes($labelAttrs) . '>' . $label . '</span>';

        $valueAttrs = isset($config['value_attrs']) && is_array($config['value_attrs']) ? $config['value_attrs'] : array();
        if (isset($config['value_id'])) $valueAttrs['id'] = $config['value_id'];
        if (!isset($valueAttrs['class'])) $valueAttrs['class'] = isset($config['value_class']) ? $config['value_class'] : 'pill';
        $value = calculatorLayoutTextOrHtml($config, 'value', 'value_html');
        echo '<span' . calculatorLayoutAttributes($valueAttrs) . '>' . $value . '</span>';
        echo '</div>';
    }
}

if (!function_exists('calculatorScoreHeader')) {
    function calculatorScoreHeader($config = array()) {
        $config = is_array($config) ? $config : array();
        $variant = isset($config['variant']) && $config['variant'] !== '' ? $config['variant'] : 'standard';
        $variant = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$variant);
        if ($variant === '') $variant = 'standard';

        $class = 'result-score-header result-score-header--' . $variant;
        if (isset($config['class']) && $config['class'] !== '') $class .= ' ' . $config['class'];
        $attrs = isset($config['attrs']) && is_array($config['attrs']) ? $config['attrs'] : array();
        if (!isset($attrs['role'])) $attrs['role'] = 'group';
        if (!isset($attrs['aria-label'])) $attrs['aria-label'] = isset($config['aria_label']) ? $config['aria_label'] : 'Αποτέλεσμα';
        $id = isset($config['id']) ? $config['id'] : null;
        calculatorLayoutOpenTag('div', $class, $id, $attrs);

        $context = calculatorLayoutTextOrHtml($config, 'context', 'context_html');
        if ($context !== '') {
            $contextAttrs = isset($config['context_attrs']) && is_array($config['context_attrs']) ? $config['context_attrs'] : array();
            if (isset($config['context_id'])) $contextAttrs['id'] = $config['context_id'];
            if (!isset($contextAttrs['class'])) $contextAttrs['class'] = 'result-score-context';
            echo '<div' . calculatorLayoutAttributes($contextAttrs) . '>' . $context . '</div>';
        }

        $valueTag = isset($config['value_tag']) ? $config['value_tag'] : 'div';
        $valueTag = preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string)$valueTag) ? $valueTag : 'div';
        $valueAttrs = isset($config['value_attrs']) && is_array($config['value_attrs']) ? $config['value_attrs'] : array();
        if (isset($config['value_id'])) $valueAttrs['id'] = $config['value_id'];
        if (!isset($valueAttrs['class'])) $valueAttrs['class'] = isset($config['value_class']) ? $config['value_class'] : 'result-score';
        $value = calculatorLayoutTextOrHtml($config, 'value', 'value_html');
        echo '<' . $valueTag . calculatorLayoutAttributes($valueAttrs) . '>' . $value . '</' . $valueTag . '>';

        $label = calculatorLayoutTextOrHtml($config, 'label', 'label_html');
        if ($label !== '') {
            $labelAttrs = isset($config['label_attrs']) && is_array($config['label_attrs']) ? $config['label_attrs'] : array();
            if (isset($config['label_id'])) $labelAttrs['id'] = $config['label_id'];
            if (!isset($labelAttrs['class'])) $labelAttrs['class'] = isset($config['label_class']) ? $config['label_class'] : 'result-score-label';
            echo '<div' . calculatorLayoutAttributes($labelAttrs) . '>' . $label . '</div>';
        }

        $cap = calculatorLayoutTextOrHtml($config, 'cap', 'cap_html');
        if ($cap !== '') {
            $capAttrs = isset($config['cap_attrs']) && is_array($config['cap_attrs']) ? $config['cap_attrs'] : array();
            if (isset($config['cap_id'])) $capAttrs['id'] = $config['cap_id'];
            if (!isset($capAttrs['class'])) $capAttrs['class'] = isset($config['cap_class']) ? $config['cap_class'] : 'result-score-cap';
            echo '<div' . calculatorLayoutAttributes($capAttrs) . '>' . $cap . '</div>';
        }
        echo '</div>';
    }
}

if (!function_exists('calculatorTotalBlock')) {
    function calculatorTotalBlock($config = array()) {
        $config = is_array($config) ? $config : array();
        $class = isset($config['class']) ? $config['class'] : 'total';
        $attrs = isset($config['attrs']) && is_array($config['attrs']) ? $config['attrs'] : array();
        $id = isset($config['id']) ? $config['id'] : null;
        calculatorLayoutOpenTag('div', $class, $id, $attrs);

        $valueTag = isset($config['value_tag']) ? $config['value_tag'] : 'div';
        $valueTag = preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string)$valueTag) ? $valueTag : 'div';
        $valueAttrs = isset($config['value_attrs']) && is_array($config['value_attrs']) ? $config['value_attrs'] : array();
        if (isset($config['value_id'])) $valueAttrs['id'] = $config['value_id'];
        if (!isset($valueAttrs['class'])) $valueAttrs['class'] = isset($config['value_class']) ? $config['value_class'] : 'num';
        $value = calculatorLayoutTextOrHtml($config, 'value', 'value_html');
        echo '<' . $valueTag . calculatorLayoutAttributes($valueAttrs) . '>' . $value . '</' . $valueTag . '>';

        $label = calculatorLayoutTextOrHtml($config, 'label', 'label_html');
        if ($label !== '') {
            $labelTag = isset($config['label_tag']) ? $config['label_tag'] : 'div';
            $labelTag = preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', (string)$labelTag) ? $labelTag : 'div';
            $labelAttrs = isset($config['label_attrs']) && is_array($config['label_attrs']) ? $config['label_attrs'] : array();
            if (isset($config['label_id'])) $labelAttrs['id'] = $config['label_id'];
            if (!isset($labelAttrs['class'])) $labelAttrs['class'] = isset($config['label_class']) ? $config['label_class'] : 'label';
            echo '<' . $labelTag . calculatorLayoutAttributes($labelAttrs) . '>' . $label . '</' . $labelTag . '>';
        }
        echo '</div>';
    }
}

if (!function_exists('calculatorResultRow')) {
    function calculatorResultRow($config = array()) {
        $config = is_array($config) ? $config : array();
        $class = isset($config['class']) ? $config['class'] : 'result-row';
        $id = isset($config['id']) ? $config['id'] : null;
        $attrs = isset($config['attrs']) && is_array($config['attrs']) ? $config['attrs'] : array();
        calculatorLayoutOpenTag('div', $class, $id, $attrs);

        $labelAttrs = isset($config['label_attrs']) && is_array($config['label_attrs']) ? $config['label_attrs'] : array();
        if (isset($config['label_id'])) $labelAttrs['id'] = $config['label_id'];
        if (isset($config['label_class'])) $labelAttrs['class'] = $config['label_class'];
        $label = calculatorLayoutTextOrHtml($config, 'label', 'label_html');
        echo '<span' . calculatorLayoutAttributes($labelAttrs) . '>' . $label . '</span>';

        $valueAttrs = isset($config['value_attrs']) && is_array($config['value_attrs']) ? $config['value_attrs'] : array();
        if (isset($config['value_id'])) $valueAttrs['id'] = $config['value_id'];
        if (isset($config['value_class'])) $valueAttrs['class'] = $config['value_class'];
        $value = calculatorLayoutTextOrHtml($config, 'value', 'value_html');
        echo '<strong' . calculatorLayoutAttributes($valueAttrs) . '>' . $value . '</strong>';
        echo '</div>';
    }
}


if (!function_exists('calculatorResultMessage')) {
    function calculatorResultMessage($config = array()) {
        $config = is_array($config) ? $config : array();
        $variant = isset($config['variant']) && $config['variant'] !== '' ? $config['variant'] : 'status';
        $allowed = array('status', 'success', 'warning', 'disclaimer');
        if (!in_array($variant, $allowed, true)) $variant = 'status';

        $class = 'result-message edu-message result-message--' . $variant;
        if ($variant === 'success') $class .= ' edu-message--success';
        elseif ($variant === 'warning') $class .= ' edu-message--warning';
        elseif ($variant === 'disclaimer') $class .= ' edu-message--info';
        else $class .= ' edu-message--status';
        if (isset($config['class']) && $config['class'] !== '') $class .= ' ' . $config['class'];

        $attrs = isset($config['attrs']) && is_array($config['attrs']) ? $config['attrs'] : array();
        $id = isset($config['id']) ? $config['id'] : null;
        $html = calculatorLayoutTextOrHtml($config, 'text', 'html');
        calculatorLayoutOpenTag('div', $class, $id, $attrs);
        echo $html;
        echo '</div>';
    }
}

if (!function_exists('calculatorActions')) {
    function calculatorActions($buttons, $config = array()) {
        $buttons = is_array($buttons) ? $buttons : array();
        $config = is_array($config) ? $config : array();
        $class = isset($config['class']) ? $config['class'] : 'actions';
        $attrs = isset($config['attrs']) ? $config['attrs'] : array();
        echo '<div class="' . calculatorLayoutEscape($class) . '"' . calculatorLayoutAttributes($attrs) . '>';
        foreach ($buttons as $button) {
            if (!is_array($button)) continue;
            $buttonAttrs = isset($button['attrs']) && is_array($button['attrs']) ? $button['attrs'] : array();
            if (isset($button['id'])) $buttonAttrs['id'] = $button['id'];
            if (isset($button['class'])) $buttonAttrs['class'] = $button['class'];
            if (!isset($buttonAttrs['type'])) $buttonAttrs['type'] = 'button';
            $label = isset($button['html']) ? (string)$button['html'] : calculatorLayoutEscape(isset($button['label']) ? $button['label'] : '');
            echo '<button' . calculatorLayoutAttributes($buttonAttrs) . '>' . $label . '</button>';
        }
        echo '</div>';
    }
}
