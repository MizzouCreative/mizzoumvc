<?php
/**
 * Registers a Primary and Audience menu with wordpress
 * @todo Change audience to tactical?
 */
	if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(
		array(
			'primary' => 'Primary',
			'audience' => 'Audience',
			)
			);
		}
?>