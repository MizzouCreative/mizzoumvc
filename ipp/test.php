<?php
/**
 * Template Name: Test
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models'.DIRECTORY_SEPARATOR.'WpBase.php';

$objPost = new WpBase();
$aryFeaturePostOptions = array(
    'count'         => 1,
    'include_meta'  => true,
    'complex_meta'  => array(
        array(
            'meta_key'      => 'post_featured',
            'meta_value'    => 'Yes'
        )
    ),
);

$aryFeaturedPosts = $objPost->retrieveContent($aryFeaturePostOptions);


$aryOptions = array (
    'post_type' => 'post',
    'posts_per_page' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' =>
        array (
              array (
                'meta_key' => 'post_featured',
                'meta_value' => 'Yes',
            ),
        ),
);
?>

<p>aryFeaturedPosts:</p>
<xmp>
    <?php var_export($aryFeaturedPosts); ?>
</xmp>

<p>Results from WP Query:</p>
<xmp>
    <?php var_export(new WP_Query($aryOptions)); ?>
</xmp>
