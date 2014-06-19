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
function mizzouOutPutView($strInnerViewFileName,$aryData)
{
    $aryIncludeSidebarPages = array(
        'about',
        'strategic-plan',
        'annual-reports',
        'contact',
        'staff',
        'policy-research-scholars',
        'graduate-research-assistants'
    );

    $boolIncludeNoIndex = false;
    $boolIncludeSidebar = false;

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
     * This one is a big of a bugger...
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

    //get contents from the inner view
    if(file_exists($strInnerView)){
        ob_start();
        require_once $strInnerView;
        $strInnerViewContent = ob_get_contents();
        ob_end_clean();
    } else {
        $strInnerViewContent = '<p>Unable to retrieve inner view.</p>';
    }

    $strWpHeaderContents = mizzouCaptureOutput('wp_head');
    $strSearchFormContents = mizzouCaptureOutput('get_search_form');

    //_mizzou_log($objSite,'our site object');
    //start actual output

    require_once $strViewsPath.'header.php';
    //get_header();

    /**
     * @todo replace with a require to the sidebar view
     */
    if($boolIncludeSidebar) {
        get_sidebar();
    }

    require_once $strViewsPath . 'outerView.php';

    $strWpFooterContents = mizzouCaptureOutput('wp_footer');

    require_once $strViewsPath . 'footer.php';
    //get_footer();
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

    $aryTitle[] = $strPageTitle;

    if(is_archive() || is_single()){
        //ok, we have a lot of different archives to deal with. let's separate out the single
        if(is_single()){
            //we need to figure out what post type it is
            global $wp_query;
            _mizzou_log($wp_query,'trying to figure out proper CPT name for a single post',false,array('func'=>__FUNCTION__));
        } else {

        }
    } else {
        //it's not an archive or a single. what do we have left? Just pages, right?
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