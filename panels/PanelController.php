<?php
/**
 * Author: Andrew Monteith
 * Date: 14/12/14 6:59 AM
 */

class PanelController {
    private $panel_count;

    /**
     * Contructor for Slide controller
     */
    function __construct(){
        $this->panel_count = 0;
    }

    /**
     * @param $panel_name
     * Pass a string referance to the panel, this will add it to the page
     */
    function add_panel($panel_name){
        include $panel_name;
        $this->panel_count += 1;
    }

    /**
     * @return int
     * returns the current number of panels
     */
    function getSlideCount(){
        return $this->panel_count;
    }

}