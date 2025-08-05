<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Articlewave
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function articlewave_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	$articlewave_site_layout = get_theme_mod( 'articlewave_site_layout', 'full-width' );
	$classes[] = 'articlewave-site-layout--'. esc_attr( $articlewave_site_layout );

	$articlewave_site_mode_switcher_option = get_theme_mod('articlewave_site_mode_switcher_option', true);
	if ( false == $articlewave_site_mode_switcher_option ) {
        $articlewave_site_mode_option = get_theme_mod('articlewave_site_mode_option', 'light' );
        $classes[] = 'site-mode--'.esc_attr( $articlewave_site_mode_option);
    }

    if ( is_home() ) {
        $articlewave_front_post_display = get_theme_mod('articlewave_front_post_display', 'grid');
        $classes[] = 'front-page-post-style--'.esc_attr($articlewave_front_post_display);
    }

    if ( is_archive() || is_single() || is_search() ) {
    	$articlewave_archive_post_display = get_theme_mod('articlewave_archive_post_display', 'grid');
    	$classes[] = 'archive-style--'.esc_attr($articlewave_archive_post_display);
    }

    if ( is_front_page() || is_home() ) {

        $front_masonry_option = get_theme_mod ( 'articlewave_masonry_for_front_grid_option' , true );
       
        if ($front_masonry_option == true ) {
            $classes[] = 'front-grid--masonry';
        }
    }

    if ( is_archive() || is_search() ) {

        $archive_masonry_option = get_theme_mod ( 'articlewave_masonry_for_archive_grid_option' , true );

      if ($archive_masonry_option == true ) {
            $classes[] = 'archive-grid--masonry';
        }

    }

    $articlewave_archive_and_blog_sidebar_layout = get_theme_mod('articlewave_archive_and_blog_sidebar_layout', 'right-sidebar');
    $articlewave_page_sidebar_layout = get_theme_mod('articlewave_page_sidebar_layout', 'right-sidebar');
    $articlewave_post_sidebar_layout = get_theme_mod('articlewave_post_sidebar_layout', 'right-sidebar');

    if ( is_page() ) {
        $classes[] = 'sidebar-layout--'.esc_attr( $articlewave_page_sidebar_layout );
    } elseif ( is_single() || is_singular() ) {
        $classes[] = 'sidebar-layout--'.esc_attr( $articlewave_post_sidebar_layout );
    } elseif ( is_archive() || is_search() ) {
        $classes[] = 'sidebar-layout--'.esc_attr( $articlewave_archive_and_blog_sidebar_layout );
    } elseif ( is_home() || is_front_page() ) {
        $classes[] = 'sidebar-layout--'.esc_attr( $articlewave_archive_and_blog_sidebar_layout );
    }
    
    return $classes;
}
add_filter( 'body_class', 'articlewave_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function articlewave_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'articlewave_pingback_header' );

/**
 * Filters for the sticky sidebar
 */
function custom_sidebar_sticky( $sidebar_sticky ) {

    return $sidebar_sticky;
}

add_filter('articlewave_sidebar_sticky_filter', 'custom_sidebar_sticky');

/**
 * Filters for the sticky header
 */
function custom_header_sticky( $header_sticky ) {

    return $header_sticky;
}

add_filter('articlewave_header_sticky_filter', 'custom_header_sticky');

/**
 * Check if there is logo or not
 */
if ( ! function_exists( 'articlewave_get_logo_site_class' ) ) :

    function articlewave_get_logo_site_class() {

        if ( ! has_custom_logo() && ! display_header_text() ) {
            return 'no-logo-site';
        } elseif ( ! has_custom_logo() && display_header_text() ) {
            return 'has-site-info';
        } else {
            return 'logo-site'; 
        }
    }

endif;

function articlewave_scripts() {

    wp_enqueue_style( 'articlewave-fonts', articlewave_google_font_callback(), array(), null );

    wp_enqueue_style( 'articlewave-style', get_stylesheet_uri(), array(), ARTICLEWAVE_VERSION );

	wp_enqueue_style( 'articlewave-responsive-style', get_template_directory_uri() . '/assets/css/articlewave-responsive.css', array(), ARTICLEWAVE_VERSION );

    wp_enqueue_style( 'articlewave-preloader-style', get_template_directory_uri() . '/assets/css/articlewave-preloader.css', array(), ARTICLEWAVE_VERSION );

	wp_enqueue_style( 'box-icons', get_template_directory_uri() . '/assets/library/box-icons/css/boxicons.css', array(), ARTICLEWAVE_VERSION );

	wp_enqueue_style( 'lightslider', get_template_directory_uri() . '/assets/library/lightslider/css/lightslider.css', array(), '1.1.6' );

	wp_enqueue_script( 'lightslider', get_template_directory_uri() . '/assets/library/lightslider/js/lightslider.js', array(), '1.1.6', true );	

    wp_enqueue_script( 'jquery-sticky-sidebar', get_template_directory_uri() . '/assets/library/sticky-sidebar/theia-sticky-sidebar.min.js', array(), ARTICLEWAVE_VERSION, true );

    wp_enqueue_script( 'jquery-sticky', get_template_directory_uri() . '/assets/library/sticky/jquery.sticky.min.js', array( 'jquery' ), ARTICLEWAVE_VERSION, true );

    wp_enqueue_script( 'imagesloaded' );

    wp_enqueue_script( 'masonry' );

	wp_enqueue_script( 'articlewave-custom-scripts', get_template_directory_uri() . '/assets/js/custom-scripts.js', array( 'jquery'), ARTICLEWAVE_VERSION, true );

    $header_sticky              = apply_filters('articlewave_header_sticky_filter', 'true');
    $sidebar_sticky             = apply_filters('articlewave_sidebar_sticky_filter', 'true');
    $articlewave_search_option  = get_theme_mod('articlewave_search_option', 'live-search');
    $live_search                = ($articlewave_search_option == 'live-search') ? 'true' : 'false';

    wp_localize_script( 'articlewave-custom-scripts', 'MT_JSObject',
        array(
            'header_sticky'     => $header_sticky,
            'sidebar_sticky'    => $sidebar_sticky,
            'live_search'       => $live_search,
            'ajaxUrl'           => admin_url('admin-ajax.php'),
            '_wpnonce'          => wp_create_nonce('articlewave-nonce')

        )
    );

    wp_enqueue_script( 'articlewave-navigation', get_template_directory_uri() . '/js/navigation.js', array(), ARTICLEWAVE_VERSION , true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
    
}

add_action( 'wp_enqueue_scripts' , 'articlewave_scripts' );


if ( ! function_exists( 'articlewave_admin_scripts' ) ) :

    /**
     * Enqueue admin scripts and styles.
     */
    function articlewave_admin_scripts( $hook ) {

        // Only needed on these admin screens
        if ( $hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php' && 'widgets.php' != $hook ) {
            return;
        }

        wp_enqueue_style( 'articlewave-widget-style', get_template_directory_uri() . '/inc/widgets/assets/css/widget-admin-style.css', array(), ARTICLEWAVE_VERSION );

        wp_enqueue_script( 'articlewave-widget-script', get_template_directory_uri() . '/inc/widgets/assets/js/widget-admin-script.js', array( 'jquery' ), ARTICLEWAVE_VERSION, true );
    }

endif;

add_action( 'admin_enqueue_scripts', 'articlewave_admin_scripts' );

if ( ! function_exists( 'articlewave_social_icons_array' ) ) :

    /**
     * List of box icons
     *
     * @return array();
     * @since 1.0.0
     */
    function articlewave_social_icons_array() {
        return array(
            "bx bxl-facebook", "bx bxl-facebook-circle", "bx bxl-facebook-square", "bx bxl-twitter", "bx bxl-google", "bx bxl-google-plus", "bx bxl-google-plus-circle", "bx bxl-google-cloud", "bx bxl-instagram", "bx bxl-instagram-alt", "bx bxl-skype", "bx bxl-whatsapp", "bx bxl-whatsapp-square", "bx bxl-tiktok", "bx bxl-airbnb", "bx bxl-deviantart", "bx bxl-linkedin", "bx bxl-linkedin-square", "bx bxl-pinterest", "bx bxl-pinterest-alt", "bx bxl-adobe", "bx bxl-flickr", "bx bxl-flickr-square", "bx bxl-tumblr", "bx bxl-slack", "bx bxl-reddit", "bx bxl-messenger", "bx bxl-wordpress", "bx bxl-behance", "bx bxl-dribbble", "bx bxl-yahoo", "bx bxl-blogger", "bx bxl-snapchat", "bx bxl-wix", "bx bxl-meta", "bx bxl-baidu", "bx bxl-discord", "bx bxl-twitch", "bx bxl-discord-alt", "bx bxl-vk", "bx bxl-trip-advisor", "bx bxl-telegram", "bx bxl-quora", "bx bxl-ok-ru", "bx bxl-microsoft-teams", "bx bxl-foursquare", "bx bxl-soundcloud", "bx bxl-vimeo", "bx bxl-digg", "bx bxl-periscope", "bx bxl-xing", "bx bxl-youtube", "bx bxl-imdb",
        );
    }

endif;


if ( ! function_exists( 'articlewave_minify_css' ) ) {

    /**
     * Minify CSS
     *
     * @since 1.0.0
     */
    function articlewave_minify_css( $css = '' ) {

        // Return if no CSS
        if ( ! $css ) return;

        // Normalize whitespace
        $css = preg_replace( '/\s+/', ' ', $css );

        // Remove ; before }
        $css = preg_replace( '/;(?=\s*})/', '', $css );

        // Remove space after , : ; { } */ >
        $css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );

        // Remove space before , ; { }
        $css = preg_replace( '/ (,|;|\{|})/', '$1', $css );

        // Strips leading 0 on decimal values (converts 0.5px into .5px)
        $css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );

        // Strips units if value is 0 (converts 0px to 0)
        $css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );

        // Trim
        $css = trim( $css );

        // Return minified CSS
        return $css;

    }

}

if ( ! function_exists( 'articlewave_get_google_font_variants' ) ) :

    /**
     * get Google font variants
     *
     * @since 1.0.0
     */
    function articlewave_get_google_font_variants() {
        $articlewave_font_list = get_option( 'articlewave_google_font' );

        $font_family = $_REQUEST['font_family'];

        $variants_array = $articlewave_font_list[$font_family]['0'];

        $options_array = '<option value="inherit">'. __( 'Inherit', 'articlewave' ) .'</option>';
        foreach ( $variants_array as $variant ) {
            $variant_html = articlewave_convert_font_variants( $variant );
            $options_array .= '<option value="'.esc_attr( $variant ).'">'. esc_html( $variant_html ).'</option>';
        }
        echo $options_array;
        die();
    }

endif;

add_action( "wp_ajax_get_google_font_variants", "articlewave_get_google_font_variants" );

if ( ! function_exists( 'articlewave_convert_font_variants' ) ) :

    /**
     * function to manage the variant name according to their value.
     *
     * @param $value  - string
     * @return string - variant name
     * @since 1.0.0
     */
    function articlewave_convert_font_variants( $value ) {
        switch ( $value ) {
            case '100':
                return __( 'Thin 100', 'articlewave' );
                break;

            case '100italic':
                return __( 'Thin 100 Italic', 'articlewave' );
                break;

            case '200':
                return __( 'Extra-Light 200', 'articlewave' );
                break;

            case '200italic':
                return __( 'Extra-Light 200 Italic', 'articlewave' );
                break;

            case '300':
                return __( 'Light 300', 'articlewave' );
                break;

            case '300italic':
                return __( 'Light 300 Italic', 'articlewave' );
                break;

            case '400':
                return __( 'Normal 400', 'articlewave' );
                break;

            case 'italic':
                return __( 'Normal 400 Italic', 'articlewave' );
                break;

            case '400italic':
                return __( 'Normal 400 Italic', 'articlewave' );
                break;

            case '500':
                return __( 'Medium 500', 'articlewave' );
                break;

            case '500italic':
                return __( 'Medium 500 Italic', 'articlewave' );
                break;

            case '600':
                return __( 'Semi-Bold 600', 'articlewave' );
                break;

            case '600italic':
                return __( 'Semi-Bold 600 Italic', 'articlewave' );
                break;

            case '700':
                return __( 'Bold 700', 'articlewave' );
                break;

            case '700italic':
                return __( 'Bold 700 Italic', 'articlewave' );
                break;

            case '800':
                return __( 'Extra-Bold 800', 'articlewave' );
                break;

            case '800italic':
                return __( 'Extra-Bold 800 Italic', 'articlewave' );
                break;

            case '900':
                return __( 'Ultra-Bold 900', 'articlewave' );
                break;

            case '900italic':
                return __( 'Ultra-Bold 900 Italic', 'articlewave' );
                break;

            case 'inherit':
                return __( 'Inherit', 'articlewave' );
                break;

            default:
                break;
        }
    }

endif;

if ( ! function_exists( 'articlewave_google_font_callback' ) ) :

    /**
     * Load google fonts api link
     *
     * @since 1.0.0
     */
    function articlewave_google_font_callback() {

        $articlewave_get_font_list   = get_option( 'articlewave_google_font' );

        $articlewave_body_font_family    = articlewave_get_customizer_option_value( 'articlewave_body_font_family' );
        $articlewave_body_font_weight    = implode( ',', $articlewave_get_font_list[$articlewave_body_font_family]['0'] );
        $body_typo_combo        = $articlewave_body_font_family.":".$articlewave_body_font_weight;

        $articlewave_heading_font_family     = articlewave_get_customizer_option_value( 'articlewave_heading_font_family' );
        $articlewave_heading_font_weight     = implode( ',', $articlewave_get_font_list[$articlewave_heading_font_family]['0'] );
        $heading_typo_combo     = $articlewave_heading_font_family.":".$articlewave_heading_font_weight;

        $get_fonts          = array( $body_typo_combo, $heading_typo_combo );

        $final_font_string = implode( '|', $get_fonts );

        $google_fonts_url = '';

        if ( $final_font_string ) {
            $query_args = array(
                'family' => urlencode( $final_font_string ),
                'subset' => urlencode( 'latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic,khmer,devanagari,arabic,hebrew,telugu' )
            );

            $google_fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
        }

        return $google_fonts_url;
    }

endif;

if ( ! function_exists( 'articlewave_get_actual_font_weight' ) ) :

    /**
     * get font weight in integer
     */
    function articlewave_get_actual_font_weight( $font_weight ) {

        if ( 'regular' == $font_weight ) {
            $font_weight = '400';
        } elseif ( 'italic' == $font_weight ) {
            $font_weight = '400italic';
        }

        return $font_weight;

    }

endif;

if ( ! function_exists( 'articlewave_get_date_format_args' ) ) :

    /**
     * Generate date format array for query arguments
     *
     * @return array
     * @since 1.0.0
     */
    function articlewave_get_date_format_args( $format ) {

        switch( $format ) {

            case 'today':
                $today_date = getdate();
                $get_args   = array(
                    'year'  => $today_date['year'],
                    'month' => $today_date['mon'],
                    'day'   => $today_date['mday'],
                );

                return $get_args ;
                break;

            case 'this-week':
                $get_args = array(
                    'year'  => date( 'Y' ),
                    'week'  => date( 'W' )
                );

                return $get_args;
                break;

            case 'last-week':
                $this_week = date( 'W' );

                if ( $this_week != 1 ) {
                    $last_week = $this_week - 1;
                } else {
                    $last_week = 52;
                }

                $this_year = date( 'Y' );

                if ( $last_week != 52 ) {
                    $this_year = date( 'Y' );
                } else {
                    $this_year = date( 'Y' ) -1;
                }

                $get_args = array(
                    'year'  => $this_year,
                    'week'  => $last_week
                );

                return $get_args;
                break;

            case 'this-month':
                $today_date = getdate();
                $get_args   = array(
                    'month' => $today_date['mon']
                );

                return $get_args;
                break;

            case 'last-month':
                $this_date = getdate();

                if ( $this_date['mon'] != 1 ) {
                    $last_month = $this_date['mon'] - 1;
                } else {
                    $last_month = 12;
                }

                $this_year = date( 'Y' );
                if ( $last_month != 12 ) {
                    $this_year = date('Y');
                } else {
                    $this_year = date('Y') - 1;
                }

                $get_args = array(
                    'year'  => $this_year,
                    'month'  => $last_month
                );

                return $get_args;
                break;

            default: return [];
        }

    }

endif;

if (!function_exists('articlewave_search_posts_content')) :

    /**
     * Ajax call for live search
     *
     * @return array
     * @since 1.0.0
     */
    function articlewave_search_posts_content() {

        check_ajax_referer('articlewave-nonce', 'security');

        $search_key = isset( $_POST['search_key'] ) ? sanitize_text_field( $_POST['search_key'] ) : '' ;

        $query_vars = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 4,
            's' => $search_key,
        );

        $n_posts = new WP_Query( $query_vars );
        $res['loaded'] = false;

        ob_start();
        echo '<div class="articlewave-search-results-wrap">';
        echo '<div class="articlewave-search-posts-wrap">';

        if ( $n_posts->have_posts() ) {
            $res['loaded'] = true; 

            while ( $n_posts->have_posts() ) :
                $n_posts->the_post();
        ?>
                <div class="articlewave-item">

                    <figure class="articlewave-post-thumb-wrap">

                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
        <?php
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'thumbnail', array(
                                    'title' => the_title_attribute(array(
                                        'echo' => false
                                    ))
                                ));
                            }
                            ?>
                        </a>
                    </figure>

                    <div class="articlewave-post-element">
                        <h4 class="articlewave-post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
                        <?php articlewave_posted_on(); ?>
                    </div> <!-- articlewave-post-element -->

                </div><!-- articlewave-item -->
    <?php
            endwhile;

        } else {

            echo 'No Results Found'; 

            $all_categories = get_categories();

            if ( ! empty( $all_categories ) ) {

                echo '<div class="articlewave-search-suggestion-category"> #Suggestion ';

                foreach ($all_categories as $category) {
                     echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
                }

                echo '</div>';
            }
        }
        echo '</div>';
        echo '</div>';

        $res['posts'] = ob_get_clean();
        
        echo json_encode($res);
        wp_die();
    }

    add_action('wp_ajax_articlewave_search_posts_content', 'articlewave_search_posts_content');

    add_action('wp_ajax_nopriv_articlewave_search_posts_content', 'articlewave_search_posts_content');

endif;

if ( ! function_exists( 'articlewave_get_right_sidebar' ) ) :

    /**
     * Function define about page/post/archive sidebar
     */
    function articlewave_get_right_sidebar() {

        $articlewave_archive_and_blog_sidebar_layout = get_theme_mod( 'articlewave_archive_and_blog_sidebar_layout', 'right-sidebar' );
        $articlewave_post_sidebar_layout = get_theme_mod( 'articlewave_post_sidebar_layout', 'right-sidebar' );
        $articlewave_page_sidebar_layout = get_theme_mod( 'articlewave_page_sidebar_layout', 'right-sidebar' );

        if (is_page() ) {
            $sidebar_layout = $articlewave_page_sidebar_layout;
            switch ( $sidebar_layout ) {
                case 'right-sidebar':
                    get_sidebar();
                    break;

                case 'both-sidebar':
                    get_sidebar();
                    break;

                case 'no-sidebar' || 'no-sidebar-center':
                    break;
            }

        } elseif ( is_single() || is_singular() ) {
            $sidebar_layout = $articlewave_post_sidebar_layout;
            switch ( $sidebar_layout ) {
                case 'right-sidebar':
                    get_sidebar();
                    break;

                case 'both-sidebar':
                    get_sidebar();
                    break;

                case 'no-sidebar' || 'no-sidebar-center':
                    break;
            }

        } elseif ( is_archive() || is_search() || is_home() ) {
            $sidebar_layout = $articlewave_archive_and_blog_sidebar_layout;
            switch ( $sidebar_layout ) {
                case 'right-sidebar':
                    get_sidebar();
                    break;

                case 'both-sidebar':
                    get_sidebar();
                    break;

                case 'no-sidebar' || 'no-sidebar-center':
                    break;
            }

        }
    }

endif;

if ( ! function_exists( 'articlewave_get_left_sidebar' ) ) :

    /**
     * Function define about page/post/archive sidebar
     */
    function articlewave_get_left_sidebar() {

        $articlewave_archive_and_blog_sidebar_layout = get_theme_mod( 'articlewave_archive_and_blog_sidebar_layout', 'right-sidebar' );
        $articlewave_post_sidebar_layout = get_theme_mod( 'articlewave_post_sidebar_layout', 'right-sidebar' );
        $articlewave_page_sidebar_layout = get_theme_mod( 'articlewave_page_sidebar_layout', 'right-sidebar' );

        if ( is_page() ) {
            $sidebar_layout = $articlewave_page_sidebar_layout;
            switch ( $sidebar_layout ) {
                case 'left-sidebar':
                    get_sidebar( 'left' );
                    break;

                case 'both-sidebar':
                    get_sidebar( 'left' );
                    break;

                case 'no-sidebar' || 'no-sidebar-center':
                    break;
            }

        } elseif ( is_single() || is_singular() ) {
            $sidebar_layout = $articlewave_post_sidebar_layout;
             switch ( $sidebar_layout ) {
                case 'left-sidebar':
                    get_sidebar( 'left' );
                    break;

                case 'both-sidebar':
                    get_sidebar( 'left' );
                    break;

                case 'no-sidebar' || 'no-sidebar-center':
                    break;
            }

        } elseif ( is_archive() || is_search() || is_home() ) {
            $sidebar_layout = $articlewave_archive_and_blog_sidebar_layout;
             switch ( $sidebar_layout ) {
                case 'left-sidebar':
                    get_sidebar( 'left' );
                    break;

                case 'both-sidebar':
                    get_sidebar( 'left' );
                    break;

                case 'no-sidebar' || 'no-sidebar-center':
                    break;
            }

        }
    }
    
endif;

if ( ! function_exists( 'articlewave_get_schema_markup' ) ) :

    /**
     * Return correct schema markup
     *
     * @since 1.0.0
     */
    function articlewave_get_schema_markup( $location ) {

        // Default
        $schema = $itemprop = $itemtype = '';

        // HTML
        if ( 'html' == $location ) {
            if ( is_home() || is_front_page() ) {
                $schema = 'itemscope=itemscope itemtype=https://schema.org/WebPage';
            } elseif ( is_category() || is_tag() || is_singular( 'post') ) {
                $schema = 'itemscope=itemscope itemtype=https://schema.org/Blog';
            } elseif ( is_page() ) {
                $schema = 'itemscope=itemscope itemtype=https://schema.org/WebPage';
            } else {
                $schema = 'itemscope=itemscope itemtype=https://schema.org/WebPage';
            }
        }

        // Creative work
        if ( 'creative_work' == $location ) {
            if ( is_single() ) {
                $schema = 'itemscope=itemscope itemtype=https://schema.org/creative_work';
            } elseif ( is_home() || is_archive() ) {
                $schema = 'itemscope=itemscope itemtype=https://schema.org/creative_work';
            }
        }

        // Header
        if ( 'header' == $location ) {
            $schema = 'itemscope=itemscope itemtype=https://schema.org/WPHeader';
        }

        // Logo
        if ( 'logo' == $location ) {
            $schema = 'itemscope itemtype=https://schema.org/Organization';
        }

        // Navigation
        if ( 'site_navigation' == $location ) {
            $schema = 'itemscope=itemscope itemtype=https://schema.org/SiteNavigationElement';
        }

        // Main
        if ( 'main' == $location ) {
            $itemtype = 'https://schema.org/WebPageElement';
            $itemprop = 'mainContentOfPage';
        }

        // Sidebar
        if ( 'sidebar' == $location ) {
            $schema = 'itemscope=itemscope itemtype=https://schema.org/WPSideBar';
        }

        // Footer widgets
        if ( 'footer' == $location ) {
            $schema = 'itemscope=itemscope itemtype=https://schema.org/WPFooter';
        }

        // Headings
        if ( 'headline' == $location ) {
            $schema = 'itemprop=headline';
        }

        // Posts
        if ( 'entry_content' == $location ) {
            $schema = 'itemprop=text';
        }

        // Publish date
        if ( 'publish_date' == $location ) {
            $schema = 'itemprop=datePublished';
        }

        // Modified date
        if ( 'modified_date' == $location ) {
            $schema = 'itemprop=dateModified';
        }

        // Author name
        if ( 'author_name' == $location ) {
            $schema = 'itemprop=name';
        }

        // Author link
        if ( 'author_link' == $location ) {
            $schema = 'itemprop=author itemscope=itemscope itemtype=https://schema.org/Person';
        }

        // Item
        if ( 'item' == $location ) {
            $schema = 'itemprop=item';
        }

        // Url
        if ( 'url' == $location ) {
            $schema = 'itemprop=url';
        }

        // Position
        if ( 'position' == $location ) {
            $schema = 'itemprop=position';
        }

        // Image
        if ( 'image' == $location ) {
            $schema = 'itemprop=image';
        }

        // Avatar
        if ( 'avatar' == $location ) {
            $schema = 'itemprop=avatar';
        }

        // Description
        if ( 'description' == $location ) {
            $schema = 'itemprop=description';
        }

        return ' ' . apply_filters( 'articlewave_schema_markup_items', $schema );

    }

endif;

if ( ! function_exists( 'articlewave_schema_markup' ) ) :

    /**
     * Outputs correct schema markup
     *
     * @since 1.0.0
     */
    function articlewave_schema_markup( $location ) {

        echo articlewave_get_schema_markup( $location );

    }

endif;