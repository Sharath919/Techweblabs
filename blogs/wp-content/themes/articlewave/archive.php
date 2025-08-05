<?php
/**
 * The template for displaying archive pages
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

<?php

    /**
     * require file to display archive post title
     *
     * @since 1.0.0
     */
             
    get_template_part( 'template-parts/partials/innerpage/archive' , 'title' );

?>

    <div class="articlewave-container articlewave-clearfix">

        <?php articlewave_get_left_sidebar(); ?>

        <main id="primary" class="site-main ">
        <?php 
            if ( have_posts() ) :
            $articlewave_archive_post_display = get_theme_mod( 'articlewave_archive_post_display' , 'grid' )
        ?>
            <div class="articlewave-content-wrapper archive-page-layout-<?php echo esc_attr( $articlewave_archive_post_display ) ?>">
                <?php
                    echo '<div class="articlewave-archive-posts-wrapper">';
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

                    echo '</div><!-- .articlewave-archive-posts-wrapper -->';
                    the_posts_pagination();

                else :

                    get_template_part( 'template-parts/content', 'none' );
                ?>

                </div> <!-- articlewave-content-wrapper -->
        <?php
            endif;
        ?>
           
        </main><!-- #main -->

    <?php articlewave_get_right_sidebar(); ?>

    </div> <!-- articlewave-container -->

</div> <!-- content -->

<?php get_footer(); ?>