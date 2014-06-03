<?php
/**
 * View file used to render the list of blog posts
 *
 * Has access to the following variables
 *  - aryPosts array of Mizzou post objects
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category view
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @todo $intDesiredColumns and $intLastColumn need to be moved up into the config/theme options. We could also move
 * $strFirstColumnClass and $strLastColumnClass up but I'm inclined to leave them here since they are soley the domain
 * of the designer.
 */

$strFirstColumnClass = 'alpha';
$strLastColumnClass = 'omega';
$intDesiredColumns = 3;
$intLastColumn = $intDesiredColumns - 1;
?>

<section aria-label="content" role="region">
    <?php if(count($aryPosts) > 0) : $intCounter = 0; ?>
        <?php foreach($aryPosts as $objPost) : $strColumnClass = ''; ?>
            <?php
                if($intCounter == 0) {
                    $strColumnClass = $strFirstColumnClass;
                } elseif($intCounter == $intLastColumn) {
                    $strColumnClass = $strLastColumnClass;
                }
            ?>
            <div class="span3 <?php echo $strColumnClass; ?>">
                <div class="post-item">
                    <a class="clearfix post-link" href="<?php echo $objPost->permalink; ?>">
                        <h3 class="post-title"><?php echo $objPost->title; ?></h3>
                    </a>
                </div>
            </div>
            <?php if($intCounter == $intLastColumn ) : $intCounter = 0;?>
                <div class="clear"></div>
            <?php else : ++$intCounter; ?>
            <?php endif; //endif could be on the line above, but this makes it more readable ?>
        <?php endforeach; ?>
    <?php else : ?>
        <?php // do we need a default message if there are currently no posts? ?>
    <?php endif; ?>
</section>