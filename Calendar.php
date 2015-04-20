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
$aryCalendarOptions = $objSite->calendar;

$objCalendar = new Calendar($aryCalendarOptions);

if(isset($aryCalendarOptions['method']) && isset($aryCalendarOptions['term'])){
	$aryReturn = $objCalendar->retrieveCalendarItems($aryCalendarOptions);
	$aryData['Events'] = $aryReturn['events'];
} else {
	_mizzou_log($aryCalendarOptions,'you asked me to get calendar items, but it doesnt appear you gave me the data i need.',false,array('line'=>__LINE__,'file'=>__FILE__));
	$aryData['Events'] = array();
}

Content::render('calendar',$aryData);