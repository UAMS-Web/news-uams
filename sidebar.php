<?php if ( uams_has_sidebar() ) :  ?>

  <div class="col-md-4 uams-sidebar">
    <?php uams_sidebar_menu(); ?>
		<?php // Added by TM
		$pdf = false;
		$pdf = get_field('news_release_pdf_url', get_the_ID());
		if($pdf && is_single()) { ?>
			<div class="align-center widget"><a class="uams-btn btn-red btn-lg btn-pdf" target="_self" title="View News Release" href="<?php echo $pdf; ?>">View News Release</a></div>
	<?php } ?>
	<?php // Add News Contacts ?>
	<?php
		if('null' !== (get_post_meta(get_the_ID(), 'media_contact', true))) {
			//dynamic_sidebar( get_post_meta( get_the_ID(), 'media_contact', true ));
			//print('<br/>');
			//echo get_post_meta( get_the_ID(), 'media_contact', true );
			switch ( get_post_meta( get_the_ID(), 'media_contact', true ) ) {
				case "Media Taylor-Caldwell":
					echo '<div role="navigation" aria-label="sidebar_navigation" id="uams-contact-taylor-caldwell" class="widget contact-widget">' .
						 ' 	<div class="contact-widget-inner">' .
				      	 '	  <span>' .
					     ' 		  <h2 class="widgettitle">Media Contacts</h2>' .
				      	 '	  </span>' .
					  	 '    <h3 class="\'person-name\'">Leslie W. Taylor</h3>' .
					  	 '    <p><a href="tel:501-686-8998" class="person-phone">Office: 501-686-8998</a></p>' .
					  	 '    <p><a href="tel:501-951-7260" class="person-phone">Mobile: 501-951-7260</a></p>' .
					  	 '    <p><a href="mailto:leslie@uams.edu" class="person-email">Leslie@uams.edu</a></p>' .
					  	 '    <h3 class="\'person-name\'">Liz Caldwell</h3>' .
					  	 '    <p><a href="tel:501-686-8995" class="person-phone">Office: 501-686-8995</a></p>' .
					  	 '    <p><a href="tel:501-350-4364" class="person-phone">Mobile: 501-350-4364</a></p>' .
					  	 '    <p><a href="mailto:liz@uams.edu" class="person-email">Liz@uams.edu</a></p>' .
					  	 '	</div><!-- /.contact-widget-inner -->' .
					  	 '</div>';
					break;
				case "Media Taylor-Dupins":
					echo '<div role="navigation" aria-label="sidebar_navigation" id="uams-contact-taylor-dupins" class="widget contact-widget">' .
						 ' 	<div class="contact-widget-inner">' .
				      	 '	  <span>' .
					     ' 		  <h2 class="widgettitle">Media Contacts</h2>' .
				      	 '	  </span>' .
					  	 '    <h3 class="\'person-name\'">Leslie W. Taylor</h3>' .
					  	 '    <p><a href="tel:501-686-8998" class="person-phone">Office: 501-686-8998</a></p>' .
					  	 '    <p><a href="tel:501-951-7260" class="person-phone">Mobile: 501-951-7260</a></p>' .
					  	 '    <p><a href="mailto:leslie@uams.edu" class="person-email">Leslie@uams.edu</a></p>' .
					  	 '    <h3 class="\'person-name\'">Katrina Dupins</h3>' .
					  	 '    <p><a href="tel:501-686-8149" class="person-phone">Office: 501-686-8149</a></p>' .
					  	 '    <p><a href="tel:501-920-6977" class="person-phone">Mobile: 501-920-6977</a></p>' .
					  	 '    <p><a href="tel:501-395-5989" class="person-phone">Pager: 501-395-5989</a></p>' .
					  	 '    <p><a href="mailto:katrina@uams.edu" class="person-email">Katrina@uams.edu</a></p>' .
					  	 '	</div><!-- /.contact-widget-inner -->' .
					  	 '</div>';
					break;
				case "Media Peel-Dupins":
					echo '<div role="navigation" aria-label="sidebar_navigation" id="uams-contact-peel-dupins" class="widget contact-widget">' .
						 ' 	<div class="contact-widget-inner">' .
				      	 '	  <span>' .
					     ' 		  <h2 class="widgettitle">Media Contacts</h2>' .
				      	 '	  </span>' .
					  	 '    <h3 class="\'person-name\'">Andrea Peel</h3>' .
					  	 '    <p><a href="tel:501-686-8996" class="person-phone">Office: 501-686-8996</a></p>' .
					  	 '    <p><a href="tel:501-351-7903" class="person-phone">Mobile: 501-351-7903</a></p>' .
					  	 '    <p><a href="mailto:Andrea@uams.edu" class="person-email">Andrea@uams.edu</a></p>' .
					  	 '    <h3 class="\'person-name\'">Katrina Dupins</h3>' .
					  	 '    <p><a href="tel:501-686-8149" class="person-phone">Office: 501-686-8149</a></p>' .
					  	 '    <p><a href="tel:501-920-6977" class="person-phone">Mobile: 501-920-6977</a></p>' .
					  	 '    <p><a href="tel:501-395-5989" class="person-phone">Pager: 501-395-5989</a></p>' .
					  	 '    <p><a href="mailto:katrina@uams.edu" class="person-email">Katrina@uams.edu</a></p>' .
					  	 '	</div><!-- /.contact-widget-inner -->' .
					  	 '</div>';
					break;
				case "Media Taylor-Peel":
					echo '<div role="navigation" aria-label="sidebar_navigation" id="uams-contact-taylor-peel" class="widget contact-widget">' .
						 ' 	<div class="contact-widget-inner">' .
				      	 '	  <span>' .
					     ' 		  <h2 class="widgettitle">Media Contacts</h2>' .
				      	 '	  </span>' .
					  	 '    <h3 class="\'person-name\'">Leslie W. Taylor</h3>' .
					  	 '    <p><a href="tel:501-686-8998" class="person-phone">Office: 501-686-8998</a></p>' .
					  	 '    <p><a href="tel:501-951-7260" class="person-phone">Mobile: 501-951-7260</a></p>' .
					  	 '    <p><a href="mailto:leslie@uams.edu" class="person-email">Leslie@uams.edu</a></p>' .
					  	 '    <h3 class="\'person-name\'">Andrea Peel</h3>' .
					  	 '    <p><a href="tel:501-686-8996" class="person-phone">Office: 501-686-8996</a></p>' .
					  	 '    <p><a href="tel:501-351-7903" class="person-phone">Mobile: 501-351-7903</a></p>' .
					  	 '    <p><a href="mailto:andrea@uams.edu" class="person-email">Andrea@uams.edu</a></p>' .
					  	 '	</div><!-- /.contact-widget-inner -->' .
					  	 '</div>';
					break;

			}
	}
	?>
    <?php dynamic_sidebar( UAMS_Sidebar::ID ); ?>
  </div>

<?php endif; ?>
