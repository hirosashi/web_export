<article id="post-<?php the_ID(); ?>" <?php post_class('post tf_clearfix'); ?>>

    <figure class="post-image tf_clearfix is_video">
        <img data-tf-not-load="1" decoding="async" src="<?= get_the_post_thumbnail_url() ?>" title="<?= get_the_title() ?>" alt="<?= get_the_title() ?>">
    </figure>

    <div class="post-content">

        <h1 class="post-title entry-title"><?= get_the_title() ?></h1>

        <div class="entry-content">
            <p><?php the_field('price'); ?></p>
            <?php the_content(); ?>
        </div><!-- /.entry-content -->

    </div>
    <!-- /.post-content -->

</article>
<!-- /.post -->

<a href="<?php the_field('shop_url'); ?>" class="module module-image tb_sy3p647 bn1-top image-right tf_mw shop_bn ex-links" target="_blank" data-wpel-link="external" rel="external noopener noreferrer">
    <div class="image-wrap tf_rel tf_mw">
        <img loading="lazy" decoding="async" width="260" height="200" src="<?php echo get_template_directory_uri(); ?>/assets/img/uploads/top1_03.jpg" class="wp-post-image wp-image-581" title="ネットショップはこちら" alt="ネットショップはこちら 商品のご購入をご希望の方はこちらからどうぞ。">
    </div>
    <!-- /image-wrap -->
    <div class="image-content">
        <div class="image-caption tb_text_wrap">
            <span>ネットショップはこちら</span>
            商品のご購入をご希望の方はこちらからどうぞ。
        </div>
    <!-- /image-caption -->
    </div>
    <!-- /image-content -->
</a>

<div class="history-back-container">
    <a class="history-back-button" href="javascript:history.back();" data-wpel-link="internal">前へ戻る</a>
</div>
