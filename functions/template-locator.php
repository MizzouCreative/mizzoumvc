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

/**
Plugin Name: Mizzou MVC Framework Test
Plugin URI:  http://marcom.missouri.edu/
Description: Initial test at moving framework from master theme into plugin
Version: 0.0.1
Author: Paul Gilzow
Author URI: http://missouri.edu/
 */

add_filter('template_include','mzuMVCTemplateOverride');
function mzuMVCTemplateOverride($strTemplate)
{
    //we only want to change the template if it is a compatible theme
    if(defined('MIZZOUMVC_COMPATIBLE') && MIZZOUMVC_COMPATIBLE) {

        mzuMVCPrintData('template file as given to us by wordpress', $strTemplate);
        //what template did wordpress match to?
        $strFoundTemplateFile = basename($strTemplate, '.php');
        mzuMVCPrintData('just the filename', $strFoundTemplateFile);
        //we want the main state, not a sub
        $strPattern = '/^(\w+)/';
        if (1 === preg_match($strPattern, $strFoundTemplateFile, $aryMatches)) {
            $strAction = $aryMatches[0];
            mzuMVCPrintData('just the action', $strAction);
            //if the found file is front-page, it is the same as home
            if ($strAction == 'front') $strAction = 'home';
            $strFunctionToCall = 'is_' . $strAction;
            mzuMVCPrintData('the function we\'ll try to call', $strFunctionToCall);
            if (!is_callable($strFunctionToCall) || !call_user_func($strFunctionToCall)) {
                //so either we have an action that isn't callable, or it was and returned false
                global $wp_query;

                mzuMVCPrintData('contents of wp_query', $wp_query);
                $aryWPQueryClassVars = get_object_vars($wp_query);
                mzuMVCPrintData('contents of aryWPQueryClassVars', $aryWPQueryClassVars);
                $strActionPattern = '/^is_(\w+)/';
                $aryIsKeys = preg_grep($strActionPattern, array_keys($aryWPQueryClassVars));
                mzuMVCPrintData('matched is_* keys', $aryIsKeys);

                $aryOverLap = array_intersect_key($aryWPQueryClassVars, array_flip($aryIsKeys));

                mzuMVCPrintData('our overlap data', $aryOverLap);

                if (false !== $strMatchedAction = array_search(true, $aryOverLap)) {
                    mzuMVCPrintData('our current action state', $strMatchedAction);
                    if (1 === preg_match($strActionPattern, $strMatchedAction, $aryMatchedAction)) {
                        $strCurrentAction = $aryMatchedAction[1];
                        $aryFiles = array();
                        switch ($strCurrentAction) {
                            case 'home':
                                $aryFiles[] = 'front-page';
                                break;
                            case 'single':
                            case 'archive':
                                if ($wp_query->query_vars['post_type'] != '') {
                                    $aryFiles[] = $strCurrentAction . '-' . $wp_query->query_vars['post_type'];
                                }
                                break;
                        }

                        $aryFiles[] = $strCurrentAction;
                        mzuMVCPrintData('here are the controllers we will look for in the framework', $aryFiles);
                        $boolFound = false;
                        //$strNewTemplatePath = dirname(__FILE__).DIRECTORY_SEPARATOR;
                        while ((list($intKey, $strFileName) = each($aryFiles)) && !$boolFound) {
                            $strNewTemplate = MIZZOUMVC_ROOT_PATH . $strFileName . '.php';
                            mzuMVCPrintData('full path to the file im going to look for', $strNewTemplate);
                            if (is_readable($strNewTemplate)) {
                                $strTemplate = $strNewTemplate;
                                $boolFound = true;
                            }
                        }

                    } else {
                        //strange, we didnt match...
                    }
                } else {
                    //weird, we dont have ANY states?
                    mzuMVCPrintData('we have no action states. see overlap values', $aryOverLap);
                }

            } else {
                mzuMVCPrintData('the file matches the action so need to intervene', null);
            }
        }
    }
    return $strTemplate;
}

function mzuMVCPrintData($strMsg,$mxdData)
{
	echo '<p>',$strMsg,'</p>',PHP_EOL,'<pre>',var_export($mxdData,true),PHP_EOL,'</pre>',PHP_EOL;
}