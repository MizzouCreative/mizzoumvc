<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/3/14
 * Time: 12:13 PM
 * @todo we should be able to take the hard-coded value (2) at line 34 and move it into a config/theme option area. Then
 * define two column classes: first (alpha) and last (omega).  Set the default column class to empty. If the counter is
 * on 0, reset the column class to first. If we are one less than the number of desired columns (e.g. desired columns is
 * 3, so when the counter hits 2), reset the column class to last.
 */

$strFirstColumnClass = 'alpha';
$strLastColumnClass = 'omega';
$intDesiredColumns = 4;
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
                    <a class="clearfix post-linkl" href="<?php echo $objPost->permalink; ?>">
                        <h3 class="post-title"><?php echo $objPost->title; ?></h3>
                    </a>
                </div>
            </div>
            <!--
            <?php var_export($objPost);?>
            -->
            <?php if($intCounter == $intLastColumn ) : $intCounter = 0;?>
                <div class="clear"></div>
            <?php else : ++$intCounter; ?>
            <?php endif; //endif could be on the line above, but this makes it more readable ?>
        <?php endforeach; ?>
    <?php else : ?>
        <?php // do we need a default message if there are currently no posts? ?>
    <?php endif; ?>
</section>