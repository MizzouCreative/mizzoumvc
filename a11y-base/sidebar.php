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

	<div id="sidebar" class="mobile-hide right-offset2 span3">
		<?php $themeta = get_post_custom($post_id); ?>
		<?php  if ($themeta['childnav'][0] !='1') { //  if childnav is populated ?>
			<?php if (function_exists('childnav')) childnav(); ?>
		<?php  } ?>

		
		<?php if (is_single() || is_archive() && !is_tax() ) { ?>

			<h2>Categories</h2>
			<nav role="navigation">
				<ol class="childnav">
					<?php wp_list_categories('orderby=name&title_li='); ?>
				</ol>
			</nav>

		<?php } ?>
		
	</div>

</aside>