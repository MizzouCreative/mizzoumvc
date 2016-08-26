<?php
/**
 * Essentially, this exists to change the output in wordpress' wp_list_changes and menus from using <ul> to <ol>.
 * That's pretty much it.
 */
namespace MizzouMVC\library;

/**
 * Essentially, this exists to change the output in wordpress' wp_list_changes and menus from using <ul> to <ol>.
 * That's pretty much it.
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category framework
 * @category model
 * @author Paul Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 *
 * @todo this class needs to be renamed.  it doesn't have anything to do with A11y.  it just changes the markup from using
 * a <ul> element to a <ol> element,  that's it.
 */

class A11yPageWalker extends \Walker_Page {

    /*
    * "Start Level". This method is run when the walker reaches the start of a new "branch" in the tree structure.
    * Generally, this method is used to add the opening tag of a container HTML element (such as <ol>, <ul>, or <div>)
    * to $output.
    * @param string $output Passed by reference. Used to append additional content.
    * @param int    $depth  Depth of page. Used for padding.
    * @param array  $args
    */
    function start_lvl(&$output, $depth = 0, $aryArgs = array())
    {
        $strIndent = str_repeat("\t", $depth);
        $output .= PHP_EOL.$strIndent.'<ol class="children">'.PHP_EOL;
    }

    /**
     * "End Level". This method is run when the walker reaches the end of a "branch" in the tree structure. Generally,
     * this method is used to add the closing tag of a container HTML element (such as </ol>, </ul>, or </div>) to $output.
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of page. Used for padding.
     * @param array $aryArgs
     */
    function end_lvl(&$output, $depth = 0, $aryArgs = array())
    {
        $strIndent = str_repeat("\t", $depth);
        $output .= $strIndent.'</ol>'.PHP_EOL;
    }
}
