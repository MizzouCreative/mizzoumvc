<?php
/**
 * Inner view file for Publications archive page
 *
 * From the IA, this view needs to display the following data:
 *  - Publication title
 *  - Publication permalink
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Main Mizzou Post object for the page, you shouldnt need it, but just in case you do
 *  - $aryPublications array of Mizzou post objects for each publication
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
 * @uses mizzouIncludeView()
 */
?>
<section aria-label="content" role="region">
    <?php foreach($aryPublicationsGroup as $strPublicationType => $aryPublications) : ?>
        <h2><?php echo $strPublicationType; ?>s</h2>
        <?php require 'publication-loop.php'; ?>
    <?php endforeach; ?>
</section>