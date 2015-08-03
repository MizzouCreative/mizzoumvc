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

class YoutubeEmbed {
	private static $objInstance;

	public function __construct()
	{
		add_filter('oembed_dataparse',array($this,'injectTitleAttribute'));
	}

	public function getInstance()
	{
		if(null === self::$objInstance){
			self::$objInstance = new YoutubeEmbed();
		}

		return self::$objInstance;
	}

	public function injectTitleAttribute($strReturn,$objData,$strUrl)
	{
		_mizzou_log($strReturn,'current contents of strReturn',false,array('line'=>__LINE__,'file'=>__FILE__));
		_mizzou_log($objData,'current contents of objData',false,array('line'=>__LINE__,'file'=>__FILE__));
		_mizzou_log($strUrl,'current contents of strUrl',false,array('line'=>__LINE__,'file'=>__FILE__));

		return $strReturn;
	}
}