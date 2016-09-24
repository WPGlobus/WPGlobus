<?php

/**
 * File: forapigen.php
 *
 * @package WPGlobus\Hooks
 */
class WPGlobus_Hooks {

	/**
	 * Fires to add customize settings.
	 *
	 * @since 1.4.6
	 *
	 * @param WP_Customize_Manager $wp_customize .
	 */
	public static function do_action__wpglobus_customize_register( $wp_customize ) {
		do_action( 'wpglobus_customize_register', $wp_customize );
	}

	/**
	 * Filter the array of sections.
	 *
	 * @since 1.0.11
	 *
	 * @param array $sections Array of Redux sections.
	 *
	 * @return array
	 */
	public static function apply_filters__wpglobus_option_sections( $sections ) {
		return apply_filters( 'wpglobus_option_sections', $sections );
	}
}
/* EOF */
