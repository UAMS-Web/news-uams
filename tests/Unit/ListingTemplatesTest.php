<?php

/**
 * The listing templates: front-page.php and the five category archives that share its card markup.
 *
 * The markup is pinned with snapshots recorded from the code as it stands; the queries each
 * template builds are asserted outright. WP_Query is a fake that serves the posts a test queues
 * (tests/bootstrap.php).
 *
 * @see front-page.php
 * @see category-health.php
 * @see category-university.php
 * @see category-patient-stories.php
 * @see category-education-research-feature.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\when;

/**
 * Posts with the given ids.
 *
 * @param  list<int>  $ids
 * @return list<object>
 */
function listingPosts(array $ids): array
{
    return array_map(fn (int $id): object => (object) ['ID' => $id], $ids);
}

/**
 * Render a listing template with the queries a request would run.
 *
 * @param  list<list<int>>  $queues  One list of post ids per WP_Query the template builds, in order.
 * @param  list<int>  $thumbnails  The ids of the posts that have a featured image.
 * @param  array{paged?: int, excluded?: list<int>}  $options  `paged` (the page number or null for none), `excluded` (the category ids the settings page excludes).
 * @return array{0: string, 1: list<array<string, mixed>>, 2: list<string>} The squished markup, the queries built and the PHP warnings raised.
 */
function listingRender(string $file, array $queues, array $thumbnails = [], array $options = []): array
{
    global $paged;

    $options += ['paged' => 1, 'excluded' => []];
    $paged = $options['paged'];

    QueryLog::$queue = array_map(listingPosts(...), $queues);
    QueryLog::$received = [];

    stubEscapeFunctions();
    when('get_header')->alias(function (): void {
        echo '[header]';
    });
    when('get_footer')->alias(function (): void {
        echo '[footer]';
    });
    when('get_sidebar')->alias(function (): void {
        echo '[sidebar]';
    });
    when('is_paged')->justReturn($options['paged'] > 1);
    when('has_post_thumbnail')->alias(fn (): bool => in_array(currentPost()->ID, $thumbnails, true));
    when('the_post_thumbnail')->alias(function (mixed $size): void {
        echo '[thumbnail '.json_encode($size).']';
    });
    when('uams_primary_post_category')->alias(fn (): string => 'category-'.currentPost()->ID);
    when('uams_get_permalink')->alias(fn (): string => '/post-'.currentPost()->ID.'/');
    when('the_title_attribute')->alias(function (): void {
        echo 'attr-'.currentPost()->ID;
    });
    when('the_title')->alias(function (): void {
        echo 'title-'.currentPost()->ID;
    });
    when('get_the_date')->justReturn('2026-11-09');
    when('get_the_excerpt')->alias(fn (): string => 'excerpt-'.currentPost()->ID);
    when('the_excerpt')->alias(function (): void {
        echo '[excerpt '.currentPost()->ID.']';
    });
    when('the_author')->alias(function (): void {
        echo 'author-'.currentPost()->ID;
    });
    when('do_shortcode')->alias(fn (string $code): string => '['.$code.']');
    when('rewind_posts')->justReturn();
    when('posts_nav_link')->alias(function (string $sep, string $prev, string $next): void {
        echo '[nav '.$sep.'|'.$prev.'|'.$next.']';
    });
    when('rwmb_meta')->justReturn(array_map(fn (int $id): object => (object) ['term_id' => $id], $options['excluded']));

    $warnings = [];
    set_error_handler(function (int $number, string $message) use (&$warnings): bool {
        $warnings[] = $message;

        return true;
    });

    try {
        $markup = squish(renderTemplate($file, ['paged' => $paged]));
    } finally {
        restore_error_handler();
    }

    return [$markup, QueryLog::$received, $warnings];
}

describe('front-page.php', function (): void {
    it('builds the five queries, each excluding the posts the earlier ones showed', function (): void {
        [, $queries] = listingRender('front-page.php', [[1, 2], [3], [4, 5, 6], [7, 8], [9]]);

        expect($queries)->toBe([
            ['posts_per_page' => 7, 'category_name' => 'featured'],
            ['posts_per_page' => 3, 'category__in' => 3852, 'post__not_in' => [1, 2]],
            ['posts_per_page' => 8, 'category__not_in' => ['16885', '16883', '16878', '16894'], 'post__not_in' => [1, 2, 3]],
            ['posts_per_page' => 10, 'category_name' => 'announcements', 'category__not_in' => ['16885', '16883'], 'post__not_in' => [1, 2, 3, 4, 6, 5]],
            ['posts_per_page' => 5, 'category_name' => 'academic-continuing-education-events', 'category__not_in' => [], 'post__not_in' => [1, 2, 3, 4, 6, 5, 7, 8]],
        ]);
    });

    it('excludes the categories chosen on the News Settings page from the story and announcement queries', function (): void {
        [, $queries] = listingRender('front-page.php', [[1], [], [], [], []], options: ['excluded' => [4, 9]]);

        expect($queries[2]['category__not_in'])->toBe([4, 9, '16885', '16883', '16878', '16894'])
            ->and($queries[3]['category__not_in'])->toBe([4, 9, '16885', '16883'])
            ->and($queries[4]['category__not_in'])->toBe([4, 9]);
    });

    it('prints the sections for a full page', function (): void {
        [$markup, , $warnings] = listingRender(
            'front-page.php',
            [[1, 2, 3], [4, 5], [6, 7, 8, 9, 10], [11, 12], [13]],
            thumbnails: [1, 2, 4, 6, 7],
        );

        expect($warnings)->toBe([])
            ->and($markup)->toMatchSnapshot();
    });

    it('prints the empty page when no query returns a post', function (): void {
        [$markup, , $warnings] = listingRender('front-page.php', [[], [], [], [], []]);

        expect($warnings)->toBe([])
            ->and($markup)->toMatchSnapshot();
    });

    it('marks the first featured card as the big one and the rest as small', function (): void {
        [$markup] = listingRender('front-page.php', [[1, 2], [], [], [], []], thumbnails: [1, 2]);

        expect($markup)->toContain('<article class="card top med"> <div class="card-image"> [thumbnail "news-half"]')
            ->toContain('<article class="card top xs"> <div class="card-image"> [thumbnail "news-third"]');
    });

    it('splits the more stories between two columns, odd posts first', function (): void {
        [$markup] = listingRender('front-page.php', [[], [], [1, 2, 3, 4, 5], [], []]);

        $columns = explode('<div class="col-md-4 cards">', $markup);

        expect($columns)->toHaveCount(3)
            ->and(substr_count($columns[1], '<article'))->toBe(3)
            ->and($columns[1])->toContain('title-1')->toContain('title-3')->toContain('title-5')->and($columns[1])->not->toContain('title-2')
            ->and(substr_count($columns[2], '<article'))->toBe(2)
            ->and($columns[2])->toContain('title-2')->toContain('title-4')->and($columns[2])->not->toContain('title-1');
    });

    it('prints no announcement list when there are no announcements', function (): void {
        [$markup] = listingRender('front-page.php', [[], [], [], [], []]);

        expect($markup)->toContain('<h3 class="margin-top-none">Announcements</h3> <h3>Academic & Continuing Education Events</h3>');
    });
});

describe('the category archives', function (): void {
    it('builds the featured query for the first page and the story query for every page', function (string $file, string $featured, string $category): void {
        [, $queries] = listingRender($file, [[1, 2], [3]], options: ['paged' => 1]);

        expect($queries[0])->toBe(['posts_per_page' => 2, 'category_name' => $featured])
            ->and($queries[1])->toMatchArray(['posts_per_page' => 10, 'category_name' => $category, 'paged' => 1, 'post__not_in' => [1, 2]]);
    })->with([
        'health' => ['category-health.php', 'featured+health', 'health'],
        'university' => ['category-university.php', 'featured+university', 'university'],
        'patient stories' => ['category-patient-stories.php', 'featured+patient-stories', 'patient-stories'],
        'education research feature' => ['category-education-research-feature.php', 'featured+education-research-feature', 'education-research-feature'],
    ]);

    it('prints the page for the first page of an archive', function (string $file): void {
        [$markup, , $warnings] = listingRender($file, [[1, 2], [3, 4, 5]], thumbnails: [1, 3, 4], options: ['paged' => 1]);

        expect($warnings)->toBe([])
            ->and($markup)->toMatchSnapshot();
    })->with([
        'health' => 'category-health.php',
        'university' => 'category-university.php',
        'patient stories' => 'category-patient-stories.php',
        'education research feature' => 'category-education-research-feature.php',
    ]);

    it('prints no featured section and runs only the story query after the first page', function (string $file): void {
        [$markup, $queries, $warnings] = listingRender($file, [[3, 4]], thumbnails: [3], options: ['paged' => 2]);

        expect($queries)->toHaveCount(1)
            ->and($queries[0])->toMatchArray(['paged' => 2, 'post__not_in' => []])
            ->and($warnings)->toBe([])
            ->and($markup)->not->toContain('featured-grid');
    })->with([
        'health' => 'category-health.php',
        'university' => 'category-university.php',
        'patient stories' => 'category-patient-stories.php',
        'education research feature' => 'category-education-research-feature.php',
    ]);
});
