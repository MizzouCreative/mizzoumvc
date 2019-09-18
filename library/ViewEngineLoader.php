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
class ViewEngineLoader
{

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
     * @var null|\Twig_Loader_Filesystem internal storage of class that handles loading templates
     */
    protected $objViewEngineFileSystemLoader = null;
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
     * Name of the directory where the namespace declarations file is contained
     * @todo should be moved into the twig-specific class once that is done
     */
    const NAMESPACE_DIRECTORY = '_data';
    /**
     * name of the json file that contains the twig namespaces to be added
     * @todo should be moved into the twig-specific class once that is done
     */
    const NAMESPACE_FILENAME = 'namespaces.json';

    /**
     * Sets viw directory locations, whether to enable caching, loads custom view filters/functions/tests
     * @param null|string $strFrameworkDir Server path location of the framework
     * @param null|string $strThemeDir Server path location of the (parent) theme
     * @param null|string $strChildThemeDir server path of the child theme, if applicable
     */
    private function __construct($strFrameworkDir = null, $strThemeDir = null, $strChildThemeDir = null)
    {
        $this->setFrameWorkDirectory($strFrameworkDir);
        $this->setThemeDirectory($strThemeDir);
        $this->setChildDirectory($strChildThemeDir);

        //initiate our twig environment loader
        //$this->objViewEngineEnvironmentLoader = new Twig_Loader_Filesystem($this->_determineViewDirectories());

        $boolAutoReload = (defined('WP_DEBUG')) ? WP_DEBUG : false;

        $aryViewEngineOptions = array(
            'cache'         => $this->_determineViewCacheLocation(),
            'auto_reload'   => $boolAutoReload,
            'autoescape'    => false,
            'debug'         => (defined('MIZZOUMVC_VIEW_DEBUG')) ? MIZZOUMVC_VIEW_DEBUG : false,
        );

        //initiate our view engine
        $aryDirectories = $this->_determineViewDirectories();
        $this->objViewEngineFileSystemLoader = new \Twig_Loader_Filesystem(($aryDirectories));
        //$this->objViewEngine = new Twig_Environment($this->objViewEngineEnvironmentLoader,$aryViewEngineOptions);
        $this->_loadNameSpaces($aryDirectories);
        //echo 'loadnamespaces called.';exit();
        $this->objViewEngine = new \Twig_Environment($this->objViewEngineFileSystemLoader, $aryViewEngineOptions);
        //load up our custom view filters
        $this->_loadViewEngineFilters();
        //load up our custom view functions
        $this->_loadViewEngineFunctions($this->strFrameworkDir);
        //load up our custom tests
        $this->_loadTests();
    }

    /**
     * Creates instance and/or returns stored instance
     * @param null|string $strFrameworkDir server path of framework
     * @param null|string $strThemeDir server path of main/parent theme
     * @param null|string $strChildThemeDir server path of child theme, if applicable
     * @return null|Twig_Environment
     */
    public static function getViewEngine($strFrameworkDir = null, $strThemeDir = null, $strChildThemeDir = null)
    {
        if (is_null(self::$objInstance) || is_null(self::$objInstance->objViewEngine)) {
            self::$objInstance = new ViewEngineLoader($strFrameworkDir, $strThemeDir, $strChildThemeDir);
        }

        return self::$objInstance->objViewEngine;
    }

    /**
     * Determines where the view directories are located to hand off the template renderer
     * @return array list of directories
     */
    protected function _determineViewDirectories()
    {
        $aryDirectories = array();
        if (!is_null($this->strChildThemeDir) && $this->strChildThemeDir != $this->strThemeDir) {
            $aryDirectories[] = $this->strChildThemeDir;
        }

        $aryDirectories[] = $this->strThemeDir;
        $aryDirectories[] = $this->strFrameworkDir;

        foreach ($aryDirectories as $intDirKey => $strDirectory) {
            $aryDirectories[$intDirKey] = $strDirectory . 'views' . DIRECTORY_SEPARATOR;
        }

        return apply_filters('mizzoumvc_view_paths', $aryDirectories);
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
         * This will disable caching altogether
         */
        if (defined('MIZZOUMVC_DISABLE_VIEW_CACHE') && MIZZOUMVC_DISABLE_VIEW_CACHE) {
            $strViewCacheLocation = false;
        } elseif (defined('MIZZOUMVC_VIEW_CACHE_LOCATION')) {
            /**
             * @todo re-evaluate the use of a global constant. We have access to the site object by the time this class is
             * called so we _could_ move this into a theme setting area.
             */
            $strViewCacheLocation = MIZZOUMVC_VIEW_CACHE_LOCATION;
        } else {
            //let's see if we have a cache directory
            $strPossibleCacheLocation = $this->strThemeDir.'cache'.DIRECTORY_SEPARATOR;
            if (!is_dir($strPossibleCacheLocation) && !file_exists($strPossibleCacheLocation)) {
                //we need to make a directory
                if (mkdir($strPossibleCacheLocation, 0700)) {
                    $strViewCacheLocation = $strPossibleCacheLocation;
                }
            /**
             * Add a check for file perms to make sure it is 0700
             */
            } elseif (!is_writable($strPossibleCacheLocation) || (0700 !== (fileperms($strPossibleCacheLocation) & 0777))) {
                //it exists but we cant write to it...
                /**
                 * @todo change to INT perms, not string
                 */
                if (chmod($strPossibleCacheLocation, 0700)) {
                    $strViewCacheLocation = $strPossibleCacheLocation;
                }
            } else {
                $strViewCacheLocation = $strPossibleCacheLocation;
            }

        }

        if ('' === $strViewCacheLocation) {
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
        $objTwigDebug = new \Twig_SimpleFilter('var_export', function ($string) {
            return PHP_EOL.'<pre>'.var_export($string, true).'</pre>'.PHP_EOL;
        });

        $objTwigSanitize = new \Twig_SimpleFilter('sanitize', function ($strString) {
            return sanitize_title_with_dashes($strString);
        });

        /**
         * Given a timestamp, a string formatted date, or a full month, we'll convert it to an AP-style month
         */
        $objTwigAPMonth = new \Twig_SimpleFilter('apmonth', function ($mxdDate) {
            $intTimeStamp = null;
            $strMonth = null;
            $strReturn = $mxdDate;

            if (is_string($mxdDate)) {
                //we have some time of string representation of a date
                $aryCalendarInfo = cal_info(0);
                //do we have a full month?
                if (in_array($mxdDate, $aryCalendarInfo['months'])) {
                    //ok we have our month
                    $strMonth = $mxdDate;
                } else {
                    $intTimeStamp = strtotime($mxdDate);
                }
            } elseif (is_numeric($mxdDate)) {
                //we'll assume they gave us a timestamp
                $intTimeStamp = $mxdDate;
            }

            if (!is_null($intTimeStamp) && false !== $intTimeStamp) {
                $strMonth = date('F', $intTimeStamp);
            }

            if (!is_null($strMonth)) {
                if (strlen($strMonth) > 5) { //stoopid september... grumble, grumble
                    if ($strMonth == 'September') {
                        $intTruncLen = 4;
                    } else {
                        $intTruncLen = 3;
                    }

                    $strReturn = substr($strMonth, 0, $intTruncLen) . '.';
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
        $this->objViewEngine->addFunction('subview', new \Twig_SimpleFunction('subview', function ($mxdControllerName, $aryContext, $aryData = array()) use ($strFrameWorkDir) {
            //_mizzou_log($mxdControllerName,'the controller we were asked to get',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
            //_mizzou_log($aryContext,'the context data that was passed in',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
            $strController = '';

            if (is_array($mxdControllerName)) {
                $aryControllerNameParts = $mxdControllerName;
            } elseif (is_string($mxdControllerName)) {
                $aryControllerNameParts = explode(' ', trim($mxdControllerName));
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
            if (count($aryData) != 0) {
                extract($aryData);
            }

            if(!is_null($strFrameWorkDir) && '' == $strController = locate_template($strControllerName)){
                //_mizzou_log(null,'we didnt find a controller in a parent or child theme. gonna look in the plugin framework',false,array('line'=>__LINE__,'file'=>__FILE__));
                //ok, we didnt find a controller in a parent or child theme, what about the plugin?
                if (is_readable($strFrameWorkDir.$strControllerName)) {
                    $strController = $strFrameWorkDir.$strControllerName;
                } else {
                    _mizzou_log($strFrameWorkDir.$strControllerName,'we couldnt find this controller in the framework either',false,array('line'=>__LINE__,'file'=>__FILE__));
                }
            }
            //_mizzou_log($strController = locate_template($strControllerName),'direct return from locate_template',false,array('file'=>__FILE__,'line'=>__LINE__));
            //_mizzou_log($strController,'the controller name before we try to require it',false,array('func'=>__FUNCTION__,'file'=>__FILE__,'line'=>__LINE__));
            if ('' != $strController) {
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
        $objNumericTest = new \Twig_SimpleTest('numeric', function ($mxdVal) {
            return is_numeric($mxdVal);
        });

        $this->objViewEngine->addTest($objNumericTest);
    }

    /**
     * Checks to see if a namespace.json file exists. if so, loads in and adds the namespace+path to the ViewEngine
     * FileSystem Loader
     * @param array $aryDirectories list of directories where our templates reside
     * @param \Twig_Loader_Filesystem $objTwigFSLoader
     * @todo this is *completely* coupled to Twig and should be moved into a Twig middleware controller
     */
    protected function _loadNameSpaces(array $aryDirectories = array())
    {
        foreach ($aryDirectories as $strDirectory) {
            $strFile = $strDirectory . $this::NAMESPACE_DIRECTORY . DIRECTORY_SEPARATOR . $this::NAMESPACE_FILENAME;
            //does the file exist and can we get its contents?
            if (file_exists($strFile) && false !== $strJSON = file_get_contents($strFile)) {
                //do we have a json object (converted to an array)
                if (null !== $aryNameSpaces = json_decode($strJSON, true)) {
                    //letd grab the declared namespace
                    foreach ($aryNameSpaces as $strNameSpace => $aryNameSpaceData) {
                        //grab the array of paths
                        if (isset($aryNameSpaceData['paths']) && is_array($aryNameSpaceData['paths'])) {
                            //add each path as a namespace
                            foreach ($aryNameSpaceData['paths'] as $strNameSpacePath) {
                                //@todo do we need to reverse the array before we loop?
                                foreach ($aryDirectories as $strTemplateLocation) {
                                    $strNameSpaceFullPath = $strTemplateLocation . $strNameSpacePath;
                                    if (is_dir($strNameSpaceFullPath)) {
                                        $this->objViewEngineFileSystemLoader->addPath($strNameSpaceFullPath, $strNameSpace);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Ensures a path ends with a directory separator
     * @param $strDirectory directory path to evaluate
     * @return string directory with ending separator
     */
    protected function ensureEndSeparator($strDirectory)
    {
        $strPattern = sprintf('/\%s$/', DIRECTORY_SEPARATOR);
        if (1 !== preg_match($strPattern, $strDirectory)) {
            $strDirectory .= DIRECTORY_SEPARATOR;
        }

        return $strDirectory;
    }

    /**
     * Sets the path to the MizzouMVC Framework
     * @param null $strFrameworkDirectory
     * @return void
     */
    protected function setFrameWorkDirectory($strFrameworkDirectory = null)
    {
        if (is_null($strFrameworkDirectory)) {
            $strFrameworkDirectory = MIZZOUMVC_ROOT_PATH;
        }

        $this->strFrameworkDir = $strFrameworkDirectory;
    }

    /**
     * Sets the system path to our theme directory
     * @param null|string $strDirectory path to theme/parent theme
     * @return void
     */
    protected function setThemeDirectory($strDirectory = null)
    {
        if (is_null($strDirectory)) {
            $strDirectory = get_template_directory();
        }

        $this->strThemeDir = $this->ensureEndSeparator($strDirectory);
    }

    /**
     * Sets the path to the current theme, or child theme, if applicable
     * @param null|string $strDirectory path to child theme
     * @return void
     */
    protected function setChildDirectory($strDirectory = null)
    {
        if (is_null($strDirectory)) {
            $strDirectory = get_stylesheet_directory();
        }

        $this->strChildThemeDir = $this->ensureEndSeparator($strDirectory);
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