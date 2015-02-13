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
_mizzou_log($aryContext,'aryContext',false,array('line'=>__LINE__,'file'=>__FILE__));
$aryData = array_merge($aryContext,$objFooter->getTemplateData());
_mizzou_log($aryData,'aryData after merging with aryContext',false,array('line'=>__LINE__,'file'=>__FILE__));
Content::render('footer',$aryData,array('include_header'=>false,'include_footer'=>false));
