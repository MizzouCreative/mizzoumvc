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
        /**
         * Doing a complex meta query for three titles (with two results) was taking 23.1333 seconds

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
         * */

        $strSQL = "SELECT a.post_id FROM mutspaipp_2.ipp_postmeta a, mutspaipp_2.ipp_posts b
WHERE
	a.post_id = b.ID AND
	b.post_status = 'publish'
AND

		a.meta_key = 'person_title1'
AND 	a.meta_value IN (%s);";

        $strTitleVals = "'".implode("','",$this->aryTopStaff)."'";
        $strSQL = sprintf($strSQL,$strTitleVals);

        global $wpdb;
        //$aryTopStafIDs = $wpdb->get_col($wpdb->prepare($strSQL,$strTitleVals));
        $aryTopStafIDs = $wpdb->get_results($strSQL);
        //_mizzou_log($aryTopStafIDs,'the post ids of the top staff');
        /**
         * ok, running our query above using $wpdb->prepare was taking 1.6s. Cutting out prepare and doing sprintf
         * brought that down to 0.833
         * */
        /**
         * Taking the IDs from our SQL above and looping over each one and querying took about 0.9 seconds
        */
        $aryReturn = array();
        foreach($aryTopStafIDs as $objTopStaff){
            $aryArg = array(
                'passthru'=>array('p'=>$objTopStaff->post_id)
            );
            //_mizzou_log($aryArg,'aryArg for what should be post_id ' . $objTopStaff->post_id);
            $aryResults = $this->retrieveContent($aryArg);
            $aryReturn[] = $aryResults[0];

        }

        return $aryReturn;
        /*
        $aryStaffIDs = array();
        foreach($aryTopStafIDs as $objTopStaff){
            $aryStaffIDs[] = $objTopStaff->post_id;
        }

        $aryArgs = array(
            'passthru'=>array('post__in'=>$aryStaffIDs)
        );

        return $this->retrieveContent($aryArgs);*/
    }
}