<?php
/**
 * Controller for a static page
 * 
 *
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */

global $post;
$aryData = array();
$aryData['objMainPost'] = new MizzouPost($post);
Content::render('page',$aryData);
