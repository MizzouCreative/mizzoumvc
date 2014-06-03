<?php
/**
 * Controller for display of a single Person CPT
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category controller
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @todo move function calls out of this view
 */

/**
 * get our models
 * @todo we should be able to make this a function and move up higher
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'people.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'publication.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$aryData = array();

$objStaffModel = new People();
$objPublicationModel = new Publication();

$objPerson = $objStaffModel->convertStaff($post,array('include_cv'=>true));
$aryPublications = $objPublicationModel->getPublicationsByStaff($objPerson->ID);

$aryData['objPerson'] = $objPerson;
$aryData['aryPublications'] = $aryPublications;
mizzouOutPutView('single-person',$aryData);
_mizzou_log(get_post_type_archive_link('post'),'post archive link');
_mizzou_log(get_post_type_archive_link('posts'),'posts archive link');