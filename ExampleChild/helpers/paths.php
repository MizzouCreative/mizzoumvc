<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/16/14
 * Time: 2:04 PM
 */

function mizzouDeterminePathToTheme()
{
    $strReturn = '';
    if(is_child_theme()){
        $strReturn = get_stylesheet_directory();
    } else {
        $strReturn = get_template_directory();
    }

    return $strReturn . DIRECTORY_SEPARATOR;
}