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
 */
function mizzouOutPutView($strInnerViewFileName,$aryData)
{
    //convert all the data for the inner view into variables
    extract($aryData);

    if(!isset($strTitle) || $strTitle == ''){
        $strTitle = wp_title('',false);
    }


    $strEditPostLink = '';
    if(is_single() || is_page()){
        $strEditPostLink = ' '.get_edit_post_link();
    }

    /**
     * hack. we only want the sidebar on specific pages. change this into a function that determines if a sidebar is actually
     * needed
     */

    $boolIncludeSidebar = false;

    if(
            is_page('about')
        ||  is_page('strategic-plan')
        ||  is_page('annual-reports')
        ||  is_page('contact')
        ||  is_page('staff')
        ||  is_page('policy-research-scholars')
        ||  is_page('graduate-research-assistants')
    ){
        $boolIncludeSidebar = true;
    }

    $intSpanWidth = ($boolIncludeSidebar) ? 9 : 12;

    //outerView needs breadcrumbs and inner view data

    /**
     * @todo the breadcrumbs plugin needs to be converted to a Model with a matching view
     */
    //get the contents for the breadcrumbs

    ob_start();
    breadcrumbs();
    $strBreadCrumbs = ob_get_contents();
    ob_clean();

    $strThemePath = mizzouDeterminePathToTheme();
    $strViewsPath = $strThemePath.'views'.DIRECTORY_SEPARATOR;
    $strInnerView = $strViewsPath . $strInnerViewFileName . '.php';
    //get contents from the inner view
    if(file_exists($strInnerView)){
        require_once $strInnerView;
        $strInnerViewContent = ob_get_clean();
    } else {
        $strInnerViewContent = '<p>Unable to retrieve inner view.</p>';
    }

    ob_end_clean();

    //start actual output
    get_header();
    if($boolIncludeSidebar) {
        get_sidebar();
    }

    require_once $strViewsPath . 'outerView.php';
    get_footer();
}