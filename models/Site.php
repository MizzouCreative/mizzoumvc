<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/12/14
 * Time: 1:52 PM
 *
 * @uses home_url()
 * @uses get_bloginfo()
 * @uses get_template_directory_uri()
 * @uses get_stylesheet_directory_uri()
 * @uses A11yPageWalker
 * @uses wp_list_pages()
 *
 *
 * ASSUMES that Base.php and A11yPageWalker.php classes has already been included
 */

/**
 * Class Site
 */
class Site extends Base {
    protected  $aryOptions = array(
        'date_format'   => 'M j, Y',
        'menu_format'   => '<ol class="%1$s %2$s">%3$s</ol>'
    );

    public function __construct($aryOptions = array())
    {
        $this->aryOptions = array_merge($this->aryOptions,$aryOptions);

        $this->add_data('CopyrightYear',date('Y'));
        $this->add_data('Name',$this->_getSiteName());
        $this->add_data('URL',$this->_getSiteHomeURL());
        $this->add_data('ParentThemeURL',$this->_getParentThemeURL());
        $this->add_data('ChildThemeURL',$this->_getChildThemeURL());
        $this->add_data('ActiveStylesheet',$this->_getActiveStylesheet());
        $this->add_data('ActiveThemeURL',$this->_getActiveThemeURL());
        $this->add_data('ParentThemePath',$this->_getParentThemePath());
        $this->add_data('ChildThemePath',$this->_getChildThemePath());
        $this->add_data('ActiveThemePath',$this->_getActiveThemePath());
        $this->add_data('TrackingCode',$this->_getTrackingCode());
        $this->add_data('AudienceMenu',$this->_getAudienceMenu());
        $this->add_data('PrimaryMenu',$this->_getPrimaryMenu());
        $this->add_data('wpHeader',$this->_getWpHeader());
        $this->add_data('wpFooter',$this->_getWpFooter());
        $this->add_data('SearchForm',$this->_getSearchForm());
        /**
         * @todo needs to be a theme option.  Manually adding for now
         */
        $this->add_data('IncludeBreadcrumbs',false);


        /**
         * @todo if we are doing this on the constructor, and making it a publicly available member, then why does
         * the method need to be publicly accessible?
         */
        $this->getPageList();
        $this->getLastModifiedDate();
    }

    public function  getLastModifiedDate($strDateFormat=null)
    {
        //set our formatting option
        if(is_null($strDateFormat)){
            $strDateFormat = $this->aryOptions['date_format'];
        }

        if(!$this->is_set('LastModifiedDate')){
            if(is_single() || is_page()){
                $strModifiedDate = get_the_modified_time($this->aryOptions['date_format']);
            } else {
                /**
                 * Get the last modified date of the most recent object
                 */
                global $wpdb;
                $strLastModDate = $wpdb->get_var( "SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified DESC LIMIT 1" );
                $strModifiedDate = date($this->aryOptions['date_format'],strtotime($strLastModDate));
            }

            $this->add_data('LastModifiedDate',$strModifiedDate);
        } elseif($strDateFormat != $this->aryOptions['date_format']) {
            /**
             * date modified is already set, but they are also passing in a date format. verify that the current date
             * we have set is the same format as the one they are requesting.
             */
            if($this->ModifiedDate != $strNewFormattedDate = date($strDateFormat,strtotime($this->LastModifiedDate))){
                return $strNewFormattedDate;
            }
        }

        return $this->LastModifiedDate;
    }

    public function getPageList($aryExclude=array())
    {
        if(!$this->is_set('PageList')){
            $aryPageListOptions = array(
                'depth'        	=> 4, // if it's a top level page, we only want to see the major sections
                'title_li'		=> '',
                'exclude'      	=> implode(',',$aryExclude),
                'walker' 		=> new A11yPageWalker(),
                'echo'          => false,
            );
            $this->add_data('PageList',wp_list_pages($aryPageListOptions));
        }

        return $this->PageList;
    }

    public function getSidebar($strSidebarName)
    {
        return $this->_captureOutput('dynamic_sidebar',array($strSidebarName));
    }

    public function currentPublicMembers()
    {
        return array_keys($this->aryData);
    }

    protected function _getWpHeader()
    {
        return $this->_captureOutput('wp_head');
    }

    protected function _getWpFooter()
    {
        return $this->_captureOutput('wp_footer');
    }

    protected function _getSearchForm()
    {
        return $this->_captureOutput('get_search_form');
    }

    private function _getSiteName()
    {
        //return get_bloginfo('name');
        return $this->_getSiteOption('blogname');
    }

    private function _getSiteHomeURL()
    {
        //return home_url();
        return $this->_getSiteOption('home');
    }

    private function _getParentThemeURL()
    {
        return get_template_directory_uri().'/';
    }

    private function _getParentThemePath()
    {
        return get_template_directory() . DIRECTORY_SEPARATOR;
    }

    private function _getChildThemePath()
    {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR;
    }

    private function _getChildThemeURL()
    {
        /**
         * get_stylesheet_directory_uri will return the URL of the active theme or child theme
         */
        return get_stylesheet_directory_uri().'/';
    }

    private function _getActiveStylesheet()
    {
        return get_stylesheet_uri();
    }

    protected function _getSiteOption($strOption)
    {
        /**
         * Leaving this here for possible use, but retrieving and setting all options seems to be a bit of overkill
         * $aryOptions = wp_load_alloptions();
         */

        return get_option($strOption);
    }

    protected function _getActiveThemeURL()
    {

        return ($this->ParentThemeURL == $this->ChildThemeURL) ? $this->ParentThemeURL : $this->ChildThemeURL;
    }

    protected function _getActiveThemePath()
    {
        if(is_child_theme()){
            return $this->ChildThemePath;
        } else {
            return $this->ParentThemePath;
        }
    }

    protected function _getTrackingCode()
    {
        return $this->_getSiteOption('tracking_input');
    }

    protected function _getAudienceMenu()
    {
        return $this->_getWPMenu('audience');
    }

    protected function _getPrimaryMenu()
    {
        return $this->_getWPMenu('primary');
    }

    protected function _getWPMenu($strMenuName,$strMenuFormat = null)
    {
        _mizzou_log($strMenuName,'name of menu requested',false,array('func'=>__FUNCTION__));
        if(is_null($strMenuFormat)){
            $strMenuFormat = $this->aryOptions['menu_format'];
        }

        $aryMenuOptions = array(
            'theme_location'    => $strMenuName,
            'items_wrap'        => $strMenuFormat
        );

        /**
         * wp_nav_menu needs the first parameter passed to it to be an array.  captureoutput needs an array of parameters
         * that it passes to the called function, so we need to pass our array of option as the first item in the array
         * that we pass to captureoutput
         */
        return $this->_captureOutPut('wp_nav_menu',array($aryMenuOptions));
    }
}