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



    public function __construct($strPageTitle, $aryAncestors = array(), $aryOptions = array())
    {
        $aryPagePath = array();

        if(count($aryAncestors) > 0 ){
            $aryPagePath[] = $this->_createNewMember($strPageTitle,'');
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
                $aryPagePath[] = $this->_createNewMember($strPageTitle,'');
                $aryPagePath[] = $this->_createNewMember($strPostTypeName,$strPostTypeURL);
            } else {
                //now we've got all of the other types of archives...
                if(is_date()){
                    //set up some defaults
                    $strYear = get_the_time('Y');
                    $strYearURL = '';
                    $strMonth = get_the_time('M');
                    $strMonthURL = '';

                    switch($this->_determineDateArchiveType()){
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
                            $aryPagePath[] = $this->_createNewMember($objDate->format('F'),$strMonthURL);
                            //if we started with month, we wont have year set yet
                            if('' == $strYearURL) $strYearURL = get_year_link($strYear);
                            //pass-through done intentionally
                        case 'year':
                            $aryPagePath[] = $this->_createNewMember($strYear,$strYearURL);
                            break;
                        default:
                            _mizzou_log($strDateArchiveType,'we are in a date archive, but if failed day, month and year checks',false,array('func'=>__FUNCTION__));
                            break;
                    }
                } elseif(is_tax() || is_tag() || is_category()){

                }
            }

        }
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