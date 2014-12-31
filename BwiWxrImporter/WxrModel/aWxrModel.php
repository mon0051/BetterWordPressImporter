<?php
/**
 * Author: Andrew Monteith
 * Date: 22/12/14 5:17 PM
 */

abstract class aWxrModel {
    /**
     * @return string | WP_Error
     */
    abstract function getJson();

    /**
     * @return int | WP_Error
     */
    abstract function saveToDatabase();
}