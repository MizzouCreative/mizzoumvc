<?php
/**
 * Template file used to render a single post page. 
 * 
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
namespace MizzouMVC\controllers;

class Single extends Main
{
    public function main()
    {
        global $post;
        $this->aryRenderData['MainPost'] = $this->load('MizzouMVC\models\MizzouPost',$post);
        $this->render('single');
    }
}

new Single();