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

class Menu extends Base {

    protected $aryDefaultMenuOptions = array(
        /**
         * Why a bogus theme_location? @see http://codex.wordpress.org/Function_Reference/wp_nav_menu#Targeting_a_specific_Menu
         */
        'theme_location'=>'no_such_location',
        'echo' => false,
        'fallback_cb'=>'',
        'container'=>false,
        'menu'=>'',
        'menu_format' => '<ol id="%1$s" class="%1$s %2$s">%3$s</ol>',
    );

    protected $aryMenuOptions = array();

    public function __construct($aryContext)
    {
        if(isset($aryContext['objSite'])){
            $this->add_data('objSite',$aryContext['objSite']);

            if(isset($aryContext['objMainPost'])){
                $this->add_data('objMainPost',$aryContext['objMainPost']);
            }

            if(isset($aryContext['PageTitle']) && $aryContext['PageTitle'] != ''){
                $this->add_data('PageTitle',$aryContext['PageTitle']);
            }
            //we're done with context, so lets kill it since it is likely pretty big
            unset($aryContext);

            if('' != $aryMenuOptions = $this->aryData['objSite']->option('menu_options')){
                $this->aryMenuOptions = array_merge($this->aryDefaultMenuOptions,$aryMenuOptions);
            } else {
                $this->aryMenuOptions = $this->aryDefaultMenuOptions;
            }

            if( '' != $aryStaticMenus = $this->aryData['objSite']->option('static_menus')){
                $this->_retrieveStaticMenus($aryStaticMenus);
            }

            $this->_retrievePageMenu();
        } else {
            /***
             * @todo we really kinda need it. what else should we do?
             */
            _mizzou_log($aryContext,'Hey, I really REALLY need objSite. Here is what you gave me',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
        }
    }

    protected function _retrieveStaticMenus($aryMenus)
    {
        _mizzou_log($aryMenus,'names of the static menus im going to try and get',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
        foreach($aryMenus as $strMenu){
           if('' != $strStaticMenu = $this->_retrieveMenu($strMenu)){
               $this->add_data($strMenu,$strStaticMenu);
           }
       }
    }

    protected function _determineMenuName()
    {
        $strMenu = '';
        if($this->aryMenuOptions['menu'] != ''){
            $strMenu = $this->aryMenuOptions['menu'];
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
        _mizzou_log($strMenu,'name of the page menu im going to try and get',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
        if($strMenu != ''){
            $this->add_data('Page',$this->_retrieveMenu($strMenu));
        }
    }

    protected function _retrieveMenu($strMenuName)
    {
        return wp_nav_menu(array_merge($this->aryMenuOptions,array('menu'=>$strMenuName)));

    }
}