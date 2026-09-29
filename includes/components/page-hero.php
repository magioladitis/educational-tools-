<?php
/**
 * Canonical page/tool hero component.
 *
 * Presentation only. Business rules and page-specific calculations do not belong here.
 * The shared mobile UX uses the data-edu-hero/data-edu-hero-info contract so every
 * tool gets the same accessible information disclosure on small screens.
 */

if (!function_exists('eduPageHeroEscape')) {
    function eduPageHeroEscape($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('eduPageHeroAttributes')) {
    function eduPageHeroAttributes($attrs) {
        if (!is_array($attrs) || count($attrs) === 0) return '';
        $out = '';
        foreach ($attrs as $name => $value) {
            if (!preg_match('/^[a-zA-Z_:][-a-zA-Z0-9_:.]*$/', (string)$name)) continue;
            if ($value === null || $value === false) continue;
            if ($value === true) $out .= ' ' . eduPageHeroEscape($name);
            else $out .= ' ' . eduPageHeroEscape($name) . '="' . eduPageHeroEscape($value) . '"';
        }
        return $out;
    }
}

if (!function_exists('eduPageHeroTextOrHtml')) {
    function eduPageHeroTextOrHtml($config, $textKey, $htmlKey) {
        if (isset($config[$htmlKey]) && $config[$htmlKey] !== '') return (string)$config[$htmlKey];
        if (isset($config[$textKey])) return eduPageHeroEscape($config[$textKey]);
        return '';
    }
}

if (!function_exists('eduPageHeroStart')) {
    function eduPageHeroStart($config = array()) {
        $config = is_array($config) ? $config : array();
        $class = isset($config['class']) && $config['class'] !== '' ? (string)$config['class'] : 'hero';
        $attrs = isset($config['attrs']) && is_array($config['attrs']) ? $config['attrs'] : array();
        $attrs['class'] = $class;
        $attrs['data-edu-hero'] = 'true';
        if (isset($config['id']) && $config['id'] !== '') $attrs['id'] = $config['id'];
        echo '<section' . eduPageHeroAttributes($attrs) . '>';
    }
}

if (!function_exists('eduPageHeroEnd')) {
    function eduPageHeroEnd() {
        echo '</section>';
    }
}

if (!function_exists('eduPageHero')) {
    function eduPageHero($config = array()) {
        $config = is_array($config) ? $config : array();
        eduPageHeroStart($config);

        $kicker = eduPageHeroTextOrHtml($config, 'kicker', 'kicker_html');
        if ($kicker !== '') {
            $kickerClass = isset($config['kicker_class']) ? (string)$config['kicker_class'] : 'hero-kicker';
            echo '<span class="' . eduPageHeroEscape($kickerClass) . '" data-edu-hero-info="true">' . $kicker . '</span>';
        }

        $title = eduPageHeroTextOrHtml($config, 'title', 'title_html');
        if ($title !== '') echo '<h1>' . $title . '</h1>';

        $intro = eduPageHeroTextOrHtml($config, 'intro', 'intro_html');
        if ($intro !== '') {
            $introAttrs = isset($config['intro_attrs']) && is_array($config['intro_attrs']) ? $config['intro_attrs'] : array();
            $introAttrs['data-edu-hero-info'] = 'true';
            echo '<p' . eduPageHeroAttributes($introAttrs) . '>' . $intro . '</p>';
        }

        $meta = isset($config['meta']) && is_array($config['meta']) ? $config['meta'] : array();
        if (count($meta) > 0) {
            $metaClass = isset($config['meta_class']) ? (string)$config['meta_class'] : 'meta';
            $metaAttrs = isset($config['meta_attrs']) && is_array($config['meta_attrs']) ? $config['meta_attrs'] : array();
            $metaAttrs['class'] = $metaClass;
            $metaAttrs['data-edu-hero-info'] = 'true';
            echo '<div' . eduPageHeroAttributes($metaAttrs) . '>';
            foreach ($meta as $item) {
                if (is_array($item)) {
                    $itemAttrs = isset($item['attrs']) && is_array($item['attrs']) ? $item['attrs'] : array();
                    $itemHtml = isset($item['html']) ? (string)$item['html'] : eduPageHeroEscape(isset($item['text']) ? $item['text'] : '');
                    echo '<span' . eduPageHeroAttributes($itemAttrs) . '>' . $itemHtml . '</span>';
                } else {
                    echo '<span>' . eduPageHeroEscape($item) . '</span>';
                }
            }
            echo '</div>';
        }

        if (isset($config['actions_html']) && $config['actions_html'] !== '') {
            $actionsClass = isset($config['actions_class']) ? (string)$config['actions_class'] : 'hero-actions';
            echo '<div class="' . eduPageHeroEscape($actionsClass) . '">' . (string)$config['actions_html'] . '</div>';
        }

        eduPageHeroEnd();
    }
}
