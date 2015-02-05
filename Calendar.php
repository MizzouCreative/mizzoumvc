<?php
/**
 * Template Name: Calendar
 *
 * @package 
 * @subpackage 
 * @since 
 * @category 
 * @category 
 * @uses 
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
require_once 'models/Calendar.php';

$objSite = new Site();
$objCalendar = new Calendar();
$aryOptions = array(
    'method'=>'department',
    'term'  =>'4581'
);

echo '<pre>',var_export($objCalendar->retrieveCalendarItems($aryOptions),true),'</pre>';

//echo $objCalendar->retrieveCalendarItems($aryOptions);