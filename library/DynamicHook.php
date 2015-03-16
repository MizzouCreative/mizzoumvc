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

class DynamicHook {
    private $mxdData;

    public function __construct($mxdArgs)
    {
        $this->mxdData = $mxdArgs;
    }

    public function __call($strCallBack,$mxdArgs)
    {
        try {
            return call_user_func($strCallBack,$mxdArgs,$this->mxdData);
        } catch (InvalidArgumentException $objError) {
            _mizzou_log($strCallBack,$objError->getMessage(),false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
        }
    }
}