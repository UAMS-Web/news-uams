<?php

/**
 * Loads functions.php the way WordPress does, in a bare process, and prints what it registered.
 *
 * Run by FileScopeRegistrationTest in a child process: functions.php registers its hooks, image
 * sizes and post status at include time, before any test starts, so only a fresh process can
 * observe that registration. Every WordPress function the file calls at file scope is replaced by
 * a recorder.
 *
 * Usage: php load-functions.php <path to functions.php>
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

require $argv[1];

echo json_encode($registered, JSON_THROW_ON_ERROR);
