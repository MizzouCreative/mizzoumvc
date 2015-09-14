<?php
/**
 * 
 *
 * @package 
 * @subpackage 
 * @since 
 * @category 
 * @category 
 * @uses 
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
namespace MizzouMVC\models;
require_once 'WpBase.php';

class People extends WpBase{
    protected $strPostType = 'person';

    public function __construct($strPostPreFix = null)
    {
        /**
         * People should always include meta
         */
        $this->aryDefaults['include_meta'] = true;
        parent::__construct($strPostPreFix);
    }
}