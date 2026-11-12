<?php
/**
* Add an automatic default custom taxonomy for calendar.
* If no event (taxonomy) is set, the event will be sorted as “draft” and won’t return an offset error.
*/
function my_acf_admin_head(): void
{

    ?>
    <script type="text/javascript">
    (function($) {

        $(document).ready(function(){

            $('.acf-field-573f70ac9357c .acf-input').append( $('#postdivrich') );

        });

    })(jQuery);
    </script>
    <style type="text/css">
        .acf-field #wp-content-editor-tools {
            background: transparent;
            padding-top: 0;
        }
    </style>
    <?php

}

/*
add_filter('allowed_http_origins', 'add_allowed_origins');

function add_allowed_origins($origins) {
    $origins[] = 'http://www.uams.edu';
    $origins[] = 'http://inside.uams.edu';
    $origins[] = 'https://uamshealth.com';
    $origins[] = 'http://www.uams.dev'; //Dev URL
    $origins[] = 'http://uamsedu.dev'; //Dev URL
    return $origins;
}

function my_customize_rest_cors() {
    remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
    add_filter( 'rest_pre_serve_request', function( $value ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: GET' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Expose-Headers: Link', false );
        return $value;
    } );
}
add_action( 'rest_api_init', 'my_customize_rest_cors', 15 );
*/

function uams_primary_post_category(): string
{

    // if ( function_exists('pricat_get_categories') ) { // if Primary Category is active.
    $prim_category_name = get_post_meta(get_the_ID(), 'pricat_get_categories', true); // Primary category slug; '' when the post has none.
    $term = null;

    // Only look the term up when the post has a primary category.
    if ($prim_category_name) {
        $prim_category_term = get_term_by('slug', $prim_category_name, 'category'); // term_id.
        $term = get_term($prim_category_term); // WP_Term Object.
    }

    if ($term && ! is_wp_error($term)) { // if primary category term exists.

        $cat_name = $term->name;

        $cat_link = get_category_link($term);

        return '<a href="'.$cat_link.'" title="'.$cat_name.'">'.$cat_name.'</a>';

    }
    // return first category
    $categories = get_the_category();
    // Do nothing if there are no categories.
    if (! $categories) {
        return '';
    }
    $i = 0;
    if ($categories[$i]->term_id == '13') { // "Featured" category, 5 on live
        $i++;
    }
    $cat_name = esc_html($categories[$i]->name);
    $cat_link = get_category_link($categories[$i]->term_id);

    return '<a href="'.$cat_link.'" title="'.$cat_name.'">'.$cat_name.'</a>';

    // } else { //return first category
    //     $categories = get_the_category();

    //     // Do nothing if there are no categories.
    //     if ( ! $categories ) {
    //         return '';
    //     }

    //     $i = 0;

    //     if ( $categories[$i]->term_id == '9' ) { // "Featured" category, 5 on live
    //         $i++;
    //     }

    //     $cat_name = esc_html($categories[$i]->name);

    //     $cat_link = get_category_link( $categories[$i]->term_id );

    //     $cat = '<a href="' . $cat_link . '">' . $cat_name . '</a>';

    //     return $cat;
    // }

}

function get_uams_breadcrumbs(): string
{

    global $post;
    $ancestors = array_reverse(get_post_ancestors($post->ID));
    $html = '<li><a href="http://www.uams.edu" title="University of Arkansas for Medical Scineces">Home</a></li>';
    $html .= '<li'.(is_front_page() ? ' class="current"' : '').'><a href="'.home_url('/').'" title="'.str_replace('   ', ' ', get_bloginfo('title')).'">'.str_replace('   ', ' ', get_bloginfo('title')).'</a><li>';

    if (is_404()) {
        $html .= '<li class="current"><span>Ooops!</span>';
    } elseif (is_search()) {
        $html .= '<li class="current"><span>Search results for '.get_search_query().'</span>';
    } elseif (is_author()) {
        $author = get_queried_object();
        $html .= '<li class="current"><span> Author: '.$author->display_name.'</span>';
    } elseif (get_queried_object_id() !== 0 && get_queried_object_id() === (int) get_option('page_for_posts')) {
        // Id zero is an unqueried view, and the setting is also zero when no posts page is set.
        $html .= '<li class="current"><span> '.get_the_title(get_queried_object_id()).' </span>';
    }

    // If the current view is a post type other than page or attachment then the breadcrumbs will be taxonomies.
    if (is_category() || is_tax() || is_single() || is_post_type_archive()) {

        if (is_post_type_archive() && ! is_tax()) {
            $posttype = get_post_type_object(get_post_type());
            // $html .=  '<li class="current"><a href="'  . get_post_type_archive_link( $posttype->query_var ) .'" title="'. $posttype->labels->menu_name .'">'. $posttype->labels->menu_name  . '</a>';
            $html .= '<li class="current"><span>'.$posttype->labels->menu_name.'</span>';
        }

        if (is_category()) {
            $category = get_category(get_query_var('cat'));
            // $html .=  '<li class="current"><a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
            $html .= '<li class="current"><span>'.get_cat_name($category->term_id).'</span>';
        }

        if (is_tax() && ! is_post_type_archive()) {
            $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $tax = get_taxonomy(get_query_var('taxonomy'));
            // $html .=  '<li class="current"><a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
            $html .= '<li class="current"><span>'.$tax->labels->name.': '.$term->name.'</span>';
        }

        if (is_tax() && is_post_type_archive()) {
            $tax = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            // $html .=  '<li class="current"><a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
            $html .= '<li class="current"><span>'.$tax->name.'</span>';
        }

        if (is_single()) {
            if (has_category()) {
                $html .= '<li>'.uams_primary_post_category(); // <a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
            }
            if (uams_is_custom_post_type()) {
                $posttype = get_post_type_object(get_post_type());
                $archive_link = get_post_type_archive_link($posttype->query_var);
                if (! empty($archive_link)) {
                    $html .= '<li><a href="'.$archive_link.'" title="'.$posttype->labels->menu_name.'">'.$posttype->labels->menu_name.'</a>';
                } elseif (! empty($posttype->rewrite['slug'])) {
                    $html .= '<li><a href="'.site_url('/'.$posttype->rewrite['slug'].'/').'" title="'.$posttype->labels->menu_name.'">'.$posttype->labels->menu_name.'</a>';
                }
            }
            $html .= '<li class="current"><span>'.wp_trim_words(get_the_title($post->ID), 5, '...').'</span>';
        }
    }

    // If the current view is a page then the breadcrumbs will be parent pages.
    elseif (is_page()) {

        if (! is_home() || ! is_front_page()) {
            $ancestors[] = $post->ID;
        }

        if (! is_front_page()) {
            foreach (array_filter($ancestors) as $index => $ancestor) {
                $class = $index + 1 === count($ancestors) ? ' class="current" ' : '';
                $page = get_post($ancestor);
                $url = get_permalink($page->ID);
                $title_attr = esc_attr($page->post_title);
                if (! empty($class)) {
                    $html .= "<li $class><span>{$page->post_title}</span></li>";
                } else {
                    $html .= "<li><a href=\"$url\" title=\"{$title_attr}\">{$page->post_title}</a></li>";
                }
            }
        }

    }

    return "<nav class='uams-breadcrumbs' aria-label='breadcrumbs'><ul>$html</ul></nav>";
}

/**
 * Warn administrators when Meta Box, or an extension the theme uses, is not active.
 *
 * The theme reads its settings and term images through rwmb_meta() and
 * registers its fields on the rwmb_meta_boxes and mb_settings_pages filters.
 * Two extensions that are not on wordpress.org supply the settings page
 * (MB Settings Page) and the category field (MB Term Meta).
 *
 * Each plugin is in one of three states: active (no message), installed but
 * not activated (an Activate link), or not installed (an Install link for
 * Meta Box, a download link and an upload link for the extensions).
 */
function uams_news_meta_box_notice(): void
{
    if (! current_user_can('activate_plugins')) {
        return;
    }

    $required = [
        [
            'file' => 'meta-box/meta-box.php',
            'name' => 'Meta Box',
            'purpose' => __('custom fields, settings and category images', 'news-uams'),
            'active' => function_exists('rwmb_meta'),
            'slug' => 'meta-box',
            'get_url' => 'https://wordpress.org/plugins/meta-box/',
        ],
        [
            'file' => 'mb-settings-page/mb-settings-page.php',
            'name' => 'MB Settings Page',
            'purpose' => __('the News Settings page', 'news-uams'),
            'active' => function_exists('mb_settings_page_load'),
            'slug' => '',
            'get_url' => 'https://metabox.io/plugins/mb-settings-page/',
        ],
        [
            'file' => 'mb-term-meta/mb-term-meta.php',
            'name' => 'MB Term Meta',
            'purpose' => __('the category image field', 'news-uams'),
            'active' => function_exists('mb_term_meta_load'),
            'slug' => '',
            'get_url' => 'https://metabox.io/plugins/mb-term-meta/',
        ],
    ];

    $installed = get_plugins();
    $admin_url = is_multisite() ? 'network_admin_url' : 'admin_url';
    $can_install = current_user_can('install_plugins');
    $items = [];

    foreach ($required as $plugin) {
        if ($plugin['active']) {
            continue;
        }

        $label = '<strong>'.esc_html($plugin['name']).'</strong> ('.esc_html($plugin['purpose']).')';

        if (array_key_exists($plugin['file'], $installed)) {
            // Installed but not activated.
            $url = wp_nonce_url(call_user_func($admin_url, 'plugins.php?action=activate&plugin='.rawurlencode($plugin['file'])), 'activate-plugin_'.$plugin['file']);
            $items[] = sprintf(
                /* translators: 1: plugin name and purpose, 2: link to activate it, 3: plugin name. */
                esc_html__('%1$s is installed but not activated. %2$s', 'news-uams'),
                $label,
                '<a href="'.esc_url($url).'">'.esc_html(sprintf(__('Activate %s', 'news-uams'), $plugin['name'])).'</a>'
            );
        } elseif ($plugin['slug']) {
            // Not installed, and available on wordpress.org.
            $url = $can_install
                ? wp_nonce_url(call_user_func($admin_url, 'update.php?action=install-plugin&plugin='.$plugin['slug']), 'install-plugin_'.$plugin['slug'])
                : $plugin['get_url'];
            $items[] = sprintf(
                /* translators: 1: plugin name and purpose, 2: link to install or get it. */
                esc_html__('%1$s is not installed. %2$s', 'news-uams'),
                $label,
                '<a href="'.esc_url($url).'">'.esc_html(sprintf($can_install ? __('Install %s', 'news-uams') : __('Get %s', 'news-uams'), $plugin['name'])).'</a>'
            );
        } else {
            // Not installed, and not on wordpress.org: download, then upload.
            $links = '<a href="'.esc_url($plugin['get_url']).'">'.esc_html__('Download from metabox.io', 'news-uams').'</a>';
            if ($can_install) {
                $links .= ', '.esc_html__('then', 'news-uams').' <a href="'.esc_url(call_user_func($admin_url, 'plugin-install.php?tab=upload')).'">'.esc_html__('upload it', 'news-uams').'</a>';
            }
            $items[] = sprintf(
                /* translators: 1: plugin name and purpose, 2: download and upload links. */
                esc_html__('%1$s is not installed, and is not on wordpress.org. %2$s.', 'news-uams'),
                $label,
                $links
            );
        }
    }

    if ($items === []) {
        return;
    }

    printf(
        '<div class="notice notice-error"><p>%s</p><ul style="list-style:disc;margin-left:2em">%s</ul></div>',
        sprintf(
            /* translators: %s: name of the active theme. */
            esc_html__('%s needs these plugins to work:', 'news-uams'),
            esc_html(wp_get_theme()->get('Name'))
        ),
        '<li>'.implode('</li><li>', $items).'</li>'
    );
}

function get_uams_boilerplate()
{
    if (! function_exists('rwmb_meta')) {
        return '';
    }

    return rwmb_meta('boilerplate_html', ['object_type' => 'setting'], 'uamswp_news');
}
function prefix_options_page($settings_pages)
{
    $settings_pages[] = [
        'id' => 'uamswp-news',
        'option_name' => 'uamswp_news',
        'menu_title' => 'News Settings',
        // 'parent'      => 'edit.php',
        'icon_url' => 'dashicons-welcome-widgets-menus',
    ];

    return $settings_pages;
}

function uams_options_meta_boxes($meta_boxes)
{
    $meta_boxes[] = [
        'id' => 'umas-news-options',
        'title' => 'News Options',
        'settings_pages' => 'uamswp-news',
        // 'tab'            => 'general',

        'fields' => [
            [
                'name' => 'Exclude Categories',
                'label_description' => 'These categories will be excluded from standard pages',
                'id' => 'exclude_cat',
                'type' => 'taxonomy_advanced',
                'taxonomy' => 'category',
                'field_type' => 'checkbox_tree',
            ],
            [
                'name' => 'Boilerplate',
                'label_description' => 'Boilerplate content to include at bottom of articles',
                'id' => 'boilerplate_html',
                'type' => 'wysiwyg',
                'raw' => true,
                'options' => [
                    'textarea_rows' => 8,
                    'teeny' => true,
                    'media_buttons' => false,
                ],
            ],
        ],
    ];

    return $meta_boxes;
}

/**
 * @return mixed[]
 */
function exclude_id_list(): array
{
    $value = [];
    if (! function_exists('rwmb_meta')) {
        return $value;
    }
    $terms = rwmb_meta('exclude_cat', ['object_type' => 'setting'], 'uamswp_news');
    if (! empty($terms) && ! is_wp_error($terms)) {
        foreach ($terms as $term) {
            $value[] = $term->term_id;
        }
    }

    return $value;
}

function uams_news_meta_boxes($meta_boxes)
{
    $meta_boxes[] = [
        'id' => 'newsroom',
        'title' => 'Newsroom Features',
        'post_types' => 'post',
        'context' => 'normal',
        'priority' => 'high',

        'fields' => [
            [
                'name' => 'Include Boilerplate',
                'id' => 'include_boilerplate',
                'type' => 'checkbox',
                'std' => 0,
            ],
            [
                'id' => 'news_release_pdf_url',
                'name' => 'News Release PDF',
                'type' => 'file_input',
                'desc' => 'Please upload PDF version of the Official News Release',
                'force_delete' => false,
            ],
            [
                'name' => 'Media Contact',
                'id' => 'media_contact',
                'type' => 'select',
                // Array of 'value' => 'Label' pairs
                'options' => [
                    // Create sidebar for each one
                    'Media Taylor-Peel' => 'Taylor-Peel',
                    'Media Peel-Dupins' => 'Peel-Dupins',
                    'Media Taylor-Dupins' => 'Taylor-Dupins',
                    'Media Taylor-Caldwell' => 'Taylor-Caldwell',
                ],
                // Allow to select multiple value?
                'multiple' => false,
                // Placeholder text
                'placeholder' => 'Select an group',
            ],
        ],
    ];
    $meta_boxes[] = [
        'title' => 'Custom Fields',
        'taxonomies' => 'category', // List of taxonomies. Array or string

        'fields' => [
            [
                'name' => 'Featured Image',
                'id' => 'cat_image',
                'type' => 'image_advanced',
            ],
        ],
    ];

    return $meta_boxes;
}

/**
 * @return mixed[]
 */
function add_img_column($column_array): array
{
    // I want to add my column at the beginning, so I use array_slice()
    // in other cases $column_array['featured_image'] = 'Featured Image' will be enough
    $column_array = array_slice($column_array, 0, 1, true)
    + ['img' => 'Featured Image'] // our new column for featured images
    + array_slice($column_array, 1, null, true);

    return $column_array;
}

function manage_img_column($column_name, $post_id)
{
    if ($column_name == 'img') {
        echo get_the_post_thumbnail($post_id, 'thimble');
    }

    return $column_name;
}

// function custom_field_excerpt($title) {
//     global $post;
//     $text = get_field($title);
//     if ( '' != $text ) {
//         $text = strip_shortcodes( $text );
//         $text = apply_filters('the_content', $text);
//         $text = str_replace(']]>', ']]>', $text);
//         $excerpt_length = 35; // 35 words
//         $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
//         $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
//     }
//     return apply_filters('the_excerpt', $text);
// }
// function wpdocs_custom_excerpt_length( $length ) {
//     return 35; // 35 words
// }
// add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );

function ap_date($string_date): string
{
    $ap_dates = [
        'Jan.', 'Feb.', 'March', 'April',
        'May', 'June', 'July', 'Aug.',
        'Sept.', 'Oct.', 'Nov.', 'Dec.',
    ];

    return $ap_dates[date('n', strtotime($string_date)) - 1].' '.
           date('j', strtotime($string_date)).', '.
           date('Y', strtotime($string_date));
}

function uamswp_register_api_hooks(): void
{

    // register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
    register_rest_field('post', 'fullcontent', [
        'get_callback' => 'uamswp_return_full_content',
        'schema' => null,
    ]);
}

function uamswp_return_full_content($object)
{
    // get the id of the post object array
    $post_id = $object['id'];

    $content = $object['content']['rendered'];
    $date = $object['date'];
    $includeboiler = get_post_meta($post_id, 'include_boilerplate', true);
    $boiler = get_uams_boilerplate();
    $beforecontent = '<span class="entrydate"><time datetime="'.$date.'" itemprop="datePublished">'.ap_date($date).'</time></span> | ';
    $first_para_pos = strpos($content, '<p>');
    $fullcontent = substr_replace($content, $beforecontent, $first_para_pos + 3, 0);
    if ($includeboiler == '1') {
        $fullcontent .= $boiler;
    }

    return $fullcontent;
}

function create_api_posts_meta_field(): void
{

    // register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
    register_rest_field('post', 'post-meta-fields', [
        'get_callback' => 'get_post_meta_for_api',
        'schema' => null,
    ]);
}

/**
 * Meta keys the public `post-meta-fields` REST field exposes.
 *
 * The posts endpoint answers anonymous requests, so this list is the whole surface: add a key
 * only when a consumer reads it, and never a `_`-prefixed (protected) key.
 */
function uamswp_api_post_meta_keys(): array
{
    return [
        'include_boilerplate',
        'media_contact',
        'news_release_pdf_url',
        'show_image_single',
    ];
}

/**
 * @return mixed[]
 */
function get_post_meta_for_api($object): array
{
    // get the id of the post object array
    $post_id = $object['id'];

    // return only the allowlisted meta, in the same key => array-of-values shape as before
    $meta = [];
    foreach (uamswp_api_post_meta_keys() as $key) {
        $values = get_post_meta($post_id, $key);
        if (! empty($values)) {
            $meta[$key] = $values;
        }
    }

    return $meta;
}
function create_api_posts_category(): void
{

    // register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
    register_rest_field('post', 'post_categories', [
        'get_callback' => 'get_post_categories_for_api',
        'schema' => null,
    ]);
}
function get_post_categories_for_api($object)
{
    // get the id of the post object array
    $post_id = $object['id'];

    // return the post meta
    return get_the_terms($post_id, 'category');
}
function uamswp_before_after($content)
{
    if (is_single() && is_main_query()) {
        $beforecontent = '<span class="entrydate"><time datetime="'.get_the_date('c').'" itemprop="datePublished">'.ap_date(get_the_date()).'</time><span> | ';
        $first_para_pos = strpos($content, '<p>');

        return substr_replace($content, $beforecontent, $first_para_pos + 3, 0);
    }

    return $content;
}

/**
 * Cut a title to at most `$length` characters, at a space, for the mobile sidebar button.
 *
 * A title with no space within the first `$length` characters is cut at `$length` itself, and a
 * `$length` below zero cuts it to nothing.
 *
 * Moved here from content-post.php, which declared it each time the template was included.
 */
function text_cut($text, $length, $dots): string
{
    // $text =get_the_title();
    $text = trim(preg_replace('#[\s\n\r\t]{2,}#', ' ', $text));
    $text_temp = $text;
    $requested = max(0, $length);
    while ($length > 0 && substr($text, $length, 1) !== ' ') {
        $length--;
        if ($length > strlen($text)) {
            break;
        }
    }
    // No space within the limit: cut at the limit.
    if ($length < 1) {
        $length = $requested;
    }
    $text = substr($text, 0, $length);

    return $text.(($dots == true && $text !== '' && strlen($text_temp) > $length) ? '...' : '');
}

/**
 * Register the theme's hooks, image sizes and post status.
 *
 * Called once, at the foot of this file, as WordPress includes it. It is a function so that a
 * test can run the registration again with the WordPress functions mocked.
 */
function uams_news_register(): void
{
    // Fix ACF Support
    add_filter('_includes/acf-pro/settings/show_admin', '__return_true');
    add_action('acf/input/admin_head', 'my_acf_admin_head');
    add_action('admin_notices', 'uams_news_meta_box_notice');
    add_filter('mb_settings_pages', 'prefix_options_page');
    // Register meta boxes and fields for settings page
    add_filter('rwmb_meta_boxes', 'uams_options_meta_boxes');
    add_filter('rwmb_meta_boxes', 'uams_news_meta_boxes');
    add_filter('manage_posts_columns', 'add_img_column');
    add_filter('manage_posts_custom_column', 'manage_img_column', 5, 2);

    /* Add Custom Image Sizes */
    add_image_size('news-full', 960, 540, true);
    add_image_size('news-half', 560, 315, true);
    add_image_size('news-third', 400, 225, true);
    add_image_size('news-quarter', 320, 180, true);
    add_image_size('uams_news', 560, 350, true); // 16x10 format cropped

    // UAMS 2020 Sizes
    add_image_size('aspect-16-9', 1024, 576, true);
    add_image_size('aspect-16-9-small', 512, 288, true);

    register_post_status('convert', [
        /* WordPress built in arguments. */
        'label' => __('Convert', 'wp-statuses'),
        'label_count' => _n_noop('To Convert <span class="count">(%s)</span>', 'To Convert <span class="count">(%s)</span>', 'wp-statuses'),
        'public' => false,
        'show_in_admin_all_list' => false,
        'show_in_admin_status_list' => true,

        /* WP Statuses specific arguments. */
        'post_type' => ['post', 'page'], // Only for posts!
        'show_in_metabox_dropdown' => true,
        'show_in_inline_dropdown' => true,
        'show_in_press_this_dropdown' => true,
        'labels' => [
            'metabox_dropdown' => __('To Be Converted', 'wp-statuses'),
            'inline_dropdown' => __('To Convert', 'wp-statuses'),
        ],
        'dashicon' => 'dashicons-yes',
    ]);

    add_action('rest_api_init', 'uamswp_register_api_hooks');
    add_action('rest_api_init', 'create_api_posts_meta_field');
    // Add post category title
    add_action('rest_api_init', 'create_api_posts_category');
    add_filter('the_content', 'uamswp_before_after');
}

uams_news_register();
