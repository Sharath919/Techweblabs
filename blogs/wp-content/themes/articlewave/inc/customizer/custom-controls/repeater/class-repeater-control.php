<?php
/**
 * Customizer Repeater Control.
 * 
 * @package ArticleWave
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ArticleWave_Control_Repeater' ) ) :
    
    /**
     * Repeater control
     *
     * @since 1.0.0
     */
    class ArticleWave_Control_Repeater extends WP_Customize_Control {

        /**
         * The control type.
         *
         * @access public
         * @var string
         * @since 1.0.0
         */
        public $type = 'articlewave-repeater';

        /**
         * The repeater field label.
         *
         * @access public
         * @var string
         * @since 1.0.0
         */
        public $articlewave_box_label = '';

        /**
         * The repeater field add button label.
         *
         * @access public
         * @var string
         * @since 1.0.0
         */
        public $articlewave_box_add_control = '';

        /**
         * The repeater field limit count.
         *
         * @access public
         * @var int
         * @since 1.0.0
         */
        public $articlewave_field_limit;

        /**
         * The fields that each container row will contain.
         *
         * @access public
         * @var array
         * @since 1.0.0
         */
        public $fields = array();

        /**
         * Get the listed categories.
         *
         * @access public
         * @var array
         * @since 1.0.0
         */
        public $categories = '';

        /**
         * Repeater drag and drop controller
         *
         * @since  1.0.0
         */
        public function __construct( $manager, $id, $args = array(), $fields = array() ) {
            
            $this->fields = $fields;
            $this->articlewave_box_label          = $args['articlewave_box_label_text'] ;
            $this->articlewave_box_add_control    = $args['articlewave_box_add_control_text'];
            $this->articlewave_field_limit        = isset( $args['articlewave_field_limit'] ) ? $args['articlewave_field_limit'] : 5;
            parent::__construct( $manager, $id, $args );
        }

        public function enqueue() {            
            wp_enqueue_style( 'articlewave-repeater-style', get_template_directory_uri() . '/inc/customizer/custom-controls/repeater/repeater.css', null );
            wp_enqueue_script( 'articlewave-repeater-script', get_template_directory_uri() . '/inc/customizer/custom-controls/repeater/repeater.js', array( 'jquery' ), false, true );
        }

        protected function render_content() {

            $repeater_id    = $this->id;
            $values         = json_decode( $this->value() );
            $field_count    = count( $values );
            
    ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

            <?php if ( $this->description ) { ?>
                <span class="description customize-control-description">
                    <?php echo wp_kses_post( $this->description ); ?>
                </span>
            <?php } ?>

            <ul class="articlewave-repeater-field-control-wrap">
                <?php $this->articlewave_get_fields(); ?>
            </ul>

            <input type="hidden" <?php $this->link(); ?> class="articlewave-repeater-collector" value="<?php echo esc_attr( $this->value() ); ?>" />
            <input type="hidden" name="<?php echo esc_attr( $repeater_id ).'_count'; ?>" class="field-count" value="<?php echo absint( $field_count ); ?>">
            <input type="hidden" name="field_limit" class="field-limit" value="<?php echo esc_attr( $this->articlewave_field_limit ); ?>">
            <button type="button" class="button articlewave-repeater-add-control-field"><i class="bx bx-plus"></i><?php echo esc_html( $this->articlewave_box_add_control ); ?></button>
    <?php
        }

        private function articlewave_get_fields() {

            $fields = $this->fields;
            $values = json_decode( $this->value() );

            if ( is_array( $values ) ) {

                foreach ( $values as $value ) {
                    if ( 'show' === $value->item_visible ) {
                        $item_class = 'item-visible';
                        $dash_icon = 'visibility';
                    } else {
                        $item_class = 'item-not-visible';
                        $dash_icon = 'hidden';
                    }
        ?>
                <li class="articlewave-repeater-field-control <?php echo esc_attr( $item_class ); ?>">

                    <div class="heading-wrapper">
                        <span class="field-display dashicons dashicons-<?php echo esc_attr( $dash_icon ); ?>"></span>
                        <span class="articlewave-repeater-field-title <?php echo esc_attr( $item_class ); ?>"><?php echo esc_html( $this->articlewave_box_label ); ?></span>
                        
                    </div><!-- .heading-wrapper -->
                
                    <div class="articlewave-repeater-fields">
                        <?php
                            foreach ( $fields as $key => $field ) {

                                $class = isset( $field['class'] ) ? $field['class'] : '';

                                $field_type  = isset( $field['type'] ) ? $field['type'] : 'none
                                ';
                        ?>
                                <div class="articlewave-repeater-field articlewave-repeater-type-<?php echo esc_attr( $field_type ).' '.esc_attr( $class ); ?>">

                                    <?php 
                                        $label          = isset( $field['label'] ) ? $field['label'] : '';
                                        $description    = isset( $field['description'] ) ? $field['description'] : '';
                                        $placeholder    = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
                                        $new_value      = isset( $value->$key ) ? $value->$key : '';
                                        $default        = isset( $field['default'] ) ? $field['default'] : '';

                                        if ( 'checkbox' != $field['type'] && 'item_visible' !== $key ) {
                                    ?>
                                            <span class="customize-control-title"><?php echo esc_html( $label ); ?></span>
                                            <span class="description customize-control-description"><?php echo esc_html( $description ); ?></span>
                                    <?php 
                                        }

                                        switch ( $field['type'] ) {

                                            /**
                                             * URL field
                                             */
                                            case 'url':
                                                echo '<input data-default="'.esc_attr( $default ).'" data-name="'.esc_attr( $key ).'" type="text" placeholder="'. esc_attr( $placeholder ) .'" value="'.esc_url( $new_value ).'"/>';
                                                break;
     
                                            /**
                                             * Social Icon field
                                             */
                                            case 'social_icon':
                                                $articlewave_social_icons_array = articlewave_social_icons_array();
                                                echo '<div class="articlewave-repeater-selected-icon"><i class="'. esc_attr( $new_value ) .'"></i><span><i class="bx bx-chevron-down"></i></span></div><ul class="articlewave-repeater-icon-list articlewave-clearfix">';
                                                foreach ( $articlewave_social_icons_array as $articlewave_social_icon ) {
                                                    $icon_class = $new_value == $articlewave_social_icon ? 'icon-active' : '';
                                                    echo '<li class='. esc_attr( $icon_class ) .'><i class="'. esc_attr( $articlewave_social_icon ) .'"></i></li>';
                                                }
                                                echo '</ul><input data-default="'. esc_attr( $default ) .'" type="hidden" value="'. esc_attr( $new_value ) .'" data-name="'. esc_attr( $key ) .'"/>';
                                                break;

                                            case 'hidden' :
                                                echo '<input type="hidden" class="repeater-field-visible-holder" data-default="' .esc_attr( $default ). '" data-name="' .esc_attr( $key ). '" value="' .esc_attr( $new_value ). '">';
                                                break;

                                            default:
                                                break;
                                        }
                                    ?>
                                </div>
                        <?php
                            }
                        ?>
                        <div class="articlewave-clearfix articlewave-repeater-footer">
                            <div class="alignright">
                            <a class="articlewave-repeater-field-remove" href="#remove"><?php esc_html_e( 'Delete', 'articlewave' ) ?></a> |
                            <a class="articlewave-repeater-field-close" href="#close"><?php esc_html_e( 'Close', 'articlewave' ) ?></a>
                            </div>
                        </div><!-- .articlewave-repeater-footer -->
                    </div><!-- .articlewave-repeater-fields-->
                </li>
        <?php   
                }
            }
        }

    }

endif;