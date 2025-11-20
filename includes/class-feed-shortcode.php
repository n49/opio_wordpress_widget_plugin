<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Core\Core;

class Feed_Shortcode {

    private static $schema_scripts = array();
    private static $schemas_processed = false;
    private static $processed_feed_ids = array(); // Track which feed IDs have been processed
    private static $needs_buffer_injection = false; // Flag to indicate schemas need buffer injection
    private static $schemas_output_in_head = false; // Track if schemas were output in wp_head
    private $extraction_debug = null; // Store debug info for schema extraction

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
        // Hook early to pre-process shortcodes and extract schemas before wp_head runs
        add_action('template_redirect', array($this, 'pre_process_schemas'), 1);
        // Start output buffering to capture page output and inject schemas
        add_action('template_redirect', array($this, 'start_output_buffering'), 999);
        // Hook into wp_head to output schemas - use late priority to avoid conflicts with other plugins
        // Many SEO plugins output at priority 10, so we use 99 to go after them
        add_action('wp_head', array($this, 'output_schemas_to_head'), 99);
        // Inject schemas into output buffer after wp_head (if schemas were found later)
        add_action('wp_footer', array($this, 'inject_schemas_into_buffer'), 1);
        // Also try wp_footer as ultimate fallback in case wp_head is blocked
        add_action('wp_footer', array($this, 'output_schemas_to_footer_fallback'), 999);
        // Track if we've successfully output schemas and process output buffer
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
        // DEBUG: Add comment to head showing pre_process_schemas was called
        add_action('wp_head', function() {
            echo "<!-- OPIO DEBUG: pre_process_schemas() was called. schemas_processed=" . (self::$schemas_processed ? 'true' : 'false') . ", is_admin=" . (is_admin() ? 'true' : 'false') . " -->\n";
        }, 0);

        if (is_admin()) {
            add_action('wp_head', function() {
                echo "<!-- OPIO DEBUG: pre_process_schemas() exited early (admin) -->\n";
            }, 0);
            return;
        }
        
        // Don't return early if already processed - still check in case shortcodes are added later
        // But set a flag to prevent infinite loops
        if (self::$schemas_processed) {
            add_action('wp_head', function() {
                echo "<!-- OPIO DEBUG: pre_process_schemas() already ran once, checking again for missed shortcodes -->\n";
            }, 0);
        }

        global $wp_query, $post, $wp_registered_sidebars;
        
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

        // Check widgets for shortcodes
        $widget_instances = get_option('widget_text', array());
        if (is_array($widget_instances)) {
            foreach ($widget_instances as $instance) {
                if (is_array($instance) && isset($instance['text'])) {
                    $content .= $instance['text'];
                }
            }
        }

        // Check custom HTML widgets (WordPress 4.8+)
        $custom_html_widgets = get_option('widget_custom_html', array());
        if (is_array($custom_html_widgets)) {
            foreach ($custom_html_widgets as $instance) {
                if (is_array($instance) && isset($instance['content'])) {
                    $content .= $instance['content'];
                }
            }
        }

        // Check active widget areas - get all widget options
        global $wp_registered_widgets;
        if ($wp_registered_widgets) {
            $sidebars_widgets = get_option('sidebars_widgets', array());
            foreach ($sidebars_widgets as $sidebar_id => $widgets) {
                if (is_array($widgets)) {
                    foreach ($widgets as $widget_id) {
                        // Extract widget type and number from widget ID
                        if (preg_match('/^(.+)-(\d+)$/', $widget_id, $matches)) {
                            $widget_type = $matches[1];
                            $widget_number = $matches[2];
                            
                            // Get widget instance data
                            $widget_option = get_option('widget_' . $widget_type, array());
                            if (is_array($widget_option) && isset($widget_option[$widget_number])) {
                                $instance = $widget_option[$widget_number];
                                if (is_array($instance)) {
                                    // Check common widget content fields
                                    foreach (array('text', 'content', 'html', 'description') as $field) {
                                        if (isset($instance[$field])) {
                                            $content .= $instance[$field];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $content_length = strlen($content);
        $has_shortcode = has_shortcode($content, 'opio_feed');

        // Extract all opio_feed shortcode IDs from content using regex
        if (preg_match_all('/\[opio_feed[^\]]*id=["\']?(\d+)["\']?[^\]]*\]/i', $content, $matches)) {
            if (!empty($matches[1])) {
                // Use absint() for better security - ensures positive integers only
                $feed_ids = array_unique(array_map('absint', $matches[1]));
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
                        $feed_ids[] = absint($atts['id']); // Use absint() for security
                    }
                }
                $feed_ids = array_unique($feed_ids);
            }
        }

        // DEBUG: Store debug info for wp_head
        $debug_feed_ids = array_map('absint', $feed_ids); // Sanitize for output
        $debug_content_length = absint($content_length);
        $debug_has_shortcode = $has_shortcode;
        add_action('wp_head', function() use ($debug_feed_ids, $debug_content_length, $debug_has_shortcode) {
            echo "<!-- OPIO DEBUG: Content length=" . esc_html($debug_content_length) . ", has_shortcode=" . ($debug_has_shortcode ? 'true' : 'false') . ", found feed_ids=" . esc_html(implode(',', $debug_feed_ids)) . " -->\n";
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
                // Get extraction debug info if available
                $extraction_debug_info = '';
                if ($this->extraction_debug) {
                    $extraction_debug_info = "<!-- OPIO DEBUG: Extraction details - has_jsonld=" . ($this->extraction_debug['has_jsonld'] ? 'true' : 'false') . 
                        ", has_jsonldschema=" . ($this->extraction_debug['has_jsonldschema'] ? 'true' : 'false') . 
                        ", has_schema_context=" . ($this->extraction_debug['has_schema_context'] ? 'true' : 'false') . 
                        ", html_length=" . absint($this->extraction_debug['html_length']) . " -->\n";
                    // Show a sample of where schema might be
                    $html_sample_safe = htmlspecialchars(substr($this->extraction_debug['html_sample'], 0, 500), ENT_QUOTES, 'UTF-8');
                    $extraction_debug_info .= "<!-- OPIO DEBUG: HTML sample (first 500 chars): " . $html_sample_safe . " -->\n";
                }
                
                add_action('wp_head', function() use ($feed_id, $reviews_length, $has_jsonld, $has_schema, $has_jsonldschema_id, $has_script_tag, $html_sample, $extraction_debug_info) {
                    echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . absint($feed_id) . ") - reviews_length=" . absint($reviews_length) . ", but NO schemas found in HTML -->\n";
                    echo "<!-- OPIO DEBUG: HTML check - has_jsonld=" . ($has_jsonld ? 'true' : 'false') . ", has_schema=" . ($has_schema ? 'true' : 'false') . ", has_jsonldschema_id=" . ($has_jsonldschema_id ? 'true' : 'false') . ", has_script_tag=" . ($has_script_tag ? 'true' : 'false') . " -->\n";
                    echo "<!-- OPIO DEBUG: HTML sample (first 500 chars): " . htmlspecialchars($html_sample, ENT_QUOTES, 'UTF-8') . " -->\n";
                    if ($extraction_debug_info) {
                        echo $extraction_debug_info;
                    }
                }, 0);
            }
        } else {
            add_action('wp_head', function() use ($feed_id) {
                echo "<!-- OPIO DEBUG: fetch_and_extract_schema(feed_id=" . $feed_id . ") - get_business() returned empty/null -->\n";
            }, 0);
        }
    }

    /**
     * Sanitize schema output to prevent XSS attacks
     * Validates that schema contains only valid JSON-LD structure
     */
    private function sanitize_schema_output($schema) {
        // Extract the JSON content from the script tag
        if (!preg_match('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $schema, $matches)) {
            return false; // Not a valid schema script tag
        }
        
        $json_content = trim($matches[1]);
        
        // Validate JSON syntax
        $decoded = json_decode($json_content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false; // Invalid JSON
        }
        
        // Check that it's a valid schema.org structure
        if (!isset($decoded['@context']) || !isset($decoded['@type'])) {
            return false; // Missing required schema.org fields
        }
        
        // Only allow schema.org context
        if (stripos($decoded['@context'], 'schema.org') === false) {
            return false; // Invalid context
        }
        
        // Re-encode to JSON and rebuild the script tag safely
        $safe_json = wp_json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($safe_json === false) {
            return false;
        }
        
        // Rebuild the script tag with sanitized content
        // Extract attributes from original script tag
        if (preg_match('/<script([^>]*)>/i', $schema, $attr_matches)) {
            $attributes = $attr_matches[1];
            // Ensure type attribute is present and correct
            if (stripos($attributes, 'type=') === false) {
                $attributes .= ' type="application/ld+json"';
            }
            return '<script' . $attributes . '>' . $safe_json . '</script>';
        }
        
        // Fallback: simple script tag
        return '<script type="application/ld+json">' . $safe_json . '</script>';
    }
    
    /**
     * Extract JSON-LD schema scripts from HTML content
     */
    private function extract_schema_from_html($html) {
        $schemas = array();
        
        // More flexible pattern - match script tags with type="application/ld+json" 
        // Try multiple patterns to catch different formats
        // Use DOTALL flag (s) to match across newlines
        
        // Pattern 1: Script with id="jsonldSchema" and type="application/ld+json"
        // This pattern handles attributes in any order, with optional whitespace, and matches multi-line content
        // Match script tag with both id="jsonldSchema" AND type="application/ld+json" (in any order)
        $pattern1 = '/<script[^>]*(?:id\s*=\s*["\']jsonldSchema["\'][^>]*type\s*=\s*["\']application\/ld\+json["\']|type\s*=\s*["\']application\/ld\+json["\'][^>]*id\s*=\s*["\']jsonldSchema["\'])[^>]*>(.*?)<\/script>/is';
        
        // Pattern 2: Script with just type="application/ld+json" (more flexible)
        $pattern2 = '/<script[^>]*type\s*=\s*["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is';
        
        // Try pattern 1 first (more specific) - handles id and type in either order
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
        
        // Pattern 3: Try to extract from within <head> tags if schema is inside head
        if (empty($schemas)) {
            // Extract head section first
            if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $html, $head_match)) {
                $head_content = $head_match[1];
                // Now search for schemas in head content
                if (preg_match_all($pattern1, $head_content, $head_matches, PREG_SET_ORDER)) {
                    foreach ($head_matches as $match) {
                        $schemas[] = $match[0]; // Full script tag
                    }
                }
                // Also try pattern 2 in head
                if (empty($schemas) && preg_match_all($pattern2, $head_content, $head_matches, PREG_SET_ORDER)) {
                    foreach ($head_matches as $match) {
                        if (stripos($match[1], '@context') !== false && stripos($match[1], 'schema.org') !== false) {
                            $schemas[] = $match[0]; // Full script tag
                        }
                    }
                }
            }
        }
        
        // DEBUG: If no schemas found, check what's actually in the HTML
        if (empty($schemas)) {
            // Check if HTML contains the schema markers
            $has_jsonld = (stripos($html, 'application/ld+json') !== false);
            $has_jsonldschema = (stripos($html, 'jsonldSchema') !== false);
            $has_schema_context = (stripos($html, '@context') !== false && stripos($html, 'schema.org') !== false);
            
            // Store debug info for later output
            $this->extraction_debug = array(
                'has_jsonld' => $has_jsonld,
                'has_jsonldschema' => $has_jsonldschema,
                'has_schema_context' => $has_schema_context,
                'html_length' => strlen($html),
                'html_sample' => substr($html, 0, 1000)
            );
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
            // Sanitize plugin names before output
            $safe_plugin_names = array_map('esc_html', $conflict_plugins);
            echo "<!-- OPIO DEBUG: Potential conflicting plugins detected: " . esc_html(implode(', ', $safe_plugin_names)) . " -->\n";
        }
        
        if (!empty($unique_schemas)) {
            $already_output = true; // Mark as output
            self::$schemas_output_in_head = true; // Mark that schemas were output in head
            echo "<!-- OPIO DEBUG: START outputting schemas to head -->\n";
            foreach ($unique_schemas as $index => $schema) {
                // Validate and sanitize schema before outputting
                $safe_schema = $this->sanitize_schema_output($schema);
                if ($safe_schema) {
                    $schema_length = absint(strlen($safe_schema));
                    $schema_preview = htmlspecialchars(substr($safe_schema, 0, 100), ENT_QUOTES, 'UTF-8');
                    echo "<!-- OPIO DEBUG: Schema #" . absint($index + 1) . " (length=" . $schema_length . " chars, preview: " . $schema_preview . "...) -->\n";
                    echo $safe_schema . "\n";
                } else {
                    echo "<!-- OPIO DEBUG: Schema #" . absint($index + 1) . " was filtered out for security reasons -->\n";
                }
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
     * Start output buffering to capture page output
     */
    public function start_output_buffering() {
        if (!is_admin() && !wp_doing_ajax()) {
            ob_start(array($this, 'buffer_callback'));
        }
    }
    
    /**
     * Inject schemas into buffer if they weren't in head
     */
    public function inject_schemas_into_buffer() {
        // Check if schemas exist but weren't output in head
        if (!empty(self::$schema_scripts)) {
            // Mark that we need to inject into buffer
            self::$needs_buffer_injection = true;
        }
    }
    
    /**
     * Process output buffer and inject schemas into head if needed
     */
    public function process_output_buffer() {
        if (!is_admin() && !wp_doing_ajax() && ob_get_level() > 0) {
            ob_end_flush();
        }
    }
    
    /**
     * Buffer callback - inject schemas into head section
     */
    public function buffer_callback($buffer) {
        // Only process if we have schemas
        if (empty(self::$schema_scripts)) {
            return $buffer;
        }
        
        // If schemas were already output in wp_head, don't inject again
        if (self::$schemas_output_in_head) {
            return $buffer;
        }
        
        // Check if schemas are already in the head section by looking for our debug comments or schema structure
        if (stripos($buffer, 'OPIO DEBUG: START outputting schemas to head') !== false ||
            stripos($buffer, 'OPIO: Injected schemas via output buffering') !== false) {
            // Schemas already in head, don't inject again
            return $buffer;
        }
        
        // Check if any schema-like content is already in head
        $head_section = '';
        if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $buffer, $head_match)) {
            $head_section = $head_match[1];
            // Check if head already contains JSON-LD schemas
            if (stripos($head_section, 'id="jsonldSchema"') !== false || 
                (stripos($head_section, 'type="application/ld+json"') !== false && stripos($head_section, '@context') !== false)) {
                // Schemas already in head, don't inject
                return $buffer;
            }
        }
        
        // Find the closing </head> tag
        $head_position = stripos($buffer, '</head>');
        if ($head_position === false) {
            // No head tag found, return as-is
            return $buffer;
        }
        
        // Get unique schemas
        $unique_schemas = array_unique(self::$schema_scripts, SORT_STRING);
        
        // Build schema HTML
        $schema_html = "\n<!-- OPIO: Injected schemas via output buffering (wp_head already fired) -->\n";
        foreach ($unique_schemas as $schema) {
            $safe_schema = $this->sanitize_schema_output($schema);
            if ($safe_schema) {
                $schema_html .= $safe_schema . "\n";
            }
        }
        $schema_html .= "<!-- OPIO: End injected schemas -->\n";
        
        // Inject schemas before closing </head> tag
        $buffer = substr_replace($buffer, $schema_html . '</head>', $head_position, 7);
        
        return $buffer;
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
        
        // Validate and sanitize shortcode attributes
        if (!isset($atts['id']) || empty($atts['id'])) {
            return '<!-- OPIO ERROR: Feed ID is required -->';
        }
        
        // Sanitize feed ID - must be a positive integer
        $feed_id = absint($atts['id']);
        if ($feed_id <= 0) {
            return '<!-- OPIO ERROR: Invalid feed ID -->';
        }
        
        ob_start();
        $feed = $this->feed_deserializer->get_feed($feed_id);
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
            
            // Initialize variables for JavaScript injection (if needed)
            $js_injection_warning = null;
            $js_injection_output = null;
            
            // Extract schema if not already processed (fallback for dynamically added shortcodes)
            $schemas = $this->extract_schema_from_html($reviews);
            $schemas_count = count($schemas);
            
            if (!empty($schemas) && !$already_processed) {
                if (!$wp_head_fired) {
                    // wp_head hasn't fired yet - add to head normally
                    self::$schema_scripts = array_merge(self::$schema_scripts, $schemas);
                    // Mark this feed as processed
                    self::$processed_feed_ids[] = $feed_id;
                } else {
                    // wp_head already fired - inject via JavaScript as fallback
                    // This won't help with SEO crawlers, but better than nothing
                    // Store schemas for JavaScript injection
                    self::$schema_scripts = array_merge(self::$schema_scripts, $schemas);
                    self::$processed_feed_ids[] = $feed_id;
                    
                    // Inject schemas into head via JavaScript
                    $js_injection = '<script type="text/javascript">';
                    $js_injection .= '(function() {';
                    $js_injection .= 'var head = document.getElementsByTagName("head")[0];';
                    $counter = 0;
                    foreach ($schemas as $schema) {
                        // Sanitize schema before injecting via JavaScript
                        $safe_schema = $this->sanitize_schema_output($schema);
                        if (!$safe_schema) {
                            continue; // Skip invalid schemas
                        }
                        
                        // Extract the JSON-LD content from the sanitized script tag
                        if (preg_match('/<script[^>]*>(.*?)<\/script>/is', $safe_schema, $matches)) {
                            $json_content = trim($matches[1]);
                            
                            // Validate JSON one more time
                            $decoded = json_decode($json_content, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                continue; // Skip invalid JSON
                            }
                            
                            // Re-encode to ensure safety
                            $safe_json = wp_json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                            if ($safe_json === false) {
                                continue; // Skip if encoding fails
                            }
                            
                            $var_name = 'script' . $counter;
                            // Use wp_json_encode for proper escaping in JavaScript context
                            $js_injection .= 'var ' . $var_name . ' = document.createElement("script");';
                            $js_injection .= $var_name . '.type = "application/ld+json";';
                            // Use wp_json_encode which handles JS context escaping properly
                            $js_injection .= $var_name . '.textContent = ' . wp_json_encode($json_content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';';
                            $js_injection .= 'head.appendChild(' . $var_name . ');';
                            $counter++;
                        }
                    }
                    $js_injection .= '})();';
                    $js_injection .= '</script>';
                    
                    // Store for output later (after debug info)
                    $js_injection_output = $js_injection;
                    
                    // Note: JavaScript injection won't help with SEO crawlers
                    // The real solution is to ensure pre_process_schemas finds the shortcodes
                    // Output the JavaScript injection right here in the body
                    $js_injection_warning = "<!-- OPIO DEBUG: wp_head already fired, injecting schemas via JavaScript (NOTE: This won't help with SEO crawlers - schemas need to be in HTML source) -->";
                }
            }

            // Always remove schema scripts from body content
            $reviews = $this->remove_schema_from_html($reviews);
            $reviews_length_after = strlen($reviews);
            $removed_length = $reviews_length_before - $reviews_length_after;
            
            // DEBUG: Add comment in body showing what happened (sanitize all values)
            $js_used = (!empty($schemas) && !$already_processed && $wp_head_fired) ? ', js_injection_used=true (WARNING: JavaScript injection - may not help SEO)' : '';
            $debug_info = "<!-- OPIO DEBUG: init() shortcode executed - feed_id=" . absint($feed_id) . ", reviews_length=" . absint($reviews_length_before) . ", schemas_found=" . absint($schemas_count) . ", wp_head_fired=" . ($wp_head_fired ? 'true' : 'false') . ", already_processed=" . ($already_processed ? 'true' : 'false') . ", removed_length=" . absint($removed_length) . esc_html($js_used) . " -->";

            // Wrap entire feed content with Nitropack exclusion wrapper
            echo $debug_info . "\n";
            // Output JavaScript injection warning and script if needed
            if (isset($js_injection_warning)) {
                echo $js_injection_warning . "\n";
            }
            if (isset($js_injection_output)) {
                echo $js_injection_output . "\n";
            }
            echo '<div data-nitro-exclude="all" data-nitro-ignore="true" data-nitro-no-optimize="true" data-nitro-preserve-ws="true">';

            if($option == "allReviewFeeds" && $review_type == "singles") {
                $reviews = $this->feed_deserializer->prepareString($reviews);
                // Use wp_kses for proper sanitization
                echo wp_kses($reviews, $this->feed_deserializer->get_allowed_tags());
            }
            else {
                // SECURITY: Sanitize all remote HTML content using the same allowed tags
                // This prevents XSS attacks from malicious content in the feed
                echo wp_kses($reviews, $this->feed_deserializer->get_allowed_tags());
            }
            echo '</div>';
            return ob_get_clean();
        }
    }
}
