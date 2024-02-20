<?php

namespace WP_Opio_Reviews\Includes\Admin;

use WP_Opio_Reviews\Includes\Post_Types;

class Admin_Menu {

    public function __construct() {
    }

    public function register() {
        add_action('admin_menu', array($this, 'add_page'));
        add_action('admin_menu', array($this, 'add_subpages'));
        add_filter('submenu_file', array($this, 'remove_submenu_pages'));
        add_filter('admin_body_class', array($this, 'add_admin_body_class'));
    }

    public function add_page() {
        add_menu_page(
            'OPIO Reviews Plugin',
            'OPIO Reviews',
            'edit_posts',
            'opio',
            '',
            OPIO_ASSETS_URL . 'img/logo.jpeg',
            90
        );
    }

    public function add_subpages() {
        $builder_page = new Admin_Page(
            'opio',
            'Reviews Builder',
            'Builder',
            'edit_posts',
            'opio-builder'
        );
        $builder_page->add_page();

        $slider_page = new Admin_Page(
            'opio',
            'Reviews Builder',
            'Review Slider',
            'edit_posts',
            'opio-slider'
        );
        $slider_page->add_page();

        $setting_page = new Admin_Page(
            'opio',
            'Support',
            'Support',
            'manage_options',
            'opio-support'
        );
        $setting_page->add_page();
    }

    public function remove_submenu_pages($submenu_file) {
        global $plugin_page;

        $hidden_pages = array(
            'opio-builder',
            'opio-slider'
        );

        if ($plugin_page && in_array($plugin_page, $hidden_pages)) {
            // $submenu_file = 'edit.php?post_type=' . Post_Types::SLIDER_POST_TYPE;
            // $submenu_file = 'edit.php?post_type=' . Post_Types::FEED_POST_TYPE;
            $submenu_file = 'edit.php?post_type=' . ($plugin_page === 'opio-slider' ? Post_Types::SLIDER_POST_TYPE : Post_Types::FEED_POST_TYPE);
        }

        foreach ($hidden_pages as $page) {
            remove_submenu_page('opio', $page);
        }

        return $submenu_file;
    }
    
    public function add_admin_body_class($classes) {
        $current_screen = get_current_screen();

        if (empty($current_screen)) {
            return;
        }

        if (strpos($current_screen->id, 'opio') !== false) {
            $classes .= ' opio-admin ';
        }
        return $classes;
    }

}
