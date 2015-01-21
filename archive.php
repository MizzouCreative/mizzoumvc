<?php
/**
 * Template file used to render a archive pages
 * 
 * Will be overriden by category.php/tag.php if available and user is viewing 
 * category/tag archives
 *
 * @package WordPress
 * @subpackage SITENAME
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @author Charlie Triplett, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
global $wp_query;
$aryData = array();
$objWpBase = new WpBase();

$aryData['aryPosts'] = $objWpBase->convertPosts($wp_query->posts);
Content::render('archive',$aryData,array('include_pagination'=>true));
