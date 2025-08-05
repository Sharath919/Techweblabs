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
?>

<div class ="articlewave-sidebar-toggle-wrap">
   <?php
        $articlewave_enable_sticky_header_sidebar = get_theme_mod( 'articlewave_enable_sticky_header_sidebar', true );
        if ( $articlewave_enable_sticky_header_sidebar == true ) {
    ?>
            <button class="sidebar-menu-toggle articlewave-modal-toggler" data-popup-content=".sticky-header-sidebar">
            <a href="javascript:void(0)">
                <div class="sidebar-menu-toggle-nav">
                    <span class="smtn-top"></span>
                    <span class="smtn-mid"></span>
                    <span class="smtn-bot"></span>
                </div>
            </a>
            </button>  

              <div class="sticky-header-sidebar articlewave-modal-popup-content">
                <div class="sticky-header-widget-wrapper">
                    <?php
                        if ( is_active_sidebar( 'header-sidebar-toggle' ) ) {
                            dynamic_sidebar( 'header-sidebar-toggle' );
                        }
                    ?>
                </div>
                <div class="sticky-header-sidebar-overlay"> </div>
                <button class="sticky-sidebar-close articlewave-madal-close" data-focus=".sidebar-menu-toggle.articlewave-modal-toggler"><i class="bx bx-x"></i></button>
            </div><!-- .sticky-header-sidebar -->
        <?php 
        }
    ?>
</div><!-- articlewave-sidebar-toggle-wrap -->
<?php 
$articlewave_enable_socialicons = get_theme_mod( 'articlewave_enable_socialicons', true );
    if ( $articlewave_enable_socialicons == true ) {
        $articlewave_socialicons = get_theme_mod( 'articlewave_socialicons' );
        $social_icon_array = json_decode( $articlewave_socialicons );
        if ( ! empty( $social_icon_array ) ) {
        ?>
            <div class="articlewave-social-icons-wrapper">
                <?php
                foreach ( $social_icon_array as $social_icon ) {
                    $icon_class = $social_icon->social_icon;
                    $icon_link = $social_icon->social_url;
                    $is_show = $social_icon->item_visible;
                    if ( 'show' == $is_show ) {
                ?>
                    <div class="single-icon-wrap">
                        <a href="<?php echo esc_url( $icon_link ); ?>"><i class="<?php echo esc_attr( $icon_class ); ?>"/></i>  </a>
                    </div><!-- .single-icon-wrap -->
                <?php
                    }
                }
            ?>
        </div><!-- .articlewave-social-icons-wrapper -->
        <?php
        }
    }
?>



