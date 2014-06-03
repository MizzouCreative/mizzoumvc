<?php
//@todo move up higher
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'base.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

global $wp_query,$post;

/**
 * Why are we using base instead of the Publications object?
 */
$objWpBase = new WpBase();

$aryData['objMainPost'] = new MizzouPost($post);
$aryOptions = array(
    'resort'        => array('key'=>'type'),
    'include_meta'  => true
);

$aryData['aryPublications'] = $objWpBase->convertPosts($wp_query->posts,$aryOptions);

/*
echo '<xmp>',var_export($wp_query,true),'</xmp>',PHP_EOL,PHP_EOL;*/

echo '<xmp>',var_export($aryData['aryPublications'],true),'</xmp>';

//mizzouOutPutView('archive-publication',$aryData);