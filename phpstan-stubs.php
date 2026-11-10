<?php

/**
 * Declarations PHPStan needs for symbols this child theme calls but does not define.
 *
 * Indexed by `scanFiles` in phpstan.neon.dist, never loaded by WordPress and never
 * analyzed. The signatures match the parent theme (`uams-2016`, `inc/template-functions.php`
 * and `widgets/class.uams-sidebar.php`) and Advanced Custom Fields. When the parent theme
 * changes one of them, change it here.
 */

declare(strict_types=1);

/**
 * Parent theme: the permalink of a post, honoring a custom link.
 *
 * @param  int|WP_Post|null  $post
 */
function uams_get_permalink($post = null): string {}

/** Parent theme: whether the current page has a sidebar. */
function uams_has_sidebar(): bool {}

/** Parent theme: whether the current post type is a custom one. */
function uams_is_custom_post_type(): bool {}

/** Parent theme: prints the sidebar menu. */
function uams_sidebar_menu(): void {}

/** Parent theme: prints the mobile sidebar menu. */
function uams_sidebar_menu_mobile(): void {}

/** Parent theme: prints the mobile menu. */
function uams_mobile_menu(): void {}

/** Parent theme: prints the site title. */
function uams_site_title(): void {}

/**
 * Advanced Custom Fields: the value of a field.
 *
 * @param  string  $selector
 * @param  int|string|false  $post_id
 * @return mixed
 */
function get_field($selector, $post_id = false, bool $format_value = true) {}

/** Parent theme: the sidebar widget area. */
class UAMS_Sidebar
{
    public const ID = 'sidebar';
}
