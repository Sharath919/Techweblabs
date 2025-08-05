<?php
/**
 * Customizer Typography Control.
 * 
 * @package Articlewave
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ArticleWave_Control_Typography' ) ) {

    /**
     * Typography Control
     *
     * @since 1.0.0
     */
    class ArticleWave_Control_Typography extends WP_Customize_Control {
    
        /**
         * The type of customize control being rendered.
         *
         * @since  1.0.0
         * @access public
         * @var    string
         */
        public $type = 'mt-typography';

        /**
         * Array 
         *
         * @since  1.0.0
         * @access public
         * @var    string
         */
        public $l10n = array();

        /**
         * Set up our control.
         *
         * @since  1.0.0
         * @access public
         * @param  object  $manager
         * @param  string  $id
         * @param  array   $args
         * @return void
         */
        public function __construct( $manager, $id, $args = array() ) {

            // Let the parent class do its thing.
            parent::__construct( $manager, $id, $args );

            // Make sure we have labels.
            $this->l10n = wp_parse_args(
                $this->l10n,
                array(
                    'family'        => esc_html__( 'Font Family', 'articlewave' ),
                    'weight'        => esc_html__( 'Font Weight', 'articlewave' ),
                    'style'         => esc_html__( 'Font Style', 'articlewave' ),
                    'transform'     => esc_html__( 'Font Transform', 'articlewave' ),
                    'decoration'    => esc_html__( 'Font Decoration', 'articlewave' )
                )
            );
        }

        /**
         * Enqueue scripts/styles.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public function enqueue() {

            wp_enqueue_script( 'articlewave-google-webfont', get_template_directory_uri() . '/inc/customizer/custom-controls/typography/webfontloader.js', array( 'jquery' ) );

            wp_enqueue_script( 'articlewave-typo-ajax-script', get_template_directory_uri() . '/inc/customizer/custom-controls/typography/typo-ajax.js', array( 'jquery', 'select2' ), false, true );
            
            wp_localize_script( 'articlewave-typo-ajax-script', 'ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }

        /**
         * Add custom parameters to pass to the JS via JSON.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public function to_json() {
            parent::to_json();

            // Loop through each of the settings and set up the data for it.
            foreach ( $this->settings as $setting_key => $setting_id ) {

                $this->json[ $setting_key ] = array(
                    'link'  => $this->get_link( $setting_key ),
                    'value' => $this->value( $setting_key ),
                    'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : '',
                );

                $this->json[$setting_key]['setid'] = $this->settings[ $setting_key ]->id;

                switch ( $setting_key ) {
                    case 'family':
                        $this->json[ $setting_key ]['choices'] = $this->get_font_families();
                        break;

                    case 'weight':
                        $this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();
                        break;

                    case 'style':
                        $this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
                        break;

                    case 'transform':
                        $this->json[ $setting_key ]['choices'] = $this->get_font_transform_choices();
                        break;

                    case 'decoration':
                        $this->json[ $setting_key ]['choices'] = $this->get_font_decoration_choices();
                        break;
                    
                    default:
                        break;
                }
            }


        }

        /**
         * Underscore JS template to handle the control's output.
         *
         * @since  1.0.0
         * @access public
         * @return void
         */
        public function content_template() {
        ?>

        <# if ( data.label ) { #>
            <span class="customize-control-title">{{ data.label }}</span>
        <# } #>

        <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
        <# } #>

        <ul>

        <# if ( data.family && data.family.choices ) { #>

            <li class="typography-font-family">

            <# if ( data.family.label ) { #>
                <span class="customize-control-title">{{ data.family.label }}</span>
            <# } #>

                <select {{{ data.family.link }}} id="{{ data.section }}" class="typography_face customize-control-select2">

                <# _.each( data.family.choices, function( label, choice ) { #>
                    <option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                <# } ) #>

                </select>
            </li>
        <#  } #>

        <# if ( data.weight && data.weight.choices ) { #>

            <li class="typography-font-weight">

            <# if ( data.weight.label ) { #>
                <span class="customize-control-title">{{ data.weight.label }}</span>
            <# } #>

                <select {{{ data.weight.link }}}>

                <# _.each( data.weight.choices, function( label, choice ) { #>
                    <option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                <# } ) #>

                </select>
            </li>
        <#  } #>

        <# if ( data.style && data.style.choices ) { #>

            <li class="typography-font-style">

            <# if ( data.style.label ) { #>
                <span class="customize-control-title">{{ data.style.label }}</span>
            <# } #>

                <select {{{ data.style.link }}}>

                <# _.each( data.style.choices, function( label, choice ) { #>
                    <option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                <# } ) #>

                </select>
            </li>
        <#  } #>

        <# if ( data.transform && data.transform.choices ) { #>

            <li class="typography-font-transform">

            <# if ( data.transform.label ) { #>
                <span class="customize-control-title">{{ data.transform.label }}</span>
            <# } #>

                <select {{{ data.transform.link }}}>

                <# _.each( data.transform.choices, function( label, choice ) { #>
                    <option value="{{ choice }}" <# if ( choice === data.transform.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                <# } ) #>

                </select>

            </li>

        <#  } #>

        <# if ( data.decoration && data.decoration.choices ) { #>

            <li class="typography-font-decoration">

            <# if ( data.decoration.label ) { #>
                <span class="customize-control-title">{{ data.decoration.label }}</span>
            <# } #>

                <select {{{ data.decoration.link }}}>

                <# _.each( data.decoration.choices, function( label, choice ) { #>
                    <option value="{{ choice }}" <# if ( choice === data.decoration.value ) { #> selected="selected" <# } #>>{{ label }}</option>
                <# } ) #>

                </select>

            </li>
        <#  } #>

        </ul>
        <?php }

        /**
         * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
         *
         * @todo Integrate with Google fonts.
         *
         * @since  1.0.0
         * @access public
         * @return array
         */
        public function get_fonts() { return array(); }

        /**
         * Returns the available font families.
         *
         * @todo Pull families from `get_fonts()`.
         *
         * @since  1.0.0
         * @access public
         * @return array
         */
        function get_font_families() {

            $articlewave_google_font = get_option( 'articlewave_google_font' );

            foreach ( $articlewave_google_font as $key => $value ) {
                $mt_fonts[esc_attr( $key )] = esc_html( $key );
            }

            return $mt_fonts;
        }

        /**
         * Returns the available font weights.
         *
         * @since  1.0.0
         * @access public
         * @return array
         */
        public function get_font_weight_choices() {

            if ( $this->settings['family']->id ) {
                $articlewave_font_list = get_option( 'articlewave_google_font' );
                $font_family_id = $this->settings['family']->id;
                $get_font_family = articlewave_get_customizer_option_value( $font_family_id );
                $variants_array = $articlewave_font_list[$get_font_family]['0'];

                if ( is_array( $variants_array ) ) {
                    $options_array = array(
                        'inherit' => __( 'Inherit', 'articlewave' )
                    );
                    foreach ( $variants_array  as $variants ) {
                        $options_array[$variants] = articlewave_convert_font_variants( $variants );
                    }

                    return $options_array;
                } else {
                    return array(
                        'inherit'   => esc_html__( 'Inherit', 'articlewave' ),
                        '400'       => esc_html__( 'Normal 400', 'articlewave' ),
                        '700'       => esc_html__( 'Bold 700', 'articlewave' ),
                    );
                }
            } else {
                return array(
                    'inherit'   => esc_html__( 'Inherit', 'articlewave' ),
                    '400'       => esc_html__( 'Normal 400', 'articlewave' ),
                    '700'       => esc_html__( 'Bold 700', 'articlewave' ),
                );
            }
        }

        /**
         * Returns the available font style.
         *
         * @since  1.0.0
         * @access public
         * @return array
         */
        public function get_font_style_choices() {

            $font_style_choices = apply_filters( 'articlewave_font_style_choices',
                array(
                    'inherit'   => __( 'Inherit', 'articlewave' ),
                    'normal'    => __( 'Normal', 'articlewave' ),
                    'italic'    => __( 'Italic', 'articlewave' ),
                )
            );

            return $font_style_choices;

        }

        /**
         * Returns the available font transform.
         *
         * @since  1.0.0
         * @access public
         * @return array
         */
        public function get_font_transform_choices() {

            $font_transform_choices = apply_filters( 'articlewave_font_transform_choices',
                array(
                    'inherit'       => __( 'Inherit', 'articlewave' ),
                    'lowercase'     => __( 'Lowercase', 'articlewave' ),
                    'capitalize'    => __( 'Capitalize', 'articlewave' ),
                    'uppercase'     => __( 'Uppercase', 'articlewave' ),
                )
            );

            return $font_transform_choices;
            
        }

        /**
         * Returns the available font decoration.
         *
         * @since  1.0.0
         * @access public
         * @return array
         */
        public function get_font_decoration_choices() {

            $font_decoration_choices = apply_filters( 'articlewave_font_decoration_choices',
                array(
                    'inherit'       => __( 'Inherit', 'articlewave' ),
                    'underline'     => __( 'Underline', 'articlewave' ),
                    'line-through'  => __( 'Line-through', 'articlewave' ),
                )
            );

            return $font_decoration_choices;
            
        }

    }
}