<?php
/**
 * Retrieves events from the Localist instance of the MU calendar and converts them to consumable event objects/arrays
 *
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 * @version 1.1
 */

namespace Mizzou\CalendarTranslator;

use Mizzou\CalendarTranslator\CalendarException as CalendarException;

require_once dirname(__FILE__). DIRECTORY_SEPARATOR .'CalendarException.php';

/**
 * Each system needs event data formatted/translated in a different manner. This class handles all of the heavy lifting
 * of retrieving the calendar event objects from localist, and allows the implementing class to handle the specifics of
 * how it needs the event data translated for its needs.
 */
abstract class AbstractTranslator
{
	/**
	* Default options. Can be overridden via passed in options during construction
	*
	* Speaker seems to be a standard request.  Anything else?
	*
	* @var array
	*/
	protected $aryOptions = array(
		'date_format'       =>'Y-m-d',
		'calendar_feed'     => "http://calendar.missouri.edu/api/2/events",
		'per_page_limit'    => 10,
        'default_timezone'  => 'America/Chicago', // @see _ensureTimezoneSet(),
        'timezone'          => '', // so we can change the timezone from the default for events
	);

    /**
     * Valid methods of querying the localist API
     * @var array
     */
    protected $aryValidMethods = array(
		'search'    => 'search',
		'department'=> 'group_id', //per localist, this should be used for departments
		'type'      => 'type',
		'group'     => 'group_id',
		'keyword'   => 'keyword'

	);

	/**
	 * Regex pattern to make sure the date we've been given matches the pattern
	 * Y-m-d.  Of course, if the user changes the date format in aryOptions[date_format]
	 * this pattern becomes irrelevant. Maybe move this into aryOptions as well?
	 * @var string
	 */
	 # Metzenj: I agree, the date pattern should be optionally changeable along with date format.
	protected $strPatternDateRegEx = '/\d{4}-\d{2}-\d{2}/';



	/**
    * The class constructor. Returns instance of the Translator object.
    *
	* @param array $aryOptions
	*/
	protected function __construct($aryOptions=array())
    {
		$this->_setOptions($aryOptions);
	}

    // JAM.
    // NOTE: At this time the Localist API doesn't even appear to support searching filters by name.
    public function getFilterList()
    {
        $strCalendarLink = $this->aryOptions["calendar_feed"] . "/filters/";

        $strResponse = file_get_contents($strCalendarLink);
        if(!$strResponse)
            throw new CalendarException("unable to retrieve content from service, using URL $strCalendarLink");

        $objRawData = json_decode($strResponse);
        if(!$objRawData)
            throw new CalendarException('unable to decode return from service. Return was: '.var_export($objRawData,TRUE));

        return $objRawData;
    }

	/**
	* Retrieves the list of calendar events based on the method and terms passed
	* in via $aryOptions
	*
	* valid options are
	* <code>
	*   method = String. Valid options are: search, department, type, group or keyword
	*   term = String. For keywords and type, separate each keyword type with a space
	*   start = String. Start date for events. format should match date format defined in options for class constructor
	*   end = String. End date for events. format should match date format defined in options for class constructor
	*   days = Integer. Number of days into the future to search. If days is included, start and end are ignored
	*   pagination = Boolean. If true, the next two options are necessary
	*      page = the page of return to start on. Defaults to 1
	*      perpage = number of events per page. Defaults to per_page_limit as defined in class constructor options
	* </code>
	*
	* @param array $aryOptions
	* @return array list of matching events
    * @throws CalendarException
	*/
	public function retrieveCalendarItems($aryOptions)
    {
		$aryDefaults = array(
			'method'        => '',
			'term'          => '',
			'pagination'    => FALSE,
			'start'         => date($this->aryOptions['date_format']),
			'end'           => date($this->aryOptions['date_format'],strtotime('+1 year')),
			'days'          => 0
		);

		$boolReturnPagination = false;

		$aryOptions = array_merge($aryDefaults,$aryOptions);

		$strFeedURL = $this->aryOptions['calendar_feed'];

		try{
			if(in_array($aryOptions['method'], array_keys($this->aryValidMethods)) && $aryOptions['term'] != ''){
						//we've got the basics, now we gotta do some checks
						/**
						 * METHOD CHECKS
						 */
						 # Metzenj: Good error checking.
						if(in_array($aryOptions['method'], array('keyword','type')) && !is_array($aryOptions['term'])){
							$aryOptions['term'] = explode(' ', $aryOptions['term']);
						}

						switch ($aryOptions['method']){
							case 'search':
								$strFeedURL .= '/search?search='.  urlencode($aryOptions['term']);
								break;
							case 'type':
                                //break intentionally omitted
							case 'keyword':
								if(!is_array($aryOptions['term'])){
									$aryOptions['term'] = explode(' ', $aryOptions['term']);
								}

								foreach ($aryOptions['term'] as $intTermKey=>$strTermValue){
                                    $aryOptions['term'][$intTermKey] = $this->aryValidMethods[$aryOptions['method']].'[]='.urlencode($strTermValue); // JAM.
								}

								$strFeedURL .= '?'. implode('&', $aryOptions['term']);

								break;
							default:
								$strFeedURL .= '?'.  $this->aryValidMethods[$aryOptions['method']].'[]='.urlencode($aryOptions['term']);
								break;
						}

						/**
						 * DATE CHECKS
						 */
						//if they've included days, then we dont need start and end
						if($aryOptions['days'] != 0 && is_numeric($aryOptions['days'])){
                            // unset($aryOptions['start']); JAM.
                            unset($aryOptions['end']);
                            // $strFeedURL .='&days='.$aryOptions['days'];
                            $aryOptions['start'] = $this->_verifyDate($aryOptions['start']);
                            $strFeedURL .='&start='.$aryOptions['start'].'&days='.$aryOptions['days'];
						} else {
							unset($aryOptions['days']);
							//we need start and end, so now we need to make sure they are formatted correctly
							$aryOptions['start'] = $this->_verifyDate($aryOptions['start']);
							$aryOptions['end']  = $this->_verifyDate($aryOptions['end']);
							$strFeedURL .='&start='.$aryOptions['start'].'&end='.$aryOptions['end'];

						}

						/**
						 * PAGINATION CHECK
						 */
						if($aryOptions['pagination']){
							if(!isset($aryOptions['page'])){
								$aryOptions['page'] = 1;
							}

							if(!isset($aryOptions['perpage'])){
								$aryOptions['perpage'] = $this->aryOptions['per_page_limit'];
							}

							if(is_numeric($aryOptions['page'])){
								$boolReturnPagination = true;
								$strFeedURL .= '&pp='.$aryOptions['perpage'].'&page='.$aryOptions['page'];
							} else {
								throw new CalendarException('The value given for page is invalid');
							}
						}

                        /**
                         * EVERYTHING ELSE (JAM)
                         * Pass through k/v pairs of anything else you want to pass to the API.
                         * @todo: Abstract into a function for use elsewhere.
                         */
                        if(isset($aryOptions["data"]) && is_array($aryOptions["data"])){
                            // NOTE: Something is slightly funky here where booleans are being converted to ints, also happens with a simple loop.
                            $strFeedURL .= "&" . http_build_query($aryOptions["data"]);
                        }

					} else {
						throw new CalendarException("The method provided is not a valid method ({$aryOptions['method']}) or term was empty.");
					}
		} catch (CalendarException $e){
			$e->log();
			//return an empty array so to the user it looks like there are no events
			return array();
		}

		$aryCalendarData = $this->_retrieveDataFromCalendar($strFeedURL,$boolReturnPagination);
		return $aryCalendarData;

	}

	/**
	 * Converts the date to the format as defined in this->aryOptions[date_format]
	 *
	 * @param string $strDate
	 * @return string
	 * @throws CalendarException
	 */
	protected function _verifyDate($strDate)
    {
		if(1 != preg_match($this->strPatternDateRegEx, $strDate)){
			//date isn't properly formatted
			try{
				if(FALSE !== $mxdDate = strtotime($strDate)){
					$strDate = date($this->aryOptions['date_format'],$mxdDate);
				} else {
					throw new CalendarException('was unable to determine a date from the string provided: ' . htmlentities($strDate,ENT_QUOTES,'UTF-8'));
				}
			} catch (CalendarException $e){
				$e->log();
			}
		}

		return $strDate;
	}

	/**
	 * Retrieves the list of events from the Localist API
	 *
	 * @param string $strCalendarLink Constructed URL to the localist API
	 * @param boolean $boolReturnPagination include the pagination details from the api in the return
	 * @return array
	 * @throws CalendarException
	 */
	protected function _retrieveDataFromCalendar($strCalendarLink,$boolReturnPagination=false)
    {
		$aryReturn = array();
		try{
			//throw new CalendarException('URL we are using: ' . $strCalendarLink);
			if(FALSE !== $strReturn = file_get_contents($strCalendarLink)){
				//we've got the feed, now let's try to parse it
				if(NULL !== $objRawData = json_decode($strReturn)){
					//ok, now we've got something we can use
					//throw new CalendarException('Are we getting anything from the API? Raw Return: ' . PHP_EOL.  var_export($objRawData,true));

				   if(isset($objRawData->status) && $objRawData->status == 'error'){
					   throw new CalendarException('API returned error status. Message: '.$objRawData->error);
                   // JAM: This method cannot currently be all-purpose. There might be situations in which we want to get something other than events (filters, for example).
				   } elseif(!isset ($objRawData->events)) {
					   throw new CalendarException('Events property is not set in the return from the API.  Here is the raw return: '.PHP_EOL.  var_export($objRawData,true));
				   } else {
					   $aryReturn['events'] = $this->_translateEvents($objRawData->events);
				   }
				} else {
					throw new CalendarException('unable to decode return from service. Return was: '.var_export($objRawData,TRUE));
				}
			} else {
				throw new CalendarException("unable to retrieve content from service, using URL $strCalendarLink");
			}
		} catch (CalendarException $e){
			$e->log();
			//let processing continue but return an empty array so it appears the same as no events
			return array();
		}

		if($boolReturnPagination && isset($objRawData->page)){
			$aryReturn['pagination'] = $objRawData->page;
		}

		return $aryReturn;
   }




	/**
	* Loops through the list of events and passes each one to the _translate_event method defined by the implementing
    * class
	*
	* @param array $aryEvents
	* @return array
	*/
	protected function _translateEvents($aryEvents)
    {
        $aryReturn = array();
		if(count($aryEvents) > 0){
            /**
             * Switch to the passed in timezone in case the user needs the dates based on a different timezone from what
             * the server might be set to.  Why?  Well, as one example @see https://wordpress.org/support/topic/why-does-wordpress-set-timezone-to-utc
             */
            if($this->aryOptions['timezone'] != '' && $this->aryOptions['timezone'] != $strOldTimeZone = date_default_timezone_get()){
                date_default_timezone_set($this->aryOptions['timezone']);
                $boolResetTimezone = true;
            }

			foreach($aryEvents as $objEventP){
				$objEvent = $objEventP->event;

				$aryReturn[$objEvent->event_instances[0]->event_instance->id] = $this->_translateEvent($objEvent);
			}

            if(isset($boolResetTimezone) && $boolResetTimezone){
                date_default_timezone_set($strOldTimeZone);
            }
		}

		return $aryReturn;
	}

	/**
	 * Should perform the actual translation of the event object from the API
	 * to whatever format is needed
	 *
	 * @param object $objEvent
	 */
	abstract protected function _translateEvent($objEvent);

	/**
	* parses the options given to the constructor with our defaults
	*
	* @param array $aryOptions
    * @return void
	*/
	protected function _setOptions($aryOptions)
    {
		$this->aryOptions = array_merge($this->aryOptions,$aryOptions);
	}
}

?>
