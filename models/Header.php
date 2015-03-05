<?php
/**
* Model to collect data needed by header view
 *
 * This model DEPENDS on objSite being passed into it
*
* @package WordPress
* @subpackage Mizzou MVC
* @category theme
* @category Model
* @author Paul F. Gilzow, Web Communications, University of Missouri
* @copyright 2015 Curators of the University of Missouri
 */

class Header extends Subview {


	function __construct($aryContext){

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
		$this->add_data('PageTitle',(isset($aryContext['PageTitle'])) ? $aryContext['PageTitle'] : '');

		$this->_setHeaderTitle();
        $this->_setActiveStylesheet();
		$this->_setIncludeNoIndex();
		$this->_setWpHead();
        /**
         * @todo this should PROBABLY be an optional item
         */
        $this->_injectPrimaryMenu();
	}



	/**
	 *
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

	protected function _setIncludeNoIndex($boolIncludeNoIndex=null)
	{
		if(is_null($boolIncludeNoIndex) || !is_bool($boolIncludeNoIndex)){
			$boolIncludeNoIndex = $this->_determineIncludeNoIndex();
		}

		$this->add_data('IncludeNoIndex',$boolIncludeNoIndex);
	}

	/**
	 *
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

		$aryTitleParts[] = $this->aryData['objSite']->name;
		/**
		 * @todo this piece should come from a Theme options class
		 */
		$aryTitleParts[] = 'University of Missouri';

		//_mizzou_log($aryTitleParts,'aryTitleParts right before we implode');
		/**
		 * @todo implosion glue should come from a Theme options class
		 */
		return implode(' // ',$aryTitleParts);
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

		$this->add_data('HeaderTitle',$strHeaderTitle);
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
		$this->add_data('CurrentPageUrl',home_url(add_query_arg(array(),$wp->request)));
	}

	/**
	 * Gets and sets the current page's short URL
	 * @return void
	 */
	protected function _setCurrentPageShortURL()
	{
		$this->add_data('CurrentPageShortUrl',wp_get_shortlink());
	}

    protected function _determineActiveStylesheet()
    {
        if($this->aryData['objSite']->option('stylesheet') != ''){
            $strStyleSheet = $this->aryData['objSite']->ActiveThemeURL . $this->aryData['objSite']->option('stylesheet');
        } else {
            $strStyleSheet = get_stylesheet_uri();
        }

        return $strStyleSheet;
    }

    protected function _setActiveStylesheet()
    {
        $this->add_data('ActiveStylesheet',$this->_determineActiveStylesheet());
    }

    protected function _injectPrimaryMenu()
    {
        if($this->aryData['objSite']->PrimaryMenu != '' && $this->aryData['PageTitle'] != ''){
            $objDomMenu = new DOMDocument();
            $objDomMenu->loadXML($this->aryData['objSite']->PrimaryMenu);
            _mizzou_log($objDomMenu,'our menu as a DOMobject',false,array('line'=>__LINE__,'file'=>dirname(__FILE__)));
            foreach($objDomMenu->childNodes as $objChildNode){
                _mizzou_log($objChildNode->nodeName,'child node name',false,array('line'=>__LINE__,'file'=>dirname(__FILE__)));
                _mizzou_log($objChildNode->nodeValue,'child node value',false,array('line'=>__LINE__,'file'=>dirname(__FILE__)));
            }

            /*
            foreach($objPrimaryMenu->ol->li as $intLiKey=>$objLI){
                if(trim($this->aryData['PageTitle']) == (string)$objLI->a){
                    _mizzou_log($objLI->a,'we have an li element that matches our page title of ' . $this->aryData['PageTitle'],false,array('line'=>__LINE__,'file'=>dirname(__FILE__)));
                    $strNewChild = "<ul><li>FOOBAR</li></ul>";
                    $objNewChild = simplexml_load_string($strNewChild);
                    $objPrimaryMenu->ol->li[$intLiKey]->addChild($objNewChild);
                } else {
                    _mizzou_log($objLI->a,'no match. objLi->a',false,array('line'=>__LINE__,'file'=>dirname(__FILE__)));
                }
                _mizzou_log($objLI,'objLi',false,array('line'=>__LINE__,'file'=>dirname(__FILE__)));
            }
            _mizzou_log($objPrimaryMenu,'our primary menu as an xml object',false,array('line'=>__LINE__,'file'=>dirname(__FILE__)));
            */
        }

    }


}