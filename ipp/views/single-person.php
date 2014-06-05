<?php
/**
 * Inner view file for Policy Area pages
 *
 * From the IA, this view needs to display the following data:
 *  - Person's name (post title)
 *  - Biography (post content)
 *  - All titles
 *  - Phone number
 *  - Fax number
 *  - email address
 *  - Office address
 *  - link to CV
 *  - Research interest
 *  - Selected Publications
 *  - Link to all Publications
 *  - All education
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Post object for the page
 *  - $objPerson customized post object. should have almost everything you need
 *  - $strPublicationArchiveURL the url for the "all publications" link
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
 */
global $objPerson;
?>

<div class="span3 one-third" id="portrait-container">
    <section aria-labelledby="portait" role="img">
        <a href="<?php echo $objPerson->image->src_full; ?>" title="<?php echo $objPerson->image->alt; ?>">
            <span id="portrait-label" class="hidden"><?php echo $objPerson->image->alt; ?></span>
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
            <img src="<?php echo $objPerson->image->src_medium; ?>" alt="<?php echo $objPerson->image->alt; ?>" class="attachment-medium wp-post-image" width="200" height="300">
        </a>
    </section>
</div>

<div class="span4 two-thirds">
    <section aria-label="contact information" role="region">
        <ol class="contact single-person nobullet">
        <?php if(count($objPerson->titles) > 0): ?>
            <?php foreach($objPerson->titles as $strTitle) : ?>
            <li class="job-title"><?php echo $strTitle; ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if('' != $objPerson->phone) : ?>
            <li class="phone"><?php echo $objPerson->phone; ?></li>
        <?php endif; ?>
        <?php if('' != $objPerson->fax) : ?>
            <li class="fax">
                <span class="uncolor">Fax: <?php echo $objPerson->fax; ?></span>
            </li>
        <?php endif; ?>
        <?php if('' != $objPerson->email) : ?>
            <li class="email break">
                <a href="mailto:<?php echo $objPerson->email; ?>"><?php echo $objPerson->email; ?></a>
            </li>
        <?php endif; ?>

        <?php if('' != $objPerson->address1) : ?>
            <li class="office-label">
                <strong>Office</strong>
            </li>
            <li class="address_1"><?php echo $objPerson->address1; ?></li>
            <?php if('' != $objPerson->address2) : ?>
            <li class="address_2"><?php echo $objPerson->address2; ?></li>
            <?php endif; ?>
            <li class="address_2">Columbia, MO 65211</li><?php // 20140528 PFG: is this always hard-coded? ?>
       <?php endif; ?>

       <?php if(count($objPerson->website) > 0) : ?>
           <?php foreach($objPerson->meta_date->website as $strWebsite) : ?>
           <li class="website break">
                <a href="<?php echo $strWebsite; ?>"><?php echo $strWebsite; ?></a>
            </li>
           <?php endforeach; ?>
       <?php endif; ?>

       <?php if(isset($objPerson->curriculumVitaeURL) && '' != $objPerson->curriculumVitaeURL) : ?>
            <li>
                <a href="<?php echo $objPerson->curriculumVitaeURL; ?>">Curriculum Vitae</a>
            </li>
       <?php endif; ?>
        </ol>
    </section>
</div>

<?php
/**
 * 20140530 PFG:
 * similar to below and publications: using span5 is causing the div to drop a line. But 3 + 4 + 5 should equal 12,
 * so i'm not sure what's up... Changing this to span4 until we can figure it out
 */
?>
<div class="span4">
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
    <?php if('' != $objPerson->content) : ?>
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
                    <?php if('' != $objPublication->authors) : ?>
                    <p><?php echo $objPublication->authors; ?></p>
                    <?php endif; ?>
                    <p><?php echo $objPublication->formatted_date; ?></p>
                    <?php if('' != $objPublication->content) : ?>
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
        <a href="<?php echo $strPublicationArchiveURL ?>">Complete publications list</a>
    </p>

    <?php else : ?>
        <?php // 20140528 PFG :should anything go here if they dont have publications? ?>
    <?php endif; ?>
</div>
<!--
Complete ObjPerson
<?php var_export($objPerson); ?>
-->