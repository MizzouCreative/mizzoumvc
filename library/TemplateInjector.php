<?php
/**
 *  Allows controllers from the framework to register as selectable "templates" in a Page
 */
/**
 * Allows for theme templates from the framework to be selectable and used by Pages
 *
 * As of this time, we only have two theme templates (Search & Calendar) but this can be expanded. Heavily inspired by
 * @see http://www.wpexplorer.com/wordpress-page-templates-plugin/
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category library
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 * @uses MIZZOUMVC_ROOT_PATH
 * @todo see if there is some way to parse our own controllers for Template Name, and then add them to our list of
 * templates. Similar to this: http://pastie.org/10071582 mirror: http://pastebin.com/rHUxkceK
 * @todo shouldnt this be namespaced?
 */
class TemplateInjector {

	/**
	 * @var TemplateInjector instance of this class
	 */
	private static $objInstance = null;

	/**
	 * @var Array of templates that we need to inject
	 * file.php => Template Name to use
	 */
	protected $aryTemplates = array(
		'search.php'    => 'mvzSearch',
        'calendar.php'  => 'mvzCalendar',
	);

    /**
     * registers the necessary filter hooks with wordpress
     */
    private function __construct()
	{
		$this->_removeOverriddenTemplates();

		if(count($this->aryTemplates) > 0){
			//inject templates into attributes metabox
			if(version_compare(floatval(get_bloginfo('version')),'4.7','<')){
			    // before v4.6.X of wordpress
                add_filter('page_attributes_dropdown_pages_args',array($this,'injectTemplates'));
			} else {
                //4.7+ method
                add_filter('theme_page_templates',array($this,'injectTemplatesNew'));
            }


			//inject templates into the page cache
			add_filter('wp_insert_post_data',array($this,'injectTemplates'));
			//return our template+path if a page has been assigned to it
			add_filter('template_include',array($this,'viewTemplate'));
		}
	}

	/**
	 * If a theme has overridden one of our templates, remove it from the listing
     * @return void
	 */
	protected function _removeOverriddenTemplates()
	{
		foreach($this->aryTemplates as $strFile => $strTemplate){
			if('' != locate_template($strFile,false,false)){
				unset($this->aryTemplates[$strFile]);
			}
		}
	}

	/**
     * Creates and/or returns static instance of class
	 * @return TemplateInjector
	 */
	public static function getInstance()
	{
		if(null == self::$objInstance){
			self::$objInstance = new TemplateInjector();
		}

		return self::$objInstance;
	}

	/**
	 * Injects our theme templates into Wordpress' list of theme templates in < 4.7
	 *
	 * @param array $aryAttributes
	 *
	 * @return array
	 */
	public function injectTemplates($aryAttributes)
	{
		//create unique key for theme cache
		$strCacheKey = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());
		//retrieve the current list of templates
		$aryTemplates = wp_get_theme()->get_page_templates();
		//remove the old cache
		wp_cache_delete($strCacheKey,'themes');
		//merge our templates in with the originals
		$aryTemplates = array_merge($aryTemplates,$this->aryTemplates);
		//rebuild the cache with our newly added templates
		wp_cache_add($strCacheKey,$aryTemplates,'themes',1800);

		return $aryAttributes;

	}

    /**
     * Injects our theme templates into Wordpress' list of templates in >= 4.7
     * @param $aryTemplates
     * @return array
     */
	public function injectTemplatesNew($aryTemplates)
    {
        return array_merge($aryTemplates,$this->aryTemplates);
    }

	/**
	 * If the page has been assigned to our template, returns the full path to the template file
	 *
	 * @param string $strTemplate
	 *
	 * @return string
	 */
	public function viewTemplate($strTemplate)
	{
		global $post;

		if('page' == $post->post_type && !isset($this->aryTemplates[basename($strTemplate)])){
			$strTemplateFile = get_post_meta($post->ID,'_wp_page_template',true);
			if(isset($this->aryTemplates[$strTemplateFile])){
				$strTemplate = MIZZOUMVC_ROOT_PATH.$strTemplateFile;
			}
		}

		return $strTemplate;
	}
}