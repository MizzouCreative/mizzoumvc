<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/9/14
 * Time: 7:41 AM
 */

$aryOptions = array(
    'include_meta'=>true,
    'format_date'=>true,
    'date_format'=>'l, F jS, Y'
);

$objMainPost = new MizzouPost($post,$aryOptions);
var_export($objMainPost);