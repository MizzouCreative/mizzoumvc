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
		//@todo do we really need to extract here? cant we just leave them in the array?
		array_merge($this->aryData,$aryContext);

		if(!isset($this->aryData['objSite'])){
			/**
			 * @todo throw a error/exception? We can't really continue without it
			 */
			_mizzou_log($this->aryData,'whoa... objSite isnt set. what is?',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
		}

		//if we werent given the post type object, set it to null
		if(!isset($this->aryData['objPostType'])) $this->add_data('objPostType',null);
		//@todo page title should always be set, throw an exception?
		if(!isset($this->aryData['PageTitle'])) $this->add_data('PageTitle','');

		$this->_setHeaderTitle();
		$this->_setIncludeNoIndex();
		$this->_setWpHead();
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

	protected function _setHeaderTitle($strHeaderTitle=null)
	{
		if(is_null($strHeaderTitle)){
			$strHeaderTitle = $this->_determineHeaderTitle();
		}

		$this->add_data('HeaderTitle',$strHeaderTitle);
	}

	protected function _setWpHead()
	{
		$this->add_data('wpHead',$this->_captureOutput('wp_head'));
	}


}