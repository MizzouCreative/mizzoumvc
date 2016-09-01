<?php
/**
 * Plugin Name: MizzouMVC
 * Plugin URI: https://marcom.missouri.edu/department/miz-creative/
 * Description: MVC Framework for rapid custom theme development
 * Version: 3.5.0
 * Author: Paul F. Gilzow, Mizzou Creative, University of Missouri
 * Author URI: https://marcom.missouri.edu/department/miz-creative/
 * @package W
 * @subpackage 
 * @since 
 * @category 
 * @category 
 * @uses 
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 * @version 3.5.0
 */
/**
 * @todo let's check to see if the memory is low and then increase if needed
 */
define('MIZZOUMVC_VERSION','3.5.0');
define('MIZZOUMVC_ROOT_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('MIZZOUMVC_ROOT_URL',plugins_url('',__FILE__));
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'template-locator.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'TemplateInjector.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'IframeEmbed.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'plugin_update_check.php';

//add_action('admin_menu','mizzoumvcRegisterAdminMenu');
//add_action('admin_menu','mizzoumvcRegisterThemeAdminMenu');
add_action('after_switch_theme','mizzouSetUpInitialOptions');
add_action('after_setup_theme','mizzouMVCShouldWeRegisterSettingsCPT');
add_action('plugins_loaded',array('TemplateInjector','getInstance'));
add_action('init',array('IframeEmbed','getInstance'),10,3);
//add_filter('embed_oembed_html','mizzouMVCYoutube',10,3);
//add_filter('oembed_dataparse','mizzouMVCYoutube',10,3);
register_activation_hook(__FILE__,'mizzouMVCPluginActivation');

/**
 * Adds oEmbed support for youtube links.
 * @param $strReturn
 * @param $objData
 * @param $strUrl
 *
 * @return string
 *
 * @deprecated wordpress added this functionality in directly
 */
function mizzouMVCYoutube($strReturn,$objData,$strUrl)
{
	_mizzou_log($strReturn,'current contents of strReturn before preg_match',false,array('line'=>__LINE__,'file'=>__FILE__));

	if(1 === preg_match('/^<iframe (.*)><\/iframe>$/',$strReturn,$aryMatches) && isset($objData->title) && '' != $objData->title){
		$strReturn = '<iframe title="'.$objData->title.'" ' . $aryMatches[1].'></iframe>';
	}

	_mizzou_log($strReturn,'contents of strReturn after preg_match',false,array('line'=>__LINE__,'file'=>__FILE__));
	_mizzou_log($objData,'current contents of objData',false,array('line'=>__LINE__,'file'=>__FILE__));
	_mizzou_log($strUrl,'current contents of strUrl',false,array('line'=>__LINE__,'file'=>__FILE__));

	return $strReturn;
}

/**
 * Determines if we should register the CPT for theme settings
 */
function mizzouMVCShouldWeRegisterSettingsCPT()
{
    if(defined('MIZZOUMVC_COMPATIBLE') && MIZZOUMVC_COMPATIBLE){
        add_action('init','mizzouMVCRegisterSettingsCPT');
    }
}

/**
 * Registers the mizzoumvc-settings CPT
 */
function mizzouMVCRegisterSettingsCPT()
{
    mizzouRegisterPostType('mizzoumvc-settings',array(
        'public'            =>false,
        'has_archive'       =>false,
        'show_ui'           =>true,
        'show_in_nav_menus' =>true,
        'show_in_menu'      =>'themes.php',
        'menu_position'     =>null,
        'supports'          =>array(
                'title',
                'custom-fields',
                'page-attributes'
        ),
        'rewrite'           =>false,
        'query_var'         =>false,
        'labels'            => mizzouCreatePostTypeLabels('Theme Setting'),
    ));
}

/**
 * Adds the initial theme settings when activating the theme
 */
function mizzouSetUpInitialOptions()
{
    $strOptionsLoadedKeyName = 'mizzouMVC_theme_options_loaded';

	/**
	 * @todo we have to make an assumption that we'll never have two different mizzouMVC frameworks being used for a single
	 * site where they arent going to need the same set of settings.  Verify this assumption
	 */
	//Is the theme dependent on our framework and have we already loaded base settings before?
	if(defined('MIZZOUMVC_COMPATIBLE') && MIZZOUMVC_COMPATIBLE &&  FALSE == get_option($strOptionsLoadedKeyName)){
		//if we can get to our config file and parse it, add a settings page for each grouping
		// @todo should config.ini be an option some where?
		$arySettingsFiles = array(
			//plugin config.ini
			dirname(__FILE__).DIRECTORY_SEPARATOR.'config.ini',
			//theme config.ini
			get_stylesheet_directory().DIRECTORY_SEPARATOR.'config.ini',
		);

		foreach($arySettingsFiles as $strSettingsFile){
			if(count($arySettings = mizzouMVCLoadOptionsFile($strSettingsFile)) > 0){
				foreach($arySettings as $strGroupSettingsKey => $arySettingsVals){
					//why page_by_path? because we're getting what should end up being the slug as the settings page key
					if(is_null($objSettingsPost = get_page_by_path($strGroupSettingsKey,OBJECT,'mizzoumvc-settings'))){
						//we dont have a settings page, so let's create one for this group
						$intSettingsPost = wp_insert_post(array(
							'post_title' => ucwords(str_replace('-',' ',$strGroupSettingsKey)),
							'post_content'=>'',
							'post_status'=>'publish',
							'post_type' => 'mizzoumvc-settings',
						),true);
					} else {
						$intSettingsPost = $objSettingsPost->ID;
					}

					//let's make double sure we have a post id
					if(!is_wp_error($intSettingsPost) && is_int($intSettingsPost)){
						//now get all of the custom meta data for this post
						$aryCustomMeta = get_post_custom($intSettingsPost);
						_mizzou_log($arySettingsVals,'setting options for group ' . $strGroupSettingsKey );
						_mizzou_log($aryCustomMeta,'custom meta data for post ' . $intSettingsPost,false,array('line'=>__LINE__,'file'=>__FILE__));
						//see if there are any keys in the config file that werent already in the settings page
						$aryDiffKeys = array_diff_key($arySettingsVals,$aryCustomMeta);
						_mizzou_log($aryDiffKeys,'result from aryDiffKeys',false,array('line'=>__LINE__,'file'=>__FILE__));
						//if so, lets add them
						foreach($aryDiffKeys as $strCustomSettingKey=>$mxdCustomSettingVal){
							if(!is_numeric($mxdMetaEntry = add_post_meta($intSettingsPost,$strCustomSettingKey,$mxdCustomSettingVal,true))){
								_mizzou_log($mxdMetaEntry,'looks like adding a post meta for '.$strGroupSettingsKey.', id '.$intSettingsPost.' failed.',false,array('line'=>__LINE__,'file'=>__FILE__));
								_mizzou_log($mxdCustomSettingVal,'we were trying to add the key ' . $strCustomSettingKey . ' and value',false,array('line'=>__LINE__,'file'=>__FILE__));
							}
						}
					}
				}
			}
		}

		if(!update_option($strOptionsLoadedKeyName,1)){
			_mizzou_log(null,'just tried to create an option for ' . $strOptionsLoadedKeyName . ' but it failed.',false,array('line'=>__LINE__,'file'=>__FILE__));
		}
	}
}

/**
 * Loads and parses config.ini file
 * @param string $strFile
 * @return array
 */
function mizzouMVCLoadOptionsFile($strFile)
{
    $aryReturn = array();
    if(is_readable($strFile)){
        if(false !== $arySettings = parse_ini_file($strFile,true)){
            $aryReturn = $arySettings;
        }
    }

    return $aryReturn;
}

/**
 * Functions to fire on plugin activation
 */
function mizzouMVCPluginActivation()
{
	/**
	 * moved to after_theme_switch hook
	 */
    //mizzouSetUpInitialOptions();
	/**
	 * @todo since we're only performing one function now, does it still make sense to hook to this function instead
	 * of hooking directly to the addManagerRole function?
	 */
	mizzouAddManagerRole();

}

/**
 * Adds a custom role of Manager to wordpress' roles
 * @todo lets move this into the Manager class?
 */
function mizzouAddManagerRole()
{
    //we dont want to add the manager role if it already exists
    if(is_null($objManager = get_role('manager'))){
        $aryNewCaps = array(
            'edit_users',
            'list_users',
            'promote_users',
            'create_users',
            'add_users',
            'delete_users',
            'remove_users',
        );

        //get the editor role so we can clone it
        $objEditorRole = get_role('editor');
        //create our new role with the same caps as editor
        $objManagerRole = add_role('manager','Manager',$objEditorRole->capabilities);
        if(!is_null($objManagerRole) && $objManagerRole instanceof WP_Role){
            //now let's add on our extra caps
            foreach($aryNewCaps as $strNewCap){
                $objManagerRole->add_cap($strNewCap);
            }
        } else {
            //what happened?
        }
    }
}

/**
 * Kernl.us private plugin hosting support
 */
$MyUpdateChecker = new PluginUpdateChecker_2_0 (
	'https://kernl.us/api/v1/updates/56e873cdad9740ca2010948d/',
	__FILE__,
	'mizzoumvc',
	1
);
