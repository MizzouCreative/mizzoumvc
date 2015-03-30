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

    public function __construct($aryOptions=array())
    {
        parent::__construct($aryOptions);
    }

    /**
     * Translates the data returned from Localist into an object we can use
     * @param $objEvent
     * @return stdClass
     */
    protected function _translateEvent($objEvent)
    {
        $objReturn = new stdClass();
        $objReturn->Id              = $objEvent->id;
        $objReturn->Title           = (string)$objEvent->title;
        $objReturn->Description     = (string)$objEvent->description;
        $objReturn->RoomNumber      = (string)$objEvent->room_number;
        $objReturn->LocationName    = (string)$objEvent->location_name;
        $objReturn->Url             = (string)$objEvent->localist_url;
        $objReturn->DescriptionText = (string)$objEvent->description_text;
        $objReturn->Start           = strtotime((string)$objEvent->event_instances[0]->event_instance->start);
        $objReturn->End             = strtotime((string)$objEvent->event_instances[0]->event_instance->end);
	    $objReturn->StartMonth      = date('F',$objReturn->Start);
	    $objReturn->StartAPMonth    = $this->_getAPMonth($objReturn->StartMonth);
	    $objReturn->EndMonth        = date('F',$objReturn->End);
	    $objReturn->EndAPMonth      = $this->_getAPMonth(($objReturn->EndMonth));
	    $objReturn->StartDay        = date('j',$objReturn->Start);
	    $objReturn->StartYear       = date('Y',$objReturn->Start);
	    $objReturn->EndYear         = date('Y',$objReturn->End);

        return $objReturn;
    }

	/**
	 * Reformats the Month to AP style date format based on the timestamp of the post
	 * @return string AP style formatted month
	 * @todo this is the exact same function from @see MizzouPost::_getAPMonth(). Can we convert
	 */
	private function _getAPMonth($strMonth)
	{
		if(strlen($strMonth) > 5){ //stoopid september... grumble, grumble
			if($strMonth == 'September'){
				$intTruncLen = 4;
			} else {
				$intTruncLen = 3;
			}

			$strMonth = substr($strMonth,0,$intTruncLen) . '.';
		}

		return $strMonth;
	}
}