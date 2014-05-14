<?php 

function recent_posts_function($atts){
   extract(shortcode_atts(array(
      'posts' => 10,
      'category' => '',
      'tag' => '',
   ), $atts));
   
   ob_start();
		wp_reset_query();
   
		$post_args = array(
					'orderby' => 'date', 
					'order' => 'DESC' , 
					'category_name' => $category,
					'posts_per_page' => $posts
					); ?>
		   
		   
		<?php $posts = new WP_Query( $post_args ); ?>
		
		<?php if($posts->have_posts()) { ?>
			<div class="post-shortcode-wrapper">
			<?php $i = 0; ?>
			
			<?php while ($posts->have_posts()) : $posts->the_post(); ?>
			<?php $i++; ?>
	
				<div class="span2 <?php if ($i % 3 != 0) { ?> alpha<?php } ?>">
				    
				    <div class="post-item">
					    <?php $themeta = get_post_custom($post_id); ?>
						
						<?php if ($themeta['link'][0] != '') { // if there's an alternate link ?>
							<a class="clearfix post-link" href="<? echo $themeta['link'][0]; ?>">
						<?php } else { // output the permalink ?>		
							<a class="clearfix post-link" href="<?php the_permalink() ?>">
						<?php }  // end if link ?>		
							<h4 class="post-title"><?php the_title(); ?></h4>
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

		<?php endwhile; ?>
		</div> <!-- end span9 -->
		<?php } // end if posts ?>
	<div class="clear"></div>

   <?php wp_reset_query();
   return ob_get_clean(); // must go after restore_current_blog
}
add_shortcode('recent-posts', 'recent_posts_function');


// Function that will return our WordPress menu
function list_menu($atts, $content = null) {
	extract(shortcode_atts(array(  
		'name'            => '', 
		'container'       => 'div', 
		'container_class' => '', 
		'container_id'    => '', 
		'menu_class'      => 'menu', 
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'depth'           => 0,
		'walker'          => '',
		'theme_location'  => ''), 
		$atts));

	return wp_nav_menu( array( 
		'menu'            => $name, 
		'container'       => $container, 
		'container_class' => $container_class, 
		'container_id'    => $container_id, 
		'menu_class'      => $menu_class, 
		'menu_id'         => $menu_id,
		'echo'            => false,
		'fallback_cb'     => $fallback_cb,
		'before'          => $before,
		'after'           => $after,
		'link_before'     => $link_before,
		'link_after'      => $link_after,
		'depth'           => $depth,
		'walker'          => $walker,
		'theme_location'  => $theme_location));
}
add_shortcode("menu", "list_menu");


function list_taxonomy ($atts){
   extract(shortcode_atts(array(
      'taxonomy' => 'category',
   ), $atts));
  ob_start();

   echo '<ul>';
	wp_list_categories( array( 
				'taxonomy' => $taxonomy, 
				'format' => 'list',
				'title_li' => ''
				) 
				);
   echo '</ul>';
   return ob_get_clean(); // must go after restore_current_blog

}
add_shortcode('list-category', 'list_taxonomy');


// Add ability to create 2 columns in content area
// Left half is not required unless you want a row
function left_half($atts) {
   extract(shortcode_atts(array(
      'row' => '',
   ), $atts));

   if ($row == 'true') {
   
   return '</div> <!-- close previous right-half -->
   		   <div class="clear"></div>
   		   <div class="left-half alpha">';
   		   
   } else {

   return '<div class="clear"></div>
   		   <div class="left-half alpha">';
   }

}
add_shortcode('left-half', 'left_half');

// Only right half is required (see page.php)
function right_half() {
	return '</div><div class="right-half omega">';
}
add_shortcode('right-half', 'right_half');


add_action( 'init', 'register_shortcodes');
?>