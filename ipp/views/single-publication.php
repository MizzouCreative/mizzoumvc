<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/9/14
 * Time: 3:03 PM
 */
?>

<?php if($objMainPost->authors != '') : ?>
<p class="authors"><?php echo $objMainPost->authors; ?></p>
<?php endif; ?>
<p class="date"><?php echo $objMainPost->formatted_date; ?></p>
<?php echo $objMainPost->content; ?>
<!--
objMainPost
<?php var_export($objMainPost); ?>
-->
