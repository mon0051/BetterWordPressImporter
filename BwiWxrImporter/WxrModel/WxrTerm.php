<?php
/**
 * Author: Andrew Monteith
 * Date: 23/12/14 5:59 PM
 */
require_once 'aWxrModel.php';
/**
 * Class WxrTerm
 */
class WxrTerm extends aWxrModel{
    public $term_id;
    public $term_taxonomy;
    public $slug;
    public $term_parent;
    public $term_name;
    public $term_description;
    /**
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = "{";
        if(!is_null($this->term_id)) $jsonString .= "\"term_id\"" . "\"" .$this->term_id . "\",";
        if(!is_null($this->term_taxonomy)) $jsonString .= "\"term_taxonomy\"" . "\"" .$this->term_taxonomy . "\",";
        if(!is_null($this->slug)) $jsonString .= "\"slug\"" . "\"" .$this->slug . "\",";
        if(!is_null($this->term_parent)) $jsonString .= "\"term_parent\"" . "\"" .$this->term_parent . "\",";
        if(!is_null($this->term_name)) $jsonString .= "\"term_name\"" . "\"" .$this->term_name . "\",";
        if(!is_null($this->term_description)) $jsonString .= "\"term_description\"" . "\"" .$this->term_description . "\"";
        $jsonString .="}";
        return $jsonString;

    }

    /**
     * @return int | WP_Error
     */
    function saveToDatabase()
    {
        $args = array(
            'description' => $this->term_description,
            'parent' => $this->term_parent,
            'slug' => $this->slug
        );
        return wp_insert_term($this->term_name,$this->term_taxonomy,$args);
    }
}