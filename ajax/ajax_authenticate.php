<?php
/**
 * Author: Andrew Monteith
 * Date: 12/12/14 9:42 AM
 */
// Must be called first to ensure that PHP is aware of class types that
// can be saved in the session (this enables automatic serialization and
// de-serialization of those class types)
require_once BWI_BASE_PATH . '/BwiWxrImporter/Parser/BWxrParser.php';
require_once BWI_BASE_PATH . '/BwiWxrImporter/BwiImport.php';
require_once BWI_BASE_PATH . '/BwiWxrImporter/BwiRollback.php';

// This is where we include the wordpress functionality, before this wordpress
// does not "exist"
$wp_bootstrap = WORDPRESS_BOOTSTRAP_PATH;
/** @noinspection PhpIncludeInspection */
require_once $wp_bootstrap;

if (!is_user_logged_in()) {
    die("Must be logged in as admin, ensure that cookies are enabled.");
}
session_start();

if (!isset($_SESSION['bwi_ajax'])) {
    $_SESSION['bwi_ajax'] = array();
}
