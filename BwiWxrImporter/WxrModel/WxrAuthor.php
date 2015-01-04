<?php

/**
 * Author: Andrew Monteith
 * Date: 23/12/14 1:33 AM
 */
require_once 'aWxrModel.php';

/**
 * Class WxrAuthor
 */
class WxrAuthor extends aWxrModel
{
    /** @var int */
    public $author_id;

    /**
     * @return int
     */
    public $author_first_name;
    public $author_last_name;
    public $author_email;
    public $author_display_name;
    public $author_login;

    /**
     * @return string | WP_Error
     */
    function getJson()
    {
        $jsonString = "{";
        if (!is_null($this->author_id)) $jsonString .= '\'author_id\'' .':'. $this->author_id . ",";
        if (!is_null($this->author_display_name)) $jsonString .= '\'author_display_name\'' .':\'' . $this->author_display_name . "\"" . ",";
        if (!is_null($this->author_last_name)) $jsonString .= '\'author_last_name\'' .':\'' . $this->author_last_name . "\"" . ",";
        if (!is_null($this->author_first_name)) $jsonString .= '\'author_first_name\'' .':\'' . $this->author_first_name . "\"" . ",";
        if (!is_null($this->author_email)) $jsonString .= '\'author_email\'' .':\'' . $this->author_email . "\"" . ",";
        if (!is_null($this->author_login)) $jsonString .= '\'author_login\'' .':\'' . $this->author_login . "\"";
        $jsonString .= "},";
        return $jsonString;
    }

    /**
     * @return int | WP_Error
     */
    function saveToDatabase($orphanList=false)
    {
        $args = array(
            'first_name' => $this->author_first_name,
            'last_name' => $this->author_last_name,
            'user_pass' => rand(100, 999999),
            'user_login' => $this->author_login,
            'user_email' => $this->author_email,
            'display_name' => $this->author_display_name
        );
        $this->author_id =  wp_insert_user($args, true);
        return $this->author_id;
    }

    /**
     * @return string $logString
     */
    function getImportLog(){
        $logString = '{ \'imported_author\' : { ';
        if (!is_null($this->author_id)) $logString .= '\'author_id\'' .' : '. $this->author_id . ",";
        if (!is_null($this->author_login)) $logString .= '\'author_login\'' .' : \'' . $this->author_login . '\' ';
        $logString .= "}},\n";
        return $logString;
    }
}