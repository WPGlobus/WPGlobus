<?php
/**
 * File: updater.php
 *
 * Client library providing API calls to the WooCommerce API Manager and a user interface for
 * plugin license management.
 *
 * @link      https://woocommerce.com/products/woocommerce-api-manager/
 *
 * @package   TIVWP_Updater
 * @author    WPGlobus
 * @copyright Copyright 2018 TIV.NET INC. and Gregory Karpinsky
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_admin() ) {
	add_action( 'plugins_loaded',
		/**
		 * The Updater "Factory".
		 */
		function () {
			/**
			 * Bail out it:
			 * - Already loaded.
			 * - WP is old (we do not test and do not want to support older WP).
			 * - Multisite (we do not support it in general).
			 * Additional checks are done for specific plugins,
			 * @see \TIVWP_Updater::__construct
			 */
			if (
				defined( 'TIVWP_UPDATER_VERSION' )
				|| is_multisite()
				|| version_compare( $GLOBALS['wp_version'], '4.5', '<' )
			) {
				return;
			}

			/**
			 * Load the class and tell others that it's done.
			 */
			require_once dirname( __FILE__ ) . '/class-tivwp-updater.php';
			define( 'TIVWP_UPDATER_VERSION', '1.0.9' );

			if ( isset( $GLOBALS['pagenow'] ) && 'plugins.php' === $GLOBALS['pagenow'] ) {
				require_once dirname( __FILE__ ) .
				             '/includes/class-tivwp-updater-setup-admin-area.php';
				/* @noinspection PhpUndefinedClassInspection */
				TIVWP_Updater_Setup_Admin_Area::construct();
			}

			/**
			 * Let everyone create the uploader objects.
			 */
			do_action( 'tivwp_updater_factory' );
		}
		// TODO: Increase the "minus" part with every new release.
		, 9999 - 10
	);
}

/*EOF*/
