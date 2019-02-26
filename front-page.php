<?php get_header(); ?>

<?php //get_template_part( 'header', 'image' ); ?>

<div class="container uams-body margin-bottom-none">

  <div class="row">

    <div class="uams-content col-md-12" role='main'>

      <?php //uams_site_title(); ?>

      <?php //get_template_part( 'menu', 'mobile' ); ?>

      <?php //get_template_part( 'breadcrumbs' ); ?>

      <div id='main_content' class="uams-body-copy" tabindex="-1">

        <section class="cards featured-grid margin-top-one">
        <?php
          // Start the Loop.
          $featured_posts = new WP_Query( array(
            'posts_per_page' => 7, // One Big, six small
            'category_name' => 'featured' )
          );
          $i=1;

          if ( $featured_posts->have_posts() ) : while ( $featured_posts->have_posts() ) : $featured_posts->the_post();
              // Loop output goes here
            $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array
            ?>
            <article class="card top <?php echo ( $i == 1 ? 'med' : 'xs' ); ?>">
              <?php
               if ( has_post_thumbnail() ) : ?>
              <div class="card-image">
                <?php
                if ( $i == 1 ) {
                  the_post_thumbnail( 'news-half' );
                } else {
                  the_post_thumbnail( 'news-third' );
                }
                ?>
              </div>
              <?php endif; ?>
              <div class="card-stack">
                <div class="card-content">
                  <span class="primary-category"><?php echo uams_primary_post_category(); ?></span>
                  <h3><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h3>
                  <p><?php echo ap_date(get_the_date()) ?> | <?php echo(get_the_excerpt()); ?></p>
                </div>
                <div class="card-action"><i class="fas fa-user-circle"></i> <?php the_author(); ?></div>
              </div>
            </article>

          <?php $i++; ?>
        <?php  endwhile; endif; ?>

        </section>
        </br>
        <section id="spotlight" class="gray10 uams-full-width padding-one">
          <h2 class="text-center margin-top-quarter">University News</h2>
          <div class="inner-container cards">
            <?php
          // Start the Loop.
          $spotlight_posts = new WP_Query( array(
            'posts_per_page' => 3,
            'category__in' => 3852,
            'post__not_in' => $do_not_duplicate )
          );

          if ( $spotlight_posts->have_posts() ) : while ( $spotlight_posts->have_posts() ) : $spotlight_posts->the_post();
              // Loop output goes here
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
                  <!-- <span class="primary-category"><?php echo uams_primary_post_category(); ?></span> -->
                  <h3><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h3>
                  <p><?php echo ap_date(get_the_date()) ?> | <?php echo(get_the_excerpt()); ?></p>
                 </div>
                 <div class="card-action"><i class="fas fa-user-circle"></i> <?php the_author(); ?></div>
              </div>
            </article>

            <?php $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array ?>
            <?php  endwhile; endif; ?>


          </div><!-- .inner-container -->
          </section><!-- #spotlight -->
          <section>
          <h2 class="margin-top-half">More Stories</h2>
          <div class="row">
            <div class="col-md-4 cards">
              <!-- small cards News -->
              <?php
          // Start the Loop.
          $exclude_more_list = array("16885", "16883", "16878", "16894");
          $more_posts = new WP_Query( array(
              'posts_per_page' => 8,
              'category__not_in' => array_merge( exclude_id_list(), $exclude_more_list), // Exclude inside articles
              'post__not_in' => $do_not_duplicate )
          );
          $i=0;
          if ($more_posts->have_posts()) :
            //var_dump ($more_posts);
          while($more_posts->have_posts()) : $i++;
            if(($i % 2) == 0) : $more_posts->next_post(); else : $more_posts->the_post();
              // Loop output goes here
            ?>
            <article class="card xs gray5">
            <?php
               if ( has_post_thumbnail() ) : ?>
              <div class="card-image">
                <?php the_post_thumbnail( 'news-third' ); ?>
              </div>
              <?php endif; ?>
              <div class="card-stack">
                <div class="card-content">
                  <span class="primary-category"><?php echo uams_primary_post_category(); ?></span>
                  <h3><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h3>
                   <p><?php echo ap_date(get_the_date()) ?> | <?php echo(get_the_excerpt()); ?></p>
                </div>
                <div class="card-action"><i class="fas fa-user-circle"></i> <?php the_author(); ?></div>
              </div>
            </article>

            <?php $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array ?>
            <?php  endif; endwhile; ?>

            <?php $i = 0; rewind_posts(); ?>

            </div><!-- .col-md-4 -->
            <div class="col-md-4 cards">
            <?php
            while($more_posts->have_posts()) : $i++;
              if(($i % 2) !== 0) : $more_posts->next_post(); else : $more_posts->the_post();
                // Loop output goes here
              ?>
              <article class="card xs  gray5">
              <?php
               if ( has_post_thumbnail() ) : ?>
                <div class="card-image">
                  <?php the_post_thumbnail( 'news-third' ); ?>
                </div>
              <?php endif; ?>
                <div class="card-stack">
                  <div class="card-content">
                    <span class="primary-category"><?php echo uams_primary_post_category(); ?></span>
                    <h3><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></h3>
                     <p><?php echo ap_date(get_the_date()) ?> | <?php echo(get_the_excerpt()); ?></p>
                  </div>
                  <div class="card-action"><i class="fas fa-user-circle"></i> <?php the_author(); ?></div>
                </div>
              </article>

              <?php $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array ?>
              <?php  endif; endwhile; endif; ?>
            </div><!-- .col-md-4 -->
            <div class="col-md-4">
              <!-- Text Col News -->
              <h3 class="margin-top-none">Announcements</h3>
              <?php
          // Start the Loop.
          $exclude_announce_list = array("16885", "16883");
          $announce_posts = new WP_Query( array(
              'posts_per_page' => 10,
              'category_name' => 'announcements',
              'category__not_in' => array_merge( exclude_id_list(), $exclude_announce_list), // Exclude inside articles
              'post__not_in' => $do_not_duplicate )
          );

          if ( $announce_posts->have_posts() ) : ?>
          <script><?php print_r( exclude_id_list() ); ?></script>
            <ul>

           <?php while ( $announce_posts->have_posts() ) : $announce_posts->the_post();
              // Loop output goes here
            //if( $post->ID == $do_not_duplicate ) continue; // Add post to do not duplicate array
            ?>
            <li><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></li>

            <?php $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array ?>
            <?php  endwhile; ?>
            </ul>
            <?php endif; ?>

              <h3>Academic & Continuing Education Events</h3>
<!--
              <?php
          // Start the Loop.
          $college_posts = new WP_Query( array(
              'posts_per_page' => 5,
              'category_name' => 'academic-continuing-education-events',
              'category__not_in' => exclude_id_list(), // Exclude inside articles
              'post__not_in' => $do_not_duplicate )
          );

          if ( $college_posts->have_posts() ) : ?>
            <ul>
           <?php while ( $college_posts->have_posts() ) : $college_posts->the_post();
              // Loop output goes here
            //if( $post->ID == $do_not_duplicate ) continue; // Add post to do not duplicate array
            ?>
            <li><a href="<?php echo uams_get_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a></li>

            <?php $do_not_duplicate[] = $post->ID; // Add post to do not duplicate array ?>
            <?php  endwhile; ?>
            </ul>
            <?php endif; ?>
            <h3>Upcoming Events</h3>
-->
            <?php echo do_shortcode( '[events_list future=1 cat="16885" past=0 nb=5]' ); ?>
			<a class="more" href="https://news.uams.edu/upcoming-academic-continuing-education-events/">View More Events</a>

            </div><!-- .col-md-4 -->
          </div><!-- .row -->
          </section>
          <div class="gray10 uams-full-width padding-top-two">
            <div class="inner-container row">
            <div class="col-md-4">
              <!-- Calendar -->
              <h3>Upcoming Events</h3>
              <script class="ai1ec-widget-placeholder" data-widget="ai1ec_agenda_widget" data-events_seek_type="events" data-cat_ids="18,4,3,5" data-events_per_page="6" defer>
                (function(){var d=document,s=d.createElement('script'),
                i='ai1ec-script';if(d.getElementById(i))return;s.async=1;
                s.id=i;s.src='//calendar.uams.edu/?ai1ec_js_widget';
                d.getElementsByTagName('head')[0].appendChild(s);})();
              </script>

              <p><a href="/events/">View More Events</a></p>
            </div>
            <div class="col-md-4">
              <!-- Scroller :( -->
              <h3>UAMS In the News</h3>
              <iframe scrolling="no" allowtransparency="65535" width="240" frameborder="0" src="https://ec2a.newsdataservice.com/MMB_UAMS_HomepageScroll_Iframe.html" height="360" style="overflow-x: hidden;" class="DRAGDIS_iframe">
              </iframe>
            </div>
            <div class="col-md-4">
              <!-- Video -->
              <h3>Youtube</h3>
              <iframe width="400" height="300" src="https://www.youtube.com/embed/videoseries?list=PL046D8DC766672966" frameborder="0" allowfullscreen></iframe>
            </div>
          </div><!-- .row -->
        </div><!-- .uams-full-width -->

      </div>

    </div>

    <?php //get_sidebar() ?>

  </div>

</div>

<?php get_footer(); ?>
