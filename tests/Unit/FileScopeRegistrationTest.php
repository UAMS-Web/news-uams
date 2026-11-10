<?php

/**
 * What functions.php registers when WordPress includes it, in the order it registers it.
 *
 * The registration runs at include time, before any test, so it is observed in a child process
 * (tests/Fixtures/load-functions.php) rather than in the test process. This test pins the old
 * way of loading the file; the registration function itself is pinned in-process by
 * RegistrationTest.
 *
 * @see functions.php
 */

declare(strict_types=1);

/**
 * Load functions.php in a bare process.
 *
 * @return list<array<int, mixed>> Every registration, in order.
 */
function fileScopeRegistrations(): array
{
    $command = escapeshellarg(PHP_BINARY).' '.escapeshellarg(__DIR__.'/../Fixtures/load-functions.php').' '.escapeshellarg(__DIR__.'/../../functions.php');
    $output = shell_exec($command);

    expect($output)->toBeString();

    return json_decode((string) $output, true, 512, JSON_THROW_ON_ERROR);
}

it('registers the hooks, image sizes and post status when the file is included', function (): void {
    $status = [
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
    ];

    expect(fileScopeRegistrations())->toBe([
        ['add_filter', '_includes/acf-pro/settings/show_admin', '__return_true', 10, 1],
        ['add_action', 'acf/input/admin_head', 'my_acf_admin_head', 10, 1],
        ['add_action', 'admin_notices', 'uams_news_meta_box_notice', 10, 1],
        ['add_filter', 'mb_settings_pages', 'prefix_options_page', 10, 1],
        ['add_filter', 'rwmb_meta_boxes', 'uams_options_meta_boxes', 10, 1],
        ['add_filter', 'rwmb_meta_boxes', 'uams_news_meta_boxes', 10, 1],
        ['add_filter', 'manage_posts_columns', 'add_img_column', 10, 1],
        ['add_filter', 'manage_posts_custom_column', 'manage_img_column', 5, 2],
        ['add_image_size', 'news-full', 960, 540, true],
        ['add_image_size', 'news-half', 560, 315, true],
        ['add_image_size', 'news-third', 400, 225, true],
        ['add_image_size', 'news-quarter', 320, 180, true],
        ['add_image_size', 'uams_news', 560, 350, true],
        ['add_image_size', 'aspect-16-9', 1024, 576, true],
        ['add_image_size', 'aspect-16-9-small', 512, 288, true],
        ['register_post_status', 'convert', $status],
        ['add_action', 'rest_api_init', 'uamswp_register_api_hooks', 10, 1],
        ['add_action', 'rest_api_init', 'create_api_posts_meta_field', 10, 1],
        ['add_action', 'rest_api_init', 'create_api_posts_category', 10, 1],
        ['add_filter', 'the_content', 'uamswp_before_after', 10, 1],
    ]);
});
