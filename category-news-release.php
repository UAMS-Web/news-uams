<?php get_header(); ?>

<?php //get_template_part( 'header', 'image' ); ?>

<div class="container uams-body">

  <div class="row">

    <div class="uams-content col-md-8" role='main'>

      <?php //uams_site_title(); ?>

      <?php //get_template_part( 'menu', 'mobile' ); ?>

      <?php //get_template_part( 'breadcrumbs' ); ?>

      <div id='main_content' class="uams-body-copy" tabindex="-1">
      <h2>News Releases</h2>
        <style>
            .archives h2,
            .archives h3 {
                /* border-bottom: 1px solid #97999B; */
                margin-top: 0;
                position: relative;
                padding-bottom: 20px;
            }
            .archives h2:before,
            .archives h3:before,
            .archives h2:after,
            .archives h3:after {
                position: absolute;
                left: 0;
                bottom: 5px;
                content: "";
                height: 4px;
                width: 100px;
                background-color: #5d5b5c;
            }
            .archives h2:after,
            .archives h3:after {
                width: 40px;
                -webkit-transform: skewX(-25deg) skewY(0);
                -o-transform: skewX(-25deg) skewY(0);
                transform: skewX(-25deg) skewY(0);
                -webkit-transform: skew(-25deg, 0);
                -ms-transform: skewX(-25deg) skewY(0);
                transform: skew(-25deg, 0);
                left: 80px;
                background-color: #fff;
                bottom: 3px;
                height: 8px;
            }
            ul.archives {
                margin-left: 0;
                padding-left: 0;
                margin-top: 2rem;
            }

            ul.archives > li {
                margin-left: 0;
                margin-bottom: 4rem;
            }

            ul.archives li {
                list-style-type: none;
                list-style-image: none;
            }

            .monthly-archives > li {
                margin-bottom: 2rem;
            }

            .monthly-archives {
                margin-top: 1rem;
            }

            .page-template-page_custom_archive-php .entry-content ol li li, .page-template-page_custom_archive-php .entry-content ul li li {
                margin-left: 3rem;
            }
        </style>
        <section class="">
            <ul class="archives">
            <?php
            // WP_Query arguments.
            $args = array(
                'category_name' => 'news-release',
                'posts_per_page' => -1,
                'order' => 'DESC',
            );

            // The Query.
            $query = new WP_Query( $args );

            $year = '';
		    $month = '';

            // The Loop.
            if ( $query->have_posts() ) {

                while ( $query->have_posts() ) {
                    $query->the_post();

                    if(ucfirst(get_the_time('F')) != $month && $month != ''){
                        echo '</ul></li>';
                    }
                    if(get_the_time('Y') != $year && $year != ''){
                        echo '</ul></li>';
                    }
                    if(get_the_time('Y') != $year){
                        $year = get_the_time('Y');
                        echo '<li><h2>' . $year . '</h2><ul class="monthly-archives">';
                    }
                    if(ucfirst(get_the_time('F')) != $month){
                        $month = ucfirst(get_the_time('F'));
                        echo '<li><h3>' . $month . '</h3><ul>';
                    }

                ?>

                    <li>
                        <span class="the_date"><?php the_time('d') ?>:</span>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </li>
                <?php
                    // // current post's published date month.
                    // $current_post_month = get_the_date( 'F' );

                    // if ( 0 === $query->current_post ) { // for the oldest post, show the Month Year.
                    //     echo '<h3 class="date">';
                    //         the_date( 'F Y' );
                    //     echo '</h3>';
                    // } else { // for all other posts, get the month of the previous post in the loop.
                    //     $p = $query->current_post - 1;
                    //     $previous_post_month = mysql2date( 'F', $query->posts[ $p ]->post_date );
                    // }

                    // // if the current post's month does not match with that of the next one, show the Month Year.
                    // if ( $previous_post_month !== $current_post_month ) {
                    //     echo '</ul><h3 class="date">';
                    //         the_date( 'F Y' );
                    //     echo '</h3><ul>';
                    // }

                    // // show the linked title.
                    // //printf( '<p><span class="the_date">%s:&nbsp;</span><a href="%s">%s</a></p>', get_the_time( 'd' ), esc_url( get_permalink() ), get_the_title() );
                    // printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink() ), get_the_title() );
                }
                echo '</ul>';
            } else {
                // no posts found.
            } ?>
          </section><!-- #spotlight -->
      </div>

    </div>

    <?php get_sidebar() ?>

  </div>

</div>

<?php get_footer(); ?>
