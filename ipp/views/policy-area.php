<?php
/**
 * Inner view file for Policy Area pages
 *
 * From the IA, this view needs to display the following data:
 *  - body content has entered into wordpress for this page
 *  - Related Publications
 *  - Link to all Publications
 *  - Related Projects
 *  - Link to all Projects
 *  - Main Staff Contact for a Policy Area
 *
 * To facilitate this, you have access to the following variables:
 *  - $objMainPost Post object for the page
 *  - $aryRelatedPublications list/array of Publication Post objects that match
 *  - $strPublicationArchiveURL the url for the publication CPT archive
 *  - $aryRelatedProjects list/array of Project Post objects that match
 *  - $strProjectArchiveURL the url for the project CPT archive
 *  - $objMainContact Person Post object that matches
 *
 * You do not need to call get_header(), get_footer, get_sidebar() or breadcrumbs() as those are handled by outer
 * functions.
 */
?>
    <main id="main" role="main">
        <div id="content">
            <article role="article">
                <header>
                    <h1 id="title"><?php echo $objMainPost->title, ' ',edit_post_link('Edit'); ?></h1>
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
                        <p><a href="<?php echo $strPublicationArchiveURL; ?>" title="Link to all Publications">All Publications</a> </p>
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
                        <p><a href="<?php echo $strProjectArchiveURL; ?>" title="Link to all Projects">All Projects</a> </p>
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
    </main>s