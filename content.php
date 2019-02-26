<?php
	$show_image = get_post_meta( get_the_ID(), 'show_image_single', true );
if ( ($show_image !== false ) && is_single() && get_post_thumbnail_id() ) { ?>
    <p class="featured-image">
      <a href="<?php echo get_the_post_thumbnail_url( get_the_ID(),'full' ); ?>" title="<?php echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); ?>" data-title="<?php echo get_post_field( 'post_title', get_post_thumbnail_id() ); ?>" data-caption="<?php echo get_post_field( 'post_excerpt', get_post_thumbnail_id() ); ?>">
        <span class="screen-reader-text"><?php esc_attr_e( 'View Larger Image', 'UAMS' ); ?></span>
      <?php the_post_thumbnail( 'post-image' ); ?>
      <?php if ( get_post_field('post_excerpt', get_post_thumbnail_id() )) { ?>
        <br/><span class="wp-caption-text"><?php echo get_post_field('post_excerpt', get_post_thumbnail_id()); ?></span>
        <?php } ?>
      </a>
    </p>
<?php
}
/*
if (is_single() || is_home()){
    the_date('F j, Y', '<p class="date">', '</p>');
}
*/
?>
<h1><?php the_title() ?></h1>
<?php
if ((is_single() || is_home()) && get_option('show_byline_on_posts')) :
?>
<div class="author-info">
    By <?php if ( function_exists( 'coauthors' ) ) { coauthors(); } else { the_author(); } ?>
    <p class="author-desc"> <small><?php the_author_meta(); ?></small></p>
</div>
<?php
endif;
  if ( ! is_home() && ! is_search() && ! is_archive() ) :
    uams_mobile_menu();
  endif;

?>

<?php


  if ( is_archive() || is_home() ) {
    the_post_thumbnail( array(130, 130), array( 'class' => 'attachment-post-thumbnail blogroll-img' ) );
    the_excerpt();
    echo "<hr>";
  } else
    the_content();
    //comments_template(true);
	if( is_single() && get_post_meta(get_the_ID(), 'include_boilerplate', true)) {
		echo get_uams_boilerplate();
	}
 ?>
