<?php
/**
 *
 */
?>
<section>
    <?php echo $objMainPost->content; ?>

    <?php if($objMainPost->alternateLink != ''): ?>
        <a class="clearfix post-link" href="<?php echo $objMainPost->alternateLink; ?>"><?php echo $objMainPost->alternateLink; ?></a>
    <?php endif; ?>
</section>

<footer>
    <p class="postmetadata">Published
        <time datetime="<?php the_time('c'); // ISO 8601 ?>" pubdate>
            <?php the_time('l, F jS	, Y'); ?>
        </time>
    </p>
</footer>

<?php endwhile; endif;?>

</article>

<?php
$categories = wp_get_post_categories($post->ID);
if ($categories) {

    $first_category = $categories[0];

    $related_post_args=array(
        'cat' => $first_category, //cat__not_in wouldn't work
        'post__not_in' => array($post->ID),
        'showposts'=>5,
        'caller_get_posts'=>1
    );

    $related_posts = new WP_Query($related_post_args);

    if( $related_posts->have_posts() ) { ?>

        <ol class="skip-links">
            <li><a class="hidden skip-to-content" href="#main"><span class="text">Skip to content</span></a></li>
        </ol>

        <h3>Related News</h3>
        <ul>
            <? while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                <li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
            <?php endwhile; ?>
        </ul>
    <?php } //if ($my_query)
} //if ($categories)
wp_reset_query();  // Restore global post data stomped by the_post().
?>