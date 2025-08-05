<?php
/**
 * Inner page Setting panel.
 * 
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Inner page Setting
 * 
 * @param WP_Customize_Manager $wp_customize Object that holds the customizer data.
 * @since 1.0.0
 */

if ( ! function_exists( 'articlewave_innerpage_customize_register' ) ) :

    function articlewave_innerpage_customize_register( $wp_customize ) {

     	$wp_customize->add_panel( 'articlewave_innerpage_setting',
            array(
                'priority'  => 4,
                'title'     => __( 'Inner Page Settings', 'articlewave' )
            )
        );

        // ----------------------------Archive Setting------------------------------------

        /**
         * Archive Page Setting
         *
         * General Settings > Inner Page Setting > Archive Setting
         *
         * @since 1.0.0
         */

       $wp_customize->add_section('articlewave_archive_setting',
	    	array(
	    		'priority'  => 1,
	    		'panel'     => 'articlewave_innerpage_setting', 
	    		'title'     => __('Archive Settings', 'articlewave')
	    	)
		);


        /**
         * Enable and Disable page title prefix
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_pagetitle_prefix',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_pagetitle_prefix',
                array(
                    'priority'      => 1,
                    'section'       => 'articlewave_archive_setting',
                    'settings'      => 'articlewave_enable_pagetitle_prefix',
                    'label'         => __( 'Enable Archive Page Title Prefix', 'articlewave' ),
                )
            )
        );

        /**
         * Choices for the archive post display
         *
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_archive_post_display',
            array(
                'default'   => 'grid',
                'sanitize_callback' => 'articlewave_sanitize_select'
            )
        ); 

        $wp_customize->add_control( 'articlewave_archive_post_display',
            array(
                'priority'  => 2,
                'section'   => 'articlewave_archive_setting',
                'setting'   => 'articlewave_archive_post_display',
                'label'     => __( 'Archive Page Style', 'articlewave' ),
                'type'      => 'select',
                'choices'   => articlewave_archive_post_display_choices(), 
            )
        );

        /**
         * Enable the mansory for grid layout
         * 
        */
        $wp_customize->add_setting( 'articlewave_masonry_for_archive_grid_option',
            array(
                'capability'        => 'edit_theme_options',
                'default'           => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_masonry_for_archive_grid_option',
                array(
                    'priority'      => 20,
                    'label'         => __( 'Enable Masonry For Grid', 'articlewave' ),
                    'section'       => 'articlewave_archive_setting',
                    'settings'      => 'articlewave_masonry_for_archive_grid_option',
                    'active_callback' => 'articlewave_archive_display_has_grid'
                    
                )
            )
        );


        /**
         * Enable or disable readmore on post
         *
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_enable_readmore',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_readmore',
                array(
                    'priority'      => 30,
                    'section'       => 'articlewave_archive_setting',
                    'settings'      => 'articlewave_enable_readmore',
                    'label'         => __( 'Enable Read More', 'articlewave' ),
                )
            )
        );

        /**
         * Enable or disable estimated reading time on post
         *
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_enable_est_time_for_reading',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_est_time_for_reading',
                array(
                    'priority'      => 40,
                    'section'       => 'articlewave_archive_setting',
                    'settings'      => 'articlewave_enable_est_time_for_reading',
                    'label'         => __( 'Enable Post Reading Time', 'articlewave' ),
                )
            )
        );

        /**
         * Upgrade field for archive section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_archive_page',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_archive_page',
                array(
                    'priority'      => 50,
                    'section'       => 'articlewave_archive_setting',
                    'settings'      => 'articlewave_upgrade_archive_page',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_archive' )
                )
            )
        );

        // ----------------------------Post Setting------------------------------------

        /**
         * Single Post Setting
         *
         * General Settings > Inner Page Setting > Archive Setting
         *
         * @since 1.0.0
         */

        $wp_customize->add_section('articlewave_post_setting',
	    	array(
	    		'priority'  => 2,
	    		'panel'     => 'articlewave_innerpage_setting', 
	    		'title'     => __('Post Settings', 'articlewave')
	    	)
		);

        /**
         * Enable or disable author box
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_authorbox',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_authorbox',
                array(
                    'priority'      => 1,
                    'section'       => 'articlewave_post_setting',
                    'settings'      => 'articlewave_enable_authorbox',
                    'label'         => __( 'Enable Author Box', 'articlewave' ),
                )
            )
        );

         /**
         * Enable or disable related post
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_relatedpost',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_relatedpost',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_post_setting',
                    'settings'      => 'articlewave_enable_relatedpost',
                    'label'         => __( 'Enable Related Posts', 'articlewave' ),
                )
            )
        );

        /**
         * Upgrade field for single post section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_single_post',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_single_post',
                array(
                    'priority'      => 20,
                    'section'       => 'articlewave_post_setting',
                    'settings'      => 'articlewave_upgrade_single_post',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_single_post' )
                )
            )
        );


        // ----------------------------404 Setting------------------------------------

        /**
         * 404 Page Setting
         *
         * General Settings > Inner Page Setting > 404 Page Setting
         *
         * @since 1.0.0
         */

       $wp_customize->add_section('articlewave_404_setting',
            array(
                'priority'  => 20,
                'panel'     => 'articlewave_innerpage_setting', 
                'title'     => __('404 Page Settings', 'articlewave')
            )
        );

        /**
         * Enable or disable back to search
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_404_search',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_404_search',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_404_setting',
                    'settings'      => 'articlewave_enable_404_search',
                    'label'         => __( 'Enable Search Box', 'articlewave' ),
                )
            )
        );

        /**
         * Enable or disable back to home
         *
         *
         * @since 1.0.0
         */

        $wp_customize->add_setting( 'articlewave_enable_404_home_button',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );
      
        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_404_home_button',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_404_setting',
                    'settings'      => 'articlewave_enable_404_home_button',
                    'label'         => __( 'Enable Homepage button', 'articlewave' ),
                )
            )
        );

         /**
         * Upgrade field for error page section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_error_page',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_error_page',
                array(
                    'priority'      => 20,
                    'section'       => 'articlewave_404_setting',
                    'settings'      => 'articlewave_upgrade_error_page',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_error_page' )
                )
            )
        );


    }

endif;

add_action( 'customize_register', 'articlewave_innerpage_customize_register' );