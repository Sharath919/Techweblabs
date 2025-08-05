<?php
/**
 * Partial template to display tab
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

<div class="tabbed-section-wrapper">
<?php
    $articlewave_tabbed_title_field  = get_theme_mod( 'articlewave_tabbed_title_field' , __( 'Popular Posts', 'articlewave' ) );
    $articlewave_tabbed_recent_field = get_theme_mod( 'articlewave_tabbed_recent_field', __( 'Recent Posts', 'articlewave' ) );
?>
     <div class="tab-buttons">
        <?php
            $articlewave_enable_tabbed_title = get_theme_mod( 'articlewave_enable_tabbed_title', true );
            if ( $articlewave_enable_tabbed_title == true ) {
        ?>
                <button class="tab-button" id="popular-tab"><i class="bx bxs-hot"></i><?php echo esc_html( $articlewave_tabbed_title_field ); ?></button>
        <?php
            }
            $articlewave_enable_tabbed_recent = get_theme_mod( 'articlewave_enable_tabbed_recent', true );
            if ( $articlewave_enable_tabbed_recent == true ) {
        ?>
                <button class="tab-button" id="recent-tab"><i class="bx bxs-time"></i><?php echo esc_html( $articlewave_tabbed_recent_field ); ?></button>
        <?php
            }
            if ( $articlewave_enable_tabbed_title == false && $articlewave_enable_tabbed_recent == false ) {
        ?>
            <button class="tab-button" id="recent-tab"><i class="bx bxs-time"></i><?php  esc_html_e( 'Latest Post', 'articlewave' ); ?></button>
        <?php
            }
        ?>
     </div> <!-- tab-buttons -->

    <div class="tabbed-posts-wrapper" id="popular-posts">
    <?php
        $tabbed_args = array(
            'posts_per_page' => apply_filters( 'articlewave_tab_post_count', 2 ),
            'orderby'        => 'comment_count',
            'order'          => 'DESC',
            'ignore_sticky_posts' => apply_filters( 'articlewave_tab_ignore_sticky_post' , 1 ),
        );
        $tabbed_query = new WP_Query( $tabbed_args );
        if ( $tabbed_query->have_posts() ) {
            echo '<div class="tabbed-posts-wrapper">';
            while( $tabbed_query->have_posts() ) {
                $tabbed_query->the_post();
            ?>
                <div class="tabbed-single-post-wrap <?php if ( ! has_post_thumbnail() ){ echo esc_attr( 'no-img-post' ); } ?>">
                    <div class="tabbed-post-thumb">
                        <figure class ="post-image <?php articlewave_image_hover_effect(); ?> " <?php articlewave_schema_markup( 'image' ); ?>>
                            <a class="article-post-thumb" href="<?php echo esc_url( get_the_permalink() ); ?>" <?php articlewave_schema_markup( 'url' ); ?>><?php the_post_thumbnail( 'medium' ); ?></a>
                        </figure>
                    </div>
                    <div class="tabbed-post-content-wrap">
                        <?php articlewave_the_post_categories_list( get_the_ID(), 1 ); ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    </div>
                </div>
        <?php
            }
            echo '</div>';
        }
        ?>
    </div><!-- tabbed-posts-wrapper -->

    <div class="tabbed-posts-wrapper" id="recent-posts">
     <?php
        $tabbed_args = array(
            'posts_per_page' =>  apply_filters( 'articlewave_tab_post_count', 2 ),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'ignore_sticky_posts' => apply_filters( 'articlewave_tab_ignore_sticky_post' , 1 ),
         );
        $tabbed_query = new WP_Query( $tabbed_args );
         if ( $tabbed_query->have_posts() ) {
                echo '<div class="tabbed-posts-wrapper">';
                while ( $tabbed_query->have_posts() ) {
                    $tabbed_query->the_post();
                ?>
                    <div class="tabbed-single-post-wrap <?php if ( ! has_post_thumbnail() ){ echo esc_attr( 'no-img-post' ); } ?>">
                        <div class="tabbed-post-thumb">
                            <figure class ="post-image <?php articlewave_image_hover_effect(); ?> " <?php articlewave_schema_markup( '
                            image' ); ?>>
                                <a  class="article-post-thumb" href="<?php echo esc_url( get_the_permalink() ); ?>" <?php articlewave_schema_markup( 'url' ); ?>><?php the_post_thumbnail( 'medium' ); ?></a>
                            </figure>
                        </div>
                        <div class="tabbed-post-content-wrap">
                            <?php articlewave_the_post_categories_list( get_the_ID(), 1 ); ?>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        </div>
                    </div>
                <?php
                }
                echo '</div>';
            }
        ?>
    </div>  <!-- tabbed-posts-wrapper -->
</div><!-- tabbed-section-wrapper -->