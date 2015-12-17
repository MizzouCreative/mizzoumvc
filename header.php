<?php
/**
 * Controller file used to gather components for header view
 *
 * Do NOT instantiate the class.  The main controller will do that
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 *
 *
 */
namespace MizzouMVC\controllers;

class Header extends Main {

	public function main()
	{
		$this->boolLoadSurroundingViewData = false;
		$objHeader = $this->load('MizzouMVC\models\Header',$this->aryRenderData);
		$this->aryRenderData = array_merge($this->aryRenderData,$objHeader->getTemplateData());
		//$this->render('header');
	}
}

//$objHeader = new Header((isset($aryContext) ? $aryContext : array()));
