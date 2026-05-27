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

        // === OPIO DEBUG (TEMP) — track per-request invocations of this shortcode
        static $invocation_count = 0;
        $invocation_count++;
        $debug_call = array(
            't_start_ms'           => round(microtime(true) * 1000, 2),
            'invocation'           => $invocation_count,
            'pid'                  => function_exists('getmypid') ? getmypid() : null,
            'ob_level_at_entry'    => ob_get_level(),
            'mem_mb'               => round(memory_get_usage(true) / 1048576, 2),
            'mem_peak_mb'          => round(memory_get_peak_usage(true) / 1048576, 2),
            'atts'                 => $atts,
            // who invoked us — short backtrace identifies Yoast vs theme vs other
            'backtrace'            => array_map(function($f){
                return (isset($f['class']) ? $f['class'] . $f['type'] : '')
                     . (isset($f['function']) ? $f['function'] : '')
                     . (isset($f['file']) ? ' @ ' . basename($f['file']) . ':' . (isset($f['line']) ? $f['line'] : '') : '');
            }, array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 12)),
            'helper_fns_already_defined' => array(
                'getStarRating'       => function_exists('getStarRating'),
                'getStarRatingWidget' => function_exists('getStarRatingWidget'),
                'randomColor'         => function_exists('randomColor'),
                'isMobileDevice'      => function_exists('isMobileDevice'),
            ),
            'current_filter'       => current_filter(),
            'doing_filter_the_content' => doing_filter('the_content'),
            'is_admin'             => is_admin(),
        );
        // === END OPIO DEBUG

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

        // include (not include_once): shortcode can be invoked multiple times
        // per request (e.g. Yoast running the_content for meta generation +
        // the actual page render). include_once would no-op on subsequent
        // calls, leaving the slider markup empty. Helper functions inside
        // each template are guarded with function_exists() so repeat-include
        // does not fatal.
        // === OPIO DEBUG (TEMP) — capture template execution
        $debug_template = array(
            'slider_type'        => (string) $slider_type,
            'feed_id_attr'       => isset($atts['id']) ? $atts['id'] : null,
            'feed_loaded'        => $feed != null,
            'feed_post_status'   => $feed ? $feed->post_status : null,
            'feed_object_keys'   => $feed_object ? array_keys((array) $feed_object) : null,
            'ob_level_before_include' => ob_get_level(),
            'buffer_len_before_include' => function_exists('ob_get_length') && ob_get_length() !== false ? ob_get_length() : null,
        );
        $marker_before_include = "\n<!-- OPIO_DEBUG_MARKER_BEFORE_INCLUDE -->\n";
        echo $marker_before_include;
        // === END OPIO DEBUG

        if($slider_type == 'horizontal') {
            include 'reviews-slider-horizontal-template.php';
        } else if($slider_type == 'horizontal-carousel') {
            include 'reviews-slider-horizontal-carousel-template.php';
        } else if($slider_type == 'vertical') {
            include 'reviews-slider-vertical-template.php';
        }
        ob_get_contents();

        // === OPIO DEBUG (TEMP) — capture state after template ran
        $marker_after_include = "\n<!-- OPIO_DEBUG_MARKER_AFTER_INCLUDE -->\n";
        echo $marker_after_include;
        $debug_template['ob_level_after_include']  = ob_get_level();
        $debug_template['buffer_len_after_include'] = function_exists('ob_get_length') && ob_get_length() !== false ? ob_get_length() : null;
        $debug_template['helper_fns_defined_after_include'] = array(
            'getStarRating'       => function_exists('getStarRating'),
            'getStarRatingWidget' => function_exists('getStarRatingWidget'),
            'randomColor'         => function_exists('randomColor'),
            'isMobileDevice'      => function_exists('isMobileDevice'),
        );
        // === END OPIO DEBUG

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

        // Was the marker actually rendered by the buffer? Detects whether
        // template output ended up in our parent buffer or somewhere else.
        $debug_template['contains_marker_before'] = strpos($raw_output, 'OPIO_DEBUG_MARKER_BEFORE_INCLUDE') !== false;
        $debug_template['contains_marker_after']  = strpos($raw_output, 'OPIO_DEBUG_MARKER_AFTER_INCLUDE') !== false;
        $debug_template['chars_between_markers']  = $debug_template['contains_marker_before'] && $debug_template['contains_marker_after']
            ? strpos($raw_output, 'OPIO_DEBUG_MARKER_AFTER_INCLUDE') - strpos($raw_output, 'OPIO_DEBUG_MARKER_BEFORE_INCLUDE') - strlen('<!-- OPIO_DEBUG_MARKER_BEFORE_INCLUDE -->')
            : null;
        $debug_call['t_end_ms']     = round(microtime(true) * 1000, 2);
        $debug_call['elapsed_ms']   = round($debug_call['t_end_ms'] - $debug_call['t_start_ms'], 2);
        $debug_call['ob_level_at_exit'] = ob_get_level();

        $debug = array(
            'call'            => $debug_call,
            'template'        => $debug_template,
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

        // Use a unique window var per invocation so a 2nd shortcode call
        // doesn't clobber the 1st in the browser.
        $invo_suffix = '_' . $invocation_count;
        $debug_script = '<script type="text/javascript" id="opio-kses-debug' . esc_attr($invo_suffix) . '">'
            . 'window.opioDebug' . esc_attr($invo_suffix) . ' = ' . wp_json_encode($debug) . ';'
            . '(function(d){'
            .     'console.group("[OPIO debug] invocation #" + d.call.invocation + " (" + d.call.current_filter + ")");'
            .     'console.log("call context", d.call);'
            .     'console.log("template context", d.template);'
            .     'console.log("lengths", {raw: d.raw_length, prepared: d.prepared_length, kses: d.kses_length});'
            .     'console.log("tag counts RAW", d.raw_tags);'
            .     'console.log("tag counts PREPARED", d.prepared_tags);'
            .     'console.log("tag counts POST-KSES", d.kses_tags);'
            .     'console.log("backtrace", d.call.backtrace);'
            .     'console.log("--- RAW HTML ---");'
            .     'console.log(atob(d.raw_b64));'
            .     'console.log("--- PREPARED HTML ---");'
            .     'console.log(atob(d.prepared_b64));'
            .     'console.log("--- POST-KSES HTML ---");'
            .     'console.log(atob(d.kses_b64));'
            .     'console.groupEnd();'
            . '})(window.opioDebug' . esc_attr($invo_suffix) . ');'
            . '</script>';

        // Also emit to PHP error log so the call sequence is visible
        // server-side (useful when JS is mid-page and console output gets
        // tangled with cache layers).
        error_log(sprintf(
            '[OPIO debug] invocation=%d current_filter=%s raw_len=%d kses_len=%d marker_before=%s marker_after=%s feed_loaded=%s slider_type=%s elapsed_ms=%s',
            $debug_call['invocation'],
            $debug_call['current_filter'] ?: '(none)',
            strlen($raw_output),
            strlen($kses_output),
            $debug_template['contains_marker_before'] ? 'yes' : 'NO',
            $debug_template['contains_marker_after']  ? 'yes' : 'NO',
            $debug_template['feed_loaded'] ? 'yes' : 'no',
            $debug_template['slider_type'],
            $debug_call['elapsed_ms']
        ));

        return $debug_script . $kses_output;
        // === END OPIO DEBUG ===


    }
}