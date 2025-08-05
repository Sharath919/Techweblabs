<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
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

	<div class = "articlewave-container articlewave-clearfix articlewave-page">

	<?php articlewave_get_left_sidebar(); ?>

	<main id="primary" class="site-main">

		<div class="articlewave-content-wrapper">
		<?php
			while ( have_posts() ) :
			the_post();
		?>
			<div class ="single-post-content-wrap <?php if ( ! has_post_thumbnail() ) { echo esc_attr( 'no-img-post' ); } ?>" >
				<div class="single-post-thumb-cat">
					<div class="post-thumbnail-wrap">
				        <figure class="post-image <?php articlewave_image_hover_effect(); ?>"<?php articlewave_schema_markup('image') ?>>
				            <a href="<?php echo esc_url( get_the_permalink() ); ?>"  <?php articlewave_schema_markup( 'url' ); ?>><?php the_post_thumbnail( 'full' ); ?></a>
				        </figure>
					</div><!-- .post-thumbnail-wrap -->

					<div class="entry-header">
					 	<div class="article-cat-item">
				            <?php articlewave_the_post_categories_list( get_the_ID(), 2 ); ?>
				        </div><!-- .article-post-content-wrap -->

			        <?php
						if ( 'post' === get_post_type() ) :
					?>
				            <div class="entry-meta">
				                <?php articlewave_posted_on(); ?>
				            </div><!-- .entry-meta -->
				    <?php
				 		endif;
						the_title( '<h1 class="entry-title">', '</h1>' );
					?>
			        </div><!-- .entry-header -->
				</div><!-- single-post-thumb-cat -->

				<div class ="entry-content"  <?php articlewave_schema_markup('entry_content'); ?>>
				<?php
					the_content(
		                sprintf(
		                    wp_kses(
		                        /* translators: %s: Name of current post. Only visible to screen readers */
		                        __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'articlewave' ),
		                        array(
		                            'span' => array(
		                                'class' => array(),
		                            ),
		                        )
		                    ),
		                    wp_kses_post( get_the_title() )
		                )
		            );
				?>
				</div><!-- entry-content -->
			</div><!-- single-post-content-wrap -->

		<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'articlewave' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'articlewave' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

			 /**
             * require file to display author box
             *
             * @since 1.0.0
             */
             get_template_part( 'template-parts/partials/innerpage/author' );


		endwhile; // End of the loop.

		/**
         * require file to display related post
         *
         * @since 1.0.0
         */
         get_template_part( 'template-parts/partials/innerpage/relatedpost' );

		?>
		
	</div><!-- articlewave-content-wrapper -->

	</main><!-- #main -->

	<?php articlewave_get_right_sidebar(); ?>
	
	</div> <!-- articlewave-container  -->

</div> <!-- content -->

<?php get_footer(); ?>
