<?php

/**
 * uams_news_meta_box_notice(), the administrator notice about Meta Box and its two extensions.
 *
 * @see functions.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;

/**
 * Print the notice with the site in the given state.
 *
 * @param  list<string>  $active  The plugin functions that exist: rwmb_meta, mb_settings_page_load, mb_term_meta_load.
 * @param  list<string>  $installed  The plugin files get_plugins() lists.
 * @param  array<string, bool>  $can  What current_user_can() answers for each capability.
 */
function metaBoxNotice(array $active, array $installed, array $can, bool $multisite = false): string
{
    $functions = ['rwmb_meta', 'mb_settings_page_load', 'mb_term_meta_load'];

    stubTranslationFunctions();
    stubEscapeFunctions();
    foreach ($functions as $function) {
        if (in_array($function, $active, true)) {
            when($function)->justReturn(true);
        }
    }

    hideFunctions(...array_values(array_diff($functions, $active)));

    when('current_user_can')->alias(fn (string $capability): bool => $can[$capability] ?? false);
    when('get_plugins')->justReturn(array_fill_keys($installed, []));
    when('is_multisite')->justReturn($multisite);
    when('admin_url')->alias(fn (string $path): string => 'https://example.test/wp-admin/'.$path);
    when('network_admin_url')->alias(fn (string $path): string => 'https://example.test/wp-admin/network/'.$path);
    when('wp_nonce_url')->alias(fn (string $url, string $action): string => $url.'&_wpnonce='.$action);
    when('wp_get_theme')->justReturn(new class
    {
        public function get(string $key): string
        {
            return $key === 'Name' ? 'UAMS News' : 'wrong key';
        }
    });

    ob_start();
    uams_news_meta_box_notice();

    return (string) ob_get_clean();
}

const NOTICE_ALL = ['rwmb_meta', 'mb_settings_page_load', 'mb_term_meta_load'];

it('prints nothing for a user who cannot activate plugins', function (): void {
    expect(metaBoxNotice([], [], ['install_plugins' => true]))->toBe('');
});

it('prints nothing when Meta Box and both extensions are active', function (): void {
    expect(metaBoxNotice(NOTICE_ALL, [], ['activate_plugins' => true]))->toBe('');
});

it('offers an activate link for a plugin that is installed but not active', function (): void {
    $output = metaBoxNotice(
        ['mb_settings_page_load', 'mb_term_meta_load'],
        ['meta-box/meta-box.php'],
        ['activate_plugins' => true],
    );

    expect($output)->toBe(
        '<div class="notice notice-error"><p>UAMS News needs these plugins to work:</p><ul style="list-style:disc;margin-left:2em"><li>'
        .'<strong>Meta Box</strong> (custom fields, settings and category images) is installed but not activated. '
        .'<a href="https://example.test/wp-admin/plugins.php?action=activate&plugin=meta-box%2Fmeta-box.php&_wpnonce=activate-plugin_meta-box/meta-box.php">Activate Meta Box</a>'
        .'</li></ul></div>'
    );
});

it('offers an install link for Meta Box when it is not installed and the user can install plugins', function (): void {
    $output = metaBoxNotice(
        ['mb_settings_page_load', 'mb_term_meta_load'],
        [],
        ['activate_plugins' => true, 'install_plugins' => true],
    );

    expect($output)->toContain(
        '<strong>Meta Box</strong> (custom fields, settings and category images) is not installed. '
        .'<a href="https://example.test/wp-admin/update.php?action=install-plugin&plugin=meta-box&_wpnonce=install-plugin_meta-box">Install Meta Box</a>'
    );
});

it('links Meta Box on wordpress.org when the user cannot install plugins', function (): void {
    $output = metaBoxNotice(
        ['mb_settings_page_load', 'mb_term_meta_load'],
        [],
        ['activate_plugins' => true],
    );

    expect($output)->toContain(
        'is not installed. <a href="https://wordpress.org/plugins/meta-box/">Get Meta Box</a>'
    );
});

it('offers a download and an upload link for an extension that is not installed', function (): void {
    $output = metaBoxNotice(
        ['rwmb_meta', 'mb_term_meta_load'],
        [],
        ['activate_plugins' => true, 'install_plugins' => true],
    );

    expect($output)->toContain(
        '<strong>MB Settings Page</strong> (the News Settings page) is not installed, and is not on wordpress.org. '
        .'<a href="https://metabox.io/plugins/mb-settings-page/">Download from metabox.io</a>, then '
        .'<a href="https://example.test/wp-admin/plugin-install.php?tab=upload">upload it</a>.'
    );
});

it('offers only the download link for an extension when the user cannot install plugins', function (): void {
    $output = metaBoxNotice(
        ['rwmb_meta', 'mb_settings_page_load'],
        [],
        ['activate_plugins' => true],
    );

    expect($output)->toContain(
        '<strong>MB Term Meta</strong> (the category image field) is not installed, and is not on wordpress.org. '
        .'<a href="https://metabox.io/plugins/mb-term-meta/">Download from metabox.io</a>.'
    )->not->toContain('upload it');
});

it('lists every missing plugin in the order Meta Box, MB Settings Page, MB Term Meta', function (): void {
    $output = metaBoxNotice([], ['mb-settings-page/mb-settings-page.php'], ['activate_plugins' => true, 'install_plugins' => true]);

    expect(substr_count($output, '<li>'))->toBe(3)
        ->and(strpos($output, 'Meta Box</strong>'))->toBeLessThan(strpos($output, 'MB Settings Page</strong>'))
        ->and(strpos($output, 'MB Settings Page</strong>'))->toBeLessThan(strpos($output, 'MB Term Meta</strong>'))
        ->and($output)->toContain('</li><li>');
});

it('uses the network admin on a multisite install', function (): void {
    $output = metaBoxNotice([], ['meta-box/meta-box.php'], ['activate_plugins' => true, 'install_plugins' => true], true);

    expect($output)->toContain('href="https://example.test/wp-admin/network/plugins.php?action=activate&plugin=meta-box%2Fmeta-box.php')
        ->toContain('href="https://example.test/wp-admin/network/plugin-install.php?tab=upload"');
});

it('uses the site admin on a single site install', function (): void {
    $output = metaBoxNotice([], [], ['activate_plugins' => true, 'install_plugins' => true]);

    expect($output)->toContain('href="https://example.test/wp-admin/update.php?action=install-plugin&plugin=meta-box')
        ->not->toContain('/network/');
});
