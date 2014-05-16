<?php
/**
 * Created by PhpStorm.
 *
 */

//pull in the base model
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'base.php';

/**
 * @param $strTerm
 * @return array
 */
function mizzouIppRetrieveRelatedPublications($strTerm)
{
    $aryArgs = array(
        'post_type' => 'publication',
        'count'     => 4, //this needs to be retrieved from config variable or theme option
        'taxonomy'  => 'policy_area',
        'tax_term'  => $strTerm
    );
    return mizzouRetrieveRelatedContent($aryArgs);
}

/**
 * @param $strTerm
 * @return array
 */
function mizzouIppRetrieveRelatedProjects($strTerm)
{
    $aryArgs = array(
        'post_type' => 'project',
        'count'     => 4, //@todo this needs to be retrieved from config variable or theme option
        'taxonomy'  => 'policy_area',
        'tax_term'  => $strTerm
    );
    return mizzouRetrieveRelatedContent($aryArgs);
}

/**
 * @param $strTerm
 * @return mixed
 */
function mizzouIppRetrieveContact($strTerm)
{
    $aryTax = array(
        'relation'  => 'AND',
        array(
            'taxonomy'  => 'policy_area',
            'field'     => 'slug',
            'terms'     => $strTerm
        ),
        array(
            'taxonomy'  => 'person_type', //defined in Mizzou People plugin
            'field'     => 'slug',
            'terms'     => 'lead-analyst' //@todo this should be moved into config or theme option
        )
    );

    $aryArgs = array(
        'post_type'     => 'person',
        'count'         => 1, //@todo this needs to be retrieved from config variable or theme option
        'complex_tax'   => $aryTax,
        'include_meta'  => true,
        'meta_prefix'   => 'person_'//@todo needs to be rerieved, not hardcoded
    );

    $aryMatches = mizzouRetrieveRelatedContent($aryArgs);

    if(count($aryMatches) !== 1) //@todo throw an exception, log it, something!
        //@todo ask if we should grab some default contact info to display?
        _mizzou_log($aryMatches,'Array Matches',false,array('func'=>__FUNCTION__));
    return $aryMatches[0];
}

function mizzouRetrievePublicationData($strTerm,&$aryData)
{
    $aryData['strPublicationArchiveURL'] = get_post_type_archive_link('publication');
    $aryData['aryRelatedPublications'] = mizzouIppRetrieveRelatedProjects($strTerm);
}

function mizzouRetrieveProjectData($strTerm,&$aryData)
{
    $aryData['strProjectArchiveURL'] = get_post_type_archive_link('project');
    $aryData['aryRelatedProjects'] = mizzouIppRetrieveRelatedProjects($strTerm);
}