<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/20/14
 * Time: 4:47 PM
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';

$objPeople = new People();

$aryStaff = $objPeople->retrieveAllStaff(true);

echo '<xmp>',var_export($aryStaff,true),'</xmp>';

//mizzouOutPutView('page-staff',$aryData);
?>
