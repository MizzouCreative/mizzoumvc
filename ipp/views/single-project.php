<?php
/**
 * Inner view file used to render a single project
 *
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
    <?php echo $objMainPost->content; ?>
</div>

