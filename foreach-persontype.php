<?php
/**
 * @deprecated
 * @todo delete
 */
foreach( $person_types as $type ) { ?>

	<?php if ($center_terms && $program_terms && person_types ) {

			$args = array(
				'post_type' => 'person',
				'orderby' => 'menu_order name',
				'order' => 'ASC',
				'tax_query' => array(
					array(	// restrict to the current program
						'taxonomy' => 'center_tax',
						'field' => 'slug',
						'terms' => $center_value->slug,
					),
					array(	// restrict to the current program
						'taxonomy' => 'program',
						'field' => 'slug',
						'terms' => $program_value->slug,
					),
					array(	// restrict to the current person type
						'taxonomy' => 'person_type',
						'field' => 'slug',
						'terms' => $type->slug
					)
				)
			);
	} else
	
	if ($center_terms && person_types && !program_terms ) {
			$args = array(
				'post_type' => 'person',
				'orderby' => 'menu_order name',
				'order' => 'ASC',
				'tax_query' => array(
					array(	// restrict to the current program
						'taxonomy' => 'center_tax',
						'field' => 'slug',
						'terms' => $center_value->slug,
					),
					array(	// restrict to the current person type
						'taxonomy' => 'person_type',
						'field' => 'slug',
						'terms' => $type->slug
					)
				)
			);
	
	} else
	
	if ($program_terms ) { 
			$args = array(
				'post_type' => 'person',
				'orderby' => 'menu_order name',
				'order' => 'ASC',
				'tax_query' => array(
					array(	// restrict to the current program
						'taxonomy' => 'program',
						'field' => 'slug',
						'terms' => $program_value->slug,
					),
					array(	// restrict to the current person type
						'taxonomy' => 'person_type',
						'field' => 'slug',
						'terms' => $type->slug
					)
				)
			);
	}
	?>
	
	<?php // Retrieve the posts matching our args
		 $people = new WP_Query( $args ); ?>
	
	<?php if($people->have_posts()) { ?>
	
		<?php if ($program_counter >1) { // if there is only one program, don't output the program name?>
			<h2 class="program">
				<span class="label">Program:</span>
				<a href="<?php echo $program_value->description; ?>"><?php echo $program_value->name; ?> &raquo;</a>
			</h2>
		<?php } ?>

		<?php if ($type_counter >1) { // if there is only one person type, don't output the persone type ?>
			<h3 class="persontype"><?php echo $type->name; ?></h3>
		<?php } ?>

			<?php include (TEMPLATEPATH . '/people-loop.php'); ?>
		<?php } ?>

<?php } //end foreach person_type?>

