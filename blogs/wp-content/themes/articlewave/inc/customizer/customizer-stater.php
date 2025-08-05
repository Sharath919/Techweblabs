<?php
/**
 * Includes theme customizer defaults and starter functions.
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'articlewave_get_customizer_option_value' ) ) :

	/**
	 * Get the customizer value `get_theme_mod()`
	 *
	 * @since 1.0.0
	 */
	function articlewave_get_customizer_option_value( $setting_id ) {

		return get_theme_mod( $setting_id, articlewave_get_customizer_default( $setting_id ) );

	}

endif;

if ( ! function_exists( 'articlewave_get_customizer_default' ) ) :

	/**
	 * Returns an array of the desired default Articlewave Options
	 *
	 * @return array
	 */
	function articlewave_get_customizer_default( $setting_id ) {

	$default_values = apply_filters( 'articlewave_get_customizer_defaults',
			array(
				//body typography
				'articlewave_body_font_family'                      => 'Roboto',
				'articlewave_body_font_weight'                      => '400',
				'articlewave_body_font_style'                       => 'normal',
				'articlewave_body_font_transform'                   => 'inherit',
				'articlewave_body_font_decoration'                  => 'inherit',
				
				//Heading
                'articlewave_heading_font_family'                   => 'Nunito',
                'articlewave_heading_font_weight'                   => '700',
                'articlewave_heading_font_style'                    => 'normal',
                'articlewave_heading_font_transform'                => 'inherit',
                'articlewave_heading_font_decoration'               => 'inherit',
                )
			);

		return  $default_values[$setting_id];
	}
endif;
