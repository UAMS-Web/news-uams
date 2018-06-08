<?php get_header(); ?>

<?php //get_template_part( 'header', 'image' ); ?>

<div class="container uams-body">

  <div class="row">

    <div class="uams-content col-md-8" role='main'>

      <?php //uams_site_title(); ?>

      <?php //get_template_part( 'menu', 'mobile' ); ?>

      <?php //get_template_part( 'breadcrumbs' ); ?>

      <div id='main_content' class="uams-body-copy" tabindex="-1">
      <h2>Patient Stories</h2>
        <?php if(!is_paged()): ?>
        <section class="cards featured-grid">
        <?php
          // Start the Loop.
          $featured_posts = new WP_Query( array(
            'posts_per_page' => 2, // One Big, six small
            'category_name' => 'featured+patient-stories' ) 
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
                <?php the_post_thumbnail( 'large' ); ?>
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
        <?php endif; ?>
        <section class="">
          <div class="cards">
            <?php
          // Start the Loop.
          $spotlight_posts = new WP_Query( array( 
            'posts_per_page' => 10, 
            'paged' => $paged,
            'post__not_in' => $do_not_duplicate ) 
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

    <?php get_sidebar() ?>

  </div>

</div>

<?php get_footer(); ?>
