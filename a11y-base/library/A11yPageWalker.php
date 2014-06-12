<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/12/14
 * Time: 11:53 AM
 */

class A11yPageWalker extends Walker_Page {

    function start_lvl(&$output, $depth)
    {
        $strIndent = str_repeat("\t", $depth);
        $output .= PHP_EOL.$strIndent.'<ol class="children">'.PHP_EOL;
    }
    function end_lvl(&$output, $depth)
    {
        $strIndent = str_repeat("\t", $depth);
        $output .= $strIndent.'</ol>'.PHP_EOL;
    }
}
