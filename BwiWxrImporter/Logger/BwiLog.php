<?php

/**
 * Author: Andrew Monteith
 * Date: 2/01/15 1:53 AM
 */

/**
 * Class BwiLog
 * This class is fairly important due to the restrictive nature of some servers,
 * which will cut a script off midway through execution if it exceeds time or
 * memory parameters.
 * This log will output to the database every 2kb of data that is added to the log
 * so we will never lose more than 2kb of logs if the script is cut short.
 * If the server puts the script to sleep it will output its buffer before exiting,
 * allowing us to pick back off where we left from when the script resumes.
 */
class BwiLog
{
    /** @var  string $buffer */
    private $buffer = '';
    private $log_post_id;
    private $output_pointer = 0;
    private $max_buffer_size = 2048;

    /**
     * Creates the CPT for the logbook file and
     */
    function __construct()
    {
        $now_date = date(DATE_ISO8601);
        $postArgs = array(
            'post_title' => 'Log started' . $now_date,
            'post_type' => 'bwi_import_log'
        );
        $this->log_post_id = wp_insert_post($postArgs);
    }

    /**
     *
     * @param string $section
     */
    public function log($section)
    {
        if (strlen($this->buffer) >= $this->max_buffer_size) {
            $this->writeBufferToDb();
            $this->buffer = $section;
        } else {
            $this->buffer .= $section;
        }
    }

    /**
     * @param $errorMessage
     */
    public function logError($errorMessage){
        $jError = "{ error : \"$errorMessage\"},\n";
        $this->log($jError);
    }
    /**
     * @param $errorMessage
     */
    public function logNotice($noticeMessage){
        $jNotice = "{ notice : \"$noticeMessage\"},\n";
        $this->log($jNotice);
    }
    public function close_log(){
        $this->writeBufferToDb();
    }
    function __sleep()
    {
        $this->writeBufferToDb();
    }

    function __destruct()
    {
        $this->writeBufferToDb();
    }

    private function writeBufferToDb()
    {
        $section_name = 'section_' . $this->output_pointer;
        update_post_meta($this->log_post_id, $section_name, $this->buffer);
        $this->output_pointer += 1;
        $this->buffer = '';
    }
}