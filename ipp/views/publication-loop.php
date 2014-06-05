<?php
/**
 * Created by PhpStorm.
 * User: gilzow
 * Date: 6/3/14
 * Time: 4:16 PM
 */
?>
<?php foreach($aryPublications as $objPublication) : ?>
    <div class="publication-item">
        <h4>
            <a title="<?php echo $objPublication->title; ?>" rel="bookmark" href="<?php echo $objPublication->permalink; ?>"><?php echo $objPublication->title; ?></a>
            <?php if('' != $objPublication->authors) : ?>
                <p><?php echo $objPublication->authors; ?></p>
            <?php endif; ?>
            <p><?php echo $objPublication->formatted_date; ?></p>
            <?php if('' != $objPublication->content) : ?>
                <?php
                /**
                 * 20140530 PFG:
                 * Do we want content_raw here? using content brings in the formatted version with <p> included.
                 * Removed the <p></p> surrounding content for now
                 */
                echo $objPublication->content; ?>
            <?php endif; ?>
        </h4>
    </div>
<?php endforeach; ?>