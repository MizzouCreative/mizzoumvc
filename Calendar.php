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
namespace MizzouMVC\controllers;

class Calendar extends Main {

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