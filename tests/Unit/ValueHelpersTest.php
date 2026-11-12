<?php

/**
 * uams_news_post_id() and uams_news_text(), the helpers that give the templates a post id and a
 * string where WordPress may return false.
 *
 * @see functions.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\when;

it('returns the id of the current post', function (): void {
    when('get_the_ID')->justReturn(7);

    expect(uams_news_post_id())->toBe(7);
});

it('returns 0 outside the loop, where get_the_ID() returns false', function (): void {
    when('get_the_ID')->justReturn(false);

    expect(uams_news_post_id())->toBe(0);
});

it('returns a scalar as text', function (mixed $value, string $expected): void {
    expect(uams_news_text($value))->toBe($expected);
})->with([
    'a string' => ['Title', 'Title'],
    'an integer' => [42, '42'],
    'false' => [false, ''],
    'true' => [true, '1'],
]);

it('returns nothing for a value that is not a scalar', function (mixed $value): void {
    expect(uams_news_text($value))->toBe('');
})->with([
    'null' => [null],
    'an array' => [['a']],
    'an object' => [new stdClass],
]);
