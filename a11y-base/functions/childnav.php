<?php function childnav() {
	// child page navigation
	global $post;
	
	$walker = new a11y_walker();

	$ancestors = get_ancestors( $post->ID, 'page' );
	$parent_id = $post->post_parent;
	$grandparent_id = $parent_id->post_parent;
	$parent_title = get_the_title($post->post_parent);
	$parent_url = get_permalink($post->post_parent);
	$parent_title = get_the_title($ancestors[0]);
	$parent_url = get_permalink($ancestors[0]);
	$grandparent_title = get_the_title($ancestors[1]);
	$grandparent_url = get_permalink($ancestors[1]);
	
	
	if ( $ancestors[0] !='' ) { // If the page has one parent
		$child_of_value = $parent_id;
		$depth_value =	2;

	} else 
	
	if ( $ancestors[1] !='' ) {  // If the page has grandparent
		$child_of_value = $parent_id;
		$depth_value =	1;
	} else { // we are on a top level page, just out put the children - easy!
		$child_of_value = $post->ID;
		$depth_value =	1;
	}

	$child_args = array(
		'child_of'     	=> $child_of_value,
		'depth'        	=> $depth_value, // if it's a top level page, we only want to see the major sections
		'echo'         	=> 0,
		'post_type'    	=> 'page',
		'post_status'  	=> 'publish',
		'sort_column'  	=> 'menu_order, post_title',
		'title_li'		=> '', 
		'walker' 		=> $walker,
	);
		
	
	$children = wp_list_pages($child_args); // use if alternate URLs aren't needed
		
	if ($children ) { ?>
	
	 	<h3>Pages in this section</h3>
		<nav role="navigation">
			<p id="childnav-label" class="clearfix hidden">Pages in this section:</p>

			<ol class="childnav menu" aria-labelledby="childnav-label">

				<?php if ( $ancestors[1] !='' ) { // if there is a grandparent open a li ol ?>
					<li class="grandparent"><a href="<?php echo $grandparent_url; ?>"><?php echo $grandparent_title; ?></a>
					<ol>
				<?php } ?>

					
					<?php if ( $ancestors[0] !='' ) { // if there is a grandparent and parent output a link to the parent?>
					
						<li class="parent"><a href="<?php echo $parent_url; ?>"><?php echo $parent_title; ?></a>
					
					   	<ol>
					   	
						   <?php echo $children; ?>
						   
					   	</ol> <!-- end ul.second-level -->
					   	
				   
					   	<? } else { // if there are no parents we're on a top level page - easy! ?>
	
						   <?php echo $children; ?>
					   	
					   	<? } ?>
				   	</li>  <!-- end li.parent -->
				   
				</ol><!-- end ul.childnav -->
				
			<?php if ( $ancestors[1] !='' ) { // close the grandparent li ?>
					</li>
				</ol>
			<?php } ?>


		</nav>
		
	<?php } // end if

} // end function ?>