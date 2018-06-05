<?php
/**
* Add an automatic default custom taxonomy for calendar.
* If no event (taxonomy) is set, the event will be sorted as “draft” and won’t return an offset error.
*
*/

// Fix ACF Support
add_filter('_includes/acf-pro/settings/show_admin', '__return_true');

add_action('acf/input/admin_head', 'my_acf_admin_head');

function my_acf_admin_head() {

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

function uams_primary_post_category() {

    if ( class_exists( 'WPSEO_Frontend' ) ) { // if Yoast SEO is active.
        $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_ID() ); // WPSEO_Primary_Term Object.
        $wpseo_primary_term = $wpseo_primary_term->get_primary_term(); // term_id.
        $term = get_term( $wpseo_primary_term ); // WP_Term Object.

        if ( $term && ! is_wp_error( $term ) ) { // if primary category term exists.
            $cat_name = get_cat_name( $wpseo_primary_term );

            $cat_link = get_category_link( $wpseo_primary_term );

            $cat = '<a href="' . $cat_link . '">' . $cat_name . '</a>';

            return $cat;
        } else { //return first category
            $categories = get_the_category();
    
            // Do nothing if there are no categories.
            if ( ! $categories ) {
                return '';
            }

            $i = 0;

            if ( $categories[$i]->term_id == '9' ) { // "Featured" category
                $i++;
            }
    
            $cat_name = esc_html($categories[$i]->name);
    
            $cat_link = get_category_link( $categories[$i]->term_id );
    
            $cat = '<a href="' . $cat_link . '">' . $cat_name . '</a>';
    
            return $cat;
        }

    } else { //return first category
        $categories = get_the_category();

        // Do nothing if there are no categories.
        if ( ! $categories ) {
            return '';
        }

        $i = 0;

        if ( $categories[$i]->term_id == '9' ) { // "Featured" category
            $i++;
        }

        $cat_name = esc_html($categories[$i]->name);

        $cat_link = get_category_link( $categories[$i]->term_id );

        $cat = '<a href="' . $cat_link . '">' . $cat_name . '</a>';

        return $cat;
    }

}

add_filter( 'mb_settings_pages', 'prefix_options_page' );
function prefix_options_page( $settings_pages ) {
    $settings_pages[] = array(
        'id'          => 'uamswp-news',
        'option_name' => 'uamswp_news',
        'menu_title'  => 'News Settings',
        //'parent'      => 'edit.php',
        'icon_url'    => 'dashicons-welcome-widgets-menus',
    );
    return $settings_pages;
}

// Register meta boxes and fields for settings page
add_filter( 'rwmb_meta_boxes', 'uams_options_meta_boxes' );
function uams_options_meta_boxes( $meta_boxes ) {
    $meta_boxes[] = array(
        'id'             => 'umas-news-options',
        'title'          => 'News Options',
        'settings_pages' => 'uamswp-news',
        //'tab'            => 'general',

        'fields' => array(
            array(
                'name' => 'Exclude Categories',
                'label_description' => 'These categories will be excluded from standard pages',
                'id'   => 'exclude_cat',
                'type' => 'taxonomy_advanced',
                'taxonomy' => 'category',
                'field_type' => 'checkbox_tree',
            ),
        ),
    );

    return $meta_boxes;
}

function exclude_id_list() {
    $terms =  rwmb_meta( 'exclude_cat', array( 'object_type' => 'setting' ), 'uamswp_news' ); 
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $value[] = $term->term_id;
            }
        }
    return $value;
}

add_filter( 'rwmb_meta_boxes', 'uams_news_meta_boxes' );
function uams_news_meta_boxes( $meta_boxes ) {
    $meta_boxes[] = array(
        'id'         => 'newsroom',
        'title'      => 'Newsroom Features',
        'post_types' => 'post',
        'context'    => 'normal',
        'priority'   => 'high',

        'fields' => array(
            array(
                'name'  => 'Include Boilerplate',
                'id'    => 'include_boilerplate',
                'type'  => 'checkbox',
                'std'   => 0,
            ),
            array(
                'id'    => 'news_release_pdf_url',
                'name'  => 'News Release PDF',
                'type'  => 'file_input',
                'desc'  => 'Please upload PDF version of the Official News Release',
            ),
            array(
                'name'            => 'Media Contact',
                'id'              => 'media_contact',
                'type'            => 'select',
                // Array of 'value' => 'Label' pairs
                'options'         => array(
                    'Media Taylor-Peel'     => 'Taylor-Peel',
                    'Media Peel-Dupins'     => 'Peel-Dupins',
                    'Media Taylor-Dupins'   => 'Taylor-Dupins',
                    'Media Taylor-Caldwell' => 'Taylor-Caldwell',
                ),
                // Allow to select multiple value?
                'multiple'        => false,
                // Placeholder text
                'placeholder'     => 'Select an group',
            ),
        )
    );
    return $meta_boxes;
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