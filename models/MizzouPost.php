<?php
/**
 * Most basic post model.
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */

/**
 * This assumes that both files are in the same directory
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PostBase.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ImageData.php';

class MizzouPost extends PostBase
{
    /**
     * @var string default post type prefix
     */
    protected $strPostPrefix= 'post_';
    /**
     * @var array list of post members to exclude from truncating
     */
    protected $aryPropertyExclude = array('post_type','post_name');
    /**
     * @var array default options
     */
    protected $aryOptions = array(
        'format_date'   => false,
        'date_format'   => null,
        'suppress_empty'=> false,
        'include_meta'  => false,
        'include_image' => true,
        'excerpt_length'=> 55, //same as wordpress' default
        'permalink'      => 'page',
        'title_override'=> false,
        'taxonomies'    => false,
        'include_ancestors'=>false,
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
     * @var array taxonomy option defaults
     */
    protected $aryTaxonomyDefaults = array(
        'only_taxonomies'   => array(), //only include specific taxonomies
        'filter_url'        => false,
        'url'               => '',
        'url_pattern'       => '%s?%s=%s'

    );
    /**
     * @var string regex pattern for locating post members that should be consolidated into an array
     * ([a-zA-Z_]*)_?\d
     */
    private $strMetaGroupPattern = '([a-zA-Z]*)_?\d';

    /**
     * @var string should be the default date format, but i think this is now deprecated, due to AP Style
     * @todo deprecate/remove
     */
    private $strDefaultDateFormat = ' j, Y';

    public function __construct($mxdPost, $aryOptions = array())
    {

        parent::__construct($mxdPost);
        $this->aryOptions = array_merge($this->aryOptions,$aryOptions);

        //if we hit an error in the creation of the post object, dont do any of this
        if(!$this->isError()){
            if(false !== $this->aryOptions['taxonomies']){
                $this->_handleTaxonomyOptions();
            }

            $this->_setMembers($this->objOriginalPost);

            if(FALSE !== $this->aryOptions['include_meta']){
                $this->_handleMetaData();
            }

            //now that we're done we no longer need the original post
            unset($this->objOriginalPost);

            if($this->aryOptions['include_image']){
                $this->getFeaturedImage();
            }

            if($this->aryOptions['include_ancestors']){
                $this->_setAncestors();
            }
        }

    }

    public function getAllCustomData()
    {
        return $this->aryCustomData;
    }

    /**
     * @return string the name of the current post's parent
     */
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

    /**
     * Retrieves and possibly returns an Image object for the post's featured image
     * @param bool $boolReturn
     * @return mixed object|void
     */
    public function getFeaturedImage($boolReturn = false)
    {
        if(!isset($this->aryData['image']) && !isset($this->aryData['hasFeaturedImage'])){
            $this->add_data('hasFeaturedImage',has_post_thumbnail($this->aryData['ID']));
            if($this->aryData['hasFeaturedImage']){
                $intImageID = get_post_thumbnail_id($this->aryData['ID']);
                //_mizzou_log($intImageID,'the post has a featured image. this is its id');
                /**
                 * @todo how should we deal with this dependency?
                 */
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

    public function retrieveAncestors()
    {
        if(!isset($this->aryData['ancestors'])){
            $this->_setAncestors();
        }

        return $this->aryData['ancestors'];
    }

    public function _setAncestors()
    {
        $aryAncestors = get_post_ancestors($this->aryData['ID']);
        foreach($aryAncestors as $intAncestor){
            $aryAncestors[$intAncestor] = get_the_title($intAncestor);
        }

        $this->add_data('ancestors',$aryAncestors);
    }

    /**
     * Consolidates the Posts applicable custom data down into array collections
     * For any custom post data that is named <post-type>_<key>_#, it will consolidate the values into an array keyed
     * as <key>. Example
     *
     * person_address_1
     * person_address_2
     * person_address_3
     *
     * Will becomes an array 'address' containing the values from the three fields
     * @param array $aryOptions
     */
    protected function _consolidateMetaGroups($aryOptions)
    {
        //we need the full pattern to use including the prefix, if applicable
        $strFullPattern = $this->_buildFullMetaGroupPattern($aryOptions['meta_prefix']);
        _mizzou_log($strFullPattern,'the pattern I\'ll use to grep with',false,array('func'=>__FUNCTION__));
        //find all of the field keys that match our pattern
        //_mizzou_log(array_keys($this->aryOriginalCustomData),'keys from original custom data to grep on',false,array('line'=>__LINE__,'file'=>__FILE__));
        $aryMetaGroupKeys = preg_grep($strFullPattern,array_keys($this->aryOriginalCustomData));
        _mizzou_log($aryMetaGroupKeys,'matches i found from the grep');
        //loop through each match, pull out the group component and add it the group array
        foreach($aryMetaGroupKeys as $strKeyInGroup){
                if(1 === preg_match($strFullPattern,$strKeyInGroup,$aryMatch)){
                $strNewKey = $aryMatch[1];
                if(!isset($this->aryData[$strNewKey])){
                    $this->aryData[$strNewKey] = $this->aryCustomData[$strNewKey] = array();
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
                        if(!isset($this->aryData[$strNewKey])) $this->aryData[$strNewKey] = $this->aryCustomData[$strNewKey] = array();
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
                        $this->aryData[$strNewKey] = $this->aryCustomData[$strNewKey] = array($strTempData);
                    }
                }


                if(!$aryOptions['suppress_empty'] || ($aryOptions['suppress_empty'] && trim($this->aryOriginalCustomData[$strKeyInGroup][0]) != '')){
                    $this->aryData[$strNewKey][] = $this->aryCustomData[$strNewKey][]  = $this->aryOriginalCustomData[$strKeyInGroup][0];
                }
            }
        }
    }

    /**
     * Builds the regex pattern used by @see _consolidateMetaGroups
     * @param string $strPrefix post type prefix
     * @return string the regext pattern to use
     */
    protected function _buildFullMetaGroupPattern($strPrefix=null)
    {
        $strPattern = '';
        if(!is_null($strPrefix)){
            $strPattern = $strPrefix;
        }

        $strPattern = '/^'.$strPattern.$this->strMetaGroupPattern.'$/';

        return $strPattern;
    }

    /**
     * Copies the members from the WP_Post object into this object
     * WP_Post is final so we can't extend it
     * @param WP_Post $objPost
     * @todo how is the functionality here different from @see _cloneObject? can we consolidate these two?
     */
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

        /**
         * @todo why is this being done here directly instead of wrapped in a method?
         */
        $this->aryData['timestamp'] = strtotime($this->aryData['date']);

        $this->_setISO8601Date();

        $this->_setFormattedDate();

        if(is_array($this->aryOptions['taxonomies']) && !is_bool($this->aryOptions['taxonomies']) ){
            $this->_retrieveTaxonomies();
        }
    }

    /**
     * Clones the public members of an object to a new object
     * @param object $objObject
     * @return object stdClass
     * @todo this is very similar to _setMembers. Can we consolidate?
     * @todo should this be moved higher? PostBase, or possibly Base?  It's pretty basic functionality
     */
    private function _cloneObject($objObject)
    {
        if(is_object($objObject)){
            $objNew = new Base();
            $aryObjectMembers = get_object_vars($objObject);
            foreach($aryObjectMembers as $strMember=>$strMemberVal){
                $objNew->add_data($strMember,$strMemberVal);
            }
            return $objNew;
        } else {
            return $objObject;
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
        //_mizzou_log($aryOptions['meta_prefix'],'the meta_prefix',false,array('line'=>__LINE__,'file'=>__FILE__));
        $intPrefixLen = strlen($aryOptions['meta_prefix']);
        foreach($this->aryOriginalCustomData as $strKey=>$mxdVal){
            $boolPersonType = false;
	        /*
            if($strKey == 'person_type'){
	            $boolPersonType = true;
		        _mizzou_log(null,'ok,we\'re dealing with the person type meta data now.',false,array('line'=>__LINE__,'file'=>__FILE__));
            }*/
	        if(0 !== strpos($strKey,'_')){ //we dont need the interal custom data keys=>vals
                //is it a serialized value that we need to unserialize?
                if(is_serialized($mxdVal[0])){
	                //if($boolPersonType) _mizzou_log($mxdVal[0],'person type data is serialized. pre unserialize',false,array('line'=>__LINE__,'file'=>__FILE__));
                    $mxdVal[0] = unserialize($mxdVal[0]);
	                //if($boolPersonType) _mizzou_log($mxdVal[0],'person type data is serialized. POST unserialize',false,array('line'=>__LINE__,'file'=>__FILE__));
                }

                if(0 !== $intPrefixLen && 0 === strpos($strKey,$aryOptions['meta_prefix'])){
                    $strKey = substr($strKey,$intPrefixLen);
	                //if($boolPersonType) _mizzou_log($strKey,'our person type key post key reformat',false,array('line'=>__LINE__,'file'=>__FILE__));
                }

                if(!$aryOptions['suppress_empty']
                   || (
		                $aryOptions['suppress_empty'] && (
			                (is_string($mxdVal[0]) && '' != trim($mxdVal[0]))
			                ||
			                (is_array($mxdVal[0]) && count($mxdVal[0]) > 0)
		                )
	                   )
                    ) //end if
                {
                    $this->aryData[$strKey] = $this->aryCustomData[$strKey] = $mxdVal[0];
                }
            }
        }
    }

    /**
     * Retrieves/sets the post's permalink
     */
    private function _setPermalink()
    {
        if($this->post_type == 'attachment' && $this->aryOptions['permalink'] == 'download'){
            $strPermalink = wp_get_attachment_url($this->ID);
        } else {
            $strPermalink = get_permalink($this->ID);
        }
        $this->add_data('permalink',$strPermalink);
    }

    /**
     * Retrieves/sets the post's format
     */
    private function _setPostFormat()
    {
        $this->aryData['post_format'] = (FALSE !== $strPostFormat = get_post_format($this->aryData['ID'])) ? $strPostFormat : 'standard';
    }

    /**
     * Retrieves and sets both the raw contents member (content_raw) and the contents member (content) after running the
     * raw contents through the the_content filters.
     */
    private function _setContent()
    {
        $this->aryData['content_raw'] = $this->aryData['content'];
        $this->aryData['content'] = apply_filters('the_content',$this->aryData['content_raw']);
    }

    /**
     * Retrieves and sets both the raw title member (title_raw) and title member (title) after running the raw title
     * through the the_title filters
     */
    private function _setTitle()
    {
        $this->aryData['title_raw'] = $this->aryData['title'];
        $this->aryData['title'] = apply_filters('the_title',$this->aryData['title_raw']);
    }

    /**
     * @todo change this to use strftime
     * @todo possibly add your own date pattern parsing method with additional tokens for AP Style components
     */
    private function _setFormattedDate()
    {
        if($this->aryOptions['format_date'] && !is_null($this->aryOptions['date_format'])){
            $strFormattedDate = date($this->aryOptions['date_format'],$this->aryData['timestamp']);
        } else {
            $strFormattedDate = $this->_getAPMonth() . date($this->strDefaultDateFormat,$this->aryData['timestamp']);
        }
        $this->aryData['formatted_date'] = $strFormattedDate;

    }

    /**
     * Reformats the Month to AP style date format based on the timestamp of the post
     * @return string AP style formatted month
     * @todo this is at least the third place this *exact* same code appears.  We need to consolidate to ONE source
     */
    private function _getAPMonth()
    {
        $strMonth = date('F',$this->timestamp);
        if(strlen($strMonth) > 5){ //stoopid september... grumble, grumble
            if($strMonth == 'September'){
                $intTruncLen = 4;
            } else {
                $intTruncLen = 3;
            }

            $strMonth = substr($strMonth,0,$intTruncLen) . '.';
        }

        return $strMonth;
    }

    /**
     * Sets the iso8601 formatted date based on the timestamp of the post
     */
    private function _setISO8601Date()
    {
        $this->aryData['iso8601_date'] = date('c',$this->aryData['timestamp']);
    }

    /**
     * Retrieves the meta (custom) data associated with a post, and handles the reformatting of meta/custom data and
     * consolidation of meta/custom fields
     * @uses get_post_custom
     */
    private function _handleMetaData()
    {
        $aryDefaults = array(
            'meta_prefix'   => $this->strPostPrefix,
            'suppress_empty'=> $this->aryOptions['suppress_empty'],
            'capture_widgets'=>false,
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

        if($aryOptions['capture_widgets'] && isset($this->widget) && count($this->widget) > 0){
            $this->_captureWidgetOutput();
        }

    }

    /**
     * Handles the setting of taxonomy options
     */
    private function _handleTaxonomyOptions()
    {
        if(is_array($this->aryOptions['taxonomies'])){
            $this->aryOptions['taxonomies'] = array_merge($this->aryTaxonomyDefaults,$this->aryOptions['taxonomies']);
        } else {
            $this->aryOptions['taxonomies'] = $this->aryTaxonomyDefaults;
        }
    }

    /**
     * Captures the contents of all sidebar widgets. This assumes the post has been set up with custom fields that allows
     * the content editor to choose what widget appears on a post/page
     */
    private function _captureWidgetOutput()
    {
        $aryWidgets = array();
        foreach($this->widget as $strWidgetName){
            $aryWidgets[$strWidgetName] = $this->_captureOutput('dynamic_sidebar',array($strWidgetName));
        }

        $this->add_data('widgets',$aryWidgets);
    }

    /**
     * Retrieves and sets the excerpt member. If the excerpt isn't explicitly set in the post, use the raw content and
     * truncate. Then run everything through the get_the_excerpt filter
     * @uses wp_trim_words
     * @uses strip_shortcodes
     */
    private function _processExcerpt()
    {
        //_mizzou_log($this->aryData['excerpt'],'excerpt before running the filters',false,array('func'=>__FUNCTION__));
        if($this->aryData['excerpt'] == '' && $this->aryData['content_raw'] != ''){
            $this->aryData['excerpt'] = wp_trim_words($this->aryData['content_raw'],$this->aryOptions['excerpt_length']);
        }

        $this->aryData['excerpt'] = strip_shortcodes($this->aryData['excerpt']);
        //_mizzou_log($this->aryData['excerpt'],'excerpt AFTER running the filters',false,array('func'=>__FUNCTION__));
    }

    /**
     * Retrieve and sets the taxonomies and taxonomy terms associated with the post
     */
    protected function _retrieveTaxonomies()
    {
        $aryTaxStore = array();
        $aryTaxonomies = get_object_taxonomies($this->post_type,'objects');
        //_mizzou_log($aryTaxonomies,'taxonomies associated with ' . $this->post_type);
        if(is_array($this->aryOptions['taxonomies']['only_taxonomies']) && count($this->aryOptions['taxonomies']['only_taxonomies']) > 0){
            $aryTaxonomies = array_intersect_key($aryTaxonomies,array_flip($this->aryOptions['taxonomies']['only_taxonomies']));
        }

        //if(2446 == $this->ID) _mizzou_log($aryTaxonomies,'list of taxonomies for post ID 2446');

        foreach($aryTaxonomies as $objTaxonomy){
            $aryTaxTerms = get_the_terms($this->ID,$objTaxonomy->name);
            //if(2446 == $this->ID) _mizzou_log($aryTaxTerms,'list of tax terms for post ID 2446');
            $objTaxonomyClone = $this->_cloneObject($objTaxonomy);

            //$objTaxonomy->items = array();

            if(is_array($aryTaxTerms)){
                $aryTaxonomyTerms = array();
                foreach($aryTaxTerms as $objTaxTerm){

                    $objTaxTermClone = $this->_cloneObject($objTaxTerm);
                    /**
                     * @todo refactor this section so that if a specific URL and URL structure hasnt been passed in, we
                     * fall back to the default taxonomy URl.
                     * @todo on a similar note, if they say they want a filter_url, but dont give is a specific url_pattern,
                     * then fall back to using /post-type/?tax_name=tax_term. @see taxonomy-filter-menu.php in IPP Project
                     */
                    if(is_bool($this->aryOptions['taxonomies']['filter_url']) && $this->aryOptions['taxonomies']['filter_url']){
                        $aryURLParts = array(
                            $this->aryOptions['taxonomies']['url'],
                            $objTaxonomyClone->name,
                            $objTaxTermClone->slug
                        );
                        $objTaxTermClone->add_data('url',vsprintf($this->aryOptions['taxonomies']['url_pattern'],$aryURLParts));
                    }

	                if(is_string($strTaxPermalink = get_term_link($objTaxTerm))){
		                $objTaxTermClone->add_data('permalink',$strTaxPermalink);
	                }



                    $aryTaxonomyTerms[] = $objTaxTermClone;
                }
                $objTaxonomyClone->add_data('items',$aryTaxonomyTerms);

            } else {
                /**
                _mizzou_log($aryTaxTerms,'well, tax terms isnt an array, so what is it???');
                _mizzou_log($this->ID,'ID of the current post that doesnt have tax terms');
                _mizzou_log($objTaxonomy->name,'name of the taxonomy that supposedly doesnt have any terms associated with this post');
                 */
            }
            //if(2446 == $this->ID) _mizzou_log($objTaxonomy->items,'objTaxonomy items for post id before assigning it back to the post object');
            //if(2446 == $this->ID) _mizzou_log($objTaxonomy,'objTaxonomy for post id 2446 before assigning it back to the post object');
            $aryTaxStore[$objTaxonomyClone->label] = $objTaxonomyClone;
        }

        $this->taxonomies = $aryTaxStore;
        //if(2446 == $this->ID) _mizzou_log($this->taxonomies,'list of all taxonomies for post 2446 immediately after assignment');
    }
} 