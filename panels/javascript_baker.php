<?php
function bakeInPhpUploadLimits()
{
    /*
     * This section dynamically alters the script to make
     * sure that it complies with the php.ini upload_max_size
     * and the post_max_size.
     */
    try {
        $kilobyte = 1024;
        $megabyte = 1048576;
        $max_up_cli = ini_get('upload_max_filesize');
        $max_post_cli = ini_get('post_max_size');
        $size_checks = array('max_upload_size' => $max_up_cli, 'post_max_size' => $max_post_cli);

        /*
         * Sometimes the size is specified in kilobytes
         * or megabytes. The section below will convert the number
         * if this is the case.
         */
        foreach ($size_checks as $check_name => $check_value) {
            $last_letter = strtolower(substr($check_value, -1));
            if ($last_letter == "m") {
                $$check_value = substr($check_value, 0, -1);
                $i_max = floatval($check_value);
                $i_max = $i_max * $megabyte;
                $check_value = $i_max;
            }
            if ($last_letter == "k") {
                $check_value = substr($check_value, 0, -1);
                $i_max = floatval($check_value);
                $i_max = $i_max * $kilobyte;
                $check_value = $i_max;
            }
            echo "var " . $check_name . "=" . $check_value . ";" . PHP_EOL;

        }
        // Now we update the script to use the correct upload_max_size value

    } catch (exception $e) {
        echo "var php_error = true;";
    }
}