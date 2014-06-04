<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/4/14
 * Time: 1:00 PM
 */

class PostBase
{
    /**
     * Stores the base.php data we need to access post reformatting
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


    public function __construct($mxdPost)
    {
        if(is_object($mxdPost) && $mxdPost instanceof WP_Post){
            $objPost = $mxdPost;
        } elseif(is_numeric($mxdPost)){
            if(null !== $objPost = get_post($mxdPost)){
                $objPost = get_post($mxdPost);
            } else {
                $strLogMsg = 'we were given a post id, but wordpress returned a null.';
                _mizzou_log($mxdPost,$strLogMsg,false,array('func'=>__FUNCTION__));
            }
        } else {
            /**
             * It's not an instance of the WP_Post class, and it isnt a post id so...
             * @todo throw an exception here?
             */
            $strLogMsg = 'We werent given a post id, or an instance of WP_Post. Not sure what to do';
            _mizzou_log($mxdPost,$strLogMsg,false,array('func'=>__FUNCTION__));
            $objPost = new stdClass();
        }

        $this->objOriginalPost = $objPost;
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
     * Returns a property from $this->aryData. If requested property, returns current value of $this->strDataNotFoundMessage
     *
     * @param mixed $mxdProperty
     * @return mixed
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
    public function memberOfGroupSet(array $aryGroupMembers){
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