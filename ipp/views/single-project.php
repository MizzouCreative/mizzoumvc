<?php
/**
 * Template file used to render a single project
 *
 * NOTE - the html structure was copied from /plugins/mizzou-projects/templates/single-project.php
 *
 * Has access to the following variables
 *  - objMainPost Mizzou project object for main post
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
    <?php if($objMainPost->authors != '') : ?>
        <p class="authors"><?php echo $objMainPost->authors; ?></p>
    <?php endif; ?>
    <p class="date"><?php echo $objMainPost->formatted_date; ?></p>
    <?php echo $objMainPost->content; ?>

    <?php if($objMainPost->link != '') : ?>
        <p><a href="<?php echo $objMainPost->link; ?>">View this project at <?php echo $objMainPost->link; ?></a></p>
    <?php endif; ?>
</div>

