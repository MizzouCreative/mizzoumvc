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

switch($aryData['objMainPost']->slug){
    case 'contact':
        //@todo move up higher
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';
        $objPeople = new People();
        $aryStaff = $objPeople->retrieveTopStaff();
        ob_start();
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'people-loop.php';
        $aryData['strStaffLoop'] = ob_get_contents();
        ob_end_clean();
        $strView = 'contact';
        break;
    case 'staff':
        /**
         * We've got duplicate code here. How can we refactor so we're not doing the same thing over and over?
         */
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';
        $objPeople = new People();
        $aryStaff = $objPeople->retrieveAllStaff(true);
        ob_start();
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'people-loop.php';
        $aryData['strStaffLoop'] = ob_get_contents();
        ob_end_clean();
        $strView = 'staff';
        break;
    case 'policy-research-scholars':
        /**
         * We've got duplicate code here. How can we refactor so we're not doing the same thing over and over?
         */
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';
        $objPeople = new People();
        $aryStaff = $objPeople->retrievePolicyScholars();
        ob_start();
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'people-loop.php';
        $aryData['strStaffLoop'] = ob_get_contents();
        ob_end_clean();
        /**
         * Is there any reason to NOT reuse the staff view here?
         */
        $strView = 'staff';
        break;
    case 'graduate-research-assistants':
        /**
         * We've got duplicate code here. How can we refactor so we're not doing the same thing over and over?
         */
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';
        $objPeople = new People();
        $aryStaff = $objPeople->retrieveGRAs();
        ob_start();
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'people-loop.php';
        $aryData['strStaffLoop'] = ob_get_contents();
        ob_end_clean();
        /**
         * Is there any reason to NOT reuse the staff view here?
         */
        $strView = 'staff';
        break;
        break;
    default:
        $strView = 'page';
        break;
}


mizzouOutPutView($strView,$aryData);
