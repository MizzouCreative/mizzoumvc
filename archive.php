<?php
/**
 * Controller for archive pages
 * 
 *
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
global $wp_query;
$aryData = array();
$objWpBase = new WpBase();

$aryData['aryPosts'] = $objWpBase->convertPosts($wp_query->posts);
Content::render('archive',$aryData,array('include_pagination'=>true));
