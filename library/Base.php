<?php
/**
 * Base class contain magic methods
 */

class Base {
    /**
     * Stores the base data we need to access post reformatting
     *
     * @var array
     */
    protected  $aryData                = array();
    /**
     * stores the ORIGINAL data we need to access
     *
     * @var mixed
     */
    protected  $aryOriginalCustomData   = array();

    protected $objOriginalPost = null;

    protected $aryBaseKeys              = array();
    /**
     * Default message to be displayed if a field of data is requested but isnt set/found
     *
     * @var string
     */
    public  $strDataNotFoundMessage = 'Cant find what you asked for';
    /**
     * The message to prepend to any logging output
     *
     * @var string
     */
    public  $strDebugMessagePrefix  = '';
    /**
     * contains a list of any errors that were encountered
     *
     * @var array
     */
    public  $error_messages = array();

    /**
     * Just a shortcut to see if the error_messages contains entries.
     *
     * @var boolean
     */
    public  $boolError = false;


    public function __construct()
    {

    }

    /**
     * Magic get so lower classes can access inaccessible properties
     *
     * @param mixed $mxdProperty
     * @return mixed
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
     * Echo's out the requested field/property
     *
     * @param mixed $mxdProperty Property to retrieve from the data array and output
     * @return void
     * @todo change to protected and let lower order classes use it to output specific?
     */
    public function output($mxdProperty){
        echo $this->get($mxdProperty);
    }

    /**
     * Returns a property from $this->aryData. If requested property, returns current value of $this->strDataNotFoundMessage
     *
     * @param mixed $mxdProperty
     * @return mixed
     */
    public function get($mxdProperty){
        if($this->is_set($mxdProperty)){
            return $this->aryData[$mxdProperty];
        } else {
            //return $this->strDataNotFoundMessage;
            return '';
        }
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

    /**
     * Captures the contents of a function that normally echos directly
     *
     * @param $strCallBack
     * @param $aryOptions
     * @return string
     */
    protected function _captureOutput($strCallBack,$aryOptions=array())
    {
        $strReturn = '';
        if(function_exists($strCallBack) && is_callable($strCallBack)){
            ob_start();
            call_user_func_array($strCallBack,$aryOptions);
            $strReturn = ob_get_contents();
            ob_end_clean();
        } else {
            /**
             * What to do, what to do...
             * @todo throw exception?
             */
            $strMsg = 'You asked me to call ' . $strCallBack . ' but it isnt available or callable. You also gave me '
                    . 'the following options';
            _mizzou_log($aryOptions,$strMsg,false,array('func'=>__FUNCTION__));
        }

        return $strReturn;
    }
} 