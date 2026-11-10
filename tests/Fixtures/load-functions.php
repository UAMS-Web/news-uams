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

require __DIR__.'/recorders.php';

require $argv[1];

echo json_encode($registered, JSON_THROW_ON_ERROR);
