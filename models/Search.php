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

    protected $arySearchParams = array();
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

        if(isset($aryData['arySearchParams'])){
            $this->arySearchParams = $aryData['arySearchParams'];
        }

        $this->add_data('SearchParams',$this->_prepQueryParams());
    }

    public function getSearchResults()
    {
        /**
         * If they didnt give us anything to search for then no need to do anything
         */
        if($this->aryData['SearchTerms'] != ''){
            if(false !== $strSearchResults = file_get_contents($this->_prepQueryString())){
                return $strSearchResults;
            } else {
                $this->add_error('Search attempt failed');
                return 'There was an error performing your search';
            }
        } else {
            return '';
        }
    }

    protected function _prepQueryString()
    {
        $strFullURL = $this->aryInternalData['objSite']->option('search_url').http_build_query($this->SearchParams);
        return $strFullURL;
    }

    protected function _prepQueryParams()
    {
        /*
         * let's copy the default params we need (collection/site, front_end, etc) from our site options into our storage
         * array that we'll pass back
         */
        $arySearchParams = $this->aryInternalData['objSite']->search_parameters;


        //did they use s or q?
        $arySearchParams['q'] = (isset($this->arySearchParams['q'])) ? $this->arySearchParams['q'] : $this->arySearchParams['s'];

        $arySearchParams['q'] = $this->_prepSearchTerms($arySearchParams['q']);

        // are they asking for a page of the search results?
        if ( isset($this->arySearchParams['start']) && $this->arySearchParams['start'] != '' && is_numeric($this->arySearchParams['start'])) {
            $arySearchParams['start']  = $this->arySearchParams['start'];
        }

        // are they asking for a sort method?
        if ( isset($this->arySearchParams['sort']) && $this->arySearchParams['sort'] != ''){
            $arySearchParams['sort']   = $this->arySearchParams['sort'];
        }

        // are they requesting duplicate results be filtered/not filtered?
        if ( isset($this->arySearchParams['filter']) && $this->arySearchParams['filter']  != '' && is_numeric($this->arySearchParams['filter'])){
            $arySearchParams['filter'] = $this->arySearchParams['filter'];
        }

        return $arySearchParams;
    }

    protected function _prepSearchTerms($mxdSearchTerms)
    {

        $strSearchTerms = (is_array($mxdSearchTerms)) ? implode(' ',$mxdSearchTerms) : $mxdSearchTerms;

        $strSearchTerms = stripcslashes($strSearchTerms);

        /**
         * @todo we need to store the prepared search terms for later use/retrieval. Should the action of storing them
         * be here in this function since it is handling the prep of the search terms, or should it be handled by the
         * calling function once we return the prepared terms?  It doesn't make sense to store them AND return them.
         */
        $this->add_data('SearchTerms',$strSearchTerms);

        return $strSearchTerms;
    }
}