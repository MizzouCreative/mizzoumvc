<?php
/**
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 *
 * ASSUMES that Base.php and Site.php classes have already been included
 *
 * @todo is this really a model?  or is it more part of the library or application?
 */
namespace MizzouMVC\models;
class Content {
    /**
     * @var array default options used by the render method
     */
    protected static $aryDefaultOptions = array(
        'include_pagination'=>false,
        'return'            =>false,
        'bypass_init'       =>false,
        'include_breadcrumbs'=>false,
    );



    /**
     * @var string
     */
    protected static $strPageTitle = '';

    /**
     * @var string
     */
    protected static $strHeaderTitle = '';

    /**
     * @var null
     */
    protected static $objPagePostType = null;

    /**
     *
     */
    protected static $objViewEngine = null;

    /**
     *
     */
    protected static $objView = null;

    protected static $intCounter = 0;
    /**
     * @param string $strInnerViewFileName
     * @param array $aryData
     * @param array $aryOptions
     * @return void
     */
    public static function render($strInnerViewFileName,$aryData, \Twig_Environment $objViewEngine, Site $objSite=null, $aryOptions=array())
    {
	    //we need a copy of the original passed in options
        $aryPassedOptions = $aryOptions;
        $aryOptions = array_merge(self::$aryDefaultOptions,$aryOptions);

        if(!$aryOptions['bypass_init']){
            if(is_null($objSite)){
                //we HAVE to Site if we arent skipping init stuff
                $strInnerViewFileName = 'framework-error';
                $aryData['PageTitle'] = 'Error Encountered';
                _mizzou_log(null,'you didnt instruct me to bypass init but you also didnt include the Site object',true,array('line'=>__LINE__,'file'=>__FILE__));
            } else {
                //edit post link
                if('' != $objSite->{'site-wide'}['include_edit_link'] && self::_mixedToBool($objSite->{'site-wide'}['include_edit_link'])){
                    if(((is_single() || is_page()) && '' != $strPostLink = get_edit_post_link())){
                        $aryData['EditLink'] = $strPostLink;
                    }
                }

                //root ancestor?
                if(!isset($aryData['RootAncestor'])){
                    $aryData['RootAncestor'] = self::_determineRootAncestor((isset($aryData['MainPost'])) ? $aryData['MainPost'] : null,$aryData['PageTitle']);
                }

                //breadcrumbs
                /**
                 * ok, we could either have the site-wide option set to true and the controller did nothing, or the site wide option could be set to true but
                 * a controller overrode to false, OR the site-wide option could be set to false, and a controller overrode to true
                 */
                if(
                    (
                        '' != $objSite->{'site-wide'}['include_breadcrumbs']
                        && self::_mixedToBool($objSite->{'site-wide'}['include_breadcrumbs'])
                        && (!isset($aryPassedOptions['include_breadcrumbs']) || $aryPassedOptions['include_breadcrumbs']))
                    ||
                    ($aryOptions['include_breadcrumbs'])
                ){
                    $aryAncestors = (isset($aryData['MainPost'])) ? $aryData['MainPost']->retrieveAncestors() : array();
                    $aryBreadcrumbOptions = (isset($objSite->breadcrumbs) && '' != $objSite->breadcrumbs) ? $objSite->breadcrumbs : array();
                    $aryData['Breadcrumbs'] = new Breadcrumbs($aryData['PageTitle'],$aryAncestors,$aryBreadcrumbOptions);
                }

                //We need to pass Site down to the view, if it isnt already in there
                if(!isset($aryData['Site'])){
                    if(isset($aryData['objSite'])){
                        $aryData['Site'] = & $aryData['objSite'];
                    } else {
                        $aryData['Site'] = $objSite;
                    }
                }
            }
        }

        /**
         * check the view name to see if we've been given the full name w/ extension, or just the file name
         */
        if(!preg_match('/\.[a-z]{2,4}/',$strInnerViewFileName)){
            /**
             * @todo Is this an option that can be configured in twig? Or can we make this a config variable higher?
             */
            $strInnerViewFileName .= '.twig';
        }

        self::$objView = $objViewEngine->loadTemplate($strInnerViewFileName);

        $strReturn = self::$objView->render($aryData);

        if(!$aryOptions['return']){
            echo $strReturn;
            $strReturn = null;
        }

        return $strReturn;

    }

    protected static function _determineRootAncestor($objMainPost=null,$strPageTitle='')
    {
        $strReturn = '';

        if(is_front_page()){
            $strReturn = "Home";
        } elseif(is_page()){
            //if it's a page, then it should have been converted into a MizzouPostObject
            if(!is_null($objMainPost)){
                $aryAncestors = $objMainPost->retrieveAncestors();
                if(count($aryAncestors) > 0){
                    $strReturn = end($aryAncestors);
                } else {
                    // it doesnt have any ancestors so it is the root
                    $strReturn = $objMainPost->title;
                }

            } else {
                //should we replicate the functionality here? or just log?
                _mizzou_log(null,'hey, youre on a page, but you didnt convert it to a MizzouPost first!',false,array('line'=>__LINE__,'file'=>basename(__FILE__),'func'=>__FUNCTION__));
                $aryAncestorIDs = get_post_ancestors(get_the_ID());
                if(count($aryAncestorIDs) > 0){
                    $intRootAncestor = end($aryAncestorIDs);
                    $strReturn = get_the_title($intRootAncestor);
                } elseif($strPageTitle != '') {
                    $strReturn = $strPageTitle;
                }
            }
        } elseif(is_tax() || is_tag() || is_category()){
			global $wp_query;
	        if(isset($wp_query->query_vars['taxonomy'])){
				$objTaxonomy = get_taxonomy($wp_query->query_vars['taxonomy']);
		        if(false !== $objTaxonomy && isset($objTaxonomy->label) && '' != $objTaxonomy->label){
			        $strReturn = $objTaxonomy->label;
		        } else {
			        $strMsg = 'trying to get the label for taxonomy '. $wp_query->query_vars['taxonomy'] .'but the label isnt set or is empty in the taxonomy object';
			        _mizzou_log($objTaxonomy,$strMsg,false,array('line'=>__LINE__,'file'=>__FILE__));
		        }
		    } else {
		        $strMsg = 'we\'re on a taxonomy archive page, yet taxonomy property isnt set in wp_query';
		        _mizzou_log($wp_query->query_vars,$strMsg,false,array('line'=>__LINE__,'file'=>__FILE__));
	        }
	    } elseif(is_404()){
            $strReturn = 'Error 404, Page Not Found';
        } else {
            //what other situations do we have besides a page and everything else?
            if(FALSE !== $strPostType = get_post_type()){
                $objPostType = get_post_type_object($strPostType);
                if(is_object($objPostType) && isset($objPostType->labels->name)) {
	                $strReturn = $objPostType->labels->name;
                } else {
					$strMsg = 'tried to get the post object for ' . $strPostType . ' but I didnt receive what I was expecting';
	                _mizzou_log($objPostType,$strMsg,false,array('line'=>__LINE__,'file'=>__FILE__));
                }
	        }
        }

        return $strReturn;
    }


    /**
     * Adjusts the labels on the default post type
     *
     * @param $strPostType
     * @todo I believe this is deprecated, and is now being handled by an extending theme
     */
    protected static function _adjustPostTypeLabels($strPostType)
    {
	    _mizzou_log(null,'deprecated function called',true,array('func'=>__FUNCTION__,'file'=>__FILE__));
        return;
	    /**
         * For the love of God, wordpress... why do you have such a hard-on for global variables????!?!#@#@!~$!@
         */
        global $wp_post_types;
	    _mizzou_log($wp_post_types[$strPostType],'looking for labels on ' . $strPostType,false,array('func'=>__FUNCTION__,'file'=>__FILE__));
        switch($strPostType){
            case 'post':
                /**
                 * @todo is there ever going to be a situation where the default post type is being used with its
                 * default labels and we DONT want to adjust the label? Or should we have a theme option here that
                 * allows us to define what the label should be for the default type?  This just seems too specific to
                 * the IPP website requirements.
                 */
                if(isset($wp_post_types[$strPostType]) && $wp_post_types[$strPostType]->labels->name == 'Posts'){
                    $wp_post_types[$strPostType]->labels->name = 'Blog';
                    $wp_post_types[$strPostType]->label = 'Blog Posts';
                }

                break;
        }

        //_mizzou_log($wp_post_types[$strPostType],'our post type after we adjusted the labels');
    }


    protected static function _mixedToBool($mxdVal)
    {
        return filter_var($mxdVal,FILTER_VALIDATE_BOOLEAN);
    }


}