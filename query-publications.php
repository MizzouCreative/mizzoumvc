<?php
/**
 * @deprecated
 * @todo delete
 */
$categories = get_the_terms(get_the_ID(),'category');;

if ($categories) { ?>

<?php foreach ($categories as $category) { // uses department description for the url ?>
		<?php $category_counter++ ?>
<?php } // end foreach ?>



<?php foreach( $categories as $category ) { ?>

	<?php if ($category_counter > 1) { // if more than one category is selected, give the name of each category ?>
		<h2 class="category">
			<a href="<?php echo get_term_link( $category->slug, 'category' ); ?>">
				<?php echo $category->name; ?>
			</a>
		</h2>
	<?php } ?>
	
	<?php
	$publication_args = array(
		'orderby'       => 'slug', 
	    'order'         => 'ASC',
	    'hide_empty'    => true, 
	    'hierarchical'  => true
	    );
	
	$publication_types = get_terms('publication_type', $publication_args);
	?>
	
	<?php foreach( $publication_types as $publication_type ) { ?>
	
	<?php $args = array(
			'post_type' => 'publication',
			'post_status' => array( 
						'publish' 
						),
			'posts_per_page' => 10,
			'orderby' => 'menu_order name',
			'order' => 'ASC',
			'tax_query' => array(
				array(	// restrict to the current department
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => $category->slug,
				)
			)
		);
		
		// Retrieve the posts matching our args
		$publications = new WP_Query( $args ); ?>
		
		<?php if($publications->have_posts()) { ?>
		
			<h3 class="publication-category">
				<a href="<?php echo get_term_link( $publication_type->slug, 'publication_type' ); ?>">
					<?php echo $publication_type->name; ?>
				</a>
			</h3>

			<ul class="publications">
				<?php while ($publications->have_posts()) : $publications->the_post(); ?>
		 		    <?php $postmeta = get_post_custom($post_id); ?>
		
					<li>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>															<ul>
							<? if ($postmeta['authors'][0]) { ?>
			                	<li><? echo $postmeta['authors'][0]; ?></li>
			                <? } ?>
			                	<li><?php the_time('F, Y'); ?>
</li>
							<? if ($postmeta['details'][0]) { ?>
			                	<li><? echo $postmeta['details'][0]; ?></li>
			                <? } ?>
							<? if ($postmeta['link'][0]) { ?>
			                	<li><a href="<? echo $postmeta['link'][0]; ?>"><? echo $postmeta['link'][0]; ?></a></li>
			                <? } ?>
						</ul>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php wp_reset_query(); ?>
		<?php } else {  // end if have posts ?>
			<!-- <p>No publications have been selected for <?php echo $award->name; ?>.</p> -->
		<?php } ?>

	<? } // end foreach award?>

<? } // end for each category ?>
<? } // end if categories ?>
