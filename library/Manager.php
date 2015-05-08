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

class Manager {
    public function __construct()
    {
        add_filter('editable_roles',array(&$this,'removeAdministrator'));
        add_filter('map_meta_cap',array(&$this,'mapCapabilities'),10,4);
    }

    /**
     * If the current user is not an administrator, remove the administrator role from the roles list
     *
     * If they are not a manager, then they shouldnt be able to add a new user as an administrator
     *
     * @todo should they be able to add another manager?
     *
     * @param $aryRoles
     * @return $aryRoles
     */
    public function removeAdministrator($aryRoles)
    {
        if(isset($aryRoles['administrator']) && !current_user_can('administrator')){
            unset($aryRoles['administrator']);
            //unset($aryRoles['manager']);
        }

        return $aryRoles;
    }

    public function mapCapabilities($aryCapabilities,$strCurrentCapability,$intUserId,$aryArgs)
    {
        /**
        $aryDebug = array(
            'capabilities'=>$aryCapabilities,
            'current_cap'=>$strCurrentCapability,
            'user_id'=>$intUserId,
            'aryArgs'=>$aryArgs,
        );

        _mizzou_log($aryDebug,'all the args we were just given',false,array('file'=>__FILE__,'line'=>__LINE__,'func'=>__FUNCTION__));
        */


        switch($strCurrentCapability){
            case "edit_user":
                //pass through done intentionally
            case "remove_user":
                //pass through done intentionally
            case "promote_user":
                if(!isset($aryArgs[0]) || (isset($aryArgs[0]) && $aryArgs[0] != $intUserId)){
                    if(isset($aryArgs[0])){
                        if(!$this->_canTheyEditThisPerson($aryArgs[0])){
                            $aryCapabilities[] = 'do_not_allow';
                        }
                    } else {
                        $aryCapabilities[] = 'do_not_allow';
                    }
                }
                break;
            case 'delete_user':
                //pass through done intentionally
            case 'delete_users':
                if(isset($aryArgs[0])){
                    if(!$this->_canTheyEditThisPerson($aryArgs[0])){
                        $aryCapabilities[] = 'do_now_allow';
                    }
                }
        }

        return $aryCapabilities;
    }

    protected function _canTheyEditThisPerson($intID)
    {
        $boolReturn = true;
        $objUserToEdit = new WP_User(absint($intID));
        if($objUserToEdit->has_cap('administrator') && !current_user_can('administrator')){
            $boolReturn = false;
        }

        return $boolReturn;
    }
}