<?php
/**
 * Inner view file for Project archive page
 *
 *
 * To facilitate this, you have access to the following variables:
 *  - $strLoopContent
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
 * $strLoopContent comes from views/projects-loop
 */
?>
            <section aria-label="content" role="region">
                <?php //replace $strLoopContent with a require to the correct view file ?>
                <?php echo $strLoopContent; ?>
            </section>

