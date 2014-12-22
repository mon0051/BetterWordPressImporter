<?php
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 6:32 PM
 */
require_once 'WxrAbstractDataType.php';

/**
 * Class WxrCategory
 * Wrapper for a freshly read category, not yet imported to the wordpress database
 */
class WxrCategory extends WxrAbstractDataType{
    /**
     * Make new Category
     */
    function __construct($term_id,$category_nicename,$cat_name,$category_description){
        parent::__construct();
    }
    /**
     * @return string DataType
     */
    function getWxrType()
    {
        return "WxrCategory";
    }

    /**
     * @return bool Will Return True if the item is properly saved to DataBase
     */
    function saveToDataBase()
    {
        // TODO: Implement saveToDataBase() method.
    }

    /**
     * @return int All data types have an id
     */
    function getId()
    {
        // TODO: Implement getId() method.
    }
}