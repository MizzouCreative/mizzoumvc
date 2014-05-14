<?php 
function webcom_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'webcom' ),
		'id' => 'primary-widget',
		'description' => __( 'The primary widget area', 'webcom' ),
		'before_widget' => '<article><div class="widget-container %2$s">',
		'after_widget' => '</div></article>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, left side of the home page.
	register_sidebar( array(
		'name' => __( 'Home - Left Side', 'webcom' ),
		'id' => 'home_left',
		'description' => __( 'Below the slide, left side', 'webcom' ),
		'before_widget' => '<article><div class="widget-container %2$s">',
		'after_widget' => '</div></article>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );


	// Area 4, right side of the home page.
	register_sidebar( array(
		'name' => __( 'Home - Right Side', 'webcom' ),
		'id' => 'home_right',
		'description' => __( 'Below the slide, right side', 'webcom' ),
		'before_widget' => '<article><div class="widget-container %2$s">',
		'after_widget' => '</div></article>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );


}
// Register sidebars by running webcom_widgets_init() on the widgets_init hook. //
add_action( 'widgets_init', 'webcom_widgets_init' );
?>