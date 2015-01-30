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

class Search extends Base {

    protected $aryInternalData = array();

    function __construct($aryData)
    {
        if(!isset($aryData['objSite'])){
            // #FAIL
            $this->add_error('I need objSite to get search parameter options');
            /**
             * @todo besides logging an error message, what else should we do?
             */
        } else {
            $this->aryInternalData['objSite'] = $aryData['objSite'];
        }

        if(!isset($aryData['GET'])){
            //#FAIL
            $this->add_error('I need the values from GET in order to build the correct query');
            /**
             * @todo besides logging an error message, what else should we do?
             */
            $this->aryInternalData['GET'] = array();
        } else {
            $this->aryInternalData['GET'] = $aryData['GET'];
        }


    }

    public function getSearchResults()
    {
        return file_get_contents($this->_prepQueryString());
    }

    protected function _prepQueryString()
    {
        return $this->aryInternalData['objSite']->strSearchURL.http_build_query($this->_prepQueryParams());
    }

    protected function _prepQueryParams()
    {
        $arySearchParams = $this->aryInternalData['objSite']->arySearchParams;

        //did they use s or q?
        $arySearchParams['q'] = (isset($this->aryInternalData['GET']['q'])) ? $this->aryInternalData['GET']['q'] : $this->aryInternalData['GET']['s'];
        //if it's an array, let's convert it to a string
        if(is_array($arySearchParams['q'])){
            $arySearchParams['q'] = implode (' ', $arySearchParams['q']);
        }

        // are they asking for a page of the search results?
        if ( isset($this->aryInternalData['GET']['start']) && $this->aryInternalData['GET']['start'] != '') {
            $arySearchParams['start']  = $this->aryInternalData['GET']['start'];
        }

        // are they asking for a sort method?
        if ( isset($this->aryInternalData['GET']['sort']) && $this->aryInternalData['GET']['sort'] != ''){
            $arySearchParams['sort']   = $this->aryInternalData['GET']['sort'];
        }

        // are they requesting duplicate results be filtered/not filtered?
        if ( isset($this->aryInternalData['GET']['filter']) && $this->aryInternalData['GET']['filter']  != ''){
            $arySearchParams['filter'] = $this->aryInternalData['GET']['filter'];
        }


        $arySearchParams['q'] = stripcslashes($arySearchParams['q']);

        return $arySearchParams;
    }

    protected function _prepSearchParams()
    {

    }
}