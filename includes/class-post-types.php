<?php

namespace WP_Opio_Reviews\Includes;

class Post_Types {

    const FEED_POST_TYPE = 'opio_feed';
    const SLIDER_POST_TYPE = 'opio_slider';

    public function register() {
        add_action('init', array($this, 'register_post_types'));
    }

    public function register_post_types() {
        $this->register_feed_post_type();
        $this->register_slider_post_type();
    }

    public function register_feed_post_type() {
        $labels = array(
            'name'                  => _x('Reviews widgets', 'Post Type General Name', 'widget-for-opio-reviews'),
            'singular_name'         => _x('Reviews widget', 'Post Type Singular Name', 'widget-for-opio-reviews'),
            'menu_name'             => __('Reviews widgets', 'widget-for-opio-reviews'),
            'name_admin_bar'        => __('Reviews widget', 'widget-for-opio-reviews'),
            'archives'              => __('Reviews Feed Archives', 'widget-for-opio-reviews'),
            'attributes'            => __('Reviews Feed Attributes', 'widget-for-opio-reviews'),
            'parent_item_colon'     => __('Parent Reviews Feed:', 'widget-for-opio-reviews'),
            'all_items'             => __('Review feed', 'widget-for-opio-reviews'),
            'add_new_item'          => __('Add New Reviews Feed', 'widget-for-opio-reviews'),
            'add_new'               => __('Add Reviews Feed', 'widget-for-opio-reviews'),
            'new_item'              => __('New Reviews Feed', 'widget-for-opio-reviews'),
            'edit_item'             => __('Edit Reviews Feed', 'widget-for-opio-reviews'),
            'update_item'           => __('Update Reviews Feed', 'widget-for-opio-reviews'),
            'view_item'             => __('View Reviews Feed', 'widget-for-opio-reviews'),
            'view_items'            => __('View Reviews Feeds', 'widget-for-opio-reviews'),
            'search_items'          => __('Search Reviews Widgets', 'widget-for-opio-reviews'),
            'not_found'             => __('Not found', 'widget-for-opio-reviews'),
            'not_found_in_trash'    => __('Not found in Trash', 'widget-for-opio-reviews'),
            'featured_image'        => __('Featured Image', 'widget-for-opio-reviews'),
            'set_featured_image'    => __('Set featured image', 'widget-for-opio-reviews'),
            'remove_featured_image' => __('Remove featured image', 'widget-for-opio-reviews'),
            'use_featured_image'    => __('Use as featured image', 'widget-for-opio-reviews'),
            'insert_into_item'      => __('Insert into item', 'widget-for-opio-reviews'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'widget-for-opio-reviews'),
            'items_list'            => __('Reviews Feeds list', 'widget-for-opio-reviews'),
            'items_list_navigation' => __('Reviews Feeds list navigation', 'widget-for-opio-reviews'),
            'filter_items_list'     => __('Filter items list', 'widget-for-opio-reviews'),
        );

        $args = array(
            'label'               => __('Reviews Feed', 'widget-for-opio-reviews'),
            'labels'              => $labels,
            'supports'            => array('title'),
            'taxonomies'          => array(),
            'hierarchical'        => false,
            'public'              => false,
            'show_in_rest'        => false,
            'show_ui'             => true,
            'show_in_menu'        => 'opio',
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capabilities'        => array('create_posts' => 'do_not_allow'),
            'map_meta_cap'        => true,
        );

        register_post_type(self::FEED_POST_TYPE, $args);
    }

    public function register_slider_post_type() {
        $labels = array(
            'name'                  => _x('Reviews widgets', 'Slider Post Type General Name', 'slider-widget-for-opio-reviews'),
            'singular_name'         => _x('Reviews widget', 'Slider Post Type Singular Name', 'slider-widget-for-opio-reviews'),
            'menu_name'             => __('Reviews widgets', 'slider-widget-for-opio-reviews'),
            'name_admin_bar'        => __('Reviews widget', 'slider-widget-for-opio-reviews'),
            'archives'              => __('Reviews Feed Archives', 'slider-widget-for-opio-reviews'),
            'attributes'            => __('Reviews Feed Attributes', 'slider-widget-for-opio-reviews'),
            'parent_item_colon'     => __('Parent Reviews Feed:', 'slider-widget-for-opio-reviews'),
            'all_items'             => __('Review Slider', 'slider-widget-for-opio-reviews'),
            'add_new_item'          => __('Add New Reviews Feed', 'slider-widget-for-opio-reviews'),
            'add_new'               => __('Add Reviews Feed', 'slider-widget-for-opio-reviews'),
            'new_item'              => __('New Reviews Feed', 'slider-widget-for-opio-reviews'),
            'edit_item'             => __('Edit Reviews Feed', 'slider-widget-for-opio-reviews'),
            'update_item'           => __('Update Reviews Feed', 'slider-widget-for-opio-reviews'),
            'view_item'             => __('View Reviews Feed', 'slider-widget-for-opio-reviews'),
            'view_items'            => __('View Reviews Feeds', 'slider-widget-for-opio-reviews'),
            'search_items'          => __('Search Reviews Widgets', 'slider-widget-for-opio-reviews'),
            'not_found'             => __('Not found', 'slider-widget-for-opio-reviews'),
            'not_found_in_trash'    => __('Not found in Trash', 'slider-widget-for-opio-reviews'),
            'featured_image'        => __('Featured Image', 'slider-widget-for-opio-reviews'),
            'set_featured_image'    => __('Set featured image', 'slider-widget-for-opio-reviews'),
            'remove_featured_image' => __('Remove featured image', 'slider-widget-for-opio-reviews'),
            'use_featured_image'    => __('Use as featured image', 'slider-widget-for-opio-reviews'),
            'insert_into_item'      => __('Insert into item', 'slider-widget-for-opio-reviews'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'slider-widget-for-opio-reviews'),
            'items_list'            => __('Reviews Feeds list', 'slider-widget-for-opio-reviews'),
            'items_list_navigation' => __('Reviews Feeds list navigation', 'slider-widget-for-opio-reviews'),
            'filter_items_list'     => __('Filter items list', 'slider-widget-for-opio-reviews'),
        );

        $args = array(
            'label'               => __('Review Slider', 'slider-widget-for-opio-reviews'),
            'labels'              => $labels,
            'supports'            => array('title'),
            'taxonomies'          => array(),
            'hierarchical'        => false,
            'public'              => false,
            'show_in_rest'        => false,
            'show_ui'             => true,
            'show_in_menu'        => 'opio',
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capabilities'        => array('create_posts' => 'do_not_allow'),
            'map_meta_cap'        => true,
        );

        register_post_type(self::SLIDER_POST_TYPE, $args);
    }
}
