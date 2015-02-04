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

require_once 'Search.php';
class FourOhFour extends Search{

    /**
     * @var array common file extensions that we do not want to include when doing our 404 auto-search
     */
    protected  $aryIgnore = array(
                    '/',
                    '-',
                    '.php',
                    '.html',
                    '.aspx',
                    '.htm',
                    '.cfm',
                    '.asp',
                );

    public function __construct($aryData)
    {
        if(isset($aryData['strRequestURI'])){
            $this->arySearchParams['q'] = $aryData['strRequestURI'];
        }

        if(isset($aryData['aryIgnore'])){
            $this->aryIgnore = array_merge($this->aryIgnore,$aryData['aryIgnore']);
        }

        parent::__construct($aryData);
    }

    protected function _prepSearchTerms($strRequestURI)
    {
        $aryReplace = array_fill(0,count($this->aryIgnore),' ');

        //we dont care about the query string
        if(FALSE != preg_match('/^[^?]+/',$this->arySearchParams['q'],$aryMatches)){
            $strRequestURI = $aryMatches[0];
        }

        //we dont want to search for index or default if it is at the end of the request uri and has a file extension
        $strRequestURI = preg_replace('/(default|index).\w{2,4}/','',$strRequestURI);
        //now replace all of our ignore words with spaces
        $strRequestURI = trim(str_replace($this->aryIgnore,$aryReplace,$strRequestURI));

        return parent::_prepSearchTerms($strRequestURI);

    }
}