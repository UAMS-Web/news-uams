<?php

/**
 * content.php, the template part that prints a post in a list or on its own page, and thinstrip.php,
 * the header strip above it.
 *
 * @see content.php
 * @see thinstrip.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

/**
 * Render content.php for a request.
 *
 * @param  array{single?: bool, home?: bool, search?: bool, archive?: bool, thumbnail_id?: int, meta?: array<string, string|false>, byline?: bool, coauthors?: bool, caption?: string, credit?: string}  $state  Overrides for the defaults below.
 */
function contentRender(array $state = []): string
{
    $s = $state + [
        'single' => false,
        'home' => false,
        'search' => false,
        'archive' => false,
        'thumbnail_id' => 0,
        'meta' => [],
        'byline' => false,
        'coauthors' => false,
        'caption' => '',
        'credit' => '',
    ];

    stubEscapeFunctions();
    stubTranslationFunctions();
    when('get_the_ID')->justReturn(7);
    when('is_single')->justReturn($s['single']);
    when('is_home')->justReturn($s['home']);
    when('is_search')->justReturn($s['search']);
    when('is_archive')->justReturn($s['archive']);
    when('get_post_thumbnail_id')->justReturn($s['thumbnail_id']);
    when('get_post_meta')->alias(function (int $id, string $key, bool $single) use ($s): mixed {
        if ($id === 99) {
            return $key === '_wp_attachment_image_alt' ? 'Alt "text"' : ($key === '_media_credit' ? $s['credit'] : 'wrong key');
        }

        return $s['meta'][$key] ?? '';
    });
    when('get_the_post_thumbnail_url')->alias(fn (int $id, string $size): string => 'https://example.test/full-'.$id.'-'.$size.'.jpg');
    when('get_post_field')->alias(fn (string $field, int $id): string => match ($field) {
        'post_title' => 'Attachment <title>',
        'post_excerpt' => $s['caption'],
        default => 'wrong field',
    });
    when('wp_kses_post')->alias(fn (string $text): string => 'kses('.$text.')');
    when('get_option')->alias(fn (string $name): mixed => $name === 'show_byline_on_posts' ? $s['byline'] : 'wrong option');
    when('the_post_thumbnail')->alias(function (mixed $size, array $attr = []): void {
        echo '[thumbnail '.json_encode($size).' '.json_encode($attr).']';
    });
    when('the_title')->alias(function (): void {
        echo 'The Title';
    });
    when('the_author')->alias(function (): void {
        echo '[author]';
    });
    when('coauthors')->alias(function (): void {
        echo '[coauthors]';
    });
    when('the_author_meta')->alias(function (): void {
        echo '[author meta]';
    });
    when('uams_mobile_menu')->alias(function (): void {
        echo '[mobile menu]';
    });
    when('the_excerpt')->alias(function (): void {
        echo '[excerpt]';
    });
    when('the_content')->alias(function (): void {
        echo '[content]';
    });
    when('rwmb_meta')->justReturn('[boilerplate]');
    if (! $s['coauthors']) {
        hideFunctions('coauthors');
    }

    return squish(renderTemplate('content.php'));
}

it('prints the title and the content of a page, under the mobile menu', function (): void {
    expect(contentRender())->toBe('<h1>The Title</h1> [mobile menu] [content]');
});

it('prints the featured image, with its caption and credit, above the title of a single post', function (): void {
    $output = contentRender(['single' => true, 'thumbnail_id' => 99, 'caption' => 'A <b>caption</b>', 'credit' => 'Ada & Co']);

    expect($output)->toStartWith(
        '<p class="featured-image"> <a href="https://example.test/full-7-full.jpg" title="Alt &quot;text&quot;" data-title="Attachment &lt;title&gt;" data-caption="A &lt;b&gt;caption&lt;/b&gt;">'
        .' <span class="screen-reader-text">View Larger Image</span>'
        .' [thumbnail "post-image" []]'
        .' <br/><span class="wp-caption-text">kses(A <b>caption</b>)</span>'
        .' <span class="wp-media-credit">Image by Ada &amp; Co</span>'
        .' </a> </p> <h1>The Title</h1>'
    );
});

it('leaves the caption out of the featured image when the attachment has none', function (): void {
    $output = contentRender(['single' => true, 'thumbnail_id' => 99, 'credit' => 'Ada']);

    expect($output)->not->toContain('wp-caption-text')
        ->toContain('<span class="wp-media-credit">Image by Ada</span>');
});

it('leaves the credit out of the featured image when the attachment has none', function (): void {
    $output = contentRender(['single' => true, 'thumbnail_id' => 99, 'caption' => 'Caption']);

    expect($output)->not->toContain('wp-media-credit')
        ->toContain('wp-caption-text');
});

it('prints no featured image for a single post without a thumbnail', function (): void {
    expect(contentRender(['single' => true]))->not->toContain('featured-image');
});

it('prints no featured image outside a single post', function (): void {
    expect(contentRender(['thumbnail_id' => 99]))->not->toContain('featured-image');
});

it('prints no featured image when the post meta show_image_single is false', function (): void {
    expect(contentRender(['single' => true, 'thumbnail_id' => 99, 'meta' => ['show_image_single' => false]]))->not->toContain('featured-image');
});

it('prints the byline of a single post when the site shows bylines', function (): void {
    expect(contentRender(['single' => true, 'byline' => true]))->toContain(
        '<div class="author-info"> By [author] <p class="author-desc"> <small>[author meta]</small></p> </div>'
    );
});

it('prints the byline on the posts page when the site shows bylines', function (): void {
    expect(contentRender(['home' => true, 'byline' => true]))->toContain('<div class="author-info">');
});

it('prints the co-authors in the byline when the plugin is active', function (): void {
    expect(contentRender(['single' => true, 'byline' => true, 'coauthors' => true]))->toContain('By [coauthors] <p');
});

it('prints no byline when the site hides bylines', function (): void {
    expect(contentRender(['single' => true]))->not->toContain('author-info');
});

it('prints no byline outside a single post and the posts page', function (): void {
    expect(contentRender(['byline' => true]))->not->toContain('author-info');
});

it('prints the mobile menu on a page and omits it on the posts page, search and archives', function (bool $home, bool $search, bool $archive, bool $menu): void {
    $output = contentRender(['home' => $home, 'search' => $search, 'archive' => $archive]);

    expect(str_contains($output, '[mobile menu]'))->toBe($menu);
})->with([
    'page' => [false, false, false, true],
    'posts page' => [true, false, false, false],
    'search' => [false, true, false, false],
    'archive' => [false, false, true, false],
]);

it('prints a thumbnail, the excerpt and a rule for an archive', function (): void {
    expect(contentRender(['archive' => true]))->toBe(
        '<h1>The Title</h1> [thumbnail [130,130] {"class":"attachment-post-thumbnail blogroll-img"}][excerpt]<hr>'
    );
});

it('prints a thumbnail, the excerpt and a rule for the posts page', function (): void {
    expect(contentRender(['home' => true]))->toBe(
        '<h1>The Title</h1> [thumbnail [130,130] {"class":"attachment-post-thumbnail blogroll-img"}][excerpt]<hr>'
    );
});

it('prints the boilerplate after a single post that includes it', function (): void {
    expect(contentRender(['single' => true, 'meta' => ['include_boilerplate' => '1']]))->toEndWith('[content][boilerplate]');
});

it('prints no boilerplate after a single post that does not include it', function (): void {
    expect(contentRender(['single' => true]))->not->toContain('[boilerplate]');
});

it('prints no boilerplate outside a single post', function (): void {
    expect(contentRender(['meta' => ['include_boilerplate' => '1']]))->not->toContain('[boilerplate]');
});

it('prints the header strip with the site title', function (): void {
    Functions\expect('uams_site_title')->once()->andReturnUsing(function (): void {
        echo 'UAMS News';
    });

    $output = renderTemplate('thinstrip.php');

    expect($output)->toContain('<div class="vertical-align h1">UAMS News</div>')
        ->toContain('<header class="uams-thinstrip">')
        ->toContain('href="http://uamshealth.com"');
});
