<?php
/**
 * File: class-tivwp-updater-setup-admin-area.php
 *
 * @package TIVWP_Updater
 */

/**
 * This is to avoid the PHPStorm warning about multiple Updater classes in the project.
 * Had to place it file-wide because otherwise PHPCS complains about improper class comment.
 * @noinspection RedundantSuppression
 * @noinspection PhpUndefinedClassInspection
 */

/**
 * Class TIVWP_Updater_Setup_Admin_Area
 */
class TIVWP_Updater_Setup_Admin_Area {

	/**
	 * Static constructor.
	 */
	public static function construct() {

		add_action( 'admin_head', array( __CLASS__, 'embed_css' ), 0 );

		if ( version_compare( $GLOBALS['wp_version'], '4.5', '>' ) ) {
			add_action( 'admin_footer', array( __CLASS__, 'embed_js' ), PHP_INT_MAX );
		}

		self::load_translations();

	}

	/**
	 * Embed the stylesheet.
	 */
	public static function embed_css() {

		echo '<style id="tivwp-updater-css">';
		include __DIR__ . '/../assets/css/tivwp-updater.css';
		echo '</style>';
	}

	/**
	 * In WP 4.6, form submission requires at least one checkbox checked.
	 * See `wp-admin/js/updates.js`
	 *
	 * @since 1.0.2
	 */
	public static function embed_js() {
		echo '<script id="tivwp-updater-js">';
		include __DIR__ . '/../assets/js/tivwp-updater.js';
		echo '</script>';
	}

	/**
	 * Load translations.
	 * Similar to the core function:
	 *
	 * @see load_plugin_textdomain
	 */
	protected static function load_translations() {

		$domain = 'tivwp-updater';

		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			$locale = is_admin() ? get_user_locale() : get_locale();
		}
		$locale = apply_filters( 'plugin_locale', $locale, $domain );

		$mofile_name = $domain . '-' . $locale . '.mo';

		// Try to load from the languages directory first.
		if ( defined( 'WP_LANG_DIR' ) ) {
			$mofile = WP_LANG_DIR . '/' . $mofile_name;
			if ( load_textdomain( $domain, $mofile ) ) {
				return;
			}
		}

		// Then try to load from our languages folder.
		$mofile = __DIR__ . '/../languages/' . $mofile_name;
		load_textdomain( $domain, $mofile );

	}
}
