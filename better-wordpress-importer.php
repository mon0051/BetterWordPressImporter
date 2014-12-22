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
require_once 'bwi_helpers.php';
add_action('admin_menu', 'register_admin_menu');
function register_admin_menu()
{
    add_menu_page("Better WordPress Importer", "Better WordPress Importer", 'manage_options',
        'better-wordpress-importer/better-wordpress-importer-admin.php', '');
}

/**
 * Registers the custom post types bwi_rollback_data and bwi_import_log.
 *
 * bwi_rollback_data array contains all the information necessary to undo an import
 * (as well as resume a partially completed one)
 *
 * bwi_import_log details any errors that occured during an import
 */
function register_bwi_custom_post_types(){
    $rollback_cpt_args = array(
        'labels'=> array(
            'name' => 'rollback_dataset',
            'menu_name' => 'BWI Backup Data'
        ),
        'description' => 'A log to enable the resumption and rollback of bwi imports',
        'show_ui' => 'true',
        'show_in_nav_menus' => 'true',
        'show_in_menu' => 'true',
        'show_in_admin_bar' => 'true',
    );
   register_post_type("bwi_rollback_data",$rollback_cpt_args);
    $bwi_cpt_log_args = array(
        'labels'=> array(
            'name' => 'rollback_dataset',
            'menu_name' => 'BWI Backup Data'
        ),
        'description' => 'A log to enable the resumption and rollback of bwi imports',
        'show_ui' => 'true',
        'show_in_nav_menus' => 'true',
        'show_in_menu' => 'true',
        'show_in_admin_bar' => 'true',
    );
    register_post_type("bwi_import_log",$bwi_cpt_log_args);
}
// Enqueue Scripts
function bwi_scripts()
{
    $bwi_plugin_folder_url = plugins_url() . '/better-wordpress-importer';
    wp_register_script('bigUpload', $bwi_plugin_folder_url . "/PanelSlider/JavaScript/bigUpload.js", array('jquery'));
    wp_register_script('panelSlider', $bwi_plugin_folder_url . "/PanelSlider/JavaScript/PanelSlider.js", array('jquery'));
    wp_register_script('authorBuilder', $bwi_plugin_folder_url . "/PanelSlider/JavaScript/authorBuilder.js", array('jquery'));
    wp_register_style('bwistylesheet',$bwi_plugin_folder_url . "/PanelSlider/css/sass-styles.css",array(),false,false);
    wp_register_style('bwi-font-awesome',$bwi_plugin_folder_url . '/PanelSlider/css/font-awesome-4.2.0/css/font-awesome.css');
    wp_enqueue_script('panelSlider');
    wp_enqueue_script('bigUpload');
    wp_enqueue_script('authorBuilder');
    wp_enqueue_style('bwistylesheet');
    wp_enqueue_style('bwi-font-awesome');
}
add_action('admin_enqueue_scripts', 'bwi_scripts');
add_action('init','register_bwi_custom_post_types');