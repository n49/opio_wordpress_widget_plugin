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

    // Shortcode tags whose presence on a page means the public bundle is needed.
    private static $shortcode_tags = array(
        'opio_slider', 'opio_feed', 'opio_feed_optimized', 'opio_feed_french',
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
        // Frontend: only load the bundle when a page actually uses an OPIO
        // shortcode. The plugin's Slick build registers jQuery.fn.slick
        // globally; loading it everywhere overwrites the theme's own Slick and
        // breaks unrelated theme sliders (see craft-bilt product galleries).
        add_action('wp_enqueue_scripts', array($this, 'maybe_enqueue_public_assets'));
        // Render-time fallback for shortcodes placed outside post_content
        // (widgets, page builders, do_shortcode in templates). Enqueues late;
        // scripts/styles print in the footer.
        add_action('opio_enqueue_assets', array($this, 'enqueue_public_assets'));

        // load Roboto font from fonts fonts/roboto.css
    }

    public function register_styles() {
        $this->register_styles_loop(array_keys(self::$css_assets));
        wp_register_style('roboto-font', $this->url . 'fonts/roboto.css', array(), $this->version);
        // Admin loads the full bundle unconditionally (preview rendering).
        if (is_admin()) {
            $this->enqueue_public_styles();
        }
    }

    public function register_scripts() {
        $this->register_scripts_loop(array_keys(self::$js_assets));
        if (is_admin()) {
            $this->enqueue_public_scripts();
        }
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

    // Enqueue the public bundle when the queried post contains an OPIO
    // shortcode. Runs on wp_enqueue_scripts so assets land in <head>.
    public function maybe_enqueue_public_assets() {
        if ($this->current_page_has_opio_shortcode()) {
            $this->enqueue_public_assets();
        }
    }

    public function enqueue_public_assets() {
        $this->enqueue_public_styles();
        $this->enqueue_public_scripts();
    }

    private function enqueue_public_styles() {
        $styles = array(
            'opio-admin-main-css', 'opio-public-main-css', 'opio-feed-css',
            'opio-slick-theme-css', 'opio-slick-min-css', 'roboto-font',
        );
        foreach ($styles as $style) {
            wp_enqueue_style($style);
        }
    }

    private function enqueue_public_scripts() {
        $scripts = array('opio-main-js', 'moment-opio-js', 'slick-opio-carousel-js', 'opio-slider-main-js');
        if (!wp_script_is('jquery', 'enqueued')) {
            array_unshift($scripts, 'jQuery-opio-3.6.0-js');
        }
        foreach ($scripts as $script) {
            wp_enqueue_script($script);
        }
    }

    private function current_page_has_opio_shortcode() {
        if (is_admin()) {
            return false;
        }
        global $post;
        if (!($post instanceof \WP_Post)) {
            return false;
        }
        foreach (self::$shortcode_tags as $tag) {
            if (has_shortcode($post->post_content, $tag)) {
                return true;
            }
        }
        return false;
    }

    private function register_styles_loop($styles) {
        foreach ($styles as $style) {
            wp_register_style($style, $this->get_css_asset($style), array(), $this->version);
        }
    }

    private function register_scripts_loop($scripts) {
        foreach ($scripts as $script) {
            wp_register_script($script, $this->get_js_asset($script), array(), $this->version);
        }
    }

    private function get_css_asset($asset) {
        return $this->url . ($this->debug ? 'src/' : '') . self::$css_assets[$asset] . '.css';
    }

    private function get_js_asset($asset) {
        return $this->url . ($this->debug ? 'src/' : '') . self::$js_assets[$asset] . '.js';
    }

}
