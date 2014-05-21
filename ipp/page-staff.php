<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/20/14
 * Time: 4:47 PM
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'people.php';

$objPeople = new People();
var_export($objPeople->retrieveTopStaff());
?>
