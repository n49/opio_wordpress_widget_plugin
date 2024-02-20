<?php

namespace WP_Opio_Reviews\Includes;

use WP_Opio_Reviews\Includes\Admin\Admin_Menu;
use WP_Opio_Reviews\Includes\Admin\Admin_Tophead;
use WP_Opio_Reviews\Includes\Admin\Admin_Notice;
use WP_Opio_Reviews\Includes\Admin\Admin_Feed_Columns;
use WP_Opio_Reviews\Includes\Admin\Admin_Slider_Columns;

final class Plugin {

    protected $name;
    protected $version;
    protected $activator;

    public function __construct() {
        
        $this->name = 'widget-for-opio-reviews';
        $this->version = OPIO_PLUGIN_VERSION;
        add_action('rest_api_init', array($this, 'init_rest_endpoint'));
    }

    public function register() {
        add_action('plugins_loaded', array($this, 'register_services'));
    }

    public function init_rest_endpoint() {
        register_rest_route('opioreviews/v1', '/get_businesses', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_businesses']
        ));
    }
        
        public function get_businesses($data) {
            function filter_opio_response($res) {
                $filtered = [];
        
                $bids = (isset($res->landingPageBusinesses) || isset($res->businessIds)) ? $res->landingPageBusinesses ?? $res->businessIds : null;
                $entities = $res->entities;
        
                // filter entities which has the above business ids
                foreach($entities as $entity) {
                    if(!$bids) {
                        $filteredArray = array(
                            '_id' => $entity->_id,
                            'name' => $entity->name,
                    );
                        $filtered[] = $filteredArray;
                        continue;
                    }
                    if(in_array($entity->_id, explode(',', $bids))) {
                        $filteredArray = array(
                            '_id' => $entity->_id,
                            'name' => $entity->name,
                    );
                        $filtered[] = $filteredArray;
                    }
                }
        
                return $filtered;
        
             }
                $opioRemoteUrl = "https://op.io/api/organizations/landingpageUsername?orgID=".$data['orgID'];
                $response = wp_remote_get($opioRemoteUrl)['body'];
                $err = wp_remote_retrieve_response_code($response);

                 if ($err) {
                 return "Remote Error #:" . $err;
                 } else {
                 return filter_opio_response(json_decode($response)[0], false);
                 }
                 
             }

    public function register_services() {
  
        $assets = new Assets(OPIO_ASSETS_URL, $this->version, get_option('opio_debug_mode') == '1');
        $assets->register();

        $post_types = new Post_Types();
        $post_types->register();
        
        $feed_deserializer = new Feed_Deserializer(new \WP_Query());
        
        $feed_page = new Feed_Page($feed_deserializer);
        $feed_page->register();

        $builder_page = new Builder_Page($feed_deserializer);
        $builder_page->register();

        $slider_deserializer = new Slider_Deserializer(new \WP_Query());

        $slider_feed_page = new Slider_Feed_Page($slider_deserializer);
        $slider_feed_page->register();

        $review_slider = new Review_Slider($slider_deserializer);
        $review_slider->register();

        $feed_shortcode = new Feed_Shortcode($feed_deserializer);
        $feed_shortcode->register();

        $slider_shortcode = new Slider_Shortcode($slider_deserializer);
        $slider_shortcode->register();

        if (is_admin()) {
            $feed_serializer = new Feed_Serializer();
            $slider_serializer = new Slider_Serializer();
            
            $admin_notice = new Admin_Notice();
            $admin_notice->register();

            $admin_menu = new Admin_Menu();
            $admin_menu->register();

            $admin_tophead = new Admin_Tophead();
            $admin_tophead->register();

            $admin_feed_columns = new Admin_Feed_Columns($feed_deserializer);
            $admin_feed_columns->register();

            $admin_slider_columns = new Admin_Slider_Columns($slider_deserializer);
            $admin_slider_columns->register();

            $plugin_support = new Plugin_Support();
            $plugin_support->register();

        }

    }

}