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

/**
 * Class ImageData
 */
class ImageData extends PostBase
{
    /**
    * Default wordpress image sizes.
    *
    * I thought perhaps we could putt the default sizes directly from wordpress, instead of hardcoding them here, but it
    * appears that they are hard-coded into wordpress as well...
     * @see line 630 https://core.trac.wordpress.org/browser/tags/3.9.1/src/wp-includes/media.php#L0
    * 
    * @var array
    */
    protected $aryImageSizes = array(
        'thumbnail',
        'medium',
        'large',
        'full'
    );

    /**
     *
     * @param $mxdPostData
     * @param bool $boolIncludeCaption deprecated. included for backwards compatibility
     */
    public function __construct($mxdPostData,$boolIncludeCaption = false){
        parent::__construct($mxdPostData);
        $this->aryData['ID'] = $this->objOriginalPost->post_id;

        $this->_retrieve_wp_data();

        unset($this->objOriginalPost);

    }

    /**
     *
     */
    protected function _retrieve_wp_data(){
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

        //set the caption
        $this->add_data('caption',$this->objOriginalPost->post_excerpt);
    }

    /**
     * @deprecated
     * @return null
     */
    public function get_caption(){
        return null;
    }
}
?>