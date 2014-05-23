<?php
/**
 * Inner view file for Staff page
 *
 * From the IA, this view needs to display the following data:
 *  - Publication title
 *  - Publication permalink
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Main Post object for the page, you shouldnt need it, but just in case you do
 *  - $aryPublications
 *
 * You do not need to call get_header(), get_footer, get_sidebar() or breadcrumbs() as those are handled by outer
 * functions and/or views.
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */
?>
<section aria-label="content" role="region">
    <ol>
        <?php foreach($aryPublications as $objPublication) : ?>
        <li><a href="<?php echo $objPublication->permalink; ?>" title="Permanent link for <?php echo $objPublication->title; ?>"></a></li>
        <?php endforeach; ?>
    </ol>
</section>