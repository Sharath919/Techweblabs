<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Articlewave
 */

get_header();

?>
<div id="content">

	 <div class = "articlewave-container articlewave-archive-content articlewave-clearfix">

        <?php articlewave_get_left_sidebar(); ?>

        <main id="primary" class="site-main ">

            <header class="page-header">

                <h1 class="page-title">
                    <?php
                    /* translators: %s: search query. */
                    printf( esc_html__( 'Search Results for: %s', 'articlewave' ), '<span>' . get_search_query() . '</span>' );
                    ?>
                </h1>
                
            </header><!-- .page-header -->

            <div class="articlewave-content-wrapper">

        <?php

            if ( have_posts() ) :

                echo '<div class="articlewave-archive-posts-wrapper">';

                /* Start the Loop */
                while ( have_posts() ) :
                    the_post();
                     /*
                     * Include the Post-Type-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                     */
                    get_template_part( 'template-parts/content', 'search' );
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

</div><!--content-->
<?php
get_footer();
