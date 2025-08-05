<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( has_post_thumbnail() ) {
	if ( is_front_page() || is_home() ) {
		$articlewave_front_post_display = get_theme_mod( 'articlewave_front_post_display', 'grid' );
		if ( 'classic' == $articlewave_front_post_display ) {
			$thumb_size = 'full';
		} else {
			$thumb_size = 'medium_large';
		}
	} elseif ( is_archive() ) {
		$articlewave_archive_post_display = get_theme_mod( 'articlewave_archive_post_display', 'grid' );
		if ( 'classic' == $articlewave_archive_post_display ) {
			$thumb_size = 'full';
		} else {
			$thumb_size = 'medium_large';
		}
	}
    $article_post_class = 'has-img-post';
} else {
    $article_post_class = 'no-img-post';
}

?>
<article id="post-<?php the_ID();?>" <?php post_class( $article_post_class ); ?>>

	<div class = "single-post-wrap ">

	    <?php if ( has_post_thumbnail() ) { ?>

	    <div class="post-thumbnail-wrap">
	    	<figure class="post-image <?php articlewave_image_hover_effect(); ?>" <?php articlewave_schema_markup( 'image' ); ?> >
		        <a href="<?php echo esc_url( get_the_permalink() ); ?>" <?php articlewave_schema_markup( 'url' ); ?>><?php the_post_thumbnail( $thumb_size ); ?></a>
		    </figure>
	    </div><!-- .post-thumbnail-wrap -->

		<?php } ?>

	    <div class="article-post-content-wrap">

			<div class="article-cat-item">
	            <?php articlewave_the_post_categories_list( get_the_ID(), 2 ); ?>
	        </div><!-- .article-cat-item -->

	        <header class="entry-header">

	        <?php
				if ( 'post' === get_post_type() ) :
			?>
	            <div class="entry-meta">
	             	<?php articlewave_posted_on(); ?>
	            </div><!-- .entry-meta -->

	        <?php 
	     		endif;

				if ( is_singular() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				endif; 
			?>

	        </header><!-- .entry-header -->

	        <?php
				$post_content_type  = apply_filters( 'articlewave_archive_post_content_type', 'excerpt' );
			?>

	        <div class="entry-content" <?php articlewave_schema_markup('entry_content'); ?>>
	        <?php
		        if ( ! is_singular() ) {

		            if ( 'excerpt' === $post_content_type ) {
		                the_excerpt();
		            } else {
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
		            }
		        } else {
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

		        }

		        wp_link_pages(
		            array(
		                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'articlewave' ),
		                'after'  => '</div>',
		            )
		        );
		    ?>
	        </div><!-- .entry-content -->

	     <?php 
	        if ( ! is_singular() ) { 
	     ?>
	        <div class="articlewave-read-more-est-time">
	        <?php
				$articlewave_enable_readmore = get_theme_mod( 'articlewave_enable_readmore' , true );
				$articlewave_enable_est_time_for_reading = get_theme_mod( 'articlewave_enable_est_time_for_reading' , true );

				if ( false !== $articlewave_enable_readmore ) { ?>
	            <div class="articlewave-button read-more-button">
	                <a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php esc_html_e( 'Continue Reading' , 'articlewave' ); ?></a>
	            </div> <!-- articlewave-button -->
	        <?php
				}
				if (  false !== $articlewave_enable_est_time_for_reading ) {
					
					articlewave_the_estimated_reading_time();
				}
			?>

	        </div><!-- articlewave-read-more-est-time-->
	        
	    </div><!-- article-post-content-wrap-->

	    <?php } ?>

	    <footer class="entry-footer">
	        <?php if ( ! is_home() && ! is_archive() ) { articlewave_entry_footer(); } ?>
	    </footer><!-- .entry-footer -->
	</div>

</article><!-- #post-<?php the_ID(); ?> -->