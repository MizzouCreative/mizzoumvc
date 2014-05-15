<?php
/**
 * From the IA, this view needs to display the following data:
 *  - body content has entered into wordpress for this page
 *  - Related Publications
 *  - Related Projects
 *  - Main Staff Contact
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Post object for the page
 *  - $aryRelatedPublications list/array of Publication Post objects that match
 *  - $aryRelatedProjects list/array of Project Post objects that match
 *  - $objMainContact Person Post object that matches
 *
 * You do not need to call get_header() and get_footer
 */
?>
<div class="flex span7">
    <?php breadcrumbs(); //@todo this needs to moved out of here. REFACTOR. ?>
    <main id="main" role="main">
        <div id="content">
            <article role="article">
                <header>
                    <h1 id="title"><?php echo $objMainPost->title; ?></h1>
                </header>
                <section aria-label="content" role="region">
                    <?php echo $objMainPost->content; ?>
                </section>



                <?php if (count($aryRelatedPublications) > 0) : ?>
                    <section>
                        <h3>Related Publications:</h3>
                        <ul>
                            <?php foreach ($aryRelatedPublications as $objPublication) : ?>
                                <li><a href="<?php echo $objPublication->permalink; ?>" title="Link to <?php echo $objPublication->title; ?>"><?php echo $objPublication->title; ?></a></li>
                            <?php endforeach;?>
                        </ul>
                    </section>
                <?php endif; ?>

                <?php if (count($aryRelatedProjects) > 0) : ?>
                    <section>
                        <h3>Related Projects:</h3>
                        <ul>
                            <?php foreach ($aryRelatedProjects as $objProject) : ?>
                                <li><a href="<?php echo $objProject->permalink; ?>" title="Link to <?php echo $objProject->title; ?>"><?php echo $objProject->title; ?></a></li>
                            <?php endforeach;?>
                        </ul>
                    </section>
                <?php endif; ?>

                <?php if(isset($objMainContact) && is_object($objMainContact)): ?>
                    <section>
                        <h3><?php echo $objMainPost->title; ?> Contact:</h3>
                        <p>
                            Name: <?php echo $objMainContact->title; ?><br>
                            Title: <?php echo $objMainContact->meta_data->title1; ?><br>
                            Address: <?php echo $objMainContact->meta_data->address1; ?><br>
                            Email: <a href="mailto:<?php echo $objMainContact->meta_data->email; ?>"><?php echo $objMainContact->meta_data->email; ?></a><br>
                            Phone: <?php echo $objMainContact->meta_data->phone;?>
                        </p>
                    </section>
                <?php endif; ?>

            </article>
        </div>
    </main>
</div>
<p>Main Staff Contact:</p>
<pre>
<?php print_r($objMainContact); ?>
</pre>