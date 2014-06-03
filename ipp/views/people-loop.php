<?php
/**
 * Loop file for people listings
 *
 * From the IA, each person needs the following data:
 *  - thumbnail
 *  - Name (post title)
 *  - permalink to their profile on the site
 *  - Title1
 *  - Phone
 *  - Email link
 *
 * View has access to the following variables:
 *  - $objMainPost Mizzou Post object for the page
 *  - $aryStaff array of People post objects
 *
 * You do not need to call get_header(), get_footer, get_sidebar() or breadcrumbs() as those are handled by outer
 * functions.
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 *
 * @todo rename $aryStaff to $aryPeople to be more accurate?
 * @todo remove the static value at line 66 into config file/theme option so we can dynamically change the number of columns?
 *
 */
?>
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
    <?php if($intColPlace == 1): $intColPlace = 0; ?>
        <div class="clear row"></div>
    <?php else : ++$intColPlace; ?>
    <?php endif;?>
<?php endforeach; ?>