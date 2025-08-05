<?php
 /**
 * Articlewave Theme Customizer Sanitize functions
 *
 * @package Articlewave
 */

if ( ! function_exists( 'articlewave_sanitize_select' ) ) :
    
    /**
     * Sanitize select.
     *
     * @param mixed                $input The value to sanitize.
     * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
     * @return mixed Sanitized value.
     *
     * @since 1.0.0
     */
    function articlewave_sanitize_select( $input, $setting ) {
        // Ensure input is a slug.
        $input = sanitize_key( $input );

        // Get list of choices from the control associated with the setting.
        $choices = $setting->manager->get_control( $setting->id )->choices;

        // If the input is a valid key, return it; otherwise, return the default.
        return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
    }

endif;


if ( ! function_exists( 'articlewave_sanitize_checkbox' ) ) :

    /**
     * Sanitize checkbox.
     *
     * @param bool $checked Whether the checkbox is checked.
     * @return bool Whether the checkbox is checked.
     *
     * @since 1.0.0
     */
    function articlewave_sanitize_checkbox( $checked ) {

        return ( ( isset( $checked ) && true === $checked ) ? true : false );

    }

endif;


if ( ! function_exists( 'articlewave_sanitize_repeater' ) ) :

    /**
     * Sanitize repeater value
     *
     * @param  json $input Customizer setting input repeater json.
     * @param  object       $setting Setting Object.
     * @return array        Return array.
     *
     * @since 1.0.0
     */
    function articlewave_sanitize_repeater( $input, $setting ) {
        $input_decoded = json_decode( $input, true );
            
        if ( !empty( $input_decoded ) ) {
            foreach ( $input_decoded as $boxes => $box ) {
                foreach ( $box as $key => $value ) {
                    if ( $key == 'url' ) {
                        $input_decoded[$boxes][$key] = esc_url_raw( $value );
                    } else {
                        $input_decoded[$boxes][$key] = wp_kses_post( $value );
                    }
                }
            }
            return json_encode( $input_decoded );
        }
        
        return $input;
    }

endif;