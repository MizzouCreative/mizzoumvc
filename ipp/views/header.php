<?php
/**
 * View file used to contain header content of theme pages
 *
 * @package WordPress
 * @subpackage Generic Theme
 * @category theme
 * @category template-part
 * @author Charlie Triplett
 * @copyright 2013 Curators of the University of Missouri
 *
 * Will need
 *  - objMainPost w/ custom meta data
 *  - strHeadTitle
 *  - template location: in site model
 *  - site option for tracking code
 *  - blog url: in site model
 *  - blog name in site model
 *  - navigation menus: audience and primary
 *  - search form: where should this come from?
 *  - contents of wp_head where should this come from?
 */?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title><?php echo $strHeaderTitle; ?> // University of Missouri</title>
    <meta content="<?php echo $strSiteName; ?>" name="apple-mobile-web-app-title"/>
    <?php if($boolIncludeNoIndex) : ?>
        <META NAME="ROBOTS" CONTENT="NOINDEX,NOARCHIVE">
    <?php endif; ?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0;">
    <!--[if ! lte IE 7]><!-->
    <link href="<?php echo $strActiveStylesheet; ?>" rel="stylesheet">
    <!--<![endif]-->
    <link rel="shortcut icon" href="<?php echo $strActiveStylesheet; ?>/favicon.ico" />

    <?php if('' != $strTrackingInput) : ?>
        <?php echo $strTrackingInput; // should this be moved to the footer? ?>
    <?php endif; ?>

    <?php echo $strWpHeaderContents; ?>

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
                                        <image xlink:href="<?php echo $strActiveThemeURL; ?>/images/mu-mark.svg" alt="MU Logo" src="<?php echo $strActiveThemeURL; ?>/images/mu-mark.png" width="108" height="62px"/>
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
                        <a href="<?php echo $strSiteURL; ?>">
                            <?php echo $strSiteName; ?>
                        </a>
                    </div>
                </div> <!-- end .banner -->

            </header>

        </div> <!-- end six banner spans -->


        <div class="span6">
            <?php $strSearchFormContents; ?>

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
