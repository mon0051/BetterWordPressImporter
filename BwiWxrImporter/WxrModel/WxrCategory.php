<?php
/**
 * Author: Andrew Monteith
 * Date: 23/12/14 6:10 PM
 */
require_once 'aWxrModel.php';

/**
 * Class WxrCategory
 */
class WxrCategory extends aWxrModel
{
    public $term_id;
    public $wxr_term_id;
    public $category_nicename;
    public $category_parent;
    public $cat_name;
    public $category_description;

    /**
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = '{';
        if (!is_null($this->term_id)) $jsonString .= '\'term_id\'' . $this->term_id . ',';
        if (!is_null($this->category_nicename)) $jsonString .= '\'category_nicename\'' . ':\'' . $this->category_nicename . '\',';
        if (!is_null($this->category_parent)) $jsonString .= '\'category_parent\'' . ':\'' . $this->category_parent . '\',';
        if (!is_null($this->cat_name)) $jsonString .= '\'cat_name\'' . ':\'' . $this->cat_name . '\',';
        if (!is_null($this->category_description)) $jsonString .= '\'category_description\'' . ':\'' . $this->category_description . '\'';
        $jsonString .= '}';
        return $jsonString;
    }

    /**
     * @return int | WP_Error
     */
    function saveToDatabase()
    {
        $cat_array = array(
            'cat_ID' => $this->term_id,
            'cat_name' => $this->cat_name,
            'category_description' => $this->category_description,
            'category_nicename' => $this->category_nicename,
            'category_parent' => $this->category_parent,
            'taxonomy' => 'category'
        );
        return wp_insert_category($cat_array, true);
    }

    /**
     * @return string
     */
    function getImportLog()
    {
        $logString = '{ \'imported_category\' :{';
        if (!is_null($this->term_id)) $logString .= '\'term_id\':' . $this->term_id . ',';
        $logString .= "}}\n";
        return $logString;
    }
}