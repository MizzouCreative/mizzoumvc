<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'slide.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

$aryData = array();

$objSlide = new Slide();
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

