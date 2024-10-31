<?php
/**
 * Plugin Name: Restrict Content for WP Bakery
 * Description: An extension for Visual Composer that restrict the content or block based on user role and display message for restricted role as well.
 * Author: Mohan Karamchandani
 * Version: 1.0.0
 * Author URI: https://github.com/mohan9a
 * Text Domain: wpb-restrict-content
 */
// If this file is called directly, abort

if (!defined('ABSPATH')) {
    die('Silly human what are you doing here');
}

// Before VC Init
function wpb_rc_vc_before_init_actions()
{
    // Require new custom Element
    include plugin_dir_path(__FILE__) . 'vc-restrict-content-element.php';
}
add_action('vc_before_init', 'wpb_rc_vc_before_init_actions');

// Link directory stylesheet
function wpc_restrict_content_scripts()
{
    wp_enqueue_style('wpb_rc_community_directory_stylesheet', plugin_dir_url(__FILE__) . 'styling/wpb-restrict-content.css');
}
add_action('wp_enqueue_scripts', 'wpc_restrict_content_scripts');
