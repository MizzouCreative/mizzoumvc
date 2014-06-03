<?php
/**
 * Template file used to render a archive pages
 * 
 * Will be overriden by category.php/tag.php if available and user is viewing 
 * category/tag archives
 *
 * @package WordPress
 * @subpackage SITENAME
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @author Charlie Triplett, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>

<?php get_header(); ?>

<?php $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); ?>

<main id="main" role="main">
	
	<article role="article"> 

			<div id="sidebar" class="span3">
				<h2>Categories</h2>
				<nav role="navigation">
					<ol class="categories">
						<?php wp_list_categories('orderby=name&title_li='); ?>
					</ol>
				</nav>
			</div> <!-- end span4 -->

			<div class="span9">
			
				<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>

				<div id="content">
	
					<section role="region" aria-labelledby="title"> <!-- Label for this region will be the title itself -->
						<h1 class="title">
							<?php if ( is_day() ) : ?>
										<?php printf( __( 'Daily News Archives: <span>%s</span>', 'webcom' ), get_the_date() ); ?>
							<?php elseif ( is_month() ) : ?>
										<?php printf( __( 'Monthly News Archives: <span>%s</span>', 'webcom' ), get_the_date('F Y') ); ?>
							<?php elseif ( is_year() ) : ?>
										<?php printf( __( 'Yearly News Archives: <span>%s</span>', 'webcom' ), get_the_date('Y') ); ?>
							<?php elseif ( is_category() ) : ?>
										Category: <?php single_cat_title(); ?>
							<?php elseif (is_author()) : ?>
										Author Archive:  <?php the_author_link(); ?>
							<?php endif; ?>
						</h1>
					</section>
					
					<?php echo category_description(); // useful for external links or simple descriptions ?>

				<section role="region" aria-label="content">
	
						<?php 
							$category_name = get_query_var( 'category_name' ); 
	
							$args = array(
								'post_type' => array(
									'post',
									),
								'orderby' => 'menu_order name',
								'category_name' => $category_name,
								'order' => 'ASC',
							);
						
						$posts = new WP_Query( $args ); ?>
			
							<?php if($posts->have_posts()) { ?>
							
								<?php $i = 0; ?>
								
								<?php while ($posts->have_posts()) : $posts->the_post(); ?>
								<?php $i++; ?>
					
									<div class="span3 <?php if ($i % 3 == 0) { ?> omega <?php } ?><?php if ($i == 1 || $i == 4 || $i == 7) { ?> alpha <?php } ?>">
									    
									    <div class="post-item">
									    
										    <?php $themeta = get_post_custom($post_id); ?>
											
											<?php if ($themeta['link'][0] != '') { // if there's an alternate link ?>
												<a class="clearfix post-link" href="<? echo $themeta['link'][0]; ?>">
											<?php } else { // output the permalink ?>		
												<a class="clearfix post-link" href="<?php the_permalink() ?>">
											<?php }  // end if link ?>		
												<h3 class="post-title"><?php the_title(); ?></h3>
											</a>
											
											<?php 
											$category = get_the_category(); 
											if($category[0]){ ?>
											<p class="post-item-category"><span class="hidden">Category:</span>
												<a href="<?php echo get_category_link($category[0]->term_id ); ?>"> <?php echo $category[0]->cat_name; ?></a></p>
											<?php }// end if category 0	?>						
										</div> <!-- end front-page-post -->
										
								</div> <!-- end span3 -->
							
								<?php if ($i % 3 == 0) { // clear rows ?>
									<div class="clear"></div>
								<?php } // end if row counter ?>
					
							<?php endwhile;
								} // end if
							?>
					
						
				</section>
			
			</div> <!-- end content -->

		</div> <!-- end span8 -->

	</article>
	
</main>

<?php get_footer(); ?>
<!--
RENDERING FILE: <?php var_export(__FILE__); ?>
-->
