<?php

/**
 * Unit bootstrap for the theme.
 *
 * Loads Composer and Patchwork, defines ABSPATH and declares the WordPress classes the tests
 * touch. functions.php is not loaded here: tests/Pest.php loads it from the first test's setup,
 * so that what the file does at include time happens inside a test and is covered by it.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../vendor/antecedent/patchwork/Patchwork.php';

if (! defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir().'/news-uams-tests-no-wordpress/');
}

/*
 * The WordPress classes the theme and its tests touch, declared by hand because WordPress is not
 * loaded. They are evaluated rather than declared so that static analysis keeps reading the real
 * ones from the WordPress stubs.
 */
eval(<<<'PHP'
class WP_Error
{
    public function __construct(public string $code = '', public string $message = '') {}
}

/**
 * A WP_Query that serves the posts a test queues, in the order the template asks for them.
 *
 * Queue one list of posts per query with WP_Query::$queue; every query the template builds shifts
 * the next list off it and records its arguments in WP_Query::$received.
 */
class WP_Query
{
    /** @var list<list<object>> */
    public static array $queue = [];

    /** @var list<array<string, mixed>> */
    public static array $received = [];

    /** @var list<object> */
    public array $posts = [];

    public int $current_post = -1;

    /** @param array<string, mixed> $args */
    public function __construct(array $args = [])
    {
        self::$received[] = $args;
        $this->posts = array_shift(self::$queue) ?? [];
    }

    public function have_posts(): bool
    {
        if ($this->current_post + 1 < count($this->posts)) {
            return true;
        }

        $this->current_post = -1;

        return false;
    }

    public function the_post(): void
    {
        $this->current_post++;
        $GLOBALS['post'] = $this->posts[$this->current_post];
    }

    public function next_post(): object
    {
        $this->current_post++;

        return $this->posts[$this->current_post];
    }
}

class UAMS_Sidebar
{
    public const ID = 'sidebar';
}
PHP);

// cspell:ignore ABSPATH
