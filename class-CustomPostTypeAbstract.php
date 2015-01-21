<?php
/**
* Abstract class to be extended for dealing with custom post data
* 
* @package WordPress
* @subpackage theme-helper
* @category theme
* @category class
* @author Paul F. Gilzow, Web Communications, University of Missouri
* @copyright 2013 Curators of the University of Missouri
* @version 201303281212
 *
 * @deprecated
 * @todo delete
* 
*/
abstract class CustomPostType {
    /**
    * Stores the base.php data we need to access post reformatting
    * 
    * @var array
    */
    var $aryData                = array();
    /**
    * stores the ORIGINAL data we need to access
    * 
    * @var mixed
    */
    var $aryOriginalData        = array(); 
    /**
    * Wordpress Post ID
    * 
    * @var integer
    */
    var $intPostID              = null;
    /**
    * Prefix of data to be removed in the formatting stage
    * 
    * @var string
    */
    var $strDataFieldPrefix     = null;
    /**
    * Default message to be displayed if a field of data is requested but isnt set/found
    * 
    * @var string
    */
    var $strDataNotFoundMessage = 'Cant find what you asked for'; 
    /**
    * The message to prepend to any logging output
    * 
    * @var string
    */
    var $strDebugMessagePrefix  = '';
    /**
    * contains a mapping of wordpress properties -> object properties
    * 
    * This allows us to change something like post_title to name (for a department)
    * 
    * @var array
    */
    var $aryWPMapping = array();
    /**
    * contains a list of any errors that were encountered
    * 
    * @var array
    */
    var $error_messages = array();
    
    /**
    * Just a shortcut to see if the error_messages contains entries.
    * 
    * @var boolean
    */
    var $boolError = false;
    
    /**
    * Main function to retrieve data from wordpress
    * 
    */
    abstract protected function _retrieve_wp_data();

    /**
    * Magic get so lower classes can access inaccessible properties
    * 
    * @param mixed $mxdProperty
    */
    public function __get($mxdProperty){
        return $this->get($mxdProperty);     
    }
    
    /**
    * Magic set so lower classes can set inaccessible properties
    * 
    * @param mixed $mxdKey
    * @param mixed $mxdValue
    */
    public function __set($mxdKey,$mxdValue){
        $this->add_data($mxdKey,$mxdValue);    
    }
    
    /**
    * magic isset so lower classes can test for existance of inaccessible properties
    * 
    * @param mixed $mxdProperty
    * @return boolean
    */
    public function __isset($mxdProperty){
        return $this->is_set($mxdProperty);
    }
  
    /**
    * Checks if a property in $this->aryData is set
    * 
    * @param mixed $mxdProperty
    * @return boolean
    */
    public function is_set($mxdProperty){
        return isset($this->aryData[$mxdProperty]);
    }
    
    /**
    * Echoe's out the requested field/property
    * 
    * @param mixed $mxdProperty Property to retrieve from the data array and output
    * @return void
    * @todo change to protected and let lower order classes use it to output specific?
    */
    public function output($mxdProperty){
        echo $this->get($mxdProperty);
    }
    
    /**
    * Returns a proprty from $this->aryData. If requested property, returns current value of $this->strDataNotFoundMessage
    * 
    * @param mixed $mxdProperty
    */
    public function get($mxdProperty){
        if($this->is_set($mxdProperty)){
            return $this->aryData[$mxdProperty];
        } else {
            return $this->strDataNotFoundMessage;
        }   
    }
    
    /**
    * Checks to see if any member of the passed array is currently set in the object
    * 
    * We have sections where if ANY piece in a section is set, we have to create the section so we can output that one piece
    * 
    * @param array $aryGroupMembers
    * @return bool
    */
    public function member_of_group_set(array $aryGroupMembers){
        $i = count($aryGroupMembers);
        $boolMemberFound = false;
        $j = 0;
        while(!$boolMemberFound && $j<$i){
            if($this->is_set($aryGroupMembers[$j])){
                $boolMemberFound = true;
            }
            ++$j;
        }
        
        return $boolMemberFound;        
    }
    
    /**
    * adds data to the $this->aryData array
    * 
    * @param mixed $mxdKey
    * @param mixed $mxdData
    */
    public function add_data($mxdKey,$mxdData){
        $this->aryData[$mxdKey] = $mxdData;
    }  
    
    /**
    * Logs data to the debug.log file
    * 
    * @param mixed $mxdVariable variable to inspect
    * @param string $strPrependMessage message you want to output before variable inspection
    * @param bool $boolBackTraced include backtrace?
    * @param array $aryDetails additional details you want included. valid keys are: line, func, file
    */
    protected function _log( $mxdVariable, $strPrependMessage = null, $boolBackTrace = null, array $aryDetails = null ) {
        $boolBackTrace = (is_bool($boolBackTrace)) ? $boolBackTrace : false;
        $aryDetails = (is_array($aryDetails)) ? $aryDetails : array();
        if( defined('WP_DEBUG') && WP_DEBUG === true ){
            $strMessage = $this->strDebugMessagePrefix;
            
            $strMessage .= ' Variable is of type '.gettype($mxdVariable).'. ';
            
            if(count($aryDetails) > 0){
                
                if(isset($aryDetails['line'])){
                    $strMessage .= 'At line number ' . $aryDetails['line'] . ' ';
                }

                if(isset($aryDetails['func'])){
                    $strMessage .= 'inside of function ' . $aryDetails['func'] . ' ';
                }

                if(isset($aryDetails['file'])){
                    $strMessage .= 'in file ' . $aryDetails['file'] .' ';
                }

                $strMessage .= PHP_EOL;
            }

            if(!is_null($strPrependMessage)) $strMessage .= $strPrependMessage.' ';

            if( is_array( $mxdVariable ) || is_object( $mxdVariable ) ){
                $strMessage .= PHP_EOL . var_export($mxdVariable,true);
            } else {
                $strMessage .= $mxdVariable;
            }

            if($boolBackTrace){
                $aryBackTrace = debug_backtrace();

                $strMessage .= PHP_EOL.'Contents of backtrace:'.PHP_EOL.var_export($aryBackTrace,true).PHP_EOL;          
            }

            error_log($strMessage);
        }
    }
    
    /**
    * Have we encountered an error 
    * 
    * @return boolean
    */
    public function is_error(){
        return $this->boolError;
    }
    
    /**
    * Adds an error message to our internal error log
    * 
    * @param string $strMessage
    */
    public function add_error($strMessage){
        $this->boolError = true;
        $this->error_messages[] = $strMessage;
    }
}
?>