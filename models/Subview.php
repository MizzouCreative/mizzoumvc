<?php
namespace MizzouMVC\models;
use MizzouMVC\models\Base;

/**
 * Intermediate class for those models who have overlapping functionality and whose data is used in subviews (views that
 * are included by other views. At the time of this writing, that includes, but is not limited to Header and Footer.
 * Header and Footer.
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category framework
 * @category Model
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 * @dependency objSite model
 */
class Subview extends Base {

	public function __construct($aryContext)
	{
		$this->aryData = array_merge($this->aryData,$aryContext);

		if(!isset($this->aryData['objSite'])){
			/**
			 * @todo we need to do something here since every subview needs access to objSite. Throw exception?
			 */
            _mizzou_log($this->aryData,'we need objSite, but it wasnt included!',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
		}
	}

    /**
     * Returns the data to be used by the subview
     * @return array
     */
    public function getTemplateData()
	{
		return $this->aryData;
	}
}