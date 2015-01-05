<?php
require_once 'Logger/BwiLog.php';

/**
 * Author: Andrew Monteith
 * Date: 20/12/14 12:03 AM
 */
class BmiImport
{
    // The WxrData types have to be imported in a specific order, so unfortunately we can't simply
    // loop though an array of all things to import
    private $authors;
    private $categories;
    private $terms;
    private $tags;
    private $posts;

    private $defaultAuthor;
    private $authorForm;
    public $logbook;

    /**
     *  Basic Constructor
     */
    function __construct()
    {
        if (isset($_SESSION['bwi_results']['authors'])) {
            $this->authors = $_SESSION['bwi_results']['authors'];
        }
        if (isset($_POST['formdata'])) {
            $this->authorForm = $_POST['formdata'];
        }
        if (isset($_SESSION['bwi_results']['posts'])) {
            $this->posts = $_SESSION['bwi_results']['posts'];
        }
        if (isset($_SESSION['bwi_results']['terms'])) {
            $this->terms = $_SESSION['bwi_results']['terms'];
        }
        if (isset($_SESSION['bwi_results']['tags'])) {
            $this->tags = $_SESSION['bwi_results']['tags'];
        }
        if (isset($_SESSION['bwi_results']['categories'])) {
            $this->categories = $_SESSION['bwi_results']['categories'];
        }
        /** @var WP_User $current_wp_user */
        $current_wp_user = wp_get_current_user();
        $this->defaultAuthor = new WxrAuthor();
        if ($current_wp_user->has_prop('user_login')) {
            $this->defaultAuthor->author_login = $current_wp_user->get('user_login');
        }
        $this->defaultAuthor->author_id = $current_wp_user->ID;
        $this->logbook = new BwiLog();
    }

    /**
     * Import the authors passed back by the web form into wordpress
     * @return boolean
     */
    public function authorImport()
    {
        if (!isset($this->authors) || $this->authors === array()) {
            return false;
        }
        foreach ($this->authors as $author) {
            if (!($author instanceof WxrAuthor)) {
                $this->logbook->logError("Invalid Author found, make sure that authors were exported properly.");
                continue;
            }
            // check if the user already exists before importing
            $existing_user = username_exists($author->author_login);
            if ($existing_user) {
                $this->logbook->logNotice("User already exists, using existing author");
                $author->author_id = $existing_user;
                continue;
            }
            $author->saveToDatabase();
            $this->logbook->log($author->getImportLog());
        }
        $this->logbook->close_log();
        return true;
    }

    /**
     * This function imports all the content to the local wordpress site
     */
    public function importContent()
    {
        $this->authorImport();
        $this->mapAuthorsToPosts();
        $this->importWxrItemArray($this->posts);
        $this->importWxrItemArray($this->terms);
        $this->importWxrItemArray($this->tags);
        $this->importWxrItemArray($this->categories);
    }

    /**
     * @param $wxrItemArray
     */
    private function importWxrItemArray($wxrItemArray)
    {
        // The orphan list contains all the items that have parents
        // after all the content has been imported, all the parents
        // should be in the database, so they will be added after
        // the initial import.
        $orphanList = array();
        if (is_array($wxrItemArray)) {
            foreach ($wxrItemArray as $wxrItem) {
                /** @var aWxrModel $wxrItem */
                $result = $wxrItem->saveToDatabase($orphanList);
                if (is_wp_error($result)) {
                    /** @var WP_Error $result */
                    $this->logbook->logError($result->get_error_message());
                } else {
                    $this->logbook->log($wxrItem->getImportLog());
                }
            }
        }
        if (!empty($orphanList)) {
            foreach ($orphanList as $orphan) {
                /** @var aWxrModel $orphan */
                $orphan->updateParentInDatabase();
            }
        }
    }

    public function mapAuthorsToPosts()
    {
        if (is_array($this->posts)) {
            foreach ($this->posts as $bPost) {
                /** @var WxrPost $bPost */
                foreach ($this->authors as $author) {
                    /** @var WxrAuthor $author */
                    if ($author->author_login == $bPost->post_author) {
                        $bPost->wxrAuthor = $author;
                        break;
                    }
                }
                if ($bPost->wxrAuthor == null) {
                    $bPost->wxrAuthor = $this->defaultAuthor;
                    $pId = $bPost->wxrPostId;
                    $this->logbook->logError("Post with id : \"$pId\" Has no valid author, using current author instead");
                }
            }
        }
    }
}