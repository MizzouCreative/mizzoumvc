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

<div class="span5">
<?php if(isset($objPerson->focus) && count($objPerson->focus) > 0) : ?>
     <div class="clear"></div>
    <h2>Research Interests</h2>
    <ul>
    <?php foreach($objPerson->focus as $strFocus) : ?>
        <li><?php echo $strFocus; ?></li>
    <?php endforeach; ?>
    </ul>
<?php else : ?>
    <?php //should anything go here if they dont have research interests? ?>
<?php endif; ?>
</div>
<div class="clear"></div>
<hr>
<div class="span6 right-offset1">
    <?php if($objPerson->content != '') : ?>
        <h2 class="hidden">Biography</h2>
        <?php echo $objPerson->content; ?>
    <?php endif; ?>
   <?php if(count($objPerson->education) > 0) : ?>
    <h2>Education</h2>
       <ul>
           <?php foreach($objPerson->education as $strEducation) : ?>
           <li><?php echo $strEducation; ?></li>
           <?php endforeach; ?>
       </ul>
    <?php endif; ?>
</div>
<div class="span5">
    <?php if(count($aryPublications) > 0) : ?>
    <h2>Selected Publications</h2>
    <div class="clearfix">
        <?php foreach($aryPublications as $objPublication) : ?>
            <div class="publication-item">
                <h4>
                    <a title="<?php echo $objPublication->title; ?>" rel="bookmark" href="<?php echo $objPublication->permalink; ?>"><?php echo $objPublication->title; ?></a>
                    <?php if($objPublication->meta_data->authors != '') : ?>
                    <p><?php echo $objPublication->meta_data->authors; ?></p>
                    <?php endif; ?>
                    <p>Date</p>
                    <?php if($objPublication->content != '') : ?>
                    <p><?php echo $objPublication->content; ?></p>
                    <?php endif; ?>
                </h4>
            </div>
        <?php endforeach; ?>
    </div>
    <p>
        <a href="">Complete publications list</a>
    </p>

    <?php else : ?>
        <?php //should anything go here if they dont have publications? ?>
    <?php endif; ?>
</div>

