<?php
/**
 * Blogs archive controller.
 * @see http://codex.wordpress.org/Creating_a_Static_Front_Page#Custom_Blog_Posts_Index_Page_Template
 */

//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'WpBase.php';

global $wp_query;

$aryData = array();
$objWpBase = new WpBase('post_');
$aryData['aryPosts'] = $objWpBase->convertPosts($wp_query->posts);

mizzouOutPutView('blog',$aryData);