<?php
/**
 * View file for looping through publication listings
 *
 * From the IA, this view needs to display the following data:
 *  - Publication name (post title)
 *  - Permalink to project profile page
 *  - Authors
 *  - Publication excerpt
 *
 * This view has access to AND REQUIRES the following variable be passed to it:
 *  - $aryProjects array of Project post objects
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
 */
?>
<?php foreach($aryPublications as $objPublication) : ?>
    <div class="publication-item">
        <h4>
            <a title="<?php echo $objPublication->title; ?>" rel="bookmark" href="<?php echo $objPublication->permalink; ?>"><?php echo $objPublication->title; ?></a>
            <?php if('' != $objPublication->authors) : ?>
                <p><?php echo $objPublication->authors; ?></p>
            <?php endif; ?>
            <p><?php echo $objPublication->formatted_date; ?></p>
            <?php if('' != $objPublication->excerpt) : ?>
                <p></p><?php echo $objPublication->excerpt; ?></p>
            <?php endif; ?>
        </h4>
    </div>
<?php endforeach; ?>