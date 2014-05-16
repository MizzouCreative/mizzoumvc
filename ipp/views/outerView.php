<?php
/**
 * View file used to render the containing structure around the majority of pages in the site
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paaul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @uses is_user_logged_in()
 * @uses comments_template()
 * @todo move function calls out of this view
 */
?>
<div class="flex span7">
<?php
    echo $strBreadCrumbs;
    echo $strInnerViewContent;
    //<div class="clear"></div>
    if (is_user_logged_in()) {
        comments_template();
    }
?>
</div> <!-- end flex span7