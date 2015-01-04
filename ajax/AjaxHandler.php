<?php
/**
 * Author: Andrew Monteith
 * Date: 15/12/14 7:13 PM
 */

require_once 'ajax_authenticate.php';
require_once 'BigUpload.php';


/**
 * Class AjaxHandler
 * Decides what to with the Ajax Request
 */
class AjaxHandler
{
    /**
     * Checks the $_GET['action'] and $_POST['action'] variables to determine what action should be done.
     */
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
            case "read_authors":
                AjaxHandler::read_authors();
                break;
            case "get_authors_form":
                AjaxHandler::get_authors_form();
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

    /**
     * @global $_POST ['action']
     */
    private static function doPostAction()
    {
        switch ($_POST['action']) {
            case "post-author-import-form":
                $importer = new BmiImport();
                $importer->authorImport();
                break;
            case "import_content":
                $importer = new BmiImport();
                $importer->contentImport();
        }
    }

    /**
     * @return BigUpload
     */
    private static function getBigUpload()
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

    private static function bigUpload()
    {
        $bigUpload = AjaxHandler::getBigUpload();
        print $bigUpload->uploadFile();
    }

    private static function bigUploadFinish()
    {
        $bigUpload = AjaxHandler::getBigUpload();
        $_SESSION['bwi-uploadFilename'] = $_POST['name'];
        print $bigUpload->finishUpload($_POST['name']);
    }

    private static function bigUploadUnsupported()
    {
        $bigUpload = AjaxHandler::getBigUpload();
        print $bigUpload->postUnsupported();
    }

    private static function bigUploadAbort()
    {
        $bigUpload = AjaxHandler::getBigUpload();
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
        $_SESSION['bwi-uploadFilename'] = '';
        echo "session_deleted";
    }

    private static function parse_xml()
    {
        if (!isset($_SESSION['bwi-uploadFilename']) || $_SESSION['bwi-uploadFilename'] == '') {
            die("Filename is not set, have you uploaded the file?");
        }

        /*
         *   The working path is where we will store the data of in-progress imports
         *   It would be extremely inefficient to add this data to the database every
         *   time it is updated, so it will only be added once completed.
         */
        $wp_uploads = wp_upload_dir();
        $uploadPath = $wp_uploads['basedir'] . '/imports/';
        // filename is an absolute path to the location of the xml file uploaded
        $filename = $uploadPath . $_SESSION['bwi-uploadFilename'];
        // Create the WXR_Parser object that will handle the file, die() if file not valid
        $ajax_parser = new BWXR_Parser();
        if (!is_file($filename)) {
            echo $filename;
            die("File Error: File could not be opened");
        }
        try {
            $results = $ajax_parser->parse($filename);
            //Save results to SESSION variable that will be available to future AJAX requests
            $_SESSION['bwi_results'] = $results;
            $authors = $results['authors'];
            foreach ($authors as $author) {
                /** @var WxrAuthor $author */
                var_dump($author);
                ?>
                <div class="author-wrapper">
                    <div class="author-login"><?php echo $author->author_login; ?></div>
                    <input type="hidden" class="author_id" value="<?php echo $author->author_id; ?>">
                </div>
            <?php
            }
        } catch (Exception $e) {
            echo "Caught an error!" . $e->getMessage();
        }
    }

    private static function read_authors()
    {
        $authors = $_SESSION['bwi_results']['authors'];
        $jsonString = "{ \"authors\" : [";
        foreach ($authors as $author) {
            $jsonString =
                $jsonString . "{ " .
                "\"author_id\" : " . $author['author_id'] .
                ", \"author_display_name\" : \"" . $author['author_display_name'] . "\"" .
                " },";
        }
        $jsonString = substr($jsonString, 0, -1);
        $jsonString = $jsonString . " ] }";
        header('Content-Type: application/json');

        echo json_encode($jsonString);

    }

    /**
     * Outputs the user form.
     */
    private static function get_authors_form()
    {
        $authors = (isset($_SESSION['bwi_results']['authors'])) ? $_SESSION['bwi_results']['authors'] : die("No Authors Found");
        if ($authors == array()) die("No Authors Found");
        /*
         * Template for user form
         */
        require_once dirname(__FILE__) . '../../PanelSlider/Elements/import_user_template.php';
    }

}