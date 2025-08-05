<?php
/**
 * Customizer helper where define functions related to customizer panel, sections and settings.
 * 
 * @package Articlewave
 */

if ( ! function_exists( 'articlewave_layout_choices' ) ) :

    /**
     * function to return choices of site layout.
     *
     * @since 1.0.0
     */
    function articlewave_layout_choices() {

        $site_layouts = apply_filters( 'articlewave_layout_choices',
            array(
                'full-width'    => array(
                    'title'     => __( 'Fullwidth', 'articlewave' ),
                    'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/full-width.png'
                ),
                'boxed-layout'  => array(
                    'title'     => __( 'Boxed', 'articlewave' ),
                    'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/boxed-layout.png'
                ),
                'separate'         => array(
                    'title'     => __( 'Separate', 'articlewave' ),
                    'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/seperate.png'
                )
            )
        );

        return $site_layouts;
    }

endif;

if ( ! function_exists( 'articlewave_site_mode_choices' ) ) :

    /**
     * function to return choices for site mode.
     *
     * @since 1.0.0
     */
    function articlewave_site_mode_choices() {

        $site_mode_choices = apply_filters( 'articlewave_site_mode_choices',
            array(
                'light'    => __( 'Light Mode', 'articlewave' ),
                'dark'     => __( 'Dark Mode', 'articlewave' )
            )
        );

        return $site_mode_choices;

    }

endif;

if ( ! function_exists( 'articlewave_preloader_style_choices' ) ) :

    /**
     * function to return choices for preloader styles.
     *
     * @since 1.0.0
     */
    function articlewave_preloader_style_choices() {

        $site_container_layout = apply_filters( 'articlewave_preloader_style_choices',
            array(
                'three_bounce'    => array(
                    'title'     => __( 'Style 1', 'articlewave' ),
                    'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/three-bounce-preloader.gif'
                ),
                'wave'         => array(
                    'title'     => __( 'Style 2', 'articlewave' ),
                    'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/wave-preloader.gif'
                ),
                'folding_cube'         => array(
                    'title'     => __( 'Style 3', 'articlewave' ),
                    'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/folding-cube-preloader.gif'
                )
            )
        );

        return $site_container_layout;

    }

endif;

if ( ! function_exists( 'articlewave_sidebar_layout_choices' ) ) :

        /**
         * function to return choices for archive sidebar layouts.
         *
         * @since 1.0.0
         */
        function articlewave_sidebar_layout_choices() {

            $sidebar_layouts = apply_filters( 'articlewave_sidebar_layout_choices',
                array(
                    'right-sidebar'    => array(
                        'title'     => __( 'Right Sidebar', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/right-sidebar.png'
                    ),
                    'left-sidebar'  => array(
                        'title'     => __( 'Left Sidebar', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/left-sidebar.png'
                    ),
                    'both-sidebar'  => array(
                        'title'     => __( 'Both Sidebar', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/both-sidebar.png'
                    ),
                    'no-sidebar'  => array(
                        'title'     => __( 'No Sidebar', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/no-sidebar.png'
                    ),
                    'no-sidebar-center'  => array(
                        'title'     => __( 'No Sidebar Center', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/no-sidebar-center.png'
                    )
                )
            );

            return $sidebar_layouts;

        }

 endif;


if ( ! function_exists( 'articlewave_category_dropdown' ) ) :

    /**
     * function to return choices for categories.
     *
     * @since 1.0.0
     */

    function articlewave_category_dropdown() {

        $get_cats = get_categories();

        $cat_lists = array(
            '' => __( 'Select Category', 'articlewave' )
        );

        foreach( $get_cats as $cat ) {
            $cat_lists[$cat->slug] = $cat->name;
        }

        return $cat_lists;
    }

endif;    


 if ( ! function_exists( 'articlewave_footer_widget_area_layout_choices' ) ) :

        /**
         * function to return choices of footer widget layout.
         *
         * @since 1.0.0
         */

        function articlewave_footer_widget_area_layout_choices() {

            $posts_layout = apply_filters( 'articlewave_footer_widget_area_layout_choices',
                array(
                    'column-one'  => array(
                        'title'     => __( 'Layout 1', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/footer-1.png'
                    ),
                    'column-two'  => array(
                        'title'     => __( 'Layout 2', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/footer-2.png'
                    ),
                    'column-three'  => array(
                        'title'     => __( 'Layout 3', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/footer-3.png'
                    ),
                    'column-four'  => array(
                        'title'     => __( 'Layout 4', 'articlewave' ),
                        'src'       => get_template_directory_uri() . '/inc/customizer/assets/images/footer-4.png'
                    )
                )
            );

            return $posts_layout;
        }

    endif;


if ( ! function_exists( 'articlewave_posts_date_filter_choices' ) ) :
    /**
     * function to return choices of posts date filter.
     *
     * @since 1.0.0
     */
    function articlewave_posts_date_filter_choices() {

        $posts_date_filter = apply_filters( 'articlewave_posts_date_filter_choices',
            array(
                'all'           => __( 'All', 'articlewave' ),
                'today'         => __( 'Today', 'articlewave' ),
                'this-week'     => __( 'This Week', 'articlewave' ),
                'last-week'     => __( 'Last Week', 'articlewave' ),
                'this-month'    => __( 'This Month', 'articlewave' ),
                'last-month'    => __( 'Last Month', 'articlewave' )
            )
        );

        return $posts_date_filter;

    }

endif;


if ( ! function_exists( 'articlewave_hover_effect_choices' ) ) :
    /**
     * function to return choices of hover effect.
     *
     * @since 1.0.0
     */
    function articlewave_hover_effect_choices() {

        $hover_effect = apply_filters( 'articlewave_hover_effect_choices',
            array(
                'none'  => __('None', 'articlewave'),
                'one'   => __('One', 'articlewave'),
            )
        );

        return $hover_effect;

    }

endif;


if ( ! function_exists( 'articlewave_search_choices' ) ) :
    /**
     * function to return choices of search.
     *
     * @since 1.0.0
     */
    function articlewave_search_choices() {

        $search_option = apply_filters( 'articlewave_search_choices',
            array(
                'default' => __('Default', 'articlewave'),
                'live-search' => __('Live Search', 'articlewave'),
            )
        );

        return $search_option;

    }

endif;


if ( ! function_exists( 'articlewave_front_page_post_display_choices' ) ) :
    /**
     * function to return choices of front page post.
     *
     * @since 1.0.0
     */
    function articlewave_front_page_post_display_choices() {

        $display_option = apply_filters( 'articlewave_front_page_post_display_choices',
            array(
                'grid' => __('Grid', 'articlewave'),
                'classic' => __('Classic', 'articlewave'),
                'list' => __('List', 'articlewave')
            )
        );

        return $display_option;

    }

endif;


if ( ! function_exists( 'articlewave_archive_post_display_choices' ) ) :
    /**
     * function to return choices of archive post display.
     *
     * @since 1.0.0
     */
    function articlewave_archive_post_display_choices() {

        $display_option = apply_filters( 'articlewave_archive_post_display_choices',
            array(
                'grid' => __('Grid', 'articlewave'),
                'classic' => __('Classic', 'articlewave'),
                'list' => __('List', 'articlewave')
            )
        );

        return $display_option;

    }

endif;

    
if ( ! function_exists( 'articlewave_upgrade_choices' ) ) :

    /**
     * function to return choices for upgrade to pro.
     *
     * @since 1.0.0
     */
    function articlewave_upgrade_choices( $setting_id ) {

        $upgrade_info_lists = array(
            'preloader'     => array( __( '10 Styles', 'articlewave' ) ),
            'typography'    => array( __( '600+ Google Fonts', 'articlewave' ), __( 'Adjustable font size', 'articlewave' ), __( 'Font Color Option', 'articlewave' ) ),
            'breadcrumb'    => array(  __( '4 Breadcrumbs Option' , 'articlewave' ) ),
            'scroll_top'    => array( __( '10 Arrow Icons', 'articlewave' ), __( 'Alignment Options', 'articlewave' ), __( 'Device visibility', 'articlewave' ), __( 'Color Option', 'articlewave' ) ),
            'posts'         => array( __( 'Author Icons on Posts', 'articlewave' ), __( '3+ Image Hover Effects', 'articlewave' ), __( '1 More Post Date Format', 'articlewave' ) ),
            'header'        => array( __( '2 More Header Layouts', 'articlewave' ), __( '2 Search Layouts ', 'articlewave' ), __( '2 Social Icons Layouts ', 'articlewave' ), __( 'Color Option on Social Icons', 'articlewave' ), __( '6+ Social Icons', 'articlewave' ) ),   
            'main_banner'    => array( __( '2 More Layouts', 'articlewave' ), __( 'Multiple Categories Option', 'articlewave' ), __( 'Banner Background Design', 'articlewave' ) ),
            'front_page'    => array( __( '4 More Layouts', 'articlewave' ), __( 'Pagination Option', 'articlewave' ) ),
            'archive'       => array( __( '4 More Layouts', 'articlewave' ), __( 'Pagination Option', 'articlewave' ), __( 'Separate Styling for Author Page and Search Page', 'articlewave' ) ),
            'single_post'   => array( __( '2 More Layouts', 'articlewave' ), __( '2 Layouts for Author box', 'articlewave' ) ),
            'error_page'    => array( __( '3 More Page Layouts', 'articlewave' ), __( 'Blank Page Option', 'articlewave' ),  __( 'Button Color', 'articlewave' ) ),
            'footer'        => array( __( 'Custom Text Color and Background Color', 'articlewave' ) ),
        );

        $setting_id = explode( 'articlewave_', $setting_id );
        $setting_id = $setting_id[1];

        return $upgrade_info_lists[$setting_id];

    }

endif;