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
    /**
     * Checks the $_GET['action'] and $_POST['action'] variables to determine what action should be done.
     */
    public static function doAction()
    {
        if (isset($_GET['action'])) {
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
    }

    private static function bigUpload()
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
        print $bigUpload->uploadFile();
    }

    private static function bigUploadFinish()
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
        $_SESSION['bwi-uploadFilename'] = $_POST['name'];
        print $bigUpload->finishUpload($_POST['name']);
    }

    private static function bigUploadUnsupported()
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
        print $bigUpload->postUnsupported();
    }

    private static function bigUploadAbort()
    {
        //Instantiate the class
        $bigUpload = new BigUpload;
        $tempName = null;
        if (isset($_GET['key'])) {
            $tempName = $_GET['key'];
        }
        if (isset($_POST['key'])) {
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
        $_SESSION['bwi-uploadFilename'] = '';
        echo "session_deleted";
    }

    private static function parse_xml()
    {
        if (!isset($_SESSION['bwi-uploadFilename']) || $_SESSION['bwi-uploadFilename'] == '') {
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
                ?>
                <div class="author-wrapper">
                    <div class="author-login"><?php echo $author['author_login']; ?></div>
                    <input type="hidden" class="author_id" value="<?php echo $author['author_id']; ?>">
                </div>
            <?php
            }
        } catch (Exception $e) {
            echo $e->getMessage();
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
     * Generally a bad idea to mix buisness logic with frontend, i will eventually move the template to a seperate file.
     */
    private static function get_authors_form()
    {
        $authors = (isset($_SESSION['bwi_results']['authors'])) ? $_SESSION['bwi_results']['authors'] : die("No Authors Found");
        if ($authors == array()) die("No Authors Found");
        $wp_users = AjaxHandler::get_wordpress_users();
        /*
         * Template for user form
         */
        ?>
        <form class="bwi-form" id="import-author-form">
            <div class="bwi-input-area">
                <div class="bwi-form-header">
                    <div class="bwi-author bwi-header">Author</div>
                    <div class="bwi-action bwi-header">Action</div>
                </div>
                <?php
                foreach ($authors as $author) :
                    $id = $author['author_id'];
                    $name = $author['author_display_name'];
                    ?>
                    <div class="author-wrapper" id="bwi-author-template">
                        <input type="hidden" name="author-id" value="<?php echo $id;?>">

                        <div class="bwi-author-name-wrapper bwi-author">
                            <div class="bwi-author-name"><?php echo $name;?></div>
                        </div>
                        <div class="bwi-action">
                            <label for="author-import-option-selector-<?php echo $id;?>"></label>
                            <select name="author-import-option-selector-<?php echo $id;?>"
                                    id="author-import-option-selector-<?php echo $id;?>">
                                <option value="import">Import Author</option>
                                <option value="import">Create New Author</option>
                                <option value="import">Use Existing Author</option>
                            </select>
                        </div>
                        <div class="author-input-wrapper bwi-hidden">
                            <div class="author-new-input-wrapper">
                                <label for="new-author-input-<?php echo $id;?>">New Name</label>
                                <input type="text" name="new-author-input-<?php echo $id;?>" value="<?php echo $name;?>"
                                       id="new-author-input-<?php echo $id;?>">
                            </div>
                            <div class="author-select-wrapper bwi-hidden">
                                <label for="existing-author-selector-<?php echo $id;?>">Assign Existing Author</label>
                                <select name="existing-author-selector-<?php echo $id;?>"
                                        id="existing-author-selector-<?php echo $id;?>">
                                    <?php
                                    foreach ($wp_users as $wp_user) {
                                        $value = "value=\"" . $wp_user->ID . "\"";
                                        $uname = $wp_user->display_name;
                                        echo "<option $value > $uname </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
            <div class="bwi-button" id="bwi-submit-author-form">
                <div class="bwi-button-text">Import</div>
            </div>
        </form>
    <?php
    }

    /**
     * @return array Array of WordPress Users
     */
    private static function get_wordpress_users()
    {
        return get_users(array('who' => 'authors'));
    }
}