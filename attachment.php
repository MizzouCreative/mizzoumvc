<?php
/**
 * Controller for an attachment page
 * 
 *
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
namespace MizzouMVC\controllers;

class Attachment extends Main
{
    public function main()
    {
        global $post;
        $objWpBase = $this->load('MizzouMVC\models\WpBase');
        $this->renderData('MainPost',$objWpBase->convertPost($post));

        $this->render('single');
    }
}

new Attachment();
