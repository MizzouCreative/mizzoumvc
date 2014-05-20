<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/16/14
 * Time: 2:00 PM
 */

//pull in the base model
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'base.php';

class Project extends WpBase
{
    /**
     * overload parent member
     * @var string
     */
    protected $strPostType = 'project';

}