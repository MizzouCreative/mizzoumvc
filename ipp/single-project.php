<?php
/**
 * Controller for display of a single Project CPT
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category controller
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'project.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$aryData = array();
$objProject = new Project();
$aryData['objMainPost'] = $objProject->convertPost($post,array('include_meta'=>true, 'format_date'=>true,'date_format'=>'F Y'));
mizzouOutPutView('single-project',$aryData);