<?php
/**
 * Partial template to display slider
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

<div class="slider-section-wrapper">
<?php
	$articlewave_enable_slider = get_theme_mod( 'articlewave_enable_slider' , true );
	if ( $articlewave_enable_slider == true ) {
?>
	<div class ="slider-section">
	<?php
		$articlewave_slider_category_dropdown = get_theme_mod( 'articlewave_slider_category_dropdown' );
		$slider_args = array(
            'category_name' => esc_attr( $articlewave_slider_category_dropdown ),
            'order'  		=> 'DESC',
            'orderby'   	=> 'title'
	    );
		$slider_query = new WP_Query( $slider_args );
		 if ( $slider_query->have_posts() ) {
	            echo '<div class="slider-posts-wrapper" id="slider-single-post">';
	            while ( $slider_query->have_posts() ) {
	                $slider_query->the_post();
	            ?>
	                <div class="slider-single-post-wrap <?php if ( ! has_post_thumbnail() ){ echo esc_attr( 'no-img-post' ); } ?>">
	                    <div class="slider-post-thumb">
	                    	<figure class ="post-image <?php articlewave_image_hover_effect(); ?> " <?php articlewave_schema_markup( 'image' ); ?>>
	                    		<a  class="article-post-thumb" href="<?php echo esc_url( get_the_permalink() ); ?>" <?php articlewave_schema_markup( 'url' ); ?>><?php the_post_thumbnail(); ?></a>
	                    	</figure>
	                    </div>
	                    <div class="slider-post-content-wrap">
	                    	<?php articlewave_the_post_categories_list( get_the_ID(), 1 ); ?>
	                        <h2><a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a></h2>
	                        <div class="slider-post-meta-wrap">
	                            <?php
	                                articlewave_posted_on();
	                                articlewave_posted_by();
	                            ?>
	                        </div>
	                        <div class="articlewave-read-more-est-time">
		                        <a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'articlewave' ); ?></a>
		                        <?php articlewave_the_estimated_reading_time(); ?>
	                        </div>
	                    </div>
	                </div>
		<?php
	            }
	            echo '</div>';
	        }
	    }
		?>
	</div> <!--slider-section -->
	
</div><!-- slider-section-wrapper-->