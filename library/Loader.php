<?php
/**
 * Dynamically loads a class' file and creates a new instance of the class
 *
 */
namespace MizzouMVC\library;

/**
 * Dynamically loads a class' file and creates a new instance of the class
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category library
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 *
 */
class Loader {
    /**
     * @var string server path to the framework
     */
    protected $strFrameworkPath = null;
    /**
     * @var string server path to the parent theme
     */
    protected $strParentThemePath = null;
    /**
     * @var string server path to the child theme
     */
    protected $strChildThemePath = null;

    /**
     * Sets the paths to the framework and themes
     *
     * @param string $strFramework server path to the framework
     * @param string $strParentTheme server path to the parent theme
     * @param string $strChildTheme server path to the child theme
     */
    public function __construct($strFramework,$strParentTheme,$strChildTheme=null)
    {
        //_mizzou_log(null,'loader constructor called',false,array('line'=>__LINE__,'file'=>__FILE__));
        //_mizzou_log(func_get_args(),'all the args passed to constructor',false,array('line'=>__LINE__,'file'=>__FILE__));
        $this->strFrameworkPath = $strFramework;
        $this->strParentThemePath = $strParentTheme;
        if(!is_null($strChildTheme) || $strParentTheme != $strChildTheme){
            $this->strChildThemePath = $strChildTheme;
        }

        //_mizzou_log($this,'Loader constructed, are our paths set?',false,array('line'=>__LINE__,'file'=>__FILE__));
    }

    /**
     * Locates the php file based on the namespaced class name, requires it and then creates a new instance of the class
     *
     * @param string $strClass
     * @param array $aryArgs
     * @return object
     */
    public function load($strClass,$aryArgs=array())
    {
        /*
         * first lets see if they gave us the file instead of the class. if so, we'll assume they named the file the same
         * as the class name
         */
        if(1 === preg_match('/\.[a-z]{1,5}$/',$strClass,$aryMatch)){
            _mizzou_log($strClass,'you gave me a file, but i need the class name',false,array('file'=>__FILE__,'line'=>__LINE__));
            $strClass = $aryMatch[1];
        }

        $aryClassParts = explode('\\',$strClass);

        $strFileName = end($aryClassParts);

        /**
         * Wordpress uses hyphens in file names to designate template parts.  But we cant use a hyphen in class names
         * @todo make sure the documentation notes controllers that need hyphens in the file name (e.g. single-person.php)
         * need to use underscores in the classname.  In those instances where they want to use underscores in the file
         * name/classname and not have them converted to hyphens will need to use double underscores
         * page_publications__by__author will convert to page-publications__by__author.php
         */
        if(1 == preg_match('/\\\\controllers\\\\/',$strClass)){
            //$strFileName = str_replace('_','-',$strFileName);
            /**
             * so it gets more interesting.  we have situations like the following:
             * page-publications_by_author.php
             * If we make the class name page_publications_by_author, then by doing a simple str replace, we'll end up
             * with a file name of page-publications-by-author.php which wont work. We could do a class name of
             * page_publicationsByAuthor which matches a file name of page-publicationsByAuthor, but not everyone is a
             * fan of camel case.  So, we'll allow double underscores to be untouched
             * Class name: page_publications__by__author
             * File name: page-publications__by__author.php
             */

            $strFileName = preg_replace('/(?=(_(?!_)))((?<!_)_)/', '-',$strFileName);
        }

        $strFileName .= '.php';


        $strFullPath = $this->_determinePath($aryClassParts) . $strFileName;

        if(file_exists($strFullPath)){
            require_once $strFullPath;
            if(class_exists($strClass,false)){
                if(count($aryArgs) > 0){
                    //_mizzou_log($aryArgs,'arguments before extractment',false,array('line'=>__LINE__,'file'=>__FILE__));
                    /**
                     * @todo what should we do about the prefix for invalid/numeric variables? should it be a theme/framework setting?
                     */
                    //return new $strClass(extract($aryArgs,EXTR_PREFIX_INVALID,'mzmvc'));
                    $objReflect = new \ReflectionClass($strClass);
                    return $objReflect->newInstanceArgs($aryArgs);
                } else {
                    return new $strClass;
                }
            } else {
                _mizzou_log($strClass,'the class name you gave me doesnt exist. make sure the namespace is correct',false,array('line'=>__LINE__,'file'=>__FILE__));
            }
        } else {
            _mizzou_log($strClass,'the class name you gave me doesnt map to a file. file map: ' . $strFullPath,false,array('line'=>__LINE__,'file'=>__FILE__));
        }
    }

    /**
     * Determines the path to the php file that needs to be required
     * @param array $aryClassParts
     * @return string path to file
     */
    protected function _determinePath($aryClassParts)
    {
        //_mizzou_log($this,'are our paths set?',false,array('line'=>__LINE__,'file'=>__FILE__));
        _mizzou_log($aryClassParts,'the class parts to the file we need',false,array('line'=>__LINE__,'file'=>__FILE__));
        $strFullPath = '';

        $strEndPiece = end($aryClassParts);

        //reset and grab the first piece
        $strFirst = reset($aryClassParts);

        if('MizzouMVC' == $strFirst){
            $strNext = next($aryClassParts); // models or plugin
            if('plugin' == $strNext){
                //we need the plugin path e.g. /var/www/html/account/www/wp-content/plugins
                $strFullPath = dirname($this->strFrameworkPath);
                //hold the pieces we have so far
                $aryPluginDir = array($strFirst,$strNext);
                //this should be the name of the plugin
                $strNext = next($aryClassParts);
                $aryPluginDir[] = $strNext;
                $strFullPath .= DIRECTORY_SEPARATOR . strtolower(implode('-',$aryPluginDir)) . DIRECTORY_SEPARATOR;
                $strNext = next($aryClassParts);
            } else {
                $strFullPath = $this->strFrameworkPath;
            }
        } else {
            $strSecond = next($aryClassParts);
            //the first part then will be the project root. The second part COULD be the parent/child indicator, but if
            //not, then it'll be the "parent" path
            if('child' == $strSecond){
                $strFullPath = $this->strChildThemePath;
                $strNext = next($aryClassParts);
            } else {
                $strFullPath = $this->strParentThemePath;
                if('parent' == $strSecond){
                    $strNext = next($aryClassParts);
                } else {
                    $strNext = $strSecond;
                }
            }
        }

        //ok, now we have our root start
        //$strNext = next($aryClassParts);
        if('controllers' != $strNext){
            //ok, add the strNext, then loop through the remainder of the pieces
            while($strNext = current($aryClassParts)) {
                if ($strEndPiece != $strNext) {
                    $strFullPath .= $strNext . DIRECTORY_SEPARATOR;
                }
                next($aryClassParts);
            }
        }

        return $strFullPath;
    }
}