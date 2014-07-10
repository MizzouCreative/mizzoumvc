<?php
/**
 * Controller for the archive of Project CPTs
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category controller
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */

/**
 * get our model
 * @todo we should be able to make this a function and move up higher
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'project.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

global $wp_query;
$aryData = array();
/**
 * Why are we using the base class instead of the project class here?
 * @todo convert to Project
 */
$objWpBase = new WpBase();

$aryProjects = $objWpBase->convertPosts($wp_query->posts);

//we need to get the contents from the loop view
ob_start();
require_once 'views' . DIRECTORY_SEPARATOR . 'projects-loop.php';
$aryData['strLoopContent'] = ob_get_clean();

//$aryData['strPageTitle'] = post_type_archive_title('',false);
//mizzouOutPutView('archive-project',$aryData);
Content::render('archive-project',$aryData);