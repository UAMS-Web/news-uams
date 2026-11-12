<?php

/**
 * Pest configuration, for the Unit suite.
 *
 * The Integration suite is class-based PHPUnit 9.6 under tests/Integration, run
 * from its own Composer setup; Pest never loads it.
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;
use function Patchwork\redefine;
use function Patchwork\relay;

/*
 * Brain Monkey around every test.
 *
 * setUp() defines WordPress' hook functions and arms Patchwork; tearDown()
 * verifies every expectation the test set (a `->once()` that never happened
 * fails here) and removes every mock, so nothing leaks into the next test.
 *
 * The assertion count: Brain Monkey's expectations are Mockery expectations,
 * which PHPUnit does not count. Without adding them, a test whose only check is
 * `Functions\expect(...)->once()` reports as risky, and failOnRisky fails it.
 */
uses()
    ->beforeEach(function (): void {
        setUp();
        loadTheme();
    })
    ->afterEach(function (): void {
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());

        tearDown();
    })
    ->in('Unit');

/**
 * Make function_exists() report the named functions as undefined, until the test ends.
 *
 * Brain Monkey defines a function the first time a test mocks it, and PHP cannot undefine it, so
 * the branches that test for a missing plugin function (rwmb_meta, get_field) could not otherwise
 * be reached once any test had mocked the function. patchwork.json lists function_exists as
 * redefinable; Brain Monkey's tearDown() restores it.
 */
function hideFunctions(string ...$names): void
{
    redefine('function_exists', function (string $name) use ($names): bool {
        if (in_array($name, $names, true)) {
            return false;
        }

        return relay();
    });
}

/**
 * The post a template is printing: the global WordPress sets while a loop runs.
 */
function currentPost(): stdClass
{
    $post = $GLOBALS['post'] ?? null;

    if (! $post instanceof stdClass) {
        throw new LogicException('No post is set.');
    }

    return $post;
}

/**
 * Include a template the way WordPress does and return what it printed.
 *
 * WordPress includes a template from inside load_template(), which declares the loop globals, so
 * `$post` reaches the template as a global, and the query variables (`$paged` and the rest) are
 * extracted into its scope. Line endings are normalized to LF so that a working copy checked out
 * with CRLF renders the same markup as one with LF.
 *
 * @param  array<string, mixed>  $queryVars  The variables WordPress extracts from the main query.
 */
function renderTemplate(string $file, array $queryVars = []): string
{
    global $post;

    extract($queryVars, EXTR_SKIP);

    ob_start();
    include __DIR__.'/../'.$file;

    return str_replace("\r\n", "\n", (string) ob_get_clean());
}

/**
 * Collapse every run of whitespace to one space and trim the ends, so markup is compared by its
 * content and not by the indentation of the template that printed it.
 */
function squish(string $markup): string
{
    return trim((string) preg_replace('/\s+/', ' ', $markup));
}

/**
 * Load functions.php once per process, from inside the first test's setup, and fail that test if the
 * file did not register what WordPress expects at include time.
 *
 * functions.php registers its hooks, image sizes and post status as it is included. Loaded from the
 * bootstrap, that happens before any test and no test is credited with it, so a mutation that
 * removes the registration call survives unseen. Loaded here, the coverage driver credits the lines
 * to the first test of the process, and the check below makes that test fail when the registration
 * changes, whichever test happens to run first.
 *
 * @throws RuntimeException When the file registered fewer than the seven image sizes, the one post
 *                          status, or the last hook it adds.
 */
function loadTheme(): void
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;
    $sizes = 0;
    $statuses = 0;

    stubTranslationFunctions();
    stubEscapeFunctions();
    when('_n_noop')->justReturn([]);
    when('get_template_directory')->justReturn(dirname(__DIR__));
    when('get_stylesheet_directory')->justReturn(dirname(__DIR__));
    when('add_image_size')->alias(function () use (&$sizes): void {
        $sizes++;
    });
    when('register_post_status')->alias(function () use (&$statuses): void {
        $statuses++;
    });

    require_once __DIR__.'/../functions.php';

    if ($sizes !== 7 || $statuses !== 1 || has_filter('the_content', 'uamswp_before_after') === false) {
        throw new RuntimeException(sprintf(
            'functions.php registered %d image sizes (7 expected), %d post statuses (1 expected) and %s the_content filter.',
            $sizes,
            $statuses,
            has_filter('the_content', 'uamswp_before_after') === false ? 'no' : 'the',
        ));
    }
}
