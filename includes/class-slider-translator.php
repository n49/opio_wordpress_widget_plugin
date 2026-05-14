<?php

namespace WP_Opio_Reviews\Includes;

class Slider_Translator {

    const TRANSIENT_PREFIX = 'opio_tr2_';
    const TRANSIENT_TTL    = 2592000; // 30 days
    const REQUEST_TIMEOUT  = 5;

    private $stats = array(
        'translation_calls'   => 0,
        'cache_full_hits'     => 0,
        'chunks_total'        => 0,
        'chunk_cache_hits'    => 0,
        'api_calls'           => 0,
        'api_success'         => 0,
        'api_errors'          => 0,
        'last_error'          => null,
        'last_http_code'      => null,
        'last_endpoint_host'  => null,
        'schema_fetched'      => false,
        'schema_fetch_success'=> false,
        'schema_translated'   => false,
    );

    public function get_stats() {
        return $this->stats;
    }

    private function record_error($message) {
        $this->stats['api_errors']++;
        $this->stats['last_error'] = is_string($message) ? substr($message, 0, 200) : 'unknown_error';
    }

    private static $locale_map = array(
        'en' => 'en_US',
        'fr' => 'fr_CA',
        'es' => 'es_ES',
        'pt' => 'pt_PT',
        'ar' => 'ar',
        'zh' => 'zh_CN',
        'hk' => 'zh_HK',
        'fa' => 'fa_IR',
        'pa' => 'pa_IN',
        'ru' => 'ru_RU',
        'tl' => 'tl_PH',
        'ta' => 'ta_IN',
        'ur' => 'ur_PK',
        'de' => 'de_DE',
        'ja' => 'ja',
        'ko' => 'ko_KR',
        'it' => 'it_IT',
        'nl' => 'nl_NL',
        'tr' => 'tr_TR',
        'pl' => 'pl_PL',
        'vi' => 'vi',
        'id' => 'id_ID',
        'ms' => 'ms_MY',
        'th' => 'th',
        'hi' => 'hi_IN',
        'bn' => 'bn_BD',
        'el' => 'el',
        'sv' => 'sv_SE',
        'he' => 'he_IL',
        'uk' => 'uk',
    );

    private static $translator_codes = array(
        'en_US' => 'en',
        'fr_CA' => 'fr',
        'es_ES' => 'es',
        'pt_PT' => 'pt',
        'ar'    => 'ar',
        'zh_CN' => 'zh',
        'zh_HK' => 'zh-TW',
        'fa_IR' => 'fa',
        'pa_IN' => 'pa',
        'ru_RU' => 'ru',
        'tl_PH' => 'tl',
        'ta_IN' => 'ta',
        'ur_PK' => 'ur',
        'de_DE' => 'de',
        'ja'    => 'ja',
        'ko_KR' => 'ko',
        'it_IT' => 'it',
        'nl_NL' => 'nl',
        'tr_TR' => 'tr',
        'pl_PL' => 'pl',
        'vi'    => 'vi',
        'id_ID' => 'id',
        'ms_MY' => 'ms',
        'th'    => 'th',
        'hi_IN' => 'hi',
        'bn_BD' => 'bn',
        'el'    => 'el',
        'sv_SE' => 'sv',
        'he_IL' => 'he',
        'uk'    => 'uk',
    );

    public function normalize_lang($lang) {
        if (empty($lang) || !is_string($lang)) {
            return null;
        }
        $lang = str_replace('-', '_', trim($lang));
        if ($lang === '') {
            return null;
        }
        // English in any form → no translation needed
        if (preg_match('/^en(_[A-Za-z]{2})?$/i', $lang)) {
            return null;
        }
        // Explicit short-code map (e.g. 'fr' → 'fr_CA')
        $short = strtolower($lang);
        if (isset(self::$locale_map[$short])) {
            return self::$locale_map[$short];
        }
        // Already a known full locale
        if (in_array($lang, self::$locale_map, true)) {
            return $lang;
        }
        // Permissive fallback: looks like a plausible locale (xx, xxx, xx_YY)
        if (preg_match('/^[a-z]{2,3}(_[A-Z]{2})?$/', $lang)) {
            return $lang;
        }
        // Single short code with mixed/upper case (e.g. 'DE') → normalise to lowercase
        if (preg_match('/^[A-Za-z]{2,3}$/', $lang)) {
            return strtolower($lang);
        }
        return null;
    }

    public function translator_lang_code($locale) {
        if (empty($locale)) {
            return '';
        }
        if (isset(self::$translator_codes[$locale])) {
            $code = self::$translator_codes[$locale];
            return $code === 'en' ? '' : $code;
        }
        // Permissive fallback: take ISO 639-1 prefix (strip _XX suffix)
        $code = strtolower(strtok($locale, '_'));
        return $code === 'en' ? '' : $code;
    }

    const CHUNK_MAX_LEN = 450;

    public function translate_review_content($text, $target_lang_code) {
        if (!is_string($text) || $text === '' || empty($target_lang_code)) {
            return $text;
        }

        $this->stats['translation_calls']++;

        $cache_key = self::TRANSIENT_PREFIX . md5($text . '|' . $target_lang_code);
        $cached    = get_transient($cache_key);
        if ($cached !== false) {
            $this->stats['cache_full_hits']++;
            return $cached;
        }

        $chunks = $this->chunk_text($text, self::CHUNK_MAX_LEN);
        $this->stats['chunks_total'] += count($chunks);
        $translated_chunks = array();
        foreach ($chunks as $chunk) {
            $translated = $this->translate_chunk($chunk, $target_lang_code);
            if ($translated === false) {
                return $text;
            }
            $translated_chunks[] = $translated;
        }

        $result = implode(' ', $translated_chunks);
        set_transient($cache_key, $result, self::TRANSIENT_TTL);
        return $result;
    }

    private function translate_chunk($text, $target_lang_code) {
        $chunk_key = self::TRANSIENT_PREFIX . 'c_' . md5($text . '|' . $target_lang_code);
        $cached    = get_transient($chunk_key);
        if ($cached !== false) {
            $this->stats['chunk_cache_hits']++;
            return $cached;
        }

        $endpoint = apply_filters('opio_translation_endpoint', 'https://api.mymemory.translated.net/get');
        $email    = apply_filters('opio_translation_email', '');
        $this->stats['last_endpoint_host'] = parse_url($endpoint, PHP_URL_HOST);

        $query = array(
            'q'        => $text,
            'langpair' => 'en|' . $target_lang_code,
        );
        if (!empty($email)) {
            $query['de'] = $email;
        }

        $this->stats['api_calls']++;

        $response = wp_remote_get(add_query_arg($query, $endpoint), array(
            'timeout' => self::REQUEST_TIMEOUT,
        ));

        if (is_wp_error($response)) {
            $err = 'wp_error: ' . $response->get_error_message();
            $this->record_error($err);
            error_log('[OPIO slider] translation request error (lang=' . $target_lang_code . ', host=' . $this->stats['last_endpoint_host'] . '): ' . $response->get_error_message());
            return false;
        }

        $http_code = wp_remote_retrieve_response_code($response);
        $this->stats['last_http_code'] = $http_code;

        if ($http_code !== 200) {
            $this->record_error('http_' . $http_code);
            error_log('[OPIO slider] translation HTTP ' . $http_code . ' (lang=' . $target_lang_code . ', host=' . $this->stats['last_endpoint_host'] . ')');
            return false;
        }

        $body    = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        if (!is_array($decoded) || empty($decoded['responseData']['translatedText']) || !is_string($decoded['responseData']['translatedText'])) {
            $this->record_error('unexpected_response: ' . substr($body, 0, 100));
            error_log('[OPIO slider] translation response unexpected (lang=' . $target_lang_code . '): ' . substr($body, 0, 200));
            return false;
        }

        $translated = $decoded['responseData']['translatedText'];
        if (stripos($translated, 'QUERY LENGTH LIMIT EXCEEDED') !== false
            || stripos($translated, 'MYMEMORY WARNING') !== false
            || stripos($translated, 'INVALID LANGUAGE PAIR') !== false) {
            $this->record_error('api_warning: ' . substr($translated, 0, 100));
            error_log('[OPIO slider] translation API warning (lang=' . $target_lang_code . '): ' . $translated);
            return false;
        }

        $this->stats['api_success']++;
        set_transient($chunk_key, $translated, self::TRANSIENT_TTL);
        return $translated;
    }

    private function chunk_text($text, $max_len) {
        if (strlen($text) <= $max_len) {
            return array($text);
        }

        $parts = preg_split('/([.!?]+\s+|\n+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $chunks  = array();
        $current = '';
        foreach ($parts as $part) {
            if (strlen($current . $part) <= $max_len) {
                $current .= $part;
                continue;
            }
            if ($current !== '') {
                $chunks[] = $current;
                $current  = '';
            }
            if (strlen($part) <= $max_len) {
                $current = $part;
            } else {
                foreach ($this->split_by_words($part, $max_len) as $sub) {
                    $chunks[] = $sub;
                }
            }
        }
        if ($current !== '') {
            $chunks[] = $current;
        }
        return $chunks;
    }

    private function split_by_words($text, $max_len) {
        $words  = preg_split('/\s+/u', $text);
        $chunks = array();
        $current = '';
        foreach ($words as $word) {
            $candidate = ($current === '') ? $word : $current . ' ' . $word;
            if (strlen($candidate) <= $max_len) {
                $current = $candidate;
            } else {
                if ($current !== '') {
                    $chunks[] = $current;
                }
                $current = $word;
            }
        }
        if ($current !== '') {
            $chunks[] = $current;
        }
        return $chunks;
    }

    public static function maybe_translate($text, $translator, $target_lang_code) {
        if (empty($target_lang_code) || !$translator instanceof self) {
            return $text;
        }
        return $translator->translate_review_content($text, $target_lang_code);
    }

    public function fetch_and_translate_schema($feed_object, $review_type, $target_lang_code) {
        if (!isset($feed_object->schema_enabled) || $feed_object->schema_enabled !== 'yes') {
            return null;
        }

        if ($review_type === 'orgfeed') {
            if (empty($feed_object->org_id)) {
                return null;
            }
            $schema_url = 'https://op.io/review-schema.json/?orgid=' . urlencode($feed_object->org_id);
        } else {
            if (empty($feed_object->biz_id)) {
                return null;
            }
            $schema_url = 'https://op.io/review-schema.json/?entid=' . urlencode($feed_object->biz_id);
        }

        if (isset($feed_object->schema_type) && $feed_object->schema_type === 'local') {
            $schema_url .= '&type=local';
        }

        $this->stats['schema_fetched'] = true;
        $response = wp_remote_get($schema_url, array('timeout' => self::REQUEST_TIMEOUT));
        if (is_wp_error($response)) {
            error_log('[OPIO slider] schema fetch error: ' . $response->get_error_message());
            return null;
        }
        $http_code = wp_remote_retrieve_response_code($response);
        if ($http_code !== 200) {
            error_log('[OPIO slider] schema fetch HTTP ' . $http_code);
            return null;
        }

        $schema_json = wp_remote_retrieve_body($response);
        if (empty($schema_json) || $schema_json === '{}' || $schema_json === 'null') {
            return null;
        }
        $this->stats['schema_fetch_success'] = true;

        if (!empty($target_lang_code)) {
            $schema_json = $this->translate_schema_json($schema_json, $target_lang_code);
            $this->stats['schema_translated'] = true;
        }

        return $schema_json;
    }

    public function translate_schema_json($json, $target_lang_code) {
        if (empty($json) || empty($target_lang_code)) {
            return $json;
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return $json;
        }
        $this->walk_translate_schema($data, $target_lang_code);
        // Keep slashes escaped so any "</script>" appearing in review content (or
        // any other field) becomes "<\/script>" — preventing a stray closing tag
        // from breaking out of the surrounding <script type="application/ld+json">.
        $encoded = wp_json_encode($data, JSON_UNESCAPED_UNICODE);
        return $encoded === false ? $json : $encoded;
    }

    private function walk_translate_schema(&$node, $lang_code) {
        if (!is_array($node)) {
            return;
        }

        if (isset($node['@type'])) {
            $types = is_array($node['@type']) ? $node['@type'] : array($node['@type']);
            if (in_array('Review', $types, true)) {
                $node['inLanguage'] = $lang_code;
                if (isset($node['reviewBody']) && is_string($node['reviewBody']) && $node['reviewBody'] !== '') {
                    $node['reviewBody'] = $this->translate_review_content($node['reviewBody'], $lang_code);
                }
                if (isset($node['description']) && is_string($node['description']) && $node['description'] !== '') {
                    $node['description'] = $this->translate_review_content($node['description'], $lang_code);
                }
            }
        }

        foreach ($node as $key => &$value) {
            if (is_array($value)) {
                $this->walk_translate_schema($value, $lang_code);
            }
        }
    }

    public function get_js_translations($locale) {
        $json_file = plugin_dir_path(OPIO_PLUGIN_FILE) . 'languages/widget-for-opio-reviews-' . $locale . '-opio-slider-main-js.json';
        if (!file_exists($json_file)) {
            return array();
        }
        $raw = file_get_contents($json_file);
        if ($raw === false) {
            return array();
        }
        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['locale_data']['messages']) || !is_array($data['locale_data']['messages'])) {
            return array();
        }
        $messages = $data['locale_data']['messages'];
        unset($messages['']);
        $result = array();
        foreach ($messages as $key => $val) {
            if (is_array($val) && isset($val[0]) && is_string($val[0])) {
                $result[$key] = $val[0];
            }
        }
        return $result;
    }
}
