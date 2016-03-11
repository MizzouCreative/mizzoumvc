<?php
/**
 * Controller file used to gather components for footer view
 */
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;
/**
 * Controller class used to gather components for header view
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category controller
 * @uses MizzouMVC\models\Footer
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */
class Footer extends Main {
    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        $this->boolLoadSurroundingViewData = false;
	    $objFooter = $this->load('MizzouMVC\models\Footer',$this->aryRenderData);
        $this->aryRenderData = array_merge($this->aryRenderData,$objFooter->getTemplateData());
    }
}
