<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/15/14
 * Time: 1:25 PM
 */

/**
 * This assumes that both files are in the same directory
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'PostBase.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ImageData.php';

class MizzouPost extends PostBase
{
    /**
     * @var string
     */
    protected $strPostPrefix= 'post_';
    /**
     * @var array
     */
    protected $aryPropertyExclude = array('post_type','post_name');
    /**
     * If incl
     * @var array
     */
    protected $aryOptions = array(
        'format_date'   => false,
        'date_format'   => 'F, j Y',
        'suppress_empty'=> false,
        'include_meta'  => false,
        'include_image' => false,
        'excerpt_length'=> 55, //same as wordpress' default
        'permalink'      => 'page',
    );

    /**
     * If include_meta is set as an array in the default options, then meta_prefix should default to this::strPostPrefix
     * and suppress_empty should default to suppress_empty in the main defaults. Though both can be overridden
     * @var array
     */
    protected $aryMetaDefaults = array(
        'meta_prefix'   => null,
        'suppress_empty'=> null
    );
    /**
     * @var string
     */
    private $strMetaGroupPattern = '([a-zA-Z]*)\d';

    public function __construct($mxdPost, $aryOptions = array())
    {
        if($mxdPost->post_type == 'attachment' && isset($aryOptions['foo'])){
            _mizzou_log($aryOptions,'aryOptions as passed into MizzouPost');
        }

        parent::__construct($mxdPost);
        $this->aryOptions = array_merge($this->aryOptions,$aryOptions);

        if($mxdPost->post_type == 'attachment' && isset($aryOptions['foo'])){
            _mizzou_log($this->aryOptions,'our options after merging in MizzouPost');
        }

        $this->_setMembers($this->objOriginalPost);

        if(FALSE !== $this->aryOptions['include_meta']){
            $this->_handleMetaData();
        }

        //now that we're done we no longer need the original post
        unset($this->objOriginalPost);

        if($aryOptions['include_image']){
            $this->getFeaturedImage();
        }
    }

    public function retrieveParentName()
    {
        if(!$this->is_set('parent_name')){
            $strParent = '';

            if(is_integer($this->aryData['parent']) && $this->aryData['parent'] != 0){
                $objPostParent = get_post($this->aryData['parent']);
                $strParent = $objPostParent->post_name;
            }

            $this->add_data('parent_name', $strParent);
        }

        return $this->get('parent_name');
    }

    public function getFeaturedImage($boolReturn = false)
    {
        if(!isset($this->aryData['image']) && !isset($this->aryData['hasFeaturedImage'])){
            $this->add_data('hasFeaturedImage',has_post_thumbnail($this->aryData['ID']));
            if($this->aryData['hasFeaturedImage']){
                $intImageID = get_post_thumbnail_id($this->aryData['ID']);
                //_mizzou_log($intImageID,'the post has a featured image. this is its id');
                $this->add_data('image',new ImageData($intImageID));
            }
        }

        if($boolReturn){
            if($this->aryData['hasFeaturedImage']){
                return $this->aryData['image'];
            } else {
                return false;
            }

        }
    }

    protected function _consolidateMetaGroups($aryOptions)
    {
        //we need the full pattern to use including the prefix, if applicable
        $strFullPattern = $this->_buildFullMetaGroupPattern($aryOptions['meta_prefix']);
        //find all of the field keys that match our pattern
        $aryMetaGroupKeys = preg_grep($strFullPattern,array_keys($this->aryOriginalCustomData));
        //loop through each match, pull out the group component and add it the group array
        foreach($aryMetaGroupKeys as $strKeyInGroup){
            if(1 === preg_match($strFullPattern,$strKeyInGroup,$aryMatch)){
                $strNewKey = $aryMatch[1];
                if(!isset($this->aryData[$strNewKey])){
                    $this->aryData[$strNewKey] = array();
                } elseif (!is_array($this->aryData[$strNewKey])){
                    if(in_array($strNewKey,$this->aryBaseKeys)){
                        /**
                         * we've got a situation where our group of fields happens to match one of the default post
                         * members that we already have set up. Let's log a message and append an 's' to the field
                         * group name.
                         *
                         * Defaults where we might have a name collision include:
                         *  - author
                         *  - title
                         *  - date
                         *  - content
                         *  - excerpt
                         *  - status
                         *  - parent
                         *
                         * @see http://codex.wordpress.org/Class_Reference/WP_Post
                         *
                         */
                        $strLogMsg = 'You have a group of custom meta fields that just happen to have the same name '
                            . "as a default member of the post. I'm going to add an 's' to the group key name";
                        _mizzou_log($strNewKey,$strLogMsg,false,array('func'=>__FUNCTION__));
                        //add an s to the key
                        $strNewKey .= 's';
                        //now check to see if the altered key hasnt been set up.
                        if(!isset($this->aryData[$strNewKey])) $this->aryData[$strNewKey] = array();
                    } else {
                        /**
                         * ok we have a situation where there is already a key set up for the group name, but it isnt an
                         * array. Most likely we have something like "address" and then "address2". so, let's take the data
                         * that is currently there and store it, create a new array, and then add that data back in
                         */
                        $strLogMsg = "looks like you have a meta field group but the first item is named what the group "
                            . "name needs to become. Fixing it for you, but you should really go back and rename the key.";
                        _mizzou_log($strNewKey,$strLogMsg,false,array('func'=>__FUNCTION__));
                        $strTempData = $this->aryData[$strNewKey];
                        $this->aryData[$strNewKey] = array($strTempData);
                    }
                }


                if(!$aryOptions['suppress_empty'] || ($aryOptions['suppress_empty'] && trim($this->aryOriginalCustomData[$strKeyInGroup][0]) != '')){
                    $this->aryData[$strNewKey][] = $this->aryOriginalCustomData[$strKeyInGroup][0];
                }
            }
        }
    }

    protected function _buildFullMetaGroupPattern($strPrefix=null)
    {
        $strPattern = '';
        if(!is_null($strPrefix)){
            $strPattern = $strPrefix;
        }

        $strPattern = '/^'.$strPattern.$this->strMetaGroupPattern.'$/';

        return $strPattern;
    }

    private function _setMembers(WP_Post $objPost)
    {
        $aryPostMembers = get_object_vars($objPost);
        $intPrefixLen = strlen($this->strPostPrefix);
        foreach($aryPostMembers as $strProperty => $strPropertyVal){
            if(0 === strpos($strProperty,$this->strPostPrefix) && !in_array($strProperty,$this->aryPropertyExclude)){
                $strProperty = substr($strProperty,$intPrefixLen);
            } elseif($strProperty == 'post_name') {
                /**
                 * This is the only property from the original post that we are going to completely get rid of and
                 * rename. Wordpress almost universally refers to this property as 'slug', so I'm not sure why they
                 * decided in the post object, and pretty only in the post object to refer to it as 'name'
                 */
                $strProperty = 'slug';
            }

            //add the key into our list of base keys
            $this->aryBaseKeys[] = $strProperty;
            //now add the data to our info
            $this->add_data($strProperty,$strPropertyVal);

        }

        $this->_setPermalink();
        $this->_setPostFormat();
        $this->_setContent();
        $this->_setTitle();
        $this->_processExcerpt();

        $this->aryData['timestamp'] = strtotime($this->aryData['date']);
        /**
         * @todo maybe we should just always create a formatted date and simply allow the calling script to override
         * the format?
         */
        if($this->aryOptions['format_date']){
            $this->_setFormattedDate();
        }

    }

    /**
     * Reformats the post's custom data.
     *
     * Removes the prefix from the id, and removes any internal custom data fields
     *
     */
    private function _reformatMetaData($aryOptions)
    {
        $intPrefixLen = strlen($aryOptions['meta_prefix']);
        foreach($this->aryOriginalCustomData as $strKey=>$mxdVal){
            if(0 !== strpos($strKey,'_')){ //we dont need the interal custom data keys=>vals
                if(0 === strpos($strKey,$aryOptions['meta_prefix'])){
                    $strKey = substr($strKey,$intPrefixLen);
                }

                if(!$aryOptions['suppress_empty'] || ($aryOptions['suppress_empty']) && '' != trim($mxdVal[0])){
                    $this->aryData[$strKey] = $mxdVal[0];
                }
            }
        }
    }

    private function _setPermalink()
    {
        if($this->post_type == 'attachment' && $this->aryOptions['permalink'] == 'download'){
            $strPermalink = get_attachment_link($this->ID);
        } else {
            $strPermalink = get_permalink($this->ID);
        }
        $this->add_data('permalink',$strPermalink);
    }

    private function _setPostFormat()
    {
        $this->aryData['post_format'] = (FALSE !== $strPostFormat = get_post_format($this->aryData['ID'])) ? $strPostFormat : 'standard';
    }

    private function _setContent()
    {
        $this->aryData['content_raw'] = $this->aryData['content'];
        $this->aryData['content'] = apply_filters('the_content',$this->aryData['content_raw']);
    }

    private function _setTitle()
    {
        $this->aryData['title_raw'] = $this->aryData['title'];
        $this->aryData['title'] = apply_filters('the_title',$this->aryData['title_raw']);
    }

    private function _setFormattedDate()
    {
        $this->aryData['formatted_date'] = date($this->aryOptions['date_format'],$this->aryData['timestamp']);

    }

    private function _handleMetaData()
    {
        $aryDefaults = array(
            'meta_prefix'   => $this->strPostPrefix,
            'suppress_empty'=> $this->aryOptions['suppress_empty']
        );

        if(is_array($this->aryOptions['include_meta'])){
            //they gave us an array of meta options
            $aryOptions = array_merge($aryDefaults,$this->aryOptions['include_meta']);
        } elseif(is_bool($this->aryOptions['include_meta']) && $this->aryOptions['include_meta']) {
            //they set include_meta to true
            $aryOptions = $aryDefaults;
        }

        $this->aryOriginalCustomData = get_post_custom($this->aryData['ID']);
        $this->_reformatMetaData($aryOptions);
        $this->_consolidateMetaGroups($aryOptions);

    }

    private function _processExcerpt()
    {
        //_mizzou_log($this->aryOptions['excerpt_length'],'excerpt length in the options',false,array('func'=>__FUNCTION__));
        if($this->aryData['excerpt'] == '' && $this->aryData['content_raw'] != ''){
            $this->aryData['excerpt'] = wp_trim_words($this->aryData['content_raw'],$this->aryOptions['excerpt_length']);
        }
    }
} 