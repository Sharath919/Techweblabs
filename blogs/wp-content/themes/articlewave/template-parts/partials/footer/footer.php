<?php
/**
 * Partial template to display footer
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$articlewave_widget_area_layout  = get_theme_mod( 'articlewave_widget_area_layout', 'column-two' ); ?>

<div id="footer-widget-area" class="widget-area footer-widget-<?php echo esc_attr( $articlewave_widget_area_layout ); ?> ">

    <div class="footer-widget-wrapper articlewave-grid">
        <?php
            $widget_columns = array(
                'column-one',
                'column-two',
                'column-three',
                'column-four',
            );

            $selected_column_index = array_search( $articlewave_widget_area_layout , $widget_columns );

            if ( $selected_column_index !== false ) {
                for ( $i = 0; $i <= $selected_column_index; $i++ ) {
                    $column = $widget_columns[$i];
                    
                    echo '<div class="footer-widget-'.$column.' ">';
                        dynamic_sidebar( 'footer-widget-' . $column );
                    echo '</div>';
                }
            }
        ?>
    </div><!-- footer-widget-wrapper-->
    
</div><!-- footer-widget-area -->
