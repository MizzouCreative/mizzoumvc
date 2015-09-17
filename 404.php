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
        $this->renderData('strRequestURI',$_SERVER['REQUEST_URI']);
        $obj404 = $this->load('MizzouMVC\models\FourOhFour',$this->aryRenderData);
        //$obj404 = new Model404($this->aryRenderData);
        $this->renderData('SearchResults',$obj404->getSearchResults());
        $this->render('search');
    }
}

$objController = new FourOhFour();

/**
$aryData = array();
$aryData['strRequestURI'] = $_SERVER['REQUEST_URI'];
$aryData['objSite'] = new Site();

$obj404 = new FourOhFour($aryData);


$aryData['SearchResults'] = $obj404->getSearchResults();
_mizzou_log($obj404->isError(),'is search returning an error?',false,array('line'=>__LINE__,'file'=>__FILE__));
Content::render('search',$aryData);
*/
