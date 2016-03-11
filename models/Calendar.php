<?php
/**
 * Translates the data received from Localist to the format we need for display
 */
namespace MizzouMVC\models;
use Mizzou\CalendarTranslator\AbstractTranslator as AbstractTranslator;

/**
 * @todo seriously need to look into autoloaders
 */
require_once dirname(dirname(__FILE__)).'/helpers/calendar/Mizzou/CalendarTranslator/AbstractTranslator.php';
/**
 * Translates the data received from Localist to the format we need for display
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category model
 * @author Paul Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 *
 */
class Calendar extends AbstractTranslator {

    /**
     * Sets options
     * @param array $aryOptions
     */
    public function __construct($aryOptions=array())
    {
        _mizzou_log(func_get_args(),'options handed to __construct',false,array('line'=>__LINE__,'file'=>__FILE__));
	    /**
         * @todo this functionality should PROBABLY be pushed back up to the AbstractClass, but we'll leave here for now
         */
        if(isset($aryOptions['calendar_exception_email']) && !defined('CALENDAR_EXCEPTION_EMAIL')){
            define('CALENDAR_EXCEPTION_EMAIL',$aryOptions['calendar_exception_email']);
            unset($aryOptions['calendar_exception_email']);
        }

        /**
         * @todo same as above
         */
        if(isset($aryOptions['calendar_exception_log']) && !defined('CALENDAR_EXCEPTION_LOG')){
            define('CALENDAR_EXCEPTION_LOG',$aryOptions['calendar_exception_log']);
            unset($aryOptions['calendar_exception_log']);
        }

        /**
         * now we need to strip out any of the options that have been passed in that arent keys in the default
         */

        $aryOptions = array_intersect_key($aryOptions,$this->aryOptions);

        parent::__construct($aryOptions);
    }

    /**
     * Translates the data returned from Localist into an object we can use
     * @param $objEvent Event object instance from Localist
     * @return stdClass
     */
    protected function _translateEvent($objEvent)
    {
        $objReturn = new \stdClass();
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
        /**
         * @todo should probably add a time format option to the AbstractTranslator
         */
        $objReturn->StartTime       = date('g:i a',$objReturn->Start);
	    $objReturn->EndMonth        = date('F',$objReturn->End);
	    $objReturn->EndAPMonth      = $this->_getAPMonth(($objReturn->EndMonth));
        $objReturn->EndTime         = date('g:i a',$objReturn->End);
	    $objReturn->StartDay        = date('j',$objReturn->Start);
	    $objReturn->StartDayofTheWeek= date('l',$objReturn->Start);
	    $objReturn->EndDay          = date('j',$objReturn->End);
	    $objReturn->EndDayofTheWeek = date('l',$objReturn->End);
	    $objReturn->StartYear       = date('Y',$objReturn->Start);
	    $objReturn->EndYear         = date('Y',$objReturn->End);

	    if(isset($this->aryOptions['excerpt_length'] ) && is_numeric($this->aryOptions['excerpt_length']) && is_int($intLength = intval($this->aryOptions['excerpt_length']))){
			$objReturn->Excerpt = $this->_calculateExcerpt($objReturn->DescriptionText,$intLength);
		} else {
		    $objReturn->Excerpt = $objReturn->DescriptionText;
	    }

        return $objReturn;
    }

	/**
	 * Reformats the Month to AP style date format based on the timestamp of the post
     * @param string $strMonth
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

	/**
	 * Creates an excerpt from an event description by truncating the description at $intLength length
	 * @param string $strDescription
	 * @param int $intLength maximum length of description before we truncate
	 *
	 * @return string
	 */
	protected function _calculateExcerpt($strDescription='',$intLength=0)
	{
		if($strDescription != '' && $intLength > 0 && strlen($strDescription) > $intLength){
			$strDescription = wordwrap($strDescription,$intLength);
			if(FALSE !== $intPos = strpos($strDescription,"\n")){
				$strDescription = substr($strDescription,0,$intPos);
			}
		}

		return $strDescription;
	}

    /**
     * Switches the timezone to the local timezone before we translate the events since wordpress switches the timezone to GMZ
     * @param array $aryEvents
     * @return array
     */
    protected function _translateEvents($aryEvents)
    {
        $boolTimeZoneChanged = false;
        $strOldTimeZone = date_default_timezone_get();
        $strNewTimeZone = get_option('timezone_string');
        if('' != $strNewTimeZone){
            date_default_timezone_set($strNewTimeZone);
            $boolTimeZoneChanged = true;
        }

        $aryReturn = parent::_translateEvents($aryEvents);

        if($boolTimeZoneChanged){
            date_default_timezone_set($strOldTimeZone);
        }

        return $aryReturn;

    }
}