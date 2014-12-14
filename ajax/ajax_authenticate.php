<?php
/**
 * Author: Andrew Monteith
 * Date: 12/12/14 9:42 AM
 */
$wp_bootstrap = dirname(__FILE__) . '../../../../../wp-load.php';
//echo $wp_bootstrap;
/** @noinspection PhpIncludeInspection */
require_once $wp_bootstrap;
if( ! is_user_logged_in() ) {
    die("Must be logged in as admin to use this AJAX, ensure that cookies are enabled.");
}
session_start();
if(! isset($_SESSION['bwi_ajax'])){
    $_SESSION['bwi_ajax'] = array();
}
