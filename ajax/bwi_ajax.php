<?php
/**
 * Author: Andrew Monteith
 * Date: 15/12/14 7:32 PM
 */
require_once dirname(__FILE__) . '/../config.php';
require_once 'ajax_authenticate.php';
require_once 'AjaxHandler.php';
AjaxHandler::doAction();
