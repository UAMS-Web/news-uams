<?php

/**
 * The REST fields the theme adds to posts, and the filter that puts the date in front of a story.
 *
 * @see functions.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\when;

it('registers the fullcontent field on posts', function (): void {
    Functions\expect('register_rest_field')->once()->with('post', 'fullcontent', [
        'get_callback' => 'uamswp_return_full_content',
        'schema' => null,
    ]);

    uamswp_register_api_hooks();
});

it('registers the post-meta-fields field on posts', function (): void {
    Functions\expect('register_rest_field')->once()->with('post', 'post-meta-fields', [
        'get_callback' => 'get_post_meta_for_api',
        'schema' => null,
    ]);

    create_api_posts_meta_field();
});

it('registers the post_categories field on posts', function (): void {
    Functions\expect('register_rest_field')->once()->with('post', 'post_categories', [
        'get_callback' => 'get_post_categories_for_api',
        'schema' => null,
    ]);

    create_api_posts_category();
});

it('exposes only the four allowlisted meta keys', function (): void {
    expect(uamswp_api_post_meta_keys())->toBe([
        'include_boilerplate',
        'media_contact',
        'news_release_pdf_url',
        'show_image_single',
    ]);
});

it('returns the allowlisted meta that a post has, as lists of values', function (): void {
    $stored = ['media_contact' => ['Media Taylor-Peel'], 'show_image_single' => ['0'], '_edit_lock' => ['secret']];
    when('get_post_meta')->alias(
        fn (int $id, string $key): array => $id === 12 ? ($stored[$key] ?? []) : ['wrong post']
    );

    expect(get_post_meta_for_api(['id' => 12]))->toBe([
        'media_contact' => ['Media Taylor-Peel'],
        'show_image_single' => ['0'],
    ]);
});

it('returns no meta for a post that has none of the allowlisted keys', function (): void {
    when('get_post_meta')->justReturn([]);

    expect(get_post_meta_for_api(['id' => 12]))->toBe([]);
});

it('returns the category terms of the post', function (): void {
    $terms = [(object) ['name' => 'News']];
    Functions\expect('get_the_terms')->once()->with(12, 'category')->andReturn($terms);

    expect(get_post_categories_for_api(['id' => 12]))->toBe($terms);
});

/**
 * Build the full content of a post with the meta and boilerplate stubbed.
 *
 * @param  mixed  $includeBoilerplate  What get_post_meta() returns for `include_boilerplate`.
 */
function restFullContent(mixed $includeBoilerplate, string $content = '<p>Body</p><p>More</p>'): mixed
{
    when('get_post_meta')->alias(
        fn (int $id, string $key, bool $single): mixed => $id === 12 && $key === 'include_boilerplate' && $single ? $includeBoilerplate : 'wrong call'
    );
    when('rwmb_meta')->justReturn('<p>About UAMS</p>');

    return uamswp_return_full_content([
        'id' => 12,
        'content' => ['rendered' => $content],
        'date' => '2026-11-09T10:00:00',
    ]);
}

it('puts the AP style date at the start of the first paragraph', function (): void {
    expect(restFullContent('0'))->toBe(
        '<p><span class="entrydate"><time datetime="2026-11-09T10:00:00" itemprop="datePublished">Nov. 9, 2026</time></span> | Body</p><p>More</p>'
    );
});

it('appends the boilerplate when the post includes it', function (): void {
    expect(restFullContent('1'))->toEndWith('<p>More</p><p>About UAMS</p>');
});

it('appends the boilerplate when the meta is the integer one', function (): void {
    expect(restFullContent(1))->toEndWith('<p>About UAMS</p>');
});

it('leaves the boilerplate off when the post does not include it', function (): void {
    expect(restFullContent(''))->not->toContain('About UAMS');
});

it('inserts the date at the start of content that has no paragraph', function (): void {
    expect(restFullContent('0', 'Plain'))->toStartWith('Pla<span class="entrydate">')
        ->toEndWith('</span> | in');
});

/**
 * Run the_content filter for a single post.
 */
function restBeforeAfter(bool $single, bool $mainQuery, string $content = '<p>Body</p>'): string
{
    when('is_single')->justReturn($single);
    when('is_main_query')->justReturn($mainQuery);
    Functions\expect('get_the_date')->zeroOrMoreTimes()->andReturnUsing(
        fn (string $format = ''): string => $format === 'c' ? '2026-11-09T10:00:00-06:00' : '2026-11-09'
    );

    return uamswp_before_after($content);
}

it('puts the date before the first paragraph of a single post in the main query', function (): void {
    expect(restBeforeAfter(true, true))->toBe(
        '<p><span class="entrydate"><time datetime="2026-11-09T10:00:00-06:00" itemprop="datePublished">Nov. 9, 2026</time><span> | Body</p>'
    );
});

it('leaves the content alone outside a single post', function (): void {
    expect(restBeforeAfter(false, true))->toBe('<p>Body</p>');
});

it('leaves the content alone outside the main query', function (): void {
    expect(restBeforeAfter(true, false))->toBe('<p>Body</p>');
});
