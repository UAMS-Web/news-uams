<?php

/**
 * Renders content-post.php in a bare process and prints the markup.
 *
 * Loads functions.php first, as WordPress does, with the registration functions it calls at include
 * time replaced by no-ops, because the template calls text_cut() from there.
 *
 * Usage: php render-content-post.php <theme directory> <title> <include_boilerplate 0|1>
 */

declare(strict_types=1);

function add_filter(): bool
{
    return true;
}

function add_action(): bool
{
    return true;
}

function add_image_size(): void {}

function register_post_status(): void {}

function __(string $text): string
{
    return $text;
}

function _n_noop(string $single, string $plural): array
{
    return [$single, $plural];
}

define('ABSPATH', __DIR__);

require $argv[1].'/functions.php';

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

function get_post_meta(int $id, string $key = '', bool $single = false): string
{
    global $argv;

    return $argv[3] === '1' ? 'yes' : '';
}

ob_start();
require $argv[1].'/content-post.php';

echo ob_get_clean();
