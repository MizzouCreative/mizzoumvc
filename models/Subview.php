<?php
/**
 * 
 * TL;DR description 
 *
 * @package 
 * @subpackage 
 * @category 
 * @category 
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */

class Subview extends Base {

	public function __construct($aryContext)
	{
		array_merge($this->aryData,$aryContext);

		if(!isset($this->aryData['objSite'])){
			/**
			 * @todo we need to do something here since every subview needs access to objSite. Throw exception?
			 */
		}
	}

	public function getTemplateData()
	{
		return $this->aryData;
	}
}