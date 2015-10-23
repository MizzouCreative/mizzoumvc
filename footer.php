<?php
/**
 * Controller for the footer of the site
 * 
 *
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
namespace MizzouMVC\controllers;

class Footer extends Main {
    public function main()
    {
        $this->boolLoadSurroundingViewData = false;
	    $objFooter = $this->load('MizzouMVC\models\Footer',$this->aryRenderData);
        $this->aryRenderData = array_merge($this->aryRenderData,$objFooter->getTemplateData);
        $this->render('footer');
    }
}

//$objFooter = new Footer((isset($aryContext) ? $aryContext : array()));
/**
$objFooter = new Footer($aryContext);
$aryData = array_merge($aryContext,$objFooter->getTemplateData());
Content::render('footer',$aryData,array('include_header'=>false,'include_footer'=>false));*/
