<?php
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * Controller for archive pages
 *
 *
 * @subpackage MizzouMVC
 * @category controller
 * @category framework
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
class Archive extends Main {

    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        global $wp_query;
        $objWpBase = $this->load('MizzouMVC\models\WpBase');
        $this->renderData('Posts',$objWpBase->convertPosts($wp_query->posts));
        $this->aryRenderOptions['include_pagination'] = true;
        $this->render('archive');
    }
}

new Archive();