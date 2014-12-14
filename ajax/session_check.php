<?php
/**
 * Author: Andrew Monteith
 * Date: 15/12/14 6:33 AM
 */
require 'ajax_authenticate.php';
session_start();
function bwi_check_session()
{
    // If the bwi_result exists and is not empty
    if (isset($_SESSION['bwi_results']) && $_SESSION['bwi_results'] != array()) {
        // And if the bwi_result has a file associated with it
        if (isset($_SESSION['bwi_results']['associated_filename'])) {
            // return that filename
            echo '<p>' . $_SESSION['bwi_results']['associated_filename'] . '</p>';
        }
    } else {
        echo "no_session";
    }
}
function bwi_delete_session(){
    $_SESSION['bwi_results'] = array();
    echo "session_deleted";
}

if(isset($_GET['action'])){
    if($_GET['action']=="first_contact"){
        bwi_check_session();
        return;
    }
}
if(isset($_GET['action'])){
    if($_GET['action']=="delete_session"){
        bwi_delete_session();
        return;
    }
}