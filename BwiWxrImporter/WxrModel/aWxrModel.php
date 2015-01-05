<?php

/**
 * Author: Andrew Monteith
 * Date: 22/12/14 5:17 PM
 */
abstract class aWxrModel
{
    /**
     * @return string | WP_Error
     */
    abstract function getJson();

    /**
     * @param $orphanList
     * @return int|WP_Error
     */
    abstract function saveToDatabase();

    abstract function getImportLog();

    /**
     * Adds the hierarchy to the data.
     * This can only be done after all data is inserted, as you need a
     * reference to the parents id on the new server.
     * @return mixed
     */
    abstract function updateParentInDatabase();

}