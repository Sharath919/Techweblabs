<?php
/**
 * Partial template to display archive post title
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$articlewave_enable_pagetitle_prefix = get_theme_mod( 'articlewave_enable_pagetitle_prefix' , true );
if ( $articlewave_enable_pagetitle_prefix == true ) :

?>

<div class="articlewave-post-title-prefix-wrapper">

<?php 
        $archive_category = get_queried_object(); 

        $archive_posts_query = new WP_Query(
            array(
                'category_name'  => $archive_category->slug,
                'posts_per_page' => -1,
            )
        );

        $post_count = $archive_posts_query->found_posts;

        wp_reset_postdata();
?>
    <div class="articlewave-container">
        <div class="articlewave-title-wrap">
            <header class="page-header">
            <?php
                the_archive_title( '<h3 class="page-title">', '</h3>' );
            ?>
                <div class="articlewave-post-count">
                    <?php
                        echo '<div>';
                        printf( __( '<span> Total Posts: %1$s</span>', 'articlewave' ), $post_count );
                        echo '</div>';
                    ?>
                </div>
            </header><!-- .page-header -->
        </div>

        <div class="articlewave-post-prefix-wrapper">
            <div class="features-post-title"><?php esc_html_e( 'Latest Post', 'articlewave' ); ?></div>
                <div class="articlewave-title-prefix articlewave-grid ">
                <?php
                    $latest_args = array(
                        'posts_per_page'        => apply_filters( 'articlewave_archive_title_count', 4 ),
                        'orderby'               => 'date',
                        'order'                 => 'DESC',
                        'ignore_sticky_posts'   => true
                    );

                    $latest_query = new WP_Query( $latest_args );

                    if ( $latest_query->have_posts() ) :
                        while ( $latest_query->have_posts() ) :
                            $latest_query->the_post(); 
                ?>
                            <div class="title-prefix-post-wrap <?php if ( ! has_post_thumbnail() ){ echo esc_attr( 'no-img-post' ); } ?>">
                                <?php if ( has_post_thumbnail() ) { ?>
                                <div class="title-prefix-post-thumb">
                                    <figure class="title-prefix-post-image" <?php articlewave_schema_markup('image') ?>>
                                        <a href="<?php echo esc_url( get_the_permalink() ); ?>" <?php articlewave_schema_markup( 'url' ); ?>><?php the_post_thumbnail( 'thumbnail' ); ?></a>
                                    </figure>
                                </div>
                                <?php } ?>

                                <div class="related-post-content-wrap">
                                    <?php articlewave_the_post_categories_list( get_the_ID(), 1 ); ?>
                                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                </div> <!-- related-post-content-wrap -->
                            </div><!-- title-prefix-post-wrap -->

                <?php
                        endwhile;
                        
                    endif;
                ?>
                </div><!-- articlewave-title-prefix-->

            </div><!-- articlewave-post-prefix-wrapper-->

        </div><!-- articlewave-container-->
    
</div><!-- .articlewave-post-title-prefix-wrapper -->

<?php endif; ?>
