<?php
/**
 * Created by PhpStorm.
 * User: gilzowp
 * Date: 5/10/16
 * Time: 2:44 PM
 */

namespace MizzouMVC\models;
use MizzouMVC\models\Base;

class SingleMenu extends Base
{

    /**
     * SingleMenu constructor.
     * @param string $strMenuName the menu we want to retrieve
     * @param array $aryMenuOptions options for retrieving the menu
     */
    public function __construct($strMenuName,$aryMenuOptions=array())
    {
        $this->add_data('name',$strMenuName);
        $this->add_data('formatted',$this->_getFormattedMenu($strMenuName,$aryMenuOptions));
        //no use trying to get the menu items if we already know the menu doesnt exist
        if('' !== $this->aryData['formatted']){
            $aryItems = $this->_getMenuItems($strMenuName);
        } else {
            $aryItems = array();
        }

        $this->add_data('items',$aryItems);

    }

    /**
     * Here for backwards compatibilty
     * @return string
     */
    public function __toString()
    {
        $strReturn = '';
        if(isset($this->aryData['formatted'])){
            $strReturn = $this->aryData['formatted'];
        }
        return $strReturn;
    }

    /**
     * retrieves the individual items for a given menu
     * @param string $strMenuName name of menu we want to retrieve
     * @return array
     * @uses wp_get_nav_menu_items()
     */
    protected function _getMenuItems($strMenuName)
    {
        $aryItems = wp_get_nav_menu_items($strMenuName);
        if(!is_array($aryItems)){
            $aryItems = array();
        }
        return $aryItems;
    }

    /**
     * Retrieves the html formatted version of the menu
     * @param string $strMenuName name of menu we want to retrieve
     * @param array $aryMenuOptions options for retrieving the menu
     * @return string formatted string version of the menu
     * @uses wp_nav_menu()
     */
    protected function _getFormattedMenu($strMenuName,$aryMenuOptions)
    {
        $strMenu = wp_nav_menu(array_merge($aryMenuOptions,array('menu'=>$strMenuName)));
        if(!is_string($strMenu)){
            _mizzou_log($strMenuName,'this menu doesnt appear to exist or came back as something other than a string ',false,array('line'=>__LINE__,'file'=>__FILE__));
            $strMenu = '';
        }
        return $strMenu;
    }
}