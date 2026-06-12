<?php
/**
 * Theme functions - wp8
 * Remove unnecessary WordPress default styles and scripts
 */

add_theme_support('post-thumbnails');

// Remove WP Block Library CSS
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');

    // Remove Contact Form 7 scripts/styles on non-contact pages
    if (!is_page('contact')) {
        wp_dequeue_style('contact-form-7');
        wp_dequeue_script('contact-form-7');
        wp_dequeue_script('swv');
        wp_dequeue_script('wp-hooks');
        wp_dequeue_script('wp-i18n');
    }
}, 100);

// Remove global-styles and wp-img-auto-sizes via deregister
add_action('wp_enqueue_scripts', function() {
    wp_deregister_style('global-styles');
    wp_deregister_style('wp-img-auto-sizes');
}, 200);

// Remove wp_enqueue_global_styles (the function that enqueues global-styles)
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

// Remove speculation rules
add_action('init', function() {
    remove_action('wp_footer', 'wp_output_js_speculation_rules');
});

// Remove wp-emoji scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Remove oEmbed discovery links
remove_action('wp_head', 'wp_oembed_add_discovery_links');

// Remove REST API link
remove_action('wp_head', 'rest_output_link_wp_head');

// Remove RSD link
remove_action('wp_head', 'rsd_link');

// Remove shortlink
remove_action('wp_head', 'wp_shortlink_wp_head');

// Remove generator meta tag
remove_action('wp_head', 'wp_generator');

// Set proper page title
add_filter('pre_get_document_title', function($title) {
    if (is_page()) {
        $page_titles = [
            'top' => '京都のれん | 風呂敷 | 株式会社 後藤三郎商店',
            'hojin' => '法人のお客様について | 京都市 | 株式会社 後藤三郎商店',
            'tapestry-furoshiki' => '風呂敷について | 京都市 | 株式会社 後藤三郎商店',
            'tsutsumikata' => '風呂敷の包み方 | 京都市 | 株式会社 後藤三郎商店',
            'contact' => 'お問い合わせ | 京都市 | 株式会社 後藤三郎商店',
            'info' => '会社概要 | 京都市 | 株式会社 後藤三郎商店',
        ];
        global $post;
        if ($post && isset($page_titles[$post->post_name])) {
            return $page_titles[$post->post_name];
        }
    }
    return $title;
});

// Remove core block patterns
add_action('after_setup_theme', function() {
    remove_theme_support('core-block-patterns');
});

// Enqueue themify framework CSS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('themify-concate', get_template_directory_uri() . '/assets/css/themify-359282732.css', [], null);
    wp_enqueue_style('themify-concate-ref', get_template_directory_uri() . '/assets/css/themify-1429799131.css', ['themify-concate'], null);
    wp_enqueue_style('vt-custom', get_template_directory_uri() . '/vt_custom.css', ['themify-concate-ref'], null);
}, 5);

// Use output buffer to strip remaining inline styles/scripts that can't be dequeued
add_action('template_redirect', function() {
    ob_start(function($html) {
        // Remove wp-img-auto-sizes-contain-inline-css block
        $html = preg_replace('/<style id="wp-img-auto-sizes-contain-inline-css">.*?<\/style>\s*/s', '', $html);
        // Remove global-styles-inline-css block
        $html = preg_replace('/<style id="global-styles-inline-css">.*?<\/style>\s*/s', '', $html);
        // Remove speculationrules script
        $html = preg_replace('/<script type="speculationrules">.*?<\/script>\s*/s', '', $html);
        return $html;
    });
});

// Ensure jQuery is loaded on frontend (needed for anchor scroll and easing)
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('jquery');
});

