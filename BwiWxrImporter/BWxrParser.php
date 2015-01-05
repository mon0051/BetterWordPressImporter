<?php
/**
 * Modified Version of the official WordPress Importers Parser.
 * @package WordPress
 * @subpackage Importer
 */
require_once dirname(__FILE__) . '../../ajax/ajax_authenticate.php';
//require_once dirname(__FILE__) . '../../BwiWxrImporter/BWxrParserSimple.php';
require_once dirname(__FILE__) . '../../BwiWxrImporter/BWxrParserRegex.php';

/**
 * Class BWXR_Parser
 */
class BWXR_Parser
{
    /**
     * Takes a File Pointer and returns an array of results.
     * Depending on the installed PHP library's, will pass this off to
     * another class to be completed.
     * @param $file
     * @return array|WP_Error
     */
    function parse($file)
    {
        // Attempt to use proper XML parsers first
//        if (extension_loaded('simplexml')) {
//            $parser = new BWXR_Parser_SimpleXML;
//            $result = $parser->parse($file);
//
//            // If SimpleXML succeeds or this is an invalid WXR file then return the results
//            if (!is_wp_error($result) || 'SimpleXML_parse_error' != $result->get_error_code())
//                return $result;
//        }

        // use regular expressions if nothing else available or this is bad XML
        $parser = new BWXR_Parser_Regex;
        return $parser->parse($file);
    }
}




