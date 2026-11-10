<?php

/**
 * The WordPress functions functions.php calls at include time, as recorders, shared by the fixtures that
 * load it in a bare process.
 *
 * Each call is appended to `$registered`. A fixture that does not read the record ignores it.
 */

declare(strict_types=1);

$registered = [];

function add_filter(string $hook, mixed $callback, int $priority = 10, int $args = 1): bool
{
    $GLOBALS['registered'][] = ['add_filter', $hook, $callback, $priority, $args];

    return true;
}

function add_action(string $hook, mixed $callback, int $priority = 10, int $args = 1): bool
{
    $GLOBALS['registered'][] = ['add_action', $hook, $callback, $priority, $args];

    return true;
}

function add_image_size(string $name, int $width, int $height, bool $crop): void
{
    $GLOBALS['registered'][] = ['add_image_size', $name, $width, $height, $crop];
}

function register_post_status(string $status, array $args): void
{
    $GLOBALS['registered'][] = ['register_post_status', $status, $args];
}

function __(string $text, string $domain = ''): string
{
    return $text;
}

function _n_noop(string $single, string $plural, string $domain = ''): array
{
    return [$single, $plural, $domain];
}

define('ABSPATH', __DIR__);
