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
    <?php $intColPlace = 0; ?>
    <?php foreach($aryStaff as $objStaff): ?>
        <?php $strFirstColClass = ($intColPlace == 0) ? 'alpha' : '';  ?>
    <div class="column-archive">
        <div class="span1 one-third portrait<?php echo ' ',$strFirstColClass; ?>">
            <a href="<?php echo $objStaff->permalink; ?>" rel="bookmark" title="<?php echo $objStaff->title; ?>">
                <img src="<?php echo $objStaff->meta_data->image->src_medium; ?>" width="static?" height="static?" alt="<?php echo $objStaff->meta_data->image->alt; ?>">
            </a>
        </div>
        <div class="span3 two-thirds omega">
            <ol class="contact nobullet">
                <li class="name">
                    <a href="<?php echo $objStaff->permalink; ?>" rel="bookmark" title="<?php echo $objStaff->title; ?>"><?php echo $objStaff->title; ?></a>
                    <?php if($objStaff->meta_data->member_of_group_set(array('title1','phone','email'))) : //is there at least one item from the list that is available> ?>
                    <ol>
                        <?php if($objStaff->meta_data->title1 != '') : ?>
                        <li class="job-title"><?php echo $objStaff->meta_data->title1; ?></li>
                        <?php endif; ?>

                        <?php if($objStaff->meta_data->phone != '') : ?>
                        <li class="phone"><?php echo $objStaff->meta_data->phone; ?></li>
                        <?php endif; ?>

                        <?php if($objStaff->meta_data->email != '') : ?>
                        <li class="email">
                            <a href="mailto:<?php echo $objStaff->meta_data->email; ?>" class="break">Email</a>
                        </li>
                        <?php endif; ?>
                    </ol>
                    <?php endif; ?>
                </li>
            </ol>
        </div>
    </div>
        <?php $intColPlace = ($intColPlace == 1) ? $intColPlace = 0 : ++$intColPlace; ?>
    <?php endforeach; ?>
</section>
