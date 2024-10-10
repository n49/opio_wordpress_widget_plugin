<?php
/*
Plugin Name: Widget for OPIO Reviews
Plugin URI: 
Description: Instantly add OPIO Reviews on your website to increase user confidence and SEO.
Version: 1.0.83
Author: Dhiraj Timalsina <dhiraj@n49.com>
Text Domain: widget-for-opio-reviews
Domain Path: /languages
*/

/*
Another Plugin
Text Domain: slider-widget-for-opio-reviews
Domain Path: /languages
*/

namespace WP_Opio_Reviews;
if (!defined('ABSPATH')) {
    exit;
}

require(ABSPATH . 'wp-includes/version.php');

define('OPIO_PLUGIN_VERSION'   , '1.0.83');
define('OPIO_PLUGIN_FILE'      , __FILE__);
define('OPIO_PLUGIN_URL'       , plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));
define('OPIO_ASSETS_URL'       , OPIO_PLUGIN_URL . '/assets/');

require_once __DIR__ . '/autoloader.php';

/*-------------------------------- Links --------------------------------*/
function opio_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="' . admin_url('admin.php?page=opio-builder') . '">' .
                             '<span style="background-color:#e7711b;color:#fff;font-weight:bold;padding:0px 8px 2px">' .
                                 'Connect Reviews' .
                             '</span>' .
                         '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'WP_Opio_Reviews\\opio_plugin_action_links', 10, 2);

/*-------------------------------- Row Meta --------------------------------*/
function opio_plugin_row_meta($input, $file) {
    if ($file != plugin_basename( __FILE__ )) {
        return $input;
    }

    $links = array(
        '<a href="' . admin_url('admin.php?page=opio-support') . '" target="_blank">' . __('View Documentation', 'widget-for-opio-reviews') . '</a>',
    );
    $input = array_merge($input, $links);
    return $input;
}
add_filter('plugin_row_meta', 'WP_Opio_Reviews\\opio_plugin_row_meta', 10, 2);

/*-------------------------------- Plugin init --------------------------------*/
$opio = new Includes\Plugin();
$opio->register();

?>