<?php
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 6:29 PM
 */
require_once 'WxrAbstractDataType.php';
/**
 * Class WxrAuthor Contains the details for a WordPress User
 */
class WxrAuthor extends WxrAbstractDataType{
    /**
     * @param $author_id
     * @param $author_login
     * @param $author_email
     * @param $author_display_name
     * @param $author_first_name
     * @param $author_last_name
     */
    function __construct($author_id,$author_login,$author_email = false,
                         $author_display_name= false,$author_first_name= false,
                         $author_last_name= false){
        parent::__construct();
        $this->argBag = new WxrArgBag();
        $author_id = (is_int($author_id)) ? new WxrArgument('author_id',$author_id) : new WxrBlankArgument('author_id');
        $this->argBag->put($author_id);
        $author_email = (is_string($author_email)) ? new WxrArgument('author_email',$author_email) : new WxrBlankArgument('author_email');
        $this->argBag->put($author_email);
        $author_login = (is_string($author_login)) ? new WxrArgument('author_login',$author_login) : new WxrBlankArgument('author_login');
        $this->argBag->put($author_login);
        $author_display_name = (is_string($author_display_name)) ? new WxrArgument('author_display_name',$author_display_name) : new WxrBlankArgument('author_display_name');
        $this->argBag->put($author_display_name);
        $author_first_name = (is_string($author_first_name)) ? new WxrArgument('author_first_name',$author_first_name) : new WxrBlankArgument('author_first_name');
        $this->argBag->put($author_first_name);
        $author_last_name = (is_string($author_last_name)) ? new WxrArgument('author_last_name',$author_last_name) : new WxrBlankArgument('author_last_name');
        $this->argBag->put($author_last_name);
    }
    /**
     * @return string DataType
     */
    function getWxrType(){
        return "WxrAuthor";
    }
    /**
     * @return bool Will Return True if the item is properly saved to DataBase
     */
    function saveToDataBase(){
        $userdata = array(
            "user_login" => $this->argBag->get('author_login'),
            "user_email" => $this->argBag->get('author_email'),
            "display_name" => $this->argBag->get('author_display_name'),
            "first_name" => $this->argBag->get('author_first_name'),
            "last_name" => $this->argBag->get('author_last_name')
        );
        $id = wp_insert_user($userdata);
        return $id;
    }
    /**
     * @return int All data types have an id
     */
    function getId(){
        return $this->argBag->get('author_id');
    }
}