<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Core\Core;

class Slider_Shortcode {

    public function __construct(Slider_Deserializer $slider_deserializer) {
        $this->slider_deserializer = $slider_deserializer;
    }

    function custom_esc($str) {
        return ($str);
    }

    public function register() {
        add_shortcode('opio_slider', array($this, 'init'));
    }

    public function init($atts) {

        ob_start();
        if (get_option('grw_active') === '0') {
            return '';
        }
        $feed = $this->slider_deserializer->get_feed($atts['id']);
        $feed_id = '';
        $loaded_bizs = '';
        $business_name = '';
        $biz_id = '';
        $org_id = '';
        $review_feed_link = '';
        $feed_inited = false;
        $businesses = null;
        $reviews = null;
        $feed_object = json_decode($feed->post_content);

        if ($feed != null) {
            $slider_type = $feed_object->slider_type;
            $review_feed_link = $feed_object->review_feed_link;
        }

        $slider_type = $feed_object->slider_type;
        $review_feed_link = $feed_object->review_feed_link;

        if($slider_type == 'horizontal') {
            include_once 'reviews-slider-horizontal-template.php'; 
        } else if($slider_type == 'horizontal-carousel') {
            include_once 'reviews-slider-horizontal-carousel-template.php'; 
        } else if($slider_type == 'vertical') {
            include_once 'reviews-slider-vertical-template.php'; 
        }
        // Include the template file
        // include_once('reviews-slider-vertical-template.php');
    
        return ob_get_clean();

    }
}
//     public function init($atts) {
//         if (get_option('grw_active') === '0') {
//             return '';
//         }
//         ob_start();
//         $feed = $this->slider_deserializer->get_feed($atts['id']);
//         $feed_id = '';
//         $loaded_bizs = '';
//         $business_name = '';
//         $biz_id = '';
//         $org_id = '';
//         $feed_inited = false;
//         $businesses = null;
//         $reviews = null;
//         $feed_object = json_decode($feed->post_content);

//         if ($feed != null) {
//             $feed_id = $feed->ID;
//             $loaded_bizs_raw = $feed_object->loaded_bizs;
//             $loaded_bizs = json_encode($feed_object->loaded_bizs);
//             $business_name = $feed->post_title;
//             $biz_id = ($feed_object->biz_id);
//             $org_id = $feed_object->org_id;
//             $biz_org_id = $feed_object->biz_org_id;
//             $review_type = $feed_object->review_type;
//             $review_option = $feed_object->review_option;
//             if (isset($biz_id) || isset($org_id)) {

//                 $valid = preg_match('/^[a-zA-Z0-9]{15,20}$/', $biz_id);
//                 $valid = $valid || preg_match('/^[a-zA-Z0-9]{15,20}$/', $org_id);
//                 if ($valid) {
//                     $feed_inited = true;
//                 }
//             }

//             $option = $review_option == "opio" ? "reviewFeed" : "allReviewFeed";
//             $opio_handler = new Opio_Handler($biz_id, $option);
//             $reviews = $opio_handler->get_business();
//             $reviews = $this->slider_deserializer->prepareString($reviews);
//             echo wp_kses($reviews, $this->slider_deserializer->get_allowed_tags());
//             return ob_get_clean();
//         }
//     }
// }
