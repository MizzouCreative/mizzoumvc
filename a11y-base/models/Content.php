<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 7/10/14
 * Time: 1:55 PM
 * ASSUMES that Base.php and Site.php classes have already been included
 */

class Content extends Base {
    protected static $aryDefaultOptions = array(
        'include_sidebars'  => false,
        'override_outerview'=>false,
    );

    protected static $aryIncludeSidebarPages = array(
        'about',
        'strategic-plan',
        'annual-reports',
        'contact',
        'staff',
        'policy-research-scholars',
        'graduate-research-assistants'
    );

    protected static $arySiteSiteMembers = array('URL','Name');

    public static function render($strInnerViewFileName,$aryData,$aryOptions=array())
    {
        $strEditPostLink                = '';
        $boolIncludeNoIndex             = false;
        $boolIncludeSidebar             = false;
        $boolIncludeImageAboveHeader    = false;

        $aryOptions = array_merge(self::$aryDefaultOptions,$aryOptions);

        extract($aryData);

        if(!isset($objSite) || !is_object($objSite)){
            $objSite = new Site();
        }

        //do we need the EditPostLink?
        if((is_single() || is_page()) && '' != $strPostLink = get_edit_post_link()){
            $strEditPostLink = ' ' . $strPostLink;
        }

        /**
         * Page specific checks...
         */
        if(is_page() && isset($objMainPost)){
            /**
             * @wp-hack
             * hack. we only want the sidebar on specific pages. change this into a function that determines if a sidebar is actually
             * needed
             */
            if(in_array($objMainPost->slug,self::$aryIncludeSidebarPages)){
                $boolIncludeSidebar = true;
            }

            if($objMainPost->image != ''){
                $boolIncludeImageAboveHeader = true;
            }
        }

        /**
         * This one is a bit of a bugger...
         * If we have access to the MainPost object AND either noindex or nolink is set and ON
         * OR
         * we're on a 404 page
         * then
         * we want to include the meta element for robots to not index the page
         *
         */
        if(
            is_404()
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
        }

        /**
         * @todo this needs to be moved either into a theme option or config file
         */
        $intSpanWidth = ($boolIncludeSidebar) ? 8 : 12;

        /**
         * For now, we want to make both the $objSite-> members and direct variables available to designers. Eventually
         * we'll decide one way or the other
         */

        foreach($objSite->currentPublicMembers() as $strSiteKey){
            $strSiteVariable = '';
            if(in_array($strSiteKey,self::$arySiteSiteMembers)) {
                $strSiteVariable = 'Site'.$strSiteKey;
            } else {
                $strSiteVariable = $strSiteKey;
            }

            $strSiteVariable = 'str'.$strSiteVariable;

            $$strSiteVariable = $objSite->{$strSiteKey};
        }

        //outerView needs breadcrumbs and inner view data
        /**
         * @todo the breadcrumbs plugin needs to be converted to a Model with a matching view
         */
        //get the contents for the breadcrumbs
        $strBreadCrumbs = self::_captureOutput('breadcrumbs');

        /**
         * Temporary setting of strPageTitle
         * @todo dont let this go to production. Finish out the method
         */
        $strPageTitle = self::_determinePageTitle();

        $strThemePath = $objSite->ActiveThemePath;
        $strViewsPath = $strThemePath.'views'.DIRECTORY_SEPARATOR;
        $strInnerView = $strViewsPath . $strInnerViewFileName . '.php';

        //now we need to start getting everyhing

        //_mizzou_log($strInnerView,'attempting to get: ');
        //get contents from the inner view
        if(!$aryOptions['override_outerview']){
            if(file_exists($strInnerView)){
                ob_start();
                require_once $strInnerView;
                $strInnerViewContent = ob_get_contents();
                ob_end_clean();
            } else {
                $strInnerViewContent = '<p>Unable to retrieve inner view.</p>';
            }
        }



        /**
         * @todo captureContents is in the site model so we need to expand it to allow for storage of this type of data
         */
        $strWpHeaderContents = $objSite->wpHeader;
        $strSearchFormContents = $objSite->SearchForm;
        $strWpFooterContents = $objSite->wpFooter;

        //start actual output

        // replaces get_header();
        require_once $strViewsPath.'header.php';
        /**
         * @todo replace with a require to the sidebar view
         */
        if($boolIncludeSidebar) {
            get_sidebar();
        }

        if($aryOptions['override_outerview']){
            require_once $strInnerView;
        } else {
            require_once $strViewsPath . 'outerView.php';
        }


        // replaces get_footer();
        require_once $strViewsPath . 'footer.php';

    }

    protected function _determinePageTitle()
    {
        $strPageTitle = wp_title('',false);
        if(is_archive()){
            if(is_date()){

            } else {
                /**
                 * If it isn't a dated archive, has it been filtered by a taxonomy?
                 */
                global $wp_query;
                $objQueried = get_queried_object();
                if(is_object($objQueried) && count($wp_query->tax_query->queries) > 0){
                    $strPageTitle = $objQueried->name . ' ' . $strPageTitle;
                }
            }
        }
    }
} 