<?php
/**
 * View file used to render the containing structure around the majority of pages in the site
 *
 * Has access to the following variables
 *  - objMainPost
 *  - strPageTitle
 *  - strEditPostLink
 *  - strInnerViewContent
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @uses is_user_logged_in()
 * @uses comments_template()
 * @todo move function calls out of this view
 */
?>
<?php if($boolIncludeSidebar) : ?>
<div class="flex span7">
<?php else: ?>
    <div class="span12">
<?php endif; ?>
    <?php echo $strBreadCrumbs; ?>
<?php if(!$boolIncludeSidebar) : ?>
    </div> <!-- end span12 -->
<?php endif; ?>
    <main id="main" role="main">
        <div id="content">
            <article role="article">
                <header>
                    <h1 id="title"><?php echo $strPageTitle; if($strEditPostLink != '') :?> <a href="<?php echo $strEditPostLink; ?>" class="post-edit-link">Edit</a><?php endif; ?></h1>
                </header>
                <?php echo $strInnerViewContent;?>
            </article>
        </div>
    </main>


    <?php
        if (is_user_logged_in()) {
            comments_template();
        }
    ?>
<?php if($boolIncludeSidebar) : ?>
</div> <!-- end flex span7 -->
<?php endif; ?>