<?php
/**
 * Author: Andrew Monteith
 * Date: 11/12/14 1:07 PM
 */
require_once 'ajax_authenticate.php';
if (!isset($_GET['filename'])) {
    die("File Error");
}
// parser.php is the same parser used by the official WordPress Importer, with some
// improvements to the robustness of the code.
require_once 'parser.php';
/*
 *   The working path is where we will store the data of in-progress imports
 *   It would be extreamly inefficient to add this data to the database every
 *   time it is updated, so it will only be added once compleated.
 */
$working_path = ABSPATH . 'wp-content/plugins/better-wordpress-importer/backup/';
// filename is an absolute path to the location of the xml file uploaded
$filename = $_GET['filename'];
// Initialise arrays to store data
$authors = $posts = $terms = $categories = $tags = $results =array();



// Create the WXR_Parser object that will handle the file, die() if file not valid
$ajax_parser = new WXR_Parser();
if (!is_file($filename)) {
    echo $filename;
    die("File Error");
}
try {
    $results = $ajax_parser->parse($filename);
    // Save results to SESSION variable that will be available to future AJAX requests
    $_SESSION['bwi_results'] = $results;
    $_SESSION['bwi_results']['associated_filename'] = $_GET['filename'];
    $authors = $results['authors'];
    foreach ($authors as $author) {
        echo "<div class=\"author_wrapper\">" . "<div class=\"author_login\">" . $author['author_login'] . "</div></div>";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}