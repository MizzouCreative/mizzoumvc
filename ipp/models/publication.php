<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/16/14
 * Time: 3:10 PM
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'base.php';

class Publication extends WpBase
{
    /**
     * Overload the parent member
     * @var string
     */
    protected $strPostType = 'publication';

}