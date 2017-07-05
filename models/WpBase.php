<?php
/**
 * Base Model class for other CPT-based models to extend. This is a replacement for /enhancement of \WP_Query
 */
namespace MizzouMVC\models;
//assumed that /theme/helpers/paths.php has been loaded already in functions.php
/**
 * Base Model class for other CPT-based models to extend. This is a replacement for /enhancement of \WP_Query
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category model
 * @author Paul Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 * @uses is_user_logged_in()
 * @todo move function calls out of this view
 * @uses get_post() @see self::convertPosts()
 * @uses wp_get_attachment_url @see self::convertPosts()
 * @todo rename to MizzouQuery since it better reflects the purpose of the class?
 */
class WpBase
{
    /**
     * @var string post type we're dealing with
     */
    protected $strPostType = 'post';

    /**
     * @var array default options for retrieval of posts
     */
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

    /**
     * @var string archive permalink for the post type
     */
    protected $strArchivePermalink  = '';

    /**
     * @var null|string the custom meta data prefix to use with this post type
     */
    public $strPostPrefix = null;

    protected $strPostModel = '';

    protected $objLoader = null;

    protected $strDefaultPostModelPattern = '/MizzouPost$/';

    /**
     * Sets up all the defaults needed when retrieving posts
     * @param string $strPostModel Namespaced model to be used for new posts. defaults to MizzouPost
     * @param object $objLoader the loader object model to used for loading Post models
     * @param string|null the post type prefix for use with meta fields
     */
    public function __construct($strPostModel = 'MizzouPost',$objLoader = null,$strPostPreFix = null)
    {
        $this->_setDefaults();
        $this->_setPermalink();
        $this->setPostPrefix($strPostPreFix);
        $this->aryDefaults['post_type'] = $this->strPostType;
        $this->_setLoaderAndModel($strPostModel,$objLoader);
    }

    protected function _setLoaderAndModel($strPostModel,$objLoader)
    {
        //_mizzou_log($strPostModel,'requested Post Model to use',false,array('line'=>__LINE__,'file'=>__FILE__));
        $this->_setPostModel($strPostModel);
        //_mizzou_log($objLoader,'loader we were given',false,array('line'=>__LINE__,'file'=>__FILE__));
        if(1 === preg_match($this->strDefaultPostModelPattern,$this->strPostModel) && is_null($objLoader)){
            //@todo warn them that they need to pass in the loader
            require_once MIZZOUMVC_ROOT_PATH . 'library' . DIRECTORY_SEPARATOR . 'Loader.php';
            /**
             * We need the path to the framework, parent path and child path
             */
            $this->objLoader = new \MizzouMVC\library\Loader(MIZZOUMVC_ROOT_PATH,get_template_directory() . DIRECTORY_SEPARATOR,get_stylesheet_directory() . DIRECTORY_SEPARATOR);
        } elseif(is_object($objLoader) && method_exists($objLoader,'load')){
            $this->objLoader = $objLoader;
        } else {
            //@todo throw an error
        }
    }

    /**
     * Checks to see if the string given to use contains a namespace (rudimentary). Also checks to see if the default was
     * used and if so adds the MizzouMVC namespace.
     *
     * @param string $strPostModel Fully qualified namespaced name of the model to use
     */
    protected function _setPostModel($strPostModel)
    {
        $strNamespacedModel = '';
        if(0 === preg_match($this->strDefaultPostModelPattern,$strPostModel)){
            if(false === strpos($strPostModel,'\\')){
                //@todo throw an error?
            } else {
                $strNamespacedModel = $strPostModel;
            }
        } elseif('MizzouPost' == $strPostModel){
            //they left the default
            $strNamespacedModel = __NAMESPACE__.'\\'.$strPostModel;
        }

        $this->strPostModel = $strNamespacedModel;
    }

    /**
     * Retrieves a collection of Custom Post objects based on options given in aryOptions
     * @param array $aryOptions
     * @return array
     */
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

    /**
     * Retrieve all posts using default options
     * @param integer|null $intCount optional.
     * @return array
     */
    public function retrieveAll($intCount=null)
    {
        $aryArgs = $this->aryDefaults;
        if(!is_null($intCount) && is_numeric($intCount)){
            $aryArgs = array_merge($aryArgs,array('count'=>(int)$intCount));
        }

        return $this->retrieveContent($aryArgs);
    }

    /**
     * Convert WP_Posts to our custom Post object \MizzouMVC\models\MizzouPost or child class
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
                            //_mizzou_log($objMizzouPost->ID,'going to sort this ID by post type');
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

    /**
     * Converts a WP_Post object to a MizzouPost (or child class of MizzouPost)
     * @param \WP_Post $objPost
     * @param array $aryOptions
     * @return \MizzouMVC\models\MizzouPost or child of
     */
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

    /**
     * Adds an item to a group
     * @param array $aryArray
     * @param string $strNewKey
     * @param mixed $mxdNewValue
     */
    protected function _addElementToGroupArray(&$aryArray,$strNewKey,$mxdNewValue)
    {
        if(!isset($aryArray[$strNewKey])){
            $aryArray[$strNewKey] = array();
        }

        $aryArray[$strNewKey][] = $mxdNewValue;
    }

    /**
     * Sets the custom meta data prefix
     * @param string|null $strPostPrefix
     */
    public function setPostPrefix($strPostPrefix=null)
    {
        if(is_null($strPostPrefix)){
            $strPostPrefix = $this->strPostType.'_';
        }

        $this->strPostPrefix = $strPostPrefix;
        //now update our defaults
        $this->aryDefaults['meta_prefix'] = $this->strPostPrefix;
    }

    /**
     * Returns the post type archive permalink
     * @return string
     */
    public function getPermalink()
    {
        return $this->strArchivePermalink;
    }

    /**
     * Sets defaults to be used in retrieval of posts
     * @return void
     */
    protected function _setDefaults()
    {
        $this->aryDefaults['post_type'] = $this->strPostType;
    }

    /**
     * Retrieves the permalink to the archive area for this post type
     * @return void
     */
    private function _setPermalink()
    {
        $this->strArchivePermalink = get_post_type_archive_link($this->strPostType);
    }

    /**
     * Creates a new instance of MizzouPost.  Here so child classes can override it
     *
     * @param WP_Post $objPost
     * @param array $aryOptions
     * @return \MizzouMVC\models\MizzouPost
     * @todo instead of having a child class overload this method, should we instead switch to using the loader class
     * and passing the namespaced class we want to use into the constructor for this class?
     */
    protected function _newPostInstance($objPost,$aryOptions)
    {
        return $this->objLoader->load($this->strPostModel,array($objPost,$aryOptions));
        //return new MizzouPost($objPost,$aryOptions);
    }

    /**
     * Creates a new instance of our custom post object
     *
     * @param mixed $objPost WP_Post or integer post id
     * @param array $aryOptions
     * @return \MizzouMVC\models\MizzouPost or child of
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