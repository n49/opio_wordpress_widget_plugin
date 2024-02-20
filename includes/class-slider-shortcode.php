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
        if (get_option('opio_active') === '0') {
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