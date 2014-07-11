<?php
/**
 * View file used to render a single blog post
 *
 * Has access to the following variables
 *  - objMainPost Mizzou post object for main post
 *  - aryRelatedPosts array of related Mizzou post objects
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @todo $intDesiredColumns and $intLastColumn need to be moved up into the config/theme options. We could also move
 * $strFirstColumnClass and $strLastColumnClass up but I'm inclined to leave them here since they are solely the domain
 * of the designer.
 */
?>
<section>
    <?php echo $objMainPost->content; ?>

    <?php if($objMainPost->alternateLink != ''): ?>
        <a class="clearfix post-link" href="<?php echo $objMainPost->alternateLink; ?>"><?php echo $objMainPost->alternateLink; ?></a>
    <?php endif; ?>
</section>

<footer>
    <p class="postmetadata">Published
        <time datetime="<?php echo $objMainPost->iso8601_date; ?>" pubdate>
            <?php echo $objMainPost->formatted_date; ?>
        </time>
    </p>
</footer>

</article>

<?php if(count($aryRelatedPosts) > 0) : ?>

        <ol class="skip-links">
            <li><a class="hidden skip-to-content" href="#main"><span class="text">Skip to content</span></a></li>
        </ol>

        <h3>Related News</h3>
        <ul>
            <?php foreach($aryRelatedPosts as $objRelatedPost) : ?>
            <li><a href="<?php echo $objRelatedPost->permalink; ?>"><?php echo $objRelatedPost->title; ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif;