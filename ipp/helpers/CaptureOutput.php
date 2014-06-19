<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/19/14
 * Time: 12:31 PM
 */

/**
 * Captures the contents of a function that normally echos directly
 *
 * @param $strCallBack
 * @param $aryOptions
 * @return string
 */
function mizzouCaptureOutput($strCallBack,$aryOptions)
{
    $strReturn = '';
    if(function_exists($strCallBack) && is_callable($strCallBack)){
        ob_start();
        call_user_func_array($strCallBack,$aryOptions);
        $strReturn = ob_get_contents();
        ob_end_clean();
    }

    return $strReturn;
}