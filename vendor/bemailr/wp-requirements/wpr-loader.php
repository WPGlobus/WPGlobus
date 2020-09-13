<?php
/**
 * File: wpr-loader.php
 * Domain Path: /languages/
 *
 * @package WP-Requirements
 */

/**
 * Load the latest version of the class.
 *
 * The version number goes to the function name and to the action priority.
 * In the a.b.c, the "b" and "c" are padded with zeroes.
 * For example, 2.1.2 becomes 20102.
 * Convention: no 2.1.123 and no 2.1.2.1 versions.
 */

if ( ! function_exists( 'wp_requirements_class_loader_20003' ) ) :

	add_action( 'plugins_loaded', 'wp_requirements_class_loader_20003', - 20003 );

	/**
	 * Load class if not loaded already.
	 */
	function wp_requirements_class_loader_20003() {

		if ( ! class_exists( 'WP_Requirements', false ) ) {
			require_once dirname( __FILE__ ) . '/includes/class-wp-requirements.php';
		}
	}

endif;

/*EOF*/
