<?php
 /**
 * Template Name: Search
 */
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * Displays search results from the GSA. Also doubles as the the template to
 * be attached to the Search page created in wordpress.
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
class Search extends Main
{
    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        if ( (isset( $_GET['q'] ) && $_GET['q'] != '') || (isset($_GET['s']) && $_GET['s'] != '')) {
            $arySearchData = array();
            $arySearchData['arySearchParams'] = $_GET;
            if(isset($this->objSite->search) && is_array($this->objSite->search)){
                $arySearchData['search_options'] = $this->objSite->search;
                $objSearch = $this->load('MizzouMVC\models\Search',$arySearchData);
                $this->renderData('SearchResults',$objSearch->getSearchResults());
                if('' != $objSearch->SearchTerms){
                    $this->renderData('SearchTerms',htmlentities($objSearch->SearchTerms,ENT_QUOTES,'UTF-8',false));
                    $this->renderData('PageTitle','Search Results for ' . $this->aryRenderData['SearchTerms']);
                }

                if($objSearch->isError()){
                    _mizzou_log($objSearch->error_messages,'error messages from objSearch',false,array('file'=>__FILE__,'line'=>__LINE__));
                }
            } else {
                _mizzou_log($this->objSite,'trying to do a search, but search options are missing',false,array('line'=>__LINE__,'file'=>__FILE__));
            }

        }

        $this->render('search');
    }
}

new Search();