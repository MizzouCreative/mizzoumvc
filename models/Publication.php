<?php
/**
 * A VERY basic model that can be used with a CPT of 'publication
 */
namespace MizzouMVC\models;
use MizzouMVC\models\WpBase;
require_once 'WpBase.php';

/**
 *
 * A VERY basic model that can be used with a CPT of 'publication'
 *
 * Essentially changes the default to include meta data when creating the MizzouPost object
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category model
 * @author Paul Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */

class Publication extends WpBase {
    /**
     * @var string post type for publications
     */
    protected $strPostType = 'publication';
}