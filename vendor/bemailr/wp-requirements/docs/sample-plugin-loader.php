<?php
/**
 * File: sample-plugin-loader.php
 *
 * @package WP-Requirements
 */

/**
 * Here goes the standard WordPress plugin header (name, URI, description and so on).
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WP Requirements library should be used in admin area only.
if ( ! defined( 'DOING_AJAX' ) && is_admin() ) {
	// The `wpr-loader` script is responsible for loading the latest version of the library.
	require_once dirname( __FILE__ ) . '/vendor/bemailr/wp-requirements/wpr-loader.php';
}

/**
 * The plugin loader.
 */
function wp_requirements_example_plugin_loader() {

	if ( class_exists( 'WP_Requirements' ) ) :

		$my_requirements = array(
			'php'       => array(
				'version'    => '5.3',
				'extensions' => array( 'mbstring', 'curl' ),
			),
			'mysql'     => array( 'version' => '5.6' ),
			'wordpress' => array(
				'version' => '4.6',
				'plugins' => array(
					'wpglobus/wpglobus.php'           => '1.6.1',
					'wpglobus-plus/wpglobus-plus.php' => true,
				),
				'theme'   => array( 'my-theme' => '1.5' ),
			),
			'params'    => array(
				'requirements_details_url' => '//google.com',
				'version_compare_operator' => '>=',
				'not_valid_actions'        => array( 'deactivate', 'admin_notice' ),
				'show_valid_results'       => true,
			),
		);

		// If the second parameter is omitted, will look for a `wp-requirements.json` file.
		$requirements = new WP_Requirements( __FILE__, $my_requirements );

		if ( ! $requirements->valid() ) {
			$requirements->process_failure();

			return;
		}
	endif;

}

add_action( 'init', 'wp_requirements_example_plugin_loader' );

/*EOF*/
