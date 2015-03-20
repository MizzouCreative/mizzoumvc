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
    protected $intDefaultPaginationWidth = 5;
    public function __construct($aryArgs)
    {
        if(isset($aryArgs['wp_query']) && $aryArgs['wp_query'] instanceof WP_Query){
            _mizzou_log($aryArgs['wp_query'],'wp_query',false,array('line'=>__LINE__,'file'=>__FILE__));

            if(isset($aryArgs['pagination_width'])) $this->intDefaultPaginationWidth = $aryArgs['pagination_width'];

            $this->add_data('OnPage',($aryArgs['wp_query']->query_vars['paged'] != 0) ? $aryArgs['wp_query']->query_vars['paged'] :1);
            $this->add_data('MaxPages',(isset($aryArgs['wp_query']->max_num_pages)) ? $aryArgs['wp_query']->max_num_pages : 1);
            $this->add_data('MidPoint',round($this->intDefaultPaginationWidth,0,PHP_ROUND_HALF_DOWN));

            if($this->aryData['MaxPages'] - $this->aryData['OnPage'] < $this->aryData['MidPoint']){
                //we're close to the end, give the extra to the low end
                $intLowerLimit = (1 > $intLower = $this->OnPage - $this->intDefaultPaginationWidth + ($this->MaxPages - $this->OnPage)) ? 1 : $intLower;
                $intUpperLimit = $this->MaxPages;
            } elseif($this->OnPage - $this->MidPoint < 1 ){
                //we're near the bottom, give the extra to the top
                $intLowerLimit = 1;
                $intUpperLimit = min(($this->OnPage+$this->MidPoint+abs($this->OnPage - $this->MidPoint)),$this->MAxPages);
            } else {
                //we're in the middle somewhere
                $intLowerLimit = $this->OnPage - $this->MidPoint;
                $intUpperLimit = $this->OnPage + $this->MidPoint;
            }

        } else {
            _mizzou_log($aryArgs,'You either didnt set wp_query, or what you gave us wasnt wp_query',false,array('line'=>__LINE__,'file'=>__FILE__));
        }
    }
}