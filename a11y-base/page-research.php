<?php
/**
Template Name: Publications Archive

 * Template file used to render a static page
 * 
 *
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template
 * @author Charlie Triplett, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>

<?php get_header(); ?>

<?php get_sidebar(); ?>

	<main id="main" role="main">
		
			<article role="article"> 
		
				<?php if (have_posts()) : while (have_posts()) : the_post();?>
		 		    
					<div class="span6">

						<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>

						<div id="content">
						    <h1 id="title"><?php the_title(); ?> <?php edit_post_link('Edit'); ?></h1>
	
							<section role="region" aria-label="content">
		
								<?php the_content(); ?>
			
								<?php include (TEMPLATEPATH . '/query-publications.php'); ?>
		
							</section>
						</div> <!-- end content -->					
					</div> <!-- end span6 -->
					
				<?php endwhile; endif;?>
		
			</article>
		
	
	</main>



<?php get_footer(); ?>
