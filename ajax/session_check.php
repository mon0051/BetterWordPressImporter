<?php
/**
 * Author: Andrew Monteith
 * Date: 15/12/14 6:33 AM
 */
require 'ajax_authenticate.php';
session_start();
if(isset($_SESSION['bwi_results'])){
    if(isset($_SESSION['bwi_results']['associated_filename'])){
        echo $_SESSION['bwi_results']['associated_filename'];
    }
}else{
    echo "no Session";
}