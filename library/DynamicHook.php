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
    private $aryOptions = array(
        'override'=>false,
    );

    public function __construct($mxdArgs,$aryOptions=array())
    {
        $this->mxdData = $mxdArgs;
        if(is_array($aryOptions) && count($aryOptions) > 0){
            $this->aryOptions = array_merge($this->aryOptions,$aryOptions);
        }
    }

    public function __call($strCallBack,$mxdArgs)
    {
        try {
            if($this->aryOptions['override']){
                $mxdArgs = $this->mxdData;
                $this->mxdData = null;
            }

            return call_user_func($strCallBack,$mxdArgs,$this->mxdData);
        } catch (InvalidArgumentException $objError) {
            _mizzou_log($strCallBack,$objError->getMessage(),false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
        }
    }
}