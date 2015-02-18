<?php
/**
 * 
 * TL;DR description 
 *
 * @package 
 * @subpackage 
 * @category 
 * @category 
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2015 Curators of the University of Missouri
 */

class Footer extends Subview {

	public function __construct($aryContext)
	{
		$this->_setCopyRightYear();
		$this->_setWpFooter();

	}

	protected function _setCopyRightYear()
	{
		$this->add_data('CopyrightYear',date('Y'));
	}

	protected function _setWpFooter()
	{
		$this->add_data('wpFooter',$this->_captureOutput('wp_footer'));
	}
}