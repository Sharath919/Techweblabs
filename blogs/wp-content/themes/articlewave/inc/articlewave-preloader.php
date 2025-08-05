<?php
/**
 * File to define functions and hooks related to preloader
 *
 * @package Articlewave
 */

if ( ! function_exists( 'articlewave_preloader_items' ) ) :

	/**
	 * function to manage the requested preloader items
	 *
	 * @since 1.0.0
	 */
	function articlewave_preloader_items() {

	$articlewave_enable_preloader = get_theme_mod( 'articlewave_enable_preloader', true );
	if ( false == $articlewave_enable_preloader ) {
		return;
	}

	$articlewave_preloader = get_theme_mod( 'articlewave_preloader', 'three_bounce' );

	?>
		<div id="articlewave-preloader" class="preloader-background">
			<div class="preloader-wrapper">
				<?php
					switch ( $articlewave_preloader ) {
						case 'three_bounce':
				?>
							<div class="articlewave-three-bounce">
	                            <div class="aw-child aw-bounce1"></div>
	                            <div class="aw-child aw-bounce2"></div>
	                            <div class="aw-child aw-bounce3"></div>
	                        </div>
				<?php
							break;

						case 'wave':
				?>
							<div class="articlewave-wave">
	                            <div class="aw-rect aw-rect1"></div>
	                            <div class="aw-rect aw-rect2"></div>
	                            <div class="aw-rect aw-rect3"></div>
	                            <div class="aw-rect aw-rect4"></div>
	                            <div class="aw-rect aw-rect5"></div>
	                        </div>
				<?php
							break;

						case 'folding_cube':
				?>
							<div class="articlewave-folding-cube">
	                            <div class="aw-cube1 aw-cube"></div>
	                            <div class="aw-cube2 aw-cube"></div>
	                            <div class="aw-cube4 aw-cube"></div>
	                            <div class="aw-cube3 aw-cube"></div>
	                        </div>
				<?php
							break;

						default:
				?>
							<div class="articlewave-three-bounce">
	                            <div class="aw-child aw-bounce1"></div>
	                            <div class="aw-child aw-bounce2"></div>
	                            <div class="aw-child aw-bounce3"></div>
	                        </div>
				<?php
							break;
					}
				?>
			</div><!-- .preloader-wrapper -->
		</div><!-- #articlewave-preloader -->
<?php
	}

endif;

add_action( 'articlewave_before_page', 'articlewave_preloader_items', 5 );