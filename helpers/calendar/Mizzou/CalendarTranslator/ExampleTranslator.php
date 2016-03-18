<?php
/**
 * Example Translator implementation. Based on actual implementation for BLSC.
 *
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 * @version 0.2
 */

namespace Mizzou\CalendarTranslator;

use Mizzou\CalendarTranslator\AbstractTranslator as AbstractTranslator;
use stdClass;

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'AbstractTranslator.php';

/**
* Example implementation of the Calendar Translater. Example case is for BLSC,
 * sciencevents.missouri.edu
*/
class ExampleTranslator extends AbstractTranslator
{
    /**
     * @var array
     */
    protected  $aryBLSCOptions = array(
		'talktitle'    => '/^(?P<talktitle>.*)\n$/'

	);

    /**
     * @param array $aryOptions
     */
    function __construct($aryOptions = array())
    {
		//merge our options with the parent options
		$this->_setOptions($this->aryBLSCOptions);
		//now merge the outside options
		parent::__construct($aryOptions);

	}


	/**
	 * Translates the event object from localist into a standard class object
	 * structured the way we need it for the current codeigniter code.
	 *
	 * @param object $objEvent
	 * @return object
	 */
	protected function _translateEvent($objEvent)
    {
		$objReturn = new stdClass();
		$objReturn->ID = $objEvent->id;
		$objReturn->START_TIME = date('M j, Y g:i A',strtotime((string)$objEvent->event_instances[0]->event_instance->start));
		$objReturn->END_TIME = date('M j, Y g:i A',strtotime((string)$objEvent->event_instances[0]->event_instance->start));
		$objReturn->SOCIAL_POST = ! (bool)$objEvent->private;
		$objReturn->TALK_TITLE = '';
		$objReturn->CANCELLED = '';
		$objReturn->EVENT_TYPE = '';
		$objReturn->LOCATION_ROOM = (string)$objEvent->room_number;
		$objReturn->LOCATION_BUILDING = (string)$objEvent->location_name;
		$objReturn->SERIES_NAME = '';
		$objReturn->SERIES_VALUE = (string)$objEvent->title;
		$objReturn->OTHER_SERIES_NAME = '';
		$objReturn->SPEAKER = '';
		$objReturn->URL = $objEvent->localist_url;

		if(isset($objEvent->custom_fields->speaker_information) && $objEvent->custom_fields->speaker_information != ''){
			$objReturn->SPEAKER = $objEvent->custom_fields->speaker_information;
		}

		if(preg_match($this->aryOptions['talktitle'], (string)$objEvent->description,$aryTalkTitleMatch)){
			$objReturn->TALK_TITLE = $aryTalkTitleMatch['talktitle'];
		}

		return $objReturn;

	}

}
?>