<?php
/**
 * Describe child theme functions
 *
 * @package Articlewave
 * @subpackage Articlewave Blog
 * @since 1.0.0
 */

/*------------------------- Theme Version -------------------------------------*/

	if ( ! defined( 'ARTICLEWAVE_BLOG_VERSION' ) ) {
		// Replace the version number of the theme on each release.
		$articlewave_blog_theme_info = wp_get_theme();
		define( 'ARTICLEWAVE_BLOG_VERSION', $articlewave_blog_theme_info->get( 'Version' ) );
	}

/*------------------------- Google Fonts --------------------------------------*/

	if ( ! function_exists( 'articlewave_blog_fonts_url' ) ) :
	    
	    /**
	     * Register Google fonts
	     *
	     * @return string Google fonts URL for the theme.
	     */
	    function articlewave_blog_fonts_url() {

	        $fonts_url = '';
	        $font_families = array();

	        /*
	         * Translators: If there are characters in your language that are not supported
	         * by Arvo, translate this to 'off'. Do not translate into your own language.
	         */
	        if ( 'off' !== _x( 'on', 'Arvo font: on or off', 'articlewave-blog' ) ) {
	            $font_families[] = 'Arvo:700,900';
	        }
	        
	         /*
	         * Translators: If there are characters in your language that are not supported
	         * by Mukta, translate this to 'off'. Do not translate into your own language.
	         */
	        if ( 'off' !== _x( 'on', 'Mukta font: on or off', 'articlewave-blog' ) ) {
	            $font_families[] = 'Mukta:400,600,700';
	        }

	        if( $font_families ) {
	            $query_args = array(
	                'family' => urlencode( implode( '|', $font_families ) ),
	                'subset' => urlencode( 'latin,latin-ext' ),
	            );

	            $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	        }

	        return $fonts_url;
	    }

	endif;

/*------------------------- Enqueue scripts -----------------------------------*/

	add_action( 'wp_enqueue_scripts', 'articlewave_blog_scripts', 99 );

	if ( ! function_exists( 'articlewave_blog_scripts' ) ) :

		/**
		 * function to load style and scripts for theme.
		 * 
		 */
		function articlewave_blog_scripts() {
	    
		    wp_enqueue_style( 'articlewave-blog-google-font', articlewave_blog_fonts_url(), array(), null );
		    
		    wp_dequeue_style( 'articlewave-style' );
		    wp_dequeue_style( 'articlewave-responsive-style' );
		    
		    wp_enqueue_style( 'articlewave-blog-parent-style', get_template_directory_uri() . '/style.css', array(), ARTICLEWAVE_VERSION );
		    
		    wp_enqueue_style( 'articlewave-blog-parent-responsive', get_template_directory_uri() . '/assets/css/articlewave-responsive.css', array(), ARTICLEWAVE_VERSION );
		    
		    wp_enqueue_style( 'articlewave-blog-style', get_stylesheet_uri(), array(), ARTICLEWAVE_BLOG_VERSION );
		}

	endif;
	
/*------------------------- Customizer Section --------------------------------*/
	
	if ( ! function_exists( 'articlewave_blog_customize_register' ) ) :

		/**
		 * Managed all the customizer field for theme.
		 */
		function articlewave_blog_customize_register( $wp_customize ) {
		    global $wp_customize;

		    $wp_customize->get_setting( 'articlewave_primary_theme_color' )->default = '#2E7D32';

	        /**
	         * Color Field for footer background color
	         *
	         * @since 1.0.0
	         */
	        $wp_customize->add_setting( 'articlewave_blog_footer_area_bg_color',
	            array(
	                'default'           => '#000',
	                'sanitize_callback' => 'sanitize_hex_color',
	            )
	        );

	        $wp_customize->add_control( new WP_Customize_Color_Control(
	            $wp_customize, 'articlewave_blog_footer_area_bg_color',
	                array(
	                    'priority'   => 5,
	                    'label'      => __( 'Footer Background Color', 'articlewave-blog' ),    
	                    'section'    => 'articlewave_widget_area',
	                    'settings'   => 'articlewave_blog_footer_area_bg_color', 
	                )
	            )
	        );


		}

	endif;

	add_action( 'customize_register', 'articlewave_blog_customize_register', 20 );


/*------------------------------ Dynamic Styles ----------------------------------*/

	/**
	 * function to handle the footer area color css.
	 *
	 * @since 1.0.0
	 */

	if ( ! function_exists( 'articlewave_blog_general_css' ) ) :

	    /**
	     * function to handle the footer area color css for all sections.
	     *
	     * @since 1.0.0
	     */
	    function articlewave_blog_general_css( $output_css ) {

	        $custom_css = '';

	        $footer_area_bg_color = get_theme_mod( 'articlewave_blog_footer_area_bg_color' , '#000' );

	        // Footer Area Color
        	$custom_css .= " .site-footer { background-color: ". esc_attr(  $footer_area_bg_color ) ."}\n";

	        if ( ! empty( $custom_css ) ) {
	            $output_css .= $custom_css;
	        }

	        return $output_css;

	    }

	endif;

	add_filter( 'articlewave_head_css', 'articlewave_blog_general_css' );


/*------------------------------ Hover Effect on Image ----------------------------------*/

	if ( ! function_exists( 'articlewave_hover_effect_choices' ) ) :
	    /**
	     * function to return choices of hover effect.
	     *
	     * @since 1.0.0
	     */
	    function articlewave_hover_effect_choices() {

	        $hover_effect = apply_filters( 'articlewave_hover_effect_choices',
	            array(
	                'none'  => __( 'None', 'articlewave-blog' ),
	                'one'   => __( 'One', 'articlewave-blog' ),
	                'two'   => __( 'Two', 'articlewave-blog' )
	            )
	        );

	        return $hover_effect;

	    }

	endif;

	if (!function_exists('articlewave_image_hover_effect')) :

	    /**
	     * Hover Effect for image
	     */
	    function articlewave_image_hover_effect()
	    {
	        $valid_effects = array( 'none', 'one', 'two');
	        $hover = get_theme_mod( 'articlewave_image_hover_effect' , 'one' );
	        
	        $output = in_array($hover, $valid_effects) ? "hover-effect--$hover" : 'hover-effect--one';

	        echo esc_attr($output);
	    }

	endif;

