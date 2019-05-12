<?php
/**
 * WPGlobus Recommendations.
 *
 * @since   1.8.7
 * @package WPGlobus\Admin
 */

/**
 * Class Admin Recommendations.
 */
class WPGlobus_Admin_Recommendations {

	/**
	 * True if need to run JS.
	 *
	 * @var bool
	 */
	protected static $run_js = false;

	/**
	 * Setup actions and filters.
	 */
	public static function setup_hooks() {
		add_filter( 'woocommerce_general_settings', array( __CLASS__, 'for_woocommerce' ) );
		add_filter( 'wpglobus_edit_slug_box', array( __CLASS__, 'wpg_plus_slug' ) );
		add_action( 'admin_footer', array( __CLASS__, 'on__admin_footer' ), 1000 );
		add_action( 'wpglobus_gutenberg_metabox', array( __CLASS__, 'on__gutenberg_metabox' ) );
	}

	/**
	 * Recommendations for WooCommerce.
	 *
	 * @param array $settings Passed by WooCommerce.
	 *
	 * @return array
	 *
	 * @internal
	 */
	public static function for_woocommerce( $settings ) {
		// Ugly set of "IFs" to display heading only if needed, and only once.
		$need_to_show_wc_heading  = false;
		$need_to_recommend_wpg_wc = false;
		$need_to_recommend_wpg_mc = false;

		if ( ! is_plugin_active( 'woocommerce-wpglobus/woocommerce-wpglobus.php' ) ) {
			$need_to_show_wc_heading  = true;
			$need_to_recommend_wpg_wc = true;
		}

		if ( ! is_plugin_active( 'woocommerce-multicurrency/woocommerce-multicurrency.php' ) ) {
			$need_to_show_wc_heading  = true;
			$need_to_recommend_wpg_mc = true;
		}

		if ( $need_to_show_wc_heading ) {
			$id    = 'wpglobus-recommend-wc-heading';
			$title = '';
			$desc  =
				'<h2><span class="wp-ui-notification" style="padding:10px 20px;">' .
				'<span class="dashicons dashicons-admin-site"></span> ' .
				esc_html__( 'WPGlobus Recommends:', 'wpglobus' ) .
				'</span></h2>';

			self::add_wc_section( $settings, $id, $title, $desc );
		}

		if ( $need_to_recommend_wpg_wc ) {
			$url   = WPGlobus_Utils::url_wpglobus_site() . 'product/woocommerce-wpglobus/';
			$id    = 'wpglobus-recommend-wpg-wc';
			$title = '&bull; ' . esc_html__( 'WPGlobus for WooCommerce', 'wpglobus' );
			$desc  =
				'<p class="wp-ui-text-notification">' .
				'<strong>' .
				esc_html__( 'Translate product titles and descriptions, product categories, tags and attributes.', 'wpglobus' ) .
				'</strong>' .
				'</p>' .
				'<p>' .
				'<strong>' .
				esc_html__( 'Get it now:', 'wpglobus' ) . ' ' .
				'</strong>' .
				'<a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a>' .
				'</p>';
			self::add_wc_section( $settings, $id, $title, $desc );
		}

		if ( $need_to_recommend_wpg_mc ) {
			$url   = WPGlobus_Utils::url_wpglobus_site() . 'product/wpglobus-multi-currency/';
			$id    = 'wpglobus-recommend-wpg-mc';
			$title = '&bull; ' . __( 'WooCommerce Multi-Currency', 'wpglobus' );
			$desc  =
				'<p class="wp-ui-text-notification">' .
				'<strong>' .
				esc_html__( 'Accept multiple currencies in your online store!', 'wpglobus' ) .
				'</strong>' .
				'</p>' .
				'<p>' .
				'<strong>' .
				esc_html__( 'Check it out:', 'wpglobus' ) .
				'</strong>' .
				' ' .
				'<a href="' . $url . '">' . $url . '</a>' .
				'</p>';
			self::add_wc_section( $settings, $id, $title, $desc );
		}

		return $settings;

	}

	/**
	 * Generic WC option section consisting of one block of text only.
	 *
	 * @param array  $settings Array of WC settings, passed by reference.
	 * @param string $id       Section ID, must be unique.
	 * @param string $title    Section title, no HTML.
	 * @param string $desc     The text to display, HTML is allowed.
	 *
	 * @return void
	 */
	protected static function add_wc_section( &$settings, $id, $title, $desc ) {
		$settings[] =
			array(
				'type'  => 'title',
				'id'    => $id,
				'title' => $title,
				'desc'  => $desc,
			);

		$settings[] =
			array(
				'type' => 'sectionend',
				'id'   => $id,
			);
	}

	/**
	 * Recommend WPGlobus Plus to edit permalinks.
	 *
	 * @since 1.9.6
	 */
	public static function wpg_plus_slug() {

		global $pagenow;

		if ( 'post-new.php' === $pagenow ) {
			return;
		}

		$container_start = '<p id="wpglobus-plus-slug-recommendation" style="padding:5px; font-weight: bold"><span class="dashicons dashicons-admin-site"></span> ';
		$container_end   = '</p>';

		if ( ! is_plugin_active( 'wpglobus-plus/wpglobus-plus.php' ) ) {
			$url = WPGlobus_Utils::url_wpglobus_site() . 'product/wpglobus-plus/#slug';
			echo $container_start; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html_e( 'Translate permalinks with our premium add-on, WPGlobus Plus!', 'wpglobus' );
			echo ' ';
			esc_html_e( 'Check it out:', 'wpglobus' );
			echo ' ';
			echo '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a>';
			echo $container_end; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			self::$run_js = true;

		} elseif ( ! class_exists( 'WPGlobusPlus_Slug', false ) ) {
			$url = admin_url( 'admin.php' ) . '?page=' . WPGlobusPlus::WPGLOBUS_PLUS_OPTIONS_PAGE . '&tab=modules';
			echo $container_start; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html_e( 'To translate permalinks, please activate the module Slug.', 'wpglobus' );
			echo ' ';
			// Do not translate.
			$msg = __( 'Go to WPGlobus Plus Options page', 'wpglobus-plus' );

			echo '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $msg ) . '.</a>';
			echo $container_end; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			self::$run_js = true;

		}
	}

	/**
	 * Action wpglobus_gutenberg_metabox.
	 *
	 * @since 1.9.17
	 */
	public static function on__gutenberg_metabox() {

		if ( WPGlobus::Config()->builder->is_running() ) {
			self::wpg_plus_slug();
			self::$run_js = false;
		}

	}

	/**
	 * Action admin_footer.
	 *
	 * @since 1.9.17
	 */
	public static function on__admin_footer() {

		if ( ! self::$run_js ) {
			return;
		}

		if ( ! WPGlobus::Config()->builder->is_running() ) {
			return;
		}

		if ( WPGlobus::Config()->builder->get_language() === WPGlobus::Config()->default_language ) {
			return;
		}

		?>
		<script>
			var $edit_slug_box = jQuery('#edit-slug-box');
			$edit_slug_box.css({'display': 'none'});
			var wpglobus_slug_recomm_box = jQuery('#wpglobus-plus-slug-recommendation').remove();
			$edit_slug_box.before(wpglobus_slug_recomm_box);
		</script>
		<?php
	}

}
