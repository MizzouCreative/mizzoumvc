<?php
/**
 * Determines and stores a list of breadcrumbs to the current location in the site
 */
namespace MizzouMVC\models;
use MizzouMVC\models\Base;

/**
 * Determines and stores a list of breadcrumbs to the current location in the site
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */
class Breadcrumbs extends Base {

    /**
     * @var array final list of breadcrumbs
     */
    protected $aryCrumbs;

    /**
     * Determines and stores a list of breadcrumbs to the current location in the site
     * @param string $strPageTitle The current page title
     * @param array $aryAncestors list of ancestor pages
     * @param array $aryOptions class options. Possible options to pass in are
     *
     * inject_post_home - can be either an array that includes text and url keys=>values, or can be set to true. If set
     *    to true, the following values need to be set
     *      - inject_post_home_text
     *      - inject_post_home_url
     * home_text - the very first breadcrumb text
     * home_url - the very first breadcrumb link
     *
     */
    public function __construct($strPageTitle, $aryAncestors = array(), $aryOptions = array())
    {

        //_mizzou_log(func_get_args(),'init in breadcrumbs, all params given to us',false,array('line'=>__LINE__,'file'=>__FILE__));
        if(count($aryAncestors) > 0 ){
	        //_mizzou_log(count($aryAncestors),'aryAncestors is more than 0',false,array('line'=>__LINE__,'file'=>__FILE__));
	        $this->aryCrumbs[] = $this->_createNewMember($strPageTitle,'');
            foreach($aryAncestors as $intID => $strName){
                $this->aryCrumbs[] = $this->_createNewMember($strName,get_permalink($intID));
            }
        } elseif(is_archive() || is_single()) {
	        //_mizzou_log(null,'we\'re dealing with either an archive or single',false,array('line'=>__LINE__,'file'=>__FILE__));
            //what post type are we dealing with?
            $strPostType = get_post_type();
            $objPostType = get_post_type_object($strPostType);
            $strPostTypeName = $objPostType->labels->name;

            if('post' == $strPostType){
                $strPostTypeURL = get_permalink(get_option('page_for_posts'));
            } else {
                $strPostTypeURL = get_post_type_archive_link($strPostType);
            }

            if(is_single()){
	            //_mizzou_log(null,'we\'re dealing with a single',false,array('line'=>__LINE__,'file'=>__FILE__));
	            $this->aryCrumbs[] = $this->_createNewMember($strPageTitle,'');
                $this->aryCrumbs[] = $this->_createNewMember($strPostTypeName,$strPostTypeURL);
            } else {
	            //_mizzou_log(null,'we\'re dealing with an archive',false,array('line'=>__LINE__,'file'=>__FILE__));
                //now we've got all of the other types of archives...
                if(is_date()){
                    //set up some defaults
                    $strYear = get_the_time('Y');
                    $strYearURL = '';
                    $strMonth = get_the_time('m');
                    $strMonthURL = '';

                    switch($strDateArchiveType = $this->_determineDateArchiveType()){
                        case 'day':
                            $aryPagePath[] = $this->_createNewMember(get_the_time('d'),'');
                            $strMonthURL = get_month_link($strYear,$strMonth);
                            $strYearURL = get_year_link($strYear);
                            //pass-through done intentionally
                        case 'month':
                            /**
                             * $strMonth is the numeric representation of our month (e.g. 06), but we'll want it in text
                             * format
                             */
                            $objDate = DateTime::createFromFormat('!m',$strMonth);
                            $this->aryCrumbs[] = $this->_createNewMember($objDate->format('F'),$strMonthURL);
                            //if we started with month, we wont have year set yet
                            if('' == $strYearURL) $strYearURL = get_year_link($strYear);
							//pass-through done intentionally
                        case 'year':
                            $this->aryCrumbs[] = $this->_createNewMember($strYear,$strYearURL);
                            break;
                        default:
                            _mizzou_log($strDateArchiveType,'we are in a date archive, but it failed day, month and year checks',false,array('func'=>__FUNCTION__));
                            break;
                    }
                } elseif(is_tax() || is_tag() || is_category()){
	                //_mizzou_log(null,'we\'re dealing with a taxonomy of some sorts',false,array('line'=>__LINE__,'file'=>__FILE__));
                    //we SHOULD have both term and taxonomy, but lets make sure
                    if('' != $strTerm = get_query_var('term') && '' != $strTaxonomy = get_query_var('taxonomy') ){
                        //now lets get our taxonomy object and our term object
                        /**
                         * Wait, why do we need the tax object? we aren't doing anything with it, are we?
                         */
                        //$objTaxonomy = get_taxonomy($strTaxonomy);
                        if(false !== $objTerm = get_term_by('slug',$strTerm,$strTaxonomy)){
                            $this->aryCrumbs[] = $this->_createNewMember($objTerm->name,'');
                        } else {
                            _mizzou_log($strTerm,'despite having the term and the taxonomy, get_term_by came back false. Here is the term.',false, array('line'=>__LINE__,'file'=>__FILE__));
                            _mizzou_log($strTaxonomy,'get_term_by came back false. Here is the taxonomy.',false, array('line'=>__LINE__,'file'=>__FILE__));
                        }
                    } else {
                        global $wp_query;
                        _mizzou_log($wp_query,'we\'re in a taxonomy archive of some type, but both term and taxonomy came back empty. Here is wp_query',false,array('line'=>__LINE__,'file'=>__FILE__));
                    }

                }

                $this->aryCrumbs[] = $this->_createNewMember($strPostTypeName,$strPostTypeURL);
            }

        } elseif( !is_front_page() && (is_page() || is_home())) {
	        //_mizzou_log(null,'we\'re dealing with a page that has no ancestors',false,array('line'=>__LINE__,'file'=>__FILE__));
            // this would be a page with no ancestors, or is a home page when there is a separate front page
            $this->aryCrumbs[] = $this->_createNewMember($strPageTitle,'');
        } else {
            //any other situations?
        }

        //ok, we either have an array of items we need to inject between home and the regular breadcrumbs, or we have been given just text and a URL
        if((isset($aryOptions['inject_post_home']) && is_array($aryOptions['inject_post_home'])) || isset($aryOptions['inject_post_home_text'])){
            //if we have an array, lets set that, otherwise start with empty
            $aryInject = (isset($aryOptions['inject_post_home']) && is_array($aryOptions['inject_post_home'])) ? $aryOptions['inject_post_home'] : array();
            //if we have an empty array, then we probably have just been given text and url
            if(count($aryInject) == 0 && isset($aryOptions['inject_post_home_text'])){
                $strInjectURL = (isset($aryOptions['inject_post_home_url'])) ? $aryOptions['inject_post_home_url'] : '';
                $aryInject[] = array('text'=>$aryOptions['inject_post_home_text'],'url'=>$strInjectURL);
            }

            foreach($aryInject as $aryCrumbInject){
                $this->aryCrumbs[] = $this->_createNewMember($aryCrumbInject['text'],$aryCrumbInject['url']);
            }
        }

        //this is where we need to add the site url and site name?
        $strHomeText = (isset($aryOptions['home_text'])) ? $aryOptions['home_text'] : 'Home';
        $strHomeUrl = (isset($aryOptions['home_url'])) ? $aryOptions['home_url'] : "/";
        $this->aryCrumbs[] = $this->_createNewMember($strHomeText,$strHomeUrl);
        //and last, assign our internal to crumbs
	    //_mizzou_log($this->aryCrumbs,'array of crumbs NOT reversed',false,array('line'=>__LINE__,'file'=>__FILE__));
        $this->add_data('crumbs',array_reverse($this->aryCrumbs));
    }

    /**
     * Creates a new stdClass object with ->name and ->url properties
     * @param string $strName Name to be included as breadcrumb link text
     * @param string $strURL URL to use as href in breadcrumb link
     * @return stdClass
     */
    protected function _createNewMember($strName,$strURL)
    {
        $objMember = new stdClass();
        $objMember->name = $strName;
        $objMember->URL = $strURL;

        return $objMember;
    }

    /**
     * Determine which type of archive is being requested
     * @return string
     */
    protected function _determineDateArchiveType()
    {
        $strDateArchiveType = '';

        if(is_day()){
            $strDateArchiveType = 'day';
        } elseif(is_month()){
            $strDateArchiveType = 'month';
        } elseif(is_year()){
            $strDateArchiveType = 'year';
        }

        return $strDateArchiveType;
    }
}