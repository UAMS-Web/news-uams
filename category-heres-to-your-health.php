<?php get_header(); ?>

<?php
	$term_id = get_queried_object()->term_id;
	$images = rwmb_meta( 'cat_image', array( 'object_type' => 'term', 'size' => 'full' ), $term_id );
	if ($images) {
		foreach ( $images as $image ) { ?>
			<div class="uams-hero-image hero-height" style="background-image:url(' <?php echo $image["full_url"]; ?>');" >
				<div id="hero-bg">
			      <div id="hero-container" class="container">
			        <h1 class="uams-site-title">Here's to Your Health</h1>
			        <h2>with Dr. T. Glenn Pait</h2>
			        <span class="udub-slant"><span></span></span>
			      </div>
			    </div>
			</div>
	<?php }
	} else { ?>
			<div class="uams-hero-image" <?php if ( get_header_image() !== '' )  { ?> style="background-image:url('<?php echo set_url_scheme( get_header_image() ); ?>');"<?php } ?> ></div>
	<?php } ?>

<div class="container uams-body">

  <div class="row">

    <div class="uams-content col-md-8" role='main'>

      <?php //uams_site_title(); ?>

      <?php //get_template_part( 'menu', 'mobile' ); ?>

      <?php //get_template_part( 'breadcrumbs' ); ?>

      <div id='main_content' class="uams-body-copy" tabindex="-1">
<!--       <h2>Here's to Your Health</h2> -->
      <?php if ( category_description() ) : // Show an optional category description ?>
        <div class="archive-meta"><?php echo category_description(); ?></div>
      <?php endif; ?>
        <?php if(!is_paged()): ?>
<!--
        <section class="cards featured-grid">
        <?php
          // Start the Loop.
          $featured_posts = new WP_Query( array(
            'posts_per_page' => 2, // One Big, six small
            'category_name' => 'featured+health' )
          );
          $i=1;

          if ( $featured_posts->have_posts() ) : while ( $featured_posts->have_posts() ) : $featured_posts->the_post();
              // Loop output goes here
            $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array
            ?>
            <article class="card top xs">
              <?php
               if ( has_post_thumbnail() ) : ?>
              <div class="card-image">
                <?php the_post_thumbnail( 'news-half' ); ?>
              </div>
              <?php endif; ?>
              <div class="card-stack">
                <div class="card-content">
                  <h3><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h3>
                  <?php the_excerpt(); ?>
                </div>
              </div>
            </article>

          <?php $i++; ?>
        <?php  endwhile; endif; ?>

        </section>
        </br>
-->
		<h2>Latest Episodes</h2>
		<?php else : ?>
		<h2> Page <?php echo $paged; ?></h2>
        <?php endif; ?>
        <section class="">
          <div class="cards">
            <?php
          // Start the Loop.
          $spotlight_posts = new WP_Query( array(
            'posts_per_page' => 10,
            'paged' => $paged,
            'category_name' => 'heres-to-your-health')
            //'post__not_in' => $do_not_duplicate )
          );

          if ( $spotlight_posts->have_posts() ) : while ( $spotlight_posts->have_posts() ) : $spotlight_posts->the_post();
              // Loop output goes here
            ?>
            <article class="card right boxed">
            <?php
               if ( has_post_thumbnail() ) : ?>
              <div class="card-image">
                <?php the_post_thumbnail( array(130, 130) ); ?>
              </div>
              <?php endif; ?>
              <div class="card-stack">
                <div class="card-content">
                  <!-- <span class="primary-category"><?php echo uams_primary_post_category(); ?></span> -->
                  <h3><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h3>
                  <?php the_excerpt(); ?>
                 </div>
              </div>
            </article>

            <?php $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array ?>
            <?php  endwhile; endif; ?>


          </div><!-- .inner-container -->
          </section><!-- #spotlight -->
          <?php posts_nav_link(' ', 'Previous page', 'Next page'); ?>
      </div>

    </div>

    <?php get_sidebar(); ?>

  </div>

</div>

<?php get_footer(); ?>
