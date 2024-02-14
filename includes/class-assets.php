<?php

namespace WP_Opio_Reviews\Includes;

class Assets {

    private $url;
    private $version;
    private $debug;

    private static $css_assets = array(
        'opio-admin-main-css'   => 'css/admin-main',
        'opio-public-clean-css' => 'css/public-clean',
        'opio-public-main-css'  => 'css/public-main',
        'opio-feed-css'  => 'css/public-feed'
    );

    private static $js_assets = array(
        'opio-main-js'    => 'js/opio-main',
        'moment-js'            => 'js/moment.min'
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

        // load robonto font from fonts fonts/roboto.css
        wp_enqueue_style('roboto-font', $this->url . 'fonts/roboto.css', array(), $this->version);
        
    }

    public function register_styles() {
        $styles = array('opio-admin-main-css', 'opio-public-main-css', 'opio-feed-css');
        $this->register_styles_loop($styles);
    }

    public function register_scripts() {
        $scripts = array('opio-main-js', 'moment-js');
        $this->register_scripts_loop($scripts);
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style('wp-jquery-ui-dialog');
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