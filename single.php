<?php
/**
 * Template file used to render a single post page. 
 * 
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
global $post;
$aryData = array();
$aryData['objMainPost'] = new MizzouPost($post);
Content::render('single',$aryData);