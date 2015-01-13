<?php
/**
 * Template file used to contain header content of theme pages
 *
 * @package WordPress
 * @subpackage Generic Theme
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
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

class Header {
    private $aryViewVariables = array();

    function __construct($aryContext){
        //@todo do we really need to extract here? cant we just leave them in the array?
        extract($aryContext);

        if(!isset($objSite)){
            /**
             * @todo throw a error/exception?
             */
        }

        if(!isset($objPostType)) $objPostType = null;
        //@todo page title should always be set, throw an exception?
        if(!isset($PageTitle)) $PageTitle = '';

        $this->aryViewVariables['HeadTitle'] = $this->_determineHeaderTitle($PageTitle,$objSite->name,$objPostType);

        /**
         * This one is a bit of a bugger...
         * If we have access to the MainPost object AND either noindex or nolink is set and ON
         * OR
         * we're on a 404 page
         * OR
         * we're on a search page
         * then
         * we want to include the meta element for robots to not index the page
         * @todo this is header stuff and should be moved to the header controller
         *
         */
        if(
            is_404() || is_search()
            || (
                isset($objMainPost)
                && (
                    (isset($objMainPost->noindex) && $objMainPost->noindex == 'on')
                    ||
                    (isset($objMainPost->nolink) && $objMainPost->nolink == 'on')
                )
            )
        ) {
            $boolIncludeNoIndex = true;
            $aryViewVariables['IncludeNoIndex'] = $boolIncludeNoIndex;
        }
    }

    /**
     * @param string $strPageTitle
     * @param string $strSiteName
     * @param object $objPostType
     * @return string <title> element contents
     */
    protected function _determineHeaderTitle($strPageTitle,$strSiteName,$objPostType)
    {
        $aryTitleParts = array();
        $strPageTitle = trim(strip_tags($strPageTitle));

        if('' != $strPageTitle){
            $aryTitleParts[] = $strPageTitle;
        }



        /**
         * @todo Should we check to see if it isnt null, or check to see if it is an object? We're making an assumption
         * right now that if it isnt null, then ->labels and ->labels->name have been set and are accessible
         */
        if(!is_null($objPostType) && $strPageTitle !== $objPostType->labels->name){
            $aryTitleParts[] = $objPostType->labels->name;
        }

        $aryTitleParts[] = $strSiteName;
        /**
         * @todo this piece should come from a Theme options class
         */
        $aryTitleParts[] = 'University of Missouri';

        //_mizzou_log($aryTitleParts,'aryTitleParts right before we implode');
        /**
         * @todo implosion glue should come from a Theme options class
         */
        return implode(' // ',$aryTitleParts);
    }

    public function getHeaderVariables()
    {
        return $this->aryViewVariables;
    }

}

$objHeader = new Header($aryContext);

Content::render('header',$objHeader->getHeaderVariables(),array('include_header'=>false,'include_footer'=>false));
