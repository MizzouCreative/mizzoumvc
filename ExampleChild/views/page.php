<?php
/**
 * Inner view file for general pages
 *
 *
 * Has access to the following variables:
 *  - $objMainPost Mizzou Post object for the page
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
?>
<section aria-label="content" role="region">

            <?php echo $objMainPost->content; ?>

</section>