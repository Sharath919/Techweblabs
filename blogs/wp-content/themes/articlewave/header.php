<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php articlewave_schema_markup( 'html' ); ?>>
<?php
    wp_body_open();
    do_action( 'articlewave_before_page' );
?>
    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'articlewave' ); ?></a>

        <header id="masthead" class="site-header <?php echo esc_attr( articlewave_get_logo_site_class() ); ?> " <?php articlewave_schema_markup( 'header' ); ?>>

            <div class="articlewave-container header-layout-one">
                <div class="site-branding" <?php articlewave_schema_markup( 'logo' ); ?>>
                <?php
                    the_custom_logo();
                    if ( is_front_page() && is_home() ) :
                        ?>
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"  rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php
                    else :
                    ?>
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php
                    endif;
                    $articlewave_description = get_bloginfo( 'description', 'display' );
                    if ( $articlewave_description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo $articlewave_description; ?></p>
                <?php endif; ?>
                </div><!-- .site-branding -->

                <div class="header-layout-wrapper">
                <?php
                    /**
                     * require file to display social icon and siderbar toggle in sticky header
                     *
                     * @since 1.0.0
                     */
                    get_template_part( 'template-parts/partials/header/sidebartoggle', 'socialicons' );
                ?>
                    <nav id="site-navigation" class="main-navigation articlewave-flex" <?php articlewave_schema_markup( 'site_navigation' ); ?>>
                        <button class="articlewave-menu-toogle" aria-controls="primary-menu" aria-expanded="false"> <?php esc_html_e( 'Menu', 'articlewave' ); ?> <i class="bx bx-menu"> </i> </button>
                        <div class="primary-menu-wrap">
                        <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'menu-1',
                                    'menu_id'        => 'primary-menu',
                                )
                            );
                        ?>
                        </div><!-- primary-menu-wrap -->
                    </nav>

                    <?php
                        /**
                         * require file to display sitemode and search in sticky header
                         *
                         * @since 1.0.0
                         */
                        get_template_part( 'template-parts/partials/header/sitemode','search' );
                    ?>
                </div><!--header-layout-wrapper -->
            </div><!-- articlewave-container -->
        </header><!-- #masthead -->
