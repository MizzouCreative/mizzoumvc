<?php
/**
 *  * Loads up framework-specific settings
 */
namespace MizzouMVC\library;

/**
 * Loads up framework-specific settings
 *
 * @package Wordpress
 * @subpackage MizzouMVC
 * @category framework
 * @category library
 * @author Paul F. Gilzow, Mizzou Creative, University of Missouri
 * @copyright 2016 Curators of the University of Missouri
 *
 *
 * @todo We have a lot of overlap here with class Base().  Can we extend that class instead of duplicating the methods?
 */
class FrameworkSettings {

    /**
     * @var null internal storage of our static instance
     */
    private static $objInstance = null;
    /**
     * @var array internal storage of the framework settings
     */
    protected $arySettings = array();
    /**
     * @var string framework settings file name
     */
    private $strSettingsFileName = 'framework-settings.ini';
    /**
     * @var array Framework setting defaults
     */
    protected $aryData = array(
        'convert_string_booleans'   => true,
        'flatten_groups'            => true,
    );

    /**
     * Retrieves and stores framework specific options from the framework, theme and child theme (if applicable)
     */
    public function __construct()
    {
        $this->_loadOptions();
    }

    /**
     * Retrieves a framework setting value
     * @param $strSetting setting to be retrieved
     * @return string setting
     * @deprecated ?
     */
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

    /**
     * Loads framework-settings file from child (if applicable) and parent themes
     */
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