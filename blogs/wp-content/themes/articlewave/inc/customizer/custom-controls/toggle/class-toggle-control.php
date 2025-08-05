<?php
/**
 * Customizer Toggle Control.
 * 
 * @package Articlewave
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ArticleWave_Control_Toggle' ) ) :

	/**
	 * Toggle control (modified checkbox).
     *
     * @since 1.0.0
     */
	class ArticleWave_Control_Toggle extends WP_Customize_Control {
		
		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 * @since 1.0.0
		 */
		public $type = 'mt-toggle';
        
        /**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 * @since 1.0.0
		 */
        public function to_json() {
			parent::to_json();

            $this->json['value']   = $this->value();
			$this->json['link']    = $this->get_link();
            $this->json['id']      = $this->id;

		}

        /**
		 * Don't render the content via PHP.  This control is handled with a JS template.
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue() {            
            wp_enqueue_style( 'articlewave-toggle-style', get_template_directory_uri() . '/inc/customizer/custom-controls/toggle/toggle.css', null );
            wp_enqueue_script( 'articlewave-toggle-script', get_template_directory_uri() . '/inc/customizer/custom-controls/toggle/toggle.js', array( 'jquery' ), false, true );
        }
        
		/**
         * An Underscore (JS) template for this control's content (but not its container).
         *
         * Class variables for this control class are available in the `data` JS object;
         * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
         *
         * @see WP_Customize_Control::print_template()
         *
         * @access protected
         * @since 1.0.0
         */
        protected function content_template() {
    ?>
    		<div class="mt-toggle">
				<div class="toggle--wrapper">
					<# if ( data.label ) { #>
						<span class="customize-control-title">{{ data.label }}</span>
					<# } #>

					<input id="toggle-{{ data.id }}" type="checkbox" class="toggle--input" value="{{ data.value }}" {{{ data.link }}} <# if ( data.value ) { #> checked="checked" <# } #> />
					<label for="toggle-{{ data.id }}" class="toggle--label"></label>
				</div><!-- .toggle--wrapper -->

				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{ data.description }}</span>
				<# } #>
			</div><!-- mt-toggle -->
	<?php
		}
	}
	
endif;