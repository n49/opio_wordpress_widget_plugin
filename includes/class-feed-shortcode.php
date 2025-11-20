<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Core\Core;

class Feed_Shortcode {

    private static $schema_scripts = array();
    private static $schemas_processed = false;
    private static $processed_feed_ids = array(); // Track which feed IDs have been processed

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
        // Hook early to pre-process shortcodes and extract schemas before wp_head runs
        add_action('template_redirect', array($this, 'pre_process_schemas'), 1);
        // Hook into wp_head to output schemas - use late priority to avoid conflicts with other plugins
        // Many SEO plugins output at priority 10, so we use 99 to go after them
        add_action('wp_head', array($this, 'output_schemas_to_head'), 99);
        // Also try wp_footer as ultimate fallback in case wp_head is blocked
        add_action('wp_footer', array($this, 'output_schemas_to_footer_fallback'), 999);
        // Track if we've successfully output schemas
        add_action('shutdown', array($this, 'check_schema_output'), 999);
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
        // DEBUG: Add comment to head showing pre_process_schemas was called
        add_action('wp_head', function() {
            echo "<!-- OPIO DEBUG: pre_process_schemas() was called. schemas_processed=" . (self::$schemas_processed ? 'true' : 'false') . ", is_admin=" . (is_admin() ? 'true' : 'false') . " -->\n";
        }, 0);

        if (self::$schemas_processed || is_admin()) {
            add_action('wp_head', function() {
                echo "<!-- OPIO DEBUG: pre_process_schemas() exited early (already processed or admin) -->\n";
            }, 0);
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

        $content_length = strlen($content);
        $has_shortcode = has_shortcode($content, 'opio_feed');

        // Extract all opio_feed shortcode IDs from content using regex
        if (preg_match_all('/\[opio_feed[^\]]*id=["\']?(\d+)["\']?[^\]]*\]/i', $content, $matches)) {
            if (!empty($matches[1])) {
                $feed_ids = array_unique(array_map('intval', $matches[1]));
            }
        }

        // Also try parsing shortcode attributes for cases where format might differ
        if ($has_shortcode) {
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

        // DEBUG: Store debug info for wp_head
        $debug_feed_ids = $feed_ids;
        $debug_content_length = $content_length;
        $debug_has_shortcode = $has_shortcode;
        add_action('wp_head', function() use ($debug_feed_ids, $debug_content_length, $debug_has_shortcode) {
            echo "<!-- OPIO DEBUG: Content length=" . $debug_content_length . ", has_shortcode=" . ($debug_has_shortcode ? 'true' : 'false') . ", found feed_ids=" . implode(',', $debug_feed_ids) . " -->\n";
        }, 0);

        // Process all found feed IDs
        $schemas_found_count = 0;
        foreach ($feed_ids as $feed_id) {
            if ($feed_id > 0 && !in_array($feed_id, self::$processed_feed_ids)) {
                $schemas_before = count(self::$schema_scripts);
                $this->fetch_and_extract_schema($feed_id);
                $schemas_after = count(self::$schema_scripts);
                if ($schemas_after > $schemas_before) {
                    $schemas_found_count += ($schemas_after - $schemas_before);
                }
                // Mark this feed as processed
                self::$processed_feed_ids[] = $feed_id;
            }
        }

        // DEBUG: Store schema count for wp_head
        $debug_schema_count = count(self::$schema_scripts);
        $debug_feed_count = count($feed_ids);
        add_action('wp_head', function() use ($debug_schema_count, $schemas_found_count, $debug_feed_count) {
            echo "<!-- OPIO DEBUG: Processed " . $debug_feed_count . " feed(s), extracted " . $schemas_found_count . " schema(s), total schemas=" . $debug_schema_count . " -->\n";
        }, 0);

        self::$schemas_processed = true;
    }

    /**
     * Fetch feed and extract schema without outputting content
     */
    private function fetch_and_extract_schema($feed_id) {
        // Skip if already processed
        if (in_array($feed_id, self::$processed_feed_ids)) {
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") skipped - already processed -->\n";
            }, 0);
            return;
        }
        if (get_option('opio_active') === '0') {
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") skipped - plugin inactive -->\n";
            }, 0);
            return;
        }

        $feed = $this->feed_deserializer->get_feed($feed_id);
        
        if ($feed == null) {
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") - feed not found -->\n";
            }, 0);
            return;
        }

        $feed_object = json_decode($feed->post_content);
        
        if (!$feed_object) {
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") - failed to decode feed object -->\n";
            }, 0);
            return;
        }

        $biz_id = isset($feed_object->biz_id) ? $feed_object->biz_id : null;
        $org_id = isset($feed_object->org_id) ? $feed_object->org_id : null;
        $review_type = isset($feed_object->review_type) ? $feed_object->review_type : null;
        $review_option = isset($feed_object->review_option) ? $feed_object->review_option : null;

        if (!isset($biz_id) && !isset($org_id)) {
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") - no biz_id or org_id -->\n";
            }, 0);
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
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") - invalid biz_id/org_id -->\n";
            }, 0);
            return;
        }

        $option = ($review_option == "opio") ? "reviewFeed" : "allReviewFeed";
        $opio_handler = new Opio_Handler($biz_id, $option, $review_type, $org_id);
        $reviews = $opio_handler->get_business();

        if ($reviews) {
            $reviews_length = strlen($reviews);
            
            // DEBUG: Check if HTML contains schema-related strings
            $has_jsonld = (stripos($reviews, 'application/ld+json') !== false);
            $has_schema = (stripos($reviews, 'schema.org') !== false);
            $has_jsonldschema_id = (stripos($reviews, 'jsonldSchema') !== false);
            $has_script_tag = (stripos($reviews, '<script') !== false);
            
            // Extract schema scripts from the feed HTML
            $schemas = $this->extract_schema_from_html($reviews);
            $schemas_count = count($schemas);
            
            // DEBUG: Show sample of HTML for debugging
            $html_sample = substr($reviews, 0, 500);
            $html_sample = htmlspecialchars($html_sample, ENT_QUOTES, 'UTF-8');
            
            if (!empty($schemas)) {
                // Store schemas to be output in head
                $schemas_before_merge = count(self::$schema_scripts);
                self::$schema_scripts = array_merge(self::$schema_scripts, $schemas);
                $schemas_after_merge = count(self::$schema_scripts);
                
                // Mark this feed as processed
                self::$processed_feed_ids[] = $feed_id;
                
                add_action('wp_head', function() use ($feed_id, $schemas_count, $reviews_length, $schemas_before_merge, $schemas_after_merge, $has_jsonld, $has_schema, $has_jsonldschema_id) {
                    echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") SUCCESS - reviews_length=" . $reviews_length . ", extracted " . $schemas_count . " schema(s), stored " . ($schemas_after_merge - $schemas_before_merge) . " (total now: " . $schemas_after_merge . ") -->\n";
                    echo "<!-- OPIO DEBUG: HTML check - has_jsonld=" . ($has_jsonld ? 'true' : 'false') . ", has_schema=" . ($has_schema ? 'true' : 'false') . ", has_jsonldschema_id=" . ($has_jsonldschema_id ? 'true' : 'false') . " -->\n";
                }, 0);
            } else {
                add_action('wp_head', function() use ($feed_id, $reviews_length, $has_jsonld, $has_schema, $has_jsonldschema_id, $has_script_tag, $html_sample) {
                    echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") - reviews_length=" . $reviews_length . ", but NO schemas found in HTML -->\n";
                    echo "<!-- OPIO DEBUG: HTML check - has_jsonld=" . ($has_jsonld ? 'true' : 'false') . ", has_schema=" . ($has_schema ? 'true' : 'false') . ", has_jsonldschema_id=" . ($has_jsonldschema_id ? 'true' : 'false') . ", has_script_tag=" . ($has_script_tag ? 'true' : 'false') . " -->\n";
                    echo "<!-- OPIO DEBUG: HTML sample (first 500 chars): " . $html_sample . " -->\n";
                }, 0);
            }
        } else {
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") - get_business() returned empty/null -->\n";
            }, 0);
        }
    }

    /**
     * Extract JSON-LD schema scripts from HTML content
     */
    private function extract_schema_from_html($html) {
        $schemas = array();
        
        // More flexible pattern - match script tags with type="application/ld+json" 
        // Try multiple patterns to catch different formats
        
        // Pattern 1: Script with id="jsonldSchema" and type="application/ld+json"
        $pattern1 = '/<script[^>]*(?:id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\']|type=["\']application\/ld\+json["\'][^>]*id=["\']jsonldSchema["\'])[^>]*>(.*?)<\/script>/is';
        
        // Pattern 2: Script with just type="application/ld+json" (more flexible)
        $pattern2 = '/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is';
        
        // Try pattern 1 first (more specific)
        if (preg_match_all($pattern1, $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $schemas[] = $match[0]; // Full script tag
            }
        }
        
        // If pattern 1 didn't find anything, try pattern 2 (more general)
        if (empty($schemas) && preg_match_all($pattern2, $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Only add if it contains schema.org context
                if (stripos($match[1], '@context') !== false && stripos($match[1], 'schema.org') !== false) {
                    $schemas[] = $match[0]; // Full script tag
                }
            }
        }
        
        return $schemas;
    }

    /**
     * Remove schema scripts from HTML content
     */
    private function remove_schema_from_html($html) {
        $html_before = $html;
        
        // Remove script tags with id="jsonldSchema" and type="application/ld+json"
        // Handles attributes in any order and with single or double quotes
        $pattern1 = '/<script[^>]*(?:id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\']|type=["\']application\/ld\+json["\'][^>]*id=["\']jsonldSchema["\'])[^>]*>.*?<\/script>/is';
        $html = preg_replace($pattern1, '', $html);
        
        // Also try more general pattern for any application/ld+json script
        $pattern2 = '/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>.*?<\/script>/is';
        $html = preg_replace($pattern2, '', $html);
        
        // Also remove any stray head tags that might be in the content
        // Remove opening <head> tags
        $html = preg_replace('/<head[^>]*>/i', '', $html);
        // Remove closing </head> tags
        $html = preg_replace('/<\/head>/i', '', $html);
        
        $removed_length = strlen($html_before) - strlen($html);
        
        return $html;
    }

    /**
     * Output all collected schemas to the head
     */
    public function output_schemas_to_head() {
        // Use a static flag to prevent multiple outputs
        static $already_output = false;
        
        if ($already_output) {
            return; // Already output, skip
        }
        
        // Check for common plugin conflicts
        $active_plugins = get_option('active_plugins', array());
        $conflict_plugins = array();
        $known_conflicts = array('yoast', 'rank-math', 'seo', 'schema', 'cache', 'autoptimize', 'w3-total-cache', 'wp-super-cache');
        foreach ($active_plugins as $plugin) {
            foreach ($known_conflicts as $conflict) {
                if (stripos($plugin, $conflict) !== false) {
                    $conflict_plugins[] = $plugin;
                    break;
                }
            }
        }
        
        // Deduplicate schemas before outputting (in case of any duplicates)
        $unique_schemas = array_unique(self::$schema_scripts, SORT_STRING);
        $schema_count = count($unique_schemas);
        $is_empty = empty($unique_schemas);
        $array_type = gettype($unique_schemas);
        $original_count = count(self::$schema_scripts);
        $duplicates_removed = $original_count - $schema_count;
        $current_priority = current_filter() . ':' . (has_filter('wp_head', array($this, 'output_schemas_to_head')) ? 'hooked' : 'not-hooked');
        
        echo "<!-- OPIO DEBUG: output_schemas_to_head() called via " . current_filter() . " with " . $original_count . " schema(s), after deduplication: " . $schema_count . " schema(s), duplicates_removed=" . $duplicates_removed . ", is_empty=" . ($is_empty ? 'true' : 'false') . " -->\n";
        
        if (!empty($conflict_plugins)) {
            echo "<!-- OPIO DEBUG: Potential conflicting plugins detected: " . implode(', ', $conflict_plugins) . " -->\n";
        }
        
        if (!empty($unique_schemas)) {
            $already_output = true; // Mark as output
            echo "<!-- OPIO DEBUG: START outputting schemas to head -->\n";
            foreach ($unique_schemas as $index => $schema) {
                $schema_length = strlen($schema);
                $schema_preview = htmlspecialchars(substr($schema, 0, 100), ENT_QUOTES, 'UTF-8');
                echo "<!-- OPIO DEBUG: Schema #" . ($index + 1) . " (length=" . $schema_length . " chars, preview: " . $schema_preview . "...) -->\n";
                echo $schema . "\n";
            }
            echo "<!-- OPIO DEBUG: END outputting schemas to head -->\n";
        } else {
            echo "<!-- OPIO DEBUG: No schemas to output (schema_scripts array is empty) -->\n";
            echo "<!-- OPIO DEBUG: Checking if static property is accessible... -->\n";
            // Try to output what's actually in the array
            if (isset(self::$schema_scripts)) {
                echo "<!-- OPIO DEBUG: self::\$schema_scripts exists but is empty -->\n";
            } else {
                echo "<!-- OPIO DEBUG: self::\$schema_scripts does not exist! -->\n";
            }
        }
    }
    
    /**
     * Fallback method to output schemas in footer if head output was blocked
     */
    public function output_schemas_to_footer_fallback() {
        // Only output if schemas exist
        if (empty(self::$schema_scripts)) {
            return;
        }
        
        // Use static flag to track if head output happened
        static $head_output_detected = false;
        
        // Check if we can detect head output (this is a best-effort check)
        // In practice, if wp_head ran, our schemas should have been output
        // We'll use a simple heuristic: if this is being called, wp_head likely already ran
        
        $unique_schemas = array_unique(self::$schema_scripts, SORT_STRING);
        if (!empty($unique_schemas)) {
            // Only output in footer if we have a lot of schemas (suggesting head didn't work)
            // This is a safety net - ideally head should work
            echo "<!-- OPIO DEBUG: Footer fallback - schemas available but may not have been output in head. Check page source to verify head output. -->\n";
            // Note: We don't actually output here to avoid duplicates
            // Instead, we rely on the check_schema_output method to warn
        }
    }
    
    /**
     * Check if schemas were successfully output (called on shutdown)
     */
    public function check_schema_output() {
        if (!empty(self::$schema_scripts) && !is_admin()) {
            // This is just for debugging - in production you might want to log this
            // The debug comments in wp_head will show if output happened
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

            $reviews_length_before = strlen($reviews);
            $wp_head_fired = did_action('wp_head');
            $already_processed = in_array($feed_id, self::$processed_feed_ids);
            
            // Extract schema if not already processed (fallback for dynamically added shortcodes)
            // Note: This won't add to head if wp_head already fired, but will remove from body
            $schemas = $this->extract_schema_from_html($reviews);
            $schemas_count = count($schemas);
            
            if (!empty($schemas) && !$wp_head_fired && !$already_processed) {
                // Only add if wp_head hasn't fired yet AND this feed hasn't been processed yet
                self::$schema_scripts = array_merge(self::$schema_scripts, $schemas);
                // Mark this feed as processed
                self::$processed_feed_ids[] = $feed_id;
            }

            // Always remove schema scripts from body content
            $reviews = $this->remove_schema_from_html($reviews);
            $reviews_length_after = strlen($reviews);
            $removed_length = $reviews_length_before - $reviews_length_after;
            
            // DEBUG: Add comment in body showing what happened
            $debug_info = "<!-- OPIO DEBUG: init() shortcode executed - feed_id=" . $feed_id . ", reviews_length=" . $reviews_length_before . ", schemas_found=" . $schemas_count . ", wp_head_fired=" . ($wp_head_fired ? 'true' : 'false') . ", already_processed=" . ($already_processed ? 'true' : 'false') . ", removed_length=" . $removed_length . " -->";

            // Wrap entire feed content with Nitropack exclusion wrapper
            echo $debug_info . "\n";
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
