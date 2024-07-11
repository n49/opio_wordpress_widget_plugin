<?php

namespace WP_Opio_Reviews\Includes;

class Assets {

    private $url;
    private $version;
    private $debug;

    private static $css_assets = array(
        'opio-slick-theme-css' => 'css/slick-theme.min',
        'opio-slick-min-css' => 'css/slick-min',
        'opio-admin-main-css'   => 'css/admin-main',
        'opio-public-clean-css' => 'css/public-clean',
        'opio-public-main-css'  => 'css/public-main',
        'opio-feed-css'  => 'css/public-feed'
    );

    private static $js_assets = array(
        'opio-main-js'    => 'js/opio-main',
        'moment-opio-js'            => 'js/moment-opio.min',
        'jQuery-opio-3.6.0-js'            => 'js/jquery-opio-3.6.0.min',
        'slick-opio-carousel-js'            => 'js/slick-opio-carousel.min',
        'opio-slider-main-js'    => 'js/opio-slider-main',
    );

    public function __construct($url, $version, $debug) {
        $this->url     = $url;
        $this->version = $version;
        $this->debug  = $debug;
    }

    public function register() {
        add_action('admin_enqueue_scripts', array($this, 'register_styles'));
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'register_styles'));
        add_action('wp_enqueue_scripts', array($this, 'register_scripts'));

        // load Roboto font from fonts fonts/roboto.css
    }

    public function register_styles() {
        $styles = array('opio-admin-main-css', 'opio-public-main-css', 'opio-feed-css', 'opio-slick-theme-css', 'opio-slick-min-css');
        $this->register_styles_loop($styles);
        wp_enqueue_style('roboto-font', $this->url . 'fonts/roboto.css', array(), $this->version);    
    }

    public function register_scripts() {
        $scripts = array('opio-main-js', 'moment-opio-js', 'slick-opio-carousel-js', 'opio-slider-main-js');
        if (!wp_script_is('jquery', 'enqueued')) {
            $scripts = array('opio-main-js', 'moment-opio-js', 'jQuery-opio-3.6.0-js', 'slick-opio-carousel-js', 'opio-slider-main-js');
        }
        $this->register_scripts_loop($scripts);
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style('roboto-font', $this->url . 'fonts/roboto.css', array(), $this->version);
        wp_enqueue_style('opio-slick-theme-css');
        wp_enqueue_style('opio-slick-min-css');
        wp_enqueue_style('opio-admin-main-css');
        wp_enqueue_style('opio-public-main-css');

    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('opio-main-js');

    }
    
    private function register_styles_loop($styles) {
        foreach ($styles as $style) {
            wp_register_style($style, $this->get_css_asset($style), array(), $this->version);
            wp_enqueue_style($style);
        }
    }

    private function register_scripts_loop($scripts) {
        foreach ($scripts as $script) {
            wp_register_script($script, $this->get_js_asset($script), array(), $this->version);
            wp_enqueue_script($script);
        }
    }

    private function get_css_asset($asset) {
        return $this->url . ($this->debug ? 'src/' : '') . self::$css_assets[$asset] . '.css';
    }

    private function get_js_asset($asset) {
        return $this->url . ($this->debug ? 'src/' : '') . self::$js_assets[$asset] . '.js';
    }

}