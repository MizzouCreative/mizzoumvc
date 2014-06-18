<?php
//@todo move up higher
/**
 * Why are we using the base model here instead of the publication model?
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'WpBase.php';
//@todo move this up higher as well
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'viewOutput.php';

//do we still need wp_query here?
global $wp_query,$post;
_mizzou_log($wp_query,'WP_Query',false,array('file'=>__FILE__));
_mizzou_log($post,'post',false,array('file'=>__FILE__));
/**
 * Why are we using base instead of the Publications object?
 */
$objWpBase = new WpBase();

if('' != $intAuthorID = get_query_var('author_archive')){
    $objAuthor = new MizzouPost($intAuthorID);
    /**
     * No no no. We've got html in our controller. Get it out of here.
     * @todo move html into a view.
     */
    $strAuthorLinkPattern = '<a href="%s" title="Link to $s\'s Profile">%s</a>';
    $aryData['strPageTitle'] = 'Publications for ' . sprintf($strAuthorLinkPattern,$objAuthor->permalink,$objAuthor->title,$objAuthor->title);
}

$aryData['objMainPost'] = new MizzouPost($post);
$aryOptions = array(
    'resort'        => array('key'=>'type'),
    'include_meta'  => true,
    'meta_prefix'   => 'publication_'
);


$aryData['aryPublicationsGroup'] = $objWpBase->convertPosts($wp_query->posts,$aryOptions);


$aryPolicyTaxTerms = get_terms('policy_area');

/**
 * @todo This needs to be moved into a taxonomy model
 */
$aryData['aryPolicyAreas'] = array();
foreach($aryPolicyTaxTerms as $objTaxTerm){
    $aryData['aryPolicyAreas'][$objTaxTerm->slug] = $objTaxTerm->name;
}


//_mizzou_log(get_terms('policy_area',$aryTermOptions),'our list of terms from policy area');

/*
echo '<xmp>',var_export($wp_query,true),'</xmp>',PHP_EOL,PHP_EOL;*/

//echo '<xmp>',var_export($aryData['aryPublicationsGroup'],true),'</xmp>';

mizzouOutPutView('archive-publication',$aryData);