<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Articlewave
 */

if ( ! function_exists( 'articlewave_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function articlewave_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( ' %s', 'post date', 'articlewave' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);
		$publish_date = articlewave_get_schema_markup('publish_date');

		echo '<span class="posted-on" '.$publish_date.'>' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'articlewave_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function articlewave_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'articlewave' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'articlewave_the_post_categories_list' ) ) :

	/**
	 * function to display the lists of post categories
	 * 
	 * @since 1.0.0
	 */
	function articlewave_the_post_categories_list( $post_id, $list_count ) {
		$categories_list = wp_get_post_categories( $post_id, array( 'number' => absint( $list_count ) ) );
		if ( empty( $categories_list ) ) {
			return;
		}
		echo '<ul class="articlewave-post-cats-list">';
		foreach ( $categories_list as $category ) {
			echo '<li class="post-cat-item cat-'. esc_attr( $category ) .'"><a href="'. esc_url( get_category_link( $category ) ) .'" rel="category tag">'. esc_html( get_cat_name( $category ) ) .'</a></li>';
		}
		echo '</ul><!-- .post-cats-list -->';
	}

endif;

if ( ! function_exists( 'articlewave_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function articlewave_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'articlewave' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'articlewave' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'articlewave' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'articlewave' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'articlewave' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'articlewave' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'articlewave_image_hover_effect' ) ) :

	/**
	 * Hover Effect for image
	 *
	 */

	function articlewave_image_hover_effect() {

		$articlewave_image_hover_effect = get_theme_mod( 'articlewave_image_hover_effect' , 'one' );

		if ( $articlewave_image_hover_effect == 'one' ){
			echo 'hover-effect--one';
		} else{
			echo 'hover-effect--none';
		}
	}

endif; 

if ( ! function_exists( 'articlewave_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function articlewave_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;


if ( ! function_exists( 'articlewave_the_estimated_reading_time' ) ) :
	/**
	 * function to display the estimated reading time for post content.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_the_estimated_reading_time( $post_id = NULL ) {

		$post_words_per_minute = apply_filters( 'articlewave_post_words_per_minute', 150 );

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$get_post_content = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );

		$content_decode = html_entity_decode( $get_post_content );

		$do_shortcode_decode = do_shortcode( $content_decode );

		$all_tags_strips = wp_strip_all_tags( $do_shortcode_decode );

		$get_post_content_words = str_word_count( wp_strip_all_tags( do_shortcode( html_entity_decode( $get_post_content ) ) ) );
		$read_per_minute = floor( $get_post_content_words / $post_words_per_minute );

		if ( $read_per_minute < 1 || $read_per_minute == 1 ) {
			$read_per_minute = 1;
			$minute_label = __( 'min read', 'articlewave' );
		} else {
			$minute_label = __( 'mins read', 'articlewave' );
		}

		$output_string = sprintf( __( '%1$s %2$s', 'articlewave' ), $read_per_minute, $minute_label );
		echo '<span class="post-min-read">'. esc_html( $output_string ) .'</span><!-- .post-min-read -->';
		
	}

endif;


if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;
