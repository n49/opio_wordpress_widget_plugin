<?php

namespace WP_Opio_Reviews\Includes;

/**
 * Optimized feed shortcode [opio_feed_optimized id='X']
 *
 * Completely isolated from Feed_Shortcode / Opio_Handler.
 * Hits the -optimized routes on feed.op.io which return identical output
 * but with smaller HTML (external JS, consolidated CSS, minimal JSON embeds).
 *
 * Only the "all" review_option has optimized routes:
 *   - allReviewFeed-optimized?entId={id}
 *   - multiReviewFeed/allReviews-optimized?orgId={id}
 *
 * The "opio" review_option falls back to standard routes (no optimized variant).
 */
class Feed_Optimized_Shortcode {

    private $feed_deserializer;

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
    }

    public function register() {
        add_shortcode('opio_feed_optimized', array($this, 'init'));
    }

    /**
     * Build the feed.op.io URL for optimized routes.
     */
    private function build_feed_url($biz_id, $org_id, $review_option, $review_type) {
        if ($review_option == 'opio') {
            // No optimized variant for opio — use standard routes
            if ($review_type == 'orgfeed') {
                return "https://feed.op.io/multiReviewFeed?orgId=${org_id}&schema_enabled=true&schema_type=Local%20Business";
            }
            if ($review_type == 'single') {
                return "https://feed.op.io/reviewFeed?entityid=${biz_id}";
            }
            return "https://feed.op.io/reviewFeed?entId=${biz_id}";
        }

        // "all" review_option — use optimized routes
        if ($review_type == 'orgfeed') {
            return "https://feed.op.io/multiReviewFeed/allReviews-optimized?orgId=${org_id}&schema_enabled=true&schema_type=Local%20Business";
        }
        return "https://feed.op.io/allReviewFeed-optimized?entId=${biz_id}";
    }

    /**
     * Fetch HTML from feed.op.io.
     */
    private function fetch_feed($url) {
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return null;
        }
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return null;
        }
        return $body;
    }

    public function init($atts) {
        if (get_option('opio_active') === '0') {
            return '';
        }

        $feed = $this->feed_deserializer->get_feed($atts['id']);
        if ($feed == null) {
            return '';
        }

        $feed_object = json_decode($feed->post_content);
        if (!$feed_object) {
            return '';
        }

        $biz_id = isset($feed_object->biz_id) ? $feed_object->biz_id : '';
        $org_id = isset($feed_object->org_id) ? $feed_object->org_id : '';
        $review_type = isset($feed_object->review_type) ? $feed_object->review_type : '';
        $review_option = isset($feed_object->review_option) ? $feed_object->review_option : '';

        // Validate IDs
        $valid = preg_match('/^[a-zA-Z0-9]{15,20}$/', $biz_id)
              || preg_match('/^[a-zA-Z0-9]{15,20}$/', $org_id);
        if (!$valid) {
            return '';
        }

        $url = $this->build_feed_url($biz_id, $org_id, $review_option, $review_type);
        $reviews = $this->fetch_feed($url);
        if ($reviews === null) {
            return '';
        }

        ob_start();

        echo '<div data-nitro-exclude="all" data-nitro-ignore="true" data-nitro-no-optimize="true" data-nitro-preserve-ws="true">';

        $entity_id = !empty($biz_id) ? $biz_id : $org_id;
        if (!empty($entity_id)) {
            echo '<script>window.opioEntityId = "' . esc_js($entity_id) . '";</script>';
        }

        echo $reviews;
        echo '</div>';

        return ob_get_clean();
    }
}
