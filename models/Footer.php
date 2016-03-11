<?php
/**
 * Retrieves relevant data that needs to be displayed in the footer
 */
namespace MizzouMVC\models;
use MizzouMVC\models\Subview;
/**
 *
 * Retrieves relevant data that needs to be displayed in the footer
 *
 * @package WordPress
 * @subpackage MizzouMVC
 * @category framework
 * @category model
 * @author Paul Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */
class Footer extends Subview {

    /**
     * Sets Copyright year and footer content captureed from wp_footer
     * @param array $aryContext
     */
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