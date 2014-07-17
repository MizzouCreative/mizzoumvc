<?php
/**
 * View file used to render the last section of every page
 *
 * Needs the following data
 *  - Site's root URL: see $objSite below
 *  - Name of the site: see $objSite below
 *  - html formatted list of pages in wordpress as returned by wp_list_pages: see $objSite below
 *  - Parent Theme's root URL: see $objSite below
 *  - Child Theme's root URL: see $objSite below
 *  - Last modified date of either the page/post/CPT or the last update made to the site in general: see $objSite below
 *  - Current copyright year: see $objSite below
 *  - Contents as returned by wp_footer(): $strWpFooterContents
 *
 *  $objSite contains
 *  -> CopyRightYear (also accessible as $strCopyRightYear)
 *  -> Name (also accessible as $strSiteName)
 *  -> URL (also accessible as $strSiteURL)
 *  -> ParentThemeURL (also accessible as $strParentThemeURL)
 *  -> ChildThemeURL (also accessible as $strChildTheme)
 *  -> ActiveStylesheet (also accessible as $strActiveStylesheet)
 *  -> ActiveThemeURL (also accessible as $strActiveThemeURL)
 *  -> TrackingCode (also accessible as $strTrackingCode)
 *  -> PrimaryMenu (also accessible as $strPrimaryMenu)
 *  -> AudienceMenu (also accessible as $strAudienceMenu)
 *  -> LastModifiedDate (also accessible as $strLastModifiedDate)
 *  -> PageList (also accessible as $strPageList)
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
    <li class="home"><a href="<?php echo $objSite->URL; // $strSiteURL; ?>"><?php echo $objSite->Name; // $strSiteName; ?></a></li>
    <?php echo $objSite->PageList; //$strPageList; ?>
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
                        <image xlink:href="<?php echo $objSite->ChildThemeURL; //$strChildThemeURL; ?>/images/mu-mark.svg" alt="MU Logo" src="<?php echo $objSite->ChildThemeURL;// $strChildThemeURL; ?>/images/mu-mark.png" width="86px" height="50px"/>
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
                    <image xlink:href="<?php echo $objSite->ParentThemeURL; // $strParentThemeURL; ?>/images/exclaim.svg" alt="" src="<?php echo $objSite->ParentThemeURL; //$strParentThemeURL; ?>/images/exclaim.png" width="24" height="24"/>
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
                            <a href="<?php echo $objSite->URL; // $strSiteURL; ?>/a-z/">A-Z Index</a>
                            -->
        </div>  <!-- accessibility -->
    </div> <!-- span2 -->


    <div class="clear"></div>

    <div class="legal span12">
        Copyright &#169; <time datetime="<?php echo $objSite->CopyrightYear; // $intCopyrightYear; ?>"><?php echo $objSite->CopyrightYear; //$intCopyrightYear; ?></time> &#8212; Curators of
        the University of Missouri. All rights reserved. <a href="http://www.missouri.edu/dmca/">DMCA</a> and
        <a href="http://missouri.edu/copyright/">other copyright information</a>. An
        <a href="http://missouri.edu/eeo-aa/">equal opportunity/affirmative action</a> institution. Published by
        <a href="<?php echo $objSite->URL; // $strSiteURL; ?>"><?php echo $objSite->Name; //$strSiteName; ?></a>. Updated: <?php echo $objSite->LastModifiedDate;// $strModifiedDate; ?>
    </div> <!--  span12 -->


</footer>



</div> <!--   #footer  container -->

</div> <!-- footer-wrapper -->

<?php echo $strWpFooterContents; ?>

</body>
</html>