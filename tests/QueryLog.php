<?php

declare(strict_types=1);

/**
 * What the fake WP_Query serves and what it was asked for. Kept apart from WP_Query so that static
 * analysis, which reads the real class from the WordPress stubs, knows the properties.
 */
final class QueryLog
{
    /** @var list<list<object>> */
    public static array $queue = [];

    /** @var list<array<string, mixed>> */
    public static array $received = [];
}
