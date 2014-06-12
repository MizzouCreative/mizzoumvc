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
 * @todo most of the stuff in here should be moved into a SiteModel object
 */

$aryPageListArgs = array(
    'depth'        	=> 4, // if it's a top level page, we only want to see the major sections
    /**
     * These arent needed as they are the defaults for the function call
     *
    'post_type'    	=> 'page',
    'post_status'  	=> 'publish',
    'sort_column'  	=> 'menu_order, post_title',
     */
    'title_li'		=> '',
    'exclude'      	=> 2129, //why are excluding this item?
    'walker' 		=> new A11yPageWalker(),
    'echo'          => false,
);
$strPageList = wp_list_pages($aryPageListArgs);

$strSiteURL = home_url();
$strSiteName = get_bloginfo('name');

/**
 * @todo once we convert this fully, we'll have access to objMainPost and wont need to call get_the_modified_time
 */
$strModifiedDate = (is_single() || is_page()) ? get_the_modified_time('M j, Y') : site_modified_date(true);

$strParentThemeURL = get_template_directory_uri();
$strChildThemeURL = get_stylesheet_directory_uri();

$intCopyrightYear = date('Y');

ob_start();
wp_footer();
$strWpFooterContents = ob_get_contents();
ob_end_clean();
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'footer.php';