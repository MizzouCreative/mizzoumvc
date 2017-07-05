<?php
/**
 * Template Name: Calendar
 */
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * Retrieves and displays calendar events from the central calendaring application (currently Localist)
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category controller
 * @category framework
 * @uses MizzouMVC\models\Calendar
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */

class Calendar extends Main {

    /**
     * Workhorse function
     */
    public function main()
    {
        if(isset($this->objSite->calendar) && is_array($this->objSite->calendar)){
            $aryCalendarOptions =  $this->objSite->calendar;
            $objCalendar = $this->load('MizzouMVC\models\Calendar',$aryCalendarOptions);

            if(isset($aryCalendarOptions['method']) && isset($aryCalendarOptions['term'])){
                $aryReturn = $objCalendar->retrieveCalendarItems($aryCalendarOptions);
                $aryEvents = $aryReturn['events'];
            } else {
                _mizzou_log($aryCalendarOptions,'you asked me to get calendar items, but it doesnt appear you gave me the data i need.',false,array('line'=>__LINE__,'file'=>__FILE__));
                $aryEvents = array();
            }

            $this->renderData('Events',$aryEvents);
        } else {
            _mizzou_log($this->objSite,'you asked me for calendar stuff but there doesnt appear to be any calendar settings',false,array('line'=>__LINE__,'file'=>__FILE__));
        }

        $this->render('calendar');
    }
}

new Calendar();