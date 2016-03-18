<?php
/**
 * Exception handler for the Calendar Translator class
 *
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 * @version 1.1
 *
 * @todo should we make either strAdminEmail/strLogFile static, or add a static
 * method for setting the configuration options instead of relying on constants?
 */

namespace Mizzou\CalendarTranslator;

use Exception;

/**
 *
 *
 * Class CalendarException
 * @package Mizzou\CalendarTranslator
 */
class CalendarException extends Exception
{
	protected $strAdminEmail = null;
	protected $strLogFile = null;
	protected $boolLog = false;

    /**
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message, $code = 0, Exception $previous = NULL) {
		$this->_setConfigurations();
		parent::__construct($message, $code, $previous);
	}

    /**
     *
     */
    private function _setConfigurations(){
		//echo 'setting exception parameters internally... <br />',PHP_EOL;
		$boolMethodDefined = false;
		if(defined('CALENDAR_EXCEPTION_EMAIL')){
			$this->strAdminEmail = CALENDAR_EXCEPTION_EMAIL;
			$boolMethodDefined = true;
		}

		try{
			if(defined('CALENDAR_EXCEPTION_LOG')){
				if(FALSE === strpos(CALENDAR_EXCEPTION_LOG, DIRECTORY_SEPARATOR)){
					//they just gave us a file name. no worries
					$strLog = dirname(__FILE__) . DIRECTORY_SEPARATOR . CALENDAR_EXCEPTION_LOG;
				} else {
					$strLog = CALENDAR_EXCEPTION_LOG;
				}

				if(touch($strLog)){
					$this->strLogFile = $strLog;
					$this->boolLog = true;
					$boolMethodDefined = true;
				} elseif(!is_null($this->strAdminEmail)) {
					$strMsg = 'You gave me a file to log exceptions to, but I wasn\'t able to create or access the file. File and path given was ' . CALENDAR_EXCEPTION_LOG;
					$this->mail($strMsg);
				}
			}

			if(!$boolMethodDefined){
				throw new Exception('An administrator email or log location has to be set or defined in order to notify you of errors.');
			}
		} catch (Exception $e){
			echo 'FATAL: ', $e->getMessage();
			exit;
		}
	}

    /**
     * Logs the error message to either email/log file based on configuration options
     */
    public function log(){
		$strContents = 'An exception occurred at ' . date('r').PHP_EOL;
		$strContents .= 'Line ' . $this->getLine() . ' in file ' . $this->getFile() . PHP_EOL;
		$strContents .= $this->getMessage().PHP_EOL;
		$strContents .= $this->getTraceAsString(). PHP_EOL . PHP_EOL;

		if($this->boolLog){
			$this->write_log($strContents);
			$strMsg = 'Exception encountered. Check log file for details.';
		} else {
			$strMsg = $strContents;
		}

		$this->mail($strMsg);
	}

    /**
     * Emails the error message
     *
     * @param string $strMessage
     */
    protected function mail($strMessage){
		mail($this->strAdminEmail,'Notification from Calendar Exception',$strMessage);
	}

    /**
     * Writes the error message to the log file
     *
     * @param string $strContents
     */
    protected function write_log($strContents){
		$mxdWrite = file_put_contents($this->strLogFile, $strContents, FILE_APPEND);
		if(FALSE === $mxdWrite && !is_null($this->strAdminEmail)){
			$strMsg = 'I tried to write the exceptions to the log, but was unable to. You should check the file for permissions. Here is the data i was supposed to log: ' . PHP_EOL . $strContents;
            $this->mail($strMsg);
		}
	}

}
?>
