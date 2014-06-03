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

    //convert all the data for the inner view into variables
    extract($aryData);

    if(!isset($strPageTitle) || $strPageTitle == ''){
        $strPageTitle = wp_title('',false);
    }


    $strEditPostLink = '';
    if((is_single() || is_page()) && '' != $strPostLink = get_edit_post_link()){
        $strEditPostLink = ' '. $strPostLink;
    }

    /**
     * @wp-hack
     * hack. we only want the sidebar on specific pages. change this into a function that determines if a sidebar is actually
     * needed
     */

    $boolIncludeSidebar = false;

    if(is_page() && in_array($objMainPost->name,$aryIncludeSidebarPages)){
        $boolIncludeSidebar = true;
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
        $strInnerViewContent = ob_get_contents();
        ob_clean();
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

function mizzouIncludeView($strViewName)
{
    $strFile = mizzouDeterminePathToTheme().'views'.DIRECTORY_SEPARATOR.$strViewName;
    if(file_exists($strFile)){
        require $strFile;
    } else {
        _mizzou_log($strFile,'this template file was requested but I couldnt find it');
    }
}