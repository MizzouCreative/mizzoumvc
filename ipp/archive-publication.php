<?php
//@todo move up higher
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'WpBase.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

//do we still need wp_query here?
global $wp_query,$post;

/**
 * Why are we using base instead of the Publications object?
 */
$objWpBase = new WpBase();

if('' != $intAuthorID = get_query_var('author_archive')){
    $objAuthor = new MizzouPost($intAuthorID);
    //_mizzou_log($objAuthor,'Our Pubs author');
    $aryData['strPageTitle'] = 'Publications for ' . $objAuthor->title;
}

$aryData['objMainPost'] = new MizzouPost($post);
$aryOptions = array(
    'resort'        => array('key'=>'type'),
    'include_meta'  => true,
    'meta_prefix'   => 'publication_'
);

$aryData['aryPublicationsGroup'] = $objWpBase->convertPosts($wp_query->posts,$aryOptions);

/*
echo '<xmp>',var_export($wp_query,true),'</xmp>',PHP_EOL,PHP_EOL;*/

//echo '<xmp>',var_export($aryData['aryPublicationsGroup'],true),'</xmp>';

mizzouOutPutView('archive-publication',$aryData);