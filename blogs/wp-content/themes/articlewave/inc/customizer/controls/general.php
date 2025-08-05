<?php
/**
 * General Settings panel.
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register General Setting
 *
 * @param WP_Customize_Manager $wp_customize Object that holds the customizer data.
 * @since 1.0.0
 */
if ( ! function_exists( 'articlewave_general_customize_register' ) ) :

    function articlewave_general_customize_register( $wp_customize ) {

        $wp_customize->add_panel( 'articlewave_general_setting',
            array(
                'priority'  => 2,
                'title'     => __( 'General Settings', 'articlewave' )
            )
        );

         /**
         * Site Layout choices and site mode switcher for site
         *
         * General Settings > Site Layout
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( 'articlewave_section_site_layout',
            array(
                'priority' => 1,
                'panel'     => 'articlewave_general_setting',
                'title'     => __( 'Site Layout', 'articlewave' )
            )
        );

        $wp_customize->add_setting( 'articlewave_site_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'full-width',
                'sanitize_callback' => 'sanitize_key'

            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Radio_Image(
            $wp_customize, 'articlewave_site_layout',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_section_site_layout',
                    'settings'      => 'articlewave_site_layout',
                    'label'         => __( 'Site Layout', 'articlewave' ),
                    'description'   => __( 'Choose from available layouts', 'articlewave' ),
                    'choices'       => articlewave_layout_choices()
                )
            )
        );

        /**
         * Enable the mode switcher for site
         * 
        */
        $wp_customize->add_setting( 'articlewave_site_mode_switcher_option',
            array(
                'capability'        => 'edit_theme_options',
                'default'           => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_site_mode_switcher_option',
                array(
                    'label'         => __( 'Enable Site Mode Switcher', 'articlewave' ),
                    'section'       => 'articlewave_section_site_layout',
                    'settings'      => 'articlewave_site_mode_switcher_option',
                    'priority'      => 60,
                )
            )
        );

         /**
         * Radio buttonset field for sitemode
         *
         * General Settings > Site Layout
         *
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'articlewave_site_mode_option',
            array(
                'default'           => 'light',
                'sanitize_callback' => 'articlewave_sanitize_select',
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Buttonset(
            $wp_customize, 'articlewave_site_mode_option',
                array(
                    'priority'          => 65,
                    'section'           => 'articlewave_section_site_layout',
                    'settings'          => 'articlewave_site_mode_option',
                    'label'             => __( 'Site Mode Option', 'articlewave' ),
                    'choices'           => articlewave_site_mode_choices(),
                    'active_callback'   => 'articlewave_has_site_mode_switcher_enable_active_callback'
                )
            )
        );

        //-----------------------------------preloader---------------------------------------------

         /**
         * Pre Loader choices for site
         *
         * General Settings > Pre Loader
         *
         * @since 1.0.0
         */


        $wp_customize->add_setting( 'articlewave_enable_preloader',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_preloader',
            array(
                'priority'      => 1,
                'section'       => 'articlewave_section_preloader',
                'settings'      => 'articlewave_enable_preloader',
                'label'         => __( 'Enable Pre Loader', 'articlewave' ),
            )
        ));

        $wp_customize->add_section( 'articlewave_section_preloader',
            array(
                'priority' => 2,
                'panel'     => 'articlewave_general_setting',
                'title'     => __( 'Pre Loader', 'articlewave' )
            )
        );

        $wp_customize->add_setting( 'articlewave_preloader',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'three_bounce',
                'sanitize_callback' => 'sanitize_key'

            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Radio_Image(
            $wp_customize, 'articlewave_preloader',
                array(
                    'priority'      => 2,
                    'section'       => 'articlewave_section_preloader',
                    'settings'      => 'articlewave_preloader',
                    'label'         => __( 'Pre Loader', 'articlewave' ),
                    'description'   => __( 'Choose from available layouts', 'articlewave' ),
                    'choices'       => articlewave_preloader_style_choices(),
                    'active_callback'   => 'articlewave_has_enable_preloader'
                )
            )
        );

        /**
         * Upgrade field for preloader section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_preloader',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_preloader',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_section_preloader',
                    'settings'      => 'articlewave_upgrade_preloader',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_preloader' )
                )
            )
        );

        //----------------------------Colour---------------------------------------

         /**
         * Colors choices for site
         *
         * General Settings > Colors
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( new ArticleWave_Customize_Section (
            $wp_customize, 'articlewave_section_theme_color',
                array(
                    'priority'  => 3,
                    'panel'   => 'articlewave_general_setting',
                    'title'     => __( 'Colors', 'articlewave' ),
                )
            )
        );

         /**
         * Add Base Colors Section
         *
         * General Settings > Colors > Base Colors
         *
         * @since 1.0.0
         */
        $wp_customize->add_section( new ArticleWave_Customize_Section (
            $wp_customize, 'articlewave_section_colors_base',
                array(
                    'priority'  => 10,
                    'section'   => 'articlewave_section_theme_color',
                    'panel'     => 'articlewave_general_setting',
                    'title'     => __( 'Base Colors', 'articlewave' ),
                )
            )
        );

        $wp_customize->add_setting( 'articlewave_primary_theme_color',
            array(
                'default'           => '#212121',
                'sanitize_callback' => 'sanitize_hex_color',
            )
        );
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 'articlewave_primary_theme_color',
                array(
                    'label'      => __( 'Primary Color', 'articlewave' ),
                    'section'    => 'articlewave_section_colors_base',
                    'settings'   => 'articlewave_primary_theme_color',
                    'priority'   => 5
                )
            )
        );

         $wp_customize->add_setting( 'articlewave_text_color',
            array(
                'default'           => '#212121',
                'sanitize_callback' => 'sanitize_hex_color',
            )
        );
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 'articlewave_text_color',
                array(
                    'label'      => __( 'Text Color', 'articlewave' ),
                    'section'    => 'articlewave_section_colors_base',
                    'settings'   => 'articlewave_text_color',
                    'priority'   => 10
                )
            )
        );


        $wp_customize->add_setting( 'articlewave_link_color',
            array(
                'default'           => '#3b2d1b',
                'sanitize_callback' => 'sanitize_hex_color',
            )
        );
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 'articlewave_link_color',
                array(
                     'priority'  => 15,
                    'label'      => __( 'Link Color', 'articlewave' ),
                    'section'    => 'articlewave_section_colors_base',
                    'settings'   => 'articlewave_link_color',
                )
            )
        );

         $wp_customize->add_setting( 'articlewave_link_hover_color',
            array(
                'default'           => '#960505',
                'sanitize_callback' => 'sanitize_hex_color',
            )
        );
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 'articlewave_link_hover_color',
                array(
                    'label'      => __( 'Link Hover Color', 'articlewave' ),
                    'section'    => 'articlewave_section_colors_base',
                    'settings'   => 'articlewave_link_hover_color',
                    'priority'   => 20
                )
            )
        );

        /**
         * Add Base Colors Section
         *
         * General Settings > Colors > Categories Colors
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( new ArticleWave_Customize_Section (
            $wp_customize, 'articlewave_section_colors_categories',
                array(
                    'priority'  => 20,
                    'section'   => 'articlewave_section_theme_color',
                    'panel'     => 'articlewave_general_setting',
                    'title'     => __( 'Categories Colors', 'articlewave' ),
                )
            )
        );

        /**
         * Color Picker field for Categories Color
         *
         * General Settings > Colors > Categories Colors
         *
         * @since 1.0.0
         */
        $priority = 5;
        $categories = get_categories( array( 'hide_empty' => 1 ) );

        foreach ( $categories as $category_list ) {
            $wp_customize->add_setting( 'category_color_'.esc_attr( $category_list->term_id ),
                array(
                    'default'           => '#3b2d1b',
                    'sanitize_callback' => 'sanitize_hex_color',
                )
            );
            $wp_customize->add_control( new WP_Customize_Color_Control(
                $wp_customize, 'category_color_'.esc_attr( $category_list->term_id ),
                    array(
                        'label'      => sprintf( __( '%1$s Color', 'articlewave' ), esc_html( $category_list->name ) ),
                        'section'    => 'articlewave_section_colors_categories',
                        'settings'   => 'category_color_'.esc_attr( $category_list->term_id ),
                        'priority'   => absint( $priority )
                    )
                )
            );
            $priority += 5;
        }

        // ------------------------------Side Bar Layout----------------------------------------------

        /**
         * Sidebar Layout choices for site
         *
         * General Settings > Siderbar Layout
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( 'articlewave_sidebar_layout',
            array(
                'priority' => 4,
                'panel'     => 'articlewave_general_setting',
                'title'     => __( 'Sidebar Layout', 'articlewave' )
            )
         );

        $wp_customize->add_setting( 'articlewave_archive_and_blog_sidebar_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'right-sidebar',
                'sanitize_callback' => 'sanitize_key'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Radio_Image(
            $wp_customize, 'articlewave_archive_and_blog_sidebar_layout',
                array(
                    'priority'      => 1,
                    'section'       => 'articlewave_sidebar_layout',
                    'settings'      => 'articlewave_archive_and_blog_sidebar_layout',
                    'label'         => __( 'Archive and Blog', 'articlewave' ),
                    'description'   => __( 'Choose from available layouts', 'articlewave' ),
                    'choices'       => articlewave_sidebar_layout_choices()
                )
            )
        );

        $wp_customize->add_setting( 'articlewave_post_sidebar_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'right-sidebar',
                'sanitize_callback' => 'sanitize_key'

            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Radio_Image(
            $wp_customize, 'articlewave_post_sidebar_layout',
                array(
                    'priority'      => 2,
                    'section'       => 'articlewave_sidebar_layout',
                    'settings'      => 'articlewave_post_sidebar_layout',
                    'label'         => __( 'Post Sidebar Layout', 'articlewave' ),
                    'description'   => __( 'Choose from available layouts', 'articlewave' ),
                    'choices'       => articlewave_sidebar_layout_choices()
                )
            )
        );

        $wp_customize->add_setting( 'articlewave_page_sidebar_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'right-sidebar',
                'sanitize_callback' => 'sanitize_key'

            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Radio_Image(
            $wp_customize, 'articlewave_page_sidebar_layout',
                array(
                    'priority'      => 3,
                    'section'       => 'articlewave_sidebar_layout',
                    'settings'      => 'articlewave_page_sidebar_layout',
                    'label'         => __( 'Page Sidebar Layout', 'articlewave' ),
                    'description'   => __( 'Choose from available layouts', 'articlewave' ),
                    'choices'       => articlewave_sidebar_layout_choices()
                )
            )
        );

        // ------------------------------Breadcrumb----------------------------------------------

        /**
         * Breadcrumb for site
         *
         * General Settings > Breadcrumb
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( 'articlewave_breadcrumb',
            array(
                'priority' => 5,
                'panel'     => 'articlewave_general_setting',
                'title'     => __( 'Breadcrumb', 'articlewave' )
            )
         );

       $wp_customize->add_setting( 'articlewave_enable_breadcrumb',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_breadcrumb',
                array(
                    'priority'      => 1,
                    'section'       => 'articlewave_breadcrumb',
                    'settings'      => 'articlewave_enable_breadcrumb',
                    'label'         => __( 'Enable Breadcrumbs', 'articlewave' ),
                )
            )
        );

        /**
         * Upgrade field for breadcrumb section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_breadcrumb',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_breadcrumb',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_breadcrumb',
                    'settings'      => 'articlewave_upgrade_breadcrumb',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_breadcrumb' )
                )
            )
        );


        // -------------------------------Typography---------------------------------------------

         /**
         * Typography choices for site
         *
         * General Settings > Typography
         *
         * @since 1.0.0
         */

        $wp_customize->add_section( new ArticleWave_Customize_Section (
            $wp_customize, 'articlewave_section_typography',
                array(
                    'priority'  => 6,
                    'panel'     => 'articlewave_general_setting',
                    'title'     => __( 'Typography', 'articlewave' ),
                )
            )
        );

        $wp_customize->add_section( new ArticleWave_Customize_Section(
            $wp_customize, 'articlewave_section_typography_body',
                array(
                    'priority'      => 10,
                    'panel'         => 'articlewave_general_setting',
                    'section'       => 'articlewave_section_typography',
                    'title'         => __( 'Body', 'articlewave' )
                )
            )
        );

        $wp_customize->add_section( new ArticleWave_Customize_Section(
            $wp_customize, 'articlewave_section_typography_heading',
                array(
                    'priority'      => 20,
                    'panel'         => 'articlewave_general_setting',
                    'section'       => 'articlewave_section_typography',
                    'title'         => __( 'Heading', 'articlewave' )
                )
            )
        );

        /**
         * Upgrade field for typography section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_typography',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_typography',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_section_typography',
                    'settings'      => 'articlewave_upgrade_typography',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_typography' )
                )
            )
        );

        //------------------------------Scroll top----------------------------------------------

         /**
         * Typography choices for site
         *
         * General Settings > Scroll Top
         *
         * @since 1.0.0
         */

         $wp_customize->add_section( 'articlewave_section_scrolltop',
            array(
                'priority' => 7,
                'panel'     => 'articlewave_general_setting',
                'title'     => __( 'Scroll Top', 'articlewave' )
            )
         );

        $wp_customize->add_setting( 'articlewave_enable_scrolltop',
            array(
                'default'   => true,
                'sanitize_callback' => 'articlewave_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new ArticleWave_Control_Toggle(
            $wp_customize, 'articlewave_enable_scrolltop',
                array(
                    'priority'      => 1,
                    'section'       => 'articlewave_section_scrolltop',
                    'settings'      => 'articlewave_enable_scrolltop',
                    'label'         => __( 'Enable Scroll Top', 'articlewave' ),
                )
            )
        );

        /**
         * Upgrade field for typography section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_scroll_up',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_scroll_up',
                array(
                    'priority'      => 10,
                    'section'       => 'articlewave_section_scrolltop',
                    'settings'      => 'articlewave_upgrade_scroll_up',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_scroll_top' )
                )
            )
        );

        //------------------------------ Hover ----------------------------------------------

         /**
         * Typography choices for site
         *
         * General Settings > Hover
         *
         * @since 1.0.0
         */

         $wp_customize->add_section( 'articlewave_section_post',
            array(
                'priority' => 8,
                'panel'     => 'articlewave_general_setting',
                'title'     => __( 'Posts', 'articlewave' )
            )
         );

        $wp_customize->add_setting( 'articlewave_image_hover_effect',
            array(
                'default'   => 'one',
                'sanitize_callback' => 'articlewave_sanitize_select'
            )
        );

        $wp_customize->add_control( 'articlewave_image_hover_effect',
            array(
                'priority'  => 10,
                'section'   => 'articlewave_section_post',
                'setting'   => 'articlewave_image_hover_effect',
                'label'     => __( 'Image Hover Effect', 'articlewave' ),
                'type'      => 'select',
                'choices'   => articlewave_hover_effect_choices(),
            )
        );

        /**
         * Upgrade field for post section
         *
         * @since 1.0.0
         */ 
        $wp_customize->add_setting( 'articlewave_upgrade_post',
            array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        $wp_customize->add_control( new Articlewave_Control_Upgrade(
            $wp_customize, 'articlewave_upgrade_post',
                array(
                    'priority'      => 15,
                    'section'       => 'articlewave_section_post',
                    'settings'      => 'articlewave_upgrade_post',
                    'label'         => __( 'More features with Articlewave Pro', 'articlewave' ),
                    'choices'       => articlewave_upgrade_choices( 'articlewave_posts' )
                )
            )
        );


        //------------------------------BackGround Image----------------------------------------------

        $wp_customize->get_section( 'background_image' )->panel = 'articlewave_general_setting';
        $wp_customize->get_section( 'background_image' )->priority = 9;

        $wp_customize->get_control( 'background_color' )->section    = 'background_image';
        $wp_customize->get_control( 'background_color' )->priority    = 5;
    }

endif;

add_action( 'customize_register', 'articlewave_general_customize_register' );