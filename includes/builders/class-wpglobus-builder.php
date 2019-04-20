<?php
/**
 * File: class-wpglobus-builder.php
 *
 * @package WPGlobus\Builders
 * @author  Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Builder.
 *
 * @since 1.9.17
 */
if ( ! class_exists( 'WPGlobus_Builder' ) ) :

	class WPGlobus_Builder {

		/**
		 * Current language of post.
		 */
		protected $language = null;

		/**
		 * Builder ID.
		 */
		protected $id = null;

		/**
		 * Array of activated builders.
		 *
		 * @since  1.9.17
		 * @access protected
		 * @var    array
		 */
		// phpcs:ignore
		//protected $builders = array(); TODO remove

		/**
		 * @var array
		 * @todo Unused?
		 */
		protected $builder_post = null;

		/**
		 * Constructor method.
		 *
		 * @since  1.9.17
		 *
		 * @param string $id The Builder ID, such as 'gutenberg'.
		 */
		public function __construct( $id ) {

			$this->id = $id;

			$this->set_current_language();

			// phpcs:ignore
			// if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			/**
			 * @todo Add the handling of AJAX.
			 */
			// }

			if ( is_admin() ) {

				add_action( 'redirect_post_location', array( $this, 'on__redirect' ), 5, 2 );

				add_filter( 'admin_body_class', array( $this, 'filter__add_admin_body_class' ) );

				/**
				 * Add builder/add-on label to admin bar.
				 *
				 * @since 1.9.27
				 */
				add_action( 'admin_bar_menu', array( $this, 'on__admin_bar_menu' ), 11 );

				/**
				 * @see "{$field_no_prefix}_edit_pre" in wp-includes\post.php
				 */
				add_filter( 'content_edit_pre', array( $this, 'filter__content' ), 5, 2 );
				add_filter( 'title_edit_pre', array( $this, 'filter__title' ), 5, 2 );
				add_filter( 'excerpt_edit_pre', array( $this, 'filter__excerpt' ), 5, 2 );

			}

			/**
			 * Show language tabs in post.php page.
			 *
			 * @see_file wpglobus\includes\class-wpglobus.php
			 */
			add_filter( 'wpglobus_show_language_tabs', array( $this, 'filter__show_language_tabs' ), 5 );

		}

		/**
		 * Filter title.
		 *
		 * @param string $value   The title.
		 * @param int    $post_id Unused.
		 *
		 * @return string
		 */
		public function filter__title(
			$value, /** @noinspection PhpUnusedParameterInspection */
			$post_id
		) {
			$value = WPGlobus_Core::text_filter( $value, $this->get_current_language(), WPGlobus::RETURN_EMPTY );

			return $value;
		}

		/**
		 * Filter content.
		 *
		 * @param string $content The content.
		 * @param int    $post_id Post ID - Unused.
		 *
		 * @return string
		 */
		public function filter__content(
			$content, /** @noinspection PhpUnusedParameterInspection */
			$post_id
		) {
			$content = WPGlobus_Core::text_filter( $content, $this->get_current_language(), WPGlobus::RETURN_EMPTY );

			return $content;
		}

		/**
		 * Filter excerpt.
		 *
		 * @param string $excerpt The excerpt.
		 * @param int    $post_id Post ID - Unused.
		 *
		 * @return string
		 */
		public function filter__excerpt(
			$excerpt, /** @noinspection PhpUnusedParameterInspection */
			$post_id
		) {
			$excerpt = WPGlobus_Core::text_filter( $excerpt, $this->get_current_language(), WPGlobus::RETURN_EMPTY );

			return $excerpt;
		}

		/**
		 * Redirect.
		 *
		 * @param string $location
		 * @param int    $post_id Post ID - Unused.
		 *
		 * @return string
		 */
		public function on__redirect(
			$location, /** @noinspection PhpUnusedParameterInspection */
			$post_id
		) {
			/**
			 * Tested with:
			 * - Page Builder by SiteOrigin OK.
			 */
			return $location . '&language=' . $this->language;
		}

		/**
		 * Getter.
		 *
		 * @return null|string
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Is this a "builder" post?
		 *
		 * @return bool
		 * @todo Unused?
		 */
		public function is_builder_post() {
			if ( is_null( $this->builder_post ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Get hidden "wpglobus-language" field.
		 *
		 * @since 1.9.17
		 * @return string
		 */
		public function get_language_field() {
			/**
			 * @see  on_add_devmode_switcher() in wpglobus\includes\class-wpglobus.php
			 * @todo may be add special function to get hidden language field.
			 */
			return '<input type="hidden" id="' . esc_attr( WPGlobus::get_language_meta_key() ) . '" name="' . esc_attr( WPGlobus::get_language_meta_key() ) . '" value="' . esc_attr( $this->get_current_language() ) . '" />';
		}

		/**
		 * Return current language.
		 *
		 * @since 1.9.17
		 * @return string
		 */
		public function get_current_language() {
			return $this->language;
		}

		/**
		 * Set current language.
		 *
		 * @since 1.9.17
		 * @return void
		 */
		public function set_current_language() {

			if ( ! is_null( $this->language ) ) {
				return;
			}

			/**
			 * Don't duplicate the defining of current language.
			 * Let's just get it from WPGlobus::Config()->builder.
			 */
			$language = WPGlobus::Config()->builder->get_language();

			if ( $language ) {
				// Language was set in WPGlobus_Config_Builder class.
				$this->language = $language;
			}

			$post_id = 0;
			if ( ! empty( $_REQUEST['post'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
				$post_id = (int) $_REQUEST['post'];
			} elseif ( ! empty( $_REQUEST['id'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
				$post_id = (int) $_REQUEST['id'];
			} elseif ( ! empty( $_REQUEST['post_ID'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
				$post_id = (int) $_REQUEST['post_ID'];
			}

			if ( $post_id && ! is_null( $this->language ) ) {
				update_post_meta( $post_id, WPGlobus::Config()->builder->get_language_meta_key(), $this->language );
			}

		}

		/**
		 * Show language tabs on post.php page.
		 *
		 * @see_file includes\class-wpglobus.php
		 *
		 * @param bool $value
		 *
		 * @return bool
		 */
		public function filter__show_language_tabs(
			/** @noinspection PhpUnusedParameterInspection */
			$value
		) {

			global $pagenow;

			$classes                      = array();
			$classes['wpglobus-post-tab'] = 'wpglobus-post-tab';
			$classes['ui-state-default']  = 'ui-state-default';
			$classes['ui-corner-top']     = 'ui-corner-top';
			$classes['ui-tabs-active']    = 'ui-tabs-active';
			$classes['ui-tabs-loading']   = 'ui-tabs-loading';

			$link_style = array();
			$link_title = '';
			if ( 'post-new.php' === $pagenow ) {
				$link_style['cursor'] = 'cursor:not-allowed';
				$link_title           = esc_html__( 'Save draft before using extra language.', 'wpglobus' );
			}

			?>
			<ul class="wpglobus-post-body-tabs-list">
				<?php
				$order = 0;

				$get_array = $_GET; // phpcs:ignore WordPress.CSRF.NonceVerification
				/**
				 * Unset unneeded elements.
				 */
				unset( $get_array['language'] );
				unset( $get_array['message'] );

				foreach ( WPGlobus::Config()->open_languages as $language ) {

					$tab_suffix = WPGlobus::Config()->default_language === $language ? 'default' : $language;

					$_classes = $classes;

					$_link_style = $link_style;

					if ( 'post-new.php' === $pagenow && WPGLobus::Config()->default_language === $language ) {
						$_link_style['cursor'] = '';
					}

					if ( $language === $this->language ) {
						$_classes[] = 'ui-state-active';
					}

					$link        = add_query_arg( array_merge( $get_array, array( 'language' => $language ) ), admin_url( $pagenow ) );
					$_link_title = '';
					if ( 'post-new.php' === $pagenow && WPGLobus::Config()->default_language !== $language ) {
						$link        = '#';
						$_link_title = $link_title;
					}
					?>
					<li id="link-tab-<?php echo esc_attr( $tab_suffix ); ?>"
							data-language="<?php echo esc_attr( $language ); ?>"
							data-order="<?php echo esc_attr( $order ); ?>"
							class="<?php echo esc_attr( implode( ' ', $_classes ) ); ?>">
						<!--<a href="#tab-<?php echo esc_attr( $tab_suffix ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>-->
						<a style="<?php echo esc_attr( implode( ';', $_link_style ) ); ?>"
								title="<?php echo esc_attr( $_link_title ); ?>"
								href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>
					</li>
					<?php
					$order++;
				}
				?>
			</ul>
			<?php
			/**
			 * Return false to prevent output standard WPGlobus tabs.
			 */
			return false;
		}

		/**
		 * Add class to body in admin.
		 *
		 * @see   admin_body_class filter
		 *
		 * @since 1.9.17
		 *
		 * @param string $classes
		 *
		 * @return string
		 */
		public function filter__add_admin_body_class( $classes ) {
			return $classes . ' wpglobus-wp-admin-builder wpglobus-wp-admin-builder-' . $this->id;
		}

		/**
		 * Add builder label to admin bar.
		 *
		 * @since 1.9.27
		 *
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		public function on__admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {

			global $pagenow;

			if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'term.php' ), true ) ) {
				return;
			}

			$_builder_label = esc_html__( 'Builder', 'wpglobus' ) . ': ';
			if ( class_exists( 'WPGlobus_Builders' ) ) {
				$_builder = WPGlobus_Builders::get_addon( $this->id );

				if ( ! empty( $_builder['admin_bar_label'] ) ) {
					$_builder_label = $_builder['admin_bar_label'] . ': ';
				}
				if ( empty( $_builder['admin_bar_builder_label'] ) ) {
					$_builder_label .= $_builder['plugin_name'];
				} else {
					$_builder_label .= $_builder['admin_bar_builder_label'];
				}
			} else {
				$_builder_label .= $this->id;
			}

			$wp_admin_bar->add_menu(
				array(
					'id'     => 'wpglobus-builder-id',
					'parent' => 'top-secondary',
					'title'  => '<span class="ab-label">' . $_builder_label . '</span>',
				)
			);

			$_title = esc_html__( 'Сompatibility Settings', 'wpglobus' );

			$_url = admin_url(
				add_query_arg(
					array(
						'page' => 'wpglobus_options',
						'tab'  => 'compatibility',
					),
					'admin.php'
				)
			);

			$wp_admin_bar->add_menu(
				array(
					'parent' => 'wpglobus-builder-id',
					'id'     => 'wpglobus-builder-compatibility-link',
					'title'  => '<span>' . $_title . '</span>',
					'href'   => $_url,
					'meta'   => array(
						'_target'  => 'blank',
						'tabindex' => -1,
					),
				)
			);

		}

	}

endif;
