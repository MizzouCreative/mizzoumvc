<?php
namespace MizzouMVC\models;
use MizzouMVC\models\WpBase;
require_once 'WpBase.php';

/**
 *
 * A VERY basic model that can be used with a CPT of 'slide'
 *
 * Essentially changes the default to include meta data when creating the MizzouPost object
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */

class Slide extends WpBase {
    protected $strPostType = 'slide';
}