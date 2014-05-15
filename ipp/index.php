<?php
/**
 * Template file used to render the Site Front Page, whether the front page 
 * displays the Blog Posts Index or a static page. The Front Page template takes 
 * precedence over the Blog Posts Index (Home) template. 
 * 
 *
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template
 * @author Charlie Triplett, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>

<?php get_header(); ?>


	<main id="main" role="main">
	
		<article role="article"> 

		<div class="span4">

		</div>

<?php  // get total # of abstracts for Monday 
	$slide_args = array(
		'post_type' => 'slide',
		'posts_per_page' => 1,
		'orderby' => 'date',
		);

$slides = new WP_Query( $slide_args ); ?>

		<div class="span8">


		<?php if($slides->have_posts()) { ?>
			
			
			<?php while ($slides->have_posts()) : $slides->the_post(); ?>
		
		    <?php $themeta = get_post_custom($post_id); ?>

			<div class="slide clearfix" style="background-image: url('<?php $thumbnail_id=get_the_post_thumbnail($post->ID, 'large'); preg_match ('/src="(.*)" class/',$thumbnail_id,$link); echo $link[1]; ?>')">
				
				<?php if ($themeta['link'][0] != '') { ?>
					<a class="clearfix" href="<? echo $themeta['link'][0]; ?>">
				<?php } ?>		
					
					<div class="slide-text <? echo $themeta['position'][0]; ?>">
						<h2><?php the_title(); ?></h2>
						<div class="slide-content">
							<?php the_content(); ?>
						</div>
					</div>
					<?php if ($themeta['link'][0]) { ?>
				</a>
				<?php } ?>
			</div> <!-- end slide -->
		<?php endwhile;
			} // end if
		?>
	
		</div><!-- end .span8 -->
	
		</article>
	
	</main>

	<div class="clear"></div>
	
	<div class="span4">
	    <?php dynamic_sidebar( 'primary-widget' ); ?>
	</div>
	
	
	<div class="span4">
		<?php dynamic_sidebar('home_right')  ?>
	</div>

	<div class="span4">
	    <?php dynamic_sidebar( 'primary-widget' ); ?>
	</div>
	


	
<?php get_footer(); ?>
