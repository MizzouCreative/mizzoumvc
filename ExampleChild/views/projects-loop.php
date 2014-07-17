<?php
/**
 * View file for looping through project listings
 *
 * From the IA, this view needs to display the following data:
 *  - Project name (post title)
 *  - Permalink to project profile page
 *
 * This view has access to the following variables:
 *  - $aryProjects array of Project post objects
 *  - $strTitle (if applicable) the type of projects that will be listed
 *  - $strProjectArchiveURL (if applicable) permalink to the archive page for projects of a specific type
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
 * @todo include a list of upper views that use this file
 *
 */

?>
<?php if(isset($aryProjects) && is_array($aryProjects) && count($aryProjects) > 0) :?>
    <?php if (isset($strTitle) && $strTitle != ''): ?>
    <h4><?php echo $strTitle; ?></h4>
    <?php endif; ?>
    <ul>
        <?php foreach($aryProjects as $objProject): ?>
            <li>
                <a href="<?php echo $objProject->permalink; ?>" title="Link to <?php echo $objProject->title; ?>"><?php echo $objProject->title; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (isset($strProjectArchiveURL) && $strProjectArchiveURL != '') :?>
    <p><a href="<?php echo $strProjectArchiveURL; ?>" title="Link to list of all projects">All Projects</a> </p>
    <?php endif;?>
<?php endif;