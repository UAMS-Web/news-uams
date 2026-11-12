<?php

/**
 * ap_date(), the Associated Press style date used in the REST output.
 *
 * @see functions.php
 */

declare(strict_types=1);

it('abbreviates the months the way AP style does', function (string $date, string $expected): void {
    expect(ap_date($date))->toBe($expected);
})->with([
    'January abbreviates' => ['2026-01-05', 'Jan. 5, 2026'],
    'March is spelled out' => ['2026-03-09', 'March 9, 2026'],
    'September is Sept.' => ['2026-09-30', 'Sept. 30, 2026'],
    'November abbreviates' => ['2026-11-09', 'Nov. 9, 2026'],
    'December abbreviates' => ['2026-12-31', 'Dec. 31, 2026'],
]);

it('drops the leading zero from the day', function (): void {
    expect(ap_date('2026-05-02'))->toBe('May 2, 2026');
});

it('reads a value that is not a date string as the start of the Unix epoch', function (mixed $value): void {
    expect(ap_date($value))->toBe('Jan. 1, 1970');
})->with([
    'false, as get_the_date() returns outside the loop' => [false],
    'an integer' => [1_700_000_000],
    'text that is not a date' => ['not a date'],
    'an empty string' => [''],
]);
