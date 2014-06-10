<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/9/14
 * Time: 3:03 PM
 */
?>
<div class="span8 alpha">
<?php if($objMainPost->authors != '') : ?>
<p class="authors"><?php echo $objMainPost->authors; ?></p>
<?php endif; ?>
<p class="date"><?php echo $objMainPost->formatted_date; ?></p>
<?php echo $objMainPost->content; ?>

<?php if($objMainPost->link != '') : ?>
<p><a href="<?php echo $objMainPost->link; ?>">View this publication at <?php echo $objMainPost->link; ?></a></p>
<?php endif; ?>

<?php if($strMorePublications != '') : ?>
<p>More publications from <?php echo $strMorePublications; ?></p>
<?php endif; ?>
</div>