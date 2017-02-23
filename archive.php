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
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
class Archive extends Main {

    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        $objWpBase = $this->load('MizzouMVC\models\WpBase');
        $this->renderData('Posts',$objWpBase->convertPosts($this->wp_query->posts));
        $this->boolIncludePagination = true;
        $this->render('archive');
    }
}

new Archive();