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
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
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
        $strPageTitle = 'Search Results for ';
        $strSearchTerms = '';


        if($this->objSite->search['internal']){
            if(isset($_GET['s']) && '' !== $_GET['s']){
                $this->boolIncludePagination = true;
                $objWpBase = $this->load('MizzouMVC\models\WpBase');
                if(is_search()){
                    //we're on the search results page and wordpress should already have our search results preppped
                    $mxdSearchResults = $objWpBase->convertPosts($this->wp_query->posts);
                    $strSearchTerms = get_search_query();
                } else {
                    //so we need to perform the search manually
                    $aryOptions = array(
                        'post_type' => '',//all post types. @todo should we worry about limiting to specific post types?
                        'passthru'  => array('s'=>$_GET['s']),//@todo do we need to validate this before passing? WP_QUERY (which is ultimately used by WpBase) should santize it for us
                    );
                    $strSearchTerms = $_GET['s']; //escaped before passing to the view
                    $mxdSearchResults = $objWpBase->retrieveContent($aryOptions);
                }
            } else {
                //so what now?
            }
        } else {

            if( (isset( $_GET['q'] ) && $_GET['q'] != '') || (isset($_GET['s']) && $_GET['s'] != '')){
                $arySearchData = array(
                    'search_options' => $this->objSite->search,
                    'search_parameters' => $_GET,
                );
                $arySearchData['arySearchParams'] = $_GET;
                $objSearch = $this->load('MizzouMVC\models\Search',$arySearchData);
                $mxdSearchResults = $objSearch->getSearchResults();
                if('' != $objSearch->SearchTerms){
                    $strSearchTerms = $objSearch->SearchTerms;
                }

                if($objSearch->isError()){
                    _mizzou_log($objSearch->error_messages,'error messages from objSearch',false,array('file'=>__FILE__,'line'=>__LINE__));
                }
            } else {
                /**
                 * @todo why are we resetting the page title back to what it is anyway from wordpress? rethink the logic
                 * here on how we handle page title
                 */
                $strPageTitle = 'Search';
            }
        }

        $strSearchTerms = esc_attr($strSearchTerms);

        $this->renderData('SearchResults',$mxdSearchResults);
        $this->renderData('SearchTerms',$strSearchTerms);
        $this->renderData('PageTitle',$strPageTitle . $strSearchTerms);
        $this->render('search');
    }
}

new Search();