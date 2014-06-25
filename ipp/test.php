<?php
/**
 * Template Name: Test
 */

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

<p>Results from WP Query:</p>
<code>
    <?php var_export(new WP_Query($aryOptions)); ?>
</code>
