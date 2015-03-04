<?php
 /**
 * Template Name: Search
 * 
 * Displays search results from the GSA. Also doubles as the the template to 
 * be attached to the Search page created in wordpress.  
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */


$aryData = array();
$objSite = new Site();


/**
* Doesnt matter if s or q has been used as the search parameter, we want to use either to invoke a gsa search
*/
if ( (isset( $_GET['q'] ) && $_GET['q'] != '') || (isset($_GET['s']) && $_GET['s'] != '')) {
    require_once 'models/Search.php';
    $arySearchData = array();
    $arySearchData['arySearchParams'] = $_GET;

    $arySearchData['objSite'] = $objSite;
    $objSearch = new Search($arySearchData);

    $aryData['SearchResults'] = $objSearch->getSearchResults();
    _mizzou_log($aryData['SearchResults'],'search results',false,array('file'=>__FILE__,'line'=>__LINE__));

    if($objSearch->SearchTerms != ''){
        $aryData['SearchTerms'] = htmlentities($objSearch->SearchTerms,ENT_QUOTES,'UTF-8',false);
        $aryData['PageTitle'] = 'Search results for ' . $aryData['SearchTerms'];

    }

    if($objSearch->isError()){
        _mizzou_log($objSearch->error_messages,'error messages from objSearch',false,array('file'=>__FILE__,'line'=>__LINE__));
    }
}

$aryData['objSite'] = $objSite;
Content::render('search',$aryData);
