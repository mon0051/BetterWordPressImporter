<?php
/**
 * Author: Andrew Monteith
 * Date: 15/12/14 7:13 PM
 */

require_once 'BigUpload.php';


/**
 * Class AjaxHandler
 * Decides what to with the Ajax Request
 */
class AjaxHandler
{
    public static function doAction()
    {
        if (isset($_GET['action'])) {
            AjaxHandler::doGetAction();
        }
        if (isset($_POST['action'])) {
            AjaxHandler::doPostAction();
        }
    }
    private static function doGetAction()
    {
        switch ($_GET['action']) {
            case "first_contact":
                AjaxHandler::check_session();
                break;
            case "delete_session":
                AjaxHandler::delete_session();
                break;
            case "parse_xml":
                AjaxHandler::parse_xml();
                break;
            case "get_authors_form":
                AjaxHandler::get_authors_form();
                break;
            case "upload":
                $bigUpload = AjaxHandler::setupUpload();
                print $bigUpload->uploadFile();
                break;
            case "abort":
                $bigUpload = AjaxHandler::setupUpload();
                print $bigUpload->abortUpload();
                break;
            case "finish":
                $bigUpload = AjaxHandler::setupUpload();
                $_SESSION['bwi-uploadFilename'] = $_POST['name'];
                print $bigUpload->finishUpload($_POST['name']);
                break;
            case 'post-unsupported':
                $bigUpload = AjaxHandler::setupUpload();
                print $bigUpload->postUnsupported();
                break;
            case 'get_rollback_data':
                AjaxHandler::getRollbackData();
                break;
        }
    }

    private static function doPostAction()
    {
        switch ($_POST['action']) {
            case "post-author-import-form":
                $importer = new BwiImport();
                $importer->authorImport();
                break;
            case "import_content":
                $importer = new BwiImport();
                $importer->importContent();
                break;
            case "rollback":
                $rollbacker = new BwiRollback();
                $rollbacker->rollback();
                break;
        }
    }
    //-------------------------------------
    //     Proper functions start here
    //-------------------------------------
    private static function getRollbackData()
    {
        echo "<div class=\"bwi-rollback-data\">";
        $args = array(
            'post_type' => 'bwi_import_log',
            'post_status' => array(
                'any', 'draft', 'auto-draft'
            )
        );
        $import_logs = get_posts($args);
        foreach ($import_logs as $log) {
            /** @var WP_Post $log */
            echo "<div class=\"bwi_log_id\">$log->ID</div>";
            echo "<div class=\"title\">$log->post_title</div>";
        }
        echo "</div>";
    }

    /**
     * @return BigUpload
     */
    private static function setupUpload()
    {
        $bigUpload = new BigUpload;
        $tempName = null;
        if (isset($_GET['key'])) {
            $tempName = $_GET['key'];
        }
        if (isset($_POST['key'])) {
            $tempName = $_POST['key'];
        }
        $bigUpload->setTempName($tempName);
        return $bigUpload;
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
        $_SESSION['bwi-uploadFilename'] = '';
        echo "session_deleted";
    }

    private static function parse_xml()
    {
        $wp_uploads = wp_upload_dir();
        $uploadPath = $wp_uploads['basedir'] . '/imports/';
        $filename = $uploadPath . $_SESSION['bwi-uploadFilename'];
        if (!is_file($filename)) { die("Error: File $filename could not be opened"); }
        $ajax_parser = new BWXR_Parser();
        $results = $ajax_parser->parse($filename);
        $_SESSION['bwi_results'] = $results;
    }

    /**
     * Outputs the user form.
     */
    private static function get_authors_form()
    {
        $authors = (isset($_SESSION['bwi_results']['authors'])) ? $_SESSION['bwi_results']['authors'] : die("No Authors Found");
        if (empty($authors)) die("No Authors Found");
        require_once BWI_BASE_PATH . '/PanelSlider/Elements/import_user_template.php';
    }

}