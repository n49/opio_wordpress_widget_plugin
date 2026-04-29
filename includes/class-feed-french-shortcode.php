<?php

namespace WP_Opio_Reviews\Includes;

/**
 * French feed shortcode [opio_feed_french id='X']
 *
 * Same as Feed_Optimized_Shortcode but hits the French-translated route.
 * Only supports "all" review_option (single entity).
 * Route: allReviewFeed-optimized-french?entId={id}
 */
class Feed_French_Shortcode {

    private $feed_deserializer;

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
    }

    public function register() {
        add_shortcode('opio_feed_french', array($this, 'init'));
    }

    private function build_feed_url($biz_id, $org_id, $review_type) {
        if ($review_type == 'orgfeed') {
            return "https://feed.op.io/multiReviewFeed/allReviews-optimized?orgId=${org_id}&schema_enabled=true&schema_type=Local%20Business";
        }
        return "https://feed.op.io/allReviewFeed-optimized-french?entId=${biz_id}";
    }

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

        $valid = preg_match('/^[a-zA-Z0-9]{15,20}$/', $biz_id)
              || preg_match('/^[a-zA-Z0-9]{15,20}$/', $org_id);
        if (!$valid) {
            return '';
        }

        $url = $this->build_feed_url($biz_id, $org_id, $review_type);
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
