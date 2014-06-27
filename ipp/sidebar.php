<?php
/**
* Template file used to render sidebar
* 
* Called on other template files via 
* <code>
* get_sidebar(); 
* </code>
* 
*
* @package WordPress
* @subpackage SITENAME
* @category theme
* @category template-part
* @author Charlie Triplett, Web Communications, University of Missouri
* @copyright 2013 Curators of the University of Missouri
*/
?>

<aside>

<div id="sidebar" class="mobile-hide span3 right-offset1 flex">
	
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

	
	<?php if (is_singular('post') || is_archive() && !is_tax() ) { ?>

		<h2>Categories</h2>
		<nav role="navigation">
			<ol class="categories">
				<?php wp_list_categories('orderby=name&title_li='); ?>
			</ol>
		</nav>

	<?php } ?>

	<?php if (is_archive() && !is_tax() ) { ?>
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
			
				<h3>News</h3>
			    <ul>
			     <? while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
			        <li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
		       <?php endwhile; ?>
			    </ul>
		   <?php } //if ($my_query)
		  } //if ($categories)
		  wp_reset_query();  // Restore global post data stomped by the_post().
		?>          
	<?php } ?>

	
	<?php if (is_singular('publication')) { ?>
		<h2>Research Areas</h2>
		<nav role="navigation">
			<ol class="categories">
			
			<?php 
			$research_category_args = array(
			  'taxonomy'     => 'research_category',
			  'orderby'      => 'name',
			  'title_li'      => '',
			);
			?>
			<?php wp_list_categories($research_category_args); ?>
			</ol>
			
		</nav>
	<?php } ?>
</div>

</aside>