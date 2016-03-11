<?php
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * Controller for a static page
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category controller
 * @uses MizzouMVC\models\MizzouPost
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
class Page extends Main
{
    /**
     * Workload function
     * @return void
     */
    public function main()
	{
		global $post;
		$this->aryRenderData['MainPost'] = $this->load('MizzouMVC\models\MizzouPost',$post);
		$this->render('page');
	}
}

new Page();
