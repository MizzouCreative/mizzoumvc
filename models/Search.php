<?php
/**
 * Queries the GSA for search terms
 */
namespace MizzouMVC\models;
use MizzouMVC\models\Base;

/**
 * Queries the GSA for search terms
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category model
 * @category framework
 * @author Paul Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */
class Search extends Base {
    /**
     * @var array search options that are handed to us during construction
     */
    protected $arySearchParams = array();
    /**
     * @var array internal storage array
     */
    protected $aryInternalData = array();

    /**
     * Sets options and the data to be searched for
     * @param array $aryData
     */
    public function __construct($aryData)
    {
        if(!isset($aryData['search_options'])){
            // #FAIL
            $this->add_error('I need the search options');
            /**
             * @todo besides logging an error message, what else should we do?
             */
        } else {
            if(is_array($aryData['search_options'])){
                $arySearchOptions = $aryData['search_options'];
                unset($aryData['search_options']);
                $this->aryInternalData['search_url'] = $arySearchOptions['url'];
                unset($arySearchOptions['url']);
                $this->aryInternalData['search_parameters'] = $arySearchOptions;
            } else {
                $this->add_error('search options is not an array!');
            }
        }

        if(isset($aryData['arySearchParams'])){
            $this->arySearchParams = $aryData['arySearchParams'];
        }

        $this->add_data('SearchParams',$this->_prepQueryParams());
    }

	/**
	 * Retrieves the search result markup from the GSA
	 *
	 * @return string search result markup from the GSA
	 */
	public function getSearchResults()
    {
        /**
         * If they didnt give us anything to search for then no need to do anything
         */
        if($this->aryData['SearchTerms'] != ''){
            if(false !== $strSearchResults = wp_remote_fopen($this->_prepQueryString())){
                return $strSearchResults;
            } else {
                $this->add_error('Search attempt failed');
                return 'There was an error performing your search';
            }
        } else {
            return '';
        }
    }

	/**
	 * Prepares the URL to be used to query the GSA
	 *
	 * @return string Full URL to the GSA
	 */
	protected function _prepQueryString()
    {
        $strFullURL = $this->aryInternalData['search_url'].http_build_query($this->SearchParams);
        _mizzou_log($strFullURL,'full URL for searching',false,array('file'=>__FILE__,'line'=>__LINE__));
        return $strFullURL;
    }

	/**
	 * Creates an array of search parameters that will be used in the construction of the URL to query the GSA
	 *
	 * @return array query parameters
	 */
	protected function _prepQueryParams()
    {
        /*
         * let's copy the default params we need (collection/site, front_end, etc) from our site options into our storage
         * array that we'll pass back
         */
        $arySearchParams = $this->aryInternalData['search_parameters'];


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

	/**
	 * Preps the search terms
	 *
	 * @param $mxdSearchTerms search terms
	 * @return string search terms
	 */
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