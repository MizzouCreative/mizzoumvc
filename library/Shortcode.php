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

namespace MizzouMVC\controllers;


abstract class Shortcode extends Main {

    protected $boolRunRender = true;
    protected $boolLoadSurroundingViewData = false;

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