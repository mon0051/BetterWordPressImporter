<?php
/**
 * Author: Andrew Monteith
 * Date: 23/12/14 6:06 PM
 */
require_once 'aWxrModel.php';

/**
 * Class WxrTag
 */
class WxrTag extends aWxrModel
{
    public $term_id;
    public $tag_slug;
    public $tag_name;
    public $tag_description;

    /**
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = "{";
        if (!is_null($this->term_id)) $jsonString .= "\"term_id\"" . $this->term_id . ",";
        if (!is_null($this->tag_name)) $jsonString .= "\"tag_name\"" . "\"" . $this->tag_name . "\",";
        if (!is_null($this->tag_slug)) $jsonString .= "\"tag_slug\"" . "\"" . $this->tag_slug . "\",";
        if (!is_null($this->tag_description)) $jsonString .= "\"tag_description\"" . "\"" . $this->tag_description . "\"";
        $jsonString .= "}";
        return $jsonString;
    }

    /**
     * @return int | WP_Error
     */
    function saveToDatabase()
    {
        $args = array(
            'description' => $this->tag_description,
            'slug' => $this->tag_slug
        );
        return wp_insert_term($this->tag_name, 'post_tag', $args);
    }
}