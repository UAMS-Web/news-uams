<?php

/**
 * content-post.php rendered the way WordPress includes it, in a bare process.
 *
 * Each render runs in a bare process (tests/Fixtures/render-content-post.php) that loads
 * functions.php and then the template, the way WordPress does. The expectations were written before
 * text_cut() moved from the template into functions.php and held unchanged after the move.
 *
 * @see content-post.php
 */

declare(strict_types=1);

/**
 * Render content-post.php for a title in a bare process.
 */
function contentPostPinRender(string $title, bool $boilerplate): string
{
    $command = escapeshellarg(PHP_BINARY).' '
        .escapeshellarg(__DIR__.'/../Fixtures/render-content-post.php').' '
        .escapeshellarg(dirname(__DIR__, 2)).' '
        .escapeshellarg($title).' '
        .($boilerplate ? '1' : '0');

    return str_replace("\r\n", "\n", (string) shell_exec($command));
}

it('prints a short title whole in the mobile sidebar button', function (): void {
    expect(contentPostPinRender('Short title', false))->toContain("<div id=\"mobile-sidebar-title\" class=\"page_item\">\n\n      Short title\n      </div>");
});

it('cuts a long title at the last space within 27 characters and adds an ellipsis', function (): void {
    expect(contentPostPinRender('A considerably longer headline about a new clinic opening in Little Rock', false))
        ->toContain("\n      A considerably longer...\n");
});

it('prints the content and the mobile menu, and omits the boilerplate unless the post asks for it', function (): void {
    $output = contentPostPinRender('Short title', false);

    expect($output)->toContain('<h1>Short title</h1>')
        ->toContain('<div id="mobile-sidebar-links" aria-hidden="true">  [mobile menu]</div>')
        ->toContain("\n[content]")
        ->not->toContain('#Begin UAMS Boilerplate');
});

it('prints the boilerplate paragraph when the post includes it', function (): void {
    $output = contentPostPinRender('Short title', true);

    expect($output)->toContain('<!-- #Begin UAMS Boilerplate -->')
        ->toContain('<p class="center-text">###</p>')
        ->toContain('<!-- #End UAMS Boilerplate -->');
});
