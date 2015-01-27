<?php
/**
 * Template file used to contain header content of theme pages
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 *
 * If called via a template, will have access to $aryContext which contains all variables given to the templating engine
 *
 * Will need to gather
 *  - <title> contents: $strHeadTitle
 *  - template location: see $objSite below
 *  - site option for tracking code: see $objSite below
 *  - blog url: see $objSite below
 *  - blog name: see $objSite below
 *  - navigation menus: see $objSite below
 *  - search form: $strSearchFormContents
 *  - contents of wp_head: $strWpHeaderContents
 *  - whether or not to include <meta> robots: $boolIncludeNoIndex
 *
 *  $objSite contains
 *  -> CopyrightYear (also accessible as $strCopyrightYear)
 *  -> Name (also accessible as $strSiteName)
 *  -> URL (also accessible as $strSiteURL)
 *  -> ParentThemeURL (also accessible as $strParentThemeURL)
 *  -> ChildThemeURL (also accessible as $strChildThemeURL)
 *  -> ActiveStylesheet (also accessible as $strActiveStylesheet)
 *  -> ActiveThemeURL (also accessible as $strActiveThemeURL)
 *  -> TrackingCode (also accessible as $strTrackingCode)
 *  -> PrimaryMenu (also accessible as $strPrimaryMenu)
 *  -> AudienceMenu (also accessible as $strAudienceMenu)
 *  -> LastModifiedDate (also accessible as $strModifiedDate)
 *
 * @todo since wpHeaderContents will ALWAYS be in the header, it makes more sense to move that into here, than having
 * the site model store it
 */

if(!isset($aryContext)){
    _mizzou_log(get_defined_vars(),'aryContext isnt defined for some reason. here is everything that is.',false,array('file'=>__FILE__));
} elseif(!isset($aryContext['objSite'])) {
    _mizzou_log($aryContext,'aryContext is set, but objSite is not. Contents of aryContext',false,array('file'=>__FILE__));
} else {
    _mizzou_log('','objSite IS SET before we create our header object',false,array('file'=>__FILE__));
}

$objHeader = new Header($aryContext);

Content::render('header',$objHeader->getTemplateData(),array('include_header'=>false,'include_footer'=>false));
