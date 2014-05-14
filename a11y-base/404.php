<?php
/**
 * Template file used to render a Server 404
 * 
 * In addition to serving the 404 header and notification, will automatically 
 * perform a search based on the non-existant URL. Change the html structure 
 * below as needed.
 *
 * @package WordPress
 * @subpackage mizzou-news
 * @since MIZZOU News 0.1
 * @category theme
 * @category template
 * @uses class-customPostData
 * @author Paul F. Gilzow & Jason L Rollins, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */



$arySearchParams = unserialize(GSA_SEARCH_PARAMS); 
$arySearchParams['q'] = mizzou_gather_404_search_terms();

$strResults = file_get_contents(GSA_SEARCH_URL.http_build_query($arySearchParams));
     
$boolNoResults = (!isset($strResults) || $strResults == '') ? true : false; 
get_header();
 ?>
 	

        <div id="default" class="span7">

			<?php if (function_exists('breadcrumbs')) breadcrumbs(); ?>
        
            <?php get_template_part('aside','example'); ?>
                    <h2>Not Found</h2>
                    <p>
                        The requested URL <?php echo htmlentities($_SERVER['REQUEST_URI'],ENT_QUOTES,'UTF-8');?> was not found on this site.  Here are the closest matches 
                        I was able to find:
                    </p>

                    <?php if($boolNoResults): ?>
                        <?php get_search_form(); ?>
                    <?php else :?>
                        <?php echo $strResults;?>
                    <?php endif;?>
        </div><!-- #default -->
<?php get_footer(); ?>

