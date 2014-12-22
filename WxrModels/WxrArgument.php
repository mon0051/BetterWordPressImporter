<?php
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 7:19 PM
 */

class WxrArgument {
    var $type = "string";
    var $arg_name;
    var $value;

    /**
     * @param string $_arg_name
     * @param mixed $_value
     * @param string $_type
     */
    function __construct($_arg_name,$_value,$_type="string"){
        $this->arg_name =$_arg_name;
    }
    function jsonValue(){
        if($this->type ==="string"){
            return " \"$this->arg_name\" : \"$this->value\",";
        }else{
            return " \"$this->arg_name\" : $this->value,";
        }
    }
}