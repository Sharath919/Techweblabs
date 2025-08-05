<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div id="content">

    <div class = "articlewave-container articlewave-clearfix">

        <div class="banner-tabbed-section-wrapper">
            <?php
                /**
                 * require file to display slider
                 *
                 * @since 1.0.0
                 */
                 get_template_part( 'template-parts/partials/banner/slider' );

                /**
                 * require file to display tab
                 *
                 * @since 1.0.0
                 */
                get_template_part( 'template-parts/partials/banner/tab' );
            ?>
        </div> <!-- banner-tabbed-section-wrapper -->

    </div>

    <div class = "articlewave-container articlewave-archive-content articlewave-clearfix">

        <?php articlewave_get_left_sidebar(); ?>

        <main id="primary" class="site-main ">

            <div class="articlewave-content-wrapper">
            <?php
                if ( have_posts() ) :

                    echo '<div class="articlewave-archive-posts-wrapper">';

                    if ( is_home() && ! is_front_page() ) :
            ?>
                        <header>
                            <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                        </header>
            <?php
                    endif;
                    /* Start the Loop */
                    while ( have_posts() ) :
                        the_post();
                         /*
                         * Include the Post-Type-specific template for the content.
                         * If you want to override this in a child theme, then include a file
                         * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                         */
                        get_template_part( 'template-parts/content', get_post_type() );
                    endwhile;

                    echo '</div><!-- articlewave-archive-posts-wrapper -->';
                    the_posts_pagination();

                else :

                    get_template_part( 'template-parts/content', 'none' );

                endif;
            ?>
            </div> <!--articlewave-content-wrapper-->

        </main><!-- #main -->

    <?php articlewave_get_right_sidebar(); ?>

    </div> <!-- articlewave-container -->
    
</div> <!-- content -->

<?php get_footer(); ?>