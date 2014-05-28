<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 5/28/14
 * Time: 3:14 PM
 */
global $objPerson;
?>

<div class="span3 one-third" id="portrait-container">
    <section aria-labelledby="portait" role="img">
        <a href="" title="">
            <span id="portrait-label" class="hidden">IMG ALT CONTENT</span>
            <img src="" alt="" class="attachment-medium wp-post-image" width="" height="">
        </a>
    </section>
</div>

<div class="span4 two-thirds">
    <section aria-label="contact information" role="region">
        <ol class="contact single-person nobullet">
        <?php if(count($objPerson->meta_data->title) > 0): ?>
            <?php foreach($objPerson->meta_data->title as $strTitle) : ?>
            <li class="job-title"><?php echo $strTitle; ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if(isset($objPerson->meta_data->phone)) : ?>
            <li class="phone"><?php echo $objPerson->meta_data->phone; ?></li>
        <?php endif; ?>
        <?php if(isset($objPerson->meta_data->fax)) : ?>
            <li class="fax">
                <span class="uncolor">Fax: <?php echo $objPerson->meta_data->fax; ?></span>
            </li>
        <?php endif; ?>
        <?php if(isset($objPerson->meta_data->email)) : ?>
            <li class="email break">
                <a href="mailto:<?php echo $objPerson->meta_data->email; ?>"><?php echo $objPerson->meta_data->email; ?></a>
            </li>
        <?php endif; ?>

        <?php if(isset($objPerson->meta_data->address1)) : ?>
            <li class="office-label">
                <strong>Office</strong>
            </li>
            <li class="address_1"><?php echo $objPerson->meta_data->address1; ?></li>
            <?php if(isset($objPerson->meta_data->address2)) : ?>
            <li class="address_2"><?php echo $objPerson->meta_data->address2; ?></li>
            <?php endif; ?>
            <li class="address_2">Columbia, MO 65211</li><?php //is this always hard-coded? ?>
       <?php endif; ?>

       <?php if(isset($objPerson->meta_data->website)) : ?>
            <li class="website break">
                <a href="<?php echo $objPerson->meta_data->website; ?>"><?php echo $objPerson->meta_data->website; ?></a>
            </li>
       <?php endif; ?>

       <?php if(isset($objPerson->cv)) : ?>
            <li>
                <a href="<?php echo $objPerson->cv; ?>">Curriculum Vitae</a>
            </li>
       <?php endif; ?>
        </ol>
    </section>
</div>

