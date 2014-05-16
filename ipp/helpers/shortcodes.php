<?php
/**
 * Adds shortcodes as needed by the system for the IPP child theme
 *
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category controller
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */

function mizzouIppShortcodeInit()
{
    add_shortcode('projects','mizzouIppProjectShortcode');
}

function mizzouIppProjectShortcode($aryAttributes)
{
    $aryDefaults = array('count'=>4);
    $aryAttributes = shortcode_atts($aryDefaults,$aryAttributes);

    //let's make sure they gave us something we can actually use...
    if(!is_numeric($aryAttributes['count'])){
        $aryAttributes['count'] = $aryDefaults['count']; //set it back to the default
    }

    $strTemplatePath = mizzouDeterminePathToTheme();
    //use the model for projects, lazy-load style
    require_once $strTemplatePath.'models'.DIRECTORY_SEPARATOR.'project.php';

    $aryProjects = mizzouIppRetrieveProjects($aryAttributes['count']);
    $strTitle = 'Recent Projects';
    ob_start();
    $strTemplatePath . 'views' . DIRECTORY_SEPARATOR . 'projects-loop.php';
    return ob_get_clean();
}

add_action('init','mizzouIppShortcodeInit');