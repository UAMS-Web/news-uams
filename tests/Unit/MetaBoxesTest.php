<?php

/**
 * The Meta Box registrations and the settings they feed: the News Settings page, the options and
 * newsroom meta boxes, the boilerplate and the excluded-category list.
 *
 * @see functions.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\when;

it('adds the News Settings page after the existing settings pages', function (): void {
    expect(prefix_options_page([['id' => 'other']]))->toBe([
        ['id' => 'other'],
        [
            'id' => 'uamswp-news',
            'option_name' => 'uamswp_news',
            'menu_title' => 'News Settings',
            'icon_url' => 'dashicons-welcome-widgets-menus',
        ],
    ]);
});

it('adds the News Options meta box to the settings page', function (): void {
    expect(uams_options_meta_boxes([]))->toBe([
        [
            'id' => 'umas-news-options',
            'title' => 'News Options',
            'settings_pages' => 'uamswp-news',
            'fields' => [
                [
                    'name' => 'Exclude Categories',
                    'label_description' => 'These categories will be excluded from standard pages',
                    'id' => 'exclude_cat',
                    'type' => 'taxonomy_advanced',
                    'taxonomy' => 'category',
                    'field_type' => 'checkbox_tree',
                ],
                [
                    'name' => 'Boilerplate',
                    'label_description' => 'Boilerplate content to include at bottom of articles',
                    'id' => 'boilerplate_html',
                    'type' => 'wysiwyg',
                    'raw' => true,
                    'options' => [
                        'textarea_rows' => 8,
                        'teeny' => true,
                        'media_buttons' => false,
                    ],
                ],
            ],
        ],
    ]);
});

it('keeps the meta boxes it is given ahead of its own', function (): void {
    expect(uams_options_meta_boxes([['id' => 'first']])[0])->toBe(['id' => 'first'])
        ->and(uams_news_meta_boxes([['id' => 'first']])[0])->toBe(['id' => 'first']);
});

it('adds the newsroom and category image meta boxes', function (): void {
    expect(uams_news_meta_boxes([]))->toBe([
        [
            'id' => 'newsroom',
            'title' => 'Newsroom Features',
            'post_types' => 'post',
            'context' => 'normal',
            'priority' => 'high',
            'fields' => [
                [
                    'name' => 'Include Boilerplate',
                    'id' => 'include_boilerplate',
                    'type' => 'checkbox',
                    'std' => 0,
                ],
                [
                    'id' => 'news_release_pdf_url',
                    'name' => 'News Release PDF',
                    'type' => 'file_input',
                    'desc' => 'Please upload PDF version of the Official News Release',
                    'force_delete' => false,
                ],
                [
                    'name' => 'Media Contact',
                    'id' => 'media_contact',
                    'type' => 'select',
                    'options' => [
                        'Media Taylor-Peel' => 'Taylor-Peel',
                        'Media Peel-Dupins' => 'Peel-Dupins',
                        'Media Taylor-Dupins' => 'Taylor-Dupins',
                        'Media Taylor-Caldwell' => 'Taylor-Caldwell',
                    ],
                    'multiple' => false,
                    'placeholder' => 'Select an group',
                ],
            ],
        ],
        [
            'title' => 'Custom Fields',
            'taxonomies' => 'category',
            'fields' => [
                [
                    'name' => 'Featured Image',
                    'id' => 'cat_image',
                    'type' => 'image_advanced',
                ],
            ],
        ],
    ]);
});

it('reads the boilerplate from the News Settings page', function (): void {
    when('rwmb_meta')->alias(
        fn (string $key, array $args, string $page): string => $key === 'boilerplate_html' && $args === ['object_type' => 'setting'] && $page === 'uamswp_news' ? '<p>About UAMS</p>' : 'wrong call'
    );

    expect(get_uams_boilerplate())->toBe('<p>About UAMS</p>');
});

it('returns no boilerplate when Meta Box is not active', function (): void {
    hideFunctions('rwmb_meta');

    expect(get_uams_boilerplate())->toBe('');
});

it('lists the ids of the categories excluded on the News Settings page', function (): void {
    when('is_wp_error')->justReturn(false);
    when('rwmb_meta')->alias(
        fn (string $key, array $args, string $page): array => $key === 'exclude_cat' && $args === ['object_type' => 'setting'] && $page === 'uamswp_news' ? [(object) ['term_id' => 4], (object) ['term_id' => 9]] : []
    );

    expect(exclude_id_list())->toBe([4, 9]);
});

it('lists no excluded categories when none are chosen', function (): void {
    when('is_wp_error')->justReturn(false);
    when('rwmb_meta')->justReturn([]);

    expect(exclude_id_list())->toBe([]);
});

it('lists no excluded categories when the setting is an error', function (): void {
    when('is_wp_error')->justReturn(true);
    when('rwmb_meta')->justReturn([(object) ['term_id' => 4]]);

    expect(exclude_id_list())->toBe([]);
});

it('lists no excluded categories when Meta Box is not active', function (): void {
    hideFunctions('rwmb_meta');

    expect(exclude_id_list())->toBe([]);
});
