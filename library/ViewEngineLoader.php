<?php
/**
 * Sets up and initializes our template engine render.  Currently this is Twig, but later would like to be able to
 * expand it.
 */
namespace MizzouMVC\library;

/**
 * Sets up and initializes our template engine render.  Currently this is Twig, but later would like to be able to
 * expand it.
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category library
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */
class ViewEngineLoader {

    /**
     * @var null no longer used
     * @deprecated not used
     */
    protected $objViewEngineEnvironmentLoader = null;
    /**
     * @var null|\Twig_Environment internal storage of the view engine
     */
    protected $objViewEngine = null;
    /**
     * @var null|ViewEngineLoader static instance of our Loader
     */
	protected static $objInstance = null;
    /**
     * @var string|null server path to the framework
     */
    protected $strFrameworkDir = null;
    /**
     * @var null|string server path to the parent theme
     */
    protected $strThemeDir = null;
    /**
     * @var null|string server path to the child theme
     */
    protected $strChildThemeDir = null;

    /**
     * Sets viw directory locations, whether to enable caching, loads custom view filters/functions/tests
     * @param string $strFrameworkDir Server path location of the framework
     * @param string $strThemeDir Server path location of the (parent) theme
     * @param null|string $strChildThemeDir server path of the child theme, if applicable
     */
    private function __construct($strFrameworkDir,$strThemeDir,$strChildThemeDir=null){
        $this->strFrameworkDir = $strFrameworkDir;
        $this->strThemeDir = $strThemeDir;
        $this->strChildThemeDir = $strChildThemeDir;

        //initiate our twig environment loader
        //$this->objViewEngineEnvironmentLoader = new Twig_Loader_Filesystem($this->_determineViewDirectories());

        $boolAutoReload = (defined('WP_DEBUG')) ? WP_DEBUG : false;

        $aryViewEngineOptions = array(
            'cache'         => $this->_determineViewCacheLocation(),
            'auto_reload'   => $boolAutoReload,
            'autoescape'    => false,
        );

        //initiate our view engine
        //$this->objViewEngine = new Twig_Environment($this->objViewEngineEnvironmentLoader,$aryViewEngineOptions);
        $this->objViewEngine = new \Twig_Environment(new \Twig_Loader_Filesystem($this->_determineViewDirectories()),$aryViewEngineOptions);
        //load up our custom view filters
        $this->_loadViewEngineFilters();
        //load up our custom view functions
        $this->_loadViewEngineFunctions($this->strFrameworkDir);
        //load up our custom tests
        $this->_loadTests();
    }

    /**
     * Creates instance and/or returns stored instance
     * @param string $strFrameworkDir server path of framework
     * @param string $strThemeDir server path of main/parent theme
     * @param string $strChildThemeDir server path of child theme, if applicable
     * @return null|Twig_Environment
     */
    public static function getViewEngine($strFrameworkDir,$strThemeDir,$strChildThemeDir=null)
    {
        if(is_null(self::$objInstance) || is_null(self::$objInstance->objViewEngine)){
            self::$objInstance = new ViewEngineLoader($strFrameworkDir,$strThemeDir,$strChildThemeDir);
	    }

	    //return self::$objViewEngine;
	    return self::$objInstance->objViewEngine;
    }

    /**
     * Determines where the view directories are located to hand off the template renderer
     * @return array list of directories
     */
    protected function _determineViewDirectories()
    {
        $aryDirectories = array();
        if(!is_null($this->strChildThemeDir) && $this->strChildThemeDir != $this->strThemeDir){
            $aryDirectories[] = $this->strChildThemeDir;
        }

        $aryDirectories[] = $this->strThemeDir;
        $aryDirectories[] = $this->strFrameworkDir;

        foreach ($aryDirectories as $intDirKey => $strDirectory) {
            $aryDirectories[$intDirKey] = $strDirectory . 'views' . DIRECTORY_SEPARATOR;
        }

        return $aryDirectories;
    }

    /**
     * Determines where we need to store the cache from our template rendering engine
     *
     * @return string cache location
     */
    protected function _determineViewCacheLocation()
    {
        $strViewCacheLocation = '';

        /**
         * @todo I believe VIEW_CACHE_LOCATION constant is a left-over from an earlier version. Remove?
         */
        if(defined('VIEW_CACHE_LOCATION')){
            $strViewCacheLocation = VIEW_CACHE_LOCATION;
        } else {
            //let's see if we have a cache directory
            $strPossibleCacheLocation = $this->strThemeDir.'cache'.DIRECTORY_SEPARATOR;
            if(!is_dir($strPossibleCacheLocation) && !file_exists($strPossibleCacheLocation)){
                //we need to make a directory
                if(mkdir($strPossibleCacheLocation,'0755')){
                    $strViewCacheLocation = $strPossibleCacheLocation;
                }
            } elseif(!is_writable($strPossibleCacheLocation)) {
                //it exists but we cant write to it...
                if(chmod($strPossibleCacheLocation,'0755')){
                    $strViewCacheLocation = $strPossibleCacheLocation;
                }
            } else {
                $strViewCacheLocation = $strPossibleCacheLocation;
            }

        }

        if(''==$strViewCacheLocation){
            /**
             * @todo we need a more elegant way of handling this
             */
            echo 'view cache location is not available or is not writeable. I can\'t continue until you fix this. ';exit;
        } else {
            return $strViewCacheLocation;
        }
    }

    /**
     * Loads custom Twig filters
     * @return void
     * @todo allow themes to create and pass in their own twig filters?
     */
    protected function _loadViewEngineFilters()
    {
        $objTwigDebug = new \Twig_SimpleFilter('var_export',function($string){
            return PHP_EOL.'<pre>'.var_export($string,true).'</pre>'.PHP_EOL;
        });

        $objTwigSanitize = new \Twig_SimpleFilter('sanitize',function($strString){
            return sanitize_title_with_dashes($strString);
        });

        /**
         * Given a timestamp, a string formatted date, or a full month, we'll convert it to an AP-style month
         */
        $objTwigAPMonth = new \Twig_SimpleFilter('apmonth',function($mxdDate){
            $intTimeStamp = null;
            $strMonth = null;
            $strReturn = $mxdDate;

            if(is_string($mxdDate)){
                //we have some time of string representation of a date
                $aryCalendarInfo = cal_info(0);
                //do we have a full month?
                if(in_array($mxdDate,$aryCalendarInfo['months'])){
                    //ok we have our month
                    $strMonth = $mxdDate;
                } else {
                    $intTimeStamp = strtotime($mxdDate);
                }
            } elseif(is_numeric($mxdDate)) {
                //we'll assume they gave us a timestamp
                $intTimeStamp = $mxdDate;
            }

            if(!is_null($intTimeStamp) && false !== $intTimeStamp){
                $strMonth = date('F',$intTimeStamp);
            }

            if(!is_null($strMonth)){
                if(strlen($strMonth) > 5){ //stoopid september... grumble, grumble
                    if($strMonth == 'September'){
                        $intTruncLen = 4;
                    } else {
                        $intTruncLen = 3;
                    }

                    $strReturn = substr($strMonth,0,$intTruncLen) . '.';
                } else {
                    $strReturn = $strMonth;
                }
            }

            return $strReturn;

        });


        $this->objViewEngine->addFilter($objTwigDebug);
        $this->objViewEngine->addFilter($objTwigSanitize);
        $this->objViewEngine->addFilter($objTwigAPMonth);
    }

    /**
     * Loads custom Twig functions
     * @param string $strFrameWorkDir server path to the framework
     * @return void
     * @todo allow themes to create and pass in their own twig functions?
     */
    protected function _loadViewEngineFunctions($strFrameWorkDir)
    {
        $this->objViewEngine->addFunction('subview',new \Twig_SimpleFunction('subview',function($mxdControllerName,$aryContext,$aryData = array()) use ($strFrameWorkDir){
            //_mizzou_log($mxdControllerName,'the controller we were asked to get',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
            //_mizzou_log($aryContext,'the context data that was passed in',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
            $strController = '';

            if(is_array($mxdControllerName)){
                $aryControllerNameParts = $mxdControllerName;
            } elseif(is_string($mxdControllerName)){
                $aryControllerNameParts = explode(' ',trim($mxdControllerName));
            } else {
                /**
                 * @todo should this be changed to a try catch with an exception?
                 * We're expecting a string (or an array), so getting something else WOULD be an exception
                 */
                //_mizzou_log($mxdControllerName,'what the heck... what were we given instead of the name for a controller?',false,array('FUNC'=>__FUNCTION__,'line'=>__LINE__,'file'=>__FILE__));
                $aryControllerNameParts = array();
            }
            $strControllerName = implode('-',$aryControllerNameParts) . '.php';
            //_mizzou_log($strControllerName,'the controller name before we run locate template',false,array('func'=>__FUNCTION__,'file'=>__FILE__,'line'=>__LINE__));
            if(count($aryData) != 0){
                extract($aryData);
            }

            if(!is_null($strFrameWorkDir) && '' == $strController = locate_template($strControllerName)){
                //_mizzou_log(null,'we didnt find a controller in a parent or child theme. gonna look in the plugin framework',false,array('line'=>__LINE__,'file'=>__FILE__));
                //ok, we didnt find a controller in a parent or child theme, what about the plugin?
                if(is_readable($strFrameWorkDir.$strControllerName)){
                    $strController = $strFrameWorkDir.$strControllerName;
                } else {
                    _mizzou_log($strFrameWorkDir.$strControllerName,'we couldnt find this controller in the framework either',false,array('line'=>__LINE__,'file'=>__FILE__));
                }
            }
            //_mizzou_log($strController = locate_template($strControllerName),'direct return from locate_template',false,array('file'=>__FILE__,'line'=>__LINE__));
            //_mizzou_log($strController,'the controller name before we try to require it',false,array('func'=>__FUNCTION__,'file'=>__FILE__,'line'=>__LINE__));
            if('' != $strController){
                require_once $strController;
            }
        }));
    }

    /**
     * Loads custom Twig tests
     * @return void
     * @todo allow themes to create and pass in their own twig tests?
     */
    protected function _loadTests()
    {
        $objNumericTest = new \Twig_SimpleTest('numeric',function($mxdVal){
           return is_numeric($mxdVal);
        });

        $this->objViewEngine->addTest($objNumericTest);
    }

	/**
	 * Prevent unserializing of the instance
	 *
	 * @return void
	 */
	private function __wakeup(){}

	/**
	 * Prevent cloning of the instance
	 *
	 * @return void
	 */
	private function __clone(){}

}