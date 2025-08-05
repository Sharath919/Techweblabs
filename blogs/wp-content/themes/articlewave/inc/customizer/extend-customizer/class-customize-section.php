<?php
/**
 * Extend default customizer section.
 *
 * @package ArticleWave
 *
 * @see     WP_Customize_Section
 * @access  public
 */

if ( class_exists( 'WP_Customize_Section' ) ) {

	/**
	 * Class ArticleWave_Customize_Section
	 *
	 * @since 1.0.0
	 */
	class ArticleWave_Customize_Section extends WP_Customize_Section {

		/**
		 * Section.
		 *
		 * @var string
		 */
		public $section;

		/**
		 * Type of this section.
		 *
		 * @var string
		 */
		public $type = 'articlewave_section';

		/**
		 * Gather the parameters passed to client JavaScript via JSON.
		 *
		 * @return array The array to be exported to the client as JSON.
		 */
		public function json() {
			$array                   = wp_array_slice_assoc( (array) $this, array(
				'id',
				'description',
				'priority',
				'panel',
				'type',
				'description_hidden',
				'section',
			) );
			$array['title']          = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
			$array['content']        = $this->get_content();
			$array['active']         = $this->active();
			$array['instanceNumber'] = $this->instance_number;

			if ( $this->panel ) {
				$array['customizeAction'] = sprintf( esc_html__( 'Customizing &#9656; %s', 'articlewave' ), esc_html( $this->manager->get_panel( $this->panel )->title ) );
			} else {
				$array['customizeAction'] = esc_html__( 'Customizing', 'articlewave' );
			}

			return $array;
		}
	}
}