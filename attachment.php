<?php
/**
 * Template file used to render a attachment page
 * 
 *
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template
 * @author Charlie Triplett, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
global $wp_query;
$aryData = array();
$objWpBase = new WpBase();

$aryData['aryPosts'] = $objWpBase->convertPosts($wp_query->posts);
Content::render('attachment',$aryData);
