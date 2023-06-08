<?php
/**
 * File: class-wpglobus-elementor.php
 *
 * @since        2.2.31 We are providing support for `External File` only. @see elementor\core\files\css\base.php::use_external_file().
 * @since        2.4.12 Disable elementor support for post, that doesn't use elementor builder.
 *             Add submit box switcher to ON/OFF elementor's support.
 * @since        2.10.5 Update language switcher.
 *
 * @package      WPGlobus\Builders\Elementor
 * Author  Alex Gor(alexgff)
 */

use Elementor\Core\Files\Manager;

if ( file_exists( WP_PLUGIN_DIR . '/elementor/core/files/manager.php' ) ) {
	require_once WP_PLUGIN_DIR . '/elementor/core/files/manager.php';
}

if ( ! class_exists( 'WPGlobus_Elementor' ) ) :

	/**
	 * Class WPGlobus_Elementor.
	 */
	class WPGlobus_Elementor extends WPGlobus_Builder {

		protected $base_redirect_url = '';

		protected $post_content = null;

		/**
		 * Var
		 *
		 * @since 2.1.15
		 */
		protected static $post_css_meta_key = null;

		/**
		 * Var
		 *
		 * @since 2.1.15
		 */
		protected static $elementor_data_meta_key = null;

		/**
		 * Var
		 *
		 * @since 2.4.12
		 */
		protected static $elementor_edit_mode_meta_key = null;

		/**
		 * Var
		 *
		 * @since 2.4.12
		 */
		protected static $post_elementor_support_meta_key = null;

		/**
		 * Var
		 *
		 * @since 2.4.12
		 */
		protected static $post_elementor_support_get_key = 'wpglobus-elementor-support';

		/**
		 * Var
		 *
		 * @since 2.4.12
		 */
		protected static $post_elementor_support = null;

		/**
		 * Constructor.
		 */
		public function __construct() {

			parent::__construct( 'elementor' );

			$_post_css_meta_key = WPGlobus::Config()->builder->get( 'post_css_meta_key' );
			if ( ! empty( $_post_css_meta_key ) ) {
				self::$post_css_meta_key = $_post_css_meta_key;
			}

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			$_post_support_meta_key = WPGlobus::Config()->builder->get( 'post_support_meta_key' );
			if ( ! empty( $_post_support_meta_key ) ) {
				self::$post_elementor_support_meta_key = $_post_support_meta_key;
			}

			$_elementor_data_meta_key = WPGlobus::Config()->builder->get( 'elementor_data_meta_key' );
			if ( ! empty( $_elementor_data_meta_key ) ) {
				self::$elementor_data_meta_key = $_elementor_data_meta_key;
			}

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			$_elementor_edit_mode_meta_key = WPGlobus::Config()->builder->get( 'elementor_edit_mode_meta_key' );
			if ( ! empty( $_elementor_edit_mode_meta_key ) ) {
				self::$elementor_edit_mode_meta_key = $_elementor_edit_mode_meta_key;
			}

			if ( 'elementor' === WPGlobus_WP::get_http_get_parameter( 'action' ) ) {
				/**
				 * See wp-includes/revision.php
				 */
				$post_id = WPGlobus_WP::get_http_get_parameter( 'post' );
				if ( (int) $post_id > 0 ) {
					$revision = wp_get_post_autosave( $post_id );
					if ( is_object( $revision ) ) {
						wp_delete_post_revision( $revision->ID );
					}
				}
			}

			/**
			 * See   wpglobus\includes\class-wpglobus.php
			 *
			 * @since 2.4.12
			 */
			add_action( 'wpglobus_submitbox_action', array( $this, 'on__submitbox_switcher' ) );

			/**
			 * See_file  wpglobus\includes\class-wpglobus.php
			 *
			 * @todo      remove after test.
			 */
			remove_action( 'wp_insert_post_data', array( 'WPGlobus', 'on_save_post_data' ), 10 );

			add_filter( 'get_post_metadata', array( $this, 'filter__post_metadata' ), 13, 4 );

			/**
			 * Todo may be need this filter for admin
			 * See includes\builders\elementor\class-wpglobus-elementor-front.php
			 * add_filter( 'update_post_metadata', array( $this, 'filter__update_metadata' ), 5, 5 );
			 */

			/**
			 * Elementor editor footer.
			 *
			 * See_file elementor\includes\editor.php
			 */
			add_action( 'elementor/editor/footer', array( $this, 'on__elementor_footer' ), 100 );

			/**
			 * W.I.P
			 * See   meta classic-editor-remember = block-editor OR classic-editor
			 *
			 * @since 2.2.11
			 * @todo  maybe to use `wp_footer` action instead of `elementor/editor/footer`.
			 * //add_action( 'wp_footer', array( $this, 'on__elementor_footer' ), 100 );
			 */

			/**
			 * W.I.P
			 *
			 * @since 2.2.11
			 * @todo  maybe useful
			 * //add_filter( 'elementor/editor/localize_settings', array( $this, 'on__localize_settings' ), 10, 2 );
			 * //add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'on__localize_settings' ) );
			 */

			/**
			 * AJAX handling.
			 */
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				if ( 'elementor_ajax' === WPGlobus_WP::get_http_post_parameter( 'action' ) && false !== strpos( WPGlobus_WP::get_http_post_parameter( 'actions' ), '"action":"save_builder"' ) ) {
					if ( class_exists( '\Elementor\Core\Files\Manager' ) ) {
						/**
						 * Clear Elementor cache and WPGlobus css meta.
						 *
						 * @since 2.1.15
						 */
						$_fm = new Manager();
						$_fm->clear_cache();
						if ( ! is_null( self::$post_css_meta_key ) ) {
							update_post_meta( WPGlobus::Config()->builder->get( 'post_id' ), self::$post_css_meta_key, '' );
						}
					}
				}
			}

			if ( is_admin() ) {

				/**
				 * Updated
				 *
				 * @since 2.2.31
				 */
				add_action( 'admin_notices', array( $this, 'on__admin_notice' ) );

				add_filter( 'the_post', array( $this, 'filter__the_post' ), 5 );

				/**
				 * See_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/edit', array( $this, 'filter__url' ), 5, 2 );

				/**
				 * See_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/exit_to_dashboard', array( $this, 'filter__url' ), 5, 2 );

				/**
				 * Filter Preview Button link in elementor side panel.
				 *
				 * See_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/wp_preview', array( $this, 'filter__preview_url' ), 5, 2 );

				/**
				 * Filter for URL in elementor-preview-iframe.
				 *
				 * See_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/preview', array( $this, 'filter__preview_url' ), 5, 2 );

				/**
				 * Filters the editor localized settings.
				 *
				 * @since    2.2.6
				 *
				 * See_file elementor\includes\editor.php
				 */
				add_filter( 'elementor/editor/localize_settings', array( $this, 'filter__localize_settings' ), 5, 2 );
			}

		}

		/**
		 * Updated
		 *
		 * @since 2.4.12
		 */
		public function on__submitbox_switcher( $post ) {

			if ( ! $post instanceof WP_Post ) {
				return;
			}

			if ( 'builder' !== get_post_meta( $post->ID, $this->get_elementor_edit_mode_meta_key(), true ) ) {
				/**
				 * Disable elementor support for post, that doesn't use elementor builder.
				 */
				self::$post_elementor_support = false;

				return;
			}

			$_GET_post_elementor_support_get_key = WPGlobus_WP::get_http_get_parameter( self::$post_elementor_support_get_key );
			if ( $_GET_post_elementor_support_get_key ) {
				$current_mode = $_GET_post_elementor_support_get_key;
				if ( in_array( $current_mode, array( 'on', 'off' ), true ) ) {
					update_post_meta( $post->ID, self::$post_elementor_support_meta_key, $current_mode );
				}
			}

			$elementor_support = get_post_meta( $post->ID, self::$post_elementor_support_meta_key, true );

			if ( 'off' === $elementor_support ) {
				self::$post_elementor_support = false;
			} else {
				$elementor_support            = 'on';
				self::$post_elementor_support = true;
			}

			// "Reverse" logic here. It's the mode to turn to, not the current one.
			$switch_to_mode = 'off';
			if ( 'off' === $elementor_support ) {
				$switch_to_mode = 'on';
			}

			if ( 'off' === $elementor_support ) {
				// Translators: ON/OFF status of WPGlobus on the edit pages.
				$status_text     = __( 'OFF', 'wpglobus' );
				$toggle_text     = __( 'Turn on', 'wpglobus' );
				$highlight_class = 'wp-ui-text-notification';
			} else {
				// Translators: ON/OFF status of WPGlobus on the edit pages.
				$status_text     = __( 'ON', 'wpglobus' );
				$toggle_text     = __( 'Turn off', 'wpglobus' );
				$highlight_class = 'wp-ui-text-highlight';
			}

			$query_string = explode( '&', WPGlobus_WP::query_string() );

			foreach ( $query_string as $key => $_q ) {
				if ( false !== strpos( $_q, 'wpglobus=' ) ) {
					unset( $query_string[ $key ] );
				}
			}

			$query = implode( '&', $query_string );

			$url = admin_url(
				add_query_arg(
					array( self::$post_elementor_support_get_key => $switch_to_mode ),
					'post.php?' . $query
				)
			);

			?>
			<div class="misc-pub-section wpglobus-elementor-support-switch">
				<span id="wpglobus-elementor-support-raw" style="margin-right: 2px;"
						class="dashicons dashicons-admin-site <?php echo esc_attr( $highlight_class ); ?>"></span>
				<?php esc_html_e( 'Elementor', 'wpglobus' ); ?>:
				<strong class="<?php echo esc_attr( $highlight_class ); ?>"><?php echo esc_html( $status_text ); ?></strong>
				<a class="button button-small" style="margin:-3px 0 0 3px;"
						href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $toggle_text ); ?></a>
			</div>
			<?php
		}

		/**
		 * Updated
		 *
		 * @since 2.4.12
		 */
		public function is_elementor_support() {

			if ( is_null( self::$post_elementor_support ) ) {

				global $post;

				$elementor_support = get_post_meta( $post->ID, self::$post_elementor_support_meta_key, true );

				if ( 'off' === $elementor_support ) {
					self::$post_elementor_support = false;
				} else {
					self::$post_elementor_support = true;
				}
			}

			if ( self::$post_elementor_support ) {
				return true;
			}

			return false;
		}

		/**
		 * Updated
		 *
		 * @since 2.4.12
		 */
		public function get_elementor_edit_mode_meta_key() {
			return self::$elementor_edit_mode_meta_key;
		}

		/**
		 * Localize editor settings.
		 *
		 * Filters the editor localized settings.
		 *
		 * @since 2.2.6
		 *
		 * @param array $localized_settings Localized settings.
		 * @param int   $post_id            The ID of the current post being edited.
		 *
		 * @return array
		 */
		public function filter__localize_settings( $localized_settings, $post_id ) {

			if ( WPGlobus::Config()->builder->is_default_language() ) {
				return $localized_settings;
			}

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			if ( ! $this->is_elementor_support() ) {
				return $localized_settings;
			}

			/**
			 * Updated
			 *
			 * @since 2.2.31
			 */
			if ( 'external' !== WPGlobus::Config()->builder->get( 'elementor_css_print_method' ) ) {
				return $localized_settings;
			}

			$url = get_permalink( $post_id );

			$localized_settings['document']['urls']['permalink'] = WPGlobus_Utils::localize_url( $url, WPGlobus::Config()->builder->get_language() );

			return $localized_settings;
		}

		/**
		 * To avoid output content with language marks from $post->post_content field on elementor builder page
		 * if "_elementor_data" meta has not content in extra language.
		 *
		 * @param WP_Post $object
		 *
		 * @return WP_Post
		 */
		public function filter__the_post( $object ) {

			if ( 'post.php' !== WPGlobus::Config()->builder->get( 'pagenow' ) ) {
				return $object;
			}

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			if ( ! $this->is_elementor_support() ) {
				return $object;
			}

			if ( is_null( $this->post_content ) ) {
				$this->post_content = $object->post_content;
			}

			$_post               = clone( $object );
			$_post->post_content = WPGlobus_Core::text_filter( $this->post_content, WPGlobus::Config()->builder->get_language(), WPGlobus::RETURN_EMPTY );

			/**
			 * See \wp-includes\cache.php
			 */
			wp_cache_replace( $object->ID, $_post, 'posts' );

			return $object;
		}

		/**
		 * Todo W.I.P
		 *
		 * @noinspection PhpUnusedParameterInspection
		 * @noinspection PhpUnused
		 */
		public static function filter__update_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
			if ( '_elementor_css' !== $meta_key ) {
				return $check;
			}

			return $check;
		}

		/**
		 * Get meta callback.
		 *
		 * @param $check
		 * @param $object_id
		 * @param $meta_key
		 * @param $single
		 *
		 * @return string
		 * @noinspection PhpUnusedParameterInspection
		 */
		public static function filter__post_metadata( $check, $object_id, $meta_key, $single ) {

			if ( self::$elementor_data_meta_key === $meta_key ) {

				$meta_cache = wp_cache_get( $object_id, 'post_meta' );

				if ( is_admin() ) {

					/**
					 * TODO Duplicate
					 *
					 * @noinspection DuplicatedCode
					 */
					if ( isset( $meta_cache[ $meta_key ] ) && isset( $meta_cache[ $meta_key ][0] ) ) {

						if ( WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {
							$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->builder->get_language() );
						} else {
							$_value = $meta_cache[ $meta_key ][0];
						}

						return $_value;

					}
				} else {

					/**
					 * Scope front.
					 *
					 * @noinspection DuplicatedCode
					 */
					if ( isset( $meta_cache[ $meta_key ] ) && isset( $meta_cache[ $meta_key ][0] ) ) {

						if ( WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {

							/**
							 * Test
							 * $_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->builder->get_language(), WPGlobus::RETURN_EMPTY );
							 */

							/**
							 * We can get current language from WPGlobus::Config().
							 *
							 * @todo just for testing purposes.
							 * //$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->language );
							 */

							$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->builder->get_language() );
						} else {
							$_value = $meta_cache[ $meta_key ][0];
						}

						return $_value;
					}
				}
			}

			return $check;

		}

		/**
		 * Elementor editor footer.
		 *
		 * Fires on Elementor editor before closing the body tag.
		 * Used to prints scripts or any other HTML before closing the body tag.
		 */
		public function on__elementor_footer() {

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			if ( ! $this->is_elementor_support() ) {
				return;
			}

			/**
			 * Updated
			 *
			 * @since 2.2.31
			 */
			if ( 'external' !== WPGlobus::Config()->builder->get( 'elementor_css_print_method' ) ) {
				return;
			}

			$this->base_redirect_url = str_replace( array( '&language=' . WPGlobus::Config()->builder->get_language() ), '', $this->base_redirect_url );
			$this->base_redirect_url = str_replace( '&action=edit', '&action=elementor', $this->base_redirect_url );
			?>
			<div id="wpglobus-elementor-wrapper">
				<div class="elementor-panel-menu-item" id="wpglobus-elementor-panel-menu-item" style="cursor:auto;">
					<div class="elementor-panel-menu-item-icon">
						<i class="eicon-globe"></i>
					</div>
					<div class="elementor-panel-menu-item-title" id="wpglobus-elementor-selector-box"
							style="padding-top:0;">
						<span id="wpglobus-elementor-selector-title"
								style="cursor:pointer;color:#6d7882;"><?php esc_html_e( 'WPGlobus languages', 'wpglobus' ); ?></span>
						<ul id="wpglobus-elementor-selector" style="display:none;margin:10px" class="hidden">
							<?php
							foreach ( WPGlobus::Config()->enabled_languages as $language ) {
								$_current = '';
								if ( WPGlobus::Config()->builder->get_language() === $language ) {
									$_current = __( 'current', 'wpglobus' );
									$_current = ' - ' . $_current;
								}
								?>
								<li style="margin-bottom:10px;cursor:auto;">
									<a href="<?php echo esc_url( $this->base_redirect_url . '&language=' . $language ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] . " ($language)" . $_current ); ?></a>
								</li>
								<?php
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<?php // phpcs:disable ?>
			<script type='text/javascript'>
				/* <![CDATA[ */
				var WPGlobusIntervalID;
				WPGlobusIntervalID = setInterval(function () {
					if (jQuery("#elementor-panel-header-menu-button").length === 0 || 'undefined' === typeof elementor.config.version) {
						return;
					}
					clearInterval(WPGlobusIntervalID);
					var wpglobusElementorPanelMenu = jQuery("#wpglobus-elementor-wrapper").html();
					jQuery(document).on('click', "#elementor-panel-header-menu-button", function () {
						if (elementor.config.version[0] === '3') {
							var elems = [".elementor-panel-menu-item-exit-to-dashboard", ".elementor-panel-menu-item-exit"];
							jQuery.each(elems, function (i, elem) {
								var $item = jQuery(elem);
								if ($item.length === 1) {
									$item.before(wpglobusElementorPanelMenu);
									return false;
								}
							});
						} else {
							jQuery(".elementor-panel-menu-item").eq(7).after(wpglobusElementorPanelMenu);
						}
					});
					jQuery(document).on('click', "#wpglobus-elementor-selector-title", function () {
						var $t = jQuery("#wpglobus-elementor-selector");
						$t.toggleClass('hidden');
						if ($t.hasClass('hidden')) {
							$t.css({'display': 'none'});
							jQuery('#wpglobus-elementor-selector-box').css({'padding-top': '0'});
						} else {
							jQuery('#wpglobus-elementor-selector-box').css({'padding-top': '10px'});
							$t.css({'display': 'block'});
						}
					});
				}, 500);
				/* ]]> */
			</script>
			<?php // phpcs:enable ?>
			<?php
		}

		/**
		 * Document edit url.
		 *
		 * Filters the document edit url.
		 *
		 * @param string $url      The edit url.
		 * @param mixed  $instance The document instance.
		 *
		 * @return string
		 */
		public function filter__url(
			$url,
			/**
			 * Unused.
			 *
			 * @noinspection PhpUnusedParameterInspection
			 */
			$instance
		) {

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			if ( ! $this->is_elementor_support() ) {
				return $url;
			}

			if ( 'external' === WPGlobus::Config()->builder->get( 'elementor_css_print_method' ) ) {
				if ( false === strpos( $url, 'language' ) ) {
					$url = $url . '&language=' . WPGlobus::Config()->builder->get_language();
				}
			} else {
				if ( false === strpos( $url, 'language' ) ) {
					$url = $url . '&language=' . WPGlobus::Config()->default_language;
				}
			}

			$this->base_redirect_url = $url;

			return $url;
		}

		/**
		 * Document "WordPress preview" URL.
		 *
		 * Filters the WordPress preview URL.
		 *
		 * @param string $url      WordPress preview URL.
		 * @param mixed  $instance The document instance.
		 *
		 * @return string
		 */
		public function filter__preview_url(
			$url,
			/**
			 * Unused.
			 *
			 * @noinspection PhpUnusedParameterInspection
			 */
			$instance
		) {

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			if ( ! $this->is_elementor_support() ) {
				return $url;
			}

			/**
			 * Updated
			 *
			 * @since 2.2.31
			 */
			if ( 'external' !== WPGlobus::Config()->builder->get( 'elementor_css_print_method' ) ) {
				return $url;
			}

			return WPGlobus_Utils::localize_url( $url, WPGlobus::Config()->builder->get_language() );
		}

		/**
		 * Prints admin screen notices.
		 *
		 * @since 2.2.31
		 */
		public function on__admin_notice() {

			if ( 'post.php' !== WPGlobus::Config()->builder->get( 'pagenow' ) ) {
				return;
			}

			/**
			 * Updated
			 *
			 * @since 2.4.12
			 */
			if ( ! $this->is_elementor_support() ) {
				return;
			}

			if ( 'external' === WPGlobus::Config()->builder->get( 'elementor_css_print_method' ) ) {
				return;
			}

			$_url = add_query_arg(
				array(
					'page' => 'elementor#tab-advanced',
				),
				admin_url( 'admin.php' )
			);

			echo '<div class="notice error"><p>';
			printf(
			// Translators:
				esc_html__( 'WPGlobus provides multilingual support for Elementor only when the option %1$s%2$s%3$s is set to %4$s.', 'wpglobus' ),
				'<a href="' . esc_url( $_url ) . '" target="_blank">',
				'<strong>CSS Print Method</strong>',
				'</a>',
				'<strong>External File</strong>'
			);
			echo '</p></div>';
		}
	}

endif;
