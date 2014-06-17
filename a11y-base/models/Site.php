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
        'date_format'   => 'M j, Y'
    );

    public function __construct($aryOptions = array())
    {
        $this->aryOptions = array_merge($this->aryOptions,$aryOptions);

        $this->add_data('CopyrightYear',date('Y'));
        $this->add_data('Name',$this->_getSiteName());
        $this->add_data('URL',$this->_getSiteHomeURL());
        $this->add_data('ParentThemeURL',$this->_getParentThemeURL());
        $this->add_data('ChildThemeURL',$this->_getChildThemeURL());
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
        return get_bloginfo('name');
    }

    private function _getSiteHomeURL()
    {
        return home_url();
    }

    private function _getParentThemeURL()
    {
        return get_template_directory_uri();
    }

    private function _getChildThemeURL()
    {
        return get_stylesheet_directory_uri();
    }
} 