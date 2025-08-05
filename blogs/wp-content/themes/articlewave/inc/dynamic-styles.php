<?php
/**
 * Managed the theme's dynamic styles.
 *
 * @package Articlewave
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*---------------------- Custom CSS ----------------------------*/

if ( ! function_exists( 'articlewave_custom_css' ) ) :

    /**
     * function to handle articlewave_head_css filter where all the css relation functions are hooked.
     *
     * @since 1.0.0
     */
    function articlewave_custom_css( $output_css = NULL ) {

        // Add filter articlewave_head_css for adding custom css via other functions.
        $output_css = apply_filters( 'articlewave_head_css', $output_css );

        if ( ! empty( $output_css ) ) {
            $output_css = wp_strip_all_tags( articlewave_minify_css( $output_css ) );
            echo "<!--Articlewave CSS -->\n<style type=\"text/css\">\n". $output_css ."\n</style>";
        }
    }

endif;

add_action( 'wp_head', 'articlewave_custom_css', 9999 );


/*---------------------- General CSS-------------------------*/

/**
 * function to handle the general css.
 *
 * @since 1.0.0
 */

if ( ! function_exists( 'articlewave_general_css' ) ) :

    /**
     * function to handle the genral css for all sections.
     *
     * @since 1.0.0
     */
    function articlewave_general_css( $output_css ) {

        $articlewave_primary_theme_color     = get_theme_mod( 'articlewave_primary_theme_color' , '#212121' );
        $articlewave_text_color              = get_theme_mod( 'articlewave_text_color' , '#212121' );
        $articlewave_link_color              = get_theme_mod( 'articlewave_link_color' , '#3b2d1b' );
        $articlewave_link_hover_color        = get_theme_mod( 'articlewave_link_hover_color' , '#960505' );

        $get_categories = get_categories( array( 'hide_empty' => 1 ) );

        //Primary theme color
        $custom_css = " .no-results input[type='submit'], input[type='submit'], .button-back-home,.tabbed-section-wrapper .tab-button:hover, .tabbed-section-wrapper .tab-button.isActive, #site-navigation ul li:hover>a, #site-navigation ul li.current-menu-item>a, #site-navigation ul li.current_page_item>a, #site-navigation ul li.current-menu-ancestor>a, #site-navigation ul li.focus>a, .articlewave-wave .aw-rect , .articlewave-three-bounce .aw-child, .articlewave-folding-cube .aw-cube:before,  .button-back-home,.navigation .nav-links a.page-numbers:hover, .navigation .nav-links .page-numbers.current {background-color: ". esc_attr( $articlewave_primary_theme_color ) ."}\n";

        // Text Color
        $custom_css .= " .articlewave-button.read-more-button a, .entry-title a, entry-title, author-name, .author-nicename a ,.articlewave-content-wrapper .nav-previous .nav-title,
        .articlewave-content-wrapper .nav-next .nav-title {color: ". esc_attr($articlewave_text_color  ) ."}\n";

        // Border Color
        $custom_css .= ".no-results input[type='submit'], input[type='submit'], .button-back-home,.tabbed-section-wrapper .tab-button:hover, .tabbed-section-wrapper .tab-button.isActive,.navigation .nav-links a.page-numbers:hover, .navigation .nav-links .page-numbers.current {border-color: ". esc_attr( $articlewave_primary_theme_color ) ."}\n";

         // Link Color
         $custom_css .= ".author-website a {color: ". esc_attr( $articlewave_link_color ) ."}\n";


        // Link Hover Color
        $custom_css .= ".articlewave-button.read-more-button a:hover, .widget a:hover, .widget a:hover::before, .widget li:hover::before, .site-info .widget a:hover, .author-name:hover, a:hover, a:focus, a:active, entry-title:hover,  .entry-title a:hover, .articlewave-content-wrapper .nav-previous .nav-title:hover,
        .articlewave-content-wrapper .nav-next .nav-title:hover, .author-nicename a:hover,.entry-cat .cat-links a:hover,.entry-cat a:hover,.byline:hover,.posted-on a:hover,.entry-meta span:hover::before{color: ". esc_attr(  $articlewave_link_hover_color ) ."}\n";

        // different categories color
        foreach ( $get_categories as $category ) {
            $get_term_id = $category->term_id;
            $get_cat_color = get_theme_mod( 'category_color_'.strtolower( $get_term_id ), '#3b2d1b' );


        $custom_css .= ".articlewave-post-cats-list li.cat-".absint( $get_term_id ) ." a { background-color: ". esc_attr( $get_cat_color ) ."}\n";
        }

        if ( ! empty( $custom_css ) ) {
            $output_css .= $custom_css;
        }

        return $output_css;

    }

endif;
add_filter( 'articlewave_head_css', 'articlewave_general_css' );


/*---------------------- Typography CSS-------------------------*/

    if ( ! function_exists( 'articlewave_typography_css' ) ) :

    /**
     * function to handle the typography css.
     *
     * @since 1.0.0
     */
    function articlewave_typography_css( $output_css ) {

        $custom_css = '';

        /**
         * Body typography
         */
        $articlewave_body_font_family       = articlewave_get_customizer_option_value( 'articlewave_body_font_family' );
        $articlewave_body_font_weight       = articlewave_get_actual_font_weight( articlewave_get_customizer_option_value( 'articlewave_body_font_weight' ) );
        $articlewave_body_font_style        = articlewave_get_customizer_option_value( 'articlewave_body_font_style' );
        $articlewave_body_font_transform    = articlewave_get_customizer_option_value( 'articlewave_body_font_transform' );
        $articlewave_body_font_decoration   = articlewave_get_customizer_option_value( 'articlewave_body_font_decoration' );

        $custom_css .= "body{
            font-family:        $articlewave_body_font_family;
            font-style:         $articlewave_body_font_style;
            font-weight:        $articlewave_body_font_weight;
            text-decoration:    $articlewave_body_font_decoration;
            text-transform:     $articlewave_body_font_transform;
        }\n";

         /**
         * H1 to H6 typography
         */
        $articlewave_heading_font_family       = articlewave_get_customizer_option_value( 'articlewave_heading_font_family' );
        $articlewave_heading_font_weight       = articlewave_get_customizer_option_value( 'articlewave_heading_font_weight' );
        $articlewave_heading_font_style        = articlewave_get_customizer_option_value( 'articlewave_heading_font_style' );
        $articlewave_heading_font_transform    = articlewave_get_customizer_option_value( 'articlewave_heading_font_transform' );
        $articlewave_heading_font_decoration   = articlewave_get_customizer_option_value( 'articlewave_heading_font_decoration' );

        $custom_css .= "h1, h2, h3, h4, h5, h6 {
            font-family:        $articlewave_heading_font_family;
            font-style:         $articlewave_heading_font_style;
            font-weight:        $articlewave_heading_font_weight;
            text-decoration:    $articlewave_heading_font_decoration;
            text-transform:     $articlewave_heading_font_transform;
        }\n";

         if ( ! empty( $custom_css ) ) {
            $output_css .= '/*/ Typography CSS /*/'. $custom_css;
        }

        return $output_css;
    }

endif;

add_filter( 'articlewave_head_css', 'articlewave_typography_css' );