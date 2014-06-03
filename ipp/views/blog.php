<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/3/14
 * Time: 12:13 PM
 */

/**
 * If we are on the first colum (key==0), the class should be alpha, nothing if we are on the second, and omega if we
 * are on the third column
 */
$aryColClasses = array(
    0=>'alpha',
    1=>'',
    2=>'omega'
);
?>

<section aria-label="content" role="region">
    <?php if(count($aryPosts) > 0) : $intCounter = 1; ?>
        <?php foreach($aryPosts as $objPost) : ?>
            <div class="span3<?php echo $aryColClasses[$intCounter]; ?>">
                <div class="post-item">
                    <a class="clearfix post-linkl" href="<?php $objPost->permalink; ?>">
                        <h3 class="post-title"><?php echo $objPost->title; ?></h3>
                    </a>
                </div>
            </div>
            <?php if(2 == $intCounter ) : $intCounter = 0;?>
                <div class="clear"></div>
            <?php else : ++$intCounter; ?>
            <?php endif; //endif could be on the line above, but this makes it more readable ?>
        <?php endforeach; ?>
    <?php else : ?>
        <?php // do we need a default message if there are currently no posts? ?>
    <?php endif; ?>
</section>