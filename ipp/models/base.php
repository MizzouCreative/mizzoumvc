<?php
/*
 *
 */

/**
 * @param $aryOptions
 * @return array
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
?>