<?php
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 6:12 PM
 */
require_once 'WxrAbstractDataType.php';

/**
 * Class WxrFile
 */
class WxrFile {

    public $authors = array();
    public $posts= array();
    public $category= array();
    public $tags= array();
    public $terms= array();
    public $import_time= array();
    public $import_filename= array();
    /** @var WxrAuthor $user_who_imported */
    public $user_who_imported;
    /** @var string  */
    public $log= "";
    public $rollback_data= array();
}