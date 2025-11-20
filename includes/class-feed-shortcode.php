<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Core\Core;

class Feed_Shortcode {

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
    }

    function custom_esc($str) {
        return ($str);
    }

    public function register() {
        add_shortcode('opio_feed', array($this, 'init'));
    }

    /**
     * Extract JSON-LD schema from HTML head section and remove it from head
     */
    private function extract_jsonld_schema(&$html) {
        // Extract the JSON-LD schema script from head
        if (preg_match('/<head[^>]*>.*?<script[^>]*id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>.*?<\/head>/is', $html, $matches)) {
            // Remove JSON-LD schema from head
            $html = preg_replace('/<script[^>]*id=["\']jsonldSchema["\'][^>]*type=["\']application\/ld\+json["\'][^>]*>.*?<\/script>/is', '', $html);
            return '<script id="jsonldSchema" type="application/ld+json">' . trim($matches[1]) . '</script>';
        }
        return '';
    }

    /**
     * Extract all link and script tags from head section (everything except JSON-LD schema)
     * Prioritizes font links to ensure they load first
     */
    private function extract_head_resources($html) {
        $font_links = '';
        $other_links = '';
        $scripts = '';
        
        // Extract head content
        if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $html, $head_matches)) {
            $head_content = $head_matches[1];
            
            // Extract all link tags
            if (preg_match_all('/<link[^>]*>/i', $head_content, $link_matches)) {
                foreach ($link_matches[0] as $link) {
                    // Prioritize font links (Google Fonts, etc.)
                    if (preg_match('/fonts\.(googleapis|gstatic)/i', $link) || preg_match('/font/i', $link)) {
                        $font_links .= $link . "\n";
                    } else {
                        $other_links .= $link . "\n";
                    }
                }
            }
            
            // Extract all script tags with src attribute (external scripts)
            if (preg_match_all('/<script[^>]*src=["\'][^"\']+["\'][^>]*><\/script>/i', $head_content, $script_matches)) {
                $scripts = implode("\n", $script_matches[0]);
            }
        }
        
        // Return with font links first
        return trim($font_links . $other_links . $scripts);
    }

    /**
     * Remove head section from HTML, keeping only body content
     */
    private function remove_head_section($html) {
        // Remove the entire head section
        $html = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $html);
        return $html;
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

            // Extract JSON-LD schema from head and remove it (schema must come first for SEO)
            $jsonld_schema = $this->extract_jsonld_schema($reviews);
            
            // Extract remaining head resources (links and scripts)
            $head_resources = $this->extract_head_resources($reviews);
            
            // Remove head section from the HTML (can't have <head> tag in body)
            $reviews = $this->remove_head_section($reviews);
            
            // Output in correct order:
            // 1. JSON-LD schema first (for Google crawler/SEO)
            if (!empty($jsonld_schema)) {
                echo $jsonld_schema;
            }
            
            // 2. Head resources (links and scripts)
            if (!empty($head_resources)) {
                echo $head_resources;
            }

            // Wrap body content with Nitropack exclusion wrapper
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
