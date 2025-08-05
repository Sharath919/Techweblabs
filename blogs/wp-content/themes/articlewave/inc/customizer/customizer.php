<?php
/**
 * Articlewave Theme Customizer
 *
 * @package Articlewave
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function articlewave_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'articlewave_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'articlewave_customize_partial_blogdescription',
			)
		);
	}

	require get_template_directory(). '/inc/customizer/custom-controls/mt-custom-controls.php';

	/**
     * Register theme upsell sections.
     *
     * @since 1.0.2
     */
    $wp_customize->add_section( new Articlewave_Section_Upsell(
        $wp_customize,
            'articlewave_theme_upsell',
            array(
            	'priority' 	=> 1,
                'title'    	=> esc_html__( 'Articlewave Pro', 'articlewave' ),
                'pro_text' 	=> esc_html__( 'Buy Now', 'articlewave' ),
                'pro_url'  	=> 'https://mysterythemes.com/pricing/?product_id=14401',          
            )
        )
    );
}
add_action( 'customize_register', 'articlewave_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function articlewave_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function articlewave_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function articlewave_customize_preview_js() {
	wp_enqueue_script( 'articlewave-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), ARTICLEWAVE_VERSION, true );
}
add_action( 'customize_preview_init', 'articlewave_customize_preview_js' );

function articlewave_customize_backend_scripts(){

	wp_enqueue_style( 'select2', get_template_directory_uri() . '/assets/library/select2/css/select2.css', null );

	wp_enqueue_style( 'articlewave-custom-control-styles', get_template_directory_uri() . '/inc/customizer/assets/css/custom-control-styles.css', array(), ARTICLEWAVE_VERSION );

	wp_enqueue_style( 'box-icons', get_template_directory_uri() . '/assets/library/box-icons/css/boxicons.css', array(), ARTICLEWAVE_VERSION );

	wp_enqueue_style( 'articlewave-extend-customizer', get_template_directory_uri() . '/inc/customizer/assets/css/extend-customizer.css', array(), ARTICLEWAVE_VERSION );

	wp_enqueue_script( 'select2', get_template_directory_uri() . '/assets/library/select2/js/select2.js', array( 'jquery' ), '4.0.13', true );

	wp_enqueue_script( 'articlewave-extend-customizer', get_template_directory_uri(). '/inc/customizer/assets/js/extend-customizer.js', array('jquery'), ARTICLEWAVE_VERSION, true );

}
add_action( 'customize_controls_enqueue_scripts', 'articlewave_customize_backend_scripts', 10 );

require get_template_directory(). '/inc/customizer/extend-customizer/class-customize-section.php';
require get_template_directory(). '/inc/customizer/extend-customizer/class-customize-panel.php';

require get_template_directory() . '/inc/customizer/controls/typography.php';
require get_template_directory() . '/inc/customizer/controls/general.php';
require get_template_directory() . '/inc/customizer/controls/header.php';
require get_template_directory() . '/inc/customizer/controls/banner.php';
require get_template_directory() . '/inc/customizer/controls/innerpage.php';
require get_template_directory() . '/inc/customizer/controls/footer-setting.php';
require get_template_directory() . '/inc/customizer/customizer-helper.php';
require get_template_directory() . '/inc/customizer/mt-active-callback.php';
require get_template_directory() . '/inc/customizer/mt-customizer-sanitize.php';