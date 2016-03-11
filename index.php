<?php
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
 * The fallback controller when routing isnt able to match another controller
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category controller
 * @uses MizzouMVC\models\MizzouPost
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */

class Index extends Main
{
    /**
     * Workload function
     * @return void
     */
    public function main()
    {
        global $post;
        $this->aryRenderData['MainPost'] = $this->load('MizzouMVC\models\MizzouPost',$post);
        $this->render('index');
    }
}

new Index();
