<?php
/**
 * Template file used to render a Server 404
 * 
 * In addition to serving the 404 header and notification, will automatically 
 * perform a search based on the non-existant URL. Change the html structure 
 * below as needed.
 *
 * @package WordPress
 * @subpackage mizzou-news
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @uses class-customPostData
 * @author Paul F. Gilzow & Jason L Rollins, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
require_once 'models/FourOhFour.php';

$aryData = array();
$aryData['strRequestURI'] = $_SERVER['REQUEST_URI'];
$aryData['objSite'] = new Site();

$obj404 = new FourOhFour($aryData);


$aryData['SearchResults'] = $obj404->getSearchResults();
_mizzou_log($aryData['SearchResults'],'search results stored in aryData',false,array('line'=>__LINE__,'file'=>__FILE__));
Content::render('search',$aryData);

