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
        add_filter('map_meta_cap',array(&$this,'mapCapabilities'));
    }

    /**
     * If the current user is not an administrator, remove the administrator role from the roles list
     *
     * @param $aryRoles
     */
    public function removeAdministrator($aryRoles)
    {
        if(isset($aryRoles['administrator']) && !current_user_can('administrator')){
            unset($aryRoles['administrator']);
        }
    }

    public function mapCapabilities($aryCapabilities,$strCurrentCapability,$intUserId,$aryArgs)
    {
        _mizzou_log($aryCapabilities,'list of capabilities',false,array('line'=>__LINE__,'file'=>__FILE__));
        _mizzou_log($strCurrentCapability,'current capability',false,array('line'=>__LINE__,'file'=>__FILE__));
        _mizzou_log($intUserId,'user id',false,array('line'=>__LINE__,'file'=>__FILE__));
        _mizzou_log($aryArgs,'aryArgs',false,array('line'=>__LINE__,'file'=>__FILE__));
        switch($strCurrentCapability){
            case "edit_user":
                //pass through done intentionally
            case "remove_user":
                //pass through done intentionally
            case "promote_user":
                if(!isset($aryArgs[0]) || (isset($aryArgs[0]) && $aryArgs[0] != $intUserId)){
                    //$aryCapabilities[] = 'do_not_allow';

                }

                break;
        }

        return $aryCapabilities;
    }
}