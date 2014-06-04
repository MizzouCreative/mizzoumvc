<?php
/**
* Gathers all the image data related to a post
* 
* @package WordPress
* @subpackage theme-helper
* @category theme
* @category class
* @author Paul F. Gilzow & Jason Rollins, Web Communications, University of Missouri
* @copyright 2013 Curators of the University of Missouri
* @uses $_wp_additional_image_sizes
* @version 201212211512 
*/
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'PostBase.php';
class ImageData extends PostBase
{
    /**
    * Default wordpress image sizes
    * 
    * @var array
    */
    var $aryImageSizes = array(
        'thumbnail',
        'medium',
        'large',
        'full'
    );
    
    var $objOriginalPost = null;
    
    function __construct($mxdPostData,$boolIncludeCaption = false){
            parent::__construct($mxdPostData);
            $this->_retrieve_wp_data();
            if($boolIncludeCaption) $this->get_caption();
            $this->aryData['ID'] = $this->objOriginalPost->post_id;
    }
    
    function _retrieve_wp_data(){
        /**
        * Get the alt data for the image
        */
        
        $strAltText =  get_post_meta($this->aryData['ID'],'_wp_attachment_image_alt',true);
        $strAltText = ($strAltText != '') ? $strAltText : $this->objOriginalPost->post_title;
        //$this->_log($this->objOriginalPost,'image object data',false,array('line'=>__LINE__,'func'=>__FUNCTION__));
        $this->add_data('alt',$strAltText);
        
        /**
        * Get the source links for all of the different sizes we might have
        */
        global $_wp_additional_image_sizes;
        /**
         * If there are no additional image sizes defined, wordpress has this set to NULL instead of an empty array. 
         */
        $arySizes = (is_null($_wp_additional_image_sizes)) ? array() : array_keys($_wp_additional_image_sizes);
        $arySizes = array_merge($arySizes,$this->aryImageSizes);
        //$this->_log($arySizes, 'array of image sizes', false, array());
        foreach($arySizes as $strSize){
            $arySizeSrc = wp_get_attachment_image_src($this->intPostID,$strSize);
            $this->add_data('src_'.$strSize,$arySizeSrc[0]);
        }    
    }
    
    function get_caption(){
        /** @deprecated going to go ahead and always get the whole object
        if(is_null($this->objOriginalPost) || !is_object($this->objOriginalPost)){
             $this->objOriginalPost = get_post($this->intPostID); 
        }
        */
        
        //$this->_log($this->objOriginalPost,'trying to find caption for this image');
        $this->add_data('caption',$this->objOriginalPost->post_excerpt);
    }
}
?>