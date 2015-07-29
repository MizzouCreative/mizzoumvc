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

class TemplateInjector {
	/**
	 * @var Unique Identifier
	 */
	protected $strPluginSlug;

	/**
	 * @var Reference to an instance of this class
	 */
	private static $objInstance = null;

	/**
	 * @var Array of templates that we need to inject
	 */
	protected $aryTemplates = array(
		'search.php'    => 'mvzSearch',
	);

	private function __construct()
	{
		$this->_removeOverriddenTemplates();

		if(count($this->aryTemplates) > 0){
			//inject templates into attributes metabox
			add_filter('page_attributes_dropdown_pages_args',array($this,'registerTemplates'));
			//inject templates into the page cache
			add_filter('wp_insert_post_data',array($this,'registerTemplates'));
			//return our template+path if a page has been assigned to it
			add_filter('template_include',array($this,'viewTemplate'));
		}
	}

	protected function _removeOverriddenTemplates()
	{
		foreach($this->aryTemplates as $strFile => $strTemplate){
			if('' != locate_template($strFile,false,false)){
				unset($this->aryTemplates[$strFile]);
			}
		}
	}

	public static function getInstance()
	{
		if(null == self::$objInstance){
			self::$objInstance = new TemplateInjector();
		}

		return self::$objInstance;
	}

	public function registerTemplates($aryAttributes)
	{
		_mizzou_log($aryAttributes,'what arguments were we given?',false,array('line'=>__LINE__,'file'=>__FILE__));
		//create unique key for theme cache
		$strCacheKey = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());
		//retrieve the current cache of templates
		$aryTemplates = wp_get_theme()->get_page_templates();
		_mizzou_log($aryTemplates,'list of templates from wordpress',false,array('line'=>__LINE__,'file'=>__FILE__));
		//remove the old cache
		wp_cache_delete($strCacheKey,'themes');

		$aryTemplates = array_merge($aryTemplates,$this->aryTemplates);
		_mizzou_log($aryTemplates,'new list of templates',false,array('line'=>__LINE__,'file'=>__FILE__));
		wp_cache_add($strCacheKey,$aryTemplates,'themes',1800);

		_mizzou_log( wp_get_theme()->get_page_templates(),'list of templates from wordpress after redoing the cache',false,array('line'=>__LINE__,'file'=>__FILE__));

		return $aryAttributes;

	}

	public function viewTemplate($strTemplate)
	{
		global $post;
		$strTemplateFile = get_post_meta($post->ID,'_wp_page_template',true);
		if(isset($this->aryTemplates[$strTemplate])){
			$strTemplate = dirname(__FILE__).DIRECTORY_SEPARATOR.$strTemplate;
		}

		return $strTemplate;
	}


}