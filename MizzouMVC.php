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

function mizzoumvcRegisterAdminMenu()
{
    add_options_page('MizzouMVC Settings','MizzouMVC','manage_options','mizzoumvc','mizzoumvcAdminMenuTest');
}

function mizzoumvcRegisterThemeAdminMenu()
{
    if(defined('MIZZOUMVC_COMPATIBLE') && MIZZOUMVC_COMPATIBLE){
        add_theme_page('Theme Settings','Theme Settings','edit_theme_options','mizzoumvc-theme','mizzoumvcThemeSettings');
    }
}

function mizzoumvcAdminMenuTest()
{
    echo 'MizzouMVC Settings Test';
}

function mizzoumvcThemeSettings()
{
    echo 'MizzouMVC Theme Settings Test';
}

function mizzouMVCShouldWeRegisterSettingsCPT()
{
    if(defined('MIZZOUMVC_COMPATIBLE') && MIZZOUMVC_COMPATIBLE){
        add_action('init','mizzouMVCRegisterSettingsCPT');
    }
}

function mizzouMVCRegisterSettingsCPT()
{
    mizzouRegisterPostType('mizzoumvc-settings',array(
        'public'=>false,
        'has_archive'=>false,
        'show_ui'=>true,
        'show_in_nav_menus'=>true,
        'show_in_menu'=>'themes.php',
        'menu_position'=>null,
        'supports'=>array(
            'title',
            'custom-fields',
            'page-attributes'
        ),
        'rewrite'=>false,
        'query_var'=>false,



    ));
}