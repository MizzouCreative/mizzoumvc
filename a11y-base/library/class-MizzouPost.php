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
        'meta_prefix'   => null
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

    public function __construct(WP_Post $objPost, $aryOptions = array())
    {
        $this->aryOptions = array_merge($this->aryOptions,$aryOptions);
        $this->_setMembers($objPost);
        if(FALSE !== $this->aryOptions['include_meta']){
            $this->_handleMetaData();
        }
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
        $intPrefixLen = strlen($this->strPrefix);
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
        $this->add_data('permalink',get_permalink($this->ID));
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
        $this->aryData['formatted_date'] = date($this->aryOptions['date_format'],strtotime($this->aryData['date']));
        $this->formatted_date = date($this->aryOptions['date_format'],strtotime($this->date));
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

    }
} 