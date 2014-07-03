<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'slide.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$aryData = array();

/**
 * @todo use ThemeOptions class to dynamically pull list of widgets to display
 * Actually, it makes more sense to have the widgets be part of the post/page object and then let content owners
 * choose widgets on the page where they should appear./
 */
$aryWidgetNames = array('primary-widget','home_right');

$objSlide = new Slide();

global $post;
$aryData['objMainPost'] = new MizzouPost($post,array('include_meta'=>array('meta_prefix'=>'page_')));
_mizzou_log($aryData['objMainPost'],'the main post object when on the front page');

/**
 * @todo will we ever use slides anywhere besides on the front page?
 * @todo will we ever want slides and NOT include meta or images? if no, then move these into the Slide class
 */
$arySlideOptions = array(
    'count'         => 1,
    'include_meta'  => true,
    'include_image' => true,
);
$arySlides = $objSlide->retrieveContent($arySlideOptions);

$aryData['objSlide'] = (count($arySlides) == 1) ? $arySlides[0] : '';

//now we need to get the widgets.
$aryData['aryWidgets'] = array();
foreach($aryWidgetNames as $strWidgetName){
    /**
     * @todo migrate this into a front page model
     */
    $aryData['aryWidgets'][$strWidgetName] = mizzouCaptureOutput('dynamic_sidebar',array($strWidgetName));
}

mizzouOutPutView('front-page',$aryData,array('override_outerview'=>true));

