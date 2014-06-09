<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/9/14
 * Time: 3:03 PM
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'publication.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$objPublicationModel = new Publication();
$aryData = array();
$aryPosts = $objPublicationModel->convertPosts(array($post),array('include_meta'=>true, 'format_date'=>true,'date_format'=>'F Y'));
$aryData['objMainPost'] = $aryPosts[0];

$aryPubTerms = get_the_terms($aryData['objMainPost']->ID,'author_archive');

_mizzou_log($aryPubTerms,'author links');

mizzouOutPutView('single-publication',$aryData);