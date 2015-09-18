<?php
/**
 * Controller for archive pages
 * 
 *
 * @subpackage MizzouMVC
 * @category theme
 * @category controller
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */
namespace MizzouMVC\controllers;

class Archive extends Main {

    public function main()
    {
        global $wp_query;
        $objWpBase = $this->load('MizzouMVC\models\WpBase');
        $this->renderData('Posts',$objWpBase->convertPosts($wp_query->posts));
        $this->aryRenderOptions['include_pagination'] = true;
        $this->render('archive');
    }
}

$objArchive = new Archive();
/**
global $wp_query;
$aryData = array();
$objWpBase = new WpBase();

$aryData['aryPosts'] = $objWpBase->convertPosts($wp_query->posts);
Content::render('archive',$aryData,array('include_pagination'=>true));
 * */
