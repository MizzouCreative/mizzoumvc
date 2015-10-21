<?php
/**
 * Controller for a static page
 * 
 *
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
namespace MizzouMVC\controllers;

class Page extends Main
{
	function main()
	{
		global $post;
		$this->aryRenderData['MainPost'] = $this->load('MizzouMVC\models\MizzouPost',$post);
		$this->render('page');
	}
}

$objPage = new Page();
