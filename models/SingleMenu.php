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
    public function __construct($strMenuName, $aryMenuOptions = array())
    {
        $this->add_data('name', $strMenuName);
        $aryItems = $this->_getMenuItems($strMenuName);

        $aryMenuItems = (count($aryItems) > 0) ? $this->_restructureMenuItems($aryItems) : array();


        $this->add_data('formatted', $this->_getFormattedMenu($strMenuName, $aryMenuOptions));
        $this->add_data('items', $aryItems);
        $this->add_data('menu_items', $aryMenuItems);

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

    /**
     *
     * Assumption: the array structure we're given is a flattened array where the items are in order of top to bottom
     * regardless of nesting
     *
     * @param array $aryItems
     */
    protected function _restructureMenuItems(array $aryItems)
    {
        $aryStructuredMenuItems = [];
        $aryChildren = [];
        /**
         * This adds our contextual classes to each item
         * @see https://core.trac.wordpress.org/browser/tags/4.9.8/src/wp-includes/nav-menu-template.php#L272
         */
        _wp_menu_item_classes_by_context($aryItems);
        $aryReversedMenu = array_reverse($aryItems);

        foreach ($aryReversedMenu as $objItem) {
            if ($objItem instanceof \WP_Post) {
                $objMenuItem = $this->_createMenuItemObject($objItem);

                /**
                 * Does this item have any children?
                 */
                if (isset($aryChildren[$objMenuItem->ID])) {
                    //the children were stored in reverse order, so we need to flip back before storing
                    $objMenuItem->children = array_reverse($aryChildren[$objMenuItem->ID]);
                    unset($aryChildren[$objMenuItem->ID]);
                }

                /**
                 * Is this item a child of another item?
                 */
                if (0 !== intval($objMenuItem->parent)) {
                    if (!isset($aryChildren[$objMenuItem->parent])) {
                        $aryChildren[$objMenuItem->parent] = array();
                    }

                    $aryChildren[$objMenuItem->parent][] = $objMenuItem;
                } else {
                    $aryStructuredMenuItems[$objMenuItem->ID] = $objMenuItem;
                }
            }
        }

        return array_reverse($aryStructuredMenuItems);
    }

    protected function _createMenuItemObject(\WP_Post $objItem )
    {

        //$objItem = wp_setup_nav_menu_item($objItem);
        /**
         * @todo we need to make an interface, implement the interface and use it here
         */
        $objMenuItem = new \stdClass();
        $objMenuItem->ID = $objItem->ID;
        $objMenuItem->href = $objItem->url;
        $objMenuItem->text = $objItem->title;
        $objMenuItem->parent = $objItem->menu_item_parent;
        $objMenuItem->children = array();

        /**
         * The rest of this is almost duplicated directly from
         * https://core.trac.wordpress.org/browser/tags/4.9.8/src/wp-includes/class-walker-nav-menu.php#L115
         */

        $objMenuItem->classes = $objItem->classes;
        //for some reason, the first element in the classes is empty. if so we'll get rid of it
        if ('' == reset($objMenuItem->classes)) {
            array_shift($objMenuItem->classes);
        }
        $objMenuItem->class = implode(' ', $objMenuItem->classes);

        return $objMenuItem;
    }
}