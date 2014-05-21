<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/19/14
 * Time: 3:38 PM
 */

//assumed that /theme/helpers/paths.php has been loaded already in functions.php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'base.php';

class People extends WpBase
{
    /**
     * @var array these are the staff that should be listed first in the all staff area, and will also need to be listed
     * in the contact area
     */
    protected $aryTopStaff = array(
        'Director',
        'Associate Director',
        'Administrative Assistant'
    );

    /**
     * overload parent member
     * @var string
     */
    protected $strPostType = 'person';

    public function retrieveAllStaff($boolTopStaff)
    {

    }

    public function retrieveStaff($aryArgs)
    {

    }

    public function retrieveTopStaff()
    {
        $aryMeta = array('relation'=>'OR');
        foreach($this->aryTopStaff as $strStaffTitle){
            $aryMeta[] = array(
                'key'   => $this->strPostPrefix.'title1',
                'value' => $strStaffTitle
            );
        }

        $aryArgs = array(
            'count'         => 3, //@todo this needs to be retrieved from config variable or theme option
            'complex_meta'   => $aryMeta,
            'include_meta'  => true,
        );

        return $this->retrieveContent($aryArgs);
    }
}