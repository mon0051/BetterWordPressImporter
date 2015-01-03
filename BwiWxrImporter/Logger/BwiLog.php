<?php

/**
 * Author: Andrew Monteith
 * Date: 2/01/15 1:53 AM
 */
class BwiLog
{
    /** @var  string $buffer */
    private $buffer = '';
    private $log_post_id;
    private $output_pointer = 0;
    private $max_buffer_size = 2048;

    /**
     * Creates the CPT for the log file and
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