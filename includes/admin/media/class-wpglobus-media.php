<?php
/**
 * @package WPGlobus
 */

/**
 * Class WPGlobus_Media.
 *
 * @since 1.7.3
 */
if ( ! class_exists( 'WPGlobus_Media' ) ) :

	class WPGlobus_Media {

		/**
		 * Instance.
		 */
		protected static $instance;

		/**
		 * Post types to work on media page.
		 */
		protected $enabled_post_types = array();

		/**
		 * Get instance.
		 */
		public static function get_instance(){
			if( null == self::$instance ){
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->enabled_post_types[] = 'attachment';

			/**
			 * @scope admin
			 * @since 1.7.3
			 */
			add_action( 'edit_form_after_editor', array(
				$this,
				'language_tabs'
			) );

			/**
			 * @scope admin
			 * @since 1.7.3
			 */
			add_action( 'admin_print_scripts', array(
				$this,
				'media__admin_scripts'
			) );

			/**
			 * @scope admin
			 * @since 1.7.3
			 */
			add_action( 'admin_print_scripts', array(
				$this,
				'post_php__admin_scripts'
			), 5 );

			/**
			 * @scope admin
			 * @since 1.7.3
			 */
			add_action( 'admin_print_styles', array(
				$this,
				'action__admin_styles'
			) );

			/**
			 * @scope admin
			 * @see filter 'media_send_to_editor' in media.php
			 * @since 1.7.3
			 */
			add_filter( 'media_send_to_editor', array(
				$this,
				'filter__media_send_to_editor'
			), 5, 3 );


		}

		/**
		 * Check for enabled post types.
		 *
		 * @scope  admin
		 * @since  1.7.3
		 *
		 * @param string $html       HTML.
		 * @param int    $id         Unused.
		 * @param array  $attachment Attachment.
		 *
		 * @return boolean
		 */
		public function filter__media_send_to_editor( $html, $id, $attachment ) {

			$fields = array(
				'post_content',
				'post_excerpt',
				'image_alt',
			);

			$current_language = WPGlobus::Config()->default_language;
			if ( ! empty( $_POST['wpglobusLanguageTab'] ) ) { // WPCS: input var ok, sanitization ok.
				/**
				 * See wpglobus-media.js
				 */
				$current_language = sanitize_text_field( wp_unslash( $_POST['wpglobusLanguageTab'] ) ); // WPCS: input var ok, sanitization ok.

				if ( ! in_array( $current_language, WPGlobus::Config()->enabled_languages, true ) ) {
					return $html;
				}
			}

			foreach ( $fields as $field ) {
				if ( ! empty( $attachment[ $field ] ) && WPGlobus_Core::has_translations( $attachment[ $field ] ) ) {
					$html = str_replace( $attachment[ $field ], WPGlobus_Core::text_filter( $attachment[ $field ], $current_language ), $html );
				}
			}

			return $html;
		}

		/**
		 * Check for enabled post types.
		 *
		 * @scope  admin
		 * @since  1.7.3
		 * @access public
		 *
		 * @return boolean
		 */
		public function is_enabled() {

			global $post;

			if ( empty( $post ) ) {
				return false;
			}

			if ( in_array( $post->post_type, $this->enabled_post_types ) ) {
				return true;
			}

			return false;

		}

		/**
		 * Enqueue admin scripts on post.php page.
		 *
		 * @scope  admin
		 * @since  1.7.3
		 * @access public
		 *
		 * @return void
		 */
		public function post_php__admin_scripts() {

			global $post;

			if ( empty( $post ) ) {
				return;
			}

			if ( in_array( $post->post_type, array( 'attachment' ) ) ) {
				/**
				 * Don't load on edit media page.
				 */
				return;
			}

			wp_register_script(
				'wpglobus-media-post-php',
				WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-media-post-php' . WPGlobus::SCRIPT_SUFFIX() . '.js',
				array(),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-media-post-php' );

		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @scope  admin
		 * @since  1.7.3
		 * @access public
		 *
		 * @return void
		 */
		public function media__admin_scripts() {

			if ( ! $this->is_enabled() ) {
				return;
			}

			/**
			 * WordPress 4.7+ needs a new version of our admin JS.
			 * @since 1.7.0
			 */
			$version = '';
			if ( version_compare( $GLOBALS['wp_version'], '4.6.999', '>' ) ) {
				$version = '-47';
			}

			wp_register_script(
				'wpglobus-admin',
				WPGlobus::$PLUGIN_DIR_URL . "includes/js/wpglobus-admin$version" . WPGlobus::SCRIPT_SUFFIX() . ".js",
				array( 'jquery', 'jquery-ui-dialog', 'jquery-ui-tabs' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-admin' );
			wp_localize_script(
				'wpglobus-admin',
				'WPGlobusAdmin',
				array(
					'version'	=> WPGLOBUS_VERSION,
					'i18n'      => array(),
					'data' 		=> array(
						'default_language' => WPGlobus::Config()->default_language
					)
				)
			);

			wp_localize_script(
				'wpglobus-admin',
				'WPGlobusCoreData',
				array(
					'multisite'			=> 'false',
					'default_language' 	=> WPGlobus::Config()->default_language,
					'enabled_languages' => WPGlobus::Config()->enabled_languages,
					'locale_tag_start'  => WPGlobus::LOCALE_TAG_START,
					'locale_tag_end'    => WPGlobus::LOCALE_TAG_END
				)
			);

			wp_register_script(
				'wpglobus-media',
				WPGlobus::$PLUGIN_DIR_URL . "includes/js/wpglobus-media" . WPGlobus::SCRIPT_SUFFIX() . ".js",
				array( 'jquery', 'wpglobus-admin' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-media' );
			wp_localize_script(
				'wpglobus-media',
				'WPGlobusMedia',
				array(
					'version'			=> WPGLOBUS_VERSION,
					'language'  		=> WPGlobus::Config()->default_language,
					'defaultLanguage'  	=> WPGlobus::Config()->default_language,
					'enabledLanguages' 	=> WPGlobus::Config()->enabled_languages,
					'attachment' 		=> array(
						'caption' 		=> 'attachment_caption',
						'alt' 			=> 'attachment_alt',
						'description' 	=> 'attachment_content',
						'title'			=> 'title'
					)
				)
			);
		}

		/**
		 * Enqueue admin styles.
		 *
		 * @scope  admin
		 * @since  1.7.3
		 * @access public
		 *
		 * @return void
		 */
		public function action__admin_styles() {

			if ( ! $this->is_enabled() ) {
				return;
			}

			wp_register_style(
				'wpglobus-admin-tabs',
				WPGlobus::$PLUGIN_DIR_URL . 'includes/css/wpglobus-admin-tabs.css',
				array(),
				WPGLOBUS_VERSION,
				'all'
			);
			wp_enqueue_style( 'wpglobus-admin-tabs' );

		}

		/**
		 * Add language tabs on media page.
		 *
		 * @scope  admin
		 * @since  1.7.3
		 * @access public
		 *
		 * @return void
		 */
		public function language_tabs() {

			if ( ! $this->is_enabled() ) {
				return;
			}

			?>
			<div id="wpglobus-media-body-tabs" style="margin-top:20px;" class="wpglobus-post-body-tabs">
				<ul class="wpglobus-post-body-tabs-list">    <?php
					$order = 0;
					foreach ( WPGlobus::Config()->open_languages as $language ) {
						$tab_suffix = $language == WPGlobus::Config()->default_language ? 'default' : $language; ?>
						<li id="link-tab-<?php echo esc_attr( $tab_suffix ); ?>" data-language="<?php echo esc_attr( $language ); ?>"
							data-order="<?php echo esc_attr( $order ); ?>"
							class="wpglobus-post-tab">
							<a href="#tab-<?php echo esc_attr( $tab_suffix ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>
						</li> <?php
						$order ++;
					} ?>
				</ul> <?php
				foreach ( WPGlobus::Config()->open_languages as $language ) {
					$tab_suffix = $language == WPGlobus::Config()->default_language ? 'default' : $language; ?>
					<div id="tab-<?php echo esc_attr( $tab_suffix ); ?>" style="display:none;"></div>	<?php
				} ?>
			</div>
			<?php
		}

	}

endif;
