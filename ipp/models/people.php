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

    protected $aryPeopleDefaults = array(
        'taxonomy'      => 'person_type',
        'tax_term'      => 'staff',
        'include_meta'  => true,
        'include_image' => true
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
            'order_by'          =>'meta_value',
            'order_direction'   => 'ASC',
            'passthru'=>array(
                'meta_key'=>$this->strPostPrefix.'lastName'
            )
        );

        $aryArgs = array_merge($this->aryDefaults,$aryArgs);

        if($boolTopStaff){
            $aryReturn = $this->retrieveTopStaff();

            $aryArgs['passthru'] = array_merge($aryArgs['passthru'],array('post__not_in'=>$this->aryTopStaffIds));
        }

        $aryOtherStaff = $this->retrieveContent($aryArgs);
        //_mizzou_log($aryOtherStaff,'all our other staff');

        return array_merge($aryReturn,$aryOtherStaff);

    }

    public function retrieveStaff($aryArgs)
    {

    }

    public function retrieveTopStaff()
    {
        $aryReturn = array();

        foreach($this->aryTopStaff as $strTitle){
            $aryArgs = array(
                'complex_meta'  => array(
                        array(
                        'key'   => $this->strPostPrefix.'title1',
                        'value' => $strTitle
                    )
                )
            );

            $aryArgs = array_merge($this->aryDefaults,$aryArgs);

            $aryResult = $this->retrieveContent($aryArgs);
            if(count($aryResult) == 1){ //there should be only one highlander
                $this->aryTopStaffIds[] = $aryResult[0]->ID;
                $aryReturn[] = $aryResult[0];
            }
        }

        return $aryReturn;

    }

    /**
     * Overload parent
     */
    protected function _setDefaults()
    {
        parent::_setDefaults();
        _mizzou_log($this->aryDefaults,'our defaults from parent',false,array('func'=>__FUNCTION__));
        $this->aryDefaults = array_merge($this->aryDefaults,$this->aryPeopleDefaults);
        _mizzou_log($this->aryDefaults,'our defaults after we merge',false,array('func'=>__FUNCTION__));
    }
}