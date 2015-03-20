<?php
/**
 * 
 *
 * @package 
 * @subpackage 
 * @since 
 * @category 
 * @category 
 * @uses 
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */

class Pagination extends Base{
    public $paged = true;

    public function __construct($aryArgs)
    {
        if(isset($aryArgs['wp_query']) && $aryArgs instanceof WP_Query){

        } else {

        }
    }
}