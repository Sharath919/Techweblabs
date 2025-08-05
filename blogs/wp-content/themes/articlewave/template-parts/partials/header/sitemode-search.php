<?php
/**
 * Partial template to display sticky header and social icons
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$articlewave_site_mode_switcher_option = get_theme_mod( 'articlewave_site_mode_switcher_option' , true );
    if ( true == $articlewave_site_mode_switcher_option ) {
?>
        <div id="articlewave-site-mode-wrap" class="articlewave-icon-elements">
            <a id="mode-switcher" class="light-mode" data-site-mode="light-mode" href="#">
                <span class="site-mode-icon"><?php esc_html_e( 'site mode button', 'articlewave' ); ?></span>
            </a>
        </div><!-- #articlewave-site-mode-wrap -->
<?php
    }

$articlewave_enable_searchbar = get_theme_mod( 'articlewave_enable_searchbar' , true );
    if ( $articlewave_enable_searchbar == true ) {
?>
        <div class="header-search-wrapper articlewave-icon-elements">
            <span class="search-icon"><a href="javascript:void(0)"><i class="bx bx-search"></i></a></span>
            <div class="overlay search-form-wrap">
            <span class="search-icon-close"><a href="javascript:void(0)"><i class="bx close bx-x"></i></a></span>
                <?php get_search_form(); ?>
            </div><!-- .search-form-wrap -->
        </div><!-- .header-search-wrapper -->
<?php
    }
?>







