<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/16/14
 * Time: 2:16 PM
 */

?>
<?php if(isset($aryProjects) && is_array($aryProjects) && count($aryProjects) > 0) :?>
    <?php if (isset($strTitle) && $strTitle != ''): ?>
    <h4><?php echo $strTitle; ?></h4>
    <?php endif; ?>
    <ul>
        <?php foreach($aryProjects as $objProject): ?>
            <li>
                <a href="<?php echo $objProject->permalink; ?>" title="Link to <?php echo $objProject->title; ?>"><?php echo $objProject->title; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (isset($strProjectArchiveURL) && $strProjectArchiveURL != '') :?>
    <p><a href="<?php echo $strProjectArchiveURL; ?>" title="Link to list of all projects">All Projects</a> </p>
    <?php endif;?>
<?php endif;