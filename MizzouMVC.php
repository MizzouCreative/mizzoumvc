<?php
/**
 * Plugin Name: MizzouMVC
 * Plugin URI: http://universityaffairs.missouri.edu/department/web-communications/
 * Description: MVC Framework for rapid deployment of custom themes
 * Version: v1.1
 * Author: Paul F. Gilzow, Web Communications, University of Missouri
 * Author URI: http://universityaffairs.missouri.edu/department/web-communications/
 * @package 
 * @subpackage 
 * @since 
 * @category 
 * @category 
 * @uses 
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
define('MIZZOUMVC_ROOT_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
define('MIZZOUMVC_ROOT_URL',plugins_url('',__FILE__));
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'template-locator.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'functions.php';

//add_action('admin_menu','mizzoumvcRegisterAdminMenu');
//add_action('admin_menu','mizzoumvcRegisterThemeAdminMenu');
add_action('after_setup_theme','mizzouMVCShouldWeRegisterSettingsCPT');
register_activation_hook(__FILE__,'mizzouMVCPluginActivation');

function mizzouMVCShouldWeRegisterSettingsCPT()
{
    if(defined('MIZZOUMVC_COMPATIBLE') && MIZZOUMVC_COMPATIBLE){
        add_action('init','mizzouMVCRegisterSettingsCPT');
    }
}

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
        'labels'            => mizzouCreatePostTypeLabels('Setting'),
    ));
}

function mizzouSetUpInitialOptions()
{
    _mizzou_log(null,'intial setup options fired!',false,array('func'=>__FUNCTION__,'line'=>__LINE__,'file'=>__FILE__));
    $strConfigFile = dirname(__FILE__).DIRECTORY_SEPARATOR.'config.ini';
    //if we can get to our config file and parse it, add a settings page for each grouping
    if(file_exists($strConfigFile) && FALSE != $arySettings = parse_ini_file($strConfigFile,true)){
        foreach($arySettings as $strGroupSettingsKey => $arySettingsVals){
            //we only want to add the post if it doesnt already exist
            if(is_null($mxdSettingsObject = get_page_by_title($strGroupSettingsKey,OBJECT,'mizzoumvc-settings'))){
                $intSettingsPost = wp_insert_post(array(
                    'post_title' => ucwords(str_replace('-',' ',$strGroupSettingsKey)),
                    'post_content'=>'',
                    'post_status'=>'publish',
                    'post_type' => 'mizzoumvc-settings',
                ),true);
                //if we were able to store the page, lets enter the post meta
                if(!is_wp_error($intSettingsPost) && is_numeric($intSettingsPost)){
                    if(is_array($arySettingsVals)){
                        foreach($arySettingsVals as $strSettingsKey => $mxdSettingsVal){
                            if(!is_numeric($mxdMetaEntry = add_post_meta($intSettingsPost,$strSettingsKey,$mxdSettingsVal,true))){
                                _mizzou_log($mxdMetaEntry,'looks like adding a post meta for '.$strGroupSettingsKey.', id '.$intSettingsPost.' failed.',false,array('line'=>__LINE__,'file'=>__FILE__));
                                _mizzou_log($mxdSettingsVal,'we were trying to add the key ' . $strSettingsKey . ' and value',false,array('line'=>__LINE__,'file'=>__FILE__));
                            }
                        }
                    } else {
                        //ok, that should have been an array, if it isnt, what is it?
                        _mizzou_log($arySettingsVals,'what should be our settings array',false,array('line'=>__LINE__,'file'=>__FILE__));
                    }
                } else {
                    //strange, why werent we able to save our post?
                    _mizzou_log($intSettingsPost,'return after we tried saving our settings post ' . $strGroupSettingsKey,false,array('line'=>__LINE__,'file'=>__FILE__));
                }
            }
        }
    }
}

function mizzouMVCPluginActivation()
{
    mizzouSetUpInitialOptions();
    mizzouAddManagerRole();

}

function mizzouAddManagerRole()
{
    $objEditorRole = get_role('editor');
    _mizzou_log($objEditorRole,'the default editor role',false,array('line'=>__LINE__,'file'=>__FILE__));
}