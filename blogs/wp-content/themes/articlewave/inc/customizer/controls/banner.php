<?php
/**
 * Front Page Setting panel.
 * 
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Front Page Setting
 * 
 * @param WP_Customize_Manager $wp_customize Object that holds the customizer data.
 * @since 1.0.0
 */

if ( ! function_exists( 'articlewave_banner_customize_register' ) ) :

     function articlewave_banner_customize_register( $wp_customize ) {

     	$wp_customize->add_panel( 'articlewave_front_page_setting',
            array(
                'priority'  => 3,
                'title'     => __( 'Front Page Settings', 'articlewave' )
            )
        );

        //----------------------------------------- Front Page Layout ---------------------------------

         /**
         * Front Page Post choices 
         *
         * General Settings > Front Page Setting > Front Post Setting
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( 'articlewave_front_post_setting',
            array(
                'priority'  => 20,
                'panel'     => 'articlewave_front_page_setting',
                'title'     => __( 'Front Post Settings', 'articlewave' )
            )
        );

        $wp_customize->add_setting( 'articlewave_front_post_display',
            array(
                'default'   => 'grid',
                'sanitize_callback' => 'articlewave_sanitize_select'
            )
        );

        $wp_customize->add_control( 'articlewave_front_post_display',
            array(
                'priority'  => 10,
                'section'   => 'articlewave_front_post_setting',
                'setting'   => 'articlewave_front_post_display',
                'label'     => __( 'Front Page  Post Style', 'articlewave' ),
                'type'      => 'select',
                'choices'   => articlewave_front_page_post_display_choices(), 
            )
        );

        /**
         * Enable the mansory for grid layout
         * 
        */
        $wp_customize->add_setting( 'articlewave_masonry_for_front_grid_option',
            array(
                'capability'        => 'edit_theme_options',
                'default'           => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_masonry_for_front_grid_option',
                array(
                    'priority'      => 20,
                    'label'         => __( 'Enable Masonry For Grid', 'articlewave' ),
                    'section'       => 'articlewave_front_post_setting',
                    'settings'      => 'articlewave_masonry_for_front_grid_option',
                    'active_callback' => 'articlewave_front_display_has_grid'
                    
                )
            )
        );

        /**
         * Upgrade field for front page section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_front_page',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_front_page',
                array(
                    'priority'      => 30,
                    'section'       => 'articlewave_front_post_setting',
                    'settings'      => 'articlewave_upgrade_front_page',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_front_page' )
                )
            )
        );

        // ---------------------------------Slider Setting-------------------------------------

         /**
         * Banner  choices for front page 
         *
         * General Settings > Banner Setting
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( 'articlewave_banner_setting',
            array(
                'priority'  => 10,
                'panel'     => 'articlewave_front_page_setting',
                'title'     => __( 'Banner Settings', 'articlewave' )
            )
        );

        /**
         * Heading field for banner block settings
         *
         * Frontpage Settings > Banner Setting
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_slider_heading',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Heading(
            $wp_customize, 'articlewave_slider_heading',
                array(
                    'priority'          => 5,
                    'section'           => 'articlewave_banner_setting',
                    'settings'          => 'articlewave_slider_heading',
                    'label'             => __( 'Slider Settings', 'articlewave' ),
                )
            )
        );


        $wp_customize->add_setting( 'articlewave_enable_slider',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

         /**
         * Enable or Disable the slider
         *
         * @since 1.0.0
         */
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_slider',
            array(
                'priority'      => 10,
                'section'       => 'articlewave_banner_setting',
                'settings'      => 'articlewave_enable_slider',
                'label'         => __( 'Enable Silder', 'articlewave' ),
            )
        ));

    	$wp_customize->add_setting( 'articlewave_slider_category_dropdown', 
            array(
            	'default'	=> '',
                'sanitize_callback' => 'articlewave_sanitize_select' 
            )
        );
          
        $wp_customize->add_control( 'articlewave_slider_category_dropdown', 
            array(
                'priority'      => 20,
                'label' => esc_html__( 'Choose a Category', 'articlewave' ),
                'section' => 'articlewave_banner_setting',
                'settings' => 'articlewave_slider_category_dropdown',
                'type' => 'select',
                'choices' => articlewave_category_dropdown(),
                'active_callback'   => 'articlewave_has_enable_slider'       
            )
        );    
    

        // ------------------------------Tabbed Setting-------------------------------------

        /**
         * Heading field for banner block settings
         *
         * Frontpage Settings > Banner Setting
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_tabbed_heading',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Heading(
            $wp_customize, 'articlewave_tabbed_heading',
                array(
                    'priority'          => 25,
                    'section'           => 'articlewave_banner_setting',
                    'settings'          => 'articlewave_tabbed_heading',
                    'label'             => __( 'Tabbed Settings', 'articlewave' ),
                )
            )
        );


        /**
         * Enable or Disable the popular tab field
         *
         * @since 1.0.0
         */

    	$wp_customize->add_setting( 'articlewave_enable_tabbed_title',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_tabbed_title',
            array(
                'priority'      => 30,
                'section'       => 'articlewave_banner_setting',
                'settings'      => 'articlewave_enable_tabbed_title',
                'label'         => __( 'Enable Popular Post Display', 'articlewave' ),
                'description'   => __( 'It will display the post based on the most comment post', 'articlewave' ),
            )
        ));

    	 $wp_customize->add_setting( 'articlewave_tabbed_title_field',
            array(
                'default'   => 'Popular Posts',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( 'articlewave_tabbed_title_field',
            array(
                'priority'  => 40,
                'section'   => 'articlewave_banner_setting',
                'setting'   => 'articlewave_tabbed_title_field',
                'label'     => __( 'Popular Title Input Field', 'articlewave' ),
                'type'      => 'text',
                'input_attrs' => array(
                    'placeholder'   => __( 'Popular Post', 'articlewave' )
                ),
                'active_callback'   => 'articlewave_has_enable_tabbed_title_field'
            )
        );

        /**
         * Enable or Disable the recent tab field
         *
         * @since 1.0.0
         */


         $wp_customize->add_setting( 'articlewave_enable_tabbed_recent',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_tabbed_recent',
                array(
                    'priority'      => 50,
                    'section'       => 'articlewave_banner_setting',
                    'settings'      => 'articlewave_enable_tabbed_recent',
                    'label'         => __( 'Enable Recent Post Display', 'articlewave' ),
                    'description'   => __( 'It will display the post based on the recent post', 'articlewave' ),
                )
            )
        );

        $wp_customize->add_setting( 'articlewave_tabbed_recent_field',
            array(
                'default'   => 'Recent Posts',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
  
        $wp_customize->add_control( 'articlewave_tabbed_recent_field',
            array(
                'priority'  => 60,
                'section'   => 'articlewave_banner_setting',
                'setting'   => 'articlewave_tabbed_recent_field',
                'label'     => __( 'Recent Title Input Field', 'articlewave' ),
                'type'      => 'text',
                'input_attrs' => array(
                    'placeholder'   => __( 'Recent Posts', 'articlewave' )
                ),
                'active_callback'   => 'articlewave_has_enable_tabbed_recent_field'
            )
        );

        /**
         * Upgrade field for main banner section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_banner',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_banner',
                array(
                    'priority'      => 70,
                    'section'       => 'articlewave_banner_setting',
                    'settings'      => 'articlewave_upgrade_banner',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_main_banner' )
                )
            )
        );
    }

endif;

add_action( 'customize_register', 'articlewave_banner_customize_register' );