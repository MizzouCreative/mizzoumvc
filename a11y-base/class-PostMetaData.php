<?php
/**
* Gathers all the custom data related to a post
* 
* @package WordPress
* @subpackage theme-helper
* @category theme
* @category class
* @author Paul F. Gilzow & Jason Rollins, Web Communications, University of Missouri
* @copyright 2013 Curators of the University of Missouri
* @version 201303281212
* 
* 
*/
require_once(dirname(__FILE__).'/class-CustomPostTypeAbstract.php');
require_once(dirname(__FILE__).'/class-ImageData.php');

class PostMetaData extends CustomPostType{
    /**
    * All of our custom fields are prefixed by their custom post type type (ie news, people, etc).  When dealing with properties from this 
    * object, we want to access them directly (ie first_name instead of people_first_name).
    * 
    * @var string
    */
    private $strPrefix = null;
    
    /**
    * put your comment there...
    * 
    * @var object
    */
    public $featured_image = null;
    
    /**
    * stores data on the images that might be attached to the post
    * 
    * @var array
    */
    private $image_data = null;            
    
    /**
    * put your comment there...
    * 
    * @param integer $intPostID
    * @param string $strPrefix
    * @return PostMetaData
    */
    function __construct($intPostID,$strPrefix = null)
    {
        if(is_numeric($intPostID)){
            $this->intPostID = $intPostID; 
            if(!is_null($strPrefix)) $this->strPrefix = $strPrefix; 
            $this->_retrieve_wp_data();
            $this->_reformat_data(); 
            $this->_retrieve_permalink();
            $this->_retrieve_post_format();
        } else {
            $this->add_error('Post ID given in constructor must be numeric');
        }
    }
    
    /**
    * Retrieves the custom post data for a post from wordress
    * 
    */
    protected function _retrieve_wp_data()
    {
        $this->aryOriginalData = get_post_custom($this->intPostID);        
    }
    
    protected function _retrieve_permalink()
    {
        $this->add_data('permalink', get_permalink($this->intPostID));
    }
    
    protected function _retrieve_post_format()
    {
        $this->add_data('post_format', (FALSE !== $strPostFormat = get_post_format($this->intPostID)) ? $strPostFormat : 'standard');
    }

    
    /**
    * Reformats the post's custom data.
    * 
    * Removes the prefix from the id, and removes any internal custom data fields
    * 
    */
    private function _reformat_data()
    {
        $boolRemovePrefix = (!is_null($this->strPrefix)) ? true : false;
        if($boolRemovePrefix) $intPrefixLen = strlen($this->strPrefix);
        foreach($this->aryOriginalData as $key=>$val){
            if(strpos($key,'_') !== 0){ //we dont need the interal custom data keys=>vals
                if($boolRemovePrefix){
                    $key = substr($key,$intPrefixLen);
                }
                
                $this->aryData[$key] = $val[0];    
            }    
        }
    } 
    
    /**
    * put your comment there...
    * 
    * @param array $aryItems
    * @param string $strValue
    */
    public function includeSideBar(array $aryItems,$strValue)
    {
        $boolIncludeSidebar = false;
        
        if($this->member_of_group_set($aryItems)){
            //we know that at least one of the items we need ffrom the groups is set.
            $intNumItems = count($aryItems);
            $i = 0;
            while($i<$intNumItems && !$boolIncludeSidebar){
                if($this->get($aryItems[$i]) == $strValue){
                    $boolIncludeSidebar = true;
                }
                ++$i;
            }     
        } 
        
        return $boolIncludeSidebar;    
    }
    
    /**
    * put your comment there...
    * 
    * @param array $aryOptions
    * @return mixed
    */
    public function retrieve_feature_image_data($aryOptions=array())
    {
        $aryDefaults = array(
            'return'=>true,
            'captions'=>false
        );
        
        $aryOptions = $this->parse_arguments($aryOptions,$aryDefaults);
        
        if(!isset($this->featured_image)){
            if(!$this->image_data_set()) $this->image_data = array();
            if(has_post_thumbnail($this->intPostID)){
                $this->add_data('featured_image',get_post_thumbnail_id($this->intPostID));
                //$this->featured_image = ;
                $this->image_data[$this->aryData['featured_image']] = new ImageData($this->aryData['featured_image'],$aryOptions['captions']);                  
            } else {
                $this->add_data('featured_image', FALSE);
            }
  
        }

        if($aryOptions['return'] ){
            return (is_bool($this->aryData['featured_image'])) ? $this->aryData['featured_image'] : $this->image_data[$this->aryData['featured_image']];
        }
            
    }
    
    /**
    * put your comment there...
    * 
    * @param array $aryOptions
    * @param array $aryDefaults
    * @return array
    * @uses wp_parse_args() from wordpress core. Could be replaced with a standard array_merge if needed
    */
    protected function parse_arguments($aryOptions,$aryDefaults)
    {
//         catch legacy code and inproper argument
         if(is_bool($aryOptions)){
            $aryOptions = array('return'=>$aryOptions);
            $this->_log($aryOptions,'legacy code use detected.',true,array('func'=>__FUNCTION__,'line'=>__LINE__));    
         } elseif(!is_array($aryOptions)){
             $mxdOptions = array();
             $this->_log(null,'Argument needs to be an an array',false,array('func'=>__FUNCTION__,'line'=>__LINE__));
         }
         
         return wp_parse_args($aryOptions,$aryDefaults);        
    }
    
    /**
    * put your comment there...
    * 
    * @param array $aryOptions
    * @return array
    */
    public function retrieve_gallery_images($aryOptions=array())
    {
         $aryDefaults = array(
            'return'=>true,
            'featured'=>true,
            'captions'=>false
         );
         
         $aryOptions = $this->parse_arguments($aryOptions,$aryDefaults);
         
    
//        do we have any image data at all? Or do we have only featured image data?
        if(!$this->image_data_set() || (isset($this->aryData['featured_image']) && count($this->image_data) == 1)){
//           do we need to set the image_data to an array?
            if(!$this->image_data_set()) $this->image_data = array();
//            do we have info on the featured image?
            if(!isset($this->aryData['featured_image'])){
                $this->retrieve_feature_image_data(false);
            }
            
            $aryParams = array(
                'numberposts'       => 5,
                'post_mime_type'    => 'image',
                'post_type'         => 'attachment',
                'post_parent'       => $this->intPostID,
                'orderby'           => 'menu_order ASC, ID', 
                'order'             => 'DESC'
            );
            
            $aryImages = get_posts($aryParams);
            //$this->_log($aryImages,'images for post '.$this->intPostID);
            foreach($aryImages as $objImage){
                if(!array_key_exists($objImage->ID,$this->image_data)){
                    $objImageData = new ImageData($objImage->ID,$aryOptions['captions']);
                    $this->image_data[$objImage->ID] = $objImageData;    
                }
            }            
        }
        
        if($aryOptions['return']){
            if($aryOptions['featured']){
                return $this->image_data;
            } else {
                return array_slice($this->image_data,1,(count($this->image_data)-1),true);
            }
                    
        }
    }
    
    /**
    * Checks to see if we have image data
    * 
    * @return boolean
    * 
    */
    protected function image_data_set()
    {
        return is_array($this->image_data);
    }
    
    public function retrieve_parent_name()
    {
        if(!$this->is_set('post_parent_name')){
            $strParent = '';
            $objPost = get_post($this->intPostID);
            if(is_integer($objPost->post_parent) && $objPost->post_parent != 0){
                $objPostParent = get_post($objPost->post_parent);
                $strParent = $objPostParent->post_name;
            }           
        
            $this->add_data('post_parent_name', $strParent);
        }
        
        return $this->get('post_parent_name');
    }

    protected function _consolidateMetaGroups()
    {

    }
}

/**
 *CHANGELOG
 * 20120706 - changed retrieve_feature_image_data() to include a check for whether or not the page has a featured image.  If it doesn't, 'featured_image will return bool false.
 */
?>