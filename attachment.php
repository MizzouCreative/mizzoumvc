<?php
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * Controller for an attachment page
 *
 *
 * @subpackage MizzouMVC
 * @category controller
 * @category framework
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
class Attachment extends Main
{
    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        global $post;
        $objWpBase = $this->load('MizzouMVC\models\WpBase');
        $this->renderData('MainPost',$objWpBase->convertPost($post));

        $this->render('single');
    }
}

new Attachment();
