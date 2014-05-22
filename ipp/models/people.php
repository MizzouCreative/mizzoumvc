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

    protected $aryTopStaffIds = array();

    public function retrieveAllStaff($boolTopStaff=false)
    {
        $aryReturn = array();
        $aryArgs = array(
            'taxonomy'          =>'person_type',
            'tax_term'          =>'staff',
            'order_by'          =>'meta_value',
            'order_direction'   => 'ASC',
            'passthru'=>array(
                'meta_key'=>$this->strPostPrefix.'lastName'
            )
        );
        if($boolTopStaff){
            $aryReturn = $this->retrieveTopStaff();

            $aryArgs['passthru'] = array_merge($aryArgs['passthru'],array('post__not_in'=>$this->aryTopStaffIds));
        }

        $aryOtherStaff = $this->retrieveContent($aryArgs);
        _mizzou_log($aryOtherStaff,'all our other staff');

        return array_merge($aryReturn,$aryOtherStaff);

    }

    public function retrieveStaff($aryArgs)
    {

    }

    public function retrieveTopStaff()
    {
        $aryReturn = array();

        foreach($this->aryTopStaff as $strTitle){
            $aryMeta = array(
                'key'   => $this->strPostPrefix.'title1',
                'value' => $strTitle
            );

            $aryArgs = array(
                'taxonomy'      => 'person_type',
                'tax_term'      => 'staff',
                'complex_meta'  => array($aryMeta)
            );

            $aryResult = $this->retrieveContent($aryArgs);
            if(count($aryResult) == 1){ //there should be only one highlander
                $this->aryTopStaffIds[] = $aryResult[0]->ID;
                $aryReturn[] = $aryResult[0];
            }
        }

        return $aryReturn;

    }
}