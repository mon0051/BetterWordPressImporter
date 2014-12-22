<?php
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 6:44 PM
 */
require_once 'WxrArgBag.php';

/**
 * Class WxrAbstractDataType
 * The Super Class for all Wxr Data Types, should never be implemented, but instead
 * use one of the sub classes should be useds
 */
abstract class WxrAbstractDataType
{
    /**
     * @var WxrArgBag $argBag
     */
    protected $argBag;

    /**
     * Initialises the argBag
     */
    function __construct(){
        $this->argBag = new WxrArgBag();
    }

    /**
     * @return string returns a JSON representation of Data Type
     */
    function toJSON()
    {
        $jsonString = "{";
        foreach ($this->argBag->all() as $wxrArgument) {
            /** @var WxrArgument $wxrArgument */
            $jsonString = $jsonString . $wxrArgument->jsonValue();
        }
        $jsonString = "}";
        return $jsonString;
    }

    /**
     * @return string DataType
     */
    abstract function getWxrType();

    /**
     * @return bool Will Return True if the item is properly saved to DataBase
     */
    abstract function saveToDataBase();

    /**
     * @param string $attribute_name
     */
    function getAttribute($attribute_name)
    {
        if ($attribute_name) {
            $this->argBag->get($attribute_name);
        }
    }

    /**
     * @return int All data types have an id
     */
    abstract function getId();
}