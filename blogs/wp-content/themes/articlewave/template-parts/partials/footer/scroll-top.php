<?php
/**
 * Partial template to display scroll up
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$articlewave_enable_scrolltop = get_theme_mod( 'articlewave_enable_scrolltop' , true );

if ( $articlewave_enable_scrolltop == true ) { ?>
    <div class = "articlewave-scrollup">
        <i class ="bx bx-up-arrow-alt"> </i>
    </div> <!-- articlewave-scrollup -->
<?php 
    } 
?>