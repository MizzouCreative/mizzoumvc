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
//_mizzou_log('',__FILE__.'called');
$strMenu = '';

if(isset($aryContext['objSite']) && $aryContext['objSite']->PageMenu != ''){
    $aryContext['menu'] = $aryContext['objSite']->PageMenu;
} else {
    if(isset($aryContext['menuName'])){
        $strMenu = $aryContext['menuName'];
    } elseif(isset($aryContext['objMainPost'])) {
        //_mizzou_log('','menu was not overridden so lets try and get the ancestors');
        $aryAncestors = get_post_ancestors($aryContext['objMainPost']->ID);
        //_mizzou_log($aryAncestors,'all of the ancestors');
        if(count($aryAncestors) > 0){
            $intOldestAncestor = end($aryAncestors);
            //_mizzou_log($intOldestAncestor,'the oldest ancestor id');
            $objOldestAncestor = get_post($intOldestAncestor);
            //_mizzou_log($objOldestAncestor,'the oldest ancestor object');
            $strMenu = $objOldestAncestor->post_title;

        } elseif(isset($aryContext['PageTitle'])) {
            $strMenu = $aryContext['PageTitle'];
        }
    }

    _mizzou_log($strMenu,'the menu we are going to attempt to look up',false,array('line'=>__LINE__,'file'=>__FILE__));

    if($strMenu != '' ){
        _mizzou_log($strMenu,'the menu I will look for');
        // @see http://codex.wordpress.org/Function_Reference/wp_nav_menu#Targeting_a_specific_Menu
        $aryMenuOptions = array(
            'menu' => $strMenu,
            'theme_location'=>'no_such_location',
            'echo' => false,
            'fallback_cb'=>'',
            'container'=>false,

        );

        $strMenuContents = wp_nav_menu($aryMenuOptions);
        $aryNavMenuItems = wp_get_nav_menu_items($strMenu);
        _mizzou_log($aryNavMenuItems,'return of nav get menu for ' . $strMenu);
        $aryContext['menu'] = $strMenuContents;
    }
}

if(isset($aryContext['menu'])){
    Content::render('menu',$aryContext);
}


