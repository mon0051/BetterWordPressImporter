<?php
/**
 * Author: Andrew Monteith
 * Date: 25/10/14
 */
require_once 'ajax_authenticate.php';
$BWIUploadDirectory = "/var/www/html/wordpress/wp-content/uploads/2014/";
if (isset($_FILES["FileInput"]) && $_FILES["FileInput"]["error"] == UPLOAD_ERR_OK) {
    // Validate FileSize ServerSide
    $max_up = ini_get('upload_max_filesize');
    /*
     * Sometimes the max upload size is specified in kilobytes
     * or megabytes. The section below will convert the number
     * if this is the case.
     */
    $last_letter = strtolower(substr($max_up, -1));
    if ($last_letter == "m") {
        $max_up = substr($max_up, 0, -1);
        $i_max_up = floatval($max_up);
        $i_max_up = $i_max_up * 1048576;
        $max_up = $i_max_up;
    }
    if ($last_letter == "k") {
        $max_up = substr($max_up, 0, -1);
        $i_max_up = floatval($max_up);
        $i_max_up = $i_max_up * 1024;
        $max_up = $i_max_up;
    }

    if($_FILES['FileInput']['size'] > $max_up) {
        echo "File to Big";
        die('File to large');
    }

    $File_Name = strtolower($_FILES["FileInput"]["name"]);
    $File_Ext = substr($File_Name, strrpos($File_Name, '.'));
    $Random_Number = rand(0, 999999999);
    $NewFileName = $File_Name . "_" . $Random_Number . $File_Ext;
    if (move_uploaded_file($_FILES['FileInput']['tmp_name'], $BWIUploadDirectory . $NewFileName)) {
        echo "<div class=\"ajax-return-value\" id=\"serverside-filename\">". $BWIUploadDirectory . $NewFileName . "</div>";
        die('WordPress Export File Uploaded Successfully');
    } else {
        echo "Failed";
        die('Upload Failed');
    }

}