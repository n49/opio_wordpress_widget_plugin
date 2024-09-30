<?php

namespace WP_Opio_Reviews\Includes;

class Opio_Handler {

    public function __construct($entity_id, $entity_type, $review_type, $org_id) {
        $this->entity_id = $entity_id;
        $this->entity_type = $entity_type;
        $this->review_type = $review_type;
        $this->org_id = $org_id;
    }

    public function get_business() {
        $ent_id = $this->entity_id;
        $ent_type = $this->entity_type;
        $rev_type = $this->review_type;
        $org_id = $this->org_id;
        if($ent_type == 'allReviewFeed' && $rev_type == 'single') {
            $biz_url = "https://feed.op.io/allReviewFeed?entId=${ent_id}";
        }
        else if($ent_type == 'reviewFeed' && $rev_type == 'single') {
            $biz_url = "https://feed.op.io/reviewFeed?entityid=${ent_id}";
        }
        else if($ent_type == 'allReviewFeed' && $rev_type == 'multiple') {
            $biz_url = "https://feed.op.io/allReviewFeed?entId=${ent_id}";
        }
        else if($ent_type == 'reviewFeed' && $rev_type == 'multiple') {
            $biz_url = "https://feed.op.io/reviewFeed?entId=${ent_id}";
        }
        else if($ent_type == 'reviewFeed' && $rev_type == 'orgfeed') {
            $biz_url = "https://feed.op.io/multiReviewFeed?orgId=${org_id}&schema_enabled=true&schema_type=Local Business";
        }
        else if($ent_type == 'allReviewFeed' && $rev_type == 'orgfeed') {
            $biz_url = "https://feed.op.io/multiReviewFeed/allReviews?orgId=${org_id}&schema_enabled=true&schema_type=Local Business";
            // url encode the url
            $biz_url = str_replace(" ", "%20", $biz_url);
        }
        else {
            echo esc_html("Invalid entity type");
            return;
        }
        $business_string = wp_remote_get($biz_url);
        if (is_wp_error($business_string)) {
            $error_message = $business_string->get_error_message();
            echo esc_html("Something went wrong: $error_message");
            return;
        }
        if(!$business_string['body']) {
            echo esc_html("Sorry! This entity doesn't have all reviews feed enabled");
            return;
        }
        return $business_string['body'];
    }
}