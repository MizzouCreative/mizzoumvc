<?php
/**
 * A VERY basic model that can be used with a CPT of 'person'
 */
namespace MizzouMVC\models;
use MizzouMVC\models\WpBase;
require_once 'WpBase.php';

/**
 *
 * A VERY basic model that can be used with a CPT of 'person'
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
class People extends WpBase{
    /**
     * @var string
     */
    protected $strPostType = 'person';

    /**
     * @param string|null $strPostPreFix meta data prefix for custom meta keys for this post type
     */
    public function __construct($strPostPreFix = null)
    {
        /**
         * People should always include meta
         */
        $this->aryDefaults['include_meta'] = true;
        parent::__construct($strPostPreFix);
    }
}