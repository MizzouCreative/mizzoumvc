<?php
/**
 * Template file used to render the Site Front Page, whether the front page 
 * displays the Blog Posts Index or a static page. The Front Page template takes 
 * precedence over the Blog Posts Index (Home) template. 
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

class Index extends Main
{
    public function main()
    {
        global $post;
        $this->aryRenderData['MainPost'] = $this->load('MizzouMVC\models\MizzouPost',$post);
        $this->render('index');
    }
}

new Index();
