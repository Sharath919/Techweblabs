<?php
/**
 * Partial template to display author box
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$articlewave_enable_authorbox = get_theme_mod( 'articlewave_enable_authorbox', true );
$author_id = get_the_author_meta( 'ID' );
$author_avatar = get_avatar( $author_id, 'thumbnail' );
$author_link = get_the_author_posts_link();
$author_url = get_the_author_meta( 'user_url' );

if ( $articlewave_enable_authorbox == true  ) {
?>
    <div class="articlewave-author-container"  <?php articlewave_schema_markup('avatar'); ?>>

        <div class="articlewave-author-image">
            <?php echo wp_kses_post( $author_avatar ); ?>
        </div> <!-- articlewave-author-container -->

        <div class="articlewave-author-info">

            <h3 class="author-nicename" <?php articlewave_schema_markup( 'author_name' ); ?>>
                <a href="<?php echo esc_url( $author_link ); ?>"><?php echo esc_html( get_the_author_meta( 'user_nicename', $author_id ) ); ?></a>
            </h3>
            <span class="author-website" <?php articlewave_schema_markup( 'author_link' ); ?>><?php esc_html_e( 'Website: ' , 'articlewave' );?> <a href="<?php echo esc_url( $author_url ); ?>" target="_blank"><?php echo esc_url( $author_url ); ?></a></span><br>
            <span class="author-description" <?php articlewave_schema_markup( 'description' ); ?>><?php the_author_meta( 'description', $author_id ); ?></span>
            
        </div><!-- articlewave-author-info -->

    </div><!-- articlewave-author-container -->

<?php
}
?>