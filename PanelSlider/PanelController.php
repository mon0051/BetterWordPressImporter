<?php
/**
 * Author: Andrew Monteith
 * Date: 14/12/14 6:59 AM
 * @package PanelSlider
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
     * Pass a string referance to the PanelSlider php file, this will add it to the page and setup
     * the variables for its position and id
     */
    function add_panel($panel_name){
        /** @noinspection PhpIncludeInspection */
        $regex = "/(?:Panels\\/)(.*)(?:\\.php)/";
        preg_match($regex,$panel_name,$matchs);
        $panel_id = 'id="' . $matchs[1] .'"';
        $panel_number = 'data_position="' . $this->panel_count .'"';
        echo "<div class=\"bwi-slide-wrapper\" $panel_id $panel_number>";
        /** @noinspection PhpIncludeInspection */
        include $panel_name;
        echo "</div>";
        $this->panel_count += 1;
    }
    /**
     * @return int
     * returns the current number of PanelSlider
     */
    function getSlideCount(){
        return $this->panel_count;
    }

}