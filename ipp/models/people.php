<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/19/14
 * Time: 3:38 PM
 */

//assumed that /theme/helpers/paths.php has been loaded already in functions.php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'WpBase.php';

/**
 * Class People
 * @todo add a check and method for assigning a default image in case we come across someone who didnt have one assigned
 *
 */
class People extends WpBase
{
    /**
     * @var array these are the staff that should be listed first in the all staff area, and will also need to be listed
     * in the contact area
     * @todo needs to be pulled dynamically
     */
    protected $aryTopStaff = array(
        'Director',
        'Associate Director',
        'Administrative Assistant'
    );

    protected $aryPeopleDefaults = array(
        'include_meta'  => true,
        'include_image' => true,

    );

    protected $aryStaffDefaults = array(
        'taxonomy'      => 'person_type',
        'tax_term'      => 'staff',
    );

    /**
     * @var array
     * @todo should these be contained within the calling method?
     */
    protected $aryPRSDefaults = array(
        'taxonomy'  => 'person_type',
        'tax_term'  => 'policy-research-scholars'
    );

    protected $aryGRADefaults = array(
        'taxonomy'  => 'person_type',
        'tax_term'  => 'graduate-research-assistants'
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

        $aryArgs = array_merge($this->aryDefaults,$this->aryStaffDefaults,$aryArgs);

        if($boolTopStaff){
            $aryReturn = $this->retrieveTopStaff();

            $aryArgs['passthru'] = array_merge($aryArgs['passthru'],array('post__not_in'=>$this->aryTopStaffIds));
        }

        $aryOtherStaff = $this->retrieveContent($aryArgs);
        //_mizzou_log($aryOtherStaff,'all our other staff');

        return array_merge($aryReturn,$aryOtherStaff);

    }

    public function convertStaff($mxdPost,$aryOptions = array())
    {

        $aryDefaults = array(
            'include_cv'            => false,
            'suppress_empty_meta'   => true
        );

        $aryOptions = array_merge($aryDefaults,$aryOptions);

        if($aryOptions['include_cv']){
            $aryOptions['include_attachment_link'] = array(
                'newkey'        =>'curriculumVitaeURL',
                'pullfrom'      =>'curriculumVitae',
            );
        }

        $boolReturnSingle = false;

        if(!is_array($mxdPost) && is_object($mxdPost)){
            $aryRetrieve = array($mxdPost);
            $boolReturnSingle = true;
        } else {
            $aryRetrieve = $mxdPost;
        }
        _mizzou_log($aryOptions,'our options before converting staff members',false,array('func'=>__FUNCTION__));
        $aryStaff = $this->convertPosts($aryRetrieve,$aryOptions);

        if(count($aryStaff) > 0){
            if($boolReturnSingle && count($aryStaff) == 1){
                return $aryStaff[0];
            } else {
                return $aryStaff;
            }
        } else {
            return new WP_Error('no-match','No matching staff');
        }
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

            $aryArgs = array_merge($this->aryDefaults,$this->aryStaffDefaults,$aryArgs);

            $aryResult = $this->retrieveContent($aryArgs);
            if(count($aryResult) == 1){ //there should be only one highlander
                $this->aryTopStaffIds[] = $aryResult[0]->ID;
                $aryReturn[] = $aryResult[0];
            }
        }

        return $aryReturn;

    }

    public function retrievePolicyScholars()
    {
        /**
         * @todo it appears that EVERYONE needs to be sorted in the same manner. this should be moved into the people
         * defaults, and possibly into the root People model if we are going to standardize on the field names
         */
        $aryArgs = array(
            'order_by'          =>'meta_value',
            'order_direction'   => 'ASC',
            'passthru'=>array(
                'meta_key'=>$this->strPostPrefix.'lastName'
            )
        );
        $aryOptions = array_merge($this->aryDefaults,$aryArgs,$this->aryPRSDefaults);
        return $this->retrieveContent($aryOptions);
    }

    /**
     * @return array
     * @todo with the exception of merging the GRAdefaults, this is identical to self::retrievePolicyScholars. Refactor
     */
    public function retrieveGRAs()
    {
        $aryArgs = array(
            'order_by'          =>'meta_value',
            'order_direction'   => 'ASC',
            'passthru'=>array(
                'meta_key'=>$this->strPostPrefix.'lastName'
            )
        );
        $aryOptions = array_merge($this->aryDefaults,$aryArgs,$this->aryGRADefaults);
        return $this->retrieveContent($aryOptions);
    }

    /**
     * Overload parent
     */
    protected function _setDefaults()
    {
        parent::_setDefaults();
        $this->aryDefaults = array_merge($this->aryDefaults,$this->aryPeopleDefaults);
    }
}