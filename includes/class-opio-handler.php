<?php

namespace WP_Opio_Reviews\Includes;

class Opio_Handler {

    public function __construct($entity_id, $entity_type) {
        $this->entity_id = $entity_id;
        $this->entity_type = $entity_type;
    }

    public function get_business() {
        $ent_id = $this->entity_id;
        $ent_type = $this->entity_type;
        if($ent_type == 'allReviewFeed') {
            $biz_url = "http://34.225.94.59/allReviewFeed?entId=${ent_id}&html=true";
            $business_string = wp_remote_get($biz_url);
        }
        else {
            $biz_url = "http://34.225.94.59/reviewFeed?entityid=${ent_id}&html=true";
            $business_string = wp_remote_get($biz_url);
        }
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