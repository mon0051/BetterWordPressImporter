<?php
require_once 'Logger/BwiLog.php';
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 12:03 AM
 */
class BmiImport
{
    public $authors;
    public $author_form;
    public $log;

    /**
     *  Basic Constructor
     */
    function __construct()
    {
        if (isset($_SESSION['bwi_results']['authors'])) {
            $this->authors = $_SESSION['bwi_results']['authors'];
        }
        if (isset($_POST['formdata'])) {
            $this->form = $_POST['formdata'];
        }
        $this->log = new BwiLog();
    }
    /**
     * Import the authors passed back by the web form into wordpress
     */
    public function authorImport()
    {
        if(!isset($this->authors) || $this->authors === array()) { return false; }
        foreach($this->authors as $author){
            if(!($author instanceof WxrAuthor)){ continue; }
            $this->log->log($author->getJson());
        }
        $this->log->close_log();
    }
}