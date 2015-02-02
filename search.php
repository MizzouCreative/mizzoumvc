<?php
 /**
 * Template Name: Search
 * 
 * Displays search results from the GSA. Also doubles as the the template to 
 * be attached to the Search page created in wordpress.  
 *
 * @package WordPress
 * @subpackage mizzou-news
 * @category theme
 * @category template
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */


$aryData = array();


/**
* Doesnt matter if s or q has been used as the search parameter, we want to use either to invoke a gsa search
*/
if ( (isset( $_GET['q'] ) && $_GET['q'] != '') || (isset($_GET['s']) && $_GET['s'] != '')) {
    require_once '../models/Search.php';
    $arySearchData = array();
    $arySearchData['GET'] = $_GET;

    $objSite = new Site();
    $arySearchData['objSite'] = $objSite;
    $objSearch = new Search($arySearchData);

    if($objSearch->strSearchTerms != ''){
        $aryData['PageTitle'] = 'Search results for ' . htmlentities($objSearch->strSearchTerms,ENT_QUOTES,'UTF-8',false);
    }

    $aryData['SearchResults'] = $objSearch->getSearchResults();
}

Content::render('search',$aryData);
