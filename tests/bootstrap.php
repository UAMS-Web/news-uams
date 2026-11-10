<?php

/**
 * Unit bootstrap for the theme.
 *
 * Loads functions.php, which the theme cannot avoid, inside one Brain Monkey
 * session with what it calls at file scope stubbed. The functions it defines
 * outlive the session; the hooks it registered are discarded by tearDown(), so
 * every test starts clean.
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../vendor/antecedent/patchwork/Patchwork.php';

if (! defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir().'/news-uams-tests-no-wordpress/');
}

setUp();

stubTranslationFunctions();
stubEscapeFunctions();
when('get_template_directory')->justReturn(dirname(__DIR__));
when('get_stylesheet_directory')->justReturn(dirname(__DIR__));
when('add_image_size')->justReturn();
when('register_post_status')->justReturn();

require_once __DIR__.'/../functions.php';

tearDown();

// cspell:ignore ABSPATH stylesheet
