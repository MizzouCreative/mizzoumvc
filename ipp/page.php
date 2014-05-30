<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/30/14
 * Time: 2:57 PM
 * @todo shouldnt we have this page be the main controller for all pages and let it decide what to do with the page
 * requests instead of having individual controllers for each page?
 */
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$aryData = array();
$aryData['objMainPost'] = new MizzouPost($post);

switch($aryData['objMainPost']->name){
    case 'contact':
        //@todo move up higher
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';
        $objPeople = new People();
        $aryStaff = $objPeople->retrieveTopStaff();
        ob_start();
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'staff-loop.php';
        $aryData['strStaffLoop'] = ob_get_contents();
        ob_end_clean();
        $strView = 'contact';
        break;
    default:
        $strView = 'page';
        break;
}


mizzouOutPutView($strView,$aryData);
