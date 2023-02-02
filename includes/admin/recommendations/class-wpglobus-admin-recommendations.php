<?php
/**
 * File: class-wpglobus-admin-recommendations.php
 *
 * WPGlobus Recommendations.
 *
 * @since 1.8.7
 * @since 2.10.8 Added support WPGlobusEditPost Sidebar plugin (block editor sidebar) v.2.
 *
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
		/**
		 * Recommendations on WC Settings page.
		 *
		 * @since 2.5.21 Disabled. Needs refactoring.
		 * <code>
		 * add_filter( 'woocommerce_general_settings', array( __CLASS__, 'for_woocommerce' ) );
		 * </code>
		 */

		add_filter( 'wpglobus_edit_slug_box', array( __CLASS__, 'wpg_plus_slug' ) );
		add_action( 'admin_footer', array( __CLASS__, 'on__admin_footer' ), 1000 );
		add_action( 'wpglobus_gutenberg_metabox', array( __CLASS__, 'on__gutenberg_metabox' ) );
		add_filter(
			'plugin_action_links_' . dirname( dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) ) . '/wpglobus.php',
			array(
				__CLASS__,
				'filter__plugin_action_links',
			)
		);

		/**
		 * @since 2.5.20
		 */			
		add_action( 'admin_notices', array( __CLASS__, 'on__admin_notices' ) );

		/**
		 * @since 2.10.8
		 */		
		add_action( 'admin_print_scripts',array( __CLASS__, 'on__print_scripts' ) );
	}
	
	/**
	 * Enqueue the script for the WPGlobusEditPost Sidebar plugin (block editor sidebar) v.2.
	 *
	 * @since 2.10.8
	 */
	public static function on__print_scripts() {
		
		global $pagenow;
		
		if ( 'post.php' !== $pagenow ) {
			return;
		}
		
		if ( 'gutenberg' !== WPGlobus::Config()->builder->get_id() ) {
			return;
		}	
	
		if ( WPGlobus::use_block_editor_sidebar_v1() ) {
			return;
		}

		$content  = '';
		$link_url = '';
		$link_content = '';
		
		if ( ! is_plugin_active( 'wpglobus-plus/wpglobus-plus.php' ) ) {
			
			$content = array();
			
			$url = WPGlobus_Utils::url_wpglobus_site() . 'product/wpglobus-plus/#slug';
			$message  = esc_html__( 'Translate permalinks with our premium add-on, WPGlobus Plus!', 'wpglobus' );
			$message .= ' ';
			$link_url = esc_html( $url ) ;
			$link_content = 'Check it out  ';
			
			$content['slug'] = array(
				'message'  => $message,
				'linkUrl'  => $link_url,
				'linkContent' => $link_content,
			);
			
			$url = WPGlobus_Utils::url_wpglobus_site() . 'product/wpglobus-plus/#publish';
			$message  = esc_html__( 'With Publish module, you will be able to write a post in one language and immediately publish it, not waiting for the translation to other languages.', 'wpglobus' );
			$message .= ' ';
			$link_url = esc_html( $url ) ;
			$link_content = 'Check it out  ';

			$content['publish'] = array(
				'message'  => $message,
				'linkUrl'  => $link_url,
				'linkContent' => $link_content,
			);

		} else {

			$content = array();

			$link_url = admin_url( 'admin.php' ) . '?page=' . WPGlobusPlus::WPGLOBUS_PLUS_OPTIONS_PAGE . '&tab=modules';
			$link_content = esc_html__( 'Go to WPGlobus Plus Options page', 'wpglobus-plus' );
				
			if ( ! class_exists( 'WPGlobusPlus_Slug', false ) ) {
				
				$current_key = 'slug';
				$message 	 = esc_html__( 'To translate permalinks, please activate the module Slug.', 'wpglobus' );
				
				$content[$current_key] = array(
					'message'  => $message,
				);
			}
			
			if ( ! class_exists( 'WPGlobusPlus_Publish', false ) ) {
				
				$current_key = 'publish';
				$message 	 = esc_html__( 'To use module Publish, please activate it.', 'wpglobus' );
	
				$content[$current_key] = array(
					'message'  => $message,
				);				
			}
			
			if ( empty($content) ) {
				$content = '';
			} else {
				/**
				 * Add external link to options page.
				 */
				$content['linkToOptions']['linkContent'] = $link_content;
				$content['linkToOptions']['linkUrl']	 = $link_url;
			}
		}		
		
		wp_register_script(
			'wpglobus-edit-post-sidebar',
			WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-edit-post-sidebar' . WPGlobus::SCRIPT_SUFFIX() . '.js',
			array(),
			WPGLOBUS_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus-edit-post-sidebar' );
		wp_localize_script(
			'wpglobus-edit-post-sidebar',
			'WPGlobusEditPostSidebar',
			array(
				'version'  => WPGLOBUS_VERSION,
				'content'  => $content,
				'hideStandardMetabox' => true,
			)
		);			
	}		
	
	/**
	 * Add a link to the Recommendations tab.
	 *
	 * @since 2.2.20
	 *
	 * @param array $links array of links for the plugins, adapted when the current plugin is found.
	 *
	 * @return array
	 */	
	public static function filter__plugin_action_links( $links ) {
		
		$_url = add_query_arg( array( 'page' => WPGlobus::OPTIONS_PAGE_SLUG, 'tab' => 'recommendations' ), admin_url( 'admin.php' ) );
		
		$recommend_link = '<a style="font-weight: bold;" href="' . $_url . '">' . esc_html__( 'Go Premium' ) . '</a>';
		array_unshift( $links, $recommend_link );

		return $links;
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

		$container_start = '<p class="wpglobus-plus-slug-recommendation" style="padding:5px; font-weight: bold"><span class="dashicons dashicons-admin-site"></span> ';
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

		} else if ( ! class_exists( 'WPGlobusPlus_Slug', false ) ) {
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
	
		/**
		 * @since 2.10.8
		 */
		if ( WPGlobus::use_block_editor_sidebar_v2() ) {
			return;
		}
		
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

	/**
	 * Display an admin notice in WordPress admin area.
	 *
	 * @since 2.5.20
	 */	
	public static function on__admin_notices() {
		
		global $wp_version;
		
		/**
		 * Check for PHP version.
		 */
		if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {

			echo '<div class="notice notice-error"><p>';
			printf( // Translators: %1$s - this plugin name. %2$s - the required PHP version.
				esc_html__( 'For %1$s to work correctly, PHP version %2$s or later is required.', 'wpglobus' ) . ' ' .
				// Translators: %3$s - the current PHP version.
				esc_html__( 'The PHP version on your server is %3$s.', 'wpglobus' ),
				'<strong>WPGlobus</strong>',
				'<strong>5.6</strong>',
				'<strong>' . PHP_VERSION . '</strong>'
			);
			echo '</p></div>';
		}

		/**
		 * Check for WordPress version.
		 */
		if ( version_compare( $wp_version, '5.4.99', '<' ) ) {

			echo '<div class="notice notice-error"><p>';
			printf( // Translators: %1$s - this plugin name. %2$s - the required WordPress version.
				esc_html__( 'For %1$s to work correctly, WordPress version %2$s or later is required.', 'wpglobus' ) . ' ',
				'<strong>WPGlobus ' . esc_html( WPGLOBUS_VERSION ) . '</strong>',
				'<strong>5.5</strong>'
			);
			echo '</p></div>';
		}
	}
	
} // class WPGlobus_Admin_Recommendations.

# --- EOF
