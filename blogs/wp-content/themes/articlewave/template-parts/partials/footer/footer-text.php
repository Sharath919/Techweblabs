<?php
/**
 * Partial template to display tabbed
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="footer-text">
    <span class="copyright-content">
    <?php
        $articlewave_bottom_footer_area = get_theme_mod( 'articlewave_bottom_footer_field' );
        echo wp_kses_post( str_replace( '{year}', date('Y'), $articlewave_bottom_footer_area ) );
    ?>
    </span>
    <a href="<?php echo esc_url( __( 'https://mysterythemes.com/', 'articlewave' ) ); ?>" rel="noopener noreferrer nofollow">
        <?php
        /* translators: %s: CMS name, i.e. WordPress. */
        printf( esc_html__( 'Proudly powered by %s', 'articlewave' ), 'Mystery Themes' );
        ?>
    </a>
</div>