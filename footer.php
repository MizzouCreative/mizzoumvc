<?php
/**
 * Template file used to render the footer of the site
 * 
 * 
 * @package WordPress
 * @subpackage mizzou-news
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @uses class-customPostData
 * @author Charlie Triplett, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
$objFooter = new Footer($aryContext);
Content::render('footer',$objFooter->getTemplateData(),array('include_header'=>false,'include_footer'=>false));
