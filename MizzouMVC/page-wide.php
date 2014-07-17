<?php
/**
 * Template file used to render a static page
 * 
 * Template Name: Wide Page
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template
 * @author Charlie Triplett, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>


<?php get_header(); ?>

<div class="span12">

	<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>

	<main id="main" role="main">

		<div id="content">
		
			<article role="article"> 
		
				<?php if (have_posts()) : while (have_posts()) : the_post();?>

					<?php if ( has_post_thumbnail()) { ?>
						<div class="featured-image-wrapper">
							<?php the_post_thumbnail('full'); ?>
						</div>
					<?php } // end if has_post_thumbnail ?>
					
					<header>
					    <h1 id="title" class="<?php if ( has_post_thumbnail()) { ?>featured<? } ?>"><?php the_title(); ?> <?php edit_post_link('Edit'); ?></h1>
					</header>
					
					<section role="region" aria-label="content">
						<?php the_content(); ?>

						<?php if (function_exists('query_people')) query_people(); ?>

					</section>					
					
				<?php endwhile; endif;?>
		
			</article>
					
		</div> <!-- #content -->

	</main>


<?php // If comments are open or we have at least one comment, load up the comment template.
if ( is_user_logged_in() ) {
	comments_template();
} ?>


</div> <!-- end .eight .spans -->


<?php get_footer(); ?>