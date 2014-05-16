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
 * @category template
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */




/**
 * Data needed by the view
 * breadcrumbs
 * default content for the page
 * 4 related projects, link to all projects
 * 1 main contact
 * 4 publications, link to all pubs
 */

$aryData = array();
$aryData['objMainPost'] = new MizzouPost($post);
$aryData['aryRelatedPublications'] = mizzouIppRetrieveRelatedPublications($post->post_name);
$aryData['aryRelatedProjects'] = mizzouIppRetrieveRelatedProjects($post->post_name);
$aryData['objMainContact'] = mizzouIppRetrieveContact($post->post_name);

mizzouOutPutView('policy-area',$aryData);
