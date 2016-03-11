<?php
namespace MizzouMVC\controllers;
use MizzouMVC\controllers\Main;

/**
* Abstract class for shortcode-specific controllers to extend.  Sets up common functionality needed when rendering
* shortcode related views.
*
* @package Wordpress
* @subpackage MizzouMVC
* @category framework
* @category library
* @author Paul F. Gilzow, Mizzou Creative, University of Missouri
* @copyright 2016 Curators of the University of Missouri
*
*/
abstract class Shortcode extends Main {

    protected $boolRunRender = true;
    protected $boolLoadSurroundingViewData = false;

    /**
     * Render a view file and return it as a string
     * @param string $stViewFileName View file to render
     * @return string|void
     */
    public function render($stViewFileName)
    {
        $strReturn = '';

        if($this->boolRunRender){
            $this->aryRenderOptions['return'] = true;
            $this->aryRenderOptions['bypass_init'] = true;
            $strReturn = parent::render($stViewFileName);
        }

        return $strReturn;
    }

	/**
	 * Merges the passed in shortcode options with the options for the shortcode from the settings page
	 *
	 * @param string $strDefaultsName settings page that contains the defaults for this shortcode
	 *
	 * @return array of shortcode options
	 */
	public function mergeOptions($strDefaultsName)
    {
        $aryArgs = array();

        if(isset($this->objSite->{$strDefaultsName}) && is_array($this->objSite->{$strDefaultsName})){
            $aryArgs = $this->objSite->{$strDefaultsName};
        }

        if(isset($this->aryRenderData['shortcode_options']) && is_array($this->aryRenderData['shortcode_options'])){
            $aryArgs = array_merge($aryArgs,$this->aryRenderData['shortcode_options']);
            unset($this->aryRenderData['shortcode_options']);
        }

        return $aryArgs;
    }
}