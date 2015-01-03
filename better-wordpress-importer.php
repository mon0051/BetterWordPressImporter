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

/**
 * Registers the custom post type bwi_import_log.
 *
 * bwi_rollback_data array contains all the information necessary to undo an import
 * (as well as resume a partially completed one)
 *
 * bwi_import_log details any errors that occurred during an import
 */
// Register Custom Post Type
function bwi_import_log() {

    $labels = array(
        'name'                => _x( 'Logs', 'Post Type General Name', 'better_wordpress_importer' ),
        'singular_name'       => _x( 'Log', 'Post Type Singular Name', 'better_wordpress_importer' ),
        'menu_name'           => __( 'BWI Log', 'better_wordpress_importer' ),
        'parent_item_colon'   => __( 'Parent Log:', 'better_wordpress_importer' ),
        'all_items'           => __( 'All Logs', 'better_wordpress_importer' ),
        'view_item'           => __( 'View Log', 'better_wordpress_importer' ),
        'add_new_item'        => __( 'Add New Log', 'better_wordpress_importer' ),
        'add_new'             => __( 'Add New', 'better_wordpress_importer' ),
        'edit_item'           => __( 'Edit Log', 'better_wordpress_importer' ),
        'update_item'         => __( 'Update Log', 'better_wordpress_importer' ),
        'search_items'        => __( 'Search Log', 'better_wordpress_importer' ),
        'not_found'           => __( 'Not found', 'better_wordpress_importer' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'better_wordpress_importer' ),
    );
    $args = array(
        'label'               => __( 'bwi_import_log', 'better_wordpress_importer' ),
        'description'         => __( 'BWI Import Log', 'better_wordpress_importer' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'revisions', 'custom-fields', ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 60,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'bwi_import_log', $args );

}

// Hook into the 'init' action
add_action( 'init', 'bwi_import_log', 0 );

// Enqueue Scripts
function bwi_scripts()
{
    $bwi_plugin_folder_url = plugins_url() . '/better-wordpress-importer';
    wp_register_script('bigUpload', $bwi_plugin_folder_url . "/PanelSlider/JavaScript/bigUpload.js", array('jquery'));
    wp_register_script('panelSlider', $bwi_plugin_folder_url . "/PanelSlider/JavaScript/PanelSlider.js", array('jquery'));
    wp_register_style('bwistylesheet', $bwi_plugin_folder_url . "/PanelSlider/css/sass-styles.css", array(), false, false);
    wp_register_style('bwi-font-awesome', $bwi_plugin_folder_url . '/PanelSlider/css/font-awesome-4.2.0/css/font-awesome.css');
    wp_enqueue_script('panelSlider');
    wp_enqueue_script('bigUpload');
    wp_enqueue_style('bwistylesheet');
    wp_enqueue_style('bwi-font-awesome');
}

add_action('admin_enqueue_scripts', 'bwi_scripts');
add_action('init', 'register_bwi_custom_post_types');