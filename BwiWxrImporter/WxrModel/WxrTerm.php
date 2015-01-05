<?php
/**
 * Author: Andrew Monteith
 * Date: 23/12/14 5:59 PM
 */
require_once 'aWxrModel.php';
/**
 * Class WxrTerm
 */
class WxrTerm extends aWxrModel
{
    public $term_id;
    public $wxrTermId;
    public $term_taxonomy;
    public $slug;
    public $term_parent;
    public $wxrTermParent;
    public $term_name;
    public $term_description;
    public $domain;

    /**
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = "{";
        if (!is_null($this->term_id)) $jsonString .= "\"term_id\"" . "\"" . $this->term_id . "\",";
        if (!is_null($this->term_taxonomy)) $jsonString .= "\"term_taxonomy\"" . "\"" . $this->term_taxonomy . "\",";
        if (!is_null($this->slug)) $jsonString .= "\"slug\"" . "\"" . $this->slug . "\",";
        if (!is_null($this->term_parent)) $jsonString .= "\"term_parent\"" . "\"" . $this->term_parent . "\",";
        if (!is_null($this->term_name)) $jsonString .= "\"term_name\"" . "\"" . $this->term_name . "\",";
        if (!is_null($this->term_description)) $jsonString .= "\"term_description\"" . "\"" . $this->term_description . "\"";
        $jsonString .= "}";
        return $jsonString;

    }

    /**
     * @return int|WP_Error
     */
    function saveToDatabase()
    {
        $this->term_id = term_exists($this->slug, $this->term_taxonomy);
        if ($this->term_id) {
            $err = new WP_Error("Term : \"$this->term_id\" already exists, leaving it alone ");
            return $err;
        }
        $args = array(
            'description' => $this->term_description,
            'slug' => $this->slug
        );
        $this->term_id = wp_insert_term($this->term_name, $this->term_taxonomy, $args);
        return $this->term_id;
    }

    /**
     * @return string
     */
    function getImportLog()
    {
        $logString = '{ \'imported_term\' {:';
        if (!is_null($this->term_id)) $logString .= '\'post_id\' :' . $this->term_id . ',';
        $logString .= "}}\n";
        return $logString;
    }

    /**
     * See the comment in aWxrModel class
     */
    function updateParentInDatabase()
    {
        if ($this->term_parent instanceof WxrTerm) {
            // the parent id must be inside an array because of legacy wordpress code
            // from before PHP was fully Object Oriented (Lots of messy things happened back
            // then to work around the semi Object Oriented nature of PHP)
            $args = array('parent' => array($this->term_parent->term_id));
            wp_update_term($this->term_id, $this->term_taxonomy, $args);
        }
    }
}