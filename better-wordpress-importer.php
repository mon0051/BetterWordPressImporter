<?php
/*
Plugin Name: Better Wordpress Importer
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A more robust implementation of the WordPress importer
Version: 1.0a
Author: Andrew Monteith
Author URI: https://plus.google.com/u/0/105936864125151130411/about
License: GPL2
*/
add_action('admin_menu', 'register_admin_menu');
function register_admin_menu()
{
    add_menu_page("Better WordPress Importer", "Better WordPress Importer", 'manage_options',
        'better-wordpress-importer/better-wordpress-importer-admin.php', '');
}
function bwi_scripts()
{
    $bwi_plugin_folder_url = plugins_url() . '/better-wordpress-importer';
    wp_register_script('bigUpload', $bwi_plugin_folder_url . "/JavaScript/bigUpload.js", array('jquery'));
    wp_register_style('bwistylesheet',$bwi_plugin_folder_url . "/css/sass-styles.css",array(),false,false);
    wp_register_style('bwi-font-awesome',$bwi_plugin_folder_url . '/css/font-awesome-4.2.0/css/font-awesome.css');
    wp_enqueue_script('bigUpload');
    wp_enqueue_style('bwistylesheet');
    wp_enqueue_style('bwi-font-awesome');
}
add_action('admin_enqueue_scripts', 'bwi_scripts');