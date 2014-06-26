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

$strPreBreadcrumbDivContents = ($boolIncludeSidebar) ? 'flex span8' : 'span12';
/**
 * Does the H1 need a class?
 */
$boolH1Class = false;
/**
 * Does the page contain a sidebar? If so, what class(es) should the content well contain? Otherwise, what class should
 * it contain?
 *
 * @var  $strPreBreadcrumbDivContents
 */
?>

<div class="<?php echo $strPreBreadcrumbDivContents; ?>">
    <?php echo $strBreadCrumbs; ?>
<?php if(!$boolIncludeSidebar) : // we need to end the div if we didnt have a sidebar ?>
    </div> <!-- end span12 -->
<?php endif; ?>
    <main id="main" role="main">
        <div id="content">
            <article role="article">
                <?php if($objMainPost->post_type == 'page' && $objMainPost->image != '') : $strH1Class = 'featured'; ?>
                    <div class="featured-image-wrapper">
                        <img src="<?php echo $objMainPost->image->src_full; ?>" alt="<?php $objMainPost->image->alt; ?>" />
                    </div>
                <?php endif;?>
                <header>
                    <h1 id="title"<?php if($boolH1Class): ?> class="featured"<?php endif; ?>><?php echo $strPageTitle; if($strEditPostLink != '') :?> <a href="<?php echo $strEditPostLink; ?>" class="post-edit-link">Edit</a><?php endif; ?></h1>
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
<?php if($boolIncludeSidebar) : // if we did have a sidebar, the breadcrumbs are contained within the same div as the main content ?>
</div> <!-- end flex span7 -->
<?php endif; ?>