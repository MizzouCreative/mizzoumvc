<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/4/14
 * Time: 1:00 PM
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Base.php';

class PostBase extends Base
{

    public function __construct($mxdPost)
    {
        if(is_object($mxdPost) && $mxdPost instanceof WP_Post){
            $objPost = $mxdPost;
        } elseif(is_numeric($mxdPost)){
            if(null !== $objPost = get_post($mxdPost)){
                $objPost = get_post($mxdPost);
            } else {
                $strLogMsg = 'we were given a post id, but wordpress returned a null.';
                _mizzou_log($mxdPost,$strLogMsg,false,array('func'=>__FUNCTION__));
            }
        } else {
            /**
             * It's not an instance of the WP_Post class, and it isnt a post id so...
             * @todo throw an exception here?
             */
            $strLogMsg = 'We werent given a post id, or an instance of WP_Post. Not sure what to do';
            _mizzou_log($mxdPost,$strLogMsg,false,array('func'=>__FUNCTION__));
            $objPost = new stdClass();
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