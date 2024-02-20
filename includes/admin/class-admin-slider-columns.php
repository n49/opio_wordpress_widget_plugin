<?php

namespace WP_Opio_Reviews\Includes\Admin;

use WP_Opio_Reviews\Includes\Post_Types;

class Admin_Slider_Columns {

    private static $plugin_themes = array(
        'list'   => 'List',
        'slider' => 'Slider',
    );

    public function __construct($slider_deserializer) {
        $this->slider_deserializer = $slider_deserializer;
    }

    public function register() {
        add_filter('get_edit_post_link', array($this, 'change_edit_post_link'), 10, 3);
        add_filter('manage_edit-' . Post_Types::SLIDER_POST_TYPE . '_columns', array($this, 'get_columns'));
        add_action('manage_' . Post_Types::SLIDER_POST_TYPE . '_posts_custom_column', array($this, 'render'), 10, 2);
        add_filter('post_row_actions', array($this, 'change_post_row_actions'), 10, 2);
        add_filter('get_the_excerpt', array($this, 'hide_opio_feed_excerpt'), 10, 2);
    }

    public function change_edit_post_link($link, $id, $context) {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if (empty($screen) || $screen->post_type !== Post_Types::SLIDER_POST_TYPE) {
                return $link;
            }
            return admin_url('admin.php?page=opio-slider&' . Post_Types::SLIDER_POST_TYPE . '_id=' . $id);
        } else {
            return;
        }
    }

    public function get_columns($columns) {
        $columns = $columns;
        $columns = array(
            'cb'        => '<input type="checkbox">',
            'title'     => __('Title', 'slider-widget-for-opio-reviews'),
            'ID'        => __('ID',    'slider-widget-for-opio-reviews'),
            'opio_theme' => __('Review Type', 'slider-widget-for-opio-reviews'),
            'opio_status' => __('Status', 'slider-widget-for-opio-reviews'),
            'date'      => __('Date',  'slider-widget-for-opio-reviews'),
        );
        return $columns;
    }

    public function render($column_name, $post_id) {
        $args = array();

        if (isset($_GET['post_status'])) {
            $post_status = sanitize_text_field(wp_unslash($_GET['post_status']));

            if ($post_status === 'trash') {
                $args['post_status'] = array('trash');
            }
        }

        $feed = $this->slider_deserializer->get_feed($post_id, $args);
        if (!$feed) {
            return null;
        }

        $connection = json_decode($feed->post_content);
        // dd($connection);
        switch ($column_name) {
            case 'ID':
                echo esc_attr($feed->ID);
                break;
            case 'opio_theme':
                echo esc_attr(isset($connection->review_type) ? $connection->review_type : 'Single');
                break;
            case 'opio_status':
                echo esc_attr($connection->review_enabled == 'yes' ? 'Working' : 'Not Working');
                break;
        }
    }

    public function change_post_row_actions($actions, $post) {
        if (isset($actions) && $post->post_type === Post_Types::SLIDER_POST_TYPE) {
            $changed_actions = array(
                'post-id' => '<span class="opio-admin-column-action">ID: ' . $post->ID . '</span>',
            );
            $actions = $changed_actions + $actions;
        }
        return $actions;
    }

    public function hide_opio_feed_excerpt($excerpt, $post = null) {
        return $post->post_type !== Post_Types::SLIDER_POST_TYPE ? $excerpt : '';
    }
}
