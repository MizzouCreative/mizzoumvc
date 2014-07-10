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
        /**
         * Also temporary
         * @todo dont let this go to production
         */
        $strHeaderTitle = self::_getHeaderTitle($strPageTitle);

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

        return $strPageTitle;
    }

    protected function _determinePagePath($strPageTitle,$strSiteName='')
    {
        $aryPath = array();

        if(is_null($strPageTitle)){
            $strPageTitle = self::_determinePageTitle();
        } else {
            /**
             * since we need this in the header, and its possible for the controller to manually set the page title, we
             * need to make sure there isn't any html in the page title
             */
            $strPageTitle = strip_tags($strPageTitle);
        }

        if(is_archive() || is_single()){
            //ok, we have a lot of different archives to deal with. let's separate out the single
            $strPostType = get_post_type();

            if($strPostType != 'post'){
                $objPostType = get_post_type_object($strPostType);
                $strPostTypeName = $objPostType->labels->name;
                $strPostTypeURL = get_post_type_archive_link($strPostType);
            } else {
                /**
                 * @todo the name of the default post type should either be pulled dynamically or moved into the theme
                 * options so we can get it from there, not typed statically.
                 */
                $strPostTypeName = 'Blog';
                $strPostTypeURL = get_permalink(get_option('page_for_posts'));
            }

            if(is_single()){
                if(!empty($strPageTitle) && $strPageTitle != ''){
                    $aryPath[$strPageTitle] = null; //we dont include a URL for the page we are on
                }
                $aryPath[$strPostTypeName] = $strPostTypeURL;

            } else {
                /**
                 * ok, what type of archive are we dealing with. Also, seems silly that we have to check specifically for
                 * category and tag considering they are just default taxonomies and dont really differ.
                 */
                if(is_date()){
                    $strYear    = get_the_time('Y');
                    $strYearURL = null;
                    $strMonth   = get_the_time('m');
                    $strMonthURL= null;

                    $strDateArchiveType = null;

                    if(is_day()){
                        $strDateArchiveType = 'day';
                    } elseif(is_month()){
                        $strDateArchiveType = 'month';
                    } elseif(is_year()){
                        $strDateArchiveType = 'year';
                    }

                    switch($strDateArchiveType){
                        case 'day':
                            $aryPath[get_the_time('d')] = null;
                            $strMonthURL = get_month_link($strYear,$strMonth);
                            $strYearURL = get_year_link($strYear);
                        //pass-through done intentionally
                        case 'month':
                            /**
                             * $strMonth is the numeric representation of our month (e.g. 06), but we'll want it in text
                             * format
                             */
                            $objDate = DateTime::createFromFormat('!m',$strMonth);
                            $aryPath[$objDate->format('F')] = $strMonthURL;
                            if(is_null($strYearURL)) $strYearURL = get_year_link($strYear);
                        //pass-through done intentionally
                        case 'year':
                            $aryPath[$strYear] = $strYearURL;
                            break;

                        default:
                            /**
                             * @todo besides log, do we need to throw an exception?
                             */
                            _mizzou_log($strDateArchiveType,'we are in a date archive, but if failed day, month and year checks',false,array('func'=>__FUNCTION__));
                            break;
                    }

                } elseif(is_tax() || is_tag() || is_category()){
                    $strTerm = get_query_var('term');
                    $strTaxonomy = get_query_var('taxonomy');
                    $objTaxonomy = get_taxonomy($strTaxonomy);
                    $objTerm = get_term_by('slug',$strTerm,$strTaxonomy);
                    /**
                    _mizzou_log($strTerm,'the cat/tag/tax term');
                    _mizzou_log($strTaxonomy,'the cat/tag/tax name');
                    _mizzou_log($objTaxonomy,'the taxonomy object');
                    _mizzou_log($objTerm,'the taxonomy term object');
                     */
                    $aryPath[$objTerm->name] = null;

                }

                $aryPath[$strPostTypeName] = $strPostTypeURL;

            }
        } elseif(is_page()) {
            //do sub sub pages need to have that information reflected in the title?
        } else {
            //it's not an archive a single, or a page. what do we have left?
        }

        /**
         * @todo get this data from the site object
         */
        if($strSiteName == ''){
            $strSiteName = get_bloginfo('name');
        }

        /**
         * @todo get this data from the site object
         */
        $aryPath[$strSiteName] = home_url();

        /**
         * Inclusion of this information should be in a theme option
         * @todo include theme option for this
         */
        $aryPath['University of Missouri'] = 'http://missouri.edu/';

        return $aryPath;
    }

    protected function _getHeaderTitle($strPageTitle)
    {
        $aryParts = self::_determinePagePath($strPageTitle);
        $aryTitleParts = array_keys($aryParts);
        /**
         * @todo implosion glue should come from a theme option
         */
        return implode(' // ', $aryTitleParts);
    }
} 