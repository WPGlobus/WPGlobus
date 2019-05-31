<?php
/**
 * File: class-wpglobus-elementor.php
 *
 * @package WPGlobus\Builders\Elementor
 * @author  Alex Gor(alexgff)
 */

if ( file_exists( WP_PLUGIN_DIR . '/elementor/core/files/manager.php' ) ) {
	require_once( WP_PLUGIN_DIR . '/elementor/core/files/manager.php' );
}

if ( ! class_exists( 'WPGlobus_Elementor' ) ) :

	/**
	 * Class WPGlobus_Elementor.
	 */
	class WPGlobus_Elementor extends WPGlobus_Builder {

		protected $base_redirect_url = '';

		protected $post_content = null;
		
		/**
		 * @since 2.1.15
		 */
		protected static $post_css_meta_key = null;

		/**
		 * @since 2.1.15
		 */
		protected static $elementor_data_meta_key = null;		
		
		/**
		 * Constructor.
		 */
		public function __construct() {

			parent::__construct( 'elementor' );

			$_post_css_meta_key = WPGlobus::Config()->builder->get('post_css_meta_key');
			if ( ! empty( $_post_css_meta_key ) ) {
				self::$post_css_meta_key = $_post_css_meta_key;
			}
	
			$_elementor_data_meta_key = WPGlobus::Config()->builder->get('elementor_data_meta_key');
			if ( ! empty( $_elementor_data_meta_key ) ) {
				self::$elementor_data_meta_key = $_elementor_data_meta_key;
			}
	
			if ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) { // phpcs:ignore WordPress.CSRF.NonceVerification
				/**
				 * @see wp-includes/revision.php
				 */
				$post_id = $_GET['post']; // phpcs:ignore WordPress.CSRF.NonceVerification
				if ( (int) $post_id > 0 ) {
					$revision = wp_get_post_autosave( $post_id );
					if ( is_object( $revision ) ) {
						wp_delete_post_revision( $revision->ID );
					}
				}
			}

			/**
			 * @see_file  wpglobus\includes\class-wpglobus.php
			 * @todo      remove after test.
			 */
			remove_action( 'wp_insert_post_data', array( 'WPGlobus', 'on_save_post_data' ), 10 );

			add_filter( 'get_post_metadata', array( $this, 'filter__post_metadata' ), 13, 4 );
			
			// @todo may be need this filter for admin, @see includes\builders\elementor\class-wpglobus-elementor-front.php
			//add_filter( 'update_post_metadata', array( $this, 'filter__update_metadata' ), 5, 5 );						
		
			/**
			 * Elementor editor footer.
			 *
			 * @see_file elementor\includes\editor.php
			 */
			add_action( 'elementor/editor/footer', array( $this, 'on__elementor_footer' ), 100 );
			
			/**
			 * AJAX handling.
			 */
			if ( defined('DOING_AJAX') && DOING_AJAX )  {
				if ( 'elementor_ajax' == $_POST['action'] && false !== strpos($_POST['actions'], '"action":"save_builder"') ) {
					if ( class_exists( '\Elementor\Core\Files\Manager' ) ) {
						/**
						 * Clear Elementor cache and WPGlobus css meta.
						 * @since 2.1.15
						 */
						$_fm = new \Elementor\Core\Files\Manager;
						$_fm->clear_cache();
						if ( ! is_null(self::$post_css_meta_key) ) {
							update_post_meta( WPGlobus::Config()->builder->get('post_id'), self::$post_css_meta_key, '' );
						}
					}
				}
			}
			
			if ( is_admin() ) {
			
				add_filter( 'the_post', array( $this, 'filter__the_post' ), 5 );

				/**
				 * @see_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/edit', array( $this, 'filter__url' ), 5, 2 );

				/**
				 * @see_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/exit_to_dashboard', array( $this, 'filter__url' ), 5, 2 );

				/**
				 * Filter Preview Button link in elementor side panel.
				 *
				 * @see_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/wp_preview', array( $this, 'filter__preview_url' ), 5, 2 );

				/**
				 * Filter for URL in elementor-preview-iframe.
				 *
				 * @see_file elementor\core\base\document.php
				 */
				add_filter( 'elementor/document/urls/preview', array( $this, 'filter__preview_url' ), 5, 2 );

				/**
				 * Filters the editor localized settings.
				 *
				 * @since    2.2.6
				 *
				 * @see_file elementor\includes\editor.php
				 */
				add_filter( 'elementor/editor/localize_settings', array( $this, 'filter__localize_settings' ), 5, 2 );

			}

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

			if ( is_null( $this->post_content ) ) {
				$this->post_content = $object->post_content;
			}

			$_post               = clone( $object );
			$_post->post_content = WPGlobus_Core::text_filter( $this->post_content, WPGlobus::Config()->builder->get_language(), WPGlobus::RETURN_EMPTY );

			/**
			 * @see \wp-includes\cache.php
			 */
			wp_cache_replace( $object->ID, $_post, 'posts' );

			return $object;

		}
	
		/**
		 * @todo W.I.P
		 */
		public static function filter__update_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
			if ( '_elementor_css' != $meta_key ) {
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
		 */
		public static function filter__post_metadata(
			$check, $object_id, $meta_key, /** @noinspection PhpUnusedParameterInspection */
			$single
		) {

			if ( self::$elementor_data_meta_key === $meta_key ) {

				$meta_cache = wp_cache_get( $object_id, 'post_meta' );

				if ( is_admin() ) {

					if ( isset( $meta_cache[ $meta_key ] ) && isset( $meta_cache[ $meta_key ][0] ) ) {

						$_value = '';

						if ( WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {
							$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->builder->get_language() );
						} else {
							$_value = $meta_cache[ $meta_key ][0];
						}

						return $_value;

					}
				} else {

					/**
					 * scope front.
					 */

					if ( isset( $meta_cache[ $meta_key ] ) && isset( $meta_cache[ $meta_key ][0] ) ) {

						/** @noinspection PhpUnusedLocalVariableInspection */
						$_value = '';

						if ( WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {
							//$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->builder->get_language(), WPGlobus::RETURN_EMPTY );
							/**
							 * We can get current language from WPGlobus::Config().
							 *
							 * @todo just for testing purposes.
							 */
							//$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->language );

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
			$this->base_redirect_url = str_replace( array( '&language=' . WPGlobus::Config()->builder->get_language() ), '', $this->base_redirect_url );
			$this->base_redirect_url = str_replace( '&action=edit', '&action=elementor', $this->base_redirect_url );
			?>
			<div id="wpglobus-elementor-wrapper">
				<div class="elementor-panel-menu-item" id="wpglobus-elementor-panel-menu-item" style="cursor:auto;">
					<div class="elementor-panel-menu-item-icon">
						<i class="fa fa-globe"></i>
					</div>
					<div class="elementor-panel-menu-item-title" id="wpglobus-elementor-selector-box"
							style="padding-top:0;">
						<span id="wpglobus-elementor-selector-title"
								style="cursor:pointer;"><?php esc_html_e( 'WPGlobus languages', 'wpglobus' ); ?></span>
						<ul id="wpglobus-elementor-selector" style="display:none;margin:10px;" class="hidden">
							<?php
							foreach ( WPGlobus::Config()->enabled_languages as $language ) {
								$_current = '';
								if ( $language == WPGlobus::Config()->builder->get_language() ) {
									$_current = esc_html__( 'current', 'wpglobus' );
									$_current = ' - ' . $_current;
								}
								?>
								<li style="margin-bottom:10px;cursor:auto;">
									<a href="<?php echo esc_url( $this->base_redirect_url . '&language=' . $language ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] . " ($language)" ); echo $_current; ?></a>
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
				var WPGlobusTimeID;
                WPGlobusTimeID = setInterval(function(){
					if ( jQuery("#elementor-panel-header-menu-button").length === 0 ) {
						return;
					}
					clearInterval(WPGlobusTimeID);
                    var wpglobusElementorPanelMenu = jQuery("#wpglobus-elementor-wrapper").html();
                    jQuery(document).on('click', "#elementor-panel-header-menu-button", function () {
                        jQuery(".elementor-panel-menu-item").eq(7).after(wpglobusElementorPanelMenu);
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
			if ( false === strpos( $url, 'language' ) ) {
				$url = $url . '&language=' . WPGlobus::Config()->builder->get_language();
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
			$url = WPGlobus_Utils::localize_url( $url, WPGlobus::Config()->builder->get_language() );

			return $url;
		}

	}

endif;
