<?php
/**
 * Helper for outputting the different view files that make up a page
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category helper
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */

/**
 * @param $strInnerViewFileName
 * @param $aryData
 * @uses breadcrumbs() from Mizzou Breadcrumbs plugin
 * @uses get_template_directory() from Wordpress core
 * @uses mizzouDeterminePathToTheme() from helpers\paths.php
 * @todo this seriously needs refactoring
 */
function mizzouOutPutView($strInnerViewFileName,$aryData,$aryOptions=array())
{
    $aryDefaultOptions = array(
        'include_sidebars'  => false,
        'override_outerview'=>false,
    );

    $aryOptions = array_merge($aryDefaultOptions,$aryOptions);

    $aryIncludeSidebarPages = array(
        'about',
        'strategic-plan',
        'annual-reports',
        'contact',
        'staff',
        'policy-research-scholars',
        'graduate-research-assistants'
    );

    $boolIncludeNoIndex             = false;
    $boolIncludeSidebar             = false;
    $boolIncludeImageAboveHeader    = false;

    //convert all the data for the inner view into variables
    extract($aryData);

    /**
     * next we need our site object
     * @todo do we need to check for the existence?
     */
    if(!isset($objSite) || !is_object($objSite)){
        $objSite = new Site();
    }

    //_mizzou_log($objSite,'our site object');
    /**
     * If the page title has not been overridden, get the default title and add our prepend
     */
    if(!isset($strPageTitle) || $strPageTitle == ''){
        $strPageTitle = prependTitle(wp_title('',false));
    }


    //global $wp_query;
    //_mizzou_log($wp_query,'wp_query');

    $strHeaderTitle = determineHeaderTitle($strPageTitle,$objSite->Name);
    _mizzou_log(determinePagePath($strPageTitle,$objSite->Name),'the array of path components');
    //_mizzou_log($strHeaderTitle,'our header title as returned');

    $strEditPostLink = '';
    if((is_single() || is_page()) && '' != $strPostLink = get_edit_post_link()){
        $strEditPostLink = ' '. $strPostLink;
    }

    /**
     * @wp-hack
     * hack. we only want the sidebar on specific pages. change this into a function that determines if a sidebar is actually
     * needed
     */

    if(is_page() && in_array($objMainPost->slug,$aryIncludeSidebarPages)){
        $boolIncludeSidebar = true;
    }

    /**
     * @todo Instead of checking to see if the MainPost->post_type is page, couldnt we just do is_page()?
     */
    if(isset($objMainPost) && $objMainPost->post_type == 'page' && $objMainPost->image != ''){
        $boolIncludeImageAboveHeader = true;
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
    $arySiteSiteMembers = array('URL','Name');
    foreach($objSite->currentPublicMembers() as $strSiteKey){
        $strSiteVariable = '';
        if(in_array($strSiteKey,$arySiteSiteMembers)) {
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
    $strBreadCrumbs = mizzouCaptureOutput('breadcrumbs');

    $strThemePath = mizzouDeterminePathToTheme();
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
    //$strWpHeaderContents = mizzouCaptureOutput('wp_head');
    $strWpHeaderContents = $objSite->wpHeader;
    //$strSearchFormContents = mizzouCaptureOutput('get_search_form');
    $strSearchFormContents = $objSite->SearchForm;
    //$strWpFooterContents = mizzouCaptureOutput('wp_footer');
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

function mizzouIncludeView($strViewName)
{
    $strFile = mizzouDeterminePathToTheme().'views'.DIRECTORY_SEPARATOR.$strViewName.'.php';
    if(file_exists($strFile)){
        require $strFile;
    } else {
        _mizzou_log($strFile,'this template file was requested but I couldnt find it');
    }
}

function determineHeaderTitle($strPageTitle=null,$strSiteName = '')
{
    $aryTitle = array();

    if(is_null($strPageTitle)){
        $strPageTitle = wp_title('',false);
    } else {
        /**
         * since we need this in the header, and its possible for the controller to manually set the page title, we
         * need to make sure there isn't any html in the page title
         */
        $strPageTitle = strip_tags($strPageTitle);
    }

    if(!empty($strPageTitle) && $strPageTitle != ''){
        $aryTitle[] = $strPageTitle;
    }


    if(is_archive() || is_single()){
        //ok, we have a lot of different archives to deal with. let's separate out the single
        if(is_single()){
            //we need to figure out what post type it is
            global $wp_query;
            $strPostType = get_post_type();
            if($strPostType != 'post'){
                _mizzou_log($strPostType,'the post type of the single',false,array('func'=>__FUNCTION__));
                $objPostType = get_post_type_object($strPostType);
                _mizzou_log($objPostType,'the post type object');
                $aryTitle[] = $objPostType->labels->name;
            } else {
                $aryTitle[] = 'Blog';
            }

        } else {

        }
    } elseif(is_page()) {
        //do sub sub pages need to have that information reflected in the title?
    } else {
        //it's not an archive a single, or a page. what do we have left?
    }

    if($strSiteName != ''){
        $aryTitle[] = $strSiteName;
    }

    $aryTitle[] = 'University of Missouri';

    _mizzou_log(implode(' // ',$aryTitle),'the header title');
    /**
     * @todo make the glue a configurable option
     */
    return implode(' // ',$aryTitle);

}

function determinePagePath($strPageTitle,$strSiteName='')
{
    $aryPath = array();

    if(is_null($strPageTitle)){
        $strPageTitle = wp_title('',false);
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

                $aryPath[$strPostTypeName] = $strPostTypeURL;

            } elseif(is_tax() || is_tag() || is_category()){
                $strTerm = get_query_var('term');
                $strTaxonomy = get_query_var('taxonomy');
                $objTaxonomy = get_taxonomy($strTaxonomy);
                $objTerm = get_term_by('slug',$strTerm,$strTaxonomy);
                _mizzou_log($strTerm,'the cat/tag/tax term');
                _mizzou_log($strTaxonomy,'the cat/tag/tax name');
                _mizzou_log($objTaxonomy,'the taxonomy object');
                _mizzou_log($objTerm,'the taxonomy term object');
            }

        }
    } elseif(is_page()) {
        //do sub sub pages need to have that information reflected in the title?
    } else {
        //it's not an archive a single, or a page. what do we have left?
    }

    if($strSiteName == ''){
        $strSiteName = get_bloginfo('name');
    }

    $aryPath[$strSiteName] = home_url();

    $aryPath['University of Missouri'] = 'http://missouri.edu/';

    return $aryPath;
}

function prependTitle($strPageTitle)
{
    if(is_archive()){
        global $wp_query;
        $objQueried = get_queried_object();
        if(is_object($objQueried) && count($wp_query->tax_query->queries) > 0 ){
            $strPageTitle = $objQueried->name . ' ' . $strPageTitle;
        }
    }

    return $strPageTitle;
}