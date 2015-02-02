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
            $this->aryInternalData['GET'] = array();
        } else {
            $this->aryInternalData['GET'] = $aryData['GET'];
        }

        $this->add_data('SearchParams',$this->_prepQueryParams());
    }

    public function getSearchResults()
    {
        /**
         * If they didnt give us anything to search for then no need to do anything
         */
        if($this->SearchTems != ''){
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
        $strFullURL = $this->aryInternalData['objSite']->strSearchURL.http_build_query($this->SearchParams);
        return $strFullURL;
    }

    protected function _prepQueryParams()
    {
        $arySearchParams = $this->aryInternalData['objSite']->arySearchParams;


        //did they use s or q?
        $arySearchParams['q'] = (isset($this->aryInternalData['GET']['q'])) ? $this->aryInternalData['GET']['q'] : $this->aryInternalData['GET']['s'];

        $arySearchParams['q'] = $this->_prepSearchTerms($arySearchParams['q']);

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
        _mizzou_log($strSearchTerms,'strSearchTerms directly',false,array('file'=>__FILE__,'line'=>__LINE__));
        _mizzou_log($this->SearchTerms,'search terms from self::SearchTerms',false,array('file'=>__FILE__,'line'=>__LINE__));

        return $strSearchTerms;
    }
}