<?php
//@todo move up higher
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'base.php';

global $wp_query,$post;
$objWpBase = new WpBase();

$aryData['objMainPost'] = new MizzouPost($post);
$aryData['aryPublications'] = $objWpBase->convertPosts($wp_query->posts);

mizzouOutPutView('archive-publication',$aryData);