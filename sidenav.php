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

$strMenu = '';

if(isset($aryContext['menuName'])){
    $strMenu = $aryContext['menuName'];
} elseif(isset($aryContext['objMainPost'])) {
    $aryAncestors = get_post_ancestors($objMainPost->ID);
    if(count($aryAncestors) > 0){
        $intOldestAncestor = end($aryAncestors);
        $objOldestAncestor = get_post($intOldestAncestor);
        $strMenu = $objOldestAncestor->post_title;
    }
}

if($strMenu != ''){
    $aryMenuOptions = array(
        'menu' => $strMenu,
        'menu_class'=>'sidebar-navigation',
        'echo' => false,
    );

    $strMenuContents = wp_nav_menu($aryMenuOptions);

    $aryContex['menu'] = $strMenuContents;
    Content::render('menu', $aryContex);


}