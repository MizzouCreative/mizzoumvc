<?php
/**
 * Template file used to render the footer of the site
 * 
 * 
 * @package WordPress
 * @subpackage mizzou-news
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @uses class-customPostData
 * @author Charlie Triplett, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>

</div> <!-- end .container from header -->

</div> <!-- end .content wrapper from header -->


<div class="footer-wrapper">

	<div id="footer" class="container clearfix">
	
			<div id="mobile-navigation" class="desktop-hide clearfix">

				<a class="close-button mobile-nav-button" href="#exit"><span class="text">Close Menu</span></a>

				<div class="menu-wrapper">
	
					<nav role="navigation">
						<?
						$walker = new a11y_walker();
						$child_args = array(
							'depth'        	=> 4, // if it's a top level page, we only want to see the major sections
							'post_type'    	=> 'page',
							'post_status'  	=> 'publish',
							'sort_column'  	=> 'menu_order, post_title',
							'title_li'		=> '', 
							'walker' 		=> $walker,
						);?>
							
						<ol class="mobilenav menu">
							<li class="home"><a href=" <?php bloginfo( 'url' ); ?> "><?php bloginfo( 'name' ); ?></a></li>
							<?php  $children = wp_list_pages($child_args); // use if alternate URLs aren't needed ?>
						</ol>
					
					</nav>
				</div> <!-- end menu-wrapper -->

				<a class="close" href="#exit"><span class="text">Close Menu</span></a>

			</div> <!-- end #mobile-navigation -->

			<footer role="contentinfo"> <!-- No more than one contentinfo -->
				
					<div class="span4">				
						<a class="footer-missouri" href="http://missouri.edu/" title="University of Missouri home">
							<span class="brand-footer">
								<svg width="200%" height="70px">
								    <image xlink:href="<?php bloginfo('template_url'); ?>/images/brand-footer.svg" alt="MU Logo" src="<?php bloginfo('template_url'); ?>/images/mulogo.png" width="100%" height="70px"/>
								</svg>
							</span>
							<span class="text">
								University of Missouri
							</span>
				    	</a>
					</div> <!-- span3 -->
					
					<div class="span3">				
				    	<p class="address">
					    	Columbia, MO 65211 <br />
					    	573-882-4544
					    </p>
					</div>  <!-- span3 -->

					
					<div class="span3">				

				        <a class="alert-icon" href="http://mualert.missouri.edu">
							<span class="exclaim">
								<svg width="24" height="24">
								    <image xlink:href="<?php bloginfo('template_url'); ?>/images/exclaim.svg" alt="" src="<?php bloginfo('template_url'); ?>/images/exclaim.png" width="24" height="24"/>
								</svg>
							</span>
					        Emergency Information
				        </a>
					
					</div> <!-- span3 -->
					<div class="span2">				

					    <div class="accessibility">
					    	<a href="http://diversity.missouri.edu/communities/disabilities.php">Disability Resources</a>
					    </div>  <!-- accessibility -->
					    <div class="accessibility">
					    	<a href="/a-z/">A-Z Index</a>
					    </div>  <!-- accessibility -->
					</div> <!-- span2 -->

					
					<div class="clear"></div>
					
					<div class="legal span12">				
					Copyright &#169; <time datetime="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></time> &#8212; Curators of the University of Missouri. All rights reserved. <a href="http://www.missouri.edu/dmca/">DMCA</a> and <a href="http://missouri.edu/statements/copyright.php">other copyright information</a>. An <a href="http://missouri.edu/statements/eeo-aa.php">equal opportunity/affirmative action</a> institution. Published by <a href=" <?php bloginfo( 'url' ); ?> "><?php bloginfo( 'name' ); ?></a>. Updated: <?php if (is_single() || is_page() ) { the_modified_time('M j, Y'); } else {site_modified_date(); } ?>				
					</div> <!--  span12 -->			
	
				</footer>
				
	</div> <!--   #footer  container -->

</div> <!-- footer-wrapper -->


<?php wp_footer(); ?>

</body>