<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/20/14
 * Time: 4:47 PM
 */

//@todo move up higher
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$objPeople = new People();

// we're probably going to do this on evey page, so can it be moved up higher?
$aryData['objMainPost'] = new MizzouPost($post);

$aryData['aryStaff'] = $objPeople->retrieveAllStaff(true);

//echo '<xmp>',var_export($aryStaff,true),'</xmp>';

mizzouOutPutView('page-staff',$aryData);
?>
