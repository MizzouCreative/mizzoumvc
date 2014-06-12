<?php
/**
 * View file used to render the last section of every page
 *
 * Has access to the following variables
 *  - $strSiteURL - Site's root URL
 *  - $strSiteName - Name of the site
 *  - $strPageList - html formatted list of pages in wordpress as returned by wp_list_pages
 *  - $strParentThemeURL - Parent Theme's root URL
 *  - $strChildThemeURL - Child Theme's root URL
 *  - $strModifiedDate - last modified date of either the page/post/CPT or the last update made to the site in general
 *  - $intCopyrightYear - year to be used for copyright purposes
 *  - $strWpFooterContents - contents as returned by wp_footer()
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Charlie Tripplet, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */
?>
</div> <!-- end .container from header -->

</div> <!-- end .content wrapper from header -->


<div class="footer-wrapper">

	<div id="footer" class="container clearfix">

			<div id="mobile-navigation" class="desktop-hide clearfix">

				<a class="close-button mobile-nav-button" href="#exit"><span class="mobile-hide text">Close Menu</span></a>

				<div class="menu-wrapper">

					<nav role="navigation">


<ol class="mobilenav menu">
    <li class="home"><a href="<?php echo $strSiteURL; ?>"><?php echo $strSiteName; ?></a></li>
    <?php echo $strPageList; ?>
</ol>

</nav>
</div> <!-- end menu-wrapper -->

<a class="close" href="#exit"><span class="text mobile-hide">Close Menu</span></a>

</div> <!-- end #mobile-navigation -->

<footer role="contentinfo"> <!-- No more than one contentinfo -->

    <div class="span4">

        <div class="footer-brand">
            <a href="http://missouri.edu/" title="University of Missouri home">
                <span class="shield">
                    <svg width="86px" height="50px">
                        <image xlink:href="<?php echo $strParentThemeURL; ?>/images/mu-mark.svg" alt="MU Logo" src="<?php echo $strParentThemeURL; ?>/images/mu-mark.png" width="86px" height="50px"/>
                    </svg>
                </span>
                <span class="text">
                    University of Missouri
                </span>
            </a>
        </div> <!--end missouri -->

    </div> <!-- span4 -->

    <div class="span3">
        <p class="address">
            Columbia, MO 65211 <br />
            573-882-3304
        </p>
    </div>  <!-- span3 -->


    <div class="span3">

        <a class="alert-icon" href="http://mualert.missouri.edu">
            <span class="exclaim">
                <svg width="24" height="24">
                    <image xlink:href="<?php echo $strParentThemeURL; ?>/images/exclaim.svg" alt="" src="<?php echo $strParentThemeURL; ?>/images/exclaim.png" width="24" height="24"/>
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
            <!-- removed temporarily 20140610 PFG
                            <a href="<?php echo $strSiteURL; ?>/a-z/">A-Z Index</a>
                            -->
        </div>  <!-- accessibility -->
    </div> <!-- span2 -->


    <div class="clear"></div>

    <div class="legal span12">
        Copyright &#169; <time datetime="<?php echo $intCopyrightYear; ?>"><?php echo $intCopyrightYear; ?></time> &#8212; Curators of
        the University of Missouri. All rights reserved. <a href="http://www.missouri.edu/dmca/">DMCA</a> and
        <a href="http://missouri.edu/copyright/">other copyright information</a>. An
        <a href="http://missouri.edu/eeo-aa/">equal opportunity/affirmative action</a> institution. Published by
        <a href="<?php echo $strSiteURL; ?>"><?php echo $strSiteName; ?></a>. Updated: <?php echo $strModifiedDate; ?>
    </div> <!--  span12 -->


</footer>



</div> <!--   #footer  container -->

</div> <!-- footer-wrapper -->

<?php echo $strWpFooterContents; ?>

</body>
</html>