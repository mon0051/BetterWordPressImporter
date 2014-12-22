<?php

/**
 * Author: Andrew Monteith
 * Date: 21/12/14 11:41 PM
 */
class WxrLog
{

    public $logPostId = -1;
    private $currentLogPartNum = 1;
    private $currentLogString = "";
    private $currentLogSize = 0;
    private $maxLogSectionSize = 200000;


    function __construct()
    {
        $args = array(
            'post_type' => 'bwi_import_log'
        );
        $this->logPostId = wp_insert_post($args);
        update_post_meta($this->logPostId,'log_status',"open");
    }

    /**
     * @param string $logBit A piece of log information to append to the log
     */
    function log($logBit)
    {
        $logSectionSize = strlen($logBit);
        if($logSectionSize + $this->currentLogSize < $this->maxLogSectionSize){
            $this->currentLogString = $this->currentLogString . $logBit;
            $this->currentLogSize += $logSectionSize;
        }else{
            update_post_meta($this->logPostId,'log_section_' . $this->currentLogPartNum,$this->currentLogString);
            $this->currentLogString = $logBit;
            $this->currentLogPartNum += 1;
        }
    }
    function finalise(){
        update_post_meta($this->logPostId,'log_section_' . $this->currentLogPartNum,$this->currentLogString);
        update_post_meta($this->logPostId,'log_status',"finalised");
    }

}