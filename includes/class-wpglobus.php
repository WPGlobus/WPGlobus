<?php
/**
 * @package WPGlobus
 */

/**
 * Class WPGlobus
 */
class WPGlobus {

	const LOCALE_TAG = '{:%s}%s{:}';
	const LOCALE_TAG_START = '{:%s}';
	const LOCALE_TAG_END = '{:}';
	const LOCALE_TAG_OPEN = '{:';
	const LOCALE_TAG_CLOSE = '}';

	const URL_WPGLOBUS_SITE = 'https://wpglobus.com/';

	/**
	 * Cookie name.
	 * @since 1.8
	 */
	const _COOKIE = 'wpglobus-language';

	/**
	 * Options page slug needed to get access to settings page
	 */
	const OPTIONS_PAGE_SLUG = 'wpglobus_options';

	/**
	 * Language edit page
	 */
	const LANGUAGE_EDIT_PAGE = 'wpglobus_language_edit';

	/**
	 * WPGlobus about page
	 */
	const PAGE_WPGLOBUS_ABOUT = 'wpglobus-about';

	/**
	 * WPGlobus clean page
	 */
	const PAGE_WPGLOBUS_CLEAN = 'wpglobus-clean';

	/**
	 * WPGlobus HelpDesk page
	 *
	 * @var string
	 * @since 1.6.5
	 */
	const PAGE_WPGLOBUS_HELPDESK = 'wpglobus-helpdesk';

	/**
	 * WPGlobus Admin Central page
	 *
	 * @var string
	 * @since 1.6.6
	 */
	const PAGE_WPGLOBUS_ADMIN_CENTRAL = 'wpglobus-admin-central';

	/**
	 * List navigation menus
	 * @var array
	 */
	public $menus = array();

	/**
	 * Initialized at plugin loader
	 * @var string
	 */
	public static $PLUGIN_DIR_PATH = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public static function plugin_dir_path() {
		return self::$PLUGIN_DIR_PATH;
	}

	/**
	 * Initialized at plugin loader
	 * @var string
	 */
	public static $PLUGIN_DIR_URL = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public static function plugin_dir_url() {
		return self::$PLUGIN_DIR_URL;
	}

	/**
	 * URL for internal images.
	 *
	 * @return string
	 */
	public static function internal_images_url() {
		return self::$PLUGIN_DIR_URL . 'includes/css/images';
	}

	/**
	 * Path to data folder.
	 *
	 * @return string
	 */
	public static function data_path() {
		return self::$PLUGIN_DIR_PATH . 'data';
	}

	/**
	 * Path to languages folder.
	 *
	 * @return string
	 * @since 1.9.6
	 */
	public static function languages_path() {
		return self::$PLUGIN_DIR_PATH . 'languages';
	}

	/**
	 * @var bool $_SCRIPT_DEBUG Internal representation of the define('SCRIPT_DEBUG')
	 */
	protected static $_SCRIPT_DEBUG = false;

	/**
	 * @var string $_SCRIPT_SUFFIX Whether to use minimized or full versions of JS.
	 */
	protected static $_SCRIPT_SUFFIX = '.min';

	/**
	 * @return string
	 */
	public static function SCRIPT_SUFFIX() {
		return self::$_SCRIPT_SUFFIX;
	}

	/**
	 * To use as the 'version' argument for JS/CSS enqueue.
	 *
	 * @since 1.2.2
	 * @return string
	 */
	public static function SCRIPT_VER() {
		return ( self::$_SCRIPT_DEBUG ? sprintf( 'debug-%d', time() ) : WPGLOBUS_VERSION );
	}

	/**
	 * Support third party plugin vendors
	 */
	public $vendors_scripts = array();

	const RETURN_IN_DEFAULT_LANGUAGE = 'in_default_language';
	const RETURN_EMPTY = 'empty';

	/**
	 * Don't make some updates at post screen and don't load scripts for this entities
	 */
	public $disabled_entities = array();

	/**
	 * Array of enabled pages for loading scripts, styles to achieve WPGlobusCore, WPGlobusDialogApp
	 * @since 1.2.0
	 */
	public $enabled_pages = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			self::$_SCRIPT_DEBUG  = true;
			self::$_SCRIPT_SUFFIX = '';
		}

		/** @todo maybe move this action to Class WPGlobus_Upgrade ? */
		add_action( 'admin_init', array(
			$this,
			'on_admin_init'
		) );

		global $pagenow;

		/**
		 * Init array of supported plugins
		 */
		$this->vendors_scripts['ACF']    = false;
		$this->vendors_scripts['ACFPRO'] = false;
		/** Set to true in @see WPGlobus_WPSEO::controller or WPGlobus_YoastSEO::controller */
		$this->vendors_scripts['WPSEO']       	= false;
		$this->vendors_scripts['WPSEO_PREMIUM'] = false;
		$this->vendors_scripts['WOOCOMMERCE']	= false;
		$this->vendors_scripts['AIOSEOP']     	= false; // All In One SEO Pack
		$this->vendors_scripts['WPCF7']       	= false; // Contact Form 7

		if ( function_exists( 'acf' ) ) {

			/**
			 * @todo  Work on the ACF compatibility is in progress
			 * Temporarily add CPT acf ( Advanced Custom Fields ) to the array of disabled_entities
			 * @see   'wpglobus_disabled_entities' filter for add/remove custom post types to array disabled_entities
			 * @since 1.0.4
			 */
			global $acf;
			if ( ! empty( $acf->settings['pro'] ) && $acf->settings['pro'] ) {
				/**
				 * @since 1.2.6
				 */
				$this->vendors_scripts['ACFPRO'] = true;
				$this->disabled_entities[]       = 'acf-field-group';
				$this->disabled_entities[]       = 'acf-field';

			} else {

				$this->vendors_scripts['ACF'] = true;
				$this->disabled_entities[]    = 'acf';

			}

		}

		if ( defined( 'WC_VERSION' ) || defined( 'WOOCOMMERCE_VERSION' ) ) {

			$this->vendors_scripts['WOOCOMMERCE'] = true;
			$this->disabled_entities[]            = 'product';
			$this->disabled_entities[]            = 'product_tag';
			$this->disabled_entities[]            = 'product_cat';
			$this->disabled_entities[]            = 'shop_order';
			$this->disabled_entities[]            = 'shop_coupon';

			/**
			 * Gathering Woocommerce's post types in one place
			 * @since 1.4.3
			 */
			$this->disabled_entities[] = 'product_variation';
			$this->disabled_entities[] = 'shop_order_refund';
			$this->disabled_entities[] = 'shop_webhook'; // Obsolete in WC3.

		}

		if ( defined( 'AIOSEOP_VERSION' ) ) {
			$this->vendors_scripts['AIOSEOP'] = true;
		}

		if ( defined( 'WPCF7_VERSION' ) ) {
			$this->vendors_scripts['WPCF7'] = true;
			/**
			 * Disable cpt of plugin Contact Form 7 by default
			 *
			 * @since 1.4.6
			 */
			$this->disabled_entities[] = 'wpcf7_contact_form';
		}

		/**
		 * If you need add new vendors script and disable cpt
		 * you must add it to customizer also
		 * @see class-wpglobus-customize-options.php:596
		 */

		/**
		 * Add builtin post type
		 */
		$this->disabled_entities[] = 'attachment';
		
		/**
		 * @since 1.9.0
		 */
		$this->disabled_entities[] = 'oembed_cache';

		/**
		 * Add disabled post types from option
		 */
		$option             = get_option( WPGlobus::Config()->option );
		$options_post_types = empty( $option['post_type'] ) ? array() : $option['post_type'];
		foreach ( $options_post_types as $post_type => $value ) {
			if ( $value != '1' ) {
				$this->disabled_entities[] = $post_type;
			}
		}

		$this->disabled_entities = array_unique( $this->disabled_entities );

		/**
		 * Set disabled entities into config
		 * @todo maybe move code to Class WPGlobus_Config
		 */
		WPGlobus::Config()->disabled_entities = $this->disabled_entities;

		add_filter( 'wp_redirect', array(
			$this,
			'on_wp_redirect'
		) );


		/**
		 * NOTE: do not check for !DOING_AJAX here.
		 */
		if ( is_admin() ) {

			/**
			 * Set values
			 * @since 1.2.0
			 */
			$this->enabled_pages[] = self::LANGUAGE_EDIT_PAGE;
			$this->enabled_pages[] = self::OPTIONS_PAGE_SLUG;
			$this->enabled_pages[] = 'post.php';
			$this->enabled_pages[] = 'post-new.php';
			$this->enabled_pages[] = 'nav-menus.php';

			/**
			 * @since 1.5.0
			 * edit-tags.php obsolete in WP 4.5
			 * term.php new in WP 4.5
			 */
			$this->enabled_pages[] = 'edit-tags.php';
			$this->enabled_pages[] = 'term.php';

			$this->enabled_pages[] = 'edit.php';
			$this->enabled_pages[] = 'options-general.php';
			$this->enabled_pages[] = 'widgets.php';
			$this->enabled_pages[] = 'customize.php';

			/**
			 * WPGlobus clean page
			 * @since 1.4.3
			 */
			$this->enabled_pages[] = self::PAGE_WPGLOBUS_CLEAN;

			/**
			 * WPGlobus Admin Central page.
			 * @since 1.6.6
			 */
			$this->enabled_pages[] = self::PAGE_WPGLOBUS_ADMIN_CENTRAL;

			add_action( 'admin_body_class', array( $this, 'on_add_admin_body_class' ) );

			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array( $this, 'on_process_ajax' ) );

			require_once 'options/class-wpglobus-options.php';
			new WPGlobus_Options();

			if ( in_array( $pagenow, array( 'edit-tags.php', 'term.php' ), true ) ) {
				/**
				 * Need to get taxonomy to use the correct filter.
				 */
				$taxonomy_slug = WPGlobus_Utils::safe_get( 'taxonomy' );
				if ( $taxonomy_slug ) {
					add_action( "{$taxonomy_slug}_pre_edit_form",
						array( $this, 'on_add_language_tabs_edit_taxonomy' ),
						10, 2
					);

					add_action( "{$taxonomy_slug}_edit_form",
						array( $this, 'on_add_taxonomy_form_wrapper' ),
						10, 2
					);
				}
			}

			if ( self::Config()->toggle == 'on' || ! $this->user_can( 'wpglobus_toggle' ) ) {

				/**
				 * Filters for adding language column to edit.php page
				 */
				if ( WPGlobus_WP::is_pagenow( 'edit.php' ) && ! $this->disabled_entity() ) {

					$post_type_filter = WPGlobus_Utils::safe_get( 'post_type' );
					if ( $post_type_filter ) {
						// This is a CPT.
						// Add underscore to form the
						// "manage_{$post->post_type}_posts_custom_column" filter.
						$post_type_filter = '_' . $post_type_filter;
					}

					add_filter( "manage{$post_type_filter}_posts_columns",
						array( $this, 'on_add_language_column' )
					);

					add_filter( "manage{$post_type_filter}_posts_custom_column",
						array( $this, 'on_manage_language_column' )
					);

				}

				/**
				 * Join post content and post title for enabled languages in func wp_insert_post
				 * @see action in wp-includes\post.php:3326
				 */
				add_action( 'wp_insert_post_data', array(
					$this,
					'on_save_post_data'
				), 10, 2 );

				add_action( 'edit_form_after_editor', array(
					$this,
					'on_add_wp_editors'
				), 10 );

				add_action( 'edit_form_after_editor', array(
					$this,
					'on_add_language_tabs'
				) );

				add_action( 'edit_form_after_title', array(
					$this,
					'on_add_title_fields'
				) );

				add_action( 'admin_print_scripts', array(
					$this,
					'on_admin_scripts'
				) );

				add_action( 'admin_print_scripts', array(
					$this,
					'on_admin_enqueue_scripts'
				), 99 );

				add_action( 'admin_footer', array(
					$this,
					'on_admin_footer'
				) );

				add_filter( 'admin_title', array(
					$this,
					'on_admin_title'
				), 10, 2 );

				if ( $this->vendors_scripts['AIOSEOP'] && WPGlobus_WP::is_pagenow( array(
						'post.php',
						'post-new.php',
						'edit.php'
					) )
				) {

					/** @global WP_Post $post */
					global $post;

					$type = empty( $post ) ? '' : $post->post_type;
					if ( ! $this->disabled_entity( $type ) ) {

						require_once 'vendor/class-wpglobus-aioseop.php';
						if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) {
							/** @noinspection PhpUnusedLocalVariableInspection */
							$WPGlobus_aioseop = new WPGlobus_aioseop();
						}
					}

				}

				/**
				 * Add multilingual Caption, Alternative Text, Description to media files.
				 * @since 1.7.3
				 */
				if ( version_compare( $GLOBALS['wp_version'], '4.6.999', '>' ) ) :
					if (
						WPGlobus_WP::is_pagenow( 'post.php' ) ||
						( WPGlobus_WP::is_doing_ajax() && WPGlobus_WP::is_http_post_action('send-attachment-to-editor') )
					) {
						require_once 'admin/media/class-wpglobus-media.php';
						WPGlobus_Media::get_instance();
					}
				endif;

			}    // endif $devmode

			if ( ( $this->vendors_scripts['ACF'] || $this->vendors_scripts['ACFPRO'] ) && WPGlobus_WP::is_pagenow( array(
					'post.php',
					'post-new.php'
				) )
			) {
				require_once 'vendor/class-wpglobus-acf.php';
				$WPGlobus_acf = new WPGlobus_Acf();
			}

			add_action( 'admin_print_styles', array(
				$this,
				'on_admin_styles'
			) );

			add_action( 'admin_menu', array(
				$this,
				'on_admin_menu'
			), 10 );

			add_action( 'post_submitbox_misc_actions', array(
				$this,
				'on_add_devmode_switcher'
			) );

			add_action( 'admin_bar_menu', array(
				$this,
				'on_admin_bar_menu'
			) );

			/**
			 * @scope admin
			 * @since 1.7.11
			 */
			add_action( 'admin_enqueue_scripts', array(
				$this,
				'enqueue_wpglobus_js'
			), 1000 );

			if ( WPGlobus_WP::is_pagenow( 'plugin-install.php' ) ) {
				require_once 'admin/class-wpglobus-plugin-install.php';
				WPGlobus_Plugin_Install::controller();
			}

		} else {

			/**
			 * @scope front
			 */

			$this->menus = self::_get_nav_menus();

			/**
			 * @todo This filter is currently disabled. Need to check if we need it.
			 *       The on_wp_list_pages is called directly from on_wp_page_menu
			 */
			0 && add_filter( 'wp_list_pages', array(
				$this,
				'on_wp_list_pages'
			), 99, 2 );

			add_filter( 'wp_page_menu', array(
				$this,
				'on_wp_page_menu'
			), 99, 2 );

			/**
			 * Add language switcher to navigation menu
			 * @see on_add_item
			 */
			add_filter( 'wp_nav_menu_objects', array(
				$this,
				'on_add_item'
			), 99, 2 );

			/**
			 * Convert url for menu items
			 */
			add_filter( 'wp_nav_menu_objects', array(
				$this,
				'on_get_convert_url_menu_items'
			), 10, 2 );

			add_action( 'wp_head', array(
				$this,
				'on_wp_head'
			), 11 );

			add_action( 'wp_footer', array(
				$this,
				'on__wp_footer'
			), 99 );

			add_action( 'wp_head', array(
				$this,
				'on_add_hreflang'
			), 11 );

			add_action( 'wp_print_styles', array(
				$this,
				'on_wp_styles'
			) );

			add_action( 'wp_enqueue_scripts', array(
				$this,
				'enqueue_wpglobus_js'
			),
				/**
				 * Load this script as late as possible,
				 * because it triggers the `wpglobus_current_language_changed` event.
				 * @since 1.5.5
				 */
				PHP_INT_MAX
			);
		}

	}

	/**
	 * Insert language title to edit.php page
	 *
	 * @param array $posts_columns
	 *
	 * @return array
	 */
	public function on_add_language_column( $posts_columns ) {
		/**
		 * Which column we insert after?
		 */
		$insert_after = 'title';

		$i = 0;
		foreach ( $posts_columns as $key => $value ) {
			if ( $key == $insert_after ) {
				break;
			}
			$i ++;
		}
		$posts_columns =
			array_slice( $posts_columns, 0, $i + 1 ) + array( 'wpglobus_languages' => 'Language' ) + array_slice( $posts_columns, $i + 1 );

		/**
		 * Filter the columns displayed in the Posts list table.
		 * Returning array.
		 * @since 1.6.5
		 *
		 * @param array $posts_columns An array of column names.
		 */
		return apply_filters( 'wpglobus_manage_posts_columns', $posts_columns );

	}

	/**
	 * Insert flags to every item at edit.php page
	 *
	 * @param string $column_name
	 */
	public function on_manage_language_column( $column_name ) {

		if ( 'wpglobus_languages' == $column_name ) {

			/** @global WP_Post $post */
			global $post;

			$output = array();
			$i      = 0;
			foreach ( WPGlobus::Config()->enabled_languages as $l ) {
				if ( 1 == preg_match( "/(\{:|\[:|<!--:)[$l]{2}/", $post->post_title . $post->post_content ) ) {
					$output[ $i ] =
						'<img title="' . WPGlobus::Config()->en_language_name[ $l ] .
						'" src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $l ] . '" />';

					/**
					 * Filter language item.
					 * Returning string.
					 * @since 1.0.14
					 *
					 * @param string $output Language item.
					 * @param array  $post   An object WP_Post.
					 * @param string $l      The language.
					 */
					$output[ $i ] = apply_filters( 'wpglobus_manage_language_item', $output[ $i ], $post, $l );
					$i ++;
				}
			}

			/**
			 * Filter language items before output.
			 * Returning array.
			 * @since 1.6.6
			 *
			 * @param string $output Array of language items.
			 * @param array  $post   An object WP_Post.
			 */
			$output = apply_filters( 'wpglobus_manage_language_items', $output, $post );

			if ( ! empty( $output ) ) {
				echo implode( '<br />', $output ); // WPCS: XSS ok.
			}

		}

	}


	/**
	 * Handle ajax process
	 */
	public function on_process_ajax() {

		$ajax_return = array();

		/**
		 * Sanitize
		 */

		$order = wp_unslash( $_POST['order'] ); // WPCS: input var ok, sanitization ok.

		$action = '';
		if ( isset( $order['action'] ) && is_string( $order['action'] ) ) {
			$action = sanitize_text_field( $order['action'] );
		}
		$post_type = '';
		if ( isset( $order['post_type'] ) && is_string( $order['post_type'] ) ) {
			$post_type = sanitize_text_field( $order['post_type'] );
		}
		$meta_key = '';
		if ( isset( $order['meta_key'] ) && is_string( $order['meta_key'] ) ) {
			$meta_key = sanitize_text_field( $order['meta_key'] );
		}
		$checked = '';
		if ( isset( $order['checked'] ) && is_string( $order['checked'] ) ) {
			$checked = sanitize_text_field( $order['checked'] );
		}
		$id = '';
		if ( isset( $order['id'] ) && is_string( $order['id'] ) ) {
			$id = sanitize_text_field( $order['id'] );
		}
		$locale = '';
		if ( isset( $order['locale'] ) && is_string( $order['locale'] ) ) {
			$locale = sanitize_text_field( $order['locale'] );
		}
		$type = '';
		if ( isset( $order['type'] ) && is_string( $order['type'] ) ) {
			$type = sanitize_text_field( $order['type'] );
		}
		$taxonomy = '';
		if ( isset( $order['taxonomy'] ) && is_string( $order['taxonomy'] ) ) {
			$taxonomy = sanitize_text_field( $order['taxonomy'] );
		}
		$titles = array();
		if ( isset( $order['title'] ) && is_array( $order['title'] ) ) {
			$titles = $order['title'];
		}

		switch ( $action ) {
			case 'clean':
			case 'wpglobus-reset':
				require_once 'admin/class-wpglobus-clean.php';
				WPGlobus_Clean::process_ajax( $order );

				break;
			case 'save_post_meta_settings':
				/**
				 * This is the WPGlobus icon, wrench and checkbox on custom post meta fields.
				 */

				$settings = (array) get_option( WPGlobus::Config()->option_post_meta_settings );

				if ( empty( $settings[ $post_type ] ) ) {
					$settings[ $post_type ] = array();
				}
				$settings[ $post_type ][ $meta_key ] = $checked;
				if ( update_option( WPGlobus::Config()->option_post_meta_settings, $settings ) ) {
					$ajax_return['result'] = 'ok';
				} else {
					$ajax_return['result'] = 'error';
				}
				$ajax_return['checked']  = $checked;
				$ajax_return['id']       = $id;
				$ajax_return['meta_key'] = $meta_key;
				break;
			case 'wpglobus_select_lang':
				if ( 'en_US' === $locale ) {
					update_option( 'WPLANG', '' );
				} else {
					update_option( 'WPLANG', $locale );
				}
				break;
			case 'get_titles':
				/**
				 * Prepare multilingual titles for Quick Edit.
				 */

				if ( 'taxonomy' === $type ) {
					/**
					 * Remove filter to get raw term description
					 */
					remove_filter( 'get_term', array( 'WPGlobus_Filters', 'filter__get_term' ), 0 );
				}

				$config = WPGlobus::Config();

				$result               = array();
				$bulkedit_post_titles = array();

				/**
				 * Iterate through the Titles array.
				 *
				 * @var  int      $id   Post or Term ID.
				 * @var  string[] $data Post or Term Name is stored in the $data['source'].
				 */
				foreach ( (array) $titles as $post_or_term_id => $data ) {
					$title = '';
					if ( ! empty( $data['source'] ) && is_string( $data['source'] ) ) {
						$title = $data['source'];
					}

					if ( ! WPGlobus_Core::has_translations( $title ) ) {
						/**
						 * In some cases, we've lost the raw data for post title on edit.php page
						 * for example product post type from Woo.
						 */
						$_title_from_db = '';
						if ( 'post' === $type ) {
							$_title_from_db = get_post_field( 'post_title', $post_or_term_id );
						} elseif ( 'taxonomy' === $type ) {
							$_term_by_id = get_term_by( 'id', $post_or_term_id, $taxonomy );
							if ( $_term_by_id ) {
								$_title_from_db = $_term_by_id->name;
							}
						}

						if ( $_title_from_db ) {
							$title = $_title_from_db;
						}

						unset( $_term_by_id, $_title_from_db );
					}

					$result[ $post_or_term_id ]['source'] = $title;

					$term = null; // Should initialize before if because used in the next foreach.

					if ( 'taxonomy' === $type && $taxonomy ) {
						$term = get_term( $post_or_term_id, $taxonomy );
						if ( is_wp_error( $term ) ) {
							$taxonomy = false;
						}
					}

					foreach ( $config->enabled_languages as $language ) {
						$return =
							$language === $config->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;

						$result[ $post_or_term_id ][ $language ]['name'] =
							WPGlobus_Core::text_filter( $title, $language, $return );
						if ( $term && 'taxonomy' === $type && $taxonomy ) {
							$result[ $post_or_term_id ][ $language ]['description'] =
								WPGlobus_Core::text_filter( $term->description, $language, $return );
						}

						$bulkedit_post_titles[ $post_or_term_id ][ $language ]['name'] =
							WPGlobus_Core::text_filter( $title, $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
					}
				}
				$ajax_return['qedit_titles']         = $result;
				$ajax_return['bulkedit_post_titles'] = $bulkedit_post_titles;
				break;
			case 'getNavMenuItemsDescription':
				/**
				 * @since 1.9.16
				 */
				$ids = array();
				foreach( $order['menuItems'] as $item_id ) {
					$_id = str_replace( 'edit-menu-item-description-', '', $item_id );
					$_id = (int) $_id;
					if ( $_id > 0 ) {
						$ids[] = $_id;
					}
				}
				
				$_results = array();
				if ( ! empty( $ids ) ) {
					$query = new WP_Query( array( 'post_type' => 'nav_menu_item', 'post__in' => $ids ) );
					$_results = $query->posts;
				}
				
				$ajax_return = array();
				if ( ! empty( $_results ) ) {
					foreach( $_results as $_post ) {
						if ( ! empty( $_post->post_content ) ) {
							$ajax_return['edit-menu-item-description-'.$_post->ID] = $_post->post_content;
						}
					}
				}
				break;
		}

		echo wp_json_encode( $ajax_return );
		die();
	}

	/**
	 * Ugly hack.
	 * @see wp_page_menu
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	public function on_wp_page_menu( $html ) {
		$switcher_html = $this->on_wp_list_pages( '' );
		$html          = str_replace( '</ul></div>', $switcher_html . '</ul></div>', $html );

		return $html;
	}

	/**
	 * Start WPGlobus on "init" hook.
	 */
	public static function init() {
		/** @global WPGlobus WPGlobus */
		global $WPGlobus;
		$WPGlobus = new self;
	}

	/**
	 * Set transient wpglobus_activated after activated plugin @see on_admin_init()
	 * @todo use $WPGlobus_Config to determine running this function?
	 *
	 * @param string $plugin
	 *
	 * @return void
	 */
	public static function activated( $plugin ) {
		if ( WPGLOBUS_PLUGIN_BASENAME == $plugin ) {
			/**
			 * Run on_activate after plugin activated
			 */
			$options['plugin'] = $plugin;
			$options['action'] = 'update';
			WPGlobus::Config()->on_activate( null, $options );

			/** @noinspection SummerTimeUnsafeTimeManipulationInspection */
			set_transient( 'wpglobus_activated', '', 60 * 60 * 24 );
		}
	}

	/**
	 * WP redirect hook
	 *
	 * @param string $location
	 *
	 * @return string
	 */
	public function on_wp_redirect( $location ) {
		if ( is_admin() ) {
			$_wp_http_referer = '';
			if ( isset( $_POST['_wp_http_referer'] ) && is_string( $_POST['_wp_http_referer'] ) ) { // WPCS: input var ok, sanitization ok.
				$_wp_http_referer = sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) ); // WPCS: input var ok.
			}
			if ( false !== strpos( $_wp_http_referer, 'wpglobus=off' ) ) {
				$location .= '&wpglobus=off';
			}
		} else {
			/**
			 * Get language code from cookie. Example: redirect $_SERVER[REQUEST_URI] = /wp-comments-post.php
			 */
			$request_uri = '';
			if ( isset( $_SERVER['REQUEST_URI'] ) && is_string( $_SERVER['REQUEST_URI'] ) ) { // WPCS: input var ok, sanitization ok.
				$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ); // WPCS: input var ok.
			}
			if ( false !== strpos( $request_uri, 'wp-comments-post.php' ) ) {
				if ( ! empty( $_COOKIE[ self::_COOKIE ] ) && is_string( $_COOKIE[ self::_COOKIE ] ) ) { // WPCS: input var ok, sanitization ok.
					$wpglobus_language_cookie = sanitize_text_field( wp_unslash( $_COOKIE[ self::_COOKIE ] ) ); // WPCS: input var ok.
					$location                 = WPGlobus_Utils::localize_url( $location, $wpglobus_language_cookie );
				}
			}
		}

		return $location;
	}

	/**
	 * Check if the current user has the $cap capability
	 *
	 * @param string $cap
	 *
	 * @return bool
	 */
	public function user_can( $cap = '' ) {
		global $current_user;
		if ( empty( $current_user ) ) {
			wp_get_current_user();
		}
		if ( 'wpglobus_toggle' == $cap ) {
			if ( $this->user_has_role( 'administrator' ) || current_user_can( $cap ) ) {
				return true;
			}

			return false;
		}

		return true;
	}

	/**
	 * Check current user has $role
	 *
	 * @param string $role
	 *
	 * @return boolean
	 */
	public function user_has_role( $role = '' ) {
		global $current_user;
		if ( empty( $current_user ) ) {
			wp_get_current_user();
		}

		return in_array( $role, $current_user->roles );
	}

	/**
	 * Add switcher to publish metabox.
	 * @return void
	 */
	public function on_add_devmode_switcher() {

		if ( ! $this->user_can( 'wpglobus_toggle' ) ) {
			return;
		}

		global $post, $pagenow;

		if ( $pagenow != 'post.php' ) {
			return;
		}

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		}

		// "Reverse" logic here. It's the mode to turn to, not the current one.
		$mode = 'off';
		if ( isset( $_GET['wpglobus'] ) && 'off' === $_GET['wpglobus'] ) { // WPCS: input var ok, sanitization ok.
			$mode = 'on';
		}

		$query_string = explode( '&', $_SERVER['QUERY_STRING'] );

		foreach ( $query_string as $key => $_q ) {
			if ( false !== strpos( $_q, 'wpglobus=' ) ) {
				unset( $query_string[ $key ] );
			}
		}

		$query = implode( '&', $query_string );
		$url   = admin_url(
			add_query_arg( array(
				'wpglobus' => $mode,
			),
				'post.php?' . $query
			)
		);

		if ( 'on' === $mode ) {
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
		?>
		<div class="misc-pub-section wpglobus-switch">
			<span id="wpglobus-raw" style="margin-right: 2px;"
					class="dashicons dashicons-admin-site <?php echo esc_attr( $highlight_class ); ?>"></span>
			<?php esc_html_e( 'WPGlobus', 'wpglobus' ); ?>:
			<strong class="<?php echo esc_attr( $highlight_class ); ?>"><?php echo esc_html( $status_text ); ?></strong>
			<a class="button button-small" style="margin:-3px 0 0 3px;"
					href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $toggle_text ); ?></a>
		</div>
		<?php
	}

	public function on_admin_enqueue_scripts() {
		/**
		 * @see on_admin_scripts()
		 */
		if ( ! wp_script_is( 'autosave', 'enqueued' ) ) {
			wp_enqueue_script( 'autosave' );
		}
	}

	/**
	 * Enqueue admin scripts
	 * @return void
	 */
	public function on_admin_scripts() {

		$post = get_post();
		$type = empty( $post->post_type ) ? '' : $post->post_type;

		if ( $this->disabled_entity( $type ) ) {
			return;
		}

		/**
		 * Dequeue autosave for prevent alert from wp.autosave.server.postChanged() after run post_edit in wpglobus.admin.js
		 * @see wp-includes\js\autosave.js
		 */
		wp_dequeue_script( 'autosave' );

		$pagenow = WPGlobus_WP::pagenow();

		$config = WPGlobus::Config();

		/**
		 * Get array of enabled pages for loading js
		 */
		$enabled_pages = $this->enabled_pages;

		/**
		 * Init $post_content
		 */
		$post_content = '';

		/**
		 * Init $post_title
		 */
		$post_title = '';

		/**
		 * Init $post_excerpt
		 */
		$post_excerpt = '';

		/**
		 * Init $page_action
		 */
		$page_action = '';

		/**
		 * Init $page
		 */
		$page = '';

		/**
		 * Init array data depending on the context for localize script
		 */
		$data = array(
			'default_language'  => $config->default_language,
			'language'          => $config->language,
			'enabled_languages' => $config->enabled_languages,
			'open_languages'    => $config->open_languages,
			'en_language_name'  => $config->en_language_name,
			'locale_tag_start'  => self::LOCALE_TAG_START,
			'locale_tag_end'    => self::LOCALE_TAG_END
		);

		if ( ! in_array( $pagenow, $enabled_pages ) ) {
			$page = WPGlobus_WP::plugin_page();
		}

		if ( '' == $page ) {
			/**
			 * Now get $pagenow
			 */
			$page = isset( $pagenow ) ? $pagenow : '';

			if ( 'post.php' == $page || 'post-new.php' == $page ) {

				$page_action = 'post.php';

				/**
				 * We use $post_content, $post_title at edit post page
				 */

				/**
				 * Set $post_content for default language
				 * because we have text with all languages and delimiters in $post->post_content
				 * next we send $post_content to js with localize script
				 * @see post_edit() in admin.globus.js
				 */
				$post_content = WPGlobus_Core::text_filter( $post->post_content, $config->default_language );

				/**
				 * Set $post_title for default language
				 */
				$post_title = WPGlobus_Core::text_filter( $post->post_title, $config->default_language );

			}

		}

		if ( in_array( $page, array( self::LANGUAGE_EDIT_PAGE, self::OPTIONS_PAGE_SLUG ) ) ) {

			/**
			 * Using the same 'select2-js' ID as Redux Plugin does, to avoid duplicate enqueueing
			 */
			if ( ! wp_script_is( 'select2-js' ) ) {
				wp_enqueue_script(
					'select2-js',
					WPGlobus::$PLUGIN_DIR_URL . 'lib/select2.min.js',
					array( 'jquery' ),
					'3.5.2',
					true
				);
			}

		}

		if ( in_array( $page, $enabled_pages ) ) {

			/**
			 * Init $tabs_suffix
			 */
			$tabs_suffix = array();

			if ( in_array( $page, array( 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ) ) ) {

				/**
				 * Make suffixes for tabs
				 */
				foreach ( $config->enabled_languages as $language ) {
					if ( $language == $config->default_language ) {
						$tabs_suffix[] = 'default';
					} else {
						$tabs_suffix[] = $language;
					}
				}

			}
			$i18n                            = array();
			$i18n['cannot_disable_language'] = __( 'You cannot disable the main language.', 'wpglobus' );

			if ( 'post.php' == $page || 'post-new.php' == $page ) {

				/**
				 * Add template for standard excerpt meta box
				 */
				$data['template'] = '';
				foreach ( WPGlobus::Config()->enabled_languages as $language ) {

					$return =
						$language == WPGlobus::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;

					$classes =
						in_array( $language, WPGlobus::Config()->open_languages ) ? 'wpglobus-excerpt wpglobus-translatable' : 'wpglobus-excerpt wpglobus-translatable hidden';

					$data['template'] .= '<textarea data-language="' . $language . '" placeholder="' . WPGlobus::Config()->en_language_name[ $language ] . '" class="' . $classes . '" rows="1" cols="40" name="excerpt-' . $language . '" id="excerpt-' . $language . '">';
					$data['template'] .= WPGlobus_Core::text_filter( $post->post_excerpt, $language, $return );
					$data['template'] .= '</textarea>';

					if ( defined( 'WPSEO_VERSION' ) ) {
						/**
						 * @todo This is the only place with WPSEO not in its own class.
						 */
						$blogname                             = get_option( 'blogname' );
						$blogdesc                             = get_option( 'blogdescription' );
						$data['blogname'][ $language ]        =
							WPGlobus_Core::text_filter( $blogname, $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
						$data['blogdescription'][ $language ] =
							WPGlobus_Core::text_filter( $blogdesc, $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
					}

				}

				$data['modify_excerpt'] = true;
				if ( isset( $this->vendors_scripts['WOOCOMMERCE'] ) && $this->vendors_scripts['WOOCOMMERCE'] && 'product' == $post->post_type ) {
					$data['modify_excerpt'] = false;
				}

				$data['tagsdiv'] = array();
				$data['tag']     = array();
				$tags            = $this->_get_taxonomies( $post->post_type, 'non-hierarchical' );

				if ( ! empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$data['tagsdiv'][]   = 'tagsdiv-' . $tag;
						$data['tag'][ $tag ] = self::_get_terms( $tag );
					}
				}

				/**
				 * Check for support 'title'
				 */
				$data['support']['title'] = true;
				if ( ! post_type_supports( $post->post_type, 'title' ) ) {
					$data['support']['title'] = false;
				}

				/**
				 * Check for support 'editor'
				 */
				$data['support']['editor'] = true;
				if ( ! post_type_supports( $post->post_type, 'editor' ) ) {
					$data['support']['editor'] = false;
				}

				if ( ! empty( $post ) ) {
					$data['post_type'] = $post->post_type;
					$opts              = (array) get_option( WPGlobus::Config()->option_post_meta_settings );
					if ( empty( $opts ) ) {
						$data['post_meta_settings'] = '';
					} else {
						$data['post_meta_settings'] = $opts;
					}
				}

				$data[ 'customFieldsEnabled' ] = true;

				/**
				 * Filter to enable/disable multilingual custom fields on post.php|post-new.php page.
				 * Returning boolean.
				 * @since 1.6.5
				 *
				 * @param boolean		 $data[ 'customFieldsEnabled' ] Enabled by default.
				 * @param WP_Post Object $post Current post.
				 */
				$data[ 'customFieldsEnabled' ] = apply_filters( 'wpglobus_custom_fields_enabled', $data[ 'customFieldsEnabled' ], $post );

				if ( $data[ 'customFieldsEnabled' ] ) {
					$data[ 'customFieldsEnabled' ] = 'true';
				} else {
					$data[ 'customFieldsEnabled' ] = 'false';
				}

			} else if ( 'nav-menus.php' === $page ) {

				$page_action = 'menu-edit';
				$menu_items  = array();
				$post_titles = array();

				/** @global wpdb $wpdb */
				global $wpdb;

				$_query = new WP_Query( array(
					'post_type' => 'nav_menu_item',
					'nopaging'  => true,
				) );

				/**
				 * Array of menu items.
				 *
				 * @var WP_Post[] $items
				 */
				$items = $_query->posts;
				unset( $_query );

				foreach ( $items as $item ) :

					$item->post_title = trim( $item->post_title );

					if ( empty( $item->post_title ) ) :

						$item_type      = get_post_meta( $item->ID, '_menu_item_type', true );
						$item_object    = get_post_meta( $item->ID, '_menu_item_object', true );
						$item_object_id = get_post_meta( $item->ID, '_menu_item_object_id', true );

						$_raw_title = '';

						if ( 'post_type' === $item_type ) {
							$_raw_title = get_post_field( 'post_title', $item_object_id );
						} elseif ( 'taxonomy' === $item_type ) {

							/**
							 * Here we need the raw term. Temporary need to disable our filter.
							 */
							remove_filter( 'get_term', array( 'WPGlobus_Filters', 'filter__get_term' ), 0 );
							$term = get_term_by( 'id', $item_object_id, $item_object );
							add_filter( 'get_term', array( 'WPGlobus_Filters', 'filter__get_term' ), 0 );

							$_raw_title = $term->name;
						}

						$_raw_title  = trim( $_raw_title );
						if ( ! empty( $_raw_title ) ) {
							$item->post_title = $_raw_title;
							// Save the raw title in the menu.
							$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_title = '%s' WHERE ID = %d", $_raw_title, $item->ID ) );
						}

					endif; // Empty post_title.

					/**
					 * Add raw data for Navigation Label
					 */
					$menu_items[ $item->ID ]['input.edit-menu-item-title']['source'] = $item->post_title;

					/**
					 * Add raw data for Title Attribute
					 */
					$menu_items[ $item->ID ]['input.edit-menu-item-attr-title']['source'] = $item->post_excerpt;

					$menu_items[ $item->ID ]['item-title'] =
						WPGlobus_Core::text_filter( $item->post_title, $config->default_language );

					$post_titles[ $item->post_title ] = $menu_items[ $item->ID ]['item-title'];

					foreach ( self::Config()->enabled_languages as $language ) {

						$return =
							$language == self::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;

						/**
						 * Navigation Label
						 */
						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-title']['caption'] =
							WPGlobus_Core::text_filter( $item->post_title, $language, $return );

						/**
						 * Title Attribute
						 */
						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-attr-title']['caption'] =
							WPGlobus_Core::text_filter( $item->post_excerpt, $language, $return );

						/**
						 * Navigation Label classes
						 */
						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-title']['class'] =
							'widefat wpglobus-menu-item wpglobus-item-title wpglobus-translatable';

						/**
						 * Title Attribute classes
						 */
						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-attr-title']['class'] =
							'widefat wpglobus-menu-item wpglobus-item-attr wpglobus-translatable';

					}

				endforeach;

				$data['items']       = $menu_items;
				$data['post_titles'] = $post_titles;

				$i18n['save_nav_menu'] = __( '*) Available after the menu is saved.', 'wpglobus' );

			} else if ( 'edit-tags.php' == $page || 'term.php' == $page ) {

				global $tag;

				$data['taxonomy']  = empty( $_GET['taxonomy'] ) ? false : sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ); // WPCS: input var ok, sanitization ok.
				$data['tag_id']    = empty( $_GET['tag_ID'] ) ? false : sanitize_text_field( wp_unslash( $_GET['tag_ID'] ) ); // WPCS: input var ok, sanitization ok.
				$data['has_items'] = true;
				$data['multilingualSlug'] = array();

				if ( $data['tag_id'] ) {
					/**
					 * For example url: edit-tags.php?action=edit&taxonomy=category&tag_ID=4&post_type=post
					 */
					$page_action = 'taxonomy-edit';
					$data['multilingualSlug']['title'] =
						'<div class=""><a href="' . WPGlobus_Utils::url_wpglobus_site() . 'product/wpglobus-plus/#taxonomies" target="_blank">' . esc_html__( 'Need a multilingual slug?', 'wpglobus' ) . '</a></div>';
				} else {
					/**
					 * For example url: edit-tags.php?taxonomy=category
					 * edit-tags.php?taxonomy=product_cat&post_type=product
					 */
					if ( $data['taxonomy']  ) {
						$terms = get_terms( $data['taxonomy'] , array( 'hide_empty' => false ) );
						if ( is_wp_error( $terms ) or empty( $terms ) ) {
							$data['has_items'] = false;
						}
					}
					$page_action = 'taxonomy-quick-edit';
				}

				if ( $data['tag_id'] ) {
					foreach ( $config->enabled_languages as $language ) {
						$lang = $language == $config->default_language ? 'default' : $language;
						if ( 'default' == $lang ) {
							$data['i18n'][ $lang ]['name']        =
								WPGlobus_Core::text_filter( $tag->name, $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
							$data['i18n'][ $lang ]['description'] =
								WPGlobus_Core::text_filter( $tag->description, $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
						} else {
							$data['i18n'][ $lang ]['name']        =
								WPGlobus_Core::text_filter( $tag->name, $language, WPGlobus::RETURN_EMPTY );
							$data['i18n'][ $lang ]['description'] =
								WPGlobus_Core::text_filter( $tag->description, $language, WPGlobus::RETURN_EMPTY );
						}
					}
				} else {
					/**
					 * Get template for quick edit taxonomy name at edit-tags.php page
					 */
					$data['template'] = $this->_get_quickedit_template();

				}

			} elseif ( $page == 'edit.php' ) {

				$page_action = 'edit.php';

				$post_type  = empty( $_GET['post_type'] ) ? 'post' : sanitize_text_field( wp_unslash( $_GET['post_type'] ) ); // WPCS: input var ok, sanitization ok.

				global $posts;
				$data['has_items'] = empty( $posts ) ? false : true;
				/**
				 * Get template for quick edit post title at edit.php page
				 */
				$data['template'] = $this->_get_quickedit_template();

				$tags = $this->_get_taxonomies( $post_type, 'non-hierarchical' );
				if ( ! empty( $tags ) ) {
					foreach ( $tags as $tag ) {
						$terms = self::_get_terms( $tag );
						if ( ! empty( $terms ) ) {
							$data['tags'][]        = $tag;
							$data['names'][ $tag ] = 'tax_input[' . $tag . ']';
							$data['tag'][ $tag ]   = $terms;
							$data['value'][ $tag ] = array( 'post_id' => '' ); // just init
						}
					}
				}

			} elseif ( 'options-general.php' === $page ) {

				$page_action = 'options-general.php';

			} elseif ( 'widgets.php' === $page ) {

				$page_action = 'widgets.php';

			} elseif ( 'customize.php' === $page ) {

				if ( version_compare( WPGLOBUS_VERSION, '1.4.0-beta1', '<' ) ) {
					$html = sprintf( __( 'You are customizing %s' ), '<strong class="theme-name site-title"><span id="wpglobus-customize-info">' . esc_html( WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->default_language ) ) . '</span></strong>' );
				} else {
					// @since 1.4.0 class panel-title site-title
					$html = sprintf( __( 'You are customizing %s' ), '<strong class="panel-title site-title"><span id="wpglobus-customize-info">' . esc_html( WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->default_language ) ) . '</span></strong>' );
				}

				$page_action      = 'customize.php';
				$page_data_key    = 'customize';
				$page_data_values = array(
					'info'        => array(
						'element' => '#customize-info .preview-notice',
						'html'    => $html
					),
					'addElements' => array(
						'wpglobus_blogname'        => array(
							'origin'         => 'blogname',
							'origin_element' => '#customize-control-blogname input',
							'origin_parent'  => '#customize-control-blogname',
							'element'        => '#customize-control-wpglobus_blogname input',
							'value'          => WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY )
						),
						'wpglobus_blogdescription' => array(
							'origin'         => 'blogdescription',
							'origin_element' => '#customize-control-blogdescription input',
							'origin_parent'  => '#customize-control-blogdescription',
							'element'        => '#customize-control-wpglobus_blogdescription input',
							'value'          => WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY )
						)
					)
				);

			} elseif ( in_array( $page, array( 'wpglobus_options', self::LANGUAGE_EDIT_PAGE ), true ) ) {

				$page_action = 'wpglobus_options';

			} elseif ( self::PAGE_WPGLOBUS_CLEAN === $page ) {

				$page_action = 'wpglobus_clean';

			} elseif (
				( 'admin.php' === $pagenow && ! empty( $_GET['page'] ) // WPCS: input var ok, sanitization ok.
				  && self::PAGE_WPGLOBUS_ADMIN_CENTRAL === $_GET['page'] ) // WPCS: input var ok, sanitization ok.
				|| self::PAGE_WPGLOBUS_ADMIN_CENTRAL === $page
			) {

				/**
				 * @since 1.6.6
				 */
				$page_action = 'wpglobusAdminCentral';
				/**
				 * @since 1.8
				 */
				$data['pagenow'] 	= $pagenow;
				$data['page'] 	 	= self::PAGE_WPGLOBUS_ADMIN_CENTRAL;
				$data['pageAction'] = $page_action;

			} else {

				$page_action = $page;

			}

			/**
			 * WordPress 4.7+ needs a new version of our admin JS.
			 * @since 1.7.0
			 */
			$version = '';
			if ( version_compare( $GLOBALS['wp_version'], '4.6.999', '>' ) ) {
				$version = '-47';
			}

			/**
			 * WordPress 4.9+ needs a new version of our admin JS.
			 * @since 1.9.2
			 */
			if ( version_compare( $GLOBALS['wp_version'], '4.8.999', '>' ) ) {
				$version = '-49';
			}			
			
			wp_register_script(
				'wpglobus-admin',
				self::$PLUGIN_DIR_URL . "includes/js/wpglobus-admin$version" . self::$_SCRIPT_SUFFIX . ".js",
				array( 'jquery', 'underscore', 'jquery-ui-dialog', 'jquery-ui-tabs', 'jquery-ui-tooltip' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-admin' );

			/**
			 * We need to send the HTML breaks and not \r\n to the JS,
			 * because we do element.text(...), and \r\n are being removed by TinyMCE
			 * See other places with the same bookmark.
			 * @bookmark EDITOR_LINE_BREAKS
			 * added 24.05.2015
			 * @todo     what's next with wpautop?  @see 'wpautop()' in https://make.wordpress.org/core/2015/05/14/dev-chat-summary-may-13/
			 */
			if ( has_filter( 'the_content', 'wpautop' ) ) {
				$post_content_autop = wpautop( $post_content );
			} else {
				$post_content_autop = $post_content;
			}

			/**
			 * Filter for data to send to JS.
			 * Returning array.
			 * @since 1.5.5
			 *
			 * @param array  $data        An array with data.
			 * @param string $page_action Page.
			 */
			$data = apply_filters( 'wpglobus_localize_data', $data, $page_action );

			/**
			 * Added $_GET array to JS.
			 * @since 1.9.11
			 */
			$__get = array();
			foreach( $_GET as $_key=>$_ ) {
				$__get[$_key] = WPGlobus_Utils::safe_get($_key);
			}
			
			wp_localize_script(
				'wpglobus-admin',
				'WPGlobusAdmin',
				array(
					'version'      => WPGLOBUS_VERSION,
					'page'         => $page_action,
					'$_get'        => $__get,
					'content'      => $post_content_autop,
					'title'        => $post_title,
					'excerpt'      => $post_excerpt,
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'parentClass'  => __CLASS__,
					'process_ajax' => __CLASS__ . '_process_ajax',
					'flag_url'     => $config->flags_url,
					'tabs'         => $tabs_suffix,
					'currentTab'   => $config->default_language,
					'i18n'         => $i18n,
					'data'         => $data
				)
			);

			if ( empty( $page_data_key ) ) {
				$page_data_key = 'page_custom_data';
			}
			if ( empty( $page_data_values ) ) {
				$page_data_values = null;
			}

			/**
			 * Add multisite property
			 * @since 1.6.0
			 */
			$is_multisite = 'false';
			if ( is_multisite() ) {
				$is_multisite = 'true';
			}

			/**
			 * Filter for custom data to send to JS.
			 * Returning array or null.
			 * @since 1.2.9
			 *
			 * @param array  $page_data_values An array with custom data or null.
			 * @param string $page_data_key    Data key. @since 1.3.0
			 * @param string $page_action      Page. @since 1.5.0
			 */
			$page_data_values = apply_filters( 'wpglobus_localize_custom_data', $page_data_values, $page_data_key, $page_action );

			wp_localize_script(
				'wpglobus-admin',
				'WPGlobusCoreData',
				array_merge(
					array(
						'version'           	=> WPGLOBUS_VERSION,
						'default_language'  	=> $config->default_language,
						'language'          	=> $config->language,
						'enabled_languages' 	=> $config->enabled_languages,
						'open_languages'    	=> $config->open_languages,
						'en_language_name'  	=> $config->en_language_name,
						'locale_tag_start'  	=> self::LOCALE_TAG_START,
						'locale_tag_end'    	=> self::LOCALE_TAG_END,
						'page'              	=> $page_action,
						'multisite'				=> $is_multisite,
						'pluginInstallLocation'	=> array(
							'single'	=> 	'plugin-install.php?tab=search&s=WPGlobus&source=WPGlobus',
							'multisite'	=>	'network/plugin-install.php?tab=search&s=WPGlobus&source=WPGlobus'
						)
					), array(
						$page_data_key => $page_data_values
					)
				)
			);

			/**
			 * Enqueue js for ACF support
			 */
			if (
				( $this->vendors_scripts['ACF'] || $this->vendors_scripts['ACFPRO'] )
				&& in_array( $page, array( 'post.php', 'post-new.php' )
				)
			) {

				/**
				 * Filter to disable translation of selected ACF and ACF Pro fields.
				 * @since 1.5.0
				 *
				 * To exclude field in ACF plugin you need to use the field name from Field Group ( usually wp-admin/edit.php?post_type=acf ).
				 * To exclude field in ACF Pro plugin you need to use id, see Wrapper Attributes section on field's edit page.
				 *
				 * @param array   $disabled_fields Default is empty array.
				 * @param boolean $is_acf_pro      Type of ACF plugin.
				 *
				 * @return array
				 */
				$disabled_fields = apply_filters( 'wpglobus_disabled_acf_fields', array(), $this->vendors_scripts['ACFPRO'] );

				wp_register_script(
					'wpglobus-acf',
					self::$PLUGIN_DIR_URL . "includes/js/wpglobus-vendor-acf" . self::$_SCRIPT_SUFFIX . ".js",
					array( 'jquery', 'wpglobus-admin' ),
					WPGLOBUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-acf' );
				wp_localize_script(
					'wpglobus-acf',
					'WPGlobusAcf',
					array(
						'wpglobus_version' => WPGLOBUS_VERSION,
						'pro'              => $this->vendors_scripts['ACFPRO'] ? true : false,
						'fields'   		   => array(),
						'disabledFields'   => $disabled_fields
					)
				);

			}

			if ( 'widgets.php' == $page ) {

				$disabled_widgets_mask = array( 'rss-url' );

				/**
				 * Filter to disable making multilingual element on widgets.php page.
				 * @since 1.5.3
				 *
				 * @param array $disabled_widgets_mask Array of disabled masks.
				 *
				 * @return array
				 */
				$disabled_widgets_mask = apply_filters( 'wpglobus_disabled_widgets_mask', $disabled_widgets_mask );

				wp_register_script(
					'wpglobus-widgets',
					self::$PLUGIN_DIR_URL . "includes/js/wpglobus-widgets" . self::$_SCRIPT_SUFFIX . ".js",
					array( 'jquery', 'underscore', 'wpglobus-admin' ),
					WPGLOBUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-widgets' );
				wp_localize_script(
					'wpglobus-widgets',
					'WPGlobusWidgets',
					array(
						'wpglobus_version' => WPGLOBUS_VERSION,
						'disabledMask'     => $disabled_widgets_mask
					)
				);

			}

		}    // endif $enabled_pages
	}

	/**
	 * Get taxonomies for post type
	 *
	 * @param string $post_type
	 * @param string $type hierarchical, non-hierarchical or all taxonomies
	 *
	 * @return array
	 */
	public function _get_taxonomies( $post_type, $type = 'all' ) {
		if ( empty( $post_type ) ) {
			return array();
		}
		$taxs       = array();
		$taxonomies = get_object_taxonomies( $post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_data = get_taxonomy( $taxonomy );
			if ( 'all' == $type ) {
				$taxs[] = $taxonomy_data->name;
				continue;
			}
			if ( 'non-hierarchical' == $type && ! $taxonomy_data->hierarchical ) {
				/**
				 * This is tag
				 * @todo Theoretically, it's not "tag". Can be any custom taxonomy. Need to check.
				 * @todo
				 * Practically in WP: all non-hierarchical taxonomy is tags.
				 * In this context I use term $tags for saving non-hierarchical taxonomies only
				 * for further work with them when editing posts
				 */
				$taxs[] = $taxonomy_data->name;
			} elseif ( 'hierarchical' == $type && $taxonomy_data->hierarchical ) {
				$taxs[] = $taxonomy_data->name;
			}
		}

		return $taxs;
	}

	/**
	 * Get template for quick edit at edit-tags.php, edit.php screens
	 * @return string
	 */
	public function _get_quickedit_template() {
		$t = '';
		foreach ( self::Config()->open_languages as $language ) {
			$t .= '<label>';
			$t .= '<span class="input-text-wrap">';
			$t .= '<input id="filled-in-js" data-language="' . $language . '" style="width:100%;" class="ptitle wpglobus-quick-edit-title wpglobus-translatable" type="text" value="" name="post_title-' . $language . '" placeholder="' . self::Config()->en_language_name[ $language ] . '">';
			$t .= '</span>';
			$t .= '</label>';
		}

		return $t;
	}

	/**
	 * Enqueue admin styles
	 * @return void
	 */
	public function on_admin_styles() {

		$page = '';
		if ( isset( $_GET['page'] ) && is_string( $_GET['page'] ) ) { // WPCS: input var ok, sanitization ok.
			$page = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // WPCS: input var ok.
		}

		wp_register_style(
			'wpglobus-admin',
			self::$PLUGIN_DIR_URL . 'includes/css/wpglobus-admin.css',
			array(),
			WPGLOBUS_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpglobus-admin' );

		if ( self::LANGUAGE_EDIT_PAGE === $page ) {
			/**
			 * Using the same 'select2-css' ID as Redux Plugin does, to avoid duplicate enqueueing
			 */
			if ( ! wp_style_is( 'select2-js' ) ) {
				wp_enqueue_style(
					'select2-css',
					WPGlobus::$PLUGIN_DIR_URL . 'lib/select2.min.css',
					array(),
					'3.5.2'
				);
			}
		}

		$post = get_post();
		$type = empty( $post ) ? '' : $post->post_type;

		if ( ! $this->disabled_entity( $type ) ) {

			/**
			 * Loading CSS for enabled pages as for js
			 * @since 1.2.0
			 */
			/** @global string $pagenow */
			global $pagenow;

			if ( in_array( $pagenow, $this->enabled_pages ) || in_array( $page, $this->enabled_pages ) ) {

				wp_register_style(
					'wpglobus-admin-tabs',
					self::$PLUGIN_DIR_URL . 'includes/css/wpglobus-admin-tabs.css',
					array(),
					WPGLOBUS_VERSION,
					'all'
				);
				wp_enqueue_style( 'wpglobus-admin-tabs' );

				wp_enqueue_style(
					'dialog-ui',
					self::$PLUGIN_DIR_URL . 'includes/css/wpglobus-dialog-ui.css',
					array(),
					WPGLOBUS_VERSION,
					'all'
				);

			}

		}

		if ( self::PAGE_WPGLOBUS_ABOUT === $page ) {
			wp_register_style(
				'wpglobus-special-pages',
				self::$PLUGIN_DIR_URL . 'includes/css/wpglobus-special-pages.css',
				array(),
				WPGLOBUS_VERSION,
				'all'
			);
			wp_enqueue_style( 'wpglobus-special-pages' );
		}

	}
	
	/**
	 * Add hidden submenu for Language edit page
	 * @return void
	 */
	public function on_admin_menu() {
		
		/**
		 * @todo Temporarily add main menu.
		 */
		/**
		add_menu_page(
			'WPGlobus',
			'WPGlobus',
			'administrator',
			'wpglobus-main',
			array( $this, 'wpglobus_about' )
		); **/
		
		add_submenu_page(
			null,
			'',
			'',
			'administrator',
			self::LANGUAGE_EDIT_PAGE,
			array(
				$this,
				'on_language_edit'
			)
		);

		add_submenu_page(
			null,
			'',
			'',
			'administrator',
			self::PAGE_WPGLOBUS_ABOUT,
			array(
				$this,
				'wpglobus_about'
			)
		);

		add_submenu_page(
			null,
			'',
			'',
			'administrator',
			self::PAGE_WPGLOBUS_CLEAN,
			array(
				$this,
				'wpglobus_clean'
			)
		);
	}

	/**
	 * Include file for WPGlobus clean page
	 * @since 1.4.3
	 * @return void
	 */
	public function wpglobus_clean() {
		require_once 'admin/class-wpglobus-clean.php';
		WPGlobus_Clean::controller();
	}

	/**
	 * Include file for WPGlobus about page
	 * @return void
	 */
	public function wpglobus_about() {
		require_once 'admin/class-wpglobus-about.php';
		WPGlobus_About::about_screen();
	}

	/**
	 * Include file for language edit page
	 * @return void
	 */
	public function on_language_edit() {
		require_once 'admin/class-wpglobus-language-edit.php';
		new WPGlobus_Language_Edit();
	}

	/**
	 * We must convert url for nav_menu_item with type == custom
	 * For other types url has language shortcode already
	 *
	 * @param $sorted_menu_items
	 *
	 * @internal param $args
	 * @return array
	 */
	public function on_get_convert_url_menu_items( $sorted_menu_items ) {

		foreach ( $sorted_menu_items as $key => $item ) {

			/**
			 * Ability to avoid the localize URL.
			 * @since 1.8.6
			 */
			$localize = true;
			if ( ! empty( $item->classes ) && in_array( 'wpglobus-menu-item-url-nolocalize', $item->classes ) ) {
				$localize = false;
			}

			if ( 'custom' == $item->type ) {
				if ( $localize ) {
					$sorted_menu_items[ $key ]->url = WPGlobus_Utils::localize_url( $sorted_menu_items[ $key ]->url );
				}
			} else {
				if ( ! $localize ) {
					/**
					 * URL was localized already.
					 * @see wp_setup_nav_menu_item() in p-includes\nav-menu.php
					 * @since 1.8.6
					 */
					$sorted_menu_items[ $key ]->url = WPGlobus_Utils::localize_url( $sorted_menu_items[ $key ]->url, WPGlobus::Config()->default_language );
				}

			}
		}

		/**
		 * Filter for menu objects
		 * @since 1.5.1
		 *
		 * @param array $sorted_menu_items An array of sorted menu items.
		 *
		 * @return array
		 */
		return apply_filters( 'wpglobus_nav_menu_objects', $sorted_menu_items );

	}

	/**
	 * Enqueue styles
	 * @return void
	 */
	public function on_wp_styles() {
		wp_register_style(
			'wpglobus',
			self::$PLUGIN_DIR_URL . "includes/css/wpglobus.css",
			array(),
			WPGLOBUS_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpglobus' );
	}

	/**
	 * Enqueue the `wpglobus.js` script.
	 * @since 1.0
	 * @since 1.7.11 Added WPGlobus::Config()->enabled_languages.
	 */
	public function enqueue_wpglobus_js() {

		wp_enqueue_script(
			'wpglobus',
			self::$PLUGIN_DIR_URL . "includes/js/wpglobus" . self::$_SCRIPT_SUFFIX . ".js",
			array( 'jquery', 'utils' ),
			WPGLOBUS_VERSION,
			true
		);

		wp_localize_script(
			'wpglobus',
			'WPGlobus',
			array(
				'version'  => WPGLOBUS_VERSION,
				'language' => WPGlobus::Config()->language,
				'enabledLanguages'	=> WPGlobus::Config()->enabled_languages
			)
		);
	}

	/**
	 * Add rel="alternate" links to head section
	 * @return void
	 */
	public function on_add_hreflang() {

		$hreflangs = WPGlobus_Utils::hreflangs();

		/**
		 * Filter hreflang.
		 * Returning array.
		 * @since 1.0.14
		 *
		 * @param string $hreflangs An array.
		 */
		$hreflangs = apply_filters( 'wpglobus_hreflang_tag', $hreflangs );

		if ( ! empty( $hreflangs ) ) {
			echo wp_kses( implode( '', $hreflangs ), array(
					'link' => array(
							'rel' => array(),
							'hreflang' => array(),
							'href' => array(),
					)
			) );
		}

	}

	/**
	 * Add css styles to head section
	 * @return void
	 */
	public function on_wp_head() {

		$config = WPGlobus::Config();

		$css = '';

		/**
		 * CSS rules for flags in the menu
		 */
		foreach ( $config->enabled_languages as $language ) {
			$css .= '.wpglobus_flag_' . $language .
			        '{background-image:url(' . $config->flags_url . $config->flag[ $language ] . ")}\n";
		}

		/**
		 * Swap flag and text for RTL
		 * (See the LTR default rules in the wpglobus-flags.mixin.less)
		 */
		if ( is_rtl() ) {
			$css .= '.wpglobus_flag{background-position:center right;}' .
			        '.wpglobus_language_name{padding-right:22px;}';
		}

		/**
		 * Filter CSS rules for frontend.
		 * @since 1.6.6
		 *
		 * @param string $css CSS rules for flags in the menu.
		 * @param string $config->css_editor Custom CSS rules @see WPGlobus options.
		 *
		 * @return string
		 */
		$css = apply_filters( 'wpglobus_styles', $css, $config->css_editor );

		if ( ! empty( $css ) ) {
			?>
			<style type="text/css" media="screen">
				<?php echo wp_kses( $css, array() ); ?>
			</style>
			<?php
		}

	}

	/**
	 * Append language switcher dropdown or flat to a navigation menu, which was created with
	 * @see wp_list_pages
	 *
	 * @since 1.5.8
	 * @param string $output The menu HTML string
	 * @return string HTML with appended switcher
	 */
	public function filter__wp_list_pages( $output ) {

		/**
		 * WPGlobus Configuration setting in admin. Must be "ON" to process.
		 */
		if ( ! WPGlobus::Config()->selector_wp_list_pages ) {
			return $output;
		}

		$current_url      = WPGlobus_Utils::current_url();
		$current_language = WPGlobus::Config()->language;

		/**
		 * List of the languages to show in the drop-down.
		 * These are all enabled languages, except for the current one.
		 * The current one will be shown at the top.
		 */
		$extra_languages = array_diff(
			WPGlobus::Config()->enabled_languages, (array) $current_language );

		/**
		 * Filter extra languages.
		 * Returning array.
		 * @since 1.0.13
		 *
		 * @param array  $extra_languages  An array with languages to show in the dropdown.
		 * @param string $current_language The current language.
		 */
		$extra_languages = apply_filters(
			'wpglobus_extra_languages', $extra_languages, $current_language );

		/**
		 * Filter to show dropdown menu or not.
		 * Returning boolean.
		 * @since 1.2.2
		 *
		 * @param bool
		 * @param WPGlobus_Config
		 */
		$dropdown_menu = apply_filters( 'wpglobus_dropdown_menu', true, WPGlobus::Config() );

		/**
		 * Array of menu items
		 */
		$wpglobus_menu_items = array();

		/**
		 * Build the top-level menu link
		 */
		//$language          = $current_language;
		$url               = WPGlobus_Utils::localize_url( $current_url, $current_language );
		$flag_name         = $this->_get_flag_name( $current_language );
		$span_classes_lang = $this->_get_language_classes( $current_language );

		$link_text = '<span class="' . implode( ' ', $span_classes_lang ) . '">' .
		             esc_html( $flag_name ) . '</span>';
		$a_tag     = '<a class="wpglobus-selector-link" href="' . esc_url( $url ) . '">' . $link_text . '</a>';

		/**
		 * Current language menu item classes
		 */
		$menu_item_classes = array(
			'page_item' 						=> 'page_item',
			'page_item_wpglobus_menu_switch' 	=> 'page_item_wpglobus_menu_switch',
			'page_item_has_children' 			=> 'page_item_has_children',
			'wpglobus-current-language' 		=> 'wpglobus-current-language'
		);

		/**
		 * Submenu item classes for extra languages
		 */
		$submenu_item_classes = array(
			'page_item' 						 => 'page_item',
			'page_item_wpglobus_menu_switch'	 => 'page_item_wpglobus_menu_switch',
			'sub_menu_item_wpglobus_menu_switch' => 'sub_menu_item_wpglobus_menu_switch'
		);

		$item                   = new stdClass();
		$item->item_has_parent  = false;
		$item->title            = $a_tag;
		$item->url         		= $url;
		$item->language    		= $current_language;

		if ( $dropdown_menu ) {

			/**
			 * Dropdown menu
			 */
			$item->classes     		= $menu_item_classes;
			$item->classes[ 'page_item_wpglobus_menu_switch_' . $current_language ] = 'page_item_wpglobus_menu_switch_' . $current_language;
			$wpglobus_menu_items[]  = $item;

			$template = '<li {{parent}}<ul class="children">{{children}}</ul></li>';

			foreach ( $extra_languages as $language ) :
				/**
				 * Build the drop-down menu links for extra language
				 */
				$url               = WPGlobus_Utils::localize_current_url( $language );
				$flag_name         = $this->_get_flag_name( $language );
				$span_classes_lang = $this->_get_language_classes( $language );

				$link_text = '<span class="' . implode( ' ', $span_classes_lang ) . '">' .
				             esc_html( $flag_name ) . '</span>';
				$a_tag     = '<a class="wpglobus-selector-link" href="' . esc_url( $url ) . '">' . $link_text . '</a>';

				$item                   = new stdClass();
				$item->item_has_parent	= true;
				$item->title            = $a_tag;
				$item->url         		= $url;
				$item->classes     		= $submenu_item_classes;
				$item->language    		= $language;
				$item->classes[ 'page_item_wpglobus_menu_switch_' . $language ]  = 'page_item_wpglobus_menu_switch_' . $language;

//				$item->object_id = $item->ID;
				$item->object = 'custom';

				$wpglobus_menu_items[] = new WP_Post( $item );

			endforeach;

		} else {

			/**
			 * Flat menu
			 */
			unset( $submenu_item_classes[ 'sub_menu_item_wpglobus_menu_switch' ] );

			$item->classes     							  = $submenu_item_classes;
			$item->classes[ 'wpglobus-current-language' ] = 'wpglobus-current-language';
			$item->classes[ 'page_item_wpglobus_menu_switch_' . $item->language ] = 'page_item_wpglobus_menu_switch_' . $item->language;

			$wpglobus_menu_items[]  = $item;

			$template = '{{parent}}{{children}}';

			foreach ( $extra_languages as $language ) :
				/**
				 * Build the top-level menu link for extra language
				 */
				$url               = WPGlobus_Utils::localize_current_url( $language );
				$flag_name         = $this->_get_flag_name( $language );
				$span_classes_lang = $this->_get_language_classes( $language );

				$link_text = '<span class="' . implode( ' ', $span_classes_lang ) . '">' .
				             esc_html( $flag_name ) . '</span>';
				$a_tag     = '<a class="wpglobus-selector-link" href="' . esc_url( $url ) . '">' . $link_text . '</a>';


				$item                   = new stdClass();
				$item->item_has_parent	= false;
				$item->title            = $a_tag;
				$item->url         		= $url;
				$item->classes     		= $submenu_item_classes;
				$item->classes[]   		= 'page_item_wpglobus_menu_switch_' . $language;
				$item->language    		= $language;

				$item->object = 'custom';

				$wpglobus_menu_items[] = new WP_Post( $item );

			endforeach;

		}    // $dropdown_menu

		/**
		 * Filter wpglobus selector items.
		 * Returning array.
		 * @since 1.5.8
		 *
		 * @param array $wpglobus_menu_items 		An array of selector items.
		 * @param array $extra_languages          	An array of extra languages.
		 */
		$wpglobus_menu_items = apply_filters( 'wpglobus_page_menu_items', $wpglobus_menu_items, $extra_languages );

		$parent		= '';
		$children 	= '';

		foreach( $wpglobus_menu_items as $item ) :

			if ( $dropdown_menu ) {

				if ( ! $item->item_has_parent ) {
					$parent = 'class="' . implode( ' ', $item->classes ) . '">' . $item->title;
					continue;
				}

				$children .= '<li class="' . implode( ' ', $item->classes ) . '">' . $item->title . '</li>';

			} else {

				$children .= '<li class="' . implode( ' ', $item->classes ) . '">' . $item->title . '</li>';

			}

		endforeach;

		$selector_html = str_replace( '{{parent}}', $parent , $template );
		$selector_html = str_replace( '{{children}}', $children, $selector_html );

		/**
		 * Filter the HTML content for language selector.
		 *
		 * @param string $selector_html 		  The HTML content for the navigation menu.
		 * @param array $wpglobus_menu_items      An array containing selector element.
		 */
		return $output . apply_filters( 'wpglobus_page_menu_items_html', $selector_html, $wpglobus_menu_items );

	}

	/**
	 * Append language switcher dropdown to a navigation menu, which was created with
	 * @see wp_list_pages
	 *
	 * @deprecated from 1.5.8
	 *
	 * @param string $output The menu HTML string
	 * @return string HTML with appended switcher
	 */
	public function on_wp_list_pages( $output ) {

		if (
			/**
			 * Filter to use 'filter__wp_list_pages' instead of 'on_wp_list_pages'.
			 *
			 * @since 1.5.8
			 * @param bool   true  If to use filter
			 * @return bool
			 */
			apply_filters( 'wpglobus_filter_wp_list_pages', true )
		) {
			return $this->filter__wp_list_pages( $output );
		}

		/**
		 * WPGlobus Configuration setting in admin. Must be "ON" to process.
		 */
		if ( ! WPGlobus::Config()->selector_wp_list_pages ) {
			return $output;
		}

		$current_url      = WPGlobus_Utils::current_url();
		$current_language = WPGlobus::Config()->language;

		/**
		 * List of the languages to show in the drop-down.
		 * These are all enabled languages, except for the current one.
		 * The current one will be shown at the top.
		 */
		$extra_languages = array_diff(
			WPGlobus::Config()->enabled_languages, (array) $current_language );

		/**
		 * Filter extra languages.
		 * Returning array.
		 * @since 1.0.13
		 *
		 * @param array  $extra_languages  An array with languages to show in the dropdown.
		 * @param string $current_language The current language.
		 */
		$extra_languages = apply_filters(
			'wpglobus_extra_languages', $extra_languages, $current_language );

		/**
		 * Filter to show dropdown menu or not.
		 * Returning boolean.
		 * @since 1.2.2
		 *
		 * @param bool
		 * @param WPGlobus_Config
		 */
		$dropdown_menu = apply_filters( 'wpglobus_dropdown_menu', true, WPGlobus::Config() );


		/**
		 * Build the top-level menu link
		 */
		$language          = $current_language;
		$url               = WPGlobus_Utils::localize_url( $current_url, $language );
		$flag_name         = $this->_get_flag_name( $language );
		$span_classes_lang = $this->_get_language_classes( $language );

		$link_text = '<span class="' . implode( ' ', $span_classes_lang ) . '">' .
		             esc_html( $flag_name ) . '</span>';
		$a_tag     = '<a class="wpglobus-selector-link" href="' . esc_url( $url ) . '">' . $link_text . '</a>';


		if ( $dropdown_menu ) {

			$output .= '<li class="page_item page_item_wpglobus_menu_switch page_item_has_children wpglobus-current-language page_item_wpglobus_menu_switch_' . $language . '">' .
			           $a_tag .
			           '<ul class="children">';

			foreach ( $extra_languages as $language ) :
				/**
				 * Build the drop-down menu links for extra language
				 */
				$url               = WPGlobus_Utils::localize_current_url( $language );
				$flag_name         = $this->_get_flag_name( $language );
				$span_classes_lang = $this->_get_language_classes( $language );

				$link_text = '<span class="' . implode( ' ', $span_classes_lang ) . '">' .
				             esc_html( $flag_name ) . '</span>';
				$a_tag     = '<a class="wpglobus-selector-link" href="' . esc_url( $url ) . '">' . $link_text . '</a>';

				$output .= '<li class="page_item page_item_wpglobus_menu_switch_' . $language . '">' .
				           $a_tag .
				           '</li>';
			endforeach;

			$output .= '</ul>' .
			           '</li>';

		} else {

			$output .= '<li class="page_item page_item_wpglobus_menu_switch wpglobus-current-language page_item_wpglobus_menu_switch_' . $language . '">' .
			           $a_tag .
			           '</li>';

			foreach ( $extra_languages as $language ) :
				/**
				 * Build the top-level menu link for extra language
				 */
//				$url                 = WPGlobus_Utils::localize_url( $current_url, $language );
				$url               = WPGlobus_Utils::localize_current_url( $language );
				$flag_name         = $this->_get_flag_name( $language );
				$span_classes_lang = $this->_get_language_classes( $language );

				$link_text = '<span class="' . implode( ' ', $span_classes_lang ) . '">' .
				             esc_html( $flag_name ) . '</span>';
				$a_tag     = '<a class="wpglobus-selector-link" href="' . esc_url( $url ) . '">' . $link_text . '</a>';

				$output .= '<li class="page_item page_item_wpglobus_menu_switch page_item_wpglobus_menu_switch_' . $language . '">' .
				           $a_tag .
				           '</li>';
			endforeach;

		}    // $dropdown_menu

		return $output;
	}

	/**
	 * Add language switcher to navigation menu
	 *
	 * @param array  $sorted_menu_items
	 * @param stdClass $args An object containing wp_nav_menu() arguments.
	 *
	 * @return array
	 * @see wp_nav_menu()
	 */
	public function on_add_item(
		$sorted_menu_items, /** @noinspection PhpUnusedParameterInspection */
		$args
	) {

		if ( empty( WPGlobus::Config()->nav_menu ) ) {
			/**
			 * User can use WPGlobus widget
			 * @since 1.0.7
			 */
			$disable_add_selector = true;

		} elseif ( 'all' === WPGlobus::Config()->nav_menu ) {
			/**
			 * Attach to every nav menu
			 * @since 1.0.7
			 */
			$disable_add_selector = false;

		} else {

			$items = array();
			foreach ( $sorted_menu_items as $item ) {
				$items[] = $item->ID;
			}

			$disable_add_selector = true;
			foreach ( $this->menus as $key => $menu ) {
				$diff = array_diff( $items, $menu->menu_items );
				if ( empty( $diff ) && WPGlobus::Config()->nav_menu === $menu->slug ) {
					$disable_add_selector = false;
					break;
				}
			}

		}

		/**
		 * Filter to add or not language selector to the menu.
		 * Returning boolean.
		 * @since 1.5.8
		 *
		 * @param bool 	$disable_add_selector 	Disable or not to add language selector to the menu.
		 * @param stdClass 	$args 					An object containing wp_nav_menu() arguments.
		 */
		$disable_add_selector = apply_filters( 'wpglobus_menu_add_selector', $disable_add_selector, $args );

		if ( $disable_add_selector ) {
			return $sorted_menu_items;
		}

		/**
		 * List of all languages, except the main one.
		 *
		 * @var string[] $extra_languages
		 */
		$extra_languages = array();
		foreach ( WPGlobus::Config()->enabled_languages as $languages ) {
			if ( $languages !== WPGlobus::Config()->language ) {
				$extra_languages[] = $languages;
			}
		}

		/**
		 * Filter extra languages.
		 * Returning array.
		 * @since 1.0.13
		 *
		 * @param array $extra_languages An array of languages to show in the menu.
		 * @param       string           WPGlobus::Config()->language The current language.
		 */
		$extra_languages = apply_filters( 'wpglobus_extra_languages', $extra_languages, WPGlobus::Config()->language );

		// Main menu item classes.
		$menu_item_classes = array(
			'',
			'menu-item',
			'menu-item-type-custom',
			'menu-item-object-custom',
			'menu_item_wpglobus_menu_switch',
			'wpglobus-selector-link'
		);

		// Submenu item classes.
		$submenu_item_classes = array(
			'',
			'menu-item',
			'menu-item-type-custom',
			'menu-item-object-custom',
			'sub_menu_item_wpglobus_menu_switch',
			'wpglobus-selector-link'
		);

		if (
			/**
			 * Filter to show the language switcher as a dropdown (default) or plain menu.
			 *
			 * @since 1.2.2
			 *
			 * @param bool   true If false then no dropdown
			 * @param WPGlobus_Config
			 *
			 * @return bool Value of the first parameter, possibly updated by the filter
			 */
		apply_filters( 'wpglobus_dropdown_menu', true, WPGlobus::Config() )
		) {
			$parent_item_ID = 9999999999; # 9 999 999 999
		} else {
			$parent_item_ID = 0;
		}

		$span_classes_lang = $this->_get_language_classes( WPGlobus::Config()->language );

		$current_url = WPGlobus_Utils::current_url();

		$item                   = new stdClass();
		$item->ID               = 0 === $parent_item_ID ? 'wpglobus_menu_switch_' . WPGlobus::Config()->language : $parent_item_ID;
		$item->db_id            = $item->ID;
		$item->object_id        = $item->ID;
		$item->object           = 'custom';
		$item->menu_item_parent = 0;
		$item->title            =
			'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( WPGlobus::Config()->language ) . '</span>';
		// The top menu level points to the current URL. Useless? Maybe good for refresh.
		$item->url         = $current_url;
		$item->classes     = $menu_item_classes;
		$item->classes[]   = 'wpglobus-current-language';
		$item->description = '';
		$item->language    = WPGlobus::Config()->language;

		$wpglobus_menu_items[] = new WP_Post($item);

		foreach ( $extra_languages as $language ) {
			$span_classes_lang      = $this->_get_language_classes( $language );
			$item                   = new stdClass();
			$item->ID               = 'wpglobus_menu_switch_' . $language;
			$item->db_id            = $item->ID;
			$item->object_id        = $item->ID;
			$item->object           = 'custom';
			$item->menu_item_parent = $parent_item_ID;
			$item->title            =
				'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span>';
			// This points to the URL localized for the selected language.
			$item->url         = WPGlobus_Utils::localize_current_url( $language );
			$item->classes     = 0 === $parent_item_ID ? $menu_item_classes : $submenu_item_classes;
			$item->description = '';
			$item->language    = $language;

			$wpglobus_menu_items[] = new WP_Post( $item );
		}

		$languages = $extra_languages;
		array_unshift( $languages, WPGlobus::Config()->language );

		return array_merge(
			$sorted_menu_items,

			/**
			 * This filter can be used to change the order of languages.
			 *
			 * @since 1.2.2
			 *
			 * @param array $wpglobus_menu_items All WPGlobus menu items.
			 * @param array $languages           All languages, including current.
			 * @return array The filtered list.
			 */
			apply_filters( 'wpglobus_menu_items', $wpglobus_menu_items, $languages )
		);
	}

	/**
	 * Get flag name for navigation menu
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function _get_flag_name( $language ) {

		switch ( WPGlobus::Config()->show_flag_name ) {
			case 'full_name' :
				$flag_name = WPGlobus::Config()->language_name[ $language ];
				break;
			case 'name' :
				$flag_name = WPGlobus::Config()->language_name[ $language ];
				break;
			case 'code' :
				$flag_name = $language;
				break;
			default:
				$flag_name = '';
		}

		return $flag_name;

	}

	/**
	 * Get language's classes
	 * @since 1.2.1
	 *
	 * @param string $language
	 *
	 * @return array
	 */
	public function _get_language_classes( $language = '' ) {

		$class = array(
			'wpglobus_flag',
			'wpglobus_language_name'
		);

		if ( ! empty( $language ) ) {
			$class[] = 'wpglobus_flag_' . $language;
		}

		switch ( WPGlobus::Config()->show_flag_name ) {
			case 'full_name' :
				/* without flag */
				$class = array(
					'wpglobus_language_full_name'
				);
				break;
		}

		return $class;
	}

	/**
	 * Get navigation menus
	 * @return array
	 */
	public static function _get_nav_menus() {
		/** @global wpdb $wpdb */
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}terms AS t
					  LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id
					  WHERE tt.taxonomy = 'nav_menu'";

		$menus = $wpdb->get_results( $query );

		foreach ( $menus as $key => $menu ) {

			$result =
				$wpdb->get_results( $wpdb->prepare( "SELECT object_id FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = %d ORDER BY object_id ASC", $menu->term_id ), OBJECT_K );

			$result = array_keys( $result );

			$menus[ $key ]->menu_items = $result;

		}

		return $menus;

	}

	/**
	 * Added wp_editor for enabled languages at post.php page
	 * @see action edit_form_after_editor in wp-admin\edit-form-advanced.php:542
	 *
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	public function on_add_wp_editors( $post ) {

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		}

		if ( ! post_type_supports( $post->post_type, 'editor' ) ) {
			return;
		}

		foreach ( WPGlobus::Config()->open_languages as $language ) :
			if ( $language == WPGlobus::Config()->default_language ) {

				continue;

			} else {

				$last_user = get_userdata( get_post_meta( $post->ID, '_edit_last', true ) );
				?>

				<div id="postdivrich-<?php echo esc_attr( $language ); ?>"
				     class="postarea <?php echo esc_attr( apply_filters( 'wpglobus_postdivrich_class', 'postdivrich-wpglobus', $language ) ); ?>"
				     style="<?php echo esc_attr( apply_filters( 'wpglobus_postdivrich_style', '', $language ) ); ?>">    <?php
					wp_editor( WPGlobus_Core::text_filter( $post->post_content, $language, WPGlobus::RETURN_EMPTY ), 'content_' . $language, array(
						'_content_editor_dfw' => true,
						#'dfw' => true,
						'drag_drop_upload'    => true,
						'tabfocus_elements'   => 'insert-media-button,save-post',
						'editor_height'       => 300,
						'editor_class'        => 'wpglobus-editor',
						'tinymce'             => array(
							'resize'             => true,
							'wp_autoresize_on'   => true,
							'add_unload_trigger' => false,
							#'readonly' => true /* @todo for WPGlobus Authors */
						),
					) );

					/**
					 * Add post status info table
					 * @since 1.0.13
					 */
					?>
					<table id="post-status-info-<?php echo esc_attr( $language ); ?>" class="wpglobus-post-status-info">
						<tbody>
						<tr>
							<td id="wp-word-count-<?php echo esc_attr( $language ); ?>"
							    class="wpglobus-wp-word-count"><?php printf(
							    	esc_html__( 'Word count: %s' ), '<span class="word-count-' . esc_attr( $language ) . '">0</span>' ); ?></td>
							<td class="autosave-info">

								<span class="autosave-message">&nbsp;</span>
								<?php
								if ( 'auto-draft' != $post->post_status ) {
									echo '<span id="last-edit">';
									if ( $last_user ) {
										printf(
											esc_html__( 'Last edited by %1$s on %2$s at %3$s' ),
											esc_html( $last_user->display_name ),
											esc_html( mysql2date( get_option( 'date_format' ), $post->post_modified ) ),
											esc_html( mysql2date( get_option( 'time_format' ), $post->post_modified ) )
										);
									} else {
										printf(
											esc_html__(  'Last edited on %1$s at %2$s' ),
											esc_html( mysql2date( get_option( 'date_format' ), $post->post_modified ) ),
											esc_html( mysql2date( get_option( 'time_format' ), $post->post_modified ) )
										);
									}
									echo '</span>';
								} ?>

							</td>
							<td id="content-resize-handle-<?php echo esc_attr( $language ); ?>"
							    class="wpglobus-content-resize-handle hide-if-no-js"><br /></td>
						</tr>
						</tbody>
					</table>

				</div> <?php // .postarea .postdivrich-wpglobus

			}
		endforeach;
	}

	/**
	 * Surround text with language tags
	 *
	 * @param string $text
	 * @param string $language
	 *
	 * @return string
	 */
	public static function add_locale_marks( $text, $language ) {
		return sprintf( WPGlobus::LOCALE_TAG, $language, $text );
	}

	/**
	 * @param array    $data
	 * @param string[] $postarr
	 *
	 * @return array
	 */
	public function on_save_post_data( $data, $postarr ) {

		if ( 'revision' == $postarr['post_type'] ) {
			/**
			 * Don't work with revisions
			 * note: revision there are 2 types, its have some differences
			 *        - [post_name] => {post_id}-autosave-v1    and [post_name] => {post_id}-revision-v1
			 *        autosave         : when [post_name] == {post_id}-autosave-v1  $postarr has [post_content] and [post_title] in default_language
			 *        regular revision : [post_name] == {post_id}-revision-v1 $postarr has [post_content] and [post_title] in all enabled languages with delimiters
			 * @see https://codex.wordpress.org/Revision_Management
			 * see $postarr for more info
			 */
			return $data;
		}

		if ( 'auto-draft' == $postarr['post_status'] ) {
			/**
			 * Auto draft was automatically created with no data
			 */
			return $data;
		}

		if ( $this->disabled_entity( $data['post_type'] ) ) {
			return $data;
		}

		/** @global string $pagenow */
		global $pagenow;

		/**
		 * Now we save post content and post title for all enabled languages for post.php, post-new.php
		 * @todo Check also 'admin-ajax.php', 'nav-menus.php', etc.
		 */
		$enabled_pages[] = 'post.php';
		$enabled_pages[] = 'post-new.php';

		if ( ! in_array( $pagenow, $enabled_pages ) ) {
			/**
			 * See other places with the same bookmark.
			 * @bookmark EDITOR_LINE_BREAKS
			 */
			//			$data['post_content'] = trim( $data['post_content'], '</p><p>' );

			return $data;
		}

		if ( 'trash' === $postarr['post_status'] ) {
			/**
			 * Don't work with move to trash
			 */
			return $data;
		}

		if ( isset( $_GET['action'] ) && 'untrash' === $_GET['action'] ) { // WPCS: input var ok, sanitization ok.
			/**
			 * Don't work with untrash
			 */
			return $data;
		}

		$devmode = false;
		if ( 'off' == WPGlobus::Config()->toggle ) {
			$devmode = true;
		}

		if ( ! $devmode ) :

			$support_title = true;
			if ( ! post_type_supports( $data['post_type'], 'title' ) ) {
				$support_title = false;
			}

			$support_editor = true;
			if ( ! post_type_supports( $data['post_type'], 'editor' ) ) {
				$support_editor = false;
			}

			$data['post_title'] = $post_title = trim( $data['post_title'] );
			if ( ! empty( $data['post_title'] ) && $support_title ) {
				$data['post_title'] =
					WPGlobus::add_locale_marks( $data['post_title'], WPGlobus::Config()->default_language );
			}

			$data['post_content'] = $post_content = trim( $data['post_content'] );
			if ( ! empty( $data['post_content'] ) && $support_editor ) {
				$data['post_content'] =
					WPGlobus::add_locale_marks( $data['post_content'], WPGlobus::Config()->default_language );
			}

			/**
			 * Add variables for check extra data
			 * @since 1.2.2
			 */
			$has_extra_post_title   = false;
			$has_extra_post_content = false;

			foreach ( WPGlobus::Config()->open_languages as $language ) :
				if ( $language == WPGlobus::Config()->default_language ) {

					continue;

				} else {

					/**
					 * Join post title for opened languages
					 */
					$title =
						isset( $postarr[ 'post_title_' . $language ] ) ? trim( $postarr[ 'post_title_' . $language ] ) : '';
					if ( ! empty( $title ) ) {
						$data['post_title'] .= WPGlobus::add_locale_marks( $postarr[ 'post_title_' . $language ], $language );
						$has_extra_post_title = true;
					}

					/**
					 * Join post content for opened languages
					 */
					$content =
						isset( $postarr[ 'content_' . $language ] ) ? trim( $postarr[ 'content_' . $language ] ) : '';
					if ( ! empty( $content ) ) {
						$data['post_content'] .= WPGlobus::add_locale_marks( $postarr[ 'content_' . $language ], $language );
						$has_extra_post_content = true;
					}

				}
			endforeach;

		endif;  //  $devmode

		if ( ! $has_extra_post_title ) {
			$data['post_title'] = $post_title;
		}

		if ( ! $has_extra_post_content ) {
			$data['post_content'] = $post_content;
		}

		$data = apply_filters( 'wpglobus_save_post_data', $data, $postarr, $devmode );

		return $data;

	}

	/**
	 * Add wrapper for every table in enabled languages at edit-tags.php page
	 * @return void
	 */
	public function on_add_taxonomy_form_wrapper() {
		foreach ( WPGlobus::Config()->enabled_languages as $language ) {
			$classes = 'hidden'; ?>
			<div id="taxonomy-tab-<?php echo esc_attr( $language ); ?>" data-language="<?php echo esc_attr( $language ); ?>"
			     class="<?php echo esc_attr( $classes ); ?>">
			</div>
			<?php
		}

	}

	/**
	 * Add language tabs for edit taxonomy name at edit-tags.php page
	 *
	 * @param $object
	 * @param $taxonomy
	 */
	public function on_add_language_tabs_edit_taxonomy(
		$object, /** @noinspection PhpUnusedParameterInspection */
		$taxonomy
	) {

		if ( $this->disabled_entity() ) {
			return;
		} ?>
		<div class="wpglobus-taxonomy-tabs">
		<ul class="wpglobus-taxonomy-tabs-list">    <?php
			foreach ( self::Config()->open_languages as $language ) {
				$return =
					$language == WPGlobus::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;
				?>
				<li id="wpglobus-link-tab-<?php echo esc_attr( $language ); ?>" class=""
				    data-language="<?php echo esc_attr( $language ); ?>"
				    data-name="<?php echo esc_attr( WPGlobus_Core::text_filter( $object->name, $language, $return ) ); ?>"
				    data-description="<?php echo esc_attr( WPGlobus_Core::text_filter( $object->description, $language, $return ) ); ?>">
					<a href="#taxonomy-tab-<?php echo esc_attr( $language ); ?>"><?php echo esc_html( self::Config()->en_language_name[ $language ] ); ?></a>
				</li> <?php
			} ?>
		</ul>
		</div><?php
	}

	/**
	 * Add language tabs for jQueryUI
	 * @return void
	 */
	public function on_add_language_tabs() {

		/** @global WP_Post $post */
		global $post;

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		}

		if (
			/**
			 * Filter to show language tabs in post page.
			 * @since 1.5.5
			 *
			 * @param bool
			 * Returning boolean.
			 */
		apply_filters( 'wpglobus_show_language_tabs', true )
		) : ?>

			<ul class="wpglobus-post-body-tabs-list">    <?php
				$order = 0;
				foreach ( self::Config()->open_languages as $language ) {
					$tab_suffix = $language == self::Config()->default_language ? 'default' : $language; ?>
					<li id="link-tab-<?php echo esc_attr( $tab_suffix ); ?>" data-language="<?php echo esc_attr( $language ); ?>"
					    data-order="<?php echo esc_attr( $order ); ?>"
					    class="wpglobus-post-tab">
						<a href="#tab-<?php echo esc_attr( $tab_suffix ); ?>"><?php echo esc_html( self::Config()->en_language_name[ $language ] ); ?></a>
					</li> <?php
					$order ++;
				} ?>
			</ul>    <?php

		endif;

	}

	/**
	 * Add title fields for enabled languages at post.php, post-new.php page
	 *
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	public function on_add_title_fields( $post ) {

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		}

		/**
		 * Check for support 'title'
		 */
		if ( ! post_type_supports( $post->post_type, 'title' ) ) {
			return;
		}

		foreach ( self::Config()->open_languages as $language ) :

			if ( $language == self::Config()->default_language ) {

				continue;

			} else { ?>

				<div id="titlediv-<?php echo esc_attr( $language ); ?>" class="titlediv-wpglobus">
					<div id="titlewrap-<?php echo esc_attr( $language ); ?>" class="titlewrap-wpglobus">
						<label class="screen-reader-text" id="title-prompt-text-<?php echo esc_attr( $language ); ?>"
						       for="title_<?php echo esc_attr( $language ); ?>"><?php echo esc_html( apply_filters( 'enter_title_here',
								esc_html__( 'Enter title here' ), $post ) ); ?></label>
						<input type="text" name="post_title_<?php echo esc_attr( $language ); ?>" size="30"
						       value="<?php echo esc_attr( WPGlobus_Core::text_filter( $post->post_title, $language, WPGlobus::RETURN_EMPTY ) ); ?>"
						       id="title_<?php echo esc_attr( $language ); ?>"
						       class="title_wpglobus"
						       data-language="<?php echo esc_attr( $language ); ?>"
						       autocomplete="off" />
					</div> <!-- #titlewrap -->
					<?php
					$slug_box = '<div class="inside">
						<div id="edit-slug-box-' . esc_attr( $language ) . '" class="wpglobus-edit-slug-box hide-if-no-js">
							<b></b>
						</div>
					</div><!-- .inside -->';
					// DO NOT ESCAPE THIS: it's HTML, already escaped above.
					echo apply_filters( 'wpglobus_edit_slug_box', $slug_box, $language ); // WPCS: XSS ok.
					?>
				</div>    <!-- #titlediv -->    <?php

			}

		endforeach;
	}

	/**
	 * Check for disabled post_types, taxonomies
	 *
	 * @param string $entity
	 *
	 * @return boolean
	 */
	public function disabled_entity( $entity = '' ) {

		$entity_type = 'post';

		if ( empty( $entity ) ) {
			/**
			 * Try get entity from url. Ex. edit-tags.php?taxonomy=product_cat&post_type=product
			 */
			if ( isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] ) ) { // WPCS: input var ok, sanitization ok.
				$entity = sanitize_text_field( wp_unslash( $_GET['post_type'] ) ); // WPCS: input var ok.
			}
			if ( empty( $entity ) && isset( $_GET['taxonomy'] ) && is_string( $_GET['taxonomy'] ) ) { // WPCS: input var ok, sanitization ok.
				$entity      = sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ); // WPCS: input var ok.
				$entity_type = 'taxonomy';
			}
			if ( empty( $entity ) && WPGlobus_WP::is_pagenow( 'edit.php' ) ) {
				$entity = 'post';
			}
		}

		if ( 'post' === $entity_type ) {
			/**
			 * Check for support 'title' and 'editor'
			 */
			/** @global WP_Post $post */
			global $post;

			$post_type = '';

			if ( ! empty( $post ) && is_object( $post ) ) {
				$post_type = $post->post_type;
			}

			/**
			 * Filter to define post type.
			 *
			 * Some plugins may rewrite global $post, e.g. @see https://wordpress.org/plugins/geodirectory/
			 * so user need to try define and return correct post type using filter to avoid PHP Notice: Trying to get property of non-object.
			 *
			 * @since 1.6.2
			 *
			 * @param string $post_type Post type.
			 * @param array  $post   	An object WP_Post.
			 *
			 * @return string.
			 */
			$post_type = apply_filters( 'wpglobus_user_defined_post_type', $post_type, $post );

			if ( ! empty( $post_type ) ) {
				if ( ! empty( $post ) && ! post_type_supports( $post_type, 'title' ) && ! post_type_supports( $post_type, 'editor' ) ) {
					return true;
				}
			}
		}

		/**
		 * Filter the array of disabled entities returned for load tabs, scripts, styles.
		 * @since 1.7.6
		 *
		 * @see 'wpglobus_disabled_entities' filter in 'admin_init' action.
		 *
		 * @param array $disabled_entities Array of disabled entities.
		 * @return boolean
		 */
		$this->disabled_entities = apply_filters( 'wpglobus_disabled_entities', $this->disabled_entities );

		if ( in_array( $entity, $this->disabled_entities ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Get raw term names for $taxonomy
	 * @todo This method should be somewhere else
	 *
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public static function _get_terms( $taxonomy = '' ) {

		if ( empty( $taxonomy ) ) {
			return array();
		}

		if ( ! taxonomy_exists( $taxonomy ) ) {
			$error = new WP_Error( 'invalid_taxonomy',
				__( 'Invalid taxonomy' ) );

			return $error;
		}

		remove_filter( 'get_terms', array( 'WPGlobus_Filters', 'filter__get_terms' ), 11 );

		$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

		add_filter( 'get_terms', array( 'WPGlobus_Filters', 'filter__get_terms' ), 11 );

		$term_names = array();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_names[ WPGlobus_Core::text_filter( $term->name, self::Config()->default_language ) ] =
					$term->name;
				/**
				 * In admin self::Config()->language is the same as result get_locale()
				 */
				$term_names[ WPGlobus_Core::text_filter( $term->name, self::Config()->language ) ] = $term->name;
			}
		}

		return $term_names;

	}

	/**
	 * Make correct title for admin pages
	 *
	 * @param string $admin_title Ignored
	 * @param string $title
	 *
	 * @return string
	 */
	public function on_admin_title(
		/** @noinspection PhpUnusedParameterInspection */
		$admin_title,
		$title
	) {
		$blogname = get_option( 'blogname' );

		return $title . ' &lsaquo; ' . WPGlobus_Core::text_filter( $blogname, WPGlobus::Config()->language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE ) . ' &#8212; WordPress';
	}

	/**
	 * Make correct Site Title in admin bar.
	 * Make template for Site Title (option blogname)
	 * a Tagline (option blogdescription) at options-general.php page.
	 * @return void
	 */
	public function on_admin_footer() {

		$blogname = get_option( 'blogname' );
		$bn       =
			WPGlobus_Core::text_filter( $blogname, WPGlobus::Config()->language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );

		?>
		<script type='text/javascript'>
			/* <![CDATA[ */
			jQuery('#wp-admin-bar-site-name a').eq(0).text("<?php echo esc_js( $bn ); ?>");
			/* ]]> */
		</script>
		<?php


		/**
		 * For dialog form
		 * @since 1.2.0
		 */
		/** @global string $pagenow */
		global $pagenow;

		$page = '';
		if ( isset( $_GET['page'] ) && is_string( $_GET['page'] ) ) { // WPCS: input var ok, sanitization ok.
			$page = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // WPCS: input var ok.
		}

		// @todo remove after testing
		//if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'widgets.php' ) ) ) {

		if ( in_array( $pagenow, $this->enabled_pages ) || in_array( $page, $this->enabled_pages ) ) {
			/**
			 * Output dialog form for window.WPGlobusDialogApp
			 */
			?>
			<div id="wpglobus-dialog-wrapper" class="hidden wpglobus-dialog-wrapper">
				<form id="wpglobus-dialog-form">
					<div id="wpglobus-dialog-tabs" class="wpglobus-dialog-tabs">
						<ul class="wpglobus-dialog-tabs-list">    <?php
							$order = 0;
							foreach ( WPGlobus::Config()->open_languages as $language ) { ?>
								<li id="dialog-link-tab-<?php echo esc_attr( $language ); ?>"
								    data-language="<?php echo esc_attr( $language ); ?>"
								    data-order="<?php echo esc_attr( $order ); ?>"
								    class="wpglobus-dialog-tab"><a
										href="#dialog-tab-<?php echo esc_attr( $language ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>
								</li> <?php
								$order ++;
							} ?>
						</ul> <?php

						foreach ( WPGlobus::Config()->open_languages as $language ) { ?>
							<div id="dialog-tab-<?php echo esc_attr( $language ); ?>" class="wpglobus-dialog-general">
								<textarea name="wpglobus-dialog-<?php echo esc_attr( $language ); ?>"
								          id="wpglobus-dialog-<?php echo esc_attr( $language ); ?>"
								          class="wpglobus_dialog_textarea textarea"
								          data-language="<?php echo esc_attr( $language ); ?>"
								          data-order="save_dialog"
								          placeholder=""></textarea>
							</div> <?php
						} ?>
					</div>
					<div id="wpglobus-dialog-form-footer" style="width:100%;"></div>
				</form>
			</div>        <?php
		}

		if ( ! WPGlobus_WP::is_pagenow( 'options-general.php' ) ) {
			return;
		}

		$blogdesc = get_option( 'blogdescription' );
		?>
		<div id="wpglobus-blogname" class="hidden">        <?php
			foreach ( self::Config()->enabled_languages as $language ) :
				$return =
					$language == self::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY; ?>

				<input type="text" class="regular-text wpglobus-blogname wpglobus-translatable"
				       value="<?php echo esc_attr( WPGlobus_Core::text_filter( $blogname, $language, $return ) ); ?>"
				       id="blogname-<?php echo esc_attr( $language ); ?>" name="blogname-<?php echo esc_attr( $language ); ?>"
				       data-language="<?php echo esc_attr( $language ); ?>"
				       placeholder="<?php echo esc_attr( self::Config()->en_language_name[ $language ] ); ?>"><br />

				<?php
			endforeach; ?>
		</div>

		<div id="wpglobus-blogdescription" class="hidden">        <?php
			foreach ( self::Config()->enabled_languages as $language ) :
				$return =
					$language == self::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY; ?>

				<input type="text" class="regular-text wpglobus-blogdesc wpglobus-translatable"
				       value="<?php echo esc_attr( WPGlobus_Core::text_filter( $blogdesc, $language, $return ) ); ?>"
				       id="blogdescription-<?php echo esc_attr( $language ); ?>" name="blogdescription-<?php echo esc_attr( $language ); ?>"
				       data-language="<?php echo esc_attr( $language ); ?>"
				       placeholder="<?php echo esc_attr( self::Config()->en_language_name[ $language ] ); ?>"><br />

				<?php
			endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Shortcut to avoid globals
	 * @return WPGlobus_Config
	 */
	public static function Config() {

		static $config = null;

		if ( is_null( $config ) ) {
			$config = new WPGlobus_Config();
		}

		return $config;

	}

	/**
	 * Shortcut to avoid globals
	 * @since 1.1.1
	 * @return WPGlobus
	 */
	public static function O() {
		/** @global WPGlobus $WPGlobus */
		global $WPGlobus;

		return $WPGlobus;
	}

	/**
	 * Show notice to admin about permalinks settings
	 * @since 1.0.13
	 */
	public function admin_notice_permalink_structure() {
		?>
		<div class="notice notice-error error">
		<p>
			<?php esc_html_e( 'You must enable Pretty Permalinks to use WPGlobus.', 'wpglobus' ); ?>
			<strong>
				<?php esc_html_e( 'Please go to Settings > Permalinks > Common Settings and choose a non-default option.', 'wpglobus' ); ?>
			</strong>
		</p>
		</div><?php
	}

	/**
	 * Various actions on admin_init hook
	 */
	public function on_admin_init() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			// do nothing
		} else {

			/**
			 * For developers use only. Deletes settings with no warning! Irreversible!
			 * @link wp-admin/admin.php?wpglobus-reset-all-options=1
			 */
			if ( ! empty( $_GET['wpglobus-reset-all-options'] ) ) { // WPCS: input var ok, sanitization ok.
				/** @global wpdb $wpdb */
				global $wpdb;
				$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpglobus_option%';" );
				wp_safe_redirect( admin_url() );
				exit();
			}

			/**
			 * Check for transient wpglobus_activated
			 */
			if ( false !== get_transient( 'wpglobus_activated' ) ) {
				delete_transient( 'wpglobus_activated' );
				wp_redirect( admin_url( add_query_arg( array( 'page' => WPGlobus::PAGE_WPGLOBUS_ABOUT ), 'admin.php' ) ) );
				exit;
			}

			if ( ! get_option( 'permalink_structure' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_permalink_structure' ) );
			}

		}

		/**
		 * Filter the array of disabled entities returned for load tabs, scripts, styles.
		 * @since 1.0.0
		 *
		 * @todo may be remove this filter @see 'wpglobus_disabled_entities' in disabled_entity().
		 *
		 * @param array $disabled_entities Array of disabled entities.
		 */
		$this->disabled_entities = apply_filters( 'wpglobus_disabled_entities', $this->disabled_entities );

		/**
		 * Filter the array of opened languages.
		 * @since 1.0.0
		 *
		 * @param array $open_languages Array of opened languages.
		 */
		WPGlobus::Config()->open_languages = apply_filters( 'wpglobus_open_languages', WPGlobus::Config()->open_languages );

		/**
		 * Filter the array of WPGlobus-enabled pages.
		 * Used to load scripts and styles for WPGlobusCore, WPGlobusDialogApp (JS).
		 * @since 1.2.0
		 *
		 * @param array $enabled_pages Array of enabled pages.
		 */
		$this->enabled_pages = apply_filters( 'wpglobus_enabled_pages', $this->enabled_pages );

	}

	/**
	 * Add class to body in admin
	 * @since 1.0.10
	 * @see   admin_body_class filter
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	public function on_add_admin_body_class( $classes ) {
		return $classes . ' wpglobus-wp-admin';
	}

	/**
	 * Add language selector to admin bar
	 * @since 1.0.8
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function on_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {

		$available_languages = get_available_languages();

		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return;
		}

		$wp_admin_bar->add_menu( array(
			'id'     => 'wpglobus-language-select',
			'parent' => 'top-secondary',
			'title'  => '<span class="ab-icon">' .
			            '<img src="' . WPGlobus::Config()->flags_url .
			            WPGlobus::Config()->flag[ WPGlobus::Config()->language ] . '"/>' .
			            '</span><span class="ab-label">' .
			            WPGlobus::Config()->language_name[ WPGlobus::Config()->language ] .
			            '</span>',
		) );

		$add_more_languages = array();
		foreach ( WPGlobus::Config()->enabled_languages as $language ) :

			if ( WPGlobus::Config()->language === $language ) {
				continue;
			}

			$locale = WPGlobus::Config()->locale[ $language ];

			if ( $locale != 'en_US' ) {
				if ( ! in_array( $locale, $available_languages ) ) {
					$add_more_languages[] = WPGlobus::Config()->language_name[ $language ];
					continue;
				}
			}

			$wp_admin_bar->add_menu( array(
				'parent' => 'wpglobus-language-select',
				'id'     => 'wpglobus-' . $language,
				'title'  => '<span><img src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $language ] . '" /></span>&nbsp;&nbsp;' . WPGlobus::Config()->language_name[ $language ],
				'href'   => admin_url( 'options-general.php' ),
				'meta'   => array(
					'tabindex' => - 1,
					'onclick'  => 'wpglobus_select_lang("' . $locale . '");return false;'
				),
			) );

		endforeach;

		if ( ! empty( $add_more_languages ) ) {
			$title = __( 'Add', 'wpglobus' ) . ' (' . implode( ', ', $add_more_languages ) . ')';
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpglobus-language-select',
				'id'     => 'wpglobus-add-languages',
				'title'  => $title,
				'href'   => admin_url( 'options-general.php' ),
				'meta'   => array(
					'tabindex' => - 1,
				),
			) );
		}
		?>
		<!--suppress AnonymousFunctionJS -->
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function ($) {
				$('#wpglobus-default-locale').on('click', function (e) {
					wpglobus_select_lang('<?php echo esc_js( WPGlobus::Config()->locale[ WPGlobus::Config()->language ] ); ?>');
				});
				wpglobus_select_lang = function (locale) {
					$.post(ajaxurl, {
						action: 'WPGlobus_process_ajax',
						order : {action: 'wpglobus_select_lang', locale: locale}
					}, function (d) {
					})
						.done(function () {
							window.location.reload();
						});
				}
			});
			//]]>
		</script>
		<?php
	}

	/**
	 * Add custom JS to footer section.
	 *
	 * @since 1.7.6
	 * @return void
	 */
	public function on__wp_footer() {

		$js = trim( WPGlobus::Config()->js_editor );

		if ( ! empty( $js ) ) {
			$js = wp_kses( $js, array() );
			$js = str_replace( array('&gt;','&lt;'), array('>','<'), $js );
			?>
			<script type="text/javascript">
				<?php echo $js; ?>
			</script>
			<?php
		}

	}

}

# --- EOF
