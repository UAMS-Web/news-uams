<?php

/**
 * Renders content-post.php in a bare process and prints the markup.
 *
 * The template declares text_cut() when it is included, so it can be included once per process;
 * ContentPostTemplateTest runs this fixture once per title.
 *
 * Usage: php render-content-post.php <path to content-post.php> <title> <include_boilerplate 0|1>
 */

declare(strict_types=1);

function get_the_title(): string
{
    global $argv;

    return $argv[2];
}

function the_title(): void
{
    echo get_the_title();
}

function the_content(): void
{
    echo '[content]';
}

function uams_sidebar_menu_mobile(): void
{
    echo '[mobile menu]';
}

function get_the_ID(): int
{
    return 7;
}

function get_post_meta(int $id, string $key, bool $single): string
{
    global $argv;

    return $argv[3] === '1' ? 'yes' : '';
}

ob_start();
require $argv[1];

echo ob_get_clean();
