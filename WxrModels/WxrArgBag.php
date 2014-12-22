<?php
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 7:51 PM
 */
require_once 'WxrArgument.php';
/**
 * Class WxrArgBag
 */
class WxrArgBag {
    /**
     * @var array $args
     */
    private $args;

    /**
     * @param WxrArgument $argument add WxrArgument to bag
     */
    function put(WxrArgument $argument){
        array_push($args,$argument);
    }

    /**
     * @param string $argument_name name of the argument to retrieve
     * @return WxrArgument $argument_name
     */
    function get($argument_name){
        foreach($this->args as $argument){
            if($argument->arg_name == $argument_name){
                return $argument_name;
            }
        }
        return false;
    }

    /**
     * @return array(WxrArgument)
     */
    function all(){
        return $this->args;
    }
}