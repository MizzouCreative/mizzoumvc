<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/30/14
 * Time: 2:57 PM
 */
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$aryData = array();
$aryData['objMainPost'] = new MizzouPost($post);
mizzouOutPutView('page',$aryData);
