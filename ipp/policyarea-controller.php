<?php
/**
 * Template Name: Policy Area
 *
 * Controller file and template for policy areas
 *
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category template
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */


function mizzouRetrieveRelatedContent($aryOptions) {
    $aryDefaults = array(
        'post_type'         => 'post',
        'count'             => -1,
        'taxonomy'          => '',
        'tax_term'          => '',
        'tax_field'         => 'slug',
        'complex_tax'       => null,
        'order_by'          => 'date',
        'order_direction'   => 'DESC',
        'include_meta'      => false,
        'meta_prefix'       => ''
    );

    $aryOptions = array_merge($aryDefaults,$aryOptions);

    $aryReturn = array();

    $aryArgs = array(
        'post_type'     =>  $aryOptions['post_type'],
        'numberposts'   =>  $aryOptions['count'],
        'orderby'       =>  $aryOptions['order_by'],
        'order'         =>  $aryOptions['order_direction']
    );

    if ('' != $aryOptions['taxonomy'] && '' != $aryOptions['tax_term']){
        $aryTaxQuery = array(
            'taxonomy'  => $aryOptions['taxonomy'],
            'field'     => $aryOptions['tax_field'],
            'terms'     => $aryOptions['tax_term']
        );

        $aryArgs = array_merge($aryArgs,array('tax_query'=>array($aryTaxQuery)));
    } elseif (is_array($aryOptions['complex_tax']) && !is_null($aryOptions['complex_tax'])) {
        $aryArgs = array_merge($aryArgs,array('tax_query'=>$aryOptions['complex_tax']));
    }

    $objQuery =  new WP_Query($aryArgs);

    if (isset($objQuery->posts) && count($objQuery->posts) > 0){
        foreach($objQuery->posts as $objPost){
            $objMizzouPost = new MizzouPost($objPost);
            if($aryOptions['include_meta']){
                $objMizzouPost->meta_data = new PostMetaData($objPost->ID,$aryOptions['meta_prefix']);
            }

            $aryReturn[] = $objMizzouPost;
        }
    }

    return $aryReturn;

}

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

    return $aryMatches[0];
}

/**
 * Data needed by the view
 * default content for the page
 * 4 related projects, link to all projects
 * 1 main contact
 * 4 publications, link to all pubs
 */

$objMainPost = new MizzouPost($post);
$aryRelatedPublications = mizzouIppRetrieveRelatedPublications('education');
$aryRelatedProjects = mizzouIppRetrieveRelatedProjects('education');
$objMainContact = mizzouIppRetrieveContact('education');

get_header();
get_sidebar();
breadcrumbs(); //this is from Mizzou Breadcrumbs plugin.
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'policy-area.php';
get_footer();
