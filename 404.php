<?php
/**
 * In addition to serving the 404 header and notification, will automatically
 * perform a search based on the non-existant URL. Change the html structure
 * below as needed.
 */
namespace MizzouMVC\controllers;
/**
 * Template file used to render a Server 404
 *
 * In addition to serving the 404 header and notification, will automatically
 * perform a search based on the non-existant URL. Change the html structure
 * below as needed.
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category controller
 * @category framework
 * @uses \MizzouMVC\models\FourOhFour
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */
class FourOhFour extends Main {
    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        $arySearchData = array();
        $arySearchData['search_options'] = $this->objSite->search;
        $arySearchData['strRequestURI'] = $_SERVER['REQUEST_URI'];
        $obj404 = $this->load('MizzouMVC\models\FourOhFour',$arySearchData);
        //$obj404 = new Model404($this->aryRenderData);
        $this->renderData('SearchResults',$obj404->getSearchResults());
        $this->render('search');
    }
}

new FourOhFour();