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
        global $wpdb;
        $aryReturn = array();
        /**
         * Doing a complex meta query for three titles (with two results) was taking 23.1333 seconds.
         * Meta query removed
         */

        $strSQL = "SELECT a.post_id,a.meta_value FROM mutspaipp_2.ipp_postmeta a, mutspaipp_2.ipp_posts b
                    WHERE
                        a.post_id = b.ID AND
                        b.post_status = 'publish'
                    AND

                            a.meta_key = 'person_title1'
                    AND 	a.meta_value IN (%s);";

        $strTitleVals = "'".implode("','",$this->aryTopStaff)."'";
        /**
         * ok, running our query above using $wpdb->prepare was taking 1.6s. Cutting out prepare and doing sprintf
         * brought that down to 0.833
         * */
        $strSQL = sprintf($strSQL,$strTitleVals);


        $aryTopStaffIDs = $wpdb->get_results($strSQL);

        /**
         * ok, this next piece might seem confusing. Why are we looping and then running a query over each item, instead
         * of doing one query with post__in and the array of post ids?  Because, surprisingly, it's actually faster
         * to loop and query each one separately.  Using post__in took about 1.1 seconds to return the results. Looping'
         * over each one, as below, takes 0.9s.
         */
        if(is_array($aryTopStaffIDs) && count($aryTopStaffIDs) > 0){
            $aryTopStaffOrdered = array();
            foreach($aryTopStaffIDs as $objTopStaff){
                $aryTopStaffOrdered[array_search($objTopStaff->meta_value,$this->aryTopStaff)] = $objTopStaff->post_id;
            }

            ksort($aryTopStaffOrdered);
            _mizzou_log($aryTopStaffOrdered,'our ordered top staff');

            foreach($aryTopStaffOrdered as $intPostId){
                $aryArg = array(
                    'passthru'=>array('p'=>$intPostId)
                );
                //_mizzou_log($aryArg,'aryArg for what should be post_id ' . $objTopStaff->post_id);
                $aryResults = $this->retrieveContent($aryArg);
                if(isset($aryResults[0])){
                    $aryReturn[] = $aryResults[0];
                }
            }
        }

        return $aryReturn;

    }
}