<?php
/**
 * Inner view file for Policy Area pages
 *
 * From the IA, this view needs to display the following data:
 *  - body content has entered into wordpress for this page
 *  - Related Publications
 *  - Link to all Publications
 *  - Related Projects
 *  - Link to all Projects
 *  - Main Staff Contact for a Policy Area
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Post object for the page
 *  - $aryRelatedPublications list/array of Publication Post objects that match
 *  - $strPublicationArchiveURL the url for the "more publications" link
 *  - $aryRelatedProjects list/array of Project Post objects that match
 *  - $strProjectArchiveURL the url for the "more projects" link
 *  - $objMainContact Person Post object that matches for the policy area
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
 */
?>

    <div class="span8">
                <?php if($objMainPost->image != ''){ ?>
                    <img src="<?php echo $objMainPost->image->src_large; ?>" width="static?" height="static?" alt="<?php echo $objMainPost->image->alt; ?>">
                <?php } ?>

                <section aria-label="content" role="region">
                    <?php echo $objMainPost->content; ?>
                </section>
    </div>
    
    <div class="span3 left-offset1 ">
                    
                    <?php if(isset($objMainContact) && is_object($objMainContact)): ?>
                        <section>
                            <h3>Contact:</h3>
                            <?php if($objMainContact->image != '') { ?>
                                <div class="span1 alpha one-third portrait<?php echo ' ',$strFirstColClass; ?>">
                                    <a href="<?php echo $objMainContact->permalink; ?>" rel="bookmark" title="<?php echo $objMainContact->title; ?>">
                                        <img src="<?php echo $objMainContact->image->src_medium; ?>" width="static?" height="static?" alt="<?php echo $objMainContact->image->alt; ?>">
                                    </a>
                                </div>
                                <div class="span2 two-thirds omega">
                            <?php } else { ?>
                                <div class="span3 alpha">
                            <?php } ?>
                    
                                <ol class="contact nobullet">
                                    <li class="name"><a href="<?php echo $objMainContact->permalink; ?>"><?php echo $objMainContact->title; ?></a></li>
        
                                    <?php if('' != $objMainContact->title1): ?>
                                        <li class="job-title"><?php echo $objMainContact->title1; ?></li>
                                    <?php endif; ?>
        
                                    <?php if('' != $objMainContact->address1): ?>
                                        <li class="address"><?php echo $objMainContact->address1; ?></li>
                                    <?php endif; ?>
        
                                    <?php if('' != $objMainContact->email): ?>
                                        <li class="email"><a href="mailto:<?php echo $objMainContact->email; ?>"><?php echo $objMainContact->email; ?></a></li>
                                    <?php endif; ?>
        
                                    <?php if('' != $objMainContact->phone): ?>
                                        <li class="phone"><?php echo $objMainContact->phone;?></li>
                                    <?php endif; ?>
        
                                </ol>
                            </div>
                        </section>
                    <?php endif; ?>
    </div><!-- end span3 -->
    
    <div class="clear"></div>

    <div class="span4">
                    <?php if (count($aryRelatedProjects) > 0) : ?>
                        <section>
                            <h3>Related Projects:</h3>
                            <ul>
                                <?php foreach ($aryRelatedProjects as $objProject) : ?>
                                    <li><a href="<?php echo $objProject->permalink; ?>" title="Link to <?php echo $objProject->title; ?>"><?php echo $objProject->title; ?></a></li>
                                <?php endforeach;?>
                            </ul>
                            <p><a href="<?php echo $strProjectArchiveURL; ?>" title="Link to all <?php echo $objMainPost->title; ?> Projects">All <?php echo $objMainPost->title; ?> Projects</a> </p>
                        </section>
                    <?php endif; ?>
    </div><!-- end span4 -->
    
    <div class="span4">
                    <?php if (count($aryRelatedPublications) > 0) : ?>
                        <section>
                            <h3>Related Publications:</h3>
                            <ul>
                                <?php foreach ($aryRelatedPublications as $objPublication) : ?>
                                    <li><a href="<?php echo $objPublication->permalink; ?>" title="Link to <?php echo $objPublication->title; ?>"><?php echo $objPublication->title; ?></a></li>
                                <?php endforeach;?>
                            </ul>
                            <p><a href="<?php echo $strPublicationArchiveURL; ?>" title="Link to all <?php echo $objMainPost->title; ?> Publications">All <?php echo $objMainPost->title; ?> Publications</a> </p>
                        </section>
                    <?php endif; ?>
    
    </div> <!-- end span4 -->


    <div class="span3 left-offset1">
                <?php if(isset($aryPolicyScholars) && count($aryPolicyScholars) > 0): ?>
                <section>
                    <h3><?php echo $objMainPost->title; ?> Policy Research Scholars</h3>
                    <ul>
                        <?php foreach($aryPolicyScholars as $objScholar): ?>
                            <li><a href="<?php echo $objScholar->permalink; ?>" title="Link to <?php echo $objScholar->title; ?> profile"><?php echo $objScholar->title; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif;?>
    </div> <!-- end span3 -->
