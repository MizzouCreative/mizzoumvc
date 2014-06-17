<?php
/**
 * Template file used to contain header content of theme pages
 *
 * @package WordPress
 * @subpackage Generic Theme
 * @category theme
 * @category template-part
 * @author Charlie Triplett
 * @copyright 2013 Curators of the University of Missouri
 */
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<title><?php if ( ( is_category() ) ) { ?>Archive: <?php } ?><?php if ( ( is_tag() ) ) { ?>Tag: <?php } ?><?php wp_title(''); ?><?php if ( !( is_front_page() ) ) { ?> // <?php } ?><?php bloginfo('name'); ?> // University of Missouri</title>
    <meta content="<?php bloginfo( 'name' ); ?>" name="apple-mobile-web-app-title"/>
    
	<?php global $post; $themeta = get_post_custom($post->ID); // Get the meta fields for the current page?>
	
	<?php if ((isset($themeta['noindex']) && isset($themeta['noindex'][0]) && $themeta['noindex'][0] == 'on') 
                     || is_404() 
					 || (isset($themeta['nolink']) && isset($themeta['nolink'][0]) && $themeta['nolink'][0] == 'on' )) { ?><META NAME="ROBOTS" CONTENT="NOINDEX,NOARCHIVE"><?php } ?>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0;">
	<!--[if ! lte IE 7]><!-->
		<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet">
	<!--<![endif]-->

	<?php wp_head(); // javascript and plugins in functions.php ?>
	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />

	<?php $tracking_code = get_option( 'tracking_input' ); 
	echo $tracking_code; ?>
	
</head>

<body>

<div class="header-wrapper clearfix">

	<div id="header" class="container clearfix">

			<div id="exit" class="span6">

				<ol class="skip-links">
					<li><a class="mobile-nav-button desktop-hide" href="#mobile-navigation"><span class="text">Skip to Menu</span></a></li>
					<li><a class="hidden skip-to-content" href="#main"><span class="text">Skip to content</span></a></li>
					<li><a class="hidden skip-to-nav mobile-hide" href="#navigation"><span class="text">Skip to Navigation</span></a></li>
				</ol>

				<header role="banner">
					<div class="banner">
						<div id="missouri">
							<a href="http://missouri.edu/" title="University of Missouri home">
								<span class="shield">
									<svg width="108" height="62px">
									    <image xlink:href="<?php echo get_stylesheet_directory_uri(); ?>/images/mu-mark.svg" alt="MU Logo" src="<?php echo get_stylesheet_directory_uri(); ?>/images/mu-mark.png" width="108" height="62px"/>
									</svg>
								</span>
								<span class="text">
									University of Missouri
								</span>
					    	</a>
						</div> <!--end missouri -->
						
						<div id="division">
				        	<a href="http://truman.missouri.edu/">
								<span class="medium-hide mobile-hide">Harry S Truman</span> School of Public Affairs
				        	</a>
						</div>
					
						<div id="site-title">
				        	<a href="<?php bloginfo('url'); ?>">
								<?php bloginfo( 'name' ); ?>
				        	</a>
						</div>
				</div> <!-- end .banner -->

			</header>

			</div> <!-- end six banner spans -->

			
				<div class="span6">
					<?php get_search_form(); ?>
                    
                    <div class="mobile-hide">
                        <?php wp_nav_menu( array(
                            'theme_location' => 'audience',
                            'items_wrap'     => '<ol class="%1$s %2$s">%3$s</ol>'
                        ) ); ?>
                    </div>

				</div> <!-- end span6 -->

	</div> <!-- end .container #header -->

</div> <!-- end header-wrapper -->


<nav id="navigation" role="navigation" class="mobile-hide">
	
	<div class="menu-wrapper">
		
		<div class="container">

			<div class="menu-container span12">
                <?php wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'items_wrap'     => '<ol class="%1$s %2$s">%3$s</ol>'
                ) ); ?>
            </div>
		
		</div>
		
	</div> <!-- end .menu-wrapper -->

</nav>

<div class="clear"></div>

<div class="content-wrapper">

<div class="container clearfix">
