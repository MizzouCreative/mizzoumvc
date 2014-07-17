<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/9/14
 * Time: 7:41 AM
 */

//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';
/**
 * get our models
 * @todo we should be able to make this a function and move up higher
 * @todo change this to the specific model once built
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'WpBase.php';

/**
 * @todo move this into the model
 */
$aryOptions = array(
    'include_meta'=>true,
    'format_date'=>true,
    'date_format'=>'l, F jS, Y'
);

$aryData = array();

$aryData['objMainPost'] = new MizzouPost($post,$aryOptions);

/**
 * 20140609 PFG: @wp-hack.  Ability to retrieve associated taxonomies is partially implemented, but not complete. Fake it
 * until after the 20140610 meeting. Definitely needs to be moved into a
 */
$aryData['aryRelatedPosts'] = array();
$aryCategories = wp_get_post_categories($aryData['objMainPost']->ID);
if (count($aryCategories) > 0) {
    //Assume that we always want the first category
    $aryArgs = array(
        'cat' => $aryCategories[0],
        'post__not_in'=> array($aryData['objMainPost']->ID),
        'posts_per_page'=>5,
        'ignore_sticky_posts'=>true
    );

    $objWPQuery = new WP_Query($aryArgs);
    //_mizzou_log($aryRelatedPosts,'ary of Related Posts');
    //_mizzou_log($aryArgs,'arguments for the query results above');

    if(count($objWPQuery->posts) > 0){
        $objPostModel = new WpBase();
        $aryData['aryRelatedPosts'] = $objPostModel->convertPosts($objWPQuery->posts);
    }
}

mizzouOutPutView('single',$aryData);
//var_export($objMainPost);