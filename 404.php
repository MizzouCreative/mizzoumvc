<?php
/**
 * Template file used to render a Server 404
 * 
 * In addition to serving the 404 header and notification, will automatically 
 * perform a search based on the non-existant URL. Change the html structure 
 * below as needed.
 *
 * @package WordPress
 * @subpackage mizzou-news
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @uses class-customPostData
 * @author Paul F. Gilzow & Jason L Rollins, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
namespace MizzouMVC\controllers;

class FourOhFour extends Main {
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