<?php
// If the query people function exists, then we want this page registered.
// Otherwise we don't want the user to see it.
if (function_exists('query_people')) {


/**
Template Name: People Archive

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

<aside>

<div id="sidebar" class="mobile-hide span3 flex">
	
	<?php if (is_page() ) { ?>
	
	<?php $themeta = get_post_custom($post->ID); ?>
		<?php  if ($themeta['childnav'][0] !='1') { //  if childnav is populated ?>
			<h3>Pages in this section</h3>
			<nav role="navigation">
				<?
				$walker = new A11yPageWalker();
				$child_args = array(
					'depth'        	=> 4, // if it's a top level page, we only want to see the major sections
					'post_type'    	=> 'page',
					'post_status'  	=> 'publish',
					'sort_column'  	=> 'menu_order, post_title',
					'title_li'		=> '', 
					'walker' 		=> $walker,
				);?>
					
				<ol class="childnav menu">
					<?php  $children = wp_list_pages($child_args); // use if alternate URLs aren't needed ?>
				</ol>
			</nav>
		<?php  } // end themata?>
	<?php  } ?>

</div>
</aside>

<main id="main" role="main">
	
		<article role="article"> 
	
			<?php if (have_posts()) : while (have_posts()) : the_post();?>
	 		    
				<div class="span9">

					<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>

					<div id="content">
					    <h1 id="title"><?php the_title(); ?> <?php edit_post_link('Edit'); ?></h1>

						<section role="region" aria-label="content">
	
							<?php the_content(); ?>

							<?php if (function_exists('query_people')) query_people('span1','span2','true',3); ?>
	
						</section>
					</div> <!-- end content -->					
				</div> <!-- end span6 -->
				
			<?php endwhile; endif;?>
	
		</article>
	

</main>

<?php } else { // end if function_exists ?>
	
	<p>You must install the Mizzou People plugin for this page template to work </p>
	
<?php } // end if function_exists  ?>


<?php get_footer(); ?>
