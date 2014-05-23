<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/22/14
 * Time: 10:11 AM
 */
?>
<section aria-label="content" role="region">
    <?php $intColPlace = 0; ?>
    <?php foreach($aryStaff as $objStaff): ?>
        <!--
        <?php var_export($objStaff->image); ?>
        -->
        <?php $strFirstColClass = ($intColPlace == 0) ? 'alpha' : '';  ?>
    <div class="column-archive">
        <div class="span1 one-third portrait<?php echo ' ',$strFirstColClass; ?>">
            <a href="<?php echo $objStaff->permalink; ?>" rel="bookmark" title="<?php echo $objStaff->title; ?>">
                <?php
                    _mizzou_log($objStaff->meta_data->image_data,'image data for ' . $objStaff->title);
                ?>

                <img src="<?php echo $objStaff->meta_data->image_data->src_medium; ?>" width="static?" height="static?" alt="<?php echo $objStaff->meta_data->image_data->alt; ?>">
            </a>
        </div>
        <div class="span2 two-thirds omega">
            <ol class="contact nobullet">
                <li class="name">
                    <a href="<?php echo $objStaff->permalink; ?>" rel="bookmark" title="<?php echo $objStaff->title; ?>"><?php echo $objStaff->title; ?></a>
                    <?php if($objStaff->meta_data->member_of_group_set(array('title1','phone','email'))) : ?>
                    <ol>
                        <?php if($objStaff->meta_data->title1 != '') : ?>
                        <li class="job-title"><?php echo $objStaff->meta_data->title1; ?></li>
                        <?php endif; ?>

                        <?php if($objStaff->meta_data->phone != '') : ?>
                        <li class="phone"><?php echo $objStaff->meta_data->phone; ?></li>
                        <?php endif; ?>

                        <?php if($objStaff->meta_data->email != '') : ?>
                        <li class="email">
                            <a href="mailto:<?php echo $objStaff->meta_data->email; ?>" class="break"><?php echo $objStaff->meta_data->email; ?></a>
                        </li>
                        <?php endif; ?>
                    </ol>
                    <?php endif; ?>
                </li>
            </ol>
        </div>
    </div>
        <?php $intColPlace = ($intColPlace == 2) ? $intColPlace = 0 : ++$intColPlace; ?>
    <?php endforeach; ?>
</section>
