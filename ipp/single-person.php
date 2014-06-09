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
/**
 * @todo no no no. This needs to be dynamically generated.
 */
$aryData['strPublicationArchiveURL'] = '/publications/?author_archive='.$objPerson->ID;
mizzouOutPutView('single-person',$aryData);
_mizzou_log(get_object_vars($objPerson->meta_data),'object vars for our custom meta object');