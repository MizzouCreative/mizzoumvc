<?php
/**
 * Inner view file for a single publication
 *
 * From the IA, this view needs to display the following data:
 *  - body content has entered into wordpress for this page
 *  - Related Publications
 *  - Link to all Publications
 *  - Related Projects
 *  - Link to all Projects
 *  - Main Staff Contact for a Policy Area
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Post object for the page
 *  - $strMorePublications html formatted string of authors and links to their archive pages
 *
 * You do not need to call get_header(), get_footer, get_sidebar() or breadcrumbs() as those are handled by outer
 * functions.
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Charlie Triplett, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 */
?>
<div class="span8">
<?php if($objMainPost->authors != '') : ?>
<p class="authors"><?php echo $objMainPost->authors; ?></p>
<?php endif; ?>
<p class="date"><?php echo $objMainPost->formatted_date; ?></p>
<?php echo $objMainPost->content; ?>

<?php if($objMainPost->link != '') : ?>
<p><a href="<?php echo $objMainPost->link; ?>">View this publication at <?php echo $objMainPost->link; ?></a></p>
<?php endif; ?>

<?php if($strMorePublications != '') : ?>
<p>More publications from <?php echo $strMorePublications; ?></p>
<?php endif; ?>
</div>