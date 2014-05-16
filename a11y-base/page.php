<?php
/**
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

<div class="flex span7">

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
					
						<? // if ONLY the divide shortcode is used, then start the column here, otherwise
						$divide_check = get_the_content();
						if( preg_match('[right-half]',$divide_check) && !preg_match('[left-half]',$divide_check) ) {
						    echo '<div class="left-half"> ';//has an image / you can use $c saves calling the function again 
						} ?>
					
						<?php the_content(); ?>

						<? 
						$divide_check = get_the_content();
						if(preg_match('[right-half]',$divide_check))
						{
						    echo '</div> ';//has an image / you can use $c saves calling the function again 
						} ?>

						<?php $pagemeta = get_post_custom(get_the_ID()); ?>
						<?php  if ($pagemeta['form_container'][0]) { //  if childnav is populated ?>
							<?php echo $pagemeta['form_container'][0]; ?>
						<?php  } ?>
				

						<?php if (function_exists('query_people')) query_people('span2','span5','false'); ?>
						
						<?php include (TEMPLATEPATH . '/query-publications.php'); ?>

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

<!-- using file: <?php echo(__FILE__); ?> -->
<?php get_footer();