<?php
/**
 * Author: Andrew Monteith
 * Date: 23/12/14 6:20 PM
 */
require_once 'aWxrModel.php';

/**
 * Class WxrPostMeta
 */
class WxrPostMeta extends aWxrModel
{
    public $key;
    public $value;
    public $post_id;

    /**
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = "{";
        if (!is_null($this->key)) $jsonString .= "\"key\"" . "\"" . $this->key . "\",";
        if (!is_null($this->value)) $jsonString .= "\"value\"" . "\"" . $this->value . "\",";
        if (!is_null($this->post_id)) $jsonString .= "\"post_id\"" . $this->post_id . ",";
        $jsonString .= "}";
        return $jsonString;
    }

    /**
     * @return int | WP_Error
     */
    function saveToDatabase()
    {
        return update_post_meta($this->post_id, $this->key, $this->value);
    }

    /**
     * @return string
     */
    function getImportLog()
    {
        $logString = '{ \'imported_postmeta\' {:';
        if (!is_null($this->post_id)) $logString .= '\'post_id\' :' . $this->post_id . ',';
        if (!is_null($this->key)) $logString .= '\'key\' : \'' . $this->key . '\',';
        if (!is_null($this->value)) $logString .= '\'value\' : \'' . $this->value . '\',';

        $logString .= "}}\n";
        return $logString;
    }
}