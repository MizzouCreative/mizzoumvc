<?php
/**
 * Template file used to render a attachment page
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

<div class="span12">

	<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>
	
	<main id="main" role="main">
		
		<article role="article"> 
	
			<?php if (have_posts()) : while (have_posts()) : the_post();?>
	 		    
			    <h1 id="title"><?php the_title(); ?> <?php edit_post_link('Edit'); ?></h1>
		
				<section role="region" aria-label="content">
	
					<?php $attachment_link = wp_get_attachment_image_src($post->ID, array(1100, 1100)); ?>
									
					<?php echo $attachment_link; ?>
					
					<?php
					    if ( wp_attachment_is_image() ) { ?>
						    
		    				<section role="img" aria-labelledby="caption"> <!-- Label for this region will be firstname lastname itself probably unnecessary and repetitive-->
		    
						        <p id="caption">
						            <?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the caption ?>
						        </p>
						        
						        <?php the_content(); // this is the description ?>
		
		    				</section>
							
						<?  } else { ?>
	    				
		    				<section role="img" aria-labelledby="portrait"> <!-- Label for this region will be firstname lastname itself probably unnecessary and repetitive-->
		
						        <p class="<?php echo $classname; ?>">
		
						        <a class="download-link" href="<?php echo wp_get_attachment_url(); ?>"><strong>Download</strong> this file: <?php the_title(); ?></a>
		
						        </p>
						        
		    				</section>
		    				
							<footer>
							<p>This file is attached to: <a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a></p>
							</footer>
		
		
	
						<? } ?>
					
					<?php frontend_delete_link(); ?>
					
					<?php endwhile; else: ?>
	
					<p>Sorry, no attachments were found.</p>
					
					<?php endif; ?>
					
					<?php if ( wp_attachment_is_image() ) { ?>
						<ol>
							<li class="previous-image"><?php previous_image_link() ?></div>
							
							<div class="next-image"> <?php next_image_link() ?> </div>
						</ol>
					<?  } ?>
				</section>
				
	
		</article>
		
	</main>
</div> <!-- end span12 -->
<?php get_footer(); ?>
