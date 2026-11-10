<?php

/**
 * uams_news_register(), the theme's hooks, image sizes and post status.
 *
 * FileScopeRegistrationTest pins the call functions.php makes when WordPress includes it; this
 * test runs the function again with the WordPress functions mocked.
 *
 * @see functions.php
 */

declare(strict_types=1);

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;

use function Brain\Monkey\Filters\expectAdded;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

it('adds the admin and editor hooks', function (): void {
    stubTranslationFunctions();
    when('_n_noop')->justReturn([]);
    when('add_image_size')->justReturn();
    when('register_post_status')->justReturn();

    expectAdded('_includes/acf-pro/settings/show_admin')->once()->with('__return_true', 10, 1);
    Actions\expectAdded('acf/input/admin_head')->once()->with('my_acf_admin_head', 10, 1);
    Actions\expectAdded('admin_notices')->once()->with('uams_news_meta_box_notice', 10, 1);
    expectAdded('mb_settings_pages')->once()->with('prefix_options_page', 10, 1);
    expectAdded('rwmb_meta_boxes')->once()->with('uams_options_meta_boxes', 10, 1);
    expectAdded('rwmb_meta_boxes')->once()->with('uams_news_meta_boxes', 10, 1);
    expectAdded('manage_posts_columns')->once()->with('add_img_column', 10, 1);
    expectAdded('manage_posts_custom_column')->once()->with('manage_img_column', 5, 2);
    Actions\expectAdded('rest_api_init')->once()->with('uamswp_register_api_hooks', 10, 1);
    Actions\expectAdded('rest_api_init')->once()->with('create_api_posts_meta_field', 10, 1);
    Actions\expectAdded('rest_api_init')->once()->with('create_api_posts_category', 10, 1);
    expectAdded('the_content')->once()->with('uamswp_before_after', 10, 1);

    uams_news_register();
});

it('adds each image size', function (): void {
    stubTranslationFunctions();
    when('_n_noop')->justReturn([]);
    when('register_post_status')->justReturn();

    Functions\expect('add_image_size')->once()->with('news-full', 960, 540, true);
    Functions\expect('add_image_size')->once()->with('news-half', 560, 315, true);
    Functions\expect('add_image_size')->once()->with('news-third', 400, 225, true);
    Functions\expect('add_image_size')->once()->with('news-quarter', 320, 180, true);
    Functions\expect('add_image_size')->once()->with('uams_news', 560, 350, true);
    Functions\expect('add_image_size')->once()->with('aspect-16-9', 1024, 576, true);
    Functions\expect('add_image_size')->once()->with('aspect-16-9-small', 512, 288, true);

    uams_news_register();
});

it('registers the convert post status', function (): void {
    stubTranslationFunctions();
    when('add_image_size')->justReturn();
    when('_n_noop')->alias(fn (string $single, string $plural, string $domain): array => [$single, $plural, $domain]);

    $received = [];
    Functions\expect('register_post_status')->once()->andReturnUsing(function (string $status, array $args) use (&$received): void {
        $received = [$status, $args];
    });

    uams_news_register();

    expect($received)->toBe(['convert', [
        'label' => 'Convert',
        'label_count' => ['To Convert <span class="count">(%s)</span>', 'To Convert <span class="count">(%s)</span>', 'wp-statuses'],
        'public' => false,
        'show_in_admin_all_list' => false,
        'show_in_admin_status_list' => true,
        'post_type' => ['post', 'page'],
        'show_in_metabox_dropdown' => true,
        'show_in_inline_dropdown' => true,
        'show_in_press_this_dropdown' => true,
        'labels' => ['metabox_dropdown' => 'To Be Converted', 'inline_dropdown' => 'To Convert'],
        'dashicon' => 'dashicons-yes',
    ]]);
});
