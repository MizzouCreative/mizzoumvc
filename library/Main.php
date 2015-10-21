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
namespace MizzouMVC\controllers;
use MizzouMVC\library\Loader;
use MizzouMVC\models\Site;
use MizzouMVC\library\FrameworkSettings;
use MizzouMVC\library\ViewEngineLoader;
use MizzouMVC\models\Content;

abstract class Main {

    protected $objSite = null;
    protected $strParentThemePath = null;
    protected $strChildThemePath = null;
    protected $strFrameworkPath = null;
    protected $objViewEngine = null;
    protected $objLoader = null;

    protected $aryRenderData = array();

    protected $aryRenderOptions = array(
        'include_pagination'=>false,
        'return'            =>false,
        'bypass_init'       =>false,
        'include_breadcrumbs'=>false,
    );


    public function __construct(array $aryContext=array())
    {

        _mizzou_log(null,'Main constructor called',false,array('line'=>__LINE__,'file'=>__FILE__));
        /**
         * @todo blarg.  We shouldnt have to set objSite to null, just in case we actually have.  rethink the logic here.
         */
        $objSite = null;
        /**
         * If we have context, we'll need to pass it down into the next view
         */
        if(count($aryContext) > 0 ){
            $this->aryRenderData = array_merge($this->aryRenderData,$aryContext);

            if(isset($this->aryRenderData['objMainPost'])){
                _mizzou_log(null,'DEPRECATED CODE: you\'re still using the name \'objMainPost\' somewhere in a view',false,array('line'=>__LINE__,'file'=>__FILE__));
                $this->aryRenderData['MainPost'] = & $this->aryRenderData['objMainPost'];
            }

            /**
             * Site (historically objSite) is a data storage object passed into the views. If we have access to it, we'll
             * use the data stored in it instead of looking those values up again
             */
            if(
                ( isset($aryContext['objSite']) && $objSite = $aryContext['objSite'] instanceof Site )
                ||
                ( isset($aryContext['Site']) && $objSite = $aryContext['Site'] instanceof Site )
            ){
                if(isset($aryContext['objSite'])){
                    _mizzou_log(null,'DEPRECATED CODE: you\'re still using the name \'objSite\' somewhere in a view',false,array('line'=>__LINE__,'file'=>__FILE__));
                    $this->aryRenderData['Site'] = & $aryContext['objSite'];
                }

            }
        }
		_mizzou_log($objSite,'what is objSite before we call init?',false,array('line'=>__LINE__,'file'=>__FILE__));
        $this->_init($objSite);

        $this->main();
    }

    protected function _init(Site $objSite=null)
    {
        _mizzou_log(null,'Main init called',false,array('line'=>__LINE__,'file'=>__FILE__));
        if(defined('MIZZOUMVC_ROOT_PATH')){
            $this->strFrameworkPath = MIZZOUMVC_ROOT_PATH;
        } else {
            _mizzou_log(null,'whoah, MIZZOUMVC_ROOT_PATH isnt defined! ',false,array('line'=>__LINE__,'file'=>__FILE__));
        }

        /**
         * objSite is a data storage object and passed around through the views. No use looking up values again if we
         * have access to the data store
         */
        if(!is_null($objSite)){
            $this->objSite = $objSite;
            $this->strParentThemePath = $objSite->ParentThemePath;
            $this->strChildThemePath = $objSite->ChildThemePath;
        } else {
            $this->strParentThemePath = get_template_directory() . DIRECTORY_SEPARATOR;
            $this->strChildThemePath = get_stylesheet_directory() . DIRECTORY_SEPARATOR;
            $this->objSite = new Site(new FrameworkSettings(),array('parent_path'=>$this->strParentThemePath,'child_path'=>$this->strChildThemePath));
        }

        $this->objViewEngine = ViewEngineLoader::getViewEngine($this->strFrameworkPath,$this->strParentThemePath,$this->strChildThemePath);
	    _mizzou_log(get_class($this->objViewEngine),'what is objViewEngine?');
        /**
         * @todo we need to pass Site down into the view, but I'd rather not store multiple copies.  Is this the best method?
         */
        $this->aryRenderData['Site'] = & $this->objSite;

        $this->objLoader = new Loader($this->strFrameworkPath,$this->strParentThemePath,$this->strChildThemePath);
        _mizzou_log($this->objLoader,'just finished creating loader',false,array('line'=>__LINE__,'file'=>__FILE__));
    }

    /**
     * @param $strInnerViewFileName
     */
    public function render($strInnerViewFileName)
    {
        $strReturn = Content::render($strInnerViewFileName,$this->aryRenderData,$this->objViewEngine,$this->objSite,$this->aryRenderOptions);

        if($this->aryRenderOptions['return']){
            return $strReturn;
        }
    }

    protected function renderOption($mxdKey,$mxdValue)
    {
        $this->aryRenderOptions[$mxdKey] = $mxdValue;
    }

    protected function renderData($mxdKey,$mxdValue)
    {
        $this->aryRenderData[$mxdKey] = $mxdValue;
    }

    protected function mixedToBool($mxdVal)
    {
        return filter_var($mxdVal,FILTER_VALIDATE_BOOLEAN);
    }

    public function load($strClass)
    {
        $aryArgs = array();
        if(func_num_args() > 1){
            $aryArgs = func_get_args();
	        _mizzou_log($aryArgs,'list of args before I shift the class name',false,array('line'=>__LINE__,'file'=>__FILE__));
            $strClass = array_shift($aryArgs);
	        _mizzou_log($aryArgs,'list of args after I shifted the class name',false,array('line'=>__LINE__,'file'=>__FILE__));
        }
        return $this->objLoader->load($strClass,$aryArgs);
    }
    public abstract function main();

}