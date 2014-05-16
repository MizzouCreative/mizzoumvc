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
 * default content for the page
 * 4 related projects, link to all projects
 * 1 main contact
 * 4 publications, link to all pubs
 */

$objMainPost = new MizzouPost($post);
$aryRelatedPublications = mizzouIppRetrieveRelatedPublications($post->post_name);
$aryRelatedProjects = mizzouIppRetrieveRelatedProjects($post->post_name);
$objMainContact = mizzouIppRetrieveContact($post->post_name);

get_header();
get_sidebar();
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'policy-area.php';
get_footer();
