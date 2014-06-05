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
            <?php if('' != $objPublication->excerpt) : ?>
                <p></p><?php echo $objPublication->content; ?></p>
            <?php endif; ?>
        </h4>
    </div>
<?php endforeach; ?>