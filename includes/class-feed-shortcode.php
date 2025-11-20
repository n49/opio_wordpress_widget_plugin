<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Core\Core;

class Feed_Shortcode {

    private static $schema_scripts = array();
    private static $schemas_processed = false;
    private static $schemas_output_in_head = false; // Track if schemas were successfully output in head

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
        // Hook early to pre-process shortcodes and extract schemas before wp_head runs
        add_action('template_redirect', array($this, 'pre_process_schemas'), 1);
        // Start output buffering early to capture entire page output
        // This must start BEFORE wp_head to capture it
        add_action('template_redirect', array($this, 'start_output_buffering'), 999);
        // Hook into wp_head to output schemas (if they were pre-processed)
        add_action('wp_head', array($this, 'output_schemas_to_head'), 1);
        // Process buffer on shutdown to inject schemas (if they were found after wp_head)
        add_action('shutdown', array($this, 'process_output_buffer'), 999);
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
        
        $feed_ids = array();
        $content_sources = array();

        // Check current post/page content
        if ($post && isset($post->post_content)) {
            $content_sources[] = $post->post_content;
        }

        // Check all posts in the main query (for archive pages, etc.)
        if ($wp_query && isset($wp_query->posts) && is_array($wp_query->posts)) {
            foreach ($wp_query->posts as $query_post) {
                if (isset($query_post->post_content)) {
                    $content_sources[] = $query_post->post_content;
                }
            }
        }

        // Check widget areas for shortcodes
        // Widgets store content differently, so we need to check widget options
        $all_widgets = wp_get_sidebars_widgets();
        if (is_array($all_widgets)) {
            foreach ($all_widgets as $sidebar_id => $widget_ids) {
                if (!is_array($widget_ids)) continue;
                
                foreach ($widget_ids as $widget_id) {
                    // Check text widgets (classic widget)
                    if (preg_match('/^text-(\d+)$/', $widget_id, $text_match)) {
                        $text_widgets = get_option('widget_text', array());
                        if (is_array($text_widgets)) {
                            foreach ($text_widgets as $widget_instance) {
                                if (isset($widget_instance['text'])) {
                                    $content_sources[] = $widget_instance['text'];
                                }
                            }
                        }
                    }
                    
                    // Check custom HTML widgets (WordPress 4.8+)
                    if (preg_match('/^custom_html-(\d+)$/', $widget_id, $html_match)) {
                        $html_widgets = get_option('widget_custom_html', array());
                        if (is_array($html_widgets)) {
                            foreach ($html_widgets as $widget_instance) {
                                if (isset($widget_instance['content'])) {
                                    $content_sources[] = $widget_instance['content'];
                                }
                            }
                        }
                    }
                    
                    // Check block widgets (WordPress 5.8+ / Full Site Editing)
                    if (preg_match('/^block-(\d+)$/', $widget_id, $block_match)) {
                        $block_widgets = get_option('widget_block', array());
                        if (is_array($block_widgets)) {
                            foreach ($block_widgets as $widget_instance) {
                                if (isset($widget_instance['content'])) {
                                    $content_sources[] = $widget_instance['content'];
                                }
                            }
                        }
                    }
                }
            }
        }

        // Extract shortcodes from all content sources
        foreach ($content_sources as $content) {
            if (empty($content)) continue;
            
            // Match: [opio_feed id='52'], [opio_feed id="52"], [opio_feed id=52]
            // This regex handles spaces and single/double quotes
            if (preg_match_all('/\[opio_feed[^\]]*id\s*=\s*["\']?(\d+)["\']?[^\]]*\]/i', $content, $matches)) {
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $feed_id_str) {
                        $feed_id = absint($feed_id_str);
                        if ($feed_id > 0) {
                            $feed_ids[] = $feed_id;
                        }
                    }
                }
            }
            
            // Also use WordPress's shortcode parser for robustness
            if (has_shortcode($content, 'opio_feed')) {
                $pattern = get_shortcode_regex(array('opio_feed'));
                if (preg_match_all('/' . $pattern . '/s', $content, $shortcode_matches)) {
                    foreach ($shortcode_matches[3] as $atts_string) {
                        if (empty($atts_string)) continue;
                        $atts = shortcode_parse_atts($atts_string);
                        if (isset($atts['id'])) {
                            $feed_id = absint($atts['id']);
                            if ($feed_id > 0) {
                                $feed_ids[] = $feed_id;
                            }
                        }
                    }
                }
            }
        }

        // Remove duplicates
        $feed_ids = array_unique(array_filter($feed_ids));

        // Process all found feed IDs - fetch schema from feed.op.io BEFORE wp_head
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
        // First, remove entire head sections that contain our schema (in case feed has nested head tags)
        // Match head tags that contain our schema script
        $head_with_schema_pattern = '/<head[^>]*>.*?<script[^>]*(?:id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\']|type=["\']application\/ld\+json["\'][^>]*id=["\']jsonldSchema["\'])[^>]*>.*?<\/script>.*?<\/head>/is';
        $html = preg_replace($head_with_schema_pattern, '', $html);
        
        // Remove script tags with id="jsonldSchema" and type="application/ld+json"
        // Handles attributes in any order and with single or double quotes
        // This catches any remaining schema scripts (even outside head tags)
        $schema_script_pattern = '/<script[^>]*(?:id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\']|type=["\']application\/ld\+json["\'][^>]*id=["\']jsonldSchema["\'])[^>]*>.*?<\/script>/is';
        $html = preg_replace($schema_script_pattern, '', $html);
        
        // Also remove any stray/empty head tags that might be in the content
        // Remove opening <head> tags
        $html = preg_replace('/<head[^>]*>/i', '', $html);
        // Remove closing </head> tags
        $html = preg_replace('/<\/head>/i', '', $html);
        
        return $html;
    }

    /**
     * Start output buffering to capture page output
     * This captures the entire page including wp_head so we can inject schemas if needed
     */
    public function start_output_buffering() {
        // Only start buffering on front-end pages
        if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
            return;
        }
        
        // Check if output buffering is already started (by another plugin)
        // If another plugin started buffering, we'll still add our own buffer on top
        // This ensures our callback runs and can inject schemas
        $existing_level = ob_get_level();
        
        // Start our own output buffering with callback
        // Even if other buffers exist, our callback will be called when WordPress flushes
        // The callback modifies the final HTML before it's sent, so Google crawler sees it
        ob_start(array($this, 'buffer_callback'));
    }
    
    /**
     * Process output buffer on shutdown
     * When output buffering with a callback is used, WordPress automatically processes it
     * But we need to ensure buffers are properly flushed
     */
    public function process_output_buffer() {
        // Only process on front-end pages
        if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
            return;
        }
        
        // If we started a buffer with a callback, WordPress will handle it
        // But we need to ensure any remaining buffers are flushed
        $buffer_level = ob_get_level();
        if ($buffer_level > 0) {
            // If we have schemas to inject, make sure the buffer is processed
            // The callback will handle the injection
            // WordPress will automatically call the callback when ob_end_flush is called
            // But we need to make sure the buffer level is correct
            // Note: If there are multiple buffers, we only want to flush ours
            // WordPress typically handles the top-level buffer automatically
        }
    }
    
    /**
     * Buffer callback - inject schemas into head section if they weren't output in wp_head
     */
    public function buffer_callback($buffer) {
        // Only process if we have schemas
        if (empty(self::$schema_scripts)) {
            return $buffer;
        }
        
        // Check if schemas are already in the head section
        if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $buffer, $head_match)) {
            $head_content = $head_match[1];
            // Check if head already contains our schemas
            $has_our_schema = (stripos($head_content, 'id="jsonldSchema"') !== false);
            if ($has_our_schema) {
                // Schemas already in head, mark as successful
                self::$schemas_output_in_head = true;
                return $buffer;
            }
        }
        
        // Find the closing </head> tag
        $head_position = stripos($buffer, '</head>');
        if ($head_position === false) {
            // No head tag found, return as-is (schema will remain in body)
            return $buffer;
        }
        
        // Get unique schemas (deduplicate)
        $unique_schemas = array_unique(self::$schema_scripts, SORT_STRING);
        
        // Build schema HTML to inject
        $schema_html = "\n";
        foreach ($unique_schemas as $schema) {
            $schema_html .= $schema . "\n";
        }
        
        // Inject schemas before closing </head> tag
        $buffer = substr_replace($buffer, $schema_html . '</head>', $head_position, 7);
        
        // Mark that schemas were successfully injected via buffer
        self::$schemas_output_in_head = true;
        
        return $buffer;
    }
    
    /**
     * Output all collected schemas to the head
     */
    public function output_schemas_to_head() {
        // Use static flag to prevent duplicate output
        static $already_output = false;
        
        if ($already_output || empty(self::$schema_scripts)) {
            return;
        }
        
        $already_output = true;
        
        // Deduplicate schemas
        $unique_schemas = array_unique(self::$schema_scripts, SORT_STRING);
        
        foreach ($unique_schemas as $schema) {
            echo $schema . "\n";
        }
        
        // Mark that schemas were successfully output in head
        self::$schemas_output_in_head = true;
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

            // Extract schema and store it (even if wp_head already fired - we'll inject via buffer)
            $schemas = $this->extract_schema_from_html($reviews);
            $has_schema = !empty($schemas);
            
            if ($has_schema) {
                // Always store schemas - we'll inject them via output buffer if wp_head already fired
                self::$schema_scripts = array_merge(self::$schema_scripts, $schemas);
            }

            // Remove schema scripts from body content if we've collected schemas
            // If schemas are in our collection, they will be (or already were) output in head
            // Only keep schema in body if NO schemas were collected at all (safety fallback)
            if ($has_schema || self::$schemas_output_in_head || !empty(self::$schema_scripts)) {
                // We found schema in this feed OR schemas were already processed/output
                // Remove schema from body since it will be (or was) in head
                $reviews = $this->remove_schema_from_html($reviews);
            }
            // If no schemas were found and none are in collection, leave in body as fallback

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