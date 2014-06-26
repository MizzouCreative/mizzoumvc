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
$aryPublicationOptions = array(
    'include_meta'      =>true,
    'format_date'       =>true,
    'date_format'       =>'F Y',
    'include_attachments'=>true,
);

$aryPosts = $objPublicationModel->convertPosts(array($post),$aryPublicationOptions);
$aryData['objMainPost'] = $aryPosts[0];

/**
 * @todo move ALL of this into the model
 */
$aryPubTerms = get_the_terms($aryData['objMainPost']->ID,'author_archive');
$aryAuthors = array();
$strAuthorPattern = '<a href="/publications/?author_archive=%s">%s</a>';
foreach($aryPubTerms as $objAuthor){
    $aryAuthors[] = sprintf($strAuthorPattern,$objAuthor->slug,$objAuthor->name);
}

$aryData['strMorePublications'] = implode(', ',$aryAuthors);

//_mizzou_log($aryPubTerms,'author links');

mizzouOutPutView('single-publication',$aryData);