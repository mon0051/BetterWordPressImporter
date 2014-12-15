<?php
/**
 * Author: Andrew Monteith
 * Date: 15/12/14 7:13 PM
 */
require_once 'ajax_authenticate.php';
require_once 'parser.php';

/**
 * Class AjaxHandler
 * Decides what to with the Ajax Request
 */
class AjaxHandler
{
    private static $BWIUploadDirectory = "/var/www/html/wordpress/wp-content/uploads/2014/";

    public static function doAction()
    {
        if (isset($_FILES["FileInput"]) && $_FILES["FileInput"]["error"] == UPLOAD_ERR_OK) {
            AjaxHandler::processUpload();
            return;
        } elseif (isset($_GET['action']) && $_GET['action'] == "first_contact") {
            AjaxHandler::check_session();
            return;
        } elseif (isset($_GET['action']) && $_GET['action'] == "delete_session") {
            AjaxHandler::delete_session();
            return;
        } elseif (isset($_GET['action']) && $_GET['action'] == 'parse_xml') {
            AjaxHandler::parse_xml();
        } elseif (isset($_GET['action']) && $_GET['action'] == 'read_authors'){
            AjaxHandler::read_authors();
        }

    }

    private static function processUpload()
    {
        // Validate FileSize ServerSide
        $max_up = ini_get('upload_max_filesize');
        // Sometimes the max upload size is specified in kilobytes or megabytes. The section below will convert the number
        // if this is the case.
        $last_letter = strtolower(substr($max_up, -1));
        if ($last_letter == "m") {
            $max_up = substr($max_up, 0, -1);
            $i_max_up = floatval($max_up);
            $i_max_up = $i_max_up * 1048576;
            $max_up = $i_max_up;
        }
        if ($last_letter == "k") {
            $max_up = substr($max_up, 0, -1);
            $i_max_up = floatval($max_up);
            $i_max_up = $i_max_up * 1024;
            $max_up = $i_max_up;
        }
        if ($_FILES['FileInput']['size'] > $max_up) {
            echo "File to Big";
            die('File to large');
        }
        $File_Name = strtolower($_FILES["FileInput"]["name"]);
        $File_Ext = substr($File_Name, strrpos($File_Name, '.'));
        $Random_Number = rand(0, 999999999);
        $NewFileName = $File_Name . "_" . $Random_Number . $File_Ext;
        if (move_uploaded_file($_FILES['FileInput']['tmp_name'], AjaxHandler::$BWIUploadDirectory . $NewFileName)) {
            echo "<div class=\"ajax-return-value\" id=\"serverside-filename\">" . AjaxHandler::$BWIUploadDirectory . $NewFileName . "</div>";
            die('WordPress Export File Uploaded Successfully');
        } else {
            echo "Failed";
            die('Upload Failed');
        }
    }

    private static function check_session()
    {
        // If the bwi_result exists and is not empty
        if (isset($_SESSION['bwi_results']) && $_SESSION['bwi_results'] != array()) {
            // And if the bwi_result has a file associated with it
            if (isset($_SESSION['bwi_results']['associated_filename'])) {
                // return that filename
                echo '<p>' . $_SESSION['bwi_results']['associated_filename'] . '</p>';
            }
        } else {
            echo "no_session";
        }
    }

    private static function delete_session()
    {
        $_SESSION['bwi_results'] = array();
        echo "session_deleted";
    }

    private static function parse_xml()
    {
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
        $authors = $posts = $terms = $categories = $tags = $results = array();
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
    }

    private static function read_authors(){
        $authors = $_SESSION['bwi_results']['authors'];
        foreach ($authors as $author) {
            echo "<div class=\"author_wrapper\">" . "<div class=\"author_login\">" . $author['author_login']
                . "<input class=\"new_author_name\" type=\"text\" ></div></div>";
        }
    }

}

