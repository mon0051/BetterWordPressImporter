<?php
/**
 * Author: Andrew Monteith
 * Date: 23/12/14 5:28 PM
 */
require_once 'aWxrModel.php';

/**
 * Class WxrPost
 */
class WxrPost extends aWxrModel
{
    /**
     * @var int $post_id
     * This refers to the local post_id after it has been inserted into the database
     * Before such time this value is NULL, and must be so in order for the save to
     * database function to work properly
     */
    public $post_id = NULL;
    public $post_title = '';
    public $guid;
    public $post_author;
    public $post_content;
    public $post_excerpt;
    public $post_date;
    public $post_date_gmt;
    public $comment_status;
    public $ping_status;
    public $post_name;
    public $status;

    public $menu_order;
    public $post_type;
    public $post_password;
    public $is_sticky;
    public $attachment_url;

    /** @var  WxrAuthor $wxrAuthor */
    public $wxrAuthor;
    public $wxrPostId;
    public $post_parent=false;
    public $wxrPostParent=false;

    // $terms is an array of WxrTerm objects
    public $terms = array();
    // $postmeta is an array of (key => value) pairs
    public $postmeta = array();
    // $comments is an array of WxrComment objects
    public $comments = array();

    /**
     * Does not output the content of the post, as this data could be very large and is not necessary for a rollback
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = "{";
        if (!is_null($this->post_title)) $jsonString .= "\"post_title\"" . "\"" . $this->post_title . "\",";
        if (!is_null($this->guid)) $jsonString .= "\"guid\"" . "\"" . $this->guid . "\",";
        if (!is_null($this->post_author)) $jsonString .= "\"post_author\"" . "\"" . $this->post_author . "\",";
        if (!is_null($this->post_id)) $jsonString .= "\"post_id\"" . $this->post_id . ",";
        if (!is_null($this->wxrPostId)) $jsonString .= "\"wxrPostId\"" . $this->wxrPostId . ",";
        if (!is_null($this->post_date)) $jsonString .= "\"post_date\"" . "\"" . $this->post_date . "\",";
        if (!is_null($this->post_date_gmt)) $jsonString .= "\"post_date_gmt\"" . "\"" . $this->post_date_gmt . "\",";
        if (!is_null($this->comment_status)) $jsonString .= "\"comment_status\"" . "\"" . $this->comment_status . "\",";
        if (!is_null($this->ping_status)) $jsonString .= "\"ping_status\"" . "\"" . $this->ping_status . "\",";
        if (!is_null($this->post_name)) $jsonString .= "\"post_name\"" . "\"" . $this->post_name . "\",";
        if (!is_null($this->status)) $jsonString .= "\"status\"" . "\"" . $this->status . "\",";
        if (!is_null($this->post_parent)) $jsonString .= "\"post_parent\"" . "\"" . $this->post_parent . "\",";
        if (!is_null($this->menu_order)) $jsonString .= "\"menu_order\"" . "\"" . $this->menu_order . "\",";
        if (!is_null($this->post_type)) $jsonString .= "\"post_type\"" . "\"" . $this->post_type . "\",";
        if (!is_null($this->post_password)) $jsonString .= "\"post_password\"" . "\"" . $this->post_password . "\",";
        if (!is_null($this->is_sticky)) $jsonString .= "\"is_sticky\"" . $this->is_sticky . ",";
        if (!is_null($this->attachment_url)) $jsonString .= "\"attachment_url\"" . "\"" . $this->attachment_url . "\",";
        if ($this->terms != array()) {
            $jsonString .= "{";
            foreach ($this->terms as $term) {
                /** @var WxrTerm $term */
                $jsonString .= $term->getJson() . ",";
            }
            $jsonString .= "},";
        }
        if ($this->postmeta != array()) {
            $jsonString .= "{";
            foreach ($this->postmeta as $postmeta) {
                /** @var WxrPostMeta $postmeta */
                $jsonString .= $postmeta->getJson() . ",";
            }
            $jsonString .= "},";
        }
        if ($this->comments != array()) {
            $jsonString .= "{";
            foreach ($this->comments as $comment) {
                /** @var WxrComment $comment */
                $jsonString .= $comment->getJson() . ",";
            }
            $jsonString .= "},";
        }
        $jsonString .= "}";
        return $jsonString;
    }

    /**
     * @param array $orphanList
     * @return int|WP_Error
     */
    function saveToDatabase($orphanList)
    {
        /** @var array $orphanList */
        // If we have post parent, then this post will be added to the orphan list
        if((int) $this->wxrPostParent){
            $orphanList[] = $this;
        }
        $args = array(
            'post_title' => $this->post_title,
            'guid' => $this->guid,
            'post_author' => $this->post_author,
            'post_content' => $this->post_content,
            'ID' => $this->post_id,
            'post_date' => $this->post_date,
            'post_date_gmt' => $this->post_date_gmt,
            'comment_status' => $this->comment_status,
            'ping_status' => $this->ping_status,
            'post_name' => $this->post_name,
            'status' => $this->status,
            'post_parent' => $this->post_parent,
            'menu_order' => $this->menu_order,
            'post_type' => $this->post_type,
            'post_password' => $this->post_password,
            'is_sticky' => $this->is_sticky,
            'attachment_url' => $this->attachment_url
        );
        $post_id = wp_insert_post($args, true);
        // Return if post did not insert
        if (is_wp_error($post_id)) return $post_id;

        foreach ($this->comments as $comment) {
            /** @var WxrComment $comment */
            $comment->comment_post_id = $post_id;
            $comment->saveToDatabase("");
        }
        foreach ($this->postmeta as $postmeta) {
            /** @var WxrPostMeta $postmeta */
            $postmeta->post_id = $post_id;
            $postmeta->saveToDatabase();
        }

        foreach ($this->terms as $term) {
            /** @var WxrTerm $term */
            $term_id = array(term_exists($term->term_id, $term->term_taxonomy, $term->term_parent));
            if ($term == 0) {
                $term->saveToDatabase();
                $term_id = array(term_exists($term->term_id, $term->term_taxonomy, $term->term_parent));
            }
            wp_set_post_terms($post_id, $term_id, $term->term_taxonomy, true);
        }
        return $post_id;
    }

    /**
     * @return string
     */
    function getImportLog()
    {
        $logString = '{ \'imported_post\':{';
        if (!is_null($this->post_id)) $logString .= '\'post_id\' :' . $this->post_id . ',';

        $logString .= "}}\n";
        return $logString;
    }

    function updateParentInDatabase()
    {
        // TODO: Implement updateParentInDatabase() method.
    }
}