<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/30/14
 * Time: 3:10 PM
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
    <?php $intColPlace = ($intColPlace == 1) ? $intColPlace = 0 : ++$intColPlace; ?>
<?php endforeach; ?>