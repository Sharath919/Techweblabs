<?php
/**
 * Partial template to display related post 
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$articlewave_enable_relatedpost = get_theme_mod( 'articlewave_enable_relatedpost', true );

if ( $articlewave_enable_relatedpost == true ) {

    $categories = get_the_category();

    if ( ! empty( $categories ) ) {
        $category_slugs = wp_list_pluck( $categories, 'slug' );

        $related_args = array(
            'category_name' => implode( ',', $category_slugs ),
            'posts_per_page' => apply_filters( 'articlewave_related_post_count', 3 ),
            'post__not_in' => array( get_the_ID() ),

        );

        $related_query = new WP_Query( $related_args );

        if ( $related_query->have_posts() ) :

            echo '<div class="related-posts">';
            echo '<h2>' . esc_html__( 'Related Posts', 'articlewave' ) . '</h2>';
            echo '<div class = "articlewave-related-posts-wrapper">';

            while ( $related_query->have_posts() ) :
                $related_query->the_post();
?>
                <div class= "related-post-wrap <?php if ( ! has_post_thumbnail() ){ echo esc_attr( 'no-img-post' ); } ?>">

                    <div class="related-post-thumb" >
                        <figure class ="related-post-image <?php articlewave_image_hover_effect(); ?> " <?php articlewave_schema_markup( 'image' ); ?> >
                            <a class="related-post-thumb" href="<?php echo esc_url( get_the_permalink() ); ?> " <?php articlewave_schema_markup( 'url' ); ?> > <?php the_post_thumbnail( 'medium' ); ?></a>
                        </figure>
                    </div> <!-- related-post-thumb -->

                    <div class="related-post-content-wrap">
                        <?php articlewave_the_post_categories_list( get_the_ID(), 1 ); ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </div> <!-- related-post-content-wrap -->

                </div> <!-- related-post-wrap -->

<?php
            endwhile;

        echo '</div>';
        echo '</div>';

        wp_reset_postdata();

        endif;
    }
}

?>