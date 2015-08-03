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

class IframeEmbed {
	private static $objInstance;

	public function __construct()
	{
		add_filter('oembed_dataparse',array($this,'injectTitleAttribute'));
	}

	public function getInstance()
	{
		if(null === self::$objInstance){
			self::$objInstance = new IframeEmbed();
		}

		return self::$objInstance;
	}

	/**
	 * Injects a title attribute into any iframe that is returned via automated oEmbed
	 *
	 * @param $strReturn string current html embed
	 * @param $objData data object returned from service provider
	 * @param $strUrl URL of service provider
	 *
	 * @return string html embed code
	 */
	public function injectTitleAttribute($strReturn,$objData,$strUrl)
	{
		if(1 === preg_match('/^<iframe (.*)><\/iframe>$/',$strReturn,$aryMatches) && isset($objData->title) && '' != $objData->title){

			//this is so that if the title has " contained it wont cause an issue
			$strTitle = htmlentities($objData->title,ENT_QUOTES,'UTF-8');

			$strReturn = '<iframe title="'.$strTitle.'" ' . $aryMatches[1].'></iframe>';
		}

		return $strReturn;
	}
}