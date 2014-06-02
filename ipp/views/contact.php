<?php
/**
 * Inner view file for Contact page
 *
 * From the IA, this view needs to display the following data:
 *  - Body content as entered in wordpress
 *  - Director, Associate Director and Admin Assistant
 *      - Thumbnail of staff person
 *      - Name (post title? Or do we want first/last as entered in meta?)
 *      - First title
 *      - Phone number
 *      - email address
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Post object for the page
 *  - $strStaffLoop see note below
 *
 * The director, associate director and admin assistant information/structure is identical to that of the Staff, Policy
 * Research Scholars and Graduate Research Assistants pages. Therefore the content for this area is generated by the
 * @see people-loop.php file and available as $strStaffLoop
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
    <?php echo $objMainPost->content; ?>
    <?php echo $strStaffLoop; ?>
</section>