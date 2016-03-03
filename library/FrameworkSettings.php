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
 *
 * @todo We have a lot of overlap here with class Base().  Can we extend that class instead of duplicating the methods?
 */
namespace MizzouMVC\library;
class FrameworkSettings {

    private static $objInstance = null;
    protected $arySettings = array();
    private $strSettingsFileName = 'framework-settings.ini';
    protected $aryData = array(
        'convert_string_booleans'   => true,
        'flatten_groups'            => true,
    );

    public function __construct()
    {
        $this->_loadOptions();
    }

    public function setting($strSetting)
    {
        $strSettingReturn = '';
        if(isset($this->arySettings[$strSetting])){
            $strSettingReturn = $this->arySettings[$strSetting];
        }

        return $strSettingReturn;
    }

    /**
     * magic isset test for existence of inaccessible properties
     *
     * @param mixed $mxdProperty
     * @return boolean
     */
    public function __isset($mxdProperty){
        return isset($this->aryData[$mxdProperty]);
    }

    /**
     * Magic get to access inaccessible properties
     *
     * @param mixed $mxdProperty
     * @return mixed
     */
    public function __get($mxdProperty){
        return $this->get($mxdProperty);
    }

    /**
     * Returns a property from $this->aryData. If requested property, returns current value of $this->strDataNotFoundMessage
     *
     * @param mixed $mxdProperty
     * @return mixed
     */
    public function get($mxdProperty){
        if(isset($this->aryData[$mxdProperty])){
            return $this->aryData[$mxdProperty];
        } else {
            //return $this->strDataNotFoundMessage;
            return '';
        }
    }

    /**
     * Safely runs parse_ini_file on $strPath
     * @param $strPath *.ini location
     * @return array options loaded in from the config.ini file, or empty array if failure
     */
    protected function _loadOptionsFile($strPath)
    {
        if(!file_exists($strPath) || FALSE == $aryReturn = parse_ini_file($strPath,true)){
            $aryReturn = array();
        }

        return $aryReturn;
    }

    protected function _loadOptions()
    {
        $aryOptionsFiles = array();
        $strParentSettings = '';
        $strChildSettings = '';

        if(file_exists($strParentSettings = get_template_directory() . DIRECTORY_SEPARATOR. $this->strSettingsFileName)){
            $aryOptionsFiles[] = $strParentSettings;
        }

        if(file_exists($strChildSettings = get_stylesheet_directory() . DIRECTORY_SEPARATOR . $this->strSettingsFileName)
            && $strChildSettings != $strParentSettings
        ) {
            $aryOptionsFiles[] = $strChildSettings;
        }

        foreach($aryOptionsFiles as $strSettingsFile){
            $this->aryData = array_merge($this->aryData,$this->_loadOptionsFile($strSettingsFile));
        }


    }



}