<?php
/*
Plugin Name: Better Wordpress Importer
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: mon
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2
*/

// Very basic security measure
/*if(! defined(ABSPATH) ){
    echo "Unauthorized access to WordPress file";
    echo "Please check the link that you have been provided with";
    die();
}*/

add_action('admin_menu', 'register_admin_menu');
function register_admin_menu()
{
    add_menu_page("Better WordPress Importer", "Better WordPress Importer", 'manage_options',
        'better-wordpress-importer/better-wordpress-importer-admin.php', '');
}

function bwi_scripts()
{
    $bwi_plugin_folder_url = plugins_url() . '/better-wordpress-importer';
    wp_register_script('jQueryFormPlugin', $bwi_plugin_folder_url . "/JavaScript/jquery.form.js", array('jquery'));
    wp_register_style('bwistylesheet',$bwi_plugin_folder_url . "/css/sass-styles.css",array(),false,false);
    wp_register_style('bwi-font-awesome',$bwi_plugin_folder_url . '/css/font-awesome-4.2.0/css/font-awesome.css');
    wp_enqueue_script('jQueryFormPlugin');
    wp_enqueue_style('bwistylesheet');
    wp_enqueue_style('bwi-font-awesome');
}

add_action('admin_enqueue_scripts', 'bwi_scripts');