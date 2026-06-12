<main id="content" class="tf_clearfix">
<?php
$cat = get_queried_object();
if ($cat) : ?>
    <h1 itemprop="name" class="page-title"><?= $cat->name ?></h1>
    <?php if ($cat->description) : ?>
    <div class="category-description"><p><?= $cat->description ?></p></div>
    <?php endif; ?>
<?php endif; ?>

<?php if(have_posts()): ?>
    <div id="loops-wrapper" class="loops-wrapper grid3 tf_clear tf_clearfix">
<?php while(have_posts()):the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post tf_clearfix'); ?>>

    <figure class="post-image tf_clearfix">
        <a href="<?= get_the_permalink() ?>" data-wpel-link="internal">
            <img data-tf-not-load="1" decoding="async" src="<?= get_the_post_thumbnail_url() ?>" title="<?= get_the_title() ?>" alt="<?= get_the_title() ?>">
        </a>
    </figure>

    <div class="post-content">

        <h2 class="post-title entry-title"><a href="<?= get_the_permalink() ?>" data-wpel-link="internal"><?= get_the_title() ?></a></h2>

        <div class="entry-content">
            <?php the_content(); ?>
        </div><!-- /.entry-content -->

    </div>
    <!-- /.post-content -->

</article>
<!-- /.post -->
<?php endwhile; ?>
    </div><!-- /.loops-wrapper -->

<?php wp_pagenavi(); ?>

<?php else : ?>
<p>お知らせコンテンツはありません。</p>
<?php endif; ?>
</main>
