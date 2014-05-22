<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/20/14
 * Time: 4:47 PM
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'people.php';

$objPeople = new People();
//var_export($objPeople->retrieveAllStaff(true));
var_export($objPeople->retrieveTopStaff());
?>
