<?php
/**

Template Name: Course Descriptions

 * Template file used to render a static page
 * 
 *
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template
 * @author Charlie Triplett, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>

<?php get_header(); ?>

<?php get_sidebar(); ?>

<div class="flex span7">

	<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>

	<main id="main" role="main">
	
		<div id="content">
		
			<article role="article"> 
		
				<?php if (have_posts()) : while (have_posts()) : the_post();?>

					<header>
					    <h1 id="title"><?php the_title(); ?> <?php edit_post_link('Edit'); ?></h1>
					</header>
					
					<section role="region" aria-label="content">
						<?php the_content(); ?>

	                	<? $subject = get_post_meta($post->ID, 'subject', true); // get custom field data ?>
					
						<? include 'course-grabber.php'; // include ADN script ?>
				        <? $DeptObject = new DepartmentCourseInfoWSObject(); ?>
				        <? $xml = $DeptObject->getUniqueCourseInformation_XML('',$subject); // course info based on custom field value  ?>

				
					<? if (!is_object($xml) || $xml == 'invalid passcode') { ?>
						<h2>Oh SNAP! Invalid passcode.</h2>>
											
					<? } else { ?>
					
					<? if ($xml->attributes()->rowcount == 0) { ?>
						<h2>Oh SNAP! MyZou isn't returning any data.<br />

					<? } else { ?>

					<h2>Undergraduate</h2>
			
			        <ul class="course-descriptions">
			            <?php 
		                    foreach($xml->item as $item) { 
		                        if ($item->catalognumber >= 1000 && $item->catalognumber <= 7000 ) { 
		                        ?>
		                        <li><?php echo $item->title; ?>
		                            <ul>
		                                <li>
		                                    <?php echo $item->subject; ?> <?php echo $item->catalognumber; ?> | <?php echo $item->hours; ?> Credit Hours
		                                </li>
		                                <li>
		                                    <?php echo $item->description ?>
		                                </li>
		                            </ul>
		                        </li>
		                        <?php 
		                        } // end if course number sort 
		                    } //end foreach 
		                ?>
			        </ul>
			
			        <? if ($item->catalognumber >= 7000) { // some programs do not have graduate courses ?>
			            <h2>Graduate</h2>
			            <?php } // end if ?>
			        <ul class="course-descriptions">
			            <?php 
		                    foreach($xml->item as $item) { 
		                        if ($item->catalognumber >= 7000) { 
		                        ?>
		                        <li><?php echo $item->title; ?>
		                            <ul>
		                                <li>
		                                    <?php echo $item->subject; ?> <?php echo $item->catalognumber; ?> | <?php echo $item->hours; ?> Credit Hours
		                                </li>
		                                <li>
		                                    <?php echo $item->description ?>
		                                </li>
		                            </ul>
		                        </li>
		                        <?php 
		                        } // end if course number sort 
		                    } //end foreach 
		                ?>
			        </ul>
			
			        <? } // end rowcount if ?>
			    <? } // end password nothing returned if ?>
			

					</section>					
					
				<?php endwhile; endif;?>
		
			</article>
					
		</div> <!-- #content -->

	</main>


<?php // If comments are open or we have at least one comment, load up the comment template.
if ( is_user_logged_in() ) {
	comments_template();
} ?>

</div> <!-- end .eight .spans -->


<?php get_footer(); ?>