<?php
/**
 * Active callback fucntuions.
 * 
 * @package Articlewave
 */

if ( ! function_exists( 'articlewave_has_enable_preloader' ) ) :

	/**
	 * check the settting preloader is enable or not.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_has_enable_preloader( $control ) {
		if ( false !== $control->manager->get_setting( 'articlewave_enable_preloader' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

endif;

if ( ! function_exists( 'articlewave_has_enable_socialicons' ) ) :

	/**
	 * check the setting social icons is enable or not.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_has_enable_socialicons( $control ) {
		if ( false !== $control->manager->get_setting( 'articlewave_enable_socialicons' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

endif;


if ( ! function_exists( 'articlewave_has_enable_search' ) ) :

	/**
	 * check the setting search is enable or not.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_has_enable_search( $control ) {
		if ( false !== $control->manager->get_setting( 'articlewave_enable_searchbar' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

endif;

if ( ! function_exists( 'articlewave_has_enable_slider' ) ) :

	/**
	 * check the setting slider is enable or not.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_has_enable_slider( $control ) {
		if ( false !== $control->manager->get_setting( 'articlewave_enable_slider' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

endif;

if ( ! function_exists( 'articlewave_has_enable_tabbed_title_field' ) ) :

	/**
	 * check the setting tab title is enable or not.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_has_enable_tabbed_title_field( $control ) {
		if ( false !== $control->manager->get_setting( 'articlewave_enable_tabbed_title' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

endif;

if ( ! function_exists( 'articlewave_has_enable_tabbed_recent_field' ) ) :

	/**
	 * check the setting tabbed is enable or not.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_has_enable_tabbed_recent_field( $control ) {
		if ( false !== $control->manager->get_setting( 'articlewave_enable_tabbed_recent' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

endif;

if ( ! function_exists( 'articlewave_front_display_has_grid' ) ) :

    /**
     * Check if boxed option is selected or not in site container layout.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function articlewave_front_display_has_grid( $control ) {
        if ( 'grid' == $control->manager->get_setting( 'articlewave_front_post_display' )->value() ) {
            return true;
        } else {
            return false;
        }
    }

endif;


if ( ! function_exists( 'articlewave_archive_display_has_grid' ) ) :

    /**
     * Check if boxed option is selected or not in site container layout.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function articlewave_archive_display_has_grid( $control ) {
        if ( 'grid' == $control->manager->get_setting( 'articlewave_archive_post_display' )->value() ) {
            return true;
        } else {
            return false;
        }
    }

endif;

if ( ! function_exists( 'articlewave_has_enable_widget_area_layout' ) ) :

	/**
	 * check the setting widget is enable or not.
	 * 
	 * @since 1.0.0
	 */
	function articlewave_has_enable_widget_area_layout( $control ) {
		if ( false !== $control->manager->get_setting( 'articlewave_enable_widget_area' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

endif;

if ( ! function_exists( 'articlewave_has_site_mode_switcher_enable_active_callback' ) ) :

    /**
	 * Check site mode switcher has enabled or not.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
    function articlewave_has_site_mode_switcher_enable_active_callback( $control ) {
        if (  true !== $control->manager->get_setting( 'articlewave_site_mode_switcher_option' )->value()  ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;