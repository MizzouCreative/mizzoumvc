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

<div class="span7 right-offset1">
    <section aria-label="content" role="region">
        <div class="policy-area-description">
            <?php echo $objMainPost->content; ?>
        </div>
    </section>
    
    <div class="left-half">
        <?php if (count($aryRelatedProjects) > 0) : ?>
            <section>
                <h3 class="projects">Related Projects</h3>
                <ul class="portfolio-list nobullet">
                    <?php foreach ($aryRelatedProjects as $objProject) : ?>
                        <li><a href="<?php echo $objProject->permalink; ?>" title="Link to <?php echo $objProject->title; ?>"><?php echo $objProject->title; ?></a></li>
                    <?php endforeach;?>
                    <li><a class="archive-link" href="<?php echo $strProjectArchiveURL; ?>" title="Link to all <?php echo $objMainPost->title; ?> Projects">All <?php echo $objMainPost->title; ?> Projects</a> </li>
                </ul>
            </section>
        <?php endif; ?>
    </div><!-- end span4 -->
    
    <div class="right-half">
        <?php if (count($aryRelatedPublications) > 0) : ?>
            <section>
                <h3 class="publications">Related Publications</h3>
                <ul class="portfolio-list nobullet">
                    <?php foreach ($aryRelatedPublications as $objPublication) : ?>
                        <li><a href="<?php echo $objPublication->permalink; ?>" title="Link to <?php echo $objPublication->title; ?>"><?php echo $objPublication->title; ?></a></li>
                    <?php endforeach;?>
                    <li><a class="archive-link" href="<?php echo $strPublicationArchiveURL; ?>" title="Link to all <?php echo $objMainPost->title; ?> Publications">All <?php echo $objMainPost->title; ?> Publications</a> </li>
                </ul>
            </section>
        <?php endif; ?>
    
    </div> <!-- end span4 -->
            
</div>


<div class="span4 pad gray">
                
    <?php if(isset($objMainContact) && is_object($objMainContact)): ?>
        <section>
            <div class="clearfix">
	            <h3>Contact</h3>
	            <?php if($objMainContact->image != '') { ?>
	                <div class="span1 alpha one-third portrait<?php echo ' ',$strFirstColClass; ?>">
	                    <a href="<?php echo $objMainContact->permalink; ?>" rel="bookmark" title="<?php echo $objMainContact->title; ?>">
	                        <img src="<?php echo $objMainContact->image->src_medium; ?>" width="static?" height="static?" alt="<?php echo $objMainContact->image->alt; ?>">
	                    </a>
	                </div>
	                <div class="span3 two-thirds omega">
	            <?php } else { ?>
	                <div class="span4 alpha">
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
            </div>
        </section>
    <?php endif; ?>
    
  <?php if(isset($aryPolicyScholars) && count($aryPolicyScholars) > 0): ?>
  <hr/>
  <section>
      <h3>Policy Research Scholars</h3>
      <ul class="nobullet">
          <?php foreach($aryPolicyScholars as $objScholar): ?>
              <li><a href="<?php echo $objScholar->permalink; ?>" title="Link to <?php echo $objScholar->title; ?> profile"><?php echo $objScholar->title; ?></a></li>
          <?php endforeach; ?>
      </ul>
  </section>
  <?php endif;?>

</div><!-- end span3 -->
