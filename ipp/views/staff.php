<?php
/**
 * Inner view file for Staff page
 *
 * From the IA, this view needs to display the following data:
 *  - Thumbnail of staff person
 *  - Name (post title? Or do we want first/last as entered in meta?)
 *  - First title
 *  - Phone number
 *  - email address
 *
 * Since this is identical to the Policy Research Scholars and GRaduate Research Assistants pages, the content for this
 * page is generated by the @see people-loop.php file.
 *
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
    <?php echo $strStaffLoop; ?>
</section>
