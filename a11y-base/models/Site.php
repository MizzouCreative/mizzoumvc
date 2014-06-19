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
        $this->add_data('TrackingCode',$this->_getTrackingCode());
        $this->add_data('PrimaryMenu',$this->_getPrimaryMenu());
        $this->add_data('AudienceMenu',$this->_getAudienceMenu());
    }

    public function  getLastModifiedDate($strDateFormat=null)
    {
        //set our formatting option
        if(is_null($strDateFormat)){
            $strDateFormat = $this->aryOptions['date_format'];
        }

        if(!$this->is_set('ModifiedDate')){
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

            $this->add_data('ModifiedDate',$strModifiedDate);
        } elseif($strDateFormat != $this->aryOptions['date_format']) {
            /**
             * date modified is already set, but they are also passing in a date format. verify that the current date
             * we have set is the same format as the one they are requesting.
             */
            if($this->ModifiedDate != $strNewFormattedDate = date($strDateFormat,strtotime($this->ModifiedDate))){
                return $strNewFormattedDate;
            }
        }

        return $this->ModifiedDate;
    }

    public function getPageList()
    {
        if(!$this->is_set('PageList')){
            $aryPageListOptions = array(
                'depth'        	=> 4, // if it's a top level page, we only want to see the major sections
                'title_li'		=> '',
                'exclude'      	=> 2129, //why are excluding this item?
                'walker' 		=> new A11yPageWalker(),
                'echo'          => false,
            );
            $this->add_data('PageList',wp_list_pages($aryPageListOptions));
        }

        return $this->PageList;
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
        return get_template_directory_uri();
    }

    private function _getChildThemeURL()
    {
        /**
         * get_stylesheet_directory_uri will return the URL of the active theme or child theme
         */
        return get_stylesheet_directory_uri();
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
        if(is_null($strMenuFormat)){
            $strMenuFormat = $this->aryOptions['menu_format'];
        }

        $aryMenuOptions = array(
            'theme_location'    => $strMenuName,
            'items_wrap'        => $strMenuFormat
        );

        return $this->_captureOutPut('wp_nav_menu',$aryMenuOptions);
    }
    /**
     * Captures the contents of a function that normally echos directly
     *
     * @param $strCallBack
     * @param array $aryOptions
     * @return string
     * @todo discuss with Jeremiah where this should be located. doesn't seem right to have it in this class
     */
    protected function _captureOutPut($strCallBack,$aryOptions=array())
    {
        $strReturn = '';
        if(function_exists($strCallBack) && is_callable($strCallBack)){
            ob_start();
            call_user_func_array($strCallBack,$aryOptions);
            $strReturn = ob_get_contents();
            ob_end_clean();
        }

        return $strReturn;
    }
} 