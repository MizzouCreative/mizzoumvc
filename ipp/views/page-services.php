<?php
/**
 * Inner view file for services page
 *
 *
 * Has access to the following variables:
 *  - $objMainPost Mizzou Post object for the page
 *  - $objFeaturedPost Mizzou Post object for the featured post
 *
 * You do not need to call get_header(), get_footer, get_sidebar() or breadcrumbs() as those are handled by outer
 * functions.
 *
 * **NOTE** 20140624 PFG: had no idea what you wanted in terms of mark up.  I did speak to Gen/Lisa and they suggested
 * the area be named "Project Spotlight"
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 *
 */
?>
<section aria-label="content" role="region">
    <div class="span12">
    <?php echo $objMainPost->content; ?>
    <?php if(isset($objFeaturedPost) && is_object($objFeaturedPost)) : ?>
        <div class="clearfix"></div>
        <div>
            <h3>Project Spotlight</h3>
            <a href="<?php echo $objFeaturedPost->permalink; ?>"><?php echo $objFeaturedPost->title; ?></a>
        </div>
    <?php endif; ?>
    </div>
</section>