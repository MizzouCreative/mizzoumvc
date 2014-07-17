<?php
/**
Template Name: Author List

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
		 		    
					<div class="span7">

						<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>
						
						<div id="content">
						    <h1 id="title"><?php the_title(); ?> <?php edit_post_link('Edit'); ?></h1>
	
							<section role="region" aria-label="content">
		
								<?php the_content(); ?>
								
								<?php $person_args = array(
											'post_type' => 'person',
											'post_status'=>'publish',
											'meta_key' => 'last_name',
											'orderby' => 'meta_value name',
											'order' => 'ASC',
											'posts_per_page'=> 1000,
										);

								$people = new WP_Query( $person_args ); ?>
								
								<?php if($people->have_posts()) { ?>
									
									<ul>
		
										<?php while ($people->have_posts()) : $people->the_post(); ?>
			
											<?php $personmeta = get_post_custom($post->ID); // get the meta from this person ?>
										    <?php $person_id = get_the_ID(); ?> 
																				
											<?php $publication_args = (array(
												'post_type'=>'publication',
												'post_status'=>'publish',
												'posts_per_page'=> 1,
												'tax_query' => array(
													array(	// restrict to the current group
														'taxonomy' => 'author_archive',
														'field' => 'slug',
														'terms' => $person_id,
														) // end array
														) // end array
													) // end array
												);
	
												$publications = new WP_Query( $publication_args );
	
												?>
								
												<?php if($publications->have_posts()) { ?>
		
													<li><a href="/author-archive/<?php echo $person_id; ?>/">
														<?php echo $personmeta['first_name'][0]." ".$personmeta['last_name'][0]; ?>
													</a></li>
		
												<?php } // end if publications ?>
										
										<?php wp_reset_query(); ?>
										<?php endwhile; ?>
										
									</ul>
								<?php } // end if people ?>
								
								
								
		
							</section>
						</div> <!-- end content -->					
					</div> <!-- end span6 -->
					
				<?php endwhile; endif;?>
		
			</article>
		
	
	</main>



<?php get_footer(); ?>
