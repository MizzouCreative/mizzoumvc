<?php
/**
 * Collects and contains site-specific information
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 *
 * @uses class WpBase()
 * @uses home_url()
 * @uses get_bloginfo()
 * @uses get_template_directory_uri()
 * @uses get_stylesheet_directory_uri()
 * @uses A11yPageWalker
 * @uses wp_list_pages()
 *
 * @todo some dependcencies need to be addressed.
 * @todo there are a couple of methods that seem outside the scope of the class
 *
 * ASSUMES that Base.php and A11yPageWalker.php classes has already been included
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'FrameworkSettings.php';

/**
 * Stores basic information used across the site.
 * @todo move getView into Framework class?
 */
class Site extends Base {
    /**
     * @var array default options
     */
    protected  $aryOptions = array(
        'date_format'       => 'M j, Y',
        'menu_format'       => '<ul id="%1$s" class="%1$s %2$s">%3$s</ul>',
        'pagelist_exclude'  => array(),
        'config_file'       => 'config.ini',
    );

    protected $objFrameworkSettings = null;

	/**
	 * @var array stores options that are loaded in from config.ini
	 */
	protected $arySiteOptions = array();

    protected $strCollapseSettingsPattern = '([a-zA-Z]*)_?\d';

    public function __construct($aryOptions = array())
    {
        $this->aryOptions = array_merge($this->aryOptions,$aryOptions);

        /**
         * @todo replace this with injecting an instance of the FrameworkSettings class.
         */
        $this->objFrameworkSettings = FrameworkSettings::getInstance();

        $this->add_data('CopyrightYear',date('Y'));
        $this->add_data('Name',$this->_getSiteName());
        $this->add_data('URL',$this->_getSiteHomeURL());
        $this->add_data('ParentThemeURL',$this->_getParentThemeURL());
        $this->add_data('ChildThemeURL',$this->_getChildThemeURL());
        //$this->add_data('ActiveStylesheet',$this->_getActiveStylesheet());
        $this->add_data('ActiveThemeURL',$this->_getActiveThemeURL());
        $this->add_data('ParentThemePath',$this->_getParentThemePath());
        $this->add_data('ChildThemePath',$this->_getChildThemePath());
        $this->add_data('ActiveThemePath',$this->_getActiveThemePath());
        $this->add_data('TrackingCode',$this->_getTrackingCode());
        $this->add_data('IsChild',is_child_theme());
        $this->add_data('IsMultisite',(defined('MULTISITE') && MULTISITE) ? MULTISITE : false);
        /**
         * Now we need to see if we are in a multisite situation
         */
        if($this->IsMultisite){
            //are we on the parent site, or a sub site?
            if('1' != get_current_blog_id()){
                switch_to_blog(1);
                $strParentSiteName = $this->_getSiteName();
                $strParentSiteURL = $this->_getSiteHomeURL();
                //and let's load up the options from the Parent site
                $this->_loadOptions();//get the parent options
                restore_current_blog();
            } else {
                //ok we're on the parent site, so we'll reuse values
                $strParentSiteName = $this->Name;
                $strParentSiteURL = $this->URL;
            }

            $this->add_data('ParentName',$strParentSiteName);
            $this->add_data('ParentURL',$strParentSiteURL);


        }

        /**
         * @todo if we are doing this on the constructor, and making it a publicly available member, then why does
         * the method need to be publicly accessible?
         */
        //$this->getPageList();
        $this->getLastModifiedDate();

        /**
         * If we arent in a multisite, or if we are but are in a child site, go load up the options

        if(!$this->IsMultisite || ($this->IsMultisite && '1' != get_current_blog_id())){
            $this->_loadOptions();
        }*/

        $this->_loadOptions();
    }

    /**
     * Returns last modified date of the site based either the modified time of the current page/single post, or if
     * on a different page, the last modified date of the post that was most recently modified
     * @param string $strDateFormat format to use for last modified date
     * @return string string-formatted last modified date
     * @uses get_the_modified_time wordpress function
     * @uses $wpdb wordpress global
     * @uses $wpdb->get_var
     * @todo we probably need the ability to format the date according to AP Style like we do for Posts, but where should
     * that code be placed and how do we handle the dependency?  Seems like that could be a static class?
     */
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

    /**
     * Stores the list of pages+link as returned by wp_list_pages
     * @param array $aryExclude
     * @return mixed
     * @uses wp_list_pages wordpress function
     * @todo specific to the original IPP implementation. See if we still need this
     */
    public function getPageList($aryExclude = array())
    {
        //if the pagelist hasnt been set, or if they have requested a different exclusion list
        if(!$this->is_set('PageList') || $this->aryOptions['pagelist_exclude'] !== $aryExclude) {
            $this->aryOptions['pagelist_exclude'] = $aryExclude;
            $aryPageListOptions = array(
                'depth'        	=> 4, // if it's a top level page, we only want to see the major sections
                'title_li'		=> '',
                'exclude'      	=> implode(',',$aryExclude),
                'walker' 		=> new A11yPageWalker(), /* @todo how should we deal with this dependency? */
                'echo'          => false,
            );
            //_mizzou_log($aryPageListOptions,'aryPageListOptions',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
            $this->add_data('PageList',wp_list_pages($aryPageListOptions));
            //_mizzou_log($this->PageList,'PageList as stored in Site object',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
        }

        return $this->PageList;
    }

    /**
     * Captures and returns the contents from the call to wordpress' dynamic_sidebar function
     * @param string $strSidebarName name or id of the dynamic sidebar
     * @return string sidebar html contents
     */
    public function getSidebar($strSidebarName)
    {
        return $this->_captureOutput('dynamic_sidebar',array($strSidebarName));
    }

    /**
     * Returns a list of "public members" of the object.
     * We're storing all data pieces inside of $this->aryData, so we really dont have ANY public members
     * @return array
     * @todo should this be moved up to the parent class?
     */
    public function currentPublicMembers()
    {
        return array_keys($this->aryData);
    }

    /**
     * Captures and returns contents of wp_head wordpress function
     * @return string contents as returned by wp_head()
     * @deprecated
     */
    protected function _getWpHeader()
    {
        return $this->_captureOutput('wp_head');
    }

    /**
     * Captures and returns contents of wp_footer wordpress function
     * @return string contents as returned by wp_footer()
     * @deprecated
     */
    protected function _getWpFooter()
    {
        return $this->_captureOutput('wp_footer');
    }

    /**
     * Captures and returns contents of the get_search_form wordpress function
     * @return string contents as returned by get_search_form()
     * @deprecated
     */
    protected function _getSearchForm()
    {
        return $this->_captureOutput('get_search_form');
    }

    /**
     * Returns the name of the site as defined in Wordpress settings
     * @return string site blog name
     */
    private function _getSiteName()
    {
        //return get_bloginfo('name');
        return $this->_getSiteOption('blogname');
    }

    /**
     * Returns the site's Home URL as defined in Wordpress settings. Always contains trailing /
     * @return string site's home url
     */
    private function _getSiteHomeURL()
    {
        //return home_url();
        $strHomeURL = $this->_getSiteOption('home');
        // we could preg_match but that's generally slower than strpos
        if(0 !== strpos(strrev($strHomeURL),'/')){
            $strHomeURL .= '/';
        }
        return $strHomeURL;
    }

    /**
     * Returns the Parent theme's URL. Includes ending forward slash.
     * Wrapper function to get_template_directory_uri() wordpress function.
     * @return string Parent Theme's URL
     * @uses get_template_directory_uri wordpress function
     */
    private function _getParentThemeURL()
    {
        return get_template_directory_uri().'/';
    }

    /**
     * Returns Parent theme's server path. Includes ending directory separator.
     * Wrapper function to get_template_directory() wordpress function.
     * @return string Parent theme's server path
     * @uses get_template_directory wordpress function
     */
    private function _getParentThemePath()
    {
        return get_template_directory() . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns Child Theme's server path. Includes ending directory separator.
     * Wrapper function to get_stylesheet_directory() wordpress function.
     * @return string Child theme's server path
     * @uses get_stylesheet_directory wordpress function
     */
    private function _getChildThemePath()
    {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR;
    }

    /**
     * Retrieves the child theme's URL. Includes ending forward slash.
     * Wrapper function for get_stylesheet_directory_uri() wordpress functions.
     * @return string Child theme's URL
     * @uses get_stylesheet_directory_uri wordpres function
     */
    private function _getChildThemeURL()
    {
        /**
         * get_stylesheet_directory_uri will return the URL of the active theme or child theme
         */
        return get_stylesheet_directory_uri().'/';
    }

    /**
     * Returns the active theme's stylesheet URL.
     * Wrapper function for get_stylesheet_uri() wordpress function.
     * @return string active stylesheet URL, be it child or parent
     * @uses get_stylesheet_uri wordpress function
     * @deprecated moved to Header model
     */
    private function _getActiveStylesheet()
    {
        return get_stylesheet_uri();
    }

    /**
     * Retrieves/returns a site option
     * Wrapper function for get_option() wordpress function.
     * @param string $strOption Site option to retrieve
     * @return mixed
     * @uses get_option wordpress function
     */
    protected function _getSiteOption($strOption)
    {
        /**
         * Leaving this here for possible use, but retrieving and setting all options seems to be a bit of overkill
         * $aryOptions = wp_load_alloptions();
         */

        return get_option($strOption);
    }

    /**
     * Returns the active theme's url
     * @return string active theme's URL
     */
    protected function _getActiveThemeURL()
    {
        return ($this->ParentThemeURL == $this->ChildThemeURL) ? $this->ParentThemeURL : $this->ChildThemeURL;
    }

    /**
     * Returns the active theme's directory path
     * @return string active theme's directory path
     * @uses is_child_theme wordpress function
     */
    protected function _getActiveThemePath()
    {
        if(is_child_theme()){
            return $this->ChildThemePath;
        } else {
            return $this->ParentThemePath;
        }
    }

    /**
     * Returns the custom site option 'tracking input'.  Typically this will be your google analytics code
     * @return mixed
     * @todo this is IPP-specific unless we agree that all sites/themes will include this custom option.
     * I'm actually torn on the validity of allowing the changing of analytics code in the wordpress GUI vs statically
     * placing the tracking code in the footer view.
     * @todo further, this should probably be moved into the footer
     */
    protected function _getTrackingCode()
    {
        return $this->_getSiteOption('tracking_input');
    }

    /**
     * Returns the markup for the audience menu
     * @return string markup for the audience menu
     * @todo this is IPP specific unless we agree that all sites/themes will include an audience menu
     */
    protected function _getAudienceMenu()
    {
        return $this->_getWPMenu('audience');
    }

    /**
     * Returns the markup for the primary audience menu
     * @return string markup for the primary audience menu
     * @todo this is IPP specific unless we agree that all sites/theme will include an audience menu
     */
    protected function _getPrimaryMenu()
    {
        return $this->_getWPMenu('primary');
    }

    /**
     * Captures and returns the markup+contents of the requested wordpress menu
     * @param $strMenuName Name, ID or slug of the menu to retrieve
     * @param null $strMenuFormat sprintf format that the menu should follow (items_wrap in the options of wp_nav_menu)
     * @return string markup of the menu requested
     * @todo seems like we need a public getMenu method that calls this method.
     */
    protected function _getWPMenu($strMenuName,$strMenuFormat = null)
    {
        //_mizzou_log($strMenuName,'name of menu requested',false,array('func'=>__FUNCTION__));
        if(is_null($strMenuFormat)){
            $strMenuFormat = $this->aryOptions['menu_format'];
        }

        $aryMenuOptions = array(
            'theme_location'    => $strMenuName,
            'menu'              => $strMenuName,
            'items_wrap'        => $strMenuFormat,
            'container'         => false,
        );

        /**
         * wp_nav_menu needs the first parameter passed to it to be an array.  captureoutput needs an array of parameters
         * that it passes to the called function, so we need to pass our array of option as the first item in the array
         * that we pass to captureoutput
         */
        return $this->_captureOutPut('wp_nav_menu',array($aryMenuOptions));
    }

    /**
     * Loads options from the parent and child config.ini files
     * return void
     */
    protected function _loadOptions()
    {

        //load up the framework options
        $aryOptions = array();
        $objWpBase = new WpBase();
        $arySettingsPages = $objWpBase->retrieveContent(array(
            'post_type'=>'mizzoumvc-settings',
            'include_meta'=>true,
            'include_image'=>false,
            ));



        foreach($arySettingsPages as $objSettingsPage){
            /**
             * @todo
             * 1. Do we want to make the key the same as the title (name)? Or should we make it the slug?
             * 2. Should we check to see if we already have a settings page with the same name (which would be a reason
             *    to use slug instead of name)? and if so, merge?
             */
            $aryOptions[$objSettingsPage->slug] = $objSettingsPage->getAllCustomData();
        }

        /**
        $aryOptions = $this->_loadOptionsFile(MIZZOUMVC_ROOT_PATH.'config.ini');
        //load up any options from the parent theme
        $arySiteOptions = $this->_loadOptionsFile($this->aryData['ParentThemePath'].$this->aryOptions['config_file']);

        /**
         * @todo this needs to be refactored.  We're repeating the same @#$#$ steps
         *//*
        foreach($arySiteOptions as $mxdSiteKey => $mxdSiteVal){
            if(isset($aryOptions[$mxdSiteKey]) && is_array($mxdSiteVal)){
                $aryOptions[$mxdSiteKey] = array_merge($aryOptions[$mxdSiteKey],$mxdSiteVal);
            } else {
                $aryOptions[$mxdSiteKey] = $mxdSiteVal;
            }
        }

        //do we have a child site we are working with?
        if($this->aryData['ActiveThemePath'] != $this->aryData['ParentThemePath']){
            $aryChildOptions = $this->_loadOptionsFile($this->aryData['ActiveThemePath'].$this->aryOptions['config_file']);
        } else {
            $aryChildOptions = array();
        }

        // merge the parent and child theme options together
        foreach($aryChildOptions as $mxdChildKey => $mxChildVal){
            if(isset($aryOptions[$mxdChildKey]) && is_array($mxChildVal)){
                $aryOptions[$mxdChildKey] = array_merge($aryOptions[$mxdChildKey],$mxChildVal);
            } else {
                $aryOptions[$mxdChildKey] = $mxChildVal;
            }
        }*/
        //_mizzou_log($aryOptions,'all of our custom options',false,array('line'=>__LINE__,'file'=>__FILE__));
        // add each option so it can be accessed directly.
        foreach($aryOptions as $mxdOptionKey => $mxdOptionVal){
            if(isset($this->aryData[$mxdOptionKey])){
                //if the old value is an array, and the new value is an array, merge the two
                if(is_array($this->aryData[$mxdOptionKey]) && is_array($mxdOptionVal)){
                    $mxdOptionVal = array_merge($this->aryData[$mxdOptionKey],$mxdOptionVal);
                } else {
                    _mizzou_log($this->aryData[$mxdOptionKey],'looks like we are going to overwrite option' . $mxdOptionKey . '. This is the original value',false,array('line'=>__LINE__,'file'=>__FILE__));
                    _mizzou_log($mxdOptionVal,'this is the new value for option ' . $mxdOptionKey,false,array('line'=>__LINE__,'file'=>__FILE__));
                }
            }

            if($this->objFrameworkSettings->flatten_groups){
                $mxdOptionVal = $this->_consolidateGroups($mxdOptionVal);
            }

            $this->add_data($mxdOptionKey,$mxdOptionVal);
        }

        //load up a flattened collection of options
        $this->_loadFlattenedOptions($aryOptions);
        //_mizzou_log($this,'our Site object after dealing with the options',false,array('line'=>__LINE__,'file'=>__FILE__));
    }

    /**
     * Takes all of our user defined options and creates a flat array
     * @param array $aryOptions list of options
     * @return void
     */
    protected function _loadFlattenedOptions(array $aryOptions)
    {
        foreach($aryOptions as $mxdKey => $mxdVal){
            /*
             * This one might need explaining. We only want to flatten out the options if they have an associative
             * key.  If it is an indexed array, we dont want to flatten it since we wouldnt have a key to name it with.
             * Easiest way to see if it is associative is to get an array of just the values and strictly compare that
             * with the original array
             */
            if(is_array($mxdVal) && array_values($mxdVal) !== $mxdVal){
                $this->_loadFlattenedOptions($mxdVal);
            } else {
                if(!isset($this->arySiteOptions[$mxdKey])){
                    $this->arySiteOptions[$mxdKey] = $mxdVal;
                } else {
                    /**
                     * well, we've got two subkeys with the same name
                     * @todo what do we do? give the second key a different name? log it? warning?
                     */
                }
            }
        }
    }

    /**
     * Safely runs parse_ini_file on $strPath
     * @param $strPath config.ini location
     * @return array options loaded in from the config.ini file, or empty array if failure
     */
    protected function _loadOptionsFile($strPath)
    {
        if(!file_exists($strPath) || FALSE == $aryReturn = parse_ini_file($strPath,true)){
            $aryReturn = array();
        }

        return $aryReturn;
    }

    /**
     * Retrieves a user-defined option
     * @param string $strOption option to retrieve
     * @return mixed option contents
     */
    public function option($strOption)
    {
        return (isset($this->arySiteOptions[$strOption])) ? $this->arySiteOptions[$strOption] : '';
    }

    /**
     * Consolidates grouped settings into an array
     * For any setting where the key/field is named <field>_#, it will consolidate the values into an array keyed
     * as <field>. Example
     *
     * address_1
     * address_2
     * address_3
     *
     * Will becomes an array 'address' containing the values from the three fields
     * @param array $arySettings
     */
    protected function _consolidateGroups($arySettings)
    {
        foreach($arySettings as $mxdKey=>$mxdVal){
            if(is_array($mxdVal)){
                $arySettings[$mxdKey] = $this->_consolidateGroups($mxdVal);
            }
        }

        //find all of the field keys that match our pattern
        $aryMetaGroupKeys = preg_grep($this->strCollapseSettingsPattern,array_keys($arySettings));
        _mizzou_log($aryMetaGroupKeys,'matches i found from the grep');
        //loop through each match, pull out the group component and add it the group array
        foreach($aryMetaGroupKeys as $strKeyInGroup){
            if(1 === preg_match($this->strCollapseSettingsPattern,$strKeyInGroup,$aryMatch)){
                _mizzou_log($aryMatch,'we have a pregmatch on ' . $strKeyInGroup.'. here is the match',false,array('line'=>__LINE__,'file'=>__FILE__));
                $strNewKey = $aryMatch[1];
                if(!isset($arySettings[$strNewKey])){
                    $arySettings[$strNewKey] = array();
                } elseif (!is_array($arySettings[$strNewKey])){
                    /**
                     * ok we have a situation where there is already a key set up for the group name, but it isnt an
                     * array. Most likely we have something like "address" and then "address2". so, let's take the data
                     * that is currently there and store it, create a new array, and then add that data back in
                     */
                    $strLogMsg = "looks like you have a meta field group but the first item is named what the group "
                        . "name needs to become. Fixing it for you, but you should really go back and rename the key.";
                    _mizzou_log($strNewKey,$strLogMsg,false,array('func'=>__FUNCTION__));
                    $strTempData = $arySettings[$strNewKey];
                    $arySettings[$strNewKey] = array($strTempData);
                }

                $this->aryData[$strNewKey][] = $arySettings[$strKeyInGroup];
                unset($arySettings[$strKeyInGroup]);
            }
        }
    }
}