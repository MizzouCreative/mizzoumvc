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

namespace MizzouMVC\library;


class Loader {

    protected $strFrameworkPath = null;
    protected $strParentThemePath = null;
    protected $strChildThemePath = null;

    public function _construct($strFramework,$strParentTheme,$strChildTheme=null)
    {
        _mizzou_log(null,'loader constructor called',false,array('line'=>__LINE__,'file'=>__FILE__));
        $this->strFrameworkPath = $strFramework;
        $this->strParentThemePath = $strParentTheme;
        if(!is_null($strChildTheme) || $strParentTheme != $strChildTheme){
            $this->strChildThemePath = $strChildTheme;
        }
    }

    public function load($strClass,$aryArgs=array())
    {
        /*
         * first lets see if they gave us the file instead of the class. if so, we'll assume they named the file the same
         * as the class name
         */
        if(1 === preg_match('/\.[a-z]{1,5}$/',$strClass,$aryMatch)){
            _mizzou_log($strClass,'you gave me a file, but i need the class name',false,array('file'=>__FILE__,'line'=>__LINE__));
            $strClass = $aryMatch[1];
        }

        $aryClassParts = explode('\\',$strClass);

        $strFullPath = $this->_determinePath($aryClassParts) . end($aryClassParts) . '.php';

        if(file_exists($strFullPath)){
            require_once $strFullPath;
            if(class_exists($strClass,false)){
                if(count($aryArgs) > 0){
                    return new $strClass(extract($aryArgs,EXTR_PREFIX_INVALID));
                } else {
                    return new $strClass;
                }
            } else {
                _mizzou_log($strClass,'the class name you gave me doesnt exist. make sure the namespace is correct',false,array('line'=>__LINE__,'file'=>__FILE__));
            }
        } else {
            _mizzou_log($strClass,'the class name you gave me doesnt map to a file. file map: ' . $strFullPath,false,array('line'=>__LINE__,'file'=>__FILE__));
        }
    }

    protected function _determinePath($aryClassParts)
    {
        _mizzou_log($aryClassParts,'the class parts to the file we need',false,array('line'=>__LINE__,'file'=>__FILE__));
        $strFullPath = '';

        $strEndPiece = end($aryClassParts);

        //reset and grab the first piece
        $strFirst = reset($aryClassParts);

        if('MizzouMVC' == $strFirst){
            $strFullPath = $this->strFrameworkPath;
        } else {
            $strSecond = next($aryClassParts);
            //the first part then will be the project root. The second part COULD be the parent/child indicator, but if
            //not, then it'll be the "parent" path
            if('child' == $strSecond){
                $strFullPath = $this->strChildThemePath;
            } else {
                $strFullPath = $this->strParentThemePath;
            }
        }

        //ok, now we have our root start
        $strNext = next($aryClassParts);
        if('controllers' != $strNext){
            //ok, add the strNext, then loop through the remainder of the pieces
            while(list($intKey,$strNext) = each($aryClassParts)){
                if($strEndPiece != $strNext){
                    $strFullPath .= $strNext . DIRECTORY_SEPARATOR;
                }
            }
        }

        return $strFullPath;
    }
}