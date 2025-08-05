<?php
/**
 * Header Setting panel.
 * 
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Header Setting
 * 
 * @param WP_Customize_Manager $wp_customize Object that holds the customizer data.
 * @since 1.0.0
 */

if ( ! function_exists( 'articlewave_header_customize_register' ) ) :

    function articlewave_header_customize_register( $wp_customize ) {

     	$wp_customize->add_panel( 'articlewave_header_setting',
            array(
                'priority'  => 2,
                'title'     => __( 'Header Settings', 'articlewave' )
            )
        );

// --------------------------Site Identity-----------------------------------  

         /**
         * Site Style style section
         *
         * Header Settings > Site Identity
         *
         * @since 1.0.0
         */

        $wp_customize->get_section( 'title_tagline' )->panel = 'articlewave_header_setting';
        $wp_customize->get_section( 'title_tagline' )->priority = 1;

        $wp_customize->get_control( 'header_textcolor' )->section    = 'title_tagline';
        $wp_customize->get_control( 'header_textcolor' )->priority    = 1;


        // --------------------------Header Image-----------------------------------

         /**
         *
         * Header Settings > Header Image
         *
         * @since 1.0.0
         */
         
        $wp_customize->get_section( 'header_image' )->panel = 'articlewave_header_setting';
        $wp_customize->get_section( 'header_image' )->priority = 2;

        // --------------------------Header Items-----------------------------------
        
        /**
         * Site Header Items Choices
         *
         * General Settings > Header Items
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( 'articlewave_header_item_section',
            array(
                'priority' => 3,
                'panel'     => 'articlewave_header_setting',
                'title'     => __( 'Header Items', 'articlewave' )
            )
        );

         /**
         * Enable and Disable social icons in header.
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_socialicons',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_socialicons',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_header_item_section',
                    'settings'      => 'articlewave_enable_socialicons',
                    'label'         => __( 'Enable social icons', 'articlewave' ),
                )
            )
        );

        $wp_customize->add_setting( 'articlewave_socialicons',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => json_encode( array( array( 'social_icon' => '', 'social_url'   => '', 'item_visible'   => 'show' ) ) ),
                'sanitize_callback' => 'articlewave_sanitize_repeater'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Repeater(
            $wp_customize, 'articlewave_socialicons',
                array(
                    'priority'      => 15,
                    'section'       => 'articlewave_header_item_section',
                    'settings'      => 'articlewave_socialicons',
                    'label'         => __( 'Social Icons', 'articlewave' ),
                    'description'   => __( 'Add item for required social icon', 'articlewave' ),
                    'articlewave_box_label_text'    => __( 'Social Icons', 'articlewave' ),
                    'articlewave_box_add_control_text'    => __( 'Add Icon', 'articlewave' ),
                    'active_callback'   => 'articlewave_has_enable_socialicons'
                ),
                array(
                    'social_icon' => array(
                        'type'          => 'social_icon',   
                        'label'         => __( 'Social Icon', 'articlewave' ),
                        'description'   => __( 'Choose social media icon.', 'articlewave' )
                    ),
                    'social_url'  => array(
                        'type'          => 'url',
                        'label'         => __( 'Social Link URL', 'articlewave' ),
                        'description'   => __( 'Enter social media url.', 'articlewave' )
                    ),
                    'item_visible'   => array(
                        'type'  => 'hidden'
                    )
                )
            )
        );

         /**
         * Enable and Disable search bar in header.
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_searchbar',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_searchbar',
                array(
                    'priority'      => 20,
                    'section'       => 'articlewave_header_item_section',
                    'settings'      => 'articlewave_enable_searchbar',
                    'label'         => __( 'Enable search bar', 'articlewave' ),
                )
            )
        );

        /**
         * Radio buttonset field for search option
         *
         * Header Settings > Header Items
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_search_option',
            array(
                'default'           => 'live-search',
                'sanitize_callback' => 'articlewave_sanitize_select',
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Buttonset(
            $wp_customize, 'articlewave_search_option',
                array(
                    'priority'          => 25,
                    'section'           => 'articlewave_header_item_section',
                    'settings'          => 'articlewave_search_option',
                    'label'             => __( 'Search Method', 'articlewave' ),
                    'choices'           => articlewave_search_choices(),
                    'active_callback'   => 'articlewave_has_enable_search'
                )
            )
        );

         /**
         * Enable and Disable sticky header sidebar in header.
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_sticky_header_sidebar',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_sticky_header_sidebar',
                array(
                    'priority'      => 30,
                    'section'       => 'articlewave_header_item_section',
                    'settings'      => 'articlewave_enable_sticky_header_sidebar',
                    'label'         => __( 'Enable sticky sidebar toggle', 'articlewave' ),
                )
            )
        );

        /**
         * Upgrade field for header section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_header',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_header',
                array(
                    'priority'      => 35,
                    'section'       => 'articlewave_header_item_section',
                    'settings'      => 'articlewave_upgrade_header',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_header' )
                )
            )
        );
    }

endif;

add_action( 'customize_register', 'articlewave_header_customize_register' );