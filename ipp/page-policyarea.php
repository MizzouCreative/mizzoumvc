<?php
/**
 * Template Name: Policy Area
 *
 * Controller file and template for policy areas
 *
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category controller
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */




/**
 * Data needed by the view
 * breadcrumbs - handled by OutPutView
 * default content for the page
 * 4 related projects, link to all projects
 * 1 main contact
 * 4 publications, link to all pubs
 */

/**
 * get our model
 * @todo we should be able to make this a function and move up higher
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.basename(__FILE__);
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$objPageModel = new PolicyArea(); // our model object

//data array
$aryData = array();
//gather the necessary data
$aryData['objMainPost'] = new MizzouPost($post);
$aryData['objMainContact'] = $objPageModel->retrieveContact($post->post_name);
$aryData['aryRelatedProjects'] = $objPageModel->retrieveProjects($post->post_name);
$aryData['strProjectArchiveURL'] = $objPageModel->retrieveProjectsArchivePermalink();
$aryData['aryRelatedPublications'] = $objPageModel->retrievePublications($post->post_name);
$aryData['strPublicationArchiveURL'] = $objPageModel->retrievePublicationsArchivePermalink();


mizzouOutPutView('policy-area',$aryData);
