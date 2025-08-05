<?php
/**
 * Add Typography section and it's fields inside General Settings panel.
 * 
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', 'articlewave_register_typography_options' );

if ( ! function_exists( 'articlewave_register_typography_options' ) ) :

    /**
     * Register theme options for Typography section.
     * 
     * @param WP_Customize_Manager $wp_customize Object that holds the customizer data.
     * @since 1.0.0
     */
    function articlewave_register_typography_options( $wp_customize ) {        /*
         * Failsafe is safe
         */
        if ( ! isset( $wp_customize ) ) {
            return;
        }

        /**
         * Typography Font filed for body typography
         *
         * General Settings > Typography > Body
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_body_font_family',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_body_font_family' ),
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_setting( 'articlewave_body_font_weight',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_body_font_weight' ),
                'sanitize_callback' => 'sanitize_key',
                'transport'         => 'postMessage'
            )
        );
        $wp_customize->add_setting( 'articlewave_body_font_style',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_body_font_style' ),
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        $wp_customize->add_setting( 'articlewave_body_font_transform',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_body_font_transform' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                
            )
        );
        $wp_customize->add_setting( 'articlewave_body_font_decoration',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_body_font_decoration' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Typography (
            $wp_customize, 'body_typography',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_section_typography_body',
                    'settings'      => array(
                        'family'        => 'articlewave_body_font_family',
                        'weight'        => 'articlewave_body_font_weight',
                        'style'         => 'articlewave_body_font_style',
                        'transform'     => 'articlewave_body_font_transform',
                        'decoration'    => 'articlewave_body_font_decoration'
                    ),
                    'description'   => __( 'Select how you want your body fonts to appear.', 'articlewave' ),
                    'l10n'          => array() // Pass custom labels. Use the setting key (above) for the specific label.
                )
            )
        );

        /**
         * Typography Font filed for heading typography
         *
         * General Settings > Typography > Heading
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting(
            'articlewave_heading_font_family',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_heading_font_family' ),
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_setting(
            'articlewave_heading_font_weight',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_heading_font_weight' ),
                'sanitize_callback' => 'sanitize_key',
                'transport'         => 'postMessage'
            )
        );
        $wp_customize->add_setting(
            'articlewave_heading_font_style',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_heading_font_style' ),
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage'
            )
        );
        $wp_customize->add_setting(
            'articlewave_heading_font_transform',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_heading_font_transform' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                
            )
        );
        $wp_customize->add_setting(
            'articlewave_heading_font_decoration',
            array(
                'default'           => articlewave_get_customizer_default( 'articlewave_heading_font_decoration' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
                
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Typography (
            $wp_customize,
                'heading_typography',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_section_typography_heading',
                    'settings'      => array(
                        'family'        => 'articlewave_heading_font_family',
                        'weight'        => 'articlewave_heading_font_weight',
                        'style'         => 'articlewave_heading_font_style',
                        'transform'     => 'articlewave_heading_font_transform',
                        'decoration'    => 'articlewave_heading_font_decoration'
                    ),
                    'description'   => __( 'Select how you want your body fonts to appear.', 'articlewave' ),
                    'l10n'          => array() // Pass custom labels. Use the setting key (above) for the specific label.
                )
            )
        );
    }
endif;
