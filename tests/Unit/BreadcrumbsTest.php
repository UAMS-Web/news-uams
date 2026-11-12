<?php

/**
 * get_uams_breadcrumbs(), the breadcrumb trail printed above a page.
 *
 * Every conditional tag and lookup the function calls is answered from one state array, so a test
 * names only what differs from an ordinary page.
 *
 * @see functions.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\when;

/**
 * Build the trail for a request.
 *
 * @param  array<string, mixed>  $state  Overrides for the defaults below.
 */
function breadcrumbsFor(array $state = []): string
{
    $s = $state + [
        'post_id' => 7,
        'ancestors' => [],
        'front_page' => false,
        'home' => false,
        'not_found' => false,
        'search' => false,
        'search_query' => '',
        'author' => false,
        'queried_object' => (object) ['display_name' => 'Ada Author'],
        'queried_id' => 0,
        'posts_page' => 99,
        'category' => false,
        'tax' => false,
        'single' => false,
        'archive' => false,
        'page' => false,
        'has_category' => false,
        'custom_type' => false,
        'archive_link' => '',
        'rewrite' => [],
        'blog_title' => 'UAMS   News',
    ];

    stubEscapeFunctions();

    $GLOBALS['post'] = (object) ['ID' => $s['post_id'], 'post_title' => 'Current Page'];

    when('get_post_ancestors')->alias(fn (int $id): array => $id === $s['post_id'] ? $s['ancestors'] : ['wrong post']);
    when('is_front_page')->justReturn($s['front_page']);
    when('is_home')->justReturn($s['home']);
    when('home_url')->alias(fn (string $path): string => 'https://example.test'.$path);
    when('get_bloginfo')->alias(fn (string $show): string => $show === 'title' ? $s['blog_title'] : 'wrong show');
    when('is_404')->justReturn($s['not_found']);
    when('is_search')->justReturn($s['search']);
    when('get_search_query')->justReturn($s['search_query']);
    when('is_author')->justReturn($s['author']);
    when('get_queried_object')->justReturn($s['queried_object']);
    when('get_queried_object_id')->justReturn($s['queried_id']);
    when('get_option')->alias(fn (string $name): mixed => $name === 'page_for_posts' ? $s['posts_page'] : 'wrong option');
    when('get_the_title')->alias(fn (int $id): string => 'Title of '.$id);
    when('is_category')->justReturn($s['category']);
    when('is_tax')->justReturn($s['tax']);
    when('is_single')->justReturn($s['single']);
    when('is_post_type_archive')->justReturn($s['archive']);
    when('is_page')->justReturn($s['page']);
    when('has_category')->justReturn($s['has_category']);
    when('uams_is_custom_post_type')->justReturn($s['custom_type']);
    when('uams_primary_post_category')->justReturn('[primary category]');
    when('get_the_category')->justReturn([(object) ['term_id' => 4], (object) ['term_id' => 5]]);
    when('get_post_type')->justReturn('release');
    when('get_post_type_object')->justReturn((object) [
        'query_var' => 'release',
        'labels' => (object) ['menu_name' => 'Releases'],
        'rewrite' => $s['rewrite'],
    ]);
    when('get_post_type_archive_link')->alias(fn (string $type): string => $type === 'release' ? $s['archive_link'] : 'wrong type');
    when('site_url')->alias(fn (string $path): string => 'https://example.test'.$path);
    when('get_query_var')->alias(fn (string $var): string => match ($var) {
        'cat' => 'cat-id',
        'term' => 'term-slug',
        'taxonomy' => 'tax-name',
        default => 'wrong var',
    });
    when('get_category')->alias(fn (string $id): object => (object) ['term_id' => $id === 'cat-id' ? 33 : 0]);
    when('get_cat_name')->alias(fn (int $id): string => $id === 33 ? 'Health' : 'wrong category');
    when('get_term_by')->alias(fn (string $field, string $value, string $taxonomy): object => (object) ['name' => 'Term '.$value, 'by' => $field.'/'.$taxonomy]);
    when('get_taxonomy')->alias(fn (string $name): object => (object) ['labels' => (object) ['name' => 'Taxonomy '.$name]]);
    when('wp_trim_words')->alias(fn (string $text, int $words, string $more): string => 'trim('.$text.','.$words.','.$more.')');
    when('get_post')->alias(fn (int $id): object => (object) ['ID' => $id, 'post_title' => 'Page "'.$id.'"']);
    when('get_permalink')->alias(fn (int $id): string => 'https://example.test/page-'.$id.'/');

    return get_uams_breadcrumbs();
}

/** The trail around the items a request adds. */
function breadcrumbsWrap(string $items, bool $front = false): string
{
    return "<nav class='uams-breadcrumbs' aria-label='breadcrumbs'><ul>"
        .'<li><a href="http://www.uams.edu" title="University of Arkansas for Medical Scineces">Home</a></li>'
        .'<li'.($front ? ' class="current"' : '').'><a href="https://example.test/" title="UAMS News">UAMS News</a><li>'
        .$items
        .'</ul></nav>';
}

it('starts every trail with the university link and the site link, with runs of spaces in the title collapsed', function (): void {
    expect(breadcrumbsFor())->toBe(breadcrumbsWrap(''));
});

it('marks the site link as current on the front page', function (): void {
    expect(breadcrumbsFor(['front_page' => true]))->toBe(breadcrumbsWrap('', true));
});

it('adds an oops crumb on a 404', function (): void {
    expect(breadcrumbsFor(['not_found' => true]))->toBe(breadcrumbsWrap('<li class="current"><span>Ooops!</span>'));
});

it('adds the search query on a search page', function (): void {
    expect(breadcrumbsFor(['search' => true, 'search_query' => 'flu shots']))
        ->toBe(breadcrumbsWrap('<li class="current"><span>Search results for flu shots</span>'));
});

it('adds the author name on an author archive', function (): void {
    expect(breadcrumbsFor(['author' => true]))
        ->toBe(breadcrumbsWrap('<li class="current"><span> Author: Ada Author</span>'));
});

it('adds the title of the posts page on the posts page', function (): void {
    expect(breadcrumbsFor(['queried_id' => 5, 'posts_page' => '5']))
        ->toBe(breadcrumbsWrap('<li class="current"><span> Title of 5 </span>'));
});

it('adds no posts page crumb when the queried object is another page', function (): void {
    expect(breadcrumbsFor(['queried_id' => 5, 'posts_page' => '6']))->toBe(breadcrumbsWrap(''));
});

it('adds the category name on a category archive', function (): void {
    expect(breadcrumbsFor(['category' => true]))
        ->toBe(breadcrumbsWrap('<li class="current"><span>Health</span>'));
});

it('adds the taxonomy label and term name on a taxonomy archive', function (): void {
    expect(breadcrumbsFor(['tax' => true]))
        ->toBe(breadcrumbsWrap('<li class="current"><span>Taxonomy tax-name: Term term-slug</span>'));
});

it('looks the term up by slug in the queried taxonomy', function (): void {
    $items = breadcrumbsFor(['tax' => true]);

    expect($items)->toContain('Term term-slug');
});

it('adds the post type menu name on a post type archive', function (): void {
    expect(breadcrumbsFor(['archive' => true]))
        ->toBe(breadcrumbsWrap('<li class="current"><span>Releases</span>'));
});

it('adds the term name on a taxonomy archive of a post type', function (): void {
    expect(breadcrumbsFor(['archive' => true, 'tax' => true]))
        ->toBe(breadcrumbsWrap('<li class="current"><span>Term term-slug</span>'));
});

it('adds the primary category and a trimmed title on a single post with a category', function (): void {
    expect(breadcrumbsFor(['single' => true, 'has_category' => true]))
        ->toBe(breadcrumbsWrap('<li>[primary category]<li class="current"><span>trim(Title of 7,5,...)</span>'));
});

it('adds only the trimmed title on a single post without a category', function (): void {
    expect(breadcrumbsFor(['single' => true]))
        ->toBe(breadcrumbsWrap('<li class="current"><span>trim(Title of 7,5,...)</span>'));
});

it('links the post type archive on a single post of a custom post type', function (): void {
    $items = breadcrumbsFor(['single' => true, 'custom_type' => true, 'archive_link' => 'https://example.test/releases/']);

    expect($items)->toBe(breadcrumbsWrap(
        '<li><a href="https://example.test/releases/" title="Releases">Releases</a>'
        .'<li class="current"><span>trim(Title of 7,5,...)</span>'
    ));
});

it('links the rewrite slug on a single post of a custom post type with no archive', function (): void {
    $items = breadcrumbsFor(['single' => true, 'custom_type' => true, 'rewrite' => ['slug' => 'press']]);

    expect($items)->toBe(breadcrumbsWrap(
        '<li><a href="https://example.test/press/" title="Releases">Releases</a>'
        .'<li class="current"><span>trim(Title of 7,5,...)</span>'
    ));
});

it('adds no post type link when the custom post type has neither an archive nor a rewrite slug', function (): void {
    $items = breadcrumbsFor(['single' => true, 'custom_type' => true, 'rewrite' => ['slug' => '']]);

    expect($items)->toBe(breadcrumbsWrap('<li class="current"><span>trim(Title of 7,5,...)</span>'));
});

it('adds no post type link on a single post of a built-in post type', function (): void {
    $items = breadcrumbsFor(['single' => true, 'archive_link' => 'https://example.test/releases/']);

    expect($items)->not->toContain('Releases');
});

it('lists the ancestors of a page, oldest first, and marks the page itself as current', function (): void {
    $items = breadcrumbsFor(['page' => true, 'ancestors' => [3, 5]]);

    expect($items)->toBe(breadcrumbsWrap(
        '<li><a href="https://example.test/page-5/" title="Page &quot;5&quot;">Page "5"</a></li>'
        .'<li><a href="https://example.test/page-3/" title="Page &quot;3&quot;">Page "3"</a></li>'
        .'<li  class="current" ><span>Page "7"</span></li>'
    ));
});

it('lists only the page itself when it has no ancestors', function (): void {
    expect(breadcrumbsFor(['page' => true]))
        ->toBe(breadcrumbsWrap('<li  class="current" ><span>Page "7"</span></li>'));
});

it('adds no page crumbs on the front page', function (): void {
    expect(breadcrumbsFor(['page' => true, 'front_page' => true, 'ancestors' => [3]]))->toBe(breadcrumbsWrap('', true));
});

it('adds the page when the page is the posts page but not the front page', function (): void {
    expect(breadcrumbsFor(['page' => true, 'home' => true]))
        ->toBe(breadcrumbsWrap('<li  class="current" ><span>Page "7"</span></li>'));
});

it('adds the page when it is the front page but not the posts page, unless it is the front page', function (): void {
    expect(breadcrumbsFor(['page' => true, 'home' => true, 'front_page' => true]))->toBe(breadcrumbsWrap('', true));
});

it('skips an empty ancestor id', function (): void {
    $items = breadcrumbsFor(['page' => true, 'ancestors' => [0, 5]]);

    expect($items)->not->toContain('page-0')
        ->toContain('page-5');
});

it('adds no crumbs on an ordinary view that is neither a page nor an archive', function (): void {
    expect(breadcrumbsFor(['page' => false, 'single' => false]))->toBe(breadcrumbsWrap(''));
});

it('adds no crumb for an unqueried view when no posts page is set', function (): void {
    expect(breadcrumbsFor(['posts_page' => 0]))->toBe(breadcrumbsWrap(''));
});
