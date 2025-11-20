<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Core\Core;

class Feed_Shortcode {

    private static $schema_scripts = array();
    private static $schemas_processed = false;

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
        // Hook early to pre-process shortcodes and extract schemas before wp_head runs
        add_action('template_redirect', array($this, 'pre_process_schemas'), 1);
        // Hook into wp_head to output schemas
        add_action('wp_head', array($this, 'output_schemas_to_head'), 1);
    }

    function custom_esc($str) {
        return ($str);
    }

    public function register() {
        add_shortcode('opio_feed', array($this, 'init'));
    }

    /**
     * Pre-process shortcodes to extract schemas before wp_head runs
     */
    public function pre_process_schemas() {
        if (self::$schemas_processed || is_admin()) {
            return;
        }

        global $wp_query, $post;
        
        $content = '';
        $feed_ids = array();

        // Check current post/page content
        if ($post && isset($post->post_content)) {
            $content .= $post->post_content;
        }

        // Check all posts in the main query (for archive pages, etc.)
        if ($wp_query && isset($wp_query->posts) && is_array($wp_query->posts)) {
            foreach ($wp_query->posts as $query_post) {
                if (isset($query_post->post_content)) {
                    $content .= $query_post->post_content;
                }
            }
        }

        // Extract all opio_feed shortcode IDs from content using regex
        if (preg_match_all('/\[opio_feed[^\]]*id=["\']?(\d+)["\']?[^\]]*\]/i', $content, $matches)) {
            if (!empty($matches[1])) {
                $feed_ids = array_unique(array_map('intval', $matches[1]));
            }
        }

        // Also try parsing shortcode attributes for cases where format might differ
        if (has_shortcode($content, 'opio_feed')) {
            // Use WordPress shortcode parser
            preg_match_all('/\[opio_feed([^\]]*)\]/i', $content, $shortcode_matches);
            if (!empty($shortcode_matches[1])) {
                foreach ($shortcode_matches[1] as $atts_string) {
                    $atts = shortcode_parse_atts($atts_string);
                    if (isset($atts['id'])) {
                        $feed_ids[] = intval($atts['id']);
                    }
                }
                $feed_ids = array_unique($feed_ids);
            }
        }

        // Process all found feed IDs
        foreach ($feed_ids as $feed_id) {
            if ($feed_id > 0) {
                $this->fetch_and_extract_schema($feed_id);
            }
        }

        self::$schemas_processed = true;
    }

    /**
     * Fetch feed and extract schema without outputting content
     */
    private function fetch_and_extract_schema($feed_id) {
        if (get_option('opio_active') === '0') {
            return;
        }

        $feed = $this->feed_deserializer->get_feed($feed_id);
        
        if ($feed == null) {
            return;
        }

        $feed_object = json_decode($feed->post_content);
        
        if (!$feed_object) {
            return;
        }

        $biz_id = isset($feed_object->biz_id) ? $feed_object->biz_id : null;
        $org_id = isset($feed_object->org_id) ? $feed_object->org_id : null;
        $review_type = isset($feed_object->review_type) ? $feed_object->review_type : null;
        $review_option = isset($feed_object->review_option) ? $feed_object->review_option : null;

        if (!isset($biz_id) && !isset($org_id)) {
            return;
        }

        $valid = false;
        if (isset($biz_id)) {
            $valid = preg_match('/^[a-zA-Z0-9]{15,20}$/', $biz_id);
        }
        if (!$valid && isset($org_id)) {
            $valid = preg_match('/^[a-zA-Z0-9]{15,20}$/', $org_id);
        }

        if (!$valid) {
            return;
        }

        $option = ($review_option == "opio") ? "reviewFeed" : "allReviewFeed";
        $opio_handler = new Opio_Handler($biz_id, $option, $review_type, $org_id);
        $reviews = $opio_handler->get_business();

        if ($reviews) {
            // Extract schema scripts from the feed HTML
            $schemas = $this->extract_schema_from_html($reviews);
            if (!empty($schemas)) {
                // Store schemas to be output in head
                self::$schema_scripts = array_merge(self::$schema_scripts, $schemas);
            }
        }
    }

    /**
     * Extract JSON-LD schema scripts from HTML content
     */
    private function extract_schema_from_html($html) {
        $schemas = array();
        
        // Pattern to match script tags with type="application/ld+json" and id="jsonldSchema"
        // Handles attributes in any order and with single or double quotes
        $pattern = '/<script[^>]*(?:id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\']|type=["\']application\/ld\+json["\'][^>]*id=["\']jsonldSchema["\'])[^>]*>(.*?)<\/script>/is';
        
        if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $schemas[] = $match[0]; // Full script tag
            }
        }
        
        return $schemas;
    }

    /**
     * Remove schema scripts from HTML content
     */
    private function remove_schema_from_html($html) {
        // Remove script tags with id="jsonldSchema" and type="application/ld+json"
        // Handles attributes in any order and with single or double quotes
        $pattern = '/<script[^>]*(?:id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\']|type=["\']application\/ld\+json["\'][^>]*id=["\']jsonldSchema["\'])[^>]*>.*?<\/script>/is';
        $html = preg_replace($pattern, '', $html);
        
        // Also remove any stray head tags that might be in the content
        // Remove opening <head> tags
        $html = preg_replace('/<head[^>]*>/i', '', $html);
        // Remove closing </head> tags
        $html = preg_replace('/<\/head>/i', '', $html);
        
        return $html;
    }

    /**
     * Output all collected schemas to the head
     */
    public function output_schemas_to_head() {
        if (!empty(self::$schema_scripts)) {
            foreach (self::$schema_scripts as $schema) {
                echo $schema . "\n";
            }
        }
    }

    public function init($atts) {
        if (get_option('opio_active') === '0') {
            return '';
        }
        ob_start();
        $feed = $this->feed_deserializer->get_feed($atts['id']);
        $feed_id = '';
        $loaded_bizs = '';
        $business_name = '';
        $biz_id = '';
        $org_id = '';
        $feed_inited = false;
        $businesses = null;
        $reviews = null;
        $feed_object = json_decode($feed->post_content);

        if ($feed != null) {
            $feed_id = $feed->ID;
            $loaded_bizs_raw = $feed_object->loaded_bizs;
            $loaded_bizs = json_encode($feed_object->loaded_bizs);
            $business_name = $feed->post_title;
            $biz_id = ($feed_object->biz_id);
            $org_id = $feed_object->org_id;
            $biz_org_id = $feed_object->biz_org_id;
            $review_type = $feed_object->review_type;
            $review_option = $feed_object->review_option;
            if (isset($biz_id) || isset($org_id)) {

                $valid = preg_match('/^[a-zA-Z0-9]{15,20}$/', $biz_id);
                $valid = $valid || preg_match('/^[a-zA-Z0-9]{15,20}$/', $org_id);
                if ($valid) {
                    $feed_inited = true;
                }
            }

            $option = $review_option == "opio" ? "reviewFeed" : "allReviewFeed";
            $opio_handler = new Opio_Handler($biz_id, $option, $review_type, $org_id);
            $reviews = $opio_handler->get_business();

            // Extract schema if not already processed (fallback for dynamically added shortcodes)
            // Note: This won't add to head if wp_head already fired, but will remove from body
            $schemas = $this->extract_schema_from_html($reviews);
            if (!empty($schemas) && !did_action('wp_head')) {
                // Only add if wp_head hasn't fired yet
                self::$schema_scripts = array_merge(self::$schema_scripts, $schemas);
            }

            // Always remove schema scripts from body content
            $reviews = $this->remove_schema_from_html($reviews);

            // Wrap entire feed content with Nitropack exclusion wrapper
            echo '<div data-nitro-exclude="all" data-nitro-ignore="true" data-nitro-no-optimize="true" data-nitro-preserve-ws="true">';

            if($option == "allReviewFeeds" && $review_type == "singles") {
                $reviews = $this->feed_deserializer->prepareString($reviews);
                echo wp_kses($reviews, $this->feed_deserializer->get_allowed_tags());
            }
            else {
                echo $reviews;
            }
            echo '</div>';
            return ob_get_clean();
        }
    }
}
