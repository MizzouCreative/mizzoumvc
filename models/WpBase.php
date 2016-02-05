<?php
/**
 * Base Model class for other models to extend
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @uses is_user_logged_in()
 * @uses comments_template()
 * @todo move function calls out of this view
 */
namespace MizzouMVC\models;
//assumed that /theme/helpers/paths.php has been loaded already in functions.php



/**
 * Class WpBase
 *
 * @uses get_post() @see self::convertPosts()
 * @uses wp_get_attachment_url @see self::convertPosts()
 */
class WpBase
{
	protected $strPostType = 'post';

	protected $aryDefaults = array(
        'post_type'         => '',
        'count'             => -1,
        'taxonomy'          => '',
        'tax_term'          => '',
        'tax_field'         => 'slug',
        'complex_tax'       => null,
        'complex_meta'      => null,
        'order_by'          => 'date',
        'order_direction'   => 'DESC',
        'include_meta'      => false,
        /**
         * per IPP meeting, images should always be available.
         * @todo come up with a way for this to more easily be altered on a global scale
         */
        'include_image'     => true,
        'meta_prefix'       => '',
        'suppress_empty_meta'=> false,
        'passthru'          => null,
        'format_date'       => false,
        'resort'            => false,
        'include_attachments'=> false,
    );

    protected $strArchivePermalink  = '';

    public $strPostPrefix = null;

    public function __construct($strPostPreFix = null)
    {
        $this->_setDefaults();
        $this->_setPermalink();
        $this->setPostPrefix($strPostPreFix);
	    $this->aryDefaults['post_type'] = $this->strPostType;
    }


    public function retrieveContent($aryOptions)
    {

        $aryOptions = array_merge($this->aryDefaults,$aryOptions);

        $aryReturn = array();

        /**
         * 20140602 PFG:
         * posts_per_page HAD been set to numberposts, but it appears that **ONLY** get_posts allows numberposts as an
         * argument value and converts it to posts_per_page before calling wp_query.
         *
         * 20160205 @todo we should consider adding a 'slug' option that maps to name and a title option that also
         * maps to name but with the value being run through sanitize_title()
         */
        $aryArgs = array(
            'post_type'     => $aryOptions['post_type'],
            'posts_per_page'   =>  $aryOptions['count'],
            'orderby'       =>  $aryOptions['order_by'],
            'order'         =>  $aryOptions['order_direction']
        );

        //_mizzou_log($aryArgs,'aryArgs, looking for the one for projects',false,array('func'=>__FUNCTION__,'line'=>__LINE__));

        if('' != $aryOptions['taxonomy'] && '' != $aryOptions['tax_term'] && !is_null($aryOptions['complex_tax']) && is_array($aryOptions['complex_tax'])){
            /**
             * whoah... we're asking for both a simple taxonomy and a complex taxonomy. that doesn't make sense. Let's
             * log a message. we will then ASSUME that because a complex taxonomy was passed in that we dont want the
             * simple taxonomy
             * @todo double-check your assumption
             * @todo throw an exception instead of logging a message?
             */
            $strLogMsg = "uh... you've requested both a complex and simple taxonomy query. That won't work. Here are "
                . "the contents of the options you gave me: ";
            _mizzou_log($aryOptions,$strLogMsg,true,array('func'=>__FUNCTION__));
            $aryOptions['taxonomy'] = $aryOptions['tax_term'] = '';
        }

        if ('' != $aryOptions['taxonomy'] && '' != $aryOptions['tax_term']){
            $aryTaxQuery = array(
                'taxonomy'  => $aryOptions['taxonomy'],
                'field'     => $aryOptions['tax_field'],
                'terms'     => $aryOptions['tax_term']
            );

            $aryArgs = array_merge($aryArgs,array('tax_query'=>array($aryTaxQuery)));
        } elseif (is_array($aryOptions['complex_tax']) && !is_null($aryOptions['complex_tax'])) {
            $aryArgs = array_merge($aryArgs,array('tax_query'=>$aryOptions['complex_tax']));
        }

        if(!is_null($aryOptions['complex_meta'])){
            $aryArgs = array_merge($aryArgs,array('meta_query'=>$aryOptions['complex_meta']));
        }

        /**
         * Every once in awhile we need to query for things that dont fit above, so we have an option to pass in other
         * WP_Query parameters directly
         */
        if(!is_null($aryOptions['passthru']) && is_array($aryOptions['passthru'])){
            $aryArgs = array_merge($aryArgs,$aryOptions['passthru']);
        }

        //_mizzou_log($aryArgs,'the full args before we run wp_query',false,array('func'=>__FUNCTION__,'file'=>__FILE__));

        $objQuery =  new \WP_Query($aryArgs);

        if (isset($objQuery->posts) && count($objQuery->posts) > 0){
            //_mizzou_log($aryOptions,'options after I ran wp_query and before I convert the posts',false,array('line'=>__LINE__,'file'=>__FILE__));
            $aryReturn = $this->convertPosts($objQuery->posts,$aryOptions);

        }
		//_mizzou_log($aryReturn,'items ill pass back to the controller',false,array('line'=>__LINE__,'file'=>__FILE__));
        return $aryReturn;
    }

    public function retrieveAll($intCount=null)
    {
        $aryArgs = $this->aryDefaults;
        if(!is_null($intCount) && is_numeric($intCount)){
            $aryArgs = array_merge($aryArgs,array('count'=>(int)$intCount));
        }

        return $this->retrieveContent($aryArgs);
    }

    /**
     * Convert Posts to our custom Post object
     *
     * @param array $aryPosts
     * @param array $aryOptions
     * @return array
     *
     */
    public function convertPosts($aryPosts,$aryOptions = array())
    {
        //_mizzou_log($aryOptions,'aryOptions given to wpBase',false,array('func'=>__FUNCTION__));
        $aryOptions = array_merge($this->aryDefaults,$aryOptions);
        //_mizzou_log($aryOptions,'aryOptions after merging with defaults',false,array('func'=>__FUNCTION__));

        $aryReturn = array();

        foreach($aryPosts as $objPost){
            $objMizzouPost = $this->convertPost($objPost,$aryOptions);
            if(is_object($objMizzouPost) && $objMizzouPost instanceof MizzouPost){
	            if(is_array($aryOptions['resort']) && isset($aryOptions['resort']['key'])){
		            if(!isset($aryOptions['resort']['method'])) $aryOptions['resort']['method'] = 'member';

		            switch($aryOptions['resort']['method']){
			            case 'taxonomy':
				            $strTaxonomyName = $aryOptions['resort']['key'];
				            //_mizzou_log($objMizzouPost,'mizzoupost object when trying to sort by taxonomy');
				            if(isset($objMizzouPost->taxonomies[$strTaxonomyName]) && is_array($objMizzouPost->taxonomies[$strTaxonomyName]->items)){
					            foreach($objMizzouPost->taxonomies[$strTaxonomyName]->items as $objTaxTerm){
						            $this->_addElementToGroupArray($aryReturn,$objTaxTerm->name,$objMizzouPost);
					            }
				            }
				            break;
			            case 'post_type':
				            _mizzou_log($objMizzouPost->ID,'going to sort this ID by post type');
				            $this->_addElementToGroupArray($aryReturn,$objMizzouPost->post_type,$objMizzouPost);
				            break;
			            case 'member':
				            /*
				 * fallthrough done intentionally
				 */
			            default:
				            if(isset($objMizzouPost->{$aryOptions['resort']['key']})){
					            $strNewKey =  $objMizzouPost->{$aryOptions['resort']['key']};
				            } else {
					            $strNewKey = 'Other';
				            }

				            $this->_addElementToGroupArray($aryReturn,$strNewKey,$objMizzouPost);
		            }
	            } else {
		            //_mizzou_log($aryOptions,'aryoptions. did we drop resorting?',false,array('line'=>__LINE__,'file'=>'wpBase'));
		            $aryReturn[$objMizzouPost->ID] = $objMizzouPost;
	            }
            }
		}

        return $aryReturn;
    }

    public function convertPost($objPost,$aryOptions = array())
    {
        if( ( is_object( $objPost ) && $objPost instanceof \WP_Post) || is_numeric($objPost) ){
	        //_mizzou_log($aryOptions,'aryOptions given to wpBase',false,array('func'=>__FUNCTION__));
	        $aryOptions = array_merge($this->aryDefaults,$aryOptions);

	        /**
	         * We need a new array of options to give to the MizzouPost object that contains
	         *  - include_meta
	         *      - meta_prefix
	         *      - suppress_empty
	         *  - include_image
	         *  - excerpt_length
	         *  - format_date
	         *  - date_format
	         *  - permalink
	         *  - include_taxonomies
	         * which are available
	         * @todo the possible options for MizzouPost is growing, so how do we allow those options to expand without
	         * having to manually match them here? We have to figure this out/
	         */
	        $aryMizzouPostOptions = array();
	        $aryMizzouPostOptions['include_image'] = $aryOptions['include_image'];
	        if(isset($aryOptions['excerpt_length'])){
		        $aryMizzouPostOptions['excerpt_length'] = $aryOptions['excerpt_length'];
	        }

	        if($aryOptions['include_meta']){
		        $aryMizzouPostOptions['include_meta'] = array(
			        'meta_prefix'   => $aryOptions['meta_prefix'],
			        'suppress_empty' => $aryOptions['suppress_empty_meta']
		        );
	        }

	        /**
	         * If they've set format_date to true, then what we want is to merge the keys format_date and date_format with
	         * their respective values into our MizzouPostOptions array. We should already have them in the larger aryOptions, but
	         * we want just those two keys.
	         */
	        if($aryOptions['format_date']){
		        $aryMizzouPostOptions = array_merge($aryMizzouPostOptions,array_intersect_key($aryOptions,array_flip(array('format_date','date_format'))));
	        }

	        if(isset($aryOptions['permalink'])){
		        $aryMizzouPostOptions['permalink'] = $aryOptions['permalink'];
	        }

	        if(isset($aryOptions['taxonomies'])){
		        $aryMizzouPostOptions['taxonomies'] = $aryOptions['taxonomies'];
	        }
//_mizzou_log($aryMizzouPostOptions,'collection of options Ill pass into MizzouPost',false,array('line'=>__LINE__,'file'=>__FILE__));
	        $objMizzouPost = $this->_instantiateNewPost($objPost,$aryMizzouPostOptions);
            
	        /**
	         * Do we need to include an attachment URL?
	         * @todo can we do something to combine this with the include_attachments area below?
	         */
	        if(isset($aryOptions['include_attachment_link'])){
		        if(isset($aryOptions['include_attachment_link']['pullfrom'])
		           && isset($objMizzouPost->{$aryOptions['include_attachment_link']['pullfrom']})
		           && is_numeric($objMizzouPost->{$aryOptions['include_attachment_link']['pullfrom']})
		           && isset($aryOptions['include_attachment_link']['newkey'])
		           && !isset($objMizzouPost->{$aryOptions['include_attachment_link']['newkey']})
		        ){
			        $objMizzouPost->add_data($aryOptions['include_attachment_link']['newkey'],wp_get_attachment_url($objMizzouPost->{$aryOptions['include_attachment_link']['pullfrom']}));
		        } else {
			        /**
			         * @todo something happened. what do we do?
			         */
		        }
	        }

	        /**
	         * Do we need to include a subobject? These should be RELATED subobjects, not things like images or attachments
	         *
	         * @todo Do/will we ever need the ability to include related objects outside this method?
	         * @todo I dont like relying on get_post here...
	         * @todo we are also having to assume that the pullfrom value is a member in the meta_data object, and not
	         * contained somewhere else.
	         */
	        if(isset($aryOptions['include_object']) && is_array($aryOptions['include_object'])){
		        if(isset($aryOptions['include_object']['newkey'])
		           && isset($aryOptions['include_object']['pullfrom'])
		           && isset($objMizzouPost->{$aryOptions['include_object']['pullfrom']})
		           && is_numeric($objMizzouPost->{$aryOptions['include_object']['pullfrom']})
		           && !isset($objMizzouPost->{$aryOptions['include_object']['newkey']})
		        ){
			        $objNew = get_post($objMizzouPost->{$aryOptions['include_object']['pullfrom']});
			        if(!is_null($objNew)){
				        $arySubOptions = array();
				        if(isset($aryOptions['include_object']['include_meta']) && $aryOptions['include_object']['include_meta']){
					        $arySubOptions['include_meta'] = true;
				        }

				        //yes, we're calling ourself to help ourself convert ourself
				        $objMizzouPost->{$aryOptions['include_object']['newkey']} = $this->convertPost($objNew,$arySubOptions);
			        } else {
				        /**
				         * @todo something went wrong trying to get the post. What do we do here?
				         */
				        _mizzou_log($aryOptions,'we were unable to get a post. Here are the options we were working with.',false, array('func'=>__FUNCTION__));
			        }
		        } else {
			        /**
			         * @todo Something went wrong while
			         */
			        _mizzou_log($aryOptions,'well something went wrong in our checks. Here are the options we were working with',false,array('func'=>__FUNCTION__));
		        }
	        }


	        /**
	         * include_attachments can either be true, or it can be array with further options
	         */
	        if(is_array($aryOptions['include_attachments']) || (is_bool($aryOptions['include_attachments']) && $aryOptions['include_attachments'])){
		        //_mizzou_log($aryOptions,'weve been asked to retrieve attachments. here are the options that were included');
		        $aryAttachmentOptions = array(
			        'post_type'     => 'attachment',
			        'posts_per_page'=>-1,
			        'post_parent'   => $objMizzouPost->ID
		        );

		        $aryAttachments = get_posts($aryAttachmentOptions);

		        if(count($aryAttachments) > 0){
			        $aryAttachmentConvertOptions = array();
			        if(is_array($aryOptions['include_attachments']) && isset($aryOptions['include_attachments']['permalink'])){
				        $aryAttachmentConvertOptions['permalink'] = $aryOptions['include_attachments']['permalink'];
			        }
			        //_mizzou_log($aryAttachmentConvertOptions,'getting ready to convert some attachments. here are the options im passing over');
			        $objMizzouPost->add_data('attachments',$this->convertPosts($aryAttachments,$aryAttachmentConvertOptions));
		        }

	        }
//if(2446 == $objMizzouPost->ID) _mizzou_log($objMizzouPost,'just created the mizzoupost object for post 2446 and preparing to return it');
	        return $objMizzouPost;
        } else {
	        _mizzou_log($objPost,'not sure what you gave me but it isnt a WP_Post object, nor is it an id',false,array('line'=>__LINE__,'file'=>__FILE__));
	        return null;
        }
    }

    protected function _addElementToGroupArray(&$aryArray,$strNewKey,$mxdNewValue)
    {
        if(!isset($aryArray[$strNewKey])){
            $aryArray[$strNewKey] = array();
        }

        $aryArray[$strNewKey][] = $mxdNewValue;
    }

    public function setPostPrefix($strPostPrefix=null)
    {
        if(is_null($strPostPrefix)){
            $strPostPrefix = $this->strPostType.'_';
        }

        $this->strPostPrefix = $strPostPrefix;
        //now update our defaults
        $this->aryDefaults['meta_prefix'] = $this->strPostPrefix;
    }

    public function getPermalink()
    {
        return $this->strArchivePermalink;
    }

    protected function _setDefaults()
    {
        $this->aryDefaults['post_type'] = $this->strPostType;
    }

    private function _setPermalink()
    {
        $this->strArchivePermalink = get_post_type_archive_link($this->strPostType);
    }

    /**
     * Here so child classes can override it
     *
     * @param WP_Post $objPost
     * @param array $aryOptions
     * @return MizzouPost
     */
    protected function _newPostInstance($objPost,$aryOptions)
    {
        return new MizzouPost($objPost,$aryOptions);
    }

    /**
     * @param mixed $objPost WP_Post or integer post id
     * @param array $aryOptions
     * @return MizzouPost or derived child
     */
    private function _instantiateNewPost($objPost,$aryOptions)
    {
        $objNewPost = $this->_newPostInstance($objPost,$aryOptions);
        if(is_subclass_of($objNewPost,'\MizzouMVC\models\MizzouPost') || is_a($objNewPost,'\MizzouMVC\models\MizzouPost')){
            return $objNewPost;
        } else {
            $strMsg = 'object returned from self::_newPostInstance must be an instance of MizzouPost or a child instance of MizzouPost. Halting further execution.';
            _mizzou_log($objNewPost,$strMsg,false,array('line'=>__LINE__,'file'=>__FILE__,'func'=>__FUNCTION__));
            exit('check the logs');
        }
    }

}
?>