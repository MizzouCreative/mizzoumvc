<?php
/**
 * get our model
 * @todo we should be able to make this a function and move up higher
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'project.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

function mizzouDetermineArchiveTitle()
{
    $strPageTitle = '';
    if(is_date()){

        if(is_day()){
            $strTimeFormat = 'F j, Y';
        } elseif(is_month()){
            $strTimeFormat = 'F Y';
        } elseif(is_year()){
            $strTimeFormat = 'Y';
        }

        $strPageTitle = get_the_time($strTimeFormat) . ' Archive';

    } else {
        $strPageTitle = post_type_archive_title();
    }

    return $strPageTitle;
}

global $wp_query;
$aryData = array();
/**
 * Why are we using the base class instead of the project class here?
 * @todo convert to Project
 */
$objWpBase = new WpBase();

$aryData['aryPosts'] = $objWpBase->convertPosts($wp_query->posts);
//$aryData['strPageTitle'] = post_type_archive_title('',false);
$aryData['strPageTitle'] = mizzouDetermineArchiveTitle();
mizzouOutPutView('blog',$aryData);