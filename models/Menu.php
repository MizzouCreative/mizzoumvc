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
    );

    protected $aryMenuOptions = array();

    protected $strPatternItemsWrap = '/^<((o|u)l)/';

    public function __construct($aryContext)
    {
        if(isset($aryContext['objSite'])){
	        /**
	         * @todo do we REALLY need to store the entire Site object?
	         */
	        $this->add_data('objSite',$aryContext['objSite']);

            if(isset($aryContext['objMainPost'])){
                $this->add_data('objMainPost',$aryContext['objMainPost']);
            }

            if(isset($aryContext['PageTitle']) && $aryContext['PageTitle'] != ''){
                $this->add_data('PageTitle',$aryContext['PageTitle']);
            }


            if('' != $aryMenuOptions = $aryContext['objSite']->menu_options){
               // _mizzou_log($aryMenuOptions,'menu options is set and here is what it contains',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
                unset($aryMenuOptions['inject_primary']);//we dont need this one for wp_nav_menu
                $this->aryMenuOptions = array_merge($this->aryDefaultMenuOptions,$aryMenuOptions);
            } else {
                $this->aryMenuOptions = $this->aryDefaultMenuOptions;
            }
            //_mizzou_log($aryContext,'full context contents',false,array('line'=>__LINE__,'file'=>__FILE__));
            if(isset($aryContext['menuName'])){
                $this->aryMenuOptions['menu'] = $aryContext['menuName'];
            }

            //$arySiteOptions = $this->aryData['objSite']->{'site-wide'};
            $arySiteOptions = $aryContext['objSite']->{'site-wide'};
            //we're done with context, so lets kill it since it is likely pretty big

            _mizzou_log($arySiteOptions,'site wide options from objSite',false,array('file'=>__FILE__,'line'=>__LINE__));
            unset($aryContext);



            $aryStaticMenuKeys = preg_grep('/static_menu_?\d/',array_keys($arySiteOptions));

            if(count($aryStaticMenuKeys) > 0){
                $aryStaticMenus = array_intersect_key($arySiteOptions,array_flip($aryStaticMenuKeys));

                $this->_retrieveStaticMenus($aryStaticMenus);
                if(isset($this->aryData['Primary']) && ($this->aryData['objSite']->option('inject_primary'))){
                    $this->_injectPrimaryMenu();
                }
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
            //_mizzou_log($strMenu,'menu was already set',false,array('line'=>__LINE__,'file'=>__FILE__));
        } elseif(isset($this->aryData['objMainPost'])){
            /**
             * @todo if we expand MizzouPost() to include ancestor data, we'll need to add a check here so that we arent
             * performing redundant calls
             */

            $aryAncestors = $this->aryData['objMainPost']->retrieveAncestors();

            //$aryAncestors = get_post_ancestors($this->aryData['objMainPost']->ID);
            if(count($aryAncestors) > 0){
                $strMenu = end($aryAncestors);
                //$strMenu = get_the_title($intOldestAncestor);
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
        //_mizzou_log($strMenu,'strMenu at the end of the function call',false,array('line'=>__LINE__,'func'=>__FUNCTION__));
        return $strMenu;
    }

    protected function _retrievePageMenu()
    {
        /**
         * it's possible we already determined the page menu when/if we injected into the primary
         */
        if(!isset($this->aryData['Page']) || $this->Page == ''){
            $strMenu = $this->_determineMenuName();
            //_mizzou_log($strMenu,'name of the page menu im going to try and get',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
            if($strMenu != ''){
                $this->add_data('Page',$this->_retrieveMenu($strMenu));
            }
        }

    }

    protected function _retrieveMenu($strMenuName)
    {
        //_mizzou_log($this->aryMenuOptions,'getting ready to retrieve menu ' . $strMenuName . 'with these options',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
        return wp_nav_menu(array_merge($this->aryMenuOptions,array('menu'=>$strMenuName)));

    }

    protected function _determineListElementType()
    {
        $strReturn = 'ul';//wordpress default for menus
        if('' != $strItemsWrap = $this->aryData['objSite']->option('items_wrap')){
            if(1 === preg_match($this->strPatternItemsWrap,$strItemsWrap,$aryMatches)){
                $strReturn = $aryMatches[1];
            }
        }

        return $strReturn;
    }

    protected function _injectPrimaryMenu()
    {
        //_mizzou_log(null,'ive been asked to inject into the primary menu');
        if($this->aryData['PageTitle'] != ''){ // if we have no page title, then there isnt a menu to inject
            $objDomMenu = new DOMDocument();
            $objDomMenu->loadXML($this->aryData['Primary']);
            //_mizzou_log($objDomMenu->saveHTML(),'our primary menu as a DOMobject',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
            $strListElementType = $this->_determineListElementType();
            $objOLNodes = $objDomMenu->getElementsByTagName($strListElementType);
            //_mizzou_log(null,'currently',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
            for($j=0;$j<$objOLNodes->length;++$j){
                //_mizzou_log(null,'currently',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
                $objChildNode = $objOLNodes->item($j);
                if($objChildNode->getAttribute('id') == 'menu-primary'){
                    $j = $objOLNodes->length;
                    $objMainMenuLIList = $objChildNode->getElementsByTagName('li');
                    for($i=0;$i<$objMainMenuLIList->length;++$i){
                        $objChildLI = $objMainMenuLIList->item($i);
                        if(trim($this->aryData['PageTitle']) == $objChildLI->nodeValue){
                            $i = $objMainMenuLIList->length;
                            /* ok, so we have a node title that matches a page title. now let's go see if we have a
                             * matching menu
                             */
                            /**
                             * @todo should we attempt to have an option for the menu class to be inserted here? injection_class in menu_options of the config file?
                             */
                            //$aryMenuOptions = array_merge($this->aryMenuOptions,array('menu' => $objChildLI->nodeValue,));

                            if('' != $strSubMenu = $this->_retrieveMenu($objChildLI->nodeValue)){
                                //_mizzou_log($strSubMenu,'we found an element that matches our current page! Here is our matching menu',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
                                //store it in the site object
                                $this->add_data('Page',$strSubMenu);
                                $objDomSecondaryMenu = new DOMDocument();
                                $objDomSecondaryMenu->loadXML($strSubMenu);
                                $objSecondaryMenuNode = $objDomSecondaryMenu->getElementsByTagName('ul')->item(0);
                                /*
                                $objFirstChildOfSecondMenu = $objSecondaryMenuNode->firstChild;
                                _mizzou_log($objFirstChildOfSecondMenu->nodeValue,'what is the first child of our second menu?');
                                */

                                $objChildLI->appendChild($objDomMenu->importNode($objSecondaryMenuNode,true));
                                $this->add_data('Primary',$objDomMenu->saveHTML());
                                //_mizzou_log($this->aryData['objSite']->PrimaryMenu,'our menu in html after injection',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));

                            }
                        }
                    }
                }


            }

        }

    }

}