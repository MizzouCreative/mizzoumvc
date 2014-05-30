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
        <a href="<?php echo $objPerson->meta_data->image->src_full; ?>" title="<?php echo $objPerson->meta_data->image->alt; ?>">
            <span id="portrait-label" class="hidden"><?php echo $objPerson->meta_data->image->alt; ?></span>
            <?php
            /**
             * 20140530 PFG:
             * On truman, width and height are set to 400 X 600, but it also has a version of the image that matches
             * those dimensions.  Right now on IPP, medium is set to 200 X 300 and large is set to 682 X 1024. Do we
             * need to alter the medium/large settings? Or do we need a custom image size? For now, I've set the
             * width/height to match the medium-sized dimensions
             *
             * @todo change width height, and/or alter settings in IPP
             */
            ?>
            <img src="<?php echo $objPerson->meta_data->image->src_medium; ?>" alt="<?php echo $objPerson->meta_data->image->alt; ?>" class="attachment-medium wp-post-image" width="200" height="300">
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
            <li class="address_2">Columbia, MO 65211</li><?php // 20140528 PFG: is this always hard-coded? ?>
       <?php endif; ?>

       <?php if(count($objPerson->meta_data->website) > 0) : ?>
           <?php foreach($objPerson->meta_date->website as $strWebsite) : ?>
           <li class="website break">
                <a href="<?php echo $strWebsite; ?>"><?php echo $strWebsite; ?></a>
            </li>
           <?php endforeach; ?>
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
    <?php // 20140528 PFG: should anything go here if they dont have research interests? ?>
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
<?php
/**
 * 20140530 PFG:
 * I had the span set to 5 like truman, but doing so caused it be pushed down below the biography div. span6 + 1 offset
 * + span 5 should equal 12, so I'm not sure what the problem is.
 */
?>
<div class="span4">
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
                    <p><?php echo $objPublication->formatted_date; ?></p>
                    <?php if($objPublication->content != '') : ?>
                    <?php
                        /**
                         * 20140530 PFG:
                         * Do we want content_raw here? using content brings in the formatted version with <p> included.
                         * Removed the <p></p> surrounding content for now
                         */
                        echo $objPublication->content; ?>
                    <?php endif; ?>
                </h4>
            </div>
        <?php endforeach; ?>
    </div>
    <p>
        <a href="">Complete publications list</a>
    </p>

    <?php else : ?>
        <?php // 20140528 PFG :should anything go here if they dont have publications? ?>
    <?php endif; ?>
</div>

<xmp>
    <?php
    var_export($objPerson);
    ?>
</xmp>