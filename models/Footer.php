<?php
/**
 * 
 * Retrieves relevant data that needs to be displayed in the footer
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */
namespace MizzouMVC\models;
class Footer extends Subview {

	public function __construct($aryContext)
	{
		$this->_setCopyRightYear();
		$this->_setWpFooter();

	}

	/**
	 * Sets copyright (current) year
	 */
	protected function _setCopyRightYear()
	{
		$this->add_data('CopyrightYear',date('Y'));
	}

	/**
	 * Captures and stores the output of wp_footer
	 */
	protected function _setWpFooter()
	{
		$this->add_data('wpFooter',$this->_captureOutput('wp_footer'));
	}
}