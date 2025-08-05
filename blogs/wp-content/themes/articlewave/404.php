<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
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
		<main id="primary" class="site-main">

			<section class="error-404 not-found">

				<span class="page-caption"><?php esc_html_e( '404' , 'articlewave' ); ?></span>

				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'articlewave' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<h3><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'articlewave' ); ?></h3>

						<div class ="page-search-wrapper">
						<?php
							$articlewave_enable_404_search = get_theme_mod( 'articlewave_enable_404_search' , true );

							if ( $articlewave_enable_404_search == true ) {
								get_search_form();
							}
						?>
						</div><!-- .page-search-wrapper -->
						
						<?php
							$articlewave_enable_404_home_button = get_theme_mod( 'articlewave_enable_404_home_button' , true );

							if ( $articlewave_enable_404_home_button == true) {
								echo '<a class="button-back-home" href="' . esc_url( get_home_url() ) . '">' . esc_html( 'Back to Home', 'articlewave' ) . '</a>';
							}
						?> 	
				</div><!-- .page-content -->

			</section><!-- .error-404 -->

		</main>

	</div><!-- articlewave-container-->
	
</div><!-- content -->

<?php
get_footer();
