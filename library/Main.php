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
	protected $boolIncludeHeader = true;
	protected $boolIncludeFooter = true;
	/**
	 * @var bool should the controller load up the data for the other views (usually header and footer)? If this is set
	 * to false, boolIncludeHeader and boolIncludeFooter are ignored
	 */
	protected $boolLoadSurroundingViewData = true;

    protected $aryRenderData = array();

    protected $aryRenderOptions = array(
        'include_pagination'=>false,
        'return'            =>false,
        'bypass_init'       =>false,
        'include_breadcrumbs'=>false,
    );


    public function __construct(array $aryContext=array())
    {

        //_mizzou_log(null,'Main constructor called',false,array('line'=>__LINE__,'file'=>__FILE__));
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
                ( isset($aryContext['objSite']) && $aryContext['objSite'] instanceof Site )
                ||
                ( isset($aryContext['Site']) && $aryContext['Site'] instanceof Site )
            ){
                if(isset($aryContext['objSite'])){
                    _mizzou_log(null,'DEPRECATED CODE: you\'re still using the name \'objSite\' somewhere in a view',false,array('line'=>__LINE__,'file'=>__FILE__));
                    $this->aryRenderData['Site'] = & $aryContext['objSite'];
	                $objSite = $aryContext['objSite'];
                } else {
	                $objSite = $aryContext['Site'];
                }

            }
        }
		//_mizzou_log($objSite,'what is objSite before we call init?',false,array('line'=>__LINE__,'file'=>__FILE__));
        $this->_init($objSite);

        $this->main();
    }

    protected function _init(Site $objSite=null)
    {
        //_mizzou_log(null,'Main init called',false,array('line'=>__LINE__,'file'=>__FILE__));
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
	    //_mizzou_log(get_class($this->objViewEngine),'what is objViewEngine?');
        /**
         * @todo we need to pass Site down into the view, but I'd rather not store multiple copies.  Is this the best method?
         * @todo redo Header and Footer to use Site instead of objSite
         */
        $this->aryRenderData['Site'] = $this->aryRenderData['objSite'] = & $this->objSite;

        $this->objLoader = new Loader($this->strFrameworkPath,$this->strParentThemePath,$this->strChildThemePath);
        //_mizzou_log($this->objLoader,'just finished creating loader',false,array('line'=>__LINE__,'file'=>__FILE__));

		if($this->boolLoadSurroundingViewData){
			$this->_loadSurroundingViewData();
		}
    }

    /**
     * @param $strInnerViewFileName
     */
    public function render($strInnerViewFileName)
    {
        $strReturn = Content::render($strInnerViewFileName,$this->aryRenderData,$this->objViewEngine,$this->objSite,$this->aryRenderOptions);

        if($this->aryRenderOptions['return']){
            return $strReturn;
        } else {
	        unset($strReturn);
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

	/**
	 * Loads and returns a class
	 *
	 * @param $strClass string class name
	 *
	 * @return object requested class
	 */
	public function load($strClass)
    {
        $aryArgs = array();
        if(func_num_args() > 1){
            $aryArgs = func_get_args();
	        //_mizzou_log($aryArgs,'list of args before I shift the class name',false,array('line'=>__LINE__,'file'=>__FILE__));
            $strClass = array_shift($aryArgs);
	        //_mizzou_log($aryArgs,'list of args after I shifted the class name',false,array('line'=>__LINE__,'file'=>__FILE__));
        }
        return $this->objLoader->load($strClass,$aryArgs);
    }

	/**
	 * Returns all of the data currently being stored in preparation for handing to the templates
	 *
	 * Mostly used by the header and footer controllers
	 *
	 * @return array All data to be handed to the templates
	 */
	public function getTemplateData()
	{
		return $this->aryRenderData;
	}

	/**
	 * Loads up template data from related views (typically header and footer).  If you have other views that need to be
	 * loaded regularly, then extend this class (Main) with an abstract child, overriding (extending) this method to include
	 * your controller that needs to be called
	 *
	 * example
	 *
	 * protected function _loadSurroundingViewData()
	 * {
	 *      parent::_loadSurroundingViewData();
	 *      if($this->boolIncludeMyOtherController){
	 *          $this->_retrieveControllerData(controllerName);
	 *
	 *      }
	 *
	 * }
	 */
	protected function _loadSurroundingViewData()
	{
		/**
		 * Yeah, I know we already checked previously, but I'm paranoid.  :P
		 */
		if($this->boolLoadSurroundingViewData){
			if($this->boolIncludeHeader){
				$this->_retrieveHeaderControllerData();
			}

			if($this->boolIncludeFooter){
				$this->_retrieveFooterControllerData();
			}
		}
	}

	/**
	 * Header controller and model need certain pieces of data that we should already have gathered.
	 */
	protected function _retrieveHeaderControllerData()
	{
		$aryData = array();
		$aryData['Site'] = $this->objSite;

		if(isset($this->aryRenderData['MainPost'])){
			$aryData['MainPost'] = $this->aryRenderData['MainPost'];
		}

		if(isset($this->aryRenderData['objPostType'])){
			$aryData['objPostType'] = $this->aryRenderData['objPostType'];
		}

		if(isset($this->aryRenderData['PageTitle'])){
			$aryData['PageTitle'] = $this->aryRenderData['PageTitle'];
		}

		$this->_retrieveControllerData('header',$aryData);
	}

	/**
	 * Here just so it can be overriden if necessary
	 */
	protected function _retrieveFooterControllerData()
	{
		$this->_retrieveControllerData('footer');
	}

	/**
	 * Requests a new instance of the controller and then merges its template data with our current template data
	 *
	 * @param $strController string controller name
	 */
	protected function _retrieveControllerData($strController,$aryData=array())
	{
		$objController = $this->_loadRoutableController($strController,$aryData);
		$this->aryRenderData = array_merge($this->aryRenderData,$objController->getTemplateData());
	}

	/**
	 * Looks up the namespace of the controller and requests a new instance of the class
	 *
	 * Assumption: the controller relys on current RenderData
	 *
	 * @param $strController string requested controller name
	 *
	 * @return object
	 */
	protected function _loadRoutableController($strController,$aryData=array())
	{
		$strNameSpacedController = $this->_determineControllerNameSpace($strController);
		return $this->load($strNameSpacedController,$aryData);
	}

	/**
	 * Attempts to discover the namespace of the given controller to be used with the @see load() method
	 *
	 * Assumptions:
	 *  - The classname is lowercase to match the controller file name (since wordpress assumes all template files to
	 *    be lowercase)
	 *  - If wordpress is unable to locate the controller (template) file via locate_template, that the requested
	 *    controller is in the MizzouMVC framework, and will have a namespace of MizzouMVC\controllers\
	 *  - The requested controller, if coming from a theme, will have a namespace. If not, the namespace is assumed to be root
	 *
	 * @param $strController string controller name
	 *
	 * @return string Fully qualified namespace of controller
	 */
	protected function _determineControllerNameSpace($strController)
	{
		$strControllerFileName = $strController.'.php';
		if('' != $strLocatedController = locate_template($strControllerFileName)){
			/**
			 * We need to search in the file for its namespace
			 * @todo this seems HORRIBLY inefficient.  Surely there's a way to say i need this file, what is this file's
			 * declared namespace?
			 * Maybe this is why they suggest that your directory structure match your namespace structure?
			 */
			_mizzou_log($strLocatedController,'located controller',false,array('line'=>__LINE__,'file'=>__FILE__));
			$boolFound = false;
			if(false != $rscHandle = fopen($strLocatedController,'r')){
				while(false != $strLine = fgetc($rscHandle) && !$boolFound){
					if(1 == preg_match('/^namespace\ ([\w\\\\]+);$/im',$strLine,$aryMatches)){
						$strNameSpacedController = $aryMatches[1] . $strController;
						$boolFound = true;
					}
				}

				fclose($rscHandle);
			}

			if(!$boolFound){
				//we have to assume that the controller doesnt have a namespace
				$strNameSpacedController = $strController;
			}
		} else {
			/**
			 * If it isnt in a theme, then we know its ours
			 */
			$strNameSpacedController = 'MizzouMVC\controllers\\'.$strController;
		}

		_mizzou_log($strNameSpacedController,'our found namespaced controller',false,array('line'=>__LINE__,'file'=>__FILE__));

		return $strNameSpacedController;

	}

	/**
	 * Main processing area for extending controllers
	 * @return mixed
	 */
	public abstract function main();

}