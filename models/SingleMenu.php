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


    public function __construct($strMenuName,$aryMenuOptions=array())
    {
        $this->add_data('formatted',$this->_getFormattedMenu($strMenuName,$aryMenuOptions));
        $this->add_data('items',$this->_getMenuItems($strMenuName));
    }

    public function __toString()
    {
        $strReturn = '';
        if(isset($this->aryData['formatted'])){
            $strReturn = $this->aryData['formatted'];
        }
        return $strReturn;
    }

    protected function _getMenuItems($strMenuName)
    {
        return wp_get_nav_menu_items($strMenuName);
    }

    protected function _getFormattedMenu($strMenuName,$aryMenuOptons)
    {
        return wp_nav_menu(array_merge($aryMenuOptons,array('menu'=>$strMenuName)));
    }
}