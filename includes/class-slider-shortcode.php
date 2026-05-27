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

    /**
     * Derive a one-word outcome label from translator stats so the render log
     * answers "did translation work?" at a glance.
     *
     *   OK              — every translation call was satisfied (cache or API)
     *   BREAKER_OPEN    — circuit breaker blocked the API; review content shown in English
     *   API_FAILED      — API was called and returned errors
     *   CACHE_ONLY      — all served from cache, no API hit this render (typical steady state)
     *   NO_OP           — translator wasn't exercised (e.g., empty feed)
     */
    private function summarize_translation_outcome($stats) {
        $calls    = isset($stats['translation_calls']) ? (int) $stats['translation_calls'] : 0;
        $skipped  = isset($stats['api_skipped'])       ? (int) $stats['api_skipped']       : 0;
        $errors   = isset($stats['api_errors'])        ? (int) $stats['api_errors']        : 0;
        $api      = isset($stats['api_calls'])         ? (int) $stats['api_calls']         : 0;
        $cache    = isset($stats['cache_full_hits'])   ? (int) $stats['cache_full_hits']   : 0;

        if ($calls === 0) {
            return 'NO_OP';
        }
        if ($skipped > 0) {
            return 'BREAKER_OPEN';
        }
        if ($errors > 0) {
            return 'API_FAILED';
        }
        if ($api === 0 && $cache > 0) {
            return 'CACHE_ONLY';
        }
        return 'OK';
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
            $stats = $opio_translator->get_stats();
            $post_stats = array(
                'translation_stats' => $stats,
                'render_phase'      => 'post',
            );
            echo '<script type="text/javascript" id="opio-slider-debug-post">console.log("[OPIO slider stats]", ' . wp_json_encode($post_stats) . ');</script>';

            // One always-on server log per slider render that touched the
            // translation pipeline. Shows config used + outcome counters so
            // production can diagnose "is it working, and if not why" without
            // toggling a debug flag. English renders skip this (nothing to
            // translate, nothing to log).
            if (!empty($opio_target_lang)) {
                $key_present = !empty($stats['azure_key_present']);
                $provider    = isset($stats['provider_resolved']) ? $stats['provider_resolved'] : 'mymemory';
                $outcome     = $this->summarize_translation_outcome($stats);
                error_log(sprintf(
                    '[OPIO slider] render lang=%s provider=%s key=%s region=%s breaker=%s outcome=%s calls=%d cache_hits=%d api_calls=%d success=%d errors=%d skipped=%d last_http=%s last_error=%s',
                    $opio_target_lang,
                    $provider,
                    $key_present ? ('set:' . (isset($stats['azure_key_last4']) ? $stats['azure_key_last4'] : '????')) : 'unset',
                    !empty($stats['azure_region']) ? $stats['azure_region'] : 'unset',
                    !empty($stats['circuit_breaker']) ? $stats['circuit_breaker'] : 'clear',
                    $outcome,
                    isset($stats['translation_calls']) ? (int) $stats['translation_calls'] : 0,
                    isset($stats['cache_full_hits'])   ? (int) $stats['cache_full_hits']   : 0,
                    isset($stats['api_calls'])         ? (int) $stats['api_calls']         : 0,
                    isset($stats['api_success'])       ? (int) $stats['api_success']       : 0,
                    isset($stats['api_errors'])        ? (int) $stats['api_errors']        : 0,
                    isset($stats['api_skipped'])       ? (int) $stats['api_skipped']       : 0,
                    isset($stats['last_http_code']) && $stats['last_http_code'] !== null ? (string) $stats['last_http_code'] : 'null',
                    isset($stats['last_error']) && $stats['last_error'] !== null ? '"' . str_replace(array("\r", "\n", '"'), array(' ', ' ', "'"), substr((string) $stats['last_error'], 0, 200)) . '"' : 'null'
                ));
            }
        }

        $output = ob_get_clean(); // Capture the entire output of the function

        if ($did_load_mo) {
            unload_textdomain('widget-for-opio-reviews');
        }

        // === OPIO DEBUG (TEMP) — remove after diagnosis ===
        $raw_output      = $output;
        $prepared_output = $this->slider_deserializer->prepareString($output);
        $kses_output     = wp_kses($prepared_output, $this->slider_deserializer->get_allowed_tags());

        $count_tags = function($html) {
            preg_match_all('/<([a-zA-Z0-9]+)\b/', $html, $m);
            $counts = array_count_values(array_map('strtolower', $m[1]));
            ksort($counts);
            return $counts;
        };

        $debug = array(
            'raw_length'      => strlen($raw_output),
            'prepared_length' => strlen($prepared_output),
            'kses_length'     => strlen($kses_output),
            'raw_tags'        => $count_tags($raw_output),
            'prepared_tags'   => $count_tags($prepared_output),
            'kses_tags'       => $count_tags($kses_output),
            'raw_b64'         => base64_encode($raw_output),
            'prepared_b64'    => base64_encode($prepared_output),
            'kses_b64'        => base64_encode($kses_output),
        );

        $debug_script = '<script type="text/javascript" id="opio-kses-debug">'
            . 'window.opioDebug = ' . wp_json_encode($debug) . ';'
            . 'console.group("[OPIO kses debug]");'
            . 'console.log("lengths", {raw: window.opioDebug.raw_length, prepared: window.opioDebug.prepared_length, kses: window.opioDebug.kses_length});'
            . 'console.log("tag counts RAW", window.opioDebug.raw_tags);'
            . 'console.log("tag counts PREPARED", window.opioDebug.prepared_tags);'
            . 'console.log("tag counts POST-KSES", window.opioDebug.kses_tags);'
            . 'console.log("--- RAW HTML ---");'
            . 'console.log(atob(window.opioDebug.raw_b64));'
            . 'console.log("--- PREPARED HTML (after prepareString) ---");'
            . 'console.log(atob(window.opioDebug.prepared_b64));'
            . 'console.log("--- POST-KSES HTML ---");'
            . 'console.log(atob(window.opioDebug.kses_b64));'
            . 'console.groupEnd();'
            . '</script>';

        return $debug_script . $kses_output;
        // === END OPIO DEBUG ===


    }
}