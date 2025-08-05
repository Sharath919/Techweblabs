<?php
/**
 * Footer Settings panel.
 * 
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Footer Setting
 * 
 * @param WP_Customize_Manager $wp_customize Object that holds the customizer data.
 * @since 1.0.0
 */

if ( ! function_exists( 'articlewave_footer_setting_customize_register' ) ) :

    function articlewave_footer_setting_customize_register( $wp_customize ) {

     	$wp_customize->add_panel( 'articlewave_footer_setting',
            array(
                'priority'  => 5,
                'title'     => __( 'Footer Settings', 'articlewave' )
            )
        );

        // --------------------------------Widget Area--------------------------------------

        /**
         * Widget Area Setting
         *
         * General Settings > Footer Setting > Widget Area
         *
         * @since 1.0.0
         */

     	 $wp_customize->add_section( 'articlewave_widget_area',
	        array(
	            'priority' => 1,
	            'panel'     => 'articlewave_footer_setting',
	            'title'     => __( 'Widget Area', 'articlewave' )
	        )
	    );

     	$wp_customize->add_setting( 'articlewave_enable_widget_area',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_widget_area',
                array(
                    'priority'      => 1,
                    'section'       => 'articlewave_widget_area',
                    'settings'      => 'articlewave_enable_widget_area',
                    'label'         => __( 'Enable Footer Main Area', 'articlewave' ),
                )
            )
        );

        $wp_customize->add_setting( 'articlewave_widget_area_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'column-two',
                'sanitize_callback' => 'sanitize_key'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Radio_Image(
            $wp_customize, 'articlewave_widget_area_layout',
                array(
                    'priority'      => 2,
                    'section'       => 'articlewave_widget_area',
                    'settings'      => 'articlewave_widget_area_layout',
                    'label'         => __( 'Column Layout', 'articlewave' ),
                    'description'   => __( 'Choose from available layouts', 'articlewave' ),
                    'choices'       => articlewave_footer_widget_area_layout_choices(),
                    'active_callback'   => 'articlewave_has_enable_widget_area_layout'
                )
            )
        );

        /**
         * Upgrade field for main banner section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_footer',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_footer',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_widget_area',
                    'settings'      => 'articlewave_upgrade_footer',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_footer' )
                )
            )
        );


        //--------------------------------Bottom Footer--------------------------------------

        /**
         * Widget Area Setting
         *
         * General Settings > Footer Setting > Bottom Footer
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( 'articlewave_bottom_footer_area',
	        array(
	            'priority' => 2,
	            'panel'     => 'articlewave_footer_setting',
	            'title'     => __( 'Bottom Footer', 'articlewave' )
	        )
	    );


         $wp_customize->add_setting( 'articlewave_bottom_footer_field',
            array(
                'default'   => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( 'articlewave_bottom_footer_field',
            array(
                'priority'  => 1,
                'section'   => 'articlewave_bottom_footer_area',
                'setting'   => 'articlewave_bottom_footer_field',
                'label'     => __( 'Footer Area', 'articlewave' ),
                'type'      => 'textarea',
                'input_attrs' => array(
                    'placeholder'   => __( 'Copyright © articlewave {year} ', 'articlewave' )
                ),

            )
        );

        $wp_customize->selective_refresh->add_partial(
        'articlewave_bottom_footer_field',
            array(
                'selector' => ' .footer-text',
                'render_callback' => 'articlewave_customize_partial_footer_text'
            )
        );
    }

endif;

add_action( 'customize_register', 'articlewave_footer_setting_customize_register' );
