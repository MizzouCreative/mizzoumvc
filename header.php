<?php
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * Controller file used to gather components for header view
 *
 * Do NOT instantiate this class.  The main controller will do that
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category controller
 * @uses MizzouMVC\models\Header
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 *
 *
 */
class Header extends Main {

    /**
     * Workload function
     * @return void
     */
    public function main()
	{
		$this->boolLoadSurroundingViewData = false;
		$objHeader = $this->load('MizzouMVC\models\Header',$this->aryRenderData);
		$this->aryRenderData = array_merge($this->aryRenderData,$objHeader->getTemplateData());
		//$this->render('header');
	}
}