<?php
/**
 * WPGlobus Post Types
 *
 * @package WPGlobus
 * @since   1.9.10
 */

/**
 * Class WPGlobus_Post_Types
 */
class WPGlobus_Post_Types {

	/**
	 * Names of the CPTs that should not be visible in the WPGlobus options panel.
	 *
	 * @var string[]
	 */
	protected static $hidden_types_main = array(
		// Built-in.
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		// Custom types that do not need WPGlobus' tabbed interface or those that we cannot handle.
		'scheduled-action',
		'wp-types-group',
		'wp-types-user-group',
		'wp-types-term-group',
		'wpcf7_contact_form',
		'tablepress_table',
		// ACF: free and pro.
		'acf',
		'acf-field',
		'acf-field-group',
	);

	/**
	 * WooCommerce types: we either force-enable them in WPG-WC or we do not need to handle them.
	 * Will hide them only if WooCommerce is active, to prevent potential conflict with other plugins
	 * that may use the same ("product") type(s).
	 *
	 * @var string[]
	 */
	protected static $hidden_types_wc = array(
		'product',
		'product_variation',
		'shop_subscription',
		'shop_coupon',
		'shop_order',
		'shop_order_refund',
	);

	/**
	 * Get hidden post types.
	 *
	 * @return string[]
	 */
	public static function hidden_types() {
		$hidden_types = self::$hidden_types_main;

		if ( class_exists( 'WooCommerce', false ) ) {
			$hidden_types = array_merge( $hidden_types, self::$hidden_types_wc );
		}

		return $hidden_types;
	}
}