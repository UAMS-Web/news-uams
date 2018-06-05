<?php if ( uams_has_sidebar() ) :  ?>

  <div class="col-md-4 uams-sidebar">
    <?php uams_sidebar_menu(); ?>
    <?php // Added by TM
		$pdf = get_field('news_release_pdf_url', get_the_ID());
		if($pdf) { ?>
			<div class="align-center widget"><a class="uams-btn btn-red btn-lg btn-pdf" target="_self" title="View News Release" href="<?php echo $pdf; ?>">View News Release</a></div>
	<?php } ?>
	<?php // Add News Contacts ?>
	<?php
		if('null' !== (get_post_meta(get_the_ID(), 'media_contact', true))) {
			dynamic_sidebar( get_post_meta( get_the_ID(), 'media_contact', true ));
			//print('<br/>');
			//echo get_post_meta( get_the_ID(), 'media_contact', true );
	}
	?>
    <?php dynamic_sidebar( UAMS_Sidebar::ID ); ?>
  </div>

<?php endif; ?>
