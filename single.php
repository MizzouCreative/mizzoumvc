<?php
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * Controller for a single post/custom post type
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category controller
 * @uses MizzouMVC\models\MizzouPost
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */

class Single extends Main
{
    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        global $post;
        $this->aryRenderData['MainPost'] = $this->load('MizzouMVC\models\MizzouPost',$post);
        $this->render('single');
    }
}

new Single();