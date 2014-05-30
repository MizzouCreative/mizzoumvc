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
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Main Post object for the page, you shouldnt need it, but just in case you do
 *  - $aryStafff - collection of Person Post objects
 *      - meta_data accessible via $objStaff->meta_data
 *      - image data accessible via $objStaff->meta_data->image
 *          - src data: all defined sizes available as "src_<size>" e.g. $objStaff->meta_data->image->src_medium
 *          - alt data: $objStaff->meta_data->image->alt
 *          - caption data: $objStaff->meta_data->image->caption
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
