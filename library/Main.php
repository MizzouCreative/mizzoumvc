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
use MizzouMVC\models\Menu;
use MizzouMVC\models\Site;
use MizzouMVC\library\FrameworkSettings;
use MizzouMVC\library\ViewEngineLoader;
use MizzouMVC\models\Content;

abstract class Main {

    protected $objSite              = null;
    protected $strParentThemePath   = null;
    protected $strChildThemePath    = null;
    protected $strFrameworkPath     = null;
    protected $objViewEngine        = null;
    protected $objLoader            = null;
	protected $boolIncludeHeader    = true;
	protected $boolIncludeFooter    = true;
	protected $boolIncludePagination= false;
	protected $objPagePostType      = null;
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
    }

    /**
     * @param $strInnerViewFileName
     */
    public function render($strInnerViewFileName)
    {

        if($this->boolLoadSurroundingViewData){
		    /**
		     * @todo flesh out this assumption
		     *
		     * we're going to assume that if they DONT want the surrounding view data (header/footer) then they probably
		     * don't need the page title to be determined for them.  And we only need to do it if it isnt already set
		     */
		    if(!isset($this->aryRenderData['PageTitle'])){
			    $this->renderData('PageTitle',$this->_determinePageTitle());
		    }


		    $this->_loadSurroundingViewData();


	    }

        /**
         * We have to load the Menu object AFTER we load the Header object since it might have created/changed values we need
         */
        if($this->boolLoadSurroundingViewData || (isset($this->aryRenderData['menuName']) && '' !== $this->aryRenderData['menuName'])){
            $this->_loadMenu();
        }


	    $strReturn = Content::render($strInnerViewFileName,$this->aryRenderData,$this->objViewEngine,$this->objSite,$this->aryRenderOptions);

        if($this->aryRenderOptions['return']){
            return $strReturn;
        } else {
	        unset($strReturn);
        }
    }

    protected function _loadMenu(){
        _mizzou_log(__FUNCTION__,'just loaded the function',false,array('line'=>__LINE__,'file'=>__FILE__));
        //Menu class HAS to have Site. it also can take MainPost, PageTitle and menuName
        $aryPossibleKeys = array('MainPost','PageTitle','menuName');

        $aryOptions = array_intersect_key($this->aryRenderData,array_flip($aryPossibleKeys));

        $this->renderData('Menu', new Menu($this->objSite,$aryOptions));

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
			if($this->boolIncludePagination){
				$this->_retrievePaginationModel();
			}

			if($this->boolIncludeHeader){
				$this->_retrieveHeaderControllerData();
			}

			if($this->boolIncludeFooter){
				$this->_retrieveFooterControllerData();
			}
		}
	}

	protected function _retrievePaginationModel()
	{
		global $wp_query;
		$aryOptions = array('wp_query'=>$wp_query);
		if('' != $this->objSite->pagination && is_array($this->objSite->pagination)){
			$aryOptions = array_merge($aryOptions,$this->objSite->pagination);
		}

		/*
		 * so how do we deal with a theme wanting to use their own pagination model?
		 * @todo set up an option for Pagination class?
		 */
		$objPagination = $this->load('MizzouMVC\models\Pagination',$aryOptions);
		_mizzou_log($objPagination,'what did we get back for pagination?',false,array('line'=>__LINE__,'file'=>__FILE__));
		$this->renderData('Pagination',$this->load('MizzouMVC\models\Pagination',$aryOptions));
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
				while(false !== ($strLine = fgets($rscHandle)) && !$boolFound){
					if(1 ==$boolMatched = preg_match('/^namespace\ ([\w\\\\]+);$/im',$strLine,$aryMatches)){
						$strNameSpacedController = $aryMatches[1] . "\\" . $strController;
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

	protected function _determinePageTitle()
	{
		$strPageTitle = '';
		if(is_archive()){
			global $wp_query;
			//_mizzou_log(post_type_archive_title(null,false),'we know we have an archive, here is the post_type_archive_title');
			if(is_date()){
				if(!isset($this->aryRenderData['DateArchiveType'])){
					$this->renderData('DateArchiveType',$this->_determineDateArchiveType());
				}

				//_mizzou_log($strDateArchiveType,'our archive date type');
				$aryDateParts = array();
				$strDatePattern = '';
				switch ($this->aryRenderData['DateArchiveType']){
					case 'day':
						$aryDateParts[] = get_the_time('d');
						$strDatePattern = ' %s,';
					case 'month':
						/**
						 * since it is possible that the day is already in the array, we need to make sure that month
						 * is pushed onto the beginning of the array no matter what, hence the array_unshift
						 */
						array_unshift($aryDateParts,get_the_time('F'));
						$strDatePattern = '%s'.$strDatePattern;
					case 'year':
						$aryDateParts[] = get_the_time('Y');
						$strDatePattern .= ' %d';
						break;
				}

				//_mizzou_log($strDatePattern,'our date pattern');
				//_mizzou_log($aryDateParts,'our date parts');

				$strPageTitle = vsprintf($strDatePattern,$aryDateParts);
				if(is_null($this->objPagePostType)){
					$this->objPagePostType = $this->_determinePagePostType();
				}
				//_mizzou_log($objPagePostType,'objPagePostType',false,array('line'=>__LINE__,'file'=>__FILE__));
				$strPageTitle .= ' ' . $this->objPagePostType->labels->name;
				//_mizzou_log($strPageTitle,'we have a date archive. this is the date formatted title weve come up with',false,array('line'=>__LINE__,'file'=>__FILE__));

			} else {
				$strPageTitle = post_type_archive_title(null,false);
				_mizzou_log($strPageTitle,'we are a non-dated archive. this is what was returned from post_type_archive_title');
				/**
				 * If it isn't a dated archive, has it been filtered by a taxonomy?
				 */
				$objQueried = get_queried_object();
				if(is_object($objQueried) && count($wp_query->tax_query->queries) > 0){
					$strPageTitle = ($strPageTitle == '') ? $objQueried->name : $objQueried->name . ' ' . $strPageTitle;
				}
			}

			//now, are we in the midst of pagination?
			_mizzou_log($wp_query,'wp_query is paged set?',false,array('line'=>__LINE__,'file'=>__FILE__));
			if(isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] != 0){
				$strPageTitle .= ', Page ' . $wp_query->query_vars['paged'];
			}
		} else {
			$strPageTitle = wp_title('',false);
		}
		_mizzou_log($strPageTitle,'page title as determined',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
		return trim($strPageTitle);
	}

	protected function _determineDateArchiveType()
	{
		$strDateArchiveType = '';

		if(is_day()){
			$strDateArchiveType = 'day';
		} elseif(is_month()){
			$strDateArchiveType = 'month';
		} elseif(is_year()){
			$strDateArchiveType = 'year';
		}

		return $strDateArchiveType;
	}

	/**
	 * Determines the post type of the current page we are dealing with
	 */
	protected function _determinePagePostType()
	{
		//$strPostType = get_post_type();

		/**
		 * the normal method failed so fall back to a secondary method
		 * @todo should we just do this method all the time instead of using it when get_post_type fails?
		 */
		$strPostType = get_query_var('post_type');
		if(is_array($strPostType)){
			$strPostType = reset($strPostType);
		} elseif(''==$strPostType){
			//still empty, let's try get_post_type
			$strPostType = get_post_type();
		}

		if('' != $strPostType){
			//self::_adjustPostTypeLabels($strPostType);
			return get_post_type_object($strPostType);
		} else {
			/**
			 * @todo we need to do something else here besides log. We have functionality further down the line that
			 * depends on the PostType being determined.
			 *
			 * HOWEVER, there are perfectly valid scenarios where we dont have a post type. Front Page is one, Search
			 */
			global $wp_query;
			_mizzou_log($wp_query,'WARNING: We were unable to determine the post type we are dealing with. Here is wp_query',true);
		}
	}

	/**
	 * Main processing area for extending controllers
	 * @return mixed
	 */
	public abstract function main();

}