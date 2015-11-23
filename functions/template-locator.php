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
    //echo 'template override called.';exit;
    //_mizzou_log(null,'template override function called',false,array('line'=>__LINE__,'file'=>__FILE__));
    //we only want to change the template if it is a compatible theme
    if(defined('MIZZOUMVC_COMPATIBLE') && MIZZOUMVC_COMPATIBLE) {

        //_mizzou_log($strTemplate,'template file as given to us by wordpress', false, array('line'=>__LINE__,'file'=>__FILE__));
        //what template did wordpress match to?
        $strFoundTemplateFile = basename($strTemplate, '.php');
        //_mizzou_log($strFoundTemplateFile,'just the filename',false, array('line'=>__LINE__,'file'=>__FILE__) );
        //we want the main state, not a sub
        $strPattern = '/^(\w+)/';
        if (1 === preg_match($strPattern, $strFoundTemplateFile, $aryMatches)) {
            $strAction = $aryMatches[0];
            //_mizzou_log($strAction,'just the action',false, array('line'=>__LINE__,'file'=>__FILE__) );
            //if the found file is front-page, we'll need to call is_front_page
            switch ($strAction){
                case 'front':
                    $strAction .= '_page';
                    break;
                case 'taxonomy':
                    $strAction = 'tax';
                    break;
            }

            $strFunctionToCall = 'is_' . $strAction;
            //_mizzou_log($strFunctionToCall,'the function we\'ll try to call', false, array('line'=>__LINE__,'file'=>__FILE__));
            if (!is_callable($strFunctionToCall) || !call_user_func($strFunctionToCall)) {
                //so either we have an action that isn't callable, or it was and returned false
                global $wp_query;

                //_mizzou_log('contents of wp_query', $wp_query);
                $aryWPQueryClassVars = get_object_vars($wp_query);
                //_mizzou_log($aryWPQueryClassVars,'contents of aryWPQueryClassVars',false, array('line'=>__LINE__,'file'=>__FILE__) );
                $strActionPattern = '/^is_(\w+)/';
                $aryIsKeys = preg_grep($strActionPattern, array_keys($aryWPQueryClassVars));
                //_mizzou_log($aryIsKeys,'matched is_* keys',false, array('line'=>__LINE__,'file'=>__FILE__) );

                $aryOverLap = array_intersect_key($aryWPQueryClassVars, array_flip($aryIsKeys));

                //_mizzou_log($aryOverLap,'our overlap data',false, array('line'=>__LINE__,'file'=>__FILE__) );

                $aryMatchedActions = array_keys($aryOverLap,true,true);

                if(count($aryMatchedActions) > 1){
                    if(false !== $intPreviewKey = array_search('is_preview',$aryMatchedActions)){
                        unset($aryMatchedActions[$intPreviewKey]);
                    } else {
                        _mizzou_log($aryMatchedActions,'we have more than one matched action but it isnt is_preview',false,array('line'=>__LINE__,'file'=>__FILE__));
                    }
                }

                if (count($aryMatchedActions) > 0) {
                    $strMatchedAction = reset($aryMatchedActions);
                    //_mizzou_log( $strMatchedAction,'our current action state',false, array('line'=>__LINE__,'file'=>__FILE__));
                    //it's possible that if we are on a page it has been assigned a specific template
                    if('is_page' != $strMatchedAction || !is_page_template(basename($strTemplate))){
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
                            //_mizzou_log($aryFiles,'here are the controllers we will look for in the framework',false, array('line'=>__LINE__,'file'=>__FILE__) );
                            $boolFound = false;
                            //$strNewTemplatePath = dirname(__FILE__).DIRECTORY_SEPARATOR;
                            while ((list($intKey, $strFileName) = each($aryFiles)) && !$boolFound) {
                                $strNewTemplate = MIZZOUMVC_ROOT_PATH . $strFileName . '.php';
                                //_mizzou_log($strNewTemplate,'full path to the file im going to look for',false, array('line'=>__LINE__,'file'=>__FILE__) );
                                if (is_readable($strNewTemplate)) {
                                    $strTemplate = $strNewTemplate;
                                    $boolFound = true;
                                }
                            }

                        } else {
                            //strange, we didnt match...
                        }
                    }
                } else {
                    //weird, we dont have ANY states?
                    _mizzou_log($aryOverLap,'we have no action states. see overlap values', false, array('line'=>__LINE__,'file'=>__FILE__));
                }

            } else {
                //_mizzou_log(null,'the file matches the action so need to intervene',false, array('line'=>__LINE__,'file'=>__FILE__) );
            }
        }
    }
    return $strTemplate;
}

//function _mizzou_log($strMsg,$mxdData)
//{
	//echo '<p>',$strMsg,'</p>',PHP_EOL,'<pre>',var_export($mxdData,true),PHP_EOL,'</pre>',PHP_EOL;
//}