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

function uams_primary_post_category() {

    // if ( function_exists('pricat_get_categories') ) { // if Primary Category is active.
        $prim_category = get_post_meta( get_the_ID(), 'pricat_get_categories' );
        $prim_category_name = $prim_category[0]; // WPSEO_Primary_Term Object.
        $prim_category_term = get_term_by('slug', $prim_category_name, 'category'); // term_id.
        $term = get_term( $prim_category_term ); // WP_Term Object.

        if ( $term && ! is_wp_error( $term ) ) { // if primary category term exists.

            $cat_name = $term->name;

            $cat_link = get_category_link( $term );

            $cat = '<a href="' . $cat_link . '" title="'. $cat_name.'">' . $cat_name . '</a>';

            return $cat;

        } else { //return first category
            $categories = get_the_category();

            // Do nothing if there are no categories.
            if ( ! $categories ) {
                return '';
            }

            $i = 0;

            if ( $categories[$i]->term_id == '13' ) { // "Featured" category, 5 on live
                $i++;
            }

            $cat_name = esc_html($categories[$i]->name);

            $cat_link = get_category_link( $categories[$i]->term_id );

            $cat = '<a href="' . $cat_link . '" title="'. $cat_name.'">' . $cat_name . '</a>';

            return $cat;
        }

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

function get_uams_breadcrumbs()
  {

    global $post;
    $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
    $html = '<li><a href="http://www.uams.edu" title="University of Arkansas for Medical Scineces">Home</a></li>';
    $html .= '<li' . (is_front_page() ? ' class="current"' : '') . '><a href="' . home_url('/') . '" title="' . str_replace('   ', ' ', get_bloginfo('title')) . '">' . str_replace('   ', ' ', get_bloginfo('title')) . '</a><li>';

    if ( is_404() )
    {
        $html .=  '<li class="current"><span>Ooops!</span>';
    } else

    if ( is_search() )
    {
        $html .=  '<li class="current"><span>Search results for ' . get_search_query() . '</span>';
    } else

    if ( is_author() )
    {
        $author = get_queried_object();
        $html .=  '<li class="current"><span> Author: '  . $author->display_name . '</span>';
    } else

    if ( get_queried_object_id() === (Int) get_option('page_for_posts')   ) {
        $html .=  '<li class="current"><span> '. get_the_title( get_queried_object_id() ) . ' </span>';
    }

    // If the current view is a post type other than page or attachment then the breadcrumbs will be taxonomies.
    if( is_category() || is_tax() || is_single() || is_post_type_archive() )
    {

      if ( is_post_type_archive() && !is_tax() )
      {
        $posttype = get_post_type_object( get_post_type() );
        //$html .=  '<li class="current"><a href="'  . get_post_type_archive_link( $posttype->query_var ) .'" title="'. $posttype->labels->menu_name .'">'. $posttype->labels->menu_name  . '</a>';
        $html .=  '<li class="current"><span>'. $posttype->labels->menu_name  . '</span>';
      }

      if ( is_category() )
      {
        $category = get_category( get_query_var( 'cat' ) );
        //$html .=  '<li class="current"><a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
        $html .=  '<li class="current"><span>'. get_cat_name($category->term_id ) . '</span>';
      }

      if ( is_tax() && !is_post_type_archive() )
      {
        $term = get_term_by("slug", get_query_var("term"), get_query_var("taxonomy") );
        $tax = get_taxonomy( get_query_var("taxonomy") );
        //$html .=  '<li class="current"><a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
        $html .=  '<li class="current"><span>'. $tax->labels->name .': '. $term->name .'</span>';
      }

      if ( is_tax() && is_post_type_archive() )
      {
        $tax = get_term_by("slug", get_query_var("term"), get_query_var("taxonomy") );
        $posttype = get_post_type_object( get_post_type() );
        //$html .=  '<li class="current"><a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
        $html .=  '<li class="current"><span>'. $tax->name . '</span>';
      }

      if ( is_single() )
      {
        if ( has_category() )
        {
          $thecat = get_the_category( $post->ID  );
          $category = array_shift( $thecat ) ;
          $html .=  '<li>' . uams_primary_post_category();//<a href="'  . get_category_link( $category->term_id ) .'" title="'. get_cat_name( $category->term_id ).'">'. get_cat_name($category->term_id ) . '</a>';
        }
        if ( uams_is_custom_post_type() )
        {
          $posttype = get_post_type_object( get_post_type() );
          $archive_link = get_post_type_archive_link( $posttype->query_var );
          if (!empty($archive_link)) {
            $html .=  '<li><a href="'  . $archive_link .'" title="'. $posttype->labels->menu_name .'">'. $posttype->labels->menu_name  . '</a>';
          }
          else if (!empty($posttype->rewrite['slug'])){
            $html .=  '<li><a href="'  . site_url('/' . $posttype->rewrite['slug'] . '/') .'" title="'. $posttype->labels->menu_name .'">'. $posttype->labels->menu_name  . '</a>';
          }
        }
        $html .=  '<li class="current"><span>'. wp_trim_words(get_the_title( $post->ID ), 5, '...') . '</span>';
      }
    }

    // If the current view is a page then the breadcrumbs will be parent pages.
    else if ( is_page() )
    {

      if ( ! is_home() || ! is_front_page() )
        $ancestors[] = $post->ID;

      if ( ! is_front_page() )
      {
        foreach ( array_filter( $ancestors ) as $index=>$ancestor )
        {
          $class      = $index+1 == count($ancestors) ? ' class="current" ' : '';
          $page       = get_post( $ancestor );
          $url        = get_permalink( $page->ID );
          $title_attr = esc_attr( $page->post_title );
          if (!empty($class)){
            $html .= "<li $class><span>{$page->post_title}</span></li>";
          }
          else {
            $html .= "<li><a href=\"$url\" title=\"{$title_attr}\">{$page->post_title}</a></li>";
          }
        }
      }

    }

    return "<nav class='uams-breadcrumbs' aria-label='breadcrumbs'><ul>$html</ul></nav>";
  }

function get_uams_boilerplate() {
	$content = rwmb_meta( 'boilerplate_html', array( 'object_type' => 'setting' ), 'uamswp_news' );
    return $content;
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
            array(
                'name' => 'Boilerplate',
                'label_description' => 'Boilerplate content to include at bottom of articles',
                'id' => 'boilerplate_html',
                'type'    => 'wysiwyg',
                'raw'     => true,
                'options' => array(
                    'textarea_rows' => 4,
                    'teeny'         => true,
                    'media_buttons' => false,
                ),  
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
	                // Create sidebar for each one
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
    $meta_boxes[] = array(
        'title'      => 'Custom Fields',
        'taxonomies' => 'category', // List of taxonomies. Array or string

        'fields' => array(
            array(
                'name' => 'Featured Image',
                'id'   => 'cat_image',
                'type' => 'image_advanced',
            ),
        ),
    );
    return $meta_boxes;
}

add_filter('manage_posts_columns', 'add_img_column');
add_filter('manage_posts_custom_column', 'manage_img_column', 5, 2);

function add_img_column($column_array) {
    // I want to add my column at the beginning, so I use array_slice()
	// in other cases $column_array['featured_image'] = 'Featured Image' will be enough
	$column_array = array_slice( $column_array, 0, 1, true )
	+ array('img' => 'Featured Image') // our new column for featured images
	+ array_slice( $column_array, 1, NULL, true );

	return $column_array;
}

function manage_img_column($column_name, $post_id) {
    if( $column_name == 'img' ) {
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

/* Add Custom Image Sizes */
add_image_size( 'news-full', 960, 540, true );
add_image_size( 'news-half', 560, 315, true );
add_image_size( 'news-third', 400, 225, true );
add_image_size( 'news-quarter', 320, 180, true );
add_image_size( 'uams_news', 560, 350, true ); //16x10 format cropped

register_post_status( 'convert', array(
		/* WordPress built in arguments. */
		'label'                       => __( 'Convert', 'wp-statuses' ),
		'label_count'                 => _n_noop( 'To Convert <span class="count">(%s)</span>', 'To Convert <span class="count">(%s)</span>', 'wp-statuses' ),
		'public'                      => false,
		'show_in_admin_all_list'      => false,
		'show_in_admin_status_list'   => true,

		/* WP Statuses specific arguments. */
		'post_type'                   => array( 'post', 'page' ), // Only for posts!
		'show_in_metabox_dropdown'    => true,
		'show_in_inline_dropdown'     => true,
		'show_in_press_this_dropdown' => true,
		'labels'                      => array(
			'metabox_dropdown' => __( 'To Be Converted',        'wp-statuses' ),
			'inline_dropdown'  => __( 'To Convert',        'wp-statuses' ),
		),
		'dashicon'                    => 'dashicons-yes',
) );
function ap_date ($string_date) {
		$ap_dates = array (
			'Jan.', 'Feb.', 'March', 'April',
			'May.', 'June', 'July', 'Aug.',
			'Sept.', 'Oct.', 'Nov.', 'Dec.'
		);
		return $ap_dates[date('n', strtotime($string_date))-1] . ' ' .
			   date('j', strtotime($string_date)) . ', ' .
			   date('Y', strtotime($string_date));
	}


add_action( 'rest_api_init', 'uamswp_register_api_hooks' );
function uamswp_register_api_hooks() {

	// register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
	register_rest_field( 'post', 'fullcontent', array(
		'get_callback' => 'uamswp_return_full_content',
		'schema' => null,
	) );
}

function uamswp_return_full_content( $object ) {
	//get the id of the post object array
	$post_id = $object['id'];

	$content = $object['content']['rendered'];
	$date = $object['date'];
	$includeboiler = get_post_meta( $post_id, 'include_boilerplate', true );
	$boiler = get_uams_boilerplate();
	$beforecontent = '<span class="entrydate"><time datetime="' . $date .'" itemprop="datePublished">'. ap_date($date) .'</time></span> | ';
	$first_para_pos = strpos( $content, '<p>' );
	$fullcontent = substr_replace( $content, $beforecontent, $first_para_pos + 3, 0 );
	if($includeboiler == "1") {
		$fullcontent = $fullcontent . $boiler;
	}

	return  $fullcontent;
}



add_action( 'rest_api_init', 'create_api_posts_meta_field' );

function create_api_posts_meta_field() {

	// register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
	register_rest_field( 'post', 'post-meta-fields', array(
		'get_callback' => 'get_post_meta_for_api',
		'schema' => null,
	) );
}

function get_post_meta_for_api( $object ) {
	//get the id of the post object array
	$post_id = $object['id'];

	//return the post meta
	return get_post_meta( $post_id );
}
function uamswp_before_after($content) {
	if (is_single() && is_main_query()){
    	$beforecontent = '<span class="entrydate"><time datetime="' . get_the_date('c') .'" itemprop="datePublished">'. ap_date(get_the_date()) .'</time><span> | ';
		$aftercontent = '';
		$first_para_pos = strpos( $content, '<p>' );
		$fullcontent = substr_replace( $content, $beforecontent, $first_para_pos + 3, 0 );
	} else {
		$fullcontent = $content;
    }

    return $fullcontent;
}
add_filter('the_content', 'uamswp_before_after');