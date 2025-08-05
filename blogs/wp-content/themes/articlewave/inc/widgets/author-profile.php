<?php
/**
 * Widget for display author profile.
 *
 * @package Articlewave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Articlewave_Author_Profile extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'articlewave-widget articlewave_author_profile',
            'description'                   => __( 'Add a complete information about author.', 'articlewave' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'articlewave_author_profile', __( 'MT: Author Profile', 'articlewave' ), $widget_ops );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields() {
        
        $fields = array(
            'widget_title' => array(
                'widget_field_name'         => 'widget_title',
                'widget_field_title'        => __( 'Widget Title', 'articlewave' ),
                'widget_field_default'      => '',
                'widget_field_type'         => 'title',
                'widget_field_placeholder'  => __( 'Widget Title', 'articlewave' )
            ),
            'author_profile_picture' => array(
                'widget_field_name'     => 'author_profile_picture',
                'widget_field_title'    => __( 'Author Profile Picture', 'articlewave' ),
                'widget_field_default'  => '',
                'widget_field_type'     => 'upload'
            ),
            'author_name' => array(
                'widget_field_name'         => 'author_name',
                'widget_field_title'        => __( 'Author Name', 'articlewave' ),
                'widget_field_default'      => '',
                'widget_field_type'         => 'title',
                'widget_field_placeholder'  => __( 'Author Name', 'articlewave' )
            ),
            'author_bio' => array(
                'widget_field_name'         => 'author_bio',
                'widget_field_title'        => __( 'Author Bio', 'articlewave' ),
                'widget_field_default'      => '',
                'widget_field_type'         => 'textarea',
            ),
            'author_link' => array(
                'widget_field_name'         => 'author_link',
                'widget_field_title'        => __( 'Author Link', 'articlewave' ),
                'widget_field_default'      => '',
                'widget_field_type'         => 'url'
            ),

            
        );

        return $fields;

    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );
        if ( empty( $instance ) ) {
            return;
        }

        $widget_title       = empty( $instance['widget_title'] ) ? '' : $instance['widget_title'];
        $profile_picture    = empty( $instance['author_profile_picture'] ) ? '' : $instance['author_profile_picture'];
        $author_name        = empty( $instance['author_name'] ) ? '' : $instance['author_name'];
        $author_bio         = empty( $instance['author_bio'] ) ? '' : $instance['author_bio'];
        $author_link        = empty( $instance['author_link'] ) ? '' : $instance['author_link'];

        echo $before_widget;
    ?>
        <div class="articlewave-author-profile-wrapper">
            <?php
                if ( ! empty( $widget_title ) ) {
                    echo $before_title . wp_kses_post( $widget_title ) . $after_title;
                }
            ?>
            <div class="author-info-wrap">
                <div class="author-avatar">
                    <a href="<?php echo esc_url( $author_link ); ?>">
                        <?php
                            if ( !empty( $profile_picture ) ) {
                                echo '<img src="'. esc_url( $profile_picture ) .'" />';
                            }
                        ?>
                    </a>
                </div>
                <a href="<?php echo esc_url( $author_link ); ?>">
                    <h3 class="author-name"> <?php echo esc_html( $author_name ); ?> </h3>
                </a>
                <div class="author-bio"> <?php echo esc_html( $author_bio ); ?> </div>
            </div><!-- .author-info-wrap -->
        </div><!-- .articlewave-author-profile-wrapper -->
    <?php
        echo $after_widget;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param   array   $new_instance   Values just sent to be saved.
     * @param   array   $old_instance   Previously saved values from database.
     *
     * @uses    articlewave_widget_updated_field_value()      defined in articlewave-widgets-helper.php
     *
     * @return  array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ( $widget_fields as $widget_field ) {

            extract( $widget_field );

            // Use helper function to get updated field values
            $instance[$widget_field_name] = articlewave_widget_updated_field_value( $widget_field, $new_instance[$widget_field_name] );
        }

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param   array $instance Previously saved values from database.
     *
     * @uses    articlewave_show_widget_field()        defined in articlewave-widgets-helper.php
     */
    public function form( $instance ) {

        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ( $widget_fields as $widget_field ) {

            // Make array elements available as variables
            extract( $widget_field );

            if ( empty( $instance ) && isset( $widget_field_default ) ) {
                $widget_field_value = $widget_field_default;
            } elseif ( empty( $instance ) ) {
                $widget_field_value = '';
            } else {
                $widget_field_value = $instance[$widget_field_name];
            }
            articlewave_show_widget_field( $this, $widget_field, $widget_field_value );
        }
    }

} //end Class Articlewave_Author_Profile