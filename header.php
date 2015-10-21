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
 */
namespace MizzouMVC\controllers;

class Header extends Main {

	public function main()
	{
		$objHeader = $this->load('MizzouMVC\models\header');
		$this->aryRenderData = array_merge($this->aryRenderData,$objHeader->getTemplateData());
		$this->render('header');
	}
}

$objHeader = new Header((isset($aryContext) ? $aryContext : array()));
