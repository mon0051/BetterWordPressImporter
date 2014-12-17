<?php
/**
 * Author: Andrew Monteith
 * Date: 15/12/14 7:13 PM
 */
require_once 'ajax_authenticate.php';
require_once 'parser.php';
require_once 'BigUpload.php';

/**
 * Class AjaxHandler
 * Decides what to with the Ajax Request
 */
class AjaxHandler
{
    public static function doAction()
    {
        if (isset($_GET['action'])){
            switch($_GET['action']){
                case "first_contact":
                    AjaxHandler::check_session();
                    break;
                case "delete_session":
                    AjaxHandler::delete_session();
                    break;
                case "parse_xml":
                    AjaxHandler::parse_xml();
                    break;
                case "read_authors":
                    AjaxHandler::read_authors();
                    break;
                case "upload":
                    AjaxHandler::bigUpload();
                    break;
                case "abort":
                    AjaxHandler::bigUploadAbort();
                    break;
                case "finish":
                    AjaxHandler::bigUploadFinish();
                    break;
                case 'post-unsupported':
                    AjaxHandler::bigUploadUnsupported();
                    break;
            }
        }
    }
    private static function bigUpload(){
        $bigUpload = new BigUpload;
        $tempName = null;
        if(isset($_GET['key'])) {
            $tempName = $_GET['key'];
        }
        if(isset($_POST['key'])) {
            $tempName = $_POST['key'];
        }
        $bigUpload->setTempName($tempName);
        print $bigUpload->uploadFile();
    }
    private static function bigUploadFinish(){
        $bigUpload = new BigUpload;
        $tempName = null;
        if(isset($_GET['key'])) {
            $tempName = $_GET['key'];
        }
        if(isset($_POST['key'])) {
            $tempName = $_POST['key'];
        }
        $bigUpload->setTempName($tempName);
        $_SESSION['bwi-uploadFilename'] = $_POST['name'];
        print $bigUpload->finishUpload($_POST['name']);

    }
    private static function bigUploadUnsupported(){
        $bigUpload = new BigUpload;
        $tempName = null;
        if(isset($_GET['key'])) {
            $tempName = $_GET['key'];
        }
        if(isset($_POST['key'])) {
            $tempName = $_POST['key'];
        }
        $bigUpload->setTempName($tempName);
        print $bigUpload->postUnsupported();
    }
    private static function bigUploadAbort(){
        //Instantiate the class
        $bigUpload = new BigUpload;
        $tempName = null;
        if(isset($_GET['key'])) {
            $tempName = $_GET['key'];
        }
        if(isset($_POST['key'])) {
            $tempName = $_POST['key'];
        }
        $bigUpload->setTempName($tempName);
        print $bigUpload->abortUpload();
    }


    private static function check_session()
    {
        // If the bwi_result exists and is not empty
        if (isset($_SESSION['bwi_results']) && $_SESSION['bwi_results'] != array()) {
            // And if the bwi_result has a file associated with it
            if (isset($_SESSION['bwi-uploadFilename']) && $_SESSION['bwi-uploadFilename'] != '') {
                // return that filename
                echo '<p>' . $_SESSION['bwi-uploadFilename'] . '</p>';
            }
        } else {
            echo "no_session";
        }
    }

    private static function delete_session()
    {
        $_SESSION['bwi_results'] = array();
        $_SESSION['bwi-uploadFilename']='';
        echo "session_deleted";
    }

    private static function parse_xml()
    {
        if (!isset($_SESSION['bwi-uploadFilename'])|| $_SESSION['bwi-uploadFilename'] == '') {

            die("Filename is not set, have you uploaded the file?");
        }
        // parser.php is the same parser used by the official WordPress Importer, with some
        // improvements to the robustness of the code.
        require_once 'parser.php';
        /*
         *   The working path is where we will store the data of in-progress imports
         *   It would be extreamly inefficient to add this data to the database every
         *   time it is updated, so it will only be added once compleated.
         */
        $wp_uploads = wp_upload_dir();
        $uploadPath = $wp_uploads['basedir'] . '/imports/';
        // filename is an absolute path to the location of the xml file uploaded

        $filename = $uploadPath . $_SESSION['bwi-uploadFilename'];
        // Initialise arrays to store data
        $authors = $posts = $terms = $categories = $tags = $results = array();
        // Create the WXR_Parser object that will handle the file, die() if file not valid
        $ajax_parser = new WXR_Parser();
        if (!is_file($filename)) {
            echo $filename;
            die("File Error: File could not be opened");
        }
        try {
            $results = $ajax_parser->parse($filename);
            // Save results to SESSION variable that will be available to future AJAX requests
            $_SESSION['bwi_results'] = $results;
            $authors = $results['authors'];
            foreach ($authors as $author) {
                echo "<div class=\"author_wrapper\">" . "<div class=\"author_login\">" . $author['author_login'] . "</div></div>";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private static function read_authors(){
        $authors = array();
        $authors = $_SESSION['bwi_results']['authors'];
        foreach ($authors as $author) {
            echo "<div class=\"author_wrapper\">" . "<div class=\"author_login\">" . $author['author_login']
                . "<input class=\"new_author_name\" type=\"text\" ></div></div>";
        }
    }

}

