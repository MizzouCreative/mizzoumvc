<?php
/*
 *
 */

//assumed that /theme/helpers/paths.php has been loaded already in functions.php

/**
 * Require once the PostMetaData class
 * @todo PostMetaData class should be integrated into the MVC structure
 */
locate_template('class-PostMetaData.php',true,true);
locate_template('class-MizzouPost.php',true,true);

/**
 * Class WpBase
 *
 * @uses get_post() @see self::convertPosts()
 * @uses wp_get_attachment_url @see self::convertPosts()
 */
class WpBase
{
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
        'include_image'     => false,
        'meta_prefix'       => '',
        'suppress_empty_meta'=> false,
        'passthru'          => null,
        'format_date'       => false,
        'resort'            => false,
    );

    protected $strArchivePermalink  = '';

    public $strPostPrefix = null;

    protected $strPostType = 'post';

    public function __construct($strPostPreFix = null)
    {
        $this->_setDefaults();
        $this->_setPermalink();
        $this->setPostPrefix($strPostPreFix);
    }


    public function retrieveContent($aryOptions)
    {

        $aryOptions = array_merge($this->aryDefaults,$aryOptions);

        $aryReturn = array();

        /**
         * 20140602 PFG:
         * posts_per_page HAD been set to numberposts, but it appears that **ONLY** get_posts allows numberposts as an
         * argument value and converts it to posts_per_page before calling wp_query.
         */
        $aryArgs = array(
            'post_type'     =>  $aryOptions['post_type'],
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

        $objQuery =  new WP_Query($aryArgs);

        if (isset($objQuery->posts) && count($objQuery->posts) > 0){
                $aryReturn = $this->convertPosts($objQuery->posts,$aryOptions);

        }

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
        $aryOptions = array_merge($this->aryDefaults,$aryOptions);

        $aryReturn = array();
        /**
         * If they've set format_date to true, then what we want is a new array that contains the keys format_date
         * and date_format with their respective values. We should already have them in the larger aryOptions, but
         * we need an array with just those two keys.
         */
        $aryMizzouPostOptions = ($aryOptions['format_date']) ? array_intersect_key($aryOptions,array_flip(array('format_date','date_format'))) : array();
        foreach($aryPosts as $objPost){
            $objMizzouPost = new MizzouPost($objPost,$aryMizzouPostOptions);
            if($aryOptions['include_meta']){
                $objMizzouPost->meta_data = new PostMetaData($objPost->ID,$aryOptions['meta_prefix'],$aryOptions['suppress_empty_meta']);
                if($aryOptions['include_image']){
                    $objMizzouPost->meta_data->add_data('image',$objMizzouPost->meta_data->retrieve_feature_image_data());
                }
            }

            /**
             * Do we need to include an attachment URL?
             */
            if(isset($aryOptions['include_attachment_link'])){
                if(isset($aryOptions['include_attachment_link']['pullfrom'])
                    && isset($objMizzouPost->meta_data->{$aryOptions['include_attachment_link']['pullfrom']})
                    && is_numeric($objMizzouPost->meta_data->{$aryOptions['include_attachment_link']['pullfrom']})
                    && isset($aryOptions['include_attachment_link']['newkey'])
                    && !isset($objMizzouPost->meta_data->{$aryOptions['include_attachment_link']['newkey']})
                ){
                    $objMizzouPost->meta_data->add_data($aryOptions['include_attachment_link']['newkey'],wp_get_attachment_url($objMizzouPost->meta_data->{$aryOptions['include_attachment_link']['pullfrom']}));
                } else {
                    /**
                     * @todo something happened. what do we do?
                     */
                }
            }

            /**
             * Do we need to include a subobject?
             *
             * @todo Do/will we ever need the ability to include related objects outside this method?
             * @todo I dont like relying on get_post here...
             * @todo we are also having to assume that the pullfrom value is a member in the meta_data object, and not
             * contained somewhere else.
             */
            if(isset($aryOptions['include_object']) && is_array($aryOptions['include_object'])){
                if(isset($aryOptions['include_object']['newkey'])
                    && isset($aryOptions['include_object']['pullfrom'])
                    && isset($objMizzouPost->meta_data->{$aryOptions['include_object']['pullfrom']})
                    && is_numeric($objMizzouPost->meta_data->{$aryOptions['include_object']['pullfrom']})
                    && !isset($objMizzouPost->meta_data->{$aryOptions['include_object']['newkey']})
            ){
                    $objNew = get_post($objMizzouPost->meta_data->{$aryOptions['include_object']['pullfrom']});
                    if(!is_null($objNew)){
                        $arySubOptions = array();
                        if(isset($aryOptions['include_object']['include_meta']) && $aryOptions['include_object']['include_meta']){
                            $arySubOptions['include_meta'] = true;
                        }

                        //yes, we're calling ourself to help ourself convert ourself
                        $aryNewObjects = $this->convertPosts(array($objNew),$arySubOptions);

                        if(count($aryNewObjects) == 1){
                            $objMizzouPost->{$aryOptions['include_object']['newkey']} = $aryNewObjects[0];
                        } else {
                            /**
                             * @todo technically this should never happen. throw an exception?
                             */
                        }
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

            if(is_array($aryOptions['resort']) && isset($aryOptions['resort']['key']) && isset($objMizzouPost->meta_data->{$aryOptions['resort']['key']})){
                if(!isset($aryReturn[$objMizzouPost->meta_data->{$aryOptions['resort']['key']}])){
                    $aryReturn[$objMizzouPost->meta_data->{$aryOptions['resort']['key']}] = array();

                }

                $aryReturn[$objMizzouPost->meta_data->{$aryOptions['resort']['key']}][] = $objMizzouPost;

            } else {
                $aryReturn[] = $objMizzouPost;
            }

        }

        return $aryReturn;
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


}

?>