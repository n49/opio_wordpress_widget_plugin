<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Core\Core;

class Slider_Shortcode {

    private $slider_translator;

    public function __construct(Slider_Deserializer $slider_deserializer, Slider_Translator $slider_translator) {
        $this->slider_deserializer = $slider_deserializer;
        $this->slider_translator   = $slider_translator;
    }

    function custom_esc($str) {
        return ($str);
    }

    public function register() {
        add_shortcode('opio_slider', array($this, 'init'));
    }

    public function init($atts) {

        if (get_option('opio_active') === '0') {
            return '';
        }
        $feed = $this->slider_deserializer->get_feed($atts['id']);
        $feed_id = '';
        $loaded_bizs = '';
        $business_name = '';
        $biz_id = '';
        $org_id = '';
        $review_feed_link = '';
        $feed_inited = false;
        $businesses = null;
        $reviews = null;
        $feed_object = json_decode($feed->post_content);

        if ($feed != null) {
            $slider_type = $feed_object->slider_type;
            $review_feed_link = $feed_object->review_feed_link;
        }

        $slider_type = $feed_object->slider_type;
        $review_feed_link = $feed_object->review_feed_link;

        $lang_attr        = isset($atts['lang']) ? $atts['lang'] : '';
        $target_locale    = $this->slider_translator->normalize_lang($lang_attr);
        $opio_translator  = $this->slider_translator;
        $opio_target_lang = $target_locale ? $this->slider_translator->translator_lang_code($target_locale) : '';
        $did_load_mo      = false;
        $mo_file          = '';
        $js_translations  = array();
        if ($target_locale) {
            $mo_file = plugin_dir_path(OPIO_PLUGIN_FILE) . 'languages/widget-for-opio-reviews-' . $target_locale . '.mo';
            if (file_exists($mo_file)) {
                unload_textdomain('widget-for-opio-reviews');
                $did_load_mo = load_textdomain('widget-for-opio-reviews', $mo_file);
            }
            $js_translations = $this->slider_translator->get_js_translations($target_locale);
        }

		ob_start();

        $debug = array(
            'plugin_version'           => defined('OPIO_PLUGIN_VERSION') ? OPIO_PLUGIN_VERSION : '',
            'lang_attr'                => (string) $lang_attr,
            'target_locale'            => (string) $target_locale,
            'opio_target_lang'         => (string) $opio_target_lang,
            'mo_file'                  => (string) $mo_file,
            'mo_file_exists'           => $mo_file ? file_exists($mo_file) : false,
            'did_load_mo'              => (bool) $did_load_mo,
            'js_translations_count'    => count($js_translations),
            'plugin_textdomain_loaded' => is_textdomain_loaded('widget-for-opio-reviews'),
            'read_more_translated'     => __('Read more', 'widget-for-opio-reviews'),
            'translation_stats'        => $opio_translator ? $opio_translator->get_stats() : null,
        );
        echo '<script type="text/javascript" id="opio-slider-debug">console.log("[OPIO slider]", ' . wp_json_encode($debug) . ');</script>';

        if (!empty($js_translations)) {
            echo '<script type="text/javascript" id="opio-slider-i18n">window.opioSliderI18nActive = ' . wp_json_encode($js_translations) . ';</script>';
        }

        // Compact CSS for the rating widget area. Translated strings (especially
        // "Powered by" → "Con tecnología de" / "Bereitgestellt von" /
        // "Pinapatakbo ng") are much longer than English and wrap across the
        // narrow widget column. Shrink the font and force nowrap on the worst
        // offenders so longer translations stay inline.
        if (!empty($opio_target_lang)) {
            echo '<style id="opio-slider-compact-css">'
                . '#opio-horizontal-widget .rating-widget-part,'
                . '#opio-carousel-widget .c-rating-widget-container,'
                . '#opio-vertical-widget .v-rating-widget-container {'
                . ' font-size: 12px;'
                . '}'
                . '#opio-horizontal-widget .w-pwd-text,'
                . '#opio-carousel-widget .c-pwd-span,'
                . '#opio-vertical-widget .v-pwd-span {'
                . ' font-size: 10px;'
                . ' white-space: nowrap;'
                . '}'
                . '#opio-horizontal-widget .see-all-text a,'
                . '#opio-carousel-widget .c-see-all-div a,'
                . '#opio-vertical-widget .v-see-all-span a {'
                . ' font-size: 11px;'
                . ' white-space: nowrap;'
                . '}'
                . '#opio-horizontal-widget .write-review-text span,'
                . '#opio-carousel-widget .c-write-rev-inner-div span,'
                . '#opio-vertical-widget .v-write-rev-div span {'
                . ' font-size: 12px;'
                . ' white-space: nowrap;'
                . '}'
                . '</style>';
        }

        if($slider_type == 'horizontal') {
            include_once 'reviews-slider-horizontal-template.php';
        } else if($slider_type == 'horizontal-carousel') {
            include_once 'reviews-slider-horizontal-carousel-template.php';
        } else if($slider_type == 'vertical') {
            include_once 'reviews-slider-vertical-template.php';
        }
        ob_get_contents();

        // Post-render stats — counters were populated during template execution above.
        if ($opio_translator) {
            $post_stats = array(
                'translation_stats' => $opio_translator->get_stats(),
                'render_phase'      => 'post',
            );
            echo '<script type="text/javascript" id="opio-slider-debug-post">console.log("[OPIO slider stats]", ' . wp_json_encode($post_stats) . ');</script>';
        }

        $output = ob_get_clean(); // Capture the entire output of the function

        if ($did_load_mo) {
            unload_textdomain('widget-for-opio-reviews');
        }

        $output = $this->slider_deserializer->prepareString($output);

        return wp_kses($output, $this->slider_deserializer->get_allowed_tags());


    }
}