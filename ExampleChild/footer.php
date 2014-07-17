<?php
/**
 * Template file used to render the footer of the site
 * 
 * 
 * @package WordPress
 * @subpackage mizzou-news
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @author Paul Gilzow, Charlie Tripplet, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 *
 *
 */

$objSite = new Site();

$strSiteName = $objSite->Name;
$strSiteURL = $objSite->URL;
$strParentThemeURL = $objSite->ParentThemeURL;
$strChildThemeURL = $objSite->ChildThemeURL;
$strModifiedDate = $objSite->getLastModifiedDate();
$strPageList = $objSite->getPageList();
$intCopyrightYear = $objSite->CopyrightYear;

ob_start();
wp_footer();
$strWpFooterContents = ob_get_contents();
ob_end_clean();
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'footer.php';