<?php
/**
 * Basis for all post-related models.
 *
 * Ensures that we start with either a wordpress post object, or that we are able to get one from the ID that is
 * passed in.
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @todo there is a dependency here on _mizzou_log. Either remove or inject the dependency
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Base.php';

class PostBase extends Base
{

    public function __construct($mxdPost)
    {
        if(is_object($mxdPost) && $mxdPost instanceof WP_Post){
            $objPost = $mxdPost;
        } elseif(is_numeric($mxdPost)){
            if(null == $objPost = get_post($mxdPost)){
                $strLogMsg = 'we were given a post id, but wordpress returned a null.';
                _mizzou_log($mxdPost,$strLogMsg,false,array('func'=>__FUNCTION__));
                $this->add_error($strLogMsg);
            }
        } else {
            /**
             * It's not an instance of the WP_Post class, and it isnt a post id so...
             * @todo throw an exception here?
             */
            $strLogMsg = 'We werent given a post id, or an instance of WP_Post. Not sure what to do';
            _mizzou_log($mxdPost,$strLogMsg,false,array('func'=>__FUNCTION__));
            $this->add_error($strLogMsg);
            $objPost = null;
	        return null;
        }

        $this->objOriginalPost = $objPost;
    }

   /**
     * Checks to see if any member of the passed array is currently set in the object
     *
     * We have sections where if ANY piece in a section is set, we have to create the section so we can output that one piece
     *
     * @param array $aryGroupMembers
     * @return bool
     */
    public function memberOfGroupSet(array $aryGroupMembers){
        $i = count($aryGroupMembers);
        $boolMemberFound = false;
        $j = 0;
        while(!$boolMemberFound && $j<$i){
            if($this->is_set($aryGroupMembers[$j])){
                $boolMemberFound = true;
            }
            ++$j;
        }

        return $boolMemberFound;
    }
}