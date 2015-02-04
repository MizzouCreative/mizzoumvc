<?php
/**
 * 
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
use Mizzou\CalendarTranslator\AbstractTranslator as AbstractTranslator;

/**
 * @todo seriously need to look into autoloaders
 */
require_once dirname(dirname(__FILE__)).'/helpers/calendar/Mizzou/CalendarTranslator/AbstractTranslator.php';
class Calendar extends AbstractTranslator {

    protected function _translate_event($objEvent) {
        $objReturn = new stdClass();
        $objReturn->Id              = $objEvent->id;
        $objReturn->Title           = (string)$objEvent->title;
        $objReturn->Description     = (string)$objEvent->description;
        $objReturn->RoomNumber     = (string)$objEvent->room_number;
        $objReturn->LocationName   = (string)$objEvent->location_name;
        $objReturn->Url             = (string)$objEvent->localist_url;
        $objReturn->DescriptionText= (string)$objEvent->description_text;
        $objReturn->Start           = strtotime((string)$objEvent->event_instances[0]->event_instance->start);
        $objReturn->End             = strtotime((string)$objEvent->event_instances[0]->event_instance->end);

        return $objReturn;
    }
}