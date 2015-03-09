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

class Menu extends Subview {

    protected $aryDefaultMenuOptions = array(
        /**
         * Why a bogus theme_location? @see http://codex.wordpress.org/Function_Reference/wp_nav_menu#Targeting_a_specific_Menu
         */
        'theme_location'=>'no_such_location',
        'echo' => false,
        'fallback_cb'=>'',
        'container'=>false,
    );

    protected $aryMenuOptions = array();

    public function __construct($aryContext)
    {
        parent::__construct($aryContext);

        if('' != $aryMenuOptions = $this->aryData['objSite']->option('menu_options')){
            $this->aryMenuOptions = array_merge($this->aryDefaultMenuOptions,$aryMenuOptions);
        } else {
            $this->aryMenuOptions = $this->aryDefaultMenuOptions;
        }

        if( '' != $aryStaticMenus = $this->aryData['objSite']->option('static_menus')){
            $this->_retrieveStaticMenus($aryStaticMenus);
        }

        $this->_retrievePageMenu();
    }

    protected function _retrieveStaticMenus($aryMenus)
    {
       foreach($aryMenus as $strMenu){
           if('' != $strStaticMenu = $this->_retrieveMenu($strMenu)){
               $this->add_data($strMenu,$strStaticMenu);
           }
       }
    }

    protected function _determineMenuName()
    {
        $strMenu = '';
        if($this->aryMenuOptions['name'] != ''){
            $strMenu = $this->aryMenuOptions['name'];
        } elseif(isset($this->aryData['objMainPost'])){
            /**
             * @todo if we expand MizzouPost() to include ancestor data, we'll need to add a check here so that we arent
             * performing redundant calls
             */
            $aryAncestors = get_post_ancestors($this->aryData['objMainPost']->ID);
            if(count($aryAncestors) > 0){
                $intOldestAncestor = end($aryAncestors);
                $strMenu = get_the_title($intOldestAncestor);
            } else {
                /**
                 * So, the main post is the oldest ancestor.  So we should use it's page title ==UNLESS== the title of
                 * the page has been overridden manually
                 */
                $strMenu = (isset($this->aryData['PageTitle']) && $this->aryData['PageTitle'] != $this->aryData['objMainPost']->title) ? $this->aryData['PageTitle'] : $this->aryData['objMainPost']->title;
            }
        } elseif(isset($this->aryData['PageTitle']) && $this->aryData['PageTitle'] != ''){
            $strMenu = $this->aryData['PageTitle'];
        }

        return $strMenu;
    }

    protected function _retrievePageMenu()
    {
        $strMenu = $this->_determineMenuName();
        if($strMenu != ''){
            $this->add_data('Page',$this->_retrieveMenu($strMenu));
        }
    }

    protected function _retrieveMenu($strMenuName)
    {
        return wp_nav_menu(array_merge($this->aryMenuOptions,array('name'=>$strMenuName)));

    }

    public function getTemplateData()
    {
        return parent::getTemplateData();
    }
}