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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<title> // University of Missouri</title>
    <meta content="<?php bloginfo( 'name' ); ?>" name="apple-mobile-web-app-title"/>
    
	<?php global $post; $themeta = get_post_custom($post->ID); // Get the meta fields for the current page?>
	
	<?php if ((isset($themeta['noindex']) && isset($themeta['noindex'][0]) && $themeta['noindex'][0] == 'on') 
                    || is_404() 
                    || (isset($themeta['nolink']) && isset($themeta['nolink'][0]) && $themeta['nolink'][0] == 'on' )) { ?><META NAME="ROBOTS" CONTENT="NOINDEX,NOARCHIVE"><?php } ?>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0;">
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,400italic,700italic' rel='stylesheet' type='text/css'>
	<!--[if ! lte IE 6]><!-->
		<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet">
		<link href="<?php bloginfo('template_url'); ?>/css/print.css" rel="stylesheet" media="print" />
	<!--<![endif]-->

	<?php wp_head(); // javascript and plugins in functions.php ?>
	
	<?php $tracking_code = get_option( 'tracking_input' ); 
	echo $tracking_code; ?>
	
</head>

<body>

<div class="header-wrapper clearfix">

	<div id="header" class="container clearfix">

			<ol class="skip-links">
				<li><a class="mobile-nav-button desktop-hide" href="#mobile-navigation"><span class="text">Skip to Menu</span></a></li>
				<li><a class="hidden skip-to-content" href="#main"><span class="text">Skip to content</span></a></li>
				<li><a class="hidden skip-to-nav mobile-hide" href="#navigation"><span class="text">Skip to Navigation</span></a></li>
			</ol>

			<div class="clear"></div>
		
			<div class="banner span8">
				<header role="banner">
				
					<div id="missouri">
						<h2>
							<a class="shield" href="http://missouri.edu/" title="University of Missouri home">
								<span class="text">
									University of Missouri
								</span>
								<svg width="500" height="83">
								    <image xlink:href="<?php bloginfo('template_url'); ?>/images/mu-mark.svg" alt="MU Logo" src="<?php bloginfo('template_url'); ?>/images/mulogo.png" width="500" height="83"/>
								</svg>
					    	</a>
						</h2>
					</div> <!--end missouri -->
					
					<div id="site-title">
				        <h3>
				        	<a class="site-logo" href="<?php bloginfo('url'); ?>">
								<span class="text">
									<?php bloginfo( 'name' ); ?> 
								</span>
								<svg width="940px" height="48px">
								    <image xlink:href="<?php bloginfo('template_url'); ?>/images/site-title.svg" alt="Logo" src="<?php bloginfo('template_url'); ?>/images/mulogo.png" width="940px" height="48px"/>
								</svg>
				        	</a>
			        	</h3>
					</div>
					
				</header>

			</div> <!-- end six banner spans -->

			<nav id="navigation" role="navigation">
			
				<div class="span4">
					<?php get_search_form(); ?>
					
					<div class="mobile-hide">
						<?php wp_nav_menu( array( 
							'theme_location' => 'audience',
							'items_wrap'     => '<ol class="%1$s %2$s">%3$s</ol>'
							) ); ?>
					</div>
				</div> <!--end span4 -->

	
	
				<div class="clear"></div>
	
				<div class="mobile-hide span12">
					<?php wp_nav_menu( array( 
						'theme_location' => 'primary',
						'items_wrap'     => '<ol class="%1$s %2$s">%3$s</ol>'
						) ); ?>
				</div>
			
			</nav>

	</div> <!-- end .container #header -->

</div> <!-- end header-wrapper -->

<div class="content-wrapper">

<div class="container clearfix">