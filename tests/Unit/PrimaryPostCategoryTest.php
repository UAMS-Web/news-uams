<?php

/**
 * uams_primary_post_category(), the category link on a post card.
 *
 * @see functions.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

/**
 * Run the function with a warning handler attached.
 *
 * @param  list<string>  $meta  What get_post_meta() returns for `pricat_get_categories`.
 * @param  list<object>  $categories  What get_the_category() returns.
 * @return array{0: string, 1: list<string>} The returned markup and the PHP warnings raised.
 */
function primaryCategoryRun(array $meta, array $categories): array
{
    $warnings = [];

    Functions\stubEscapeFunctions();
    Functions\when('get_the_ID')->justReturn(7);
    // Mirrors get_post_meta(): a list by default, the first value or '' when $single is true.
    Functions\when('get_post_meta')->alias(
        fn (int $id, string $key = '', bool $single = false) => $single ? ($meta[0] ?? '') : $meta
    );
    Functions\when('get_the_category')->justReturn($categories);
    Functions\when('is_wp_error')->alias(fn ($thing): bool => $thing instanceof WP_Error);
    Functions\when('get_category_link')->alias(fn ($term): string => 'https://example.test/category/'.(is_object($term) ? $term->term_id : $term).'/');
    Functions\when('get_term_by')->alias(
        fn (string $field, $value): object|false => $value === 'news' ? (object) ['term_id' => 5, 'name' => 'News'] : false
    );
    Functions\when('get_term')->alias(
        fn ($term) => $term === false ? new WP_Error('invalid_term', 'Empty Term.') : $term
    );

    set_error_handler(function (int $number, string $message) use (&$warnings): bool {
        $warnings[] = $message;

        return true;
    });

    try {
        $markup = uams_primary_post_category();
    } finally {
        restore_error_handler();
    }

    return [$markup, $warnings];
}

beforeEach(function (): void {
    if (! class_exists('WP_Error')) {
        eval('class WP_Error { public function __construct(public string $code = "", public string $message = "") {} }');
    }
});

it('links the primary category when the post has the meta', function (): void {
    [$markup] = primaryCategoryRun(['news'], [(object) ['term_id' => 9, 'name' => 'Other']]);

    expect($markup)->toBe('<a href="https://example.test/category/5/" title="News">News</a>');
});

it('falls back to the first category when the post has no meta', function (): void {
    [$markup] = primaryCategoryRun([], [(object) ['term_id' => 9, 'name' => 'Other']]);

    expect($markup)->toBe('<a href="https://example.test/category/9/" title="Other">Other</a>');
});

it('returns an empty string for a post with no categories', function (): void {
    [$markup] = primaryCategoryRun([], []);

    expect($markup)->toBe('');
});

it('raises no warning for a post with no meta', function (): void {
    [, $warnings] = primaryCategoryRun([], [(object) ['term_id' => 9, 'name' => 'Other']]);

    expect($warnings)->toBe([]);
});
