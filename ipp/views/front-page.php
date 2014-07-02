<?php
/**
 * Template file used to render the Site Front Page, whether the front page
 * displays the Blog Posts Index or a static page. The Front Page template takes
 * precedence over the Blog Posts Index (Home) template.
 *
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category template
 * @author Charlie Triplett, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>
    <main id="main" role="main">

        <article role="article">

            <div class="span4">

            </div>

            <div class="span8">
            <?php if($objSlide != '') : ?>
                <div class="slide clearfix" style="background-image: url('<?php echo $objSlide->image->src_large; ?>')">

                    <?php if ($objSlide->link != '') : ?>
                    <a class="clearfix" href="<?php echo $objSlide->link; ?>">
                     <?php endif; ?>

                    <div class="slide-text">
                        <h2><?php echo $objSlide->title; ?></h2>
                        <div class="slide-content">
                            <?php echo $objSlide->content; ?>
                        </div>
                    </div>
                    <?php if ($objSlide->link != '') : ?>
                    </a>
                    <?php endif; ?>
                </div> <!-- end slide -->
            <?php endif; ?>

            </div><!-- end .span8 -->

            <div class="span4">
                <?php dynamic_sidebar( 'primary-widget' ); ?>

                <?php dynamic_sidebar('home_right')  ?>
            </div>


        </article>

    </main>

    <div class="clear"></div>