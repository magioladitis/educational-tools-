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
        if ($intro !== '') echo '<p>' . $intro . '</p>';

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
                if ($title !== '') echo '<h2>' . $title . '</h2>';
                if ($subtitle !== '') echo '<p class="subtitle">' . $subtitle . '</p>';
                echo '</div>';
                if ($cap !== '') echo '<div class="max">' . $cap . '</div>';
                echo '</div>';
            }
        } else {
            if ($title !== '') echo '<h2>' . $title . '</h2>';
            if ($subtitle !== '') {
                $subtitleClass = isset($config['subtitle_class']) ? $config['subtitle_class'] : 'subtitle';
                echo '<p class="' . calculatorLayoutEscape($subtitleClass) . '">' . $subtitle . '</p>';
            }
            if ($cap !== '') {
                $capClass = isset($config['cap_class']) ? $config['cap_class'] : 'cap';
                echo '<p class="' . calculatorLayoutEscape($capClass) . '">' . $cap . '</p>';
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
