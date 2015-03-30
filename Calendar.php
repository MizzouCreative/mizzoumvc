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
$arySiteCalendarOptions = $objSite->option('calendar');

//do we need to include the excerpt_length option when creating our calendar object?
$aryCalendarOptions = (isset($arySiteCalendarOptions['excerpt_length'])) ? array('excerpt_length'=>$arySiteCalendarOptions['excerpt_length']) : array();

$objCalendar = new Calendar($aryCalendarOptions);

if(isset($arySiteCalendarOptions['method']) && isset($arySiteCalendarOptions['term'])){
	$aryOptions = $arySiteCalendarOptions;
	unset($aryOptions['excerpt_length']);
	$aryReturn = $objCalendar->retrieveCalendarItems($aryOptions);
	$aryData['Events'] = $aryReturn['events'];
} else {
	$aryData['Events'] = array();
}

Content::render('calendar',$aryData);