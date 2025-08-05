<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

	<footer id="colophon" class="site-footer" <?php articlewave_schema_markup( 'footer' ); ?>>

		<div class = "articlewave-footer articlewave-container">

			<div class="site-info">
		<?php 	
				$articlewave_enable_widget_area = get_theme_mod( 'articlewave_enable_widget_area' , true );

				if ( $articlewave_enable_widget_area == true )	{
					
					/**
	                 * require file to display footer widget area
	                 *
	                 * @since 1.0.0
	                 */
					get_template_part( 'template-parts/partials/footer/footer' );
				}

				/**
                 * require file to display footer bottom area
                 *
                 * @since 1.0.0
                 */
				get_template_part( 'template-parts/partials/footer/footer','text' );
		?>
			</div><!-- .site-info -->

		</div> <!-- articlewave-footer -->

	</footer><!-- #colophon -->

</div><!-- #page -->

<?php
/**
 * require file to display scroll top
 *
 * @since 1.0.0
 */
get_template_part( 'template-parts/partials/footer/scroll','top' );
wp_footer(); 
?>
</body>
</html>
