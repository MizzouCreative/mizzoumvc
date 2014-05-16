<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/16/14
 * Time: 2:16 PM
 */

?>
    <?php if (isset($strTitle) && $strTitle != ''): ?>
    <h4><?php echo $strTitle; ?></h4>
    <?php endif; ?>
    <ul>
        <?php foreach($aryProjects as $objProject): ?>
            <li>
                <a href="<?php $objProject->permalink; ?>" title="Link to <?php $objProject->title; ?>"><?php $objProject->title; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
