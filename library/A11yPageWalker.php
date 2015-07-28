<?php
/**
 * Essentially, this exists to change the output in wordpress' wp_list_changes and menus from using <ul> to <ol>.
 * That's pretty much it.
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category theme
 * @category model
 * @author Charlie Triplett, Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */

class A11yPageWalker extends Walker_Page {

    function start_lvl(&$output, $depth, $aryArgs = array())
    {
        $strIndent = str_repeat("\t", $depth);
        $output .= PHP_EOL.$strIndent.'<ol class="children">'.PHP_EOL;
    }
    function end_lvl(&$output, $depth, $aryArgs = array())
    {
        $strIndent = str_repeat("\t", $depth);
        $output .= $strIndent.'</ol>'.PHP_EOL;
    }
}
