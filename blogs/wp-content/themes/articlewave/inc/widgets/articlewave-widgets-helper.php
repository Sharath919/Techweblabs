<?php
/**
 * Define custom fields and function for widgets
 * 
 * @package ArticleWave
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! function_exists( 'articlewave_show_widget_field' ) ) :

    /**
     * function to display the widget fields
     *
     * @since 1.0.0
     */
    function articlewave_show_widget_field( $instance = '', $widget_field = '', $widget_field_value = '' ) {

        extract( $widget_field );

        $widget_field_wrapper       = isset( $widget_field_wrapper ) ? $widget_field_wrapper : '';
        $widget_field_relation      = isset( $widget_field_relation ) ? $widget_field_relation : array();
        $widget_relation_json       = wp_json_encode( $widget_field_relation );
        $widget_relation_class      = ( $widget_field_relation ) ? 'widget_field_relation' : '';
        $widget_field_placeholder   = isset( $widget_field_placeholder ) ? $widget_field_placeholder : '';

        switch ( $widget_field_type ) {

            /**
             * Widget title field ( ~ text field )
             *
             * @since 1.0.0
             */
            case 'title' :
            ?>
                <p class="widget-field-wrapper <?php echo esc_attr( $widget_field_wrapper ); ?>">
                    <label for="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>"><?php echo esc_html( $widget_field_title ); ?>:</label>
                    <input class="widefat" id="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $widget_field_name ) ); ?>" type="text" placeholder="<?php echo esc_html( $widget_field_placeholder ); ?>" value="<?php echo esc_html( $widget_field_value ); ?>" />

                    <?php if ( isset( $widget_field_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
                    <?php } ?>
                </p>
            <?php
                break;

            /**
             * Widget url field ( ~ text field )
             *
             * @since 1.0.0
             */
            case 'url' :
            ?>
                <p class="widget-field-wrapper <?php echo esc_attr( $widget_field_wrapper ); ?>">
                    <label for="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>"><?php echo esc_html( $widget_field_title ); ?>:</label>
                    <input class="widefat" id="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $widget_field_name ) ); ?>" type="url" placeholder="<?php echo esc_html( $widget_field_placeholder ); ?>" value="<?php echo esc_url( $widget_field_value ); ?>" />

                    <?php if ( isset( $widget_field_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
                    <?php } ?>
                </p>
            <?php
                break;

            /**
             * Widget textarea field
             */
            case 'textarea' :
            ?>
                <p class="widget-field-wrapper <?php echo esc_attr( $widget_field_wrapper ); ?>">
                    <label for="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>"><?php echo esc_html( $widget_field_title ); ?>:</label>

                    <textarea class="widefat" id="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $widget_field_name ) ); ?>"><?php echo wp_kses_post( $widget_field_value ); ?></textarea>
                    <?php if ( isset( $widget_field_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
                    <?php } ?>
                </p>
            <?php
                break;

            /**
             * Widget number field
             *
             * @since 1.0.0
             */
            case 'number' :
                $input_attr = isset( $input_attr ) ? $input_attr : array();
                //var_dump($input_attr['min']);
            ?>
                <p class="widget-field-wrapper <?php echo esc_attr( $widget_field_wrapper ); ?>">
                    <label for="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>"><?php echo esc_html( $widget_field_title ); ?>:</label>
                    <?php if ( ! empty( $input_attr ) ) : ?>
                        <input name="<?php echo esc_attr( $instance->get_field_name( $widget_field_name ) ); ?>" type="number" step="<?php echo absint( $input_attr['step'] ); ?>" max="<?php echo absint( $input_attr['max'] ); ?>" min="<?php echo absint( $input_attr['min'] ); ?>" id="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>" value="<?php echo intval( $widget_field_value ); ?>" class="small-text" />
                    <?php else : ?>
                        <input name="<?php echo esc_attr( $instance->get_field_name( $widget_field_name ) ); ?>" type="number" id="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>" value="<?php echo intval( $widget_field_value ); ?>" class="small-text" />
                    <?php endif; ?>
                    <?php if ( isset( $widget_field_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
                    <?php } ?>
                </p>
            <?php
                break;

            /**
             * Widget select field (~ dropdown)
             *
             * @since 1.0.0
             */
            case 'select' :
            ?>
                <p class="widget-field-wrapper <?php echo esc_attr( $widget_field_wrapper ); ?>">
                    <label for="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>"><?php echo esc_html( $widget_field_title ); ?>:</label>
                    <?php if ( isset( $widget_field_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
                    <?php } ?>
                    <select name="<?php echo esc_attr( $instance->get_field_name( $widget_field_name ) ); ?>" id="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>" class="widefat <?php echo esc_attr( $widget_relation_class ); ?>" data-relations="<?php echo esc_attr( $widget_relation_json ) ?>">
                        <?php foreach ( $widget_field_options as $key => $value ) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $instance->get_field_id( $key ) ); ?>" <?php selected( $key, $widget_field_value ); ?>><?php echo esc_html( $value ); ?></option>
                        <?php } ?>
                    </select>
                </p>
            <?php 
                break;

            /**
             * Widget field category dropdown
             *
             * @since 1.0.0
             */
            case 'category_dropdown' :
                $select_field = 'name="'. esc_attr( $instance->get_field_name( $widget_field_name ) ) .'" id="'. esc_attr( $instance->get_field_id( $widget_field_name ) ) .'" class="widefat"';
            ?>
                    <p class="post-cat mt-widget-field-wrapper <?php echo esc_attr( $widget_field_wrapper ); ?>">
                        <label for="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>"><?php echo esc_html( $widget_field_title ); ?>:</label>
                        <?php if ( isset( $widget_field_description ) ) { ?>
                           <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
                        <?php }

                            $dropdown_args = wp_parse_args( array(
                                'taxonomy'          => 'category',
                                'show_option_none'  => __( '- - Select Category - -', 'articlewave' ),
                                'selected'          => esc_attr( $widget_field_value ),
                                'show_option_all'   => '',
                                'orderby'           => 'id',
                                'order'             => 'ASC',
                                'show_count'        => 1,
                                'hide_empty'        => 1,
                                'child_of'          => 0,
                                'exclude'           => '',
                                'hierarchical'      => 1,
                                'depth'             => 0,
                                'tab_index'         => 0,
                                'hide_if_empty'     => false,
                                'option_none_value' => 0,
                                'value_field'       => 'slug',
                            ) );

                            $dropdown_args['echo'] = false;

                            $dropdown = wp_dropdown_categories( $dropdown_args );
                            $dropdown = str_replace( '<select', '<select ' . $select_field, $dropdown );
                            echo $dropdown;
                        ?>
                    </p>
            <?php
                break;

            /**
             * Widget selector field
             *
             * @since 1.0.0
             */
            case 'selector':
                if ( empty( $widget_field_value ) ) {
                    $widget_field_value = $widget_field_default;
                }
            ?>
                <p><span class="field-label"><label class="field-title"><?php echo esc_html( $widget_field_title ); ?></label></span></p>
            <?php
                if ( isset( $widget_field_description ) ) {
            ?>
                    <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
            <?php
                }
                echo '<div class="selector-labels">';
                foreach ( $widget_field_options as $key => $value ) {
                    $img_path = $value['img_path'];
                    $class = ( $widget_field_value == $key ) ? 'selector-selected': '';
                    echo '<label class="'. esc_attr( $class ) .'" data-val="'. esc_attr( $key ) .'">';
                    echo '<img src="'. esc_url( $value['img_path'] ) .'" title="'. esc_attr( $value['label'] ) .'" alt="'. esc_attr( $value['label'] ) .'"/>';
                    echo '</label>';
                }
                echo '</div>';
                echo '<input data-default="'. esc_attr( $widget_field_value ) .'" type="hidden" value="'. esc_attr( $widget_field_value ) .'" name="'. esc_attr( $instance->get_field_name( $widget_field_name ) ) .'"/>';
                break;

            /**
             * Widget upload field
             */
            case 'upload':
                $image = $image_class = "";
                if ( $widget_field_value ) { 
                    $image          = '<img src="'.esc_url( $widget_field_value ).'" style="max-width:100%;"/>';
                    $image_class    = ' hidden';
                }
            ?>
                <div class="attachment-media-view">
                    <p>
                        <span class="field-label"><label for="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>"><?php echo esc_html( $widget_field_title ); ?>:</label></span>
                    </p>
                    
                    <div class="placeholder<?php echo esc_attr( $image_class ); ?>">
                        <?php esc_html_e( 'No image selected', 'articlewave' ); ?>
                    </div>
                    <div class="thumbnail thumbnail-image">
                        <?php echo $image; ?>
                    </div>

                    <div class="actions articlewave-clearfix">
                        <button type="button" class="button mt-delete-button align-left"><?php esc_html_e( 'Remove', 'articlewave' ); ?></button>
                        <button type="button" class="button mt-upload-button alignright"><?php esc_html_e( 'Select Image', 'articlewave' ); ?></button>
                        
                        <input name="<?php echo esc_attr( $instance->get_field_name( $widget_field_name ) ); ?>" id="<?php echo esc_attr( $instance->get_field_id( $widget_field_name ) ); ?>" class="upload-id" type="hidden" value="<?php echo esc_url( $widget_field_value ); ?>"/>
                    </div>

                    <?php if ( isset( $widget_field_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $widget_field_description ); ?></em></span>
                    <?php } ?>

                </div>
            <?php
                break;
        }

    }

endif;

if ( ! function_exists( 'articlewave_widget_updated_field_value' ) ) :

    /**
     * function to manage the sanitize widget filed value
     *
     * @since 1.0.0 
     *
     */
    function articlewave_widget_updated_field_value( $widget_field, $new_field_value ) {

        extract( $widget_field );

        if ( $widget_field_type == 'number') {
            return absint( $new_field_value );
        } elseif ( $widget_field_type == 'textarea' || $widget_field_type == 'title' ) {
            return wp_kses_post( $new_field_value );
        } elseif ( $widget_field_type == 'url' ) {
            return esc_url_raw( $new_field_value );
        } elseif ( $widget_field_type == 'multicheckboxes' ) {
            $multicheck_list = array();
            if ( is_array( $new_field_value ) ) {
                foreach ( $new_field_value as $key => $value ) {
                    $multicheck_list[esc_attr( $key )] = esc_attr( $value );
                }
            }
            return $multicheck_list;
        } elseif ( $widget_field_type == 'multi_term_list' ) {
            $multi_term_list = array();
            if ( is_array( $new_field_value ) ) {
                foreach ( $new_field_value as $key => $value ) {
                    $multi_term_list[] = esc_attr( $value );
                }
            }
            return $multi_term_list;
        } else {
            return sanitize_text_field( $new_field_value );
        }
    }

endif;


if ( ! function_exists( 'articlewave_trending_posts_widget_layout_choices' ) ) :

    /**
     * function to return choices of trending posts widget layout
     *
     * @since 1.0.0
     */
    function articlewave_trending_posts_widget_layout_choices() {
        $widget_layout = apply_filters( 'articlewave_trending_posts_widget_layout_choices',
            array(
                'trending-layout--one'  => array(
                    'label'     => esc_html__( 'Layout One', 'articlewave' ),
                    'img_path'  => get_template_directory_uri() . '/inc/widgets/assets/images/latest-post-widget-layout-one.png'
                ),
                'trending-layout--one'  => array(
                    'label'     => esc_html__( 'Layout Two', 'articlewave' ),
                    'img_path'  => get_template_directory_uri() . '/inc/widgets/assets/images/latest-post-widget-layout-two.png'
                )
            )
        );
        
        return $widget_layout;
    }

endif;

if ( ! function_exists( 'articlewave_posts_query_filter_choices' ) ) :

    /**
     * function to return choices of post query filter
     *
     * @since 1.0.0
     */
    function articlewave_posts_query_filter_choices() {
        $post_query_filter = apply_filters( 'articlewave_posts_query_filter_choices',
            array(
                'latest'  => __( 'Latest Posts', 'articlewave' ),
                'random'  => __( 'Random Posts', 'articlewave' )
            )
        );
        
        return $post_query_filter;
    }

endif;

if ( ! function_exists( 'articlewave_trending_posts_filter_choices' ) ) :

    /**
     * function to return choices of trending posts filter
     *
     * @since 1.0.0
     */
    function articlewave_trending_posts_filter_choices() {
        $trending_posts_filter = apply_filters( 'articlewave_trending_posts_filter_choices',
            array(
                'tag'      => __( 'Tag', 'articlewave' ),
                'comment'  => __( 'Comment', 'articlewave' )
            )
        );
        
        return $trending_posts_filter;
    }

endif;


