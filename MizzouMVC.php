<?php
/**
 * Plugin Name: MizzouMVC
 * Plugin URI: https://marcom.missouri.edu/department/miz-creative/
 * Description: MVC Framework for rapid custom theme development
 * Version: 3.8.0
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
 * @version 3.8.0
 */
/**
 * @todo let's check to see if the memory is low and then increase if needed
 */
define('MIZZOUMVC_VERSION','3.8.0');
define('MIZZOUMVC_ROOT_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('MIZZOUMVC_ROOT_URL',plugins_url('',__FILE__));
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'template-locator.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'TemplateInjector.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'IframeEmbed.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'MizzouMvcCLI.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'plugin_update_check.php';

//add_action('admin_menu','mizzoumvcRegisterAdminMenu');
//add_action('admin_menu','mizzoumvcRegisterThemeAdminMenu');
add_action('after_switch_theme','mizzouSetUpInitialOptions');
add_action('after_setup_theme','mizzouMVCShouldWeRegisterSettingsCPT');
add_action('init',array('TemplateInjector','getInstance'));
add_action('init',array('IframeEmbed','getInstance'),10,3);
add_action('init','mizzouManagerRoleFixCheck');
//add_filter('embed_oembed_html','mizzouMVCYoutube',10,3);
//add_filter('oembed_dataparse','mizzouMVCYoutube',10,3);
register_activation_hook(__FILE__,'mizzouMVCPluginActivation');

//make sure our plugin registration function fires when a new child site is added
add_action('wpmu_new_blog',function ($intBlogID){
    //make sure the function has loaded before calling it
    if(!function_exists('is_plugin_active_for_network')){
        require_once ABSPATH . DIRECTORY_SEPARATOR . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php';
    }

    if(is_plugin_active_for_network('mizzoumvc/MizzouMVC.php')){
        mizzouMVCPluginActivation(true,array($intBlogID));
    }
},10,1);

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

        /**
         * @todo we need some higher-order way of tracking optional "themes" that we might have access to. right now that list is
         * hard-coded partially in TemplateInjector and partially in other locations
         */
        $aryPageAndTemplates = array(
            'search',
            'calendar',
            'news'
        );

        foreach($aryPageAndTemplates as $strTemplate){
            if(is_null(get_page_by_path($strTemplate))){
                $aryPageParams = array(
                    'post_title'    => ucwords($strTemplate),
                    'post_status'   => 'publish',
                    'post_type'     => 'page',
                    'comment_status'=> 'closed',
                    'ping_status'   => 'closed',
                    'meta_input'    => array(
                        '_wp_page_template' => $strTemplate . '.php',
                    ),
                );

                wp_insert_post($aryPageParams);
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
 * @param boolean $boolNetworkActivated is the plugin network activated
 * @param array $arySites
 * @return void
 * @todo needs to be refactored. super messy.
 */
function mizzouMVCPluginActivation($boolNetworkActivated = false, $arySites = array())
{
    /**
     * moved to after_theme_switch hook
     */
    //mizzouSetUpInitialOptions();

    //were we network activated?
    if(is_multisite() && $boolNetworkActivated){
        if(0 === count($arySites)){
            $arySites = mizzouRetrieveSiteIDs();
        }

        foreach ($arySites as $intBlogID){
            switch_to_blog($intBlogID);
            mizzouAddManagerRole();
            restore_current_blog();
        }
    } else {
        mizzouAddManagerRole();
    }
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
 * Checks to see if we've ensured child sites have the manager role when the plugin is network activated
 * @return void
 */
function mizzouManagerRoleFixCheck()
{
    if(is_multisite() && false === get_option('mizzou_managerrole_check') ){
        //check that plugin.php has loaded
        if(!function_exists('is_plugin_active_for_network')){
            require_once ABSPATH . DIRECTORY_SEPARATOR . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php';
        }
        if(is_plugin_active_for_network('mizzoumvc/MizzouMVC.php')){
            /*
             * we're in a multisite, the plugin is network activate but we dont have the check flag.
             */
            mizzouMVCPluginActivation(true);
            add_option('mizzou_managerrole_check',1,'','no');
        }
    }
}

/**
 * Retrieves an array of site IDs
 * @return array
 */
function mizzouRetrieveSiteIDs()
{
    if(class_exists('WP_Site_Query')){
        //wordpress 4.6+
        $objSiteQuery = new WP_Site_Query(array('fields'=>'ids'));
        $arySites = $objSiteQuery->get_sites();
    } else {
        // old wordpress
        global $wpdb;
        $arySites = $wpdb->get_col("SELECT blog_id from $wpdb->blogs");
    }

    return $arySites;
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
