<?php
/**
 * Author: Andrew Monteith
 * Date: 28/12/14 4:38 AM
 */

class AjaxHelpers {
    function safe_require_once($filename){
        if(file_exists($filename)){
            require_once $filename;
        }else{
            die("Required File " . $filename . " can't be found");
        }
    }
}