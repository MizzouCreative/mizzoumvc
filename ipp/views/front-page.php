<?php
/**
 * Template file used to render the Site Front Page, whether the front page
 * displays the Blog Posts Index or a static page. The Front Page template takes
 * precedence over the Blog Posts Index (Home) template.
 *
 * objSlide
 * aryWidgets
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category template
 * @author Paul Gilzow   , Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
?>
    <main id="main" role="main">

        <article role="article">

            <div class="span8">
                
                <?php if($objSlide != '') : ?>
           
                    <div class="slide" style="background-image: url('<?php echo $objSlide->image->src_large; ?>')">
     
                        <?php if ($objSlide->link != '') : ?>
                            <a class="clearfix" href="<?php echo $objSlide->link; ?>">
                        <?php endif; ?>
                         
                        <div class="<?php echo $objSlide->text_width; ?> alpha">
    
                            <div class="slide-text <?php echo $objSlide->text_position; ?>">
                                <div class="slide-content">
                                    <?php echo $objSlide->content; ?>
                                </div>
                            </div>
                        
                        </div>
                        
                        <?php if ($objSlide->link != '') : ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div><!-- end .slide  -->
            </div><!-- end .span8 -->

            <div class="span4">
            <?php if(count($aryWidgets) > 0) : ?>
                <?php foreach($aryWidgets as $strWidget) : ?>
                    <?php echo $strWidget; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>


        </article>

    </main>

    <div class="clear"></div>