<?php
/**
 * Author: Andrew Monteith
 * Date: 23/12/14 5:39 PM
 */
require_once 'aWxrModel.php';

/**
 * Class WxrComment
 */
class WxrComment extends aWxrModel
{
    public $comment_post_id;
    public $comment_id;
    public $wxr_comment_id;
    public $comment_author;
    public $comment_author_email;
    public $comment_author_IP;
    public $comment_author_url;
    public $comment_date;
    public $comment_date_gmt;
    public $comment_content;
    public $comment_approved;
    public $comment_type;
    public $comment_parent;
    public $comment_user_id;
    public $commentmeta;

    /**
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = "{";
        if (!is_null($this->comment_id)) $jsonString .= "\"comment_id\"" . $this->comment_id . ",";
        if (!is_null($this->comment_author)) $jsonString .= "\"comment_author\"" . "\"" . $this->comment_author . "\",";
        if (!is_null($this->comment_author_email)) $jsonString .= "\"comment_author_email\"" . "\"" . $this->comment_author_email . "\",";
        if (!is_null($this->comment_author_IP)) $jsonString .= "\"comment_author_IP\"" . "\"" . $this->comment_author_IP . "\",";
        if (!is_null($this->comment_author_url)) $jsonString .= "\"comment_author_url\"" . "\"" . $this->comment_author_url . "\",";
        if (!is_null($this->comment_date)) $jsonString .= "\"comment_date\"" . "\"" . $this->comment_date . "\",";
        if (!is_null($this->comment_date_gmt)) $jsonString .= "\"comment_date_gmt\"" . "\"" . $this->comment_date_gmt . "\",";
        if (!is_null($this->comment_content)) $jsonString .= "\"comment_content\"" . "\"" . $this->comment_content . "\",";
        if (!is_null($this->comment_approved)) $jsonString .= "\"comment_approved\"" . "\"" . $this->comment_approved . "\",";
        if (!is_null($this->comment_type)) $jsonString .= "\"comment_type\"" . "\"" . $this->comment_type . "\",";
        if (!is_null($this->comment_parent)) $jsonString .= "\"comment_parent\"" . "\"" . $this->comment_parent . "\",";
        if (!is_null($this->comment_user_id)) $jsonString .= "\"comment_user_id\"" . "\"" . $this->comment_user_id . "\",";
        if (!is_null($this->commentmeta)) $jsonString .= "\"commentmeta\"" . "\"" . $this->commentmeta . "\"";
        $jsonString .= "}";
        return $jsonString;
    }

    /**
     * @return int | WP_Error
     */
    function saveToDatabase($orphanList=false)
    {
        $args = array(
            'comment_post_id' => $this->comment_post_id,
            'comment_author' => $this->comment_author,
            'comment_author_email' => $this->comment_author_email,
            'comment_author_url' => $this->comment_author_url,
            'comment_content' => $this->comment_content,
            'comment_type' => $this->comment_type,
            'comment_parent' => $this->comment_parent,
            'user_id' => $this->comment_user_id,
            'comment_author_IP' => $this->comment_author_IP,
            'comment_date' => $this->comment_date,
            'comment_approved' => $this->comment_approved
        );
        return wp_insert_comment($args);
    }

    /**
     * @return string $logString
     */
    function getImportLog()
    {
        $logString = '{ \'imported_comment\' {:';
        if (!is_null($this->comment_id)) $logString .= '\'comment_id\' :' . $this->comment_id . ',';

        $logString .= "}}\n";
        return $logString;
    }
}