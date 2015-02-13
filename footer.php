<?php
/**
 * Controller for the footer of the site
 * 
 *
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
$objFooter = new Footer($aryContext);
$aryData = array_merge($aryContext,$objFooter->getTemplateData());
Content::render('footer',$aryData,array('include_header'=>false,'include_footer'=>false));
