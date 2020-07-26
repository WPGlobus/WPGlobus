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
		/**
		 * Built-in.
		 * @see create_initial_post_types() in wp-includes\post.php
		 */
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request', // @since 1.9.17
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
		// Gutenberg: @since 1.9.17
		'wp_block',
		// Gutenberg: @since 2.2.20
		'wp_template',
		'wp_area',
		// WPBakery PB: @since 1.9.17
		'vc4_templates',
		'vc_grid_item',
		// Elementor: @since 2.2.7
		'elementor_library',
		// NextGEN Gallery: @since 2.2.29
		'ngg_album',
		'ngg_gallery',
		'ngg_pictures',
		'lightbox_library',
		'displayed_gallery',
		'display_type',
		'gal_display_source',
		// MC4WP: Mailchimp for WordPress(https://wordpress.org/plugins/mailchimp-for-wp/) : @since 2.2.32
		'mc4wp-form',
		// Pods: https://wordpress.org/plugins/pods/ @since 2.2.34
		'_pods_template',
		'_pods_pod',
		'_pods_field',
		// Elementor Pro: @since 2.3.1
		'elementor_font',
		'elementor_icons',
		// WPForms Lite: https://wordpress.org/plugins/wpforms-lite/ @since 2.3.6
		'wpforms',
		'wpforms_log',
		// rtMedia for WordPress, BuddyPress and bbPress: https://wordpress.org/plugins/buddypress-media/ @since 2.3.12
		'rtmedia_album',
		// Divi: https://www.elegantthemes.com/gallery/divi/ @since 2.3.12
		'et_theme_builder',
		'et_template',
		'et_header_layout',
		'et_body_layout',
		'et_footer_layout',
		// GDPR Cookie Consent: https://wordpress.org/plugins/cookie-law-info/ @since 2.4.14
		'cookielawinfo',
		// Advanced Post List: https://wordpress.org/plugins/advanced-post-list/ @since 2.4.15
		'apl_post_list',
		'apl_design'
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

		/**
		 * @since 2.3.6
		 * @see https://themeforest.net/item/bodega-a-stylish-theme-for-small-businesses/10276763
		 */
		if ( defined('BODEGA_CORE_VERSION') ) {
			self::$hidden_types_main[] = 'testimonials';
			self::$hidden_types_main[] = 'slides';
			self::$hidden_types_main[] = 'carousels';
		}
		
		$hidden_types = self::$hidden_types_main;

		if ( class_exists( 'WooCommerce', false ) ) {
			$hidden_types = array_merge( $hidden_types, self::$hidden_types_wc );
		}

		/**
		 * Filter for hidden post types.
		 * @see filter `wpglobus_disabled_entities` in includes\class-wpglobus.php for admin.
		 *
		 * @since 2.3.2
		 *
		 * @param array $hidden_types Array of hidden types.
		 */
		$hidden_types = apply_filters( 'wpglobus_hidden_types', $hidden_types );

		return $hidden_types;
	}
}
