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
        'passthru'          => null
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

        $aryArgs = array(
            'post_type'     =>  $aryOptions['post_type'],
            'numberposts'   =>  $aryOptions['count'],
            'orderby'       =>  $aryOptions['order_by'],
            'order'         =>  $aryOptions['order_direction']
        );

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

        _mizzou_log($aryArgs,'the full args before we run wp_query',true,array('func'=>__FUNCTION__,'file'=>__FILE__));

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
        foreach($aryPosts as $objPost){
            $objMizzouPost = new MizzouPost($objPost);
            if($aryOptions['include_meta']){
                $objMizzouPost->meta_data = new PostMetaData($objPost->ID,$aryOptions['meta_prefix'],$aryOptions['suppress_empty_meta']);
                if($aryOptions['include_image']){
                    $objMizzouPost->meta_data->add_data('image',$objMizzouPost->meta_data->retrieve_feature_image_data());
                }
            }
            $aryReturn[] = $objMizzouPost;
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