<?php
/**
* Model to collect data needed by header view
 *
 * This model DEPENDS on objSite being passed into it
*
* @package WordPress
* @subpackage MizzouMVC
* @category framework
* @category Model
* @author Paul F. Gilzow, Web Communications, University of Missouri
* @copyright 2015 Curators of the University of Missouri
* @dependency objSite model
 */
namespace MizzouMVC\models;
class Header extends Subview {


    /**
     * Determines and sets value used in the header area
     * @param $aryContext
     * @return void
     */
    function __construct($aryContext){
        /**
         * Why are replicating the exact same functionality that the parent class does in its __construct??
         * @todo verify and then remove this code
         * @todo verify that we still need access to the Site class.
         */
        if(isset($aryContext['objSite'])){
            $this->add_data('objSite',$aryContext['objSite']);
        } else {
			/**
			 * @todo throw a error/exception? We can't really continue without it
			 */
			_mizzou_log($aryContext,'whoa... objSite isnt set. was it in the data passed to us?',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
		}

		//if we werent given the post type object, set it to null
        $this->add_data('objPostType',(isset($aryContext['objPostType'])) ? $aryContext['objPostType'] : null);
		//@todo page title should always be set, throw an exception?
        //_mizzou_log($aryContext,'aryContext to see if we have PageTitle',false,array('line'=>__LINE__,'file'=>__FILE__));
		$this->add_data('PageTitle',(isset($aryContext['PageTitle'])) ? $aryContext['PageTitle'] : '');
        //_mizzou_log($this->aryData['PageTitle'],'so was pagetitle set correctly?',false,array('line'=>__LINE__,'file'=>__FILE__));
		$this->_setHeaderTitle();
        $this->_setActiveStylesheet();
		$this->_setIncludeNoIndex();
        $this->_setCurrentPageURL();
        $this->_setCurrentPageShortURL();
		$this->_setWpHead();
        /**
         * @todo this has been moved to the Menu model. deprecate
         */
        if($aryContext['objSite']->option('inject_primary')){
            $this->_injectPrimaryMenu();
        }

	}



	/**
	 * Determines whether or not we should include a noindex meta for the current page
     * @return void
	 */
	protected function _determineIncludeNoIndex()
	{
		$boolIncludeNoIndex = false;

		/**
		 * This one is a bit of a bugger...
		 * If we have access to the MainPost object AND either noindex or nolink is set and ON
		 * OR
		 * we're on a 404 page
		 * OR
		 * we're on a search page
		 * then
		 * we want to include the meta element for robots to not index the page
		 *
		 */
		if(
			is_404() || is_search()
			|| (
				isset($this->aryData['objMainPost'])
				&& (
					(isset($this->aryData['objMainPost']->noindex) && $this->aryData['objMainPost']->noindex == 'on')
					||
					(isset($this->aryData['objMainPost']->nolink) && $this->aryData['objMainPost']->nolink == 'on')
				)
			)
		) {
			$boolIncludeNoIndex = true;

		}

		return $boolIncludeNoIndex;
	}

    /*
     * Sets the no index value
     * @return void
     */
	protected function _setIncludeNoIndex($boolIncludeNoIndex=null)
	{
		if(is_null($boolIncludeNoIndex) || !is_bool($boolIncludeNoIndex)){
			$boolIncludeNoIndex = $this->_determineIncludeNoIndex();
		}

		$this->add_data('IncludeNoIndex',$boolIncludeNoIndex);
	}

	/**
	 * Determines the Page title to be used for the <title> element
     * @return void
	 */
	protected function _determineHeaderTitle()
	{
		$aryTitleParts = array();
		$strPageTitle = trim(strip_tags($this->aryData['PageTitle']));

		if('' != $strPageTitle){
			$aryTitleParts[] = $strPageTitle;
		}



		/**
		 * @todo Should we check to see if it isnt null, or check to see if it is an object? We're making an assumption
		 * right now that if it isnt null, then ->labels and ->labels->name have been set and are accessible
		 */
		if(!is_null($this->aryData['objPostType']) && $strPageTitle !== $this->aryData['objPostType']->labels->name){
			$aryTitleParts[] = $this->aryData['objPostType']->labels->name;
		}

		$aryTitleParts[] = $this->aryData['objSite']->Name;
		/**
		 * @todo this piece should come from a Theme options class
		 */
		$aryTitleParts[] = 'University of Missouri';


        $strGlue = $this->aryData['objSite']->option('header_title_separator');

		return implode($strGlue,$aryTitleParts);
	}

	/**
	 * Sets the string to be used in <title> element of the page
	 * @param string $strHeaderTitle
	 * @return void
	 */
	protected function _setHeaderTitle($strHeaderTitle=null)
	{
		if(is_null($strHeaderTitle)){
			$strHeaderTitle = $this->_determineHeaderTitle();
		}

		$this->add_data('HeadTitle',$strHeaderTitle);
	}

	/**
	 * Captures and stores the contents of wp_head
	 * @return void
	 */
	protected function _setWpHead()
	{
		$this->add_data('wpHead',$this->_captureOutput('wp_head'));
	}

	/**
	 * Retrieves the current page's URL
	 * This CAN be different than the permalink. You'd think we would use get_permalink, but for sections that deal
	 * with multiple items, that returns the permalink of the last item.
	 * @return void
	 */
	protected function _setCurrentPageURL()
	{
		/**
		 * I hate globals as much as the next programmer, but wordpress lubs 'em.  I don't see anyway around this unless
		 * we want to inject the $wp variable into the aryContext array that is passed in on initialization
		 */
		global $wp;
		$this->add_data('CurrentPageUrl',home_url(add_query_arg(array(),$wp->request)).'/');
	}

	/**
	 * Gets and sets the current page's short URL
	 * @return void
	 */
	protected function _setCurrentPageShortURL()
	{
		$this->add_data('CurrentPageShortUrl',wp_get_shortlink());
	}

    /**
     * Determines which stylesheet should be used
     * @category settings
     * @return string full src path to the stylesheet
     */
    protected function _determineActiveStylesheet()
    {
        if($this->aryData['objSite']->option('stylesheet') != ''){
            $strStyleSheet = $this->aryData['objSite']->ActiveThemeURL . $this->aryData['objSite']->option('stylesheet');
        } else {
            $strStyleSheet = get_stylesheet_uri();
        }

        return $strStyleSheet;
    }

    /**
     * Sets a full src path to a stylesheet
     * @return void
     */
    protected function _setActiveStylesheet()
    {
        $this->add_data('ActiveStylesheet',$this->_determineActiveStylesheet());
    }

    /**
     * @deprecated
     */
    protected function _injectPrimaryMenu()
    {
        _mizzou_log(null,'DEPRECATED FUNCTION CALL: turn on stack trace to figure out where it was called. ',false,array('line'=>__LINE__,'file'=>__FILE__,'func'=>__FUNCTION__));
        if($this->aryData['objSite']->PrimaryMenu != '' && $this->aryData['PageTitle'] != ''){
            $objDomMenu = new DOMDocument();
            $objDomMenu->loadXML($this->aryData['objSite']->PrimaryMenu);
            //_mizzou_log($objDomMenu->saveHTML(),'our menu as a DOMobject',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
            $objOLNodes = $objDomMenu->getElementsByTagName('ol');
            //_mizzou_log(null,'currently',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
            for($j=0;$j<$objOLNodes->length;++$j){
                //_mizzou_log(null,'currently',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
                $objChildNode = $objOLNodes->item($j);
                if($objChildNode->getAttribute('id') == 'menu-primary'){
                    $j = $objOLNodes->length;
                    $objMainMenuLIList = $objChildNode->getElementsByTagName('li');
                    for($i=0;$i<$objMainMenuLIList->length;++$i){
                        $objChildLI = $objMainMenuLIList->item($i);
                        if(trim($this->aryData['PageTitle']) == $objChildLI->nodeValue){
                            $i = $objMainMenuLIList->length;
                            /* ok, so we have a node title that matches a page title. now let's go see if we have a
                             * matching menu
                             */
                            $aryMenuOptions = array(
                                'menu' => $objChildLI->nodeValue,
                                /**
                                 * @todo should we attempt to have an option for the menu class to be inserted here?
                                 */
                                //'menu_class'=>'sidebar-navigation',
                                'theme_location'=>'no_such_location',
                                'echo' => false,
                                'fallback_cb'=>'',
                                'container'=>false,
                            );

                            if('' != $strSubMenu = wp_nav_menu($aryMenuOptions)){
                                //_mizzou_log($strSubMenu,'we found an element that matches our current page! Here is our matching menu',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
                                //store it in the site object
                                $this->aryData['objSite']->add_data('PageMenu',$strSubMenu);
                                $objDomSecondaryMenu = new DOMDocument();
                                $objDomSecondaryMenu->loadXML($strSubMenu);
                                $objSecondaryMenuNode = $objDomSecondaryMenu->getElementsByTagName('ul')->item(0);
                                /*
                                $objFirstChildOfSecondMenu = $objSecondaryMenuNode->firstChild;
                                _mizzou_log($objFirstChildOfSecondMenu->nodeValue,'what is the first child of our second menu?');
                                */

                                $objChildLI->appendChild($objDomMenu->importNode($objSecondaryMenuNode,true));
                                $this->aryData['objSite']->add_data('PrimaryMenu',$objDomMenu->saveHTML());
                                //_mizzou_log($this->aryData['objSite']->PrimaryMenu,'our menu in html after injection',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));

                            }
                        }
                    }
                }


            }

        }

    }


}