<?php

/**
 * Author: Andrew Monteith
 * Date: 20/12/14 12:03 AM
 */
class BmiImport
{
    public $authors;
    public $author_form;

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
    }

    /**
     * Import the authors passed back by the web form into wordpress
     */
    public function authorImport()
    {

    }


    /**
     * @param $log
     */
    function save_log($log)
    {

    }
}