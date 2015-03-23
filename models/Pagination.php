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

	protected $aryDefaults = array(
		'pagination_width'  => 5,
		'pagination_first'  => false,
		'pagination_last'   => false,
		'pagination_next'   => false,
		'pagination_prev'   => false,
	);

	protected $aryAdjacentItems = array();

	protected $aryOptions = array();

	protected $objWPQuery = null;

    public function __construct($aryArgs)
    {
        if(isset($aryArgs['wp_query']) && $aryArgs['wp_query'] instanceof WP_Query){
            _mizzou_log($aryArgs['wp_query'],'wp_query',false,array('line'=>__LINE__,'file'=>__FILE__));
			$this->objWPQuery = $aryArgs['wp_query'];
	        unset($aryArgs['wp_query']);

	        $this->aryOptions = array_merge($this->aryDefaults,$aryArgs);

            $this->add_data('OnPage',($aryArgs['wp_query']->query_vars['paged'] != 0) ? $aryArgs['wp_query']->query_vars['paged'] :1);
            $this->add_data('MaxPages',(isset($aryArgs['wp_query']->max_num_pages)) ? $aryArgs['wp_query']->max_num_pages : 1);
	        if($this->MaxPages > 1 ) $this->paged = true;
            $this->add_data('MidPoint',round($this->aryOptions['pagination_width'],0,PHP_ROUND_HALF_DOWN));

			$this->_determineLowerAndUpperLimits();



        } else {
            _mizzou_log($aryArgs,'You either didnt set wp_query, or what you gave us wasnt wp_query',false,array('line'=>__LINE__,'file'=>__FILE__));
        }
    }

	protected function _determineLowerAndUpperLimits()
	{
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

		/**
		 * If intLowerLimit is 3 or 2 we end up with uneeded ellipsis
		 */
		if($intLowerLimit <= 3) $intLowerLimit = 1;
		/**
		 * If the offset between max and upper limit is 2 or less, we end up with
		 * uneeded ellipsis
		 */
		if(($this->MaxPages - $intUpperLimit) <=2 ) $intUpperLimit = $this->MaxPages;

		//now that we've figured them all out, lets set them
		$this->add_data('LowerLimit',$intLowerLimit);
		$this->add_data('UpperLimit',$intUpperLimit);
	}

	protected function _determineNext()
	{

	}

	protected function _determinePrevious()
	{

	}

	protected function _setAdjacentItems()
	{
		$strPattern = '/^pagination_([a-z]+)/';
		foreach($this->aryOptions as $strOptionKey => $strOptionValue){
			if($strOptionKey != 'pagination_width' && false !== $strOptionValue && 1 == preg_match($strPattern,$strOptionKey,$aryMatches)){
				$this->aryAdjacentItems[] = $aryMatches[1];
			}
		}
	}

}