<?php
/**
 * Author: Andrew Monteith
 * Date: 11/12/14 1:07 PM
 */
//require_once 'required_wordpress_code.php';
require_once 'ajax_authenticate.php';
if(! isset($_GET['filename'])){
    die("File Error");
}
require_once 'parser.php';
//require_once '/var/www/html/wordpress/wp-admin/includes/import.php';
$working_path = ABSPATH . 'wp-content/plugins/better-wordpress-importer/backup/' ;
$filename =  $_GET['filename'];
$authors = $posts = $terms = $categories = $tags = array();
$results = array();
$ajax_parser = new WXR_Parser();
if ( ! is_file($filename) ) {
    echo $filename;
    die(" file error");
}
try {
    $results = $ajax_parser->parse($filename);
    $_SESSION['bwi_results'] = $results;
    $_SESSION['bwi_results']['associated_filename'] = $_GET['filename'];
    $authors = $results['authors'];
    foreach($authors as $author){
        echo "<div class=\"author_wrapper\">"."<div class=\"author_login\">".$author['author_login']."</div></div>";
    }




}catch (Exception $e){
    echo $e->getMessage();
}
