<?php

/**
 * The two archive templates that do not share the card markup: category-heres-to-your-health.php,
 * the podcast archive with a hero image, and category-news-release.php, the news releases listed by
 * year and month.
 *
 * @see category-heres-to-your-health.php
 * @see category-news-release.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\when;

/**
 * Render category-heres-to-your-health.php.
 *
 * @param  list<list<int>>  $queues  One list of post ids per WP_Query the template builds.
 * @param  array<string, mixed>  $options  `images` (what rwmb_meta returns, or null when Meta Box is not active), `header` (the header image), `description`, `paged`.
 * @return array{0: string, 1: list<array<string, mixed>>}
 */
function heresRender(array $queues, array $options = []): array
{
    global $paged;

    $options += ['images' => [], 'header' => '', 'description' => '', 'paged' => 1];
    $paged = $options['paged'];

    QueryLog::$queue = array_map(
        fn (array $ids): array => array_map(fn (int $id): object => (object) ['ID' => $id], $ids),
        $queues,
    );
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
    when('get_queried_object')->justReturn((object) ['term_id' => 31]);
    if ($options['images'] === null) {
        hideFunctions('rwmb_meta');
    } else {
        when('rwmb_meta')->alias(fn (string $key, array $args, int $term): array => $key === 'cat_image' && $args === ['object_type' => 'term', 'size' => 'full'] && $term === 31 ? $options['images'] : []);
    }

    when('get_header_image')->justReturn($options['header']);
    when('set_url_scheme')->alias(fn (string $url): string => 'scheme('.$url.')');
    when('category_description')->justReturn($options['description']);
    when('is_paged')->justReturn($options['paged'] > 1);
    when('has_post_thumbnail')->alias(fn (): bool => $GLOBALS['post']->ID % 2 === 1);
    when('the_post_thumbnail')->alias(function (mixed $size): void {
        echo '[thumbnail '.json_encode($size).']';
    });
    when('uams_primary_post_category')->alias(fn (): string => 'category-'.$GLOBALS['post']->ID);
    when('uams_get_permalink')->alias(fn (): string => '/post-'.$GLOBALS['post']->ID.'/');
    when('the_title_attribute')->alias(function (): void {
        echo 'attr-'.$GLOBALS['post']->ID;
    });
    when('the_title')->alias(function (): void {
        echo 'title-'.$GLOBALS['post']->ID;
    });
    when('the_excerpt')->alias(function (): void {
        echo '[excerpt '.$GLOBALS['post']->ID.']';
    });
    when('posts_nav_link')->alias(function (string $sep, string $prev, string $next): void {
        echo '[nav '.$sep.'|'.$prev.'|'.$next.']';
    });

    $markup = squish(renderTemplate('category-heres-to-your-health.php', ['paged' => $paged]));

    return [$markup, QueryLog::$received];
}

it('prints a hero for each category image', function (): void {
    [$markup] = heresRender([[], []], ['images' => [['full_url' => 'https://example.test/one.jpg'], ['full_url' => 'https://example.test/two.jpg']]]);

    expect(substr_count($markup, '<div class="uams-hero-image hero-height"'))->toBe(2)
        ->and($markup)->toContain('style="background-image:url(\' https://example.test/one.jpg\');" > <div id="hero-bg"> <div id="hero-container" class="container"> <h1 class="uams-site-title">Here\'s to Your Health</h1> <h2>with Dr. T. Glenn Pait</h2> <span class="udub-slant"><span></span></span>')
        ->toContain("url(' https://example.test/two.jpg')")
        ->not->toContain('<div class="uams-hero-image" ');
});

it('prints the header image as the hero when the category has no image', function (): void {
    [$markup] = heresRender([[], []], ['header' => 'http://example.test/header.jpg']);

    expect($markup)->toContain('<div class="uams-hero-image" style="background-image:url(\'scheme(http://example.test/header.jpg)\');" ></div>')
        ->not->toContain('hero-height');
});

it('prints a plain hero when the category has no image and the theme has no header image', function (): void {
    [$markup] = heresRender([[], []]);

    expect($markup)->toContain('<div class="uams-hero-image" ></div>');
});

it('prints a plain hero when Meta Box is not active', function (): void {
    [$markup] = heresRender([[], []], ['images' => null]);

    expect($markup)->toContain('<div class="uams-hero-image" ></div>')
        ->not->toContain('hero-height');
});

it('prints the category description when there is one', function (): void {
    [$markup] = heresRender([[], []], ['description' => '<p>Weekly episodes</p>']);

    expect($markup)->toContain('<div class="archive-meta"><p>Weekly episodes</p></div>');
});

it('prints no description block when the category has none', function (): void {
    [$markup] = heresRender([[], []]);

    expect($markup)->not->toContain('archive-meta');
});

it('builds the featured and episode queries for the first page', function (): void {
    [, $queries] = heresRender([[1], [2]]);

    expect($queries)->toBe([
        ['posts_per_page' => 2, 'category_name' => 'featured+health'],
        ['posts_per_page' => 10, 'paged' => 1, 'category_name' => 'heres-to-your-health'],
    ]);
});

it('prints the first page', function (): void {
    [$markup] = heresRender([[1, 2], [3, 4]]);

    expect($markup)->toMatchSnapshot();
});

it('prints the later pages with the page number and only the episode query', function (): void {
    [$markup, $queries] = heresRender([[3, 4]], ['paged' => 3]);

    expect($queries)->toBe([['posts_per_page' => 10, 'paged' => 3, 'category_name' => 'heres-to-your-health']])
        ->and($markup)->toContain('<h2> Page 3</h2>')
        ->not->toContain('Latest Episodes')
        ->toContain('[nav |Previous page|Next page]');
});

/**
 * Render category-news-release.php with the given posts.
 *
 * @param  list<array{0: int, 1: string, 2: string, 3: string}>  $posts  Post id, year, month name and day, newest first.
 */
function newsReleaseRender(array $posts): string
{
    QueryLog::$queue = [array_map(fn (array $post): object => (object) ['ID' => $post[0], 'time' => [$post[1], $post[2], $post[3]]], $posts)];
    QueryLog::$received = [];

    when('get_header')->alias(function (): void {
        echo '[header]';
    });
    when('get_footer')->alias(function (): void {
        echo '[footer]';
    });
    when('get_sidebar')->alias(function (): void {
        echo '[sidebar]';
    });
    when('get_the_time')->alias(fn (string $format): string => match ($format) {
        'Y' => $GLOBALS['post']->time[0],
        'F' => $GLOBALS['post']->time[1],
        default => 'wrong format',
    });
    when('the_time')->alias(function (string $format): void {
        echo $format === 'd' ? $GLOBALS['post']->time[2] : 'wrong format';
    });
    when('the_permalink')->alias(function (): void {
        echo '/release-'.$GLOBALS['post']->ID.'/';
    });
    when('the_title')->alias(function (): void {
        echo 'Release '.$GLOBALS['post']->ID;
    });

    return squish(renderTemplate('category-news-release.php'));
}

/** The part of the news release page after its styles. */
function newsReleaseList(string $markup): string
{
    return substr($markup, (int) strpos($markup, '<ul class="archives">'));
}

it('queries every news release, newest first', function (): void {
    newsReleaseRender([]);

    expect(QueryLog::$received)->toBe([['category_name' => 'news-release', 'posts_per_page' => -1, 'order' => 'DESC']]);
});

it('prints an empty list when there are no news releases', function (): void {
    expect(newsReleaseList(newsReleaseRender([])))->toBe('<ul class="archives"> </section><!-- #spotlight --> </div> </div> [sidebar] </div> </div> [footer]');
});

it('lists the news releases of one month under their year and month', function (): void {
    $list = newsReleaseList(newsReleaseRender([[1, '2026', 'november', '09'], [2, '2026', 'november', '03']]));

    expect($list)->toBe(
        '<ul class="archives"> <li><h2>2026</h2><ul class="monthly-archives"><li><h3>November</h3><ul>'
        .' <li> <span class="the_date">09:</span> <a href="/release-1/">Release 1</a> </li>'
        .' <li> <span class="the_date">03:</span> <a href="/release-2/">Release 2</a> </li>'
        .' </ul> </section><!-- #spotlight --> </div> </div> [sidebar] </div> </div> [footer]'
    );
});

it('closes the month when the month changes within a year', function (): void {
    $list = newsReleaseList(newsReleaseRender([[1, '2026', 'november', '09'], [2, '2026', 'october', '03']]));

    expect($list)->toContain('<a href="/release-1/">Release 1</a> </li> </ul></li><li><h3>October</h3><ul> <li>')
        ->and(substr_count($list, '<li><h2>'))->toBe(1);
});

it('closes the month and the year when the year changes', function (): void {
    $list = newsReleaseList(newsReleaseRender([[1, '2026', 'january', '09'], [2, '2025', 'december', '03']]));

    expect($list)->toContain('<a href="/release-1/">Release 1</a> </li> </ul></li></ul></li><li><h2>2025</h2><ul class="monthly-archives"><li><h3>December</h3><ul> <li>');
});

it('omits the month heading of a new year when the month name repeats the previous one', function (): void {
    $list = newsReleaseList(newsReleaseRender([[1, '2026', 'november', '09'], [2, '2025', 'november', '03']]));

    expect($list)->toContain('<a href="/release-1/">Release 1</a> </li> </ul></li><li><h2>2025</h2><ul class="monthly-archives"> <li>')
        ->not->toContain('<h3>November</h3><ul> <li> <span class="the_date">03');
});
