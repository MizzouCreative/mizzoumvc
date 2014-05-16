<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 5/16/14
 * Time: 3:36 PM
 */
?>

<main id="main" role="main">
    <div id="content">
        <article role="article">
            <header>
                <h1 id="title"><?php echo $strPageTitle, ' ',edit_post_link('Edit'); ?></h1>
            </header>
            <section aria-label="content" role="region">
                <?php echo $strLoopContent; ?>
            </section>
        </article>
    </div>
</main>
