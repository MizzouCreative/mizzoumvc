<?php
/**
 * 
 *
 * @package 
 * @subpackage 
 * @since 
 * @category 
 * @category 
 * @uses 
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */

class Breadcrumbs extends Base {

    protected $aryCrumbs;

    public function __construct($strPageTitle, $aryAncestors = array(), $aryOptions = array())
    {

        if(count($aryAncestors) > 0 ){
            $this->aryCrumbs[] = $this->_createNewMember($strPageTitle,'');
            foreach($aryAncestors as $intID => $strName){
                $aryAncestors[] = array(
                    'name'  => $strPageTitle,
                    'URL'   => get_permalink($intID),
                );
            }
        } elseif(is_archive() || is_single()) {
            //what post type are we dealng with?
            $strPostType = get_post_type();
            $objPostType = get_post_type_object($strPostType);
            $strPostTypeName = $objPostType->labels->name;

            if('post' == $strPostType){
                $strPostTypeURL = get_permalink(get_option('page_for_posts'));
            } else {
                $strPostTypeURL = get_post_type_archive_link($strPostType);
            }

            if(is_single()){
                $this->aryCrumbs[] = $this->_createNewMember($strPageTitle,'');
                $this->aryCrumbs[] = $this->_createNewMember($strPostTypeName,$strPostTypeURL);
            } else {
                //now we've got all of the other types of archives...
                if(is_date()){
                    //set up some defaults
                    $strYear = get_the_time('Y');
                    $strYearURL = '';
                    $strMonth = get_the_time('M');
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
                            _mizzou_log($strDateArchiveType,'we are in a date archive, but if failed day, month and year checks',false,array('func'=>__FUNCTION__));
                            break;
                    }
                } elseif(is_tax() || is_tag() || is_category()){
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

        } elseif(is_page()) {
            // do we need to do anything else with pages?
        } else {
            //any other situations?
        }

        //this is where we need to add the site url and site name?
        $this->aryCrumbs[] = $this->_createNewMember('Home','/');
        //and last, assign our internal to crumbs
        $this->add_data('crumbs',array_reverse($this->aryCrumbs));
    }

    protected function _createNewMember($strName,$strURL)
    {
        $objMember = new stdClass();
        $objMember->name = $strName;
        $objMember->URL = $strURL;

        return $objMember;
    }

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