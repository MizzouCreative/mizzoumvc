<?php 

// Make the metabox appear on the page editing screen
function categories_for_pages() {
	register_taxonomy_for_object_type('category', 'page');
}
add_action('init', 'categories_for_pages');


?>