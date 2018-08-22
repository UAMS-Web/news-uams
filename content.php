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
if (is_single() || is_home()){
    the_date('F j, Y', '<p class="date">', '</p>');
}
?>
<h1><?php the_title() ?></h1>
<?php
if ((is_single() || is_home()) && get_option('show_byline_on_posts')) :
?>
<div class="author-info">
    <?php if ( function_exists( 'coauthors' ) ) { coauthors(); } else { the_author(); } ?>
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
   if( is_single() && get_post_meta(get_the_ID(), 'include_boilerplate', true)) { ?>
      <!-- #Begin UAMS Boilerplate -->
      <hr size="1" width="85%"/><br/>
      <p>UAMS is the state’s only health sciences university, with colleges of Medicine, Nursing, Pharmacy, Health Professions and Public Health; a graduate school; hospital; northwest Arkansas regional campus; statewide network of regional centers; and seven institutes: the Winthrop P. Rockefeller Cancer Institute, Jackson T. Stephens Spine &amp; Neurosciences Institute, Myeloma Institute, Harvey &amp; Bernice Jones Eye Institute, Psychiatric Research Institute, Donald W. Reynolds Institute on Aging and Translational Research Institute. It is the only adult Level 1 trauma center in the state. UAMS has 2,834 students, 822 medical residents and six dental residents. It is the state’s largest public employer with more than 10,000 employees, including 1,200 physicians who provide care to patients at UAMS, its regional campuses throughout the state, Arkansas Children’s Hospital, the VA Medical Center and Baptist Health. Visit <a href="http://www.uams.edu/">www.uams.edu</a> or <a href="http://www.uamshealth.com/" title="http://www.uamshealth.com/">www.uamshealth.com</a>. Find us on <a href="https://www.facebook.com/UAMShealth">Facebook</a>, <a href="https://twitter.com/uamshealth">Twitter</a>, <a href="https://www.youtube.com/user/UAMSHealth">YouTube</a> or <a href="https://instagram.com/uamshealth/">Instagram</a>.</p>
      <p class="align-center textcenter">###</p>
      <!-- #End UAMS Boilerplate -->
    <?php }
 ?>
