<?php
/**
 * Template file used to render a single post page. 
 * 
 *
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template
 * @uses class-customPostData
 * @author Charlie Triplett, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>

<?php get_header(); ?>

<?php get_sidebar(); ?>

	
	<div class="flex span7">

		<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>

		<main id="main" role="main">
		
			<div id="content">
		
				<article role="article"> <!-- Article is any syndicatable kind of content -->
			
					<?php if (have_posts()) : while (have_posts()) : the_post();?>
			 		    	
						<header>
						    <h1 id="title"><?php the_title(); ?> <?php edit_post_link('Edit'); ?></h1>
						</header>
								
						<section>
							<?php the_content(); ?>
							
						    <?php $postmeta = get_post_custom($post->ID); ?>
							
							<?php if ($postmeta['link'][0] != '') { // if there's an alternate link ?>
								<a class="clearfix post-link" href="<? echo $postmeta['link'][0]; ?>">
									<? echo $postmeta['link'][0]; ?>
								</a>
							<? } ?>
							
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
		
			</div> <!-- end content -->
		</main> 

	<?php // If comments are open or we have at least one comment, load up the comment template.
	if ( is_user_logged_in() ) {
		comments_template();
	} ?>

	</div> <!-- end span7 -->


<?php get_footer(); ?>