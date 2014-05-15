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

/**
 * @param $strPostType
 * @param $intCount
 * @param string $strTaxonomy
 * @param string $strTaxTerm
 * @param string $strTaxField
 * @param string $strOrderBy
 * @param string $strOrderDirection
 * @param bool $boolIncludeMeta
 * @param string $strMetaPrefix
 * @return array
 */
function mizzouRetrieveRelatedContent($strPostType,$intCount=-1,$strTaxonomy='',$strTaxTerm='',$strTaxField='slug',
        $strOrderBy='date',$strOrderDirection='DESC',$boolIncludeMeta = false,$strMetaPrefix=''
) {
    $aryReturn = array();

    $aryArgs = array(
        'post_type'     =>  $strPostType,
        'numberposts'   =>  $intCount,
        'orderby'       =>  $strOrderBy,
        'order'         =>  $strOrderDirection
    );

    if('' != $strTaxonomy && '' != $strTaxTerm){
        $aryTaxQuery = array(
            'taxonomy'  => $strTaxonomy,
            'field'     => $strTaxField,
            'terms'     => $strTaxTerm
        );

        $aryArgs = array_merge($aryArgs,array('tax_query'=>array($aryTaxQuery)));
    }

    $objQuery =  new WP_Query($aryArgs);

    if (isset($objQuery->posts) && count($objQuery->posts) > 0){
        foreach($objQuery->posts as $objPost){
            $objMizzouPost = new MizzouPost($objPost);
            if($boolIncludeMeta){
                $objMizzouPost->meta_data = new PostMetaData($objPost->ID,$strMetaPrefix);
            }

            $aryReturn[] = $objMizzouPost;
        }
    }

    return $aryReturn;

}

function mizzouIppRetrieveRelatedPublications($strTerm){
    $intNumber = 4; //this needs to be retrieved from config variable or theme option
    return mizzouRetrieveRelatedContent('publication',$intNumber,'policy_area',$strTerm);
}

/**
 * Data needed by the view
 * default content for the page
 * 4 related projects, link to all projects
 * 1 main contact
 * 4 publications, link to all pubs
 */

$strPageContent = apply_filters('the_content',get_the_content());

get_header();
echo '<pre>',var_export(mizzouIppRetrieveRelatedPublications('education'),true),'</pre>';
get_footer();
