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
            _mizzou_log($aryArgs['wp_query'],'wp_query',false,array('line'=>__LINE__,'file'=>__FILE__));
        } else {
            _mizzou_log($aryArgs,'You either didnt set wp_query, or what you gave us wasnt wp_query',false,array('line'=>__LINE__,'file'=>__FILE__));
        }
    }
}