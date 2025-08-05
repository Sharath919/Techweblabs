<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Articlewave
 */

$articlewave_archive_and_blog_sidebar_layout = get_theme_mod('articlewave_archive_and_blog_sidebar_layout', 'right-sidebar');
$articlewave_post_sidebar_layout = get_theme_mod('articlewave_post_sidebar_layout' , 'right-sidebar');
$articlewave_page_sidebar_layout = get_theme_mod('articlewave_page_sidebar_layout', 'right-sidebar');

$sidebar_layout = '';
if ( is_page() ) {
     $sidebar_layout = $articlewave_page_sidebar_layout;
} elseif ( is_single() || is_singular() ) {
	$sidebar_layout = $articlewave_post_sidebar_layout;
} elseif (is_archive() || is_search() || is_home() ) {
	$sidebar_layout = $articlewave_archive_and_blog_sidebar_layout;
}

if ( ! is_active_sidebar( 'sidebar-right' ) ) {
	return;
}
?>


<aside id="secondary" class="widget-area sidebar-layout-<?php echo esc_attr( $sidebar_layout ); ?>" <?php articlewave_schema_markup('sidebar') ?>>
	<?php dynamic_sidebar( 'sidebar-right' ); ?>
</aside><!-- #secondary -->




