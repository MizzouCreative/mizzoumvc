<?php
/**
 * Allows us to pass parameters to functions passed to add_filter and add_action
 *
 * @example add_filter('admin_menu',array(new DynamicHook(array('single'=>'News','plural'=>'News')),'mizzouChangeLabelsOnDefaultPostType'));
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category library
 * @author Paul F. Gilzow, Web Communications, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 */
class DynamicHook {
    /**
     * @var temporary storage of arguments to pass to the function
     */
    private $mxdData;
    /**
     * @var array Default options
     */
    private $aryOptions = array(
        'override'=>false,
    );

    /**
     * Stores the parameters we want to pass to the function
     * @param mixed $mxdArgs
     * @param array $aryOptions
     */
    public function __construct($mxdArgs,$aryOptions=array())
    {
        $this->mxdData = $mxdArgs;
        if(is_array($aryOptions) && count($aryOptions) > 0){
            $this->aryOptions = array_merge($this->aryOptions,$aryOptions);
        }
    }

    /**
     * Allows us to dynamically call a function with arguments that was passed to add_filter or add_action
     * @param string $strCallBack
     * @param mixed $mxdArgs
     * @return mixed
     */
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