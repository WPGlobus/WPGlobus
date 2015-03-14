<?php
/**
 * @package   WPGlobus
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

	const URL_WPGLOBUS_SITE = 'http://www.wpglobus.com/';

	/**
	 * @var string
	 */
	public static $minimalReduxFramework_version = '3.2.9.4';

	/**
	 * Options page slug needed to get access to settings page
	 */
	const OPTIONS_PAGE_SLUG = 'wpglobus_options';

	/**
	 * Language edit page
	 */
	const LANGUAGE_EDIT_PAGE = 'wpglobus_language_edit';

	/**
	 * Language edit page
	 */
	const PAGE_WPGLOBUS_ABOUT = 'wpglobus-about';

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
	 * Initialized at plugin loader
	 * @var string
	 */
	public static $PLUGIN_DIR_URL = '';

	/**
	 * @var bool $_SCRIPT_DEBUG Internal representation of the define('SCRIPT_DEBUG')
	 */
	protected static $_SCRIPT_DEBUG = false;

	/**
	 * @var string $_SCRIPT_SUFFIX Whether to use minimized or full versions of JS and CSS.
	 */
	protected static $_SCRIPT_SUFFIX = '.min';

	/**
	 * @return string
	 */
	public static function SCRIPT_SUFFIX(){
		return self::$_SCRIPT_SUFFIX;
	}

	/**
	 * Are we using our version of Redux or someone else's?
	 * @var string
	 */
	public $redux_framework_origin = 'external';

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
	 * Constructor
	 */
	function __construct() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			self::$_SCRIPT_DEBUG  = true;
			self::$_SCRIPT_SUFFIX = '';
		}

		/** @todo maybe move this action to Class WPGlobus_Upgrade ? */
		add_action( 'admin_init', array(
			$this,
			'on_admin_init'
		) );

		global $WPGlobus_Config, $WPGlobus_Options;

		global $pagenow;

		$this->disabled_entities[] = 'attachment';

		/**
		 * Init array of supported plugins
		 */
		$this->vendors_scripts['ACF'] 		  = false;
		$this->vendors_scripts['WPSEO']       = false;
		$this->vendors_scripts['WOOCOMMERCE'] = false;
		$this->vendors_scripts['AIOSEOP'] 	  = false; // All In One SEO Pack

		if ( function_exists('acf') ) {
			$this->vendors_scripts['ACF'] = true;
			
			/**
			 * @todo  Work on the ACF compatibility is in progress
			 * Temporarily add CPT acf ( Advanced Custom Fields ) to the array of disabled_entities
			 * @see   'wpglobus_disabled_entities' filter for add/remove custom post types to array disabled_entities
			 * @since 1.0.4
			 */
			$this->disabled_entities[] = 'acf';					
		}	
		
		if ( defined( 'WPSEO_VERSION' ) ) {
			$this->vendors_scripts['WPSEO'] = true;
		}

		if ( defined( 'WC_VERSION' ) || defined( 'WOOCOMMERCE_VERSION' ) ) {
			$this->vendors_scripts['WOOCOMMERCE'] = true;
			$this->disabled_entities[]            = 'product';
			$this->disabled_entities[]            = 'product_tag';
			$this->disabled_entities[]            = 'product_cat';
			$this->disabled_entities[]            = 'shop_order';
			$this->disabled_entities[]            = 'shop_coupon';
		}
		
		if ( defined( 'AIOSEOP_VERSION' ) ) {
			$this->vendors_scripts['AIOSEOP'] = true;
		}	
			
		add_filter( 'wp_redirect', array(
			$this,
			'on_wp_redirect'
		) );


		/**
		 * NOTE: do not check for !DOING_AJAX here. Redux uses AJAX, for example, for disabling tracking.
		 * So, we need to load Redux on AJAX requests, too
		 */
		if ( is_admin() ) {

			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array( $this, 'on_process_ajax' ) );

			if ( ! class_exists( 'ReduxFramework' ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once self::$PLUGIN_DIR_PATH . 'vendor/ReduxCore/framework.php';

				/** Set a flag to know that we are using the embedded Redux */
				$this->redux_framework_origin = 'embedded';
			}

			require_once 'options/class-wpglobus-options.php';
			$WPGlobus_Options = new WPGlobus_Options();

			if ( 'edit-tags.php' == $pagenow ) {
				/**
				 * Need to get taxonomy for using correct filter
				 */
				if ( ! empty( $_GET['taxonomy'] ) ) {

					add_action( "{$_GET['taxonomy']}_pre_edit_form", array(
						$this,
						'on_add_language_tabs_edit_taxonomy'
					), 10, 2 );

					add_action( "{$_GET['taxonomy']}_edit_form", array(
						$this,
						'on_add_taxonomy_form_wrapper'
					), 10, 2 );

				}
			}

			if ( self::Config()->toggle == 'on' || ! $this->user_can( 'wpglobus_toggle' ) ) {

				/**
				 * Four filters for adding language column to edit.php page
				 */
				if ( ! $this->disabled_entity() ) {
					add_filter( 'manage_posts_columns', array(
						$this,
						'on_add_language_column'
					), 10 );

					add_filter( 'manage_pages_columns', array(
						$this,
						'on_add_language_column'
					), 10 );

					add_filter( 'manage_posts_custom_column', array(
						$this,
						'on_manage_language_column'
					), 10 );

					add_filter( 'manage_pages_custom_column', array(
						$this,
						'on_manage_language_column'
					), 10 );
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

				if ( $this->vendors_scripts['WPSEO'] ) {
					add_action( 'wpseo_tab_content', array(
						$this,
						'on_wpseo_tab_content'
					), 11 );
				}

				if ( $this->vendors_scripts['AIOSEOP'] && WPGlobus_WP::is_pagenow(array('post.php','edit.php')) ) {
					
					/** @global $post */
					global $post;

					$type = empty( $post ) ? '' : $post->post_type;
					if ( ! $this->disabled_entity( $type ) ) {
					
						require_once 'vendor/class-wpglobus-aioseop.php';
						if ( WPGlobus_WP::is_pagenow('post.php') ) {
							$WPGlobus_aioseop = new WPGlobus_aioseop();	
						}
					}		
					
				}	

			}    // endif $devmode

			add_action( 'admin_print_styles', array(
				$this,
				'on_admin_styles'
			) );

			add_filter( "redux/{$WPGlobus_Config->option}/field/class/table", array(
				$this,
				'on_field_table'
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
			
		} else {
			$WPGlobus_Config->url_info = WPGlobus_Utils::extract_url(
				$_SERVER['REQUEST_URI'],
				$_SERVER['HTTP_HOST'],
				isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ''
			);

			$WPGlobus_Config->language = $WPGlobus_Config->url_info['language'];

			$this->menus = self::_get_nav_menus();

			/** @todo */
			//			add_filter( 'wp_list_pages', array(
			//				$this,
			//				'on_wp_list_pages'
			//			), 99, 2 );

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

			add_action( 'wp_head', array(
				$this,
				'on_add_hreflang'
			), 11 );

			add_action( 'wp_print_styles', array(
				$this,
				'on_wp_styles'
			) );

			add_action( 'wp_print_styles', array(
				$this,
				'on_wp_scripts'
			) );
		}

	}

	/**
	 * Insert language title to edit.php page
	 *
	 * @param array $posts_columns
	 *
	 * @return array
	 */
	function on_add_language_column( $posts_columns ) {

		/**
		 * What column we insert after?
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

		return $posts_columns;

	}

	/**
	 * Insert flags to every item at edit.php page
	 *
	 * @param $column_name
	 */
	function on_manage_language_column( $column_name ) {

		if ( 'wpglobus_languages' == $column_name ) {
			global $post;
			foreach ( WPGlobus::Config()->enabled_languages as $l ) {
				if ( 1 == preg_match( "/(\{:|\[:|<!--:)[$l]{2}/", $post->post_title . $post->post_content ) ) {
					echo '<img title="' . WPGlobus::Config()->en_language_name[ $l ] . '" src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $l ] . '" /><br />';
				}
			}
		}

	}

	/**
	 * Add language tabs to wpseo metabox ( .wpseo-metabox-tabs-div )
	 * @return void
	 */
	function on_wpseo_tab_content() {

		/** @global $post */
		global $post;

		$type = empty( $post ) ? '' : $post->post_type;
		if ( $this->disabled_entity( $type ) ) {
			return;
		}

		$permalink = array();
		if ( 'publish' == $post->post_status ) {
			$permalink['url']    = get_permalink( $post->ID );
			$permalink['action'] = 'complete';
		} else {
			$permalink['url']    = trailingslashit( home_url() );
			$permalink['action'] = '';
		}
		?>

		<div id="wpglobus-wpseo-tabs">    <?php
			/**
			 * Use span with attributes 'data' for send to js script ids, names elements for which needs to be set new ids, names with language code.
			 */ ?>
			<span id="wpglobus-wpseo-attr"
			      data-ids="wpseosnippet,wpseosnippet_title,yoast_wpseo_focuskw,focuskwresults,yoast_wpseo_title,yoast_wpseo_title-length-warning,yoast_wpseo_metadesc,yoast_wpseo_metadesc-length,yoast_wpseo_metadesc_notice"
			      data-names="yoast_wpseo_focuskw,yoast_wpseo_title,yoast_wpseo_metadesc"
			      data-qtip="snippetpreviewhelp,focuskwhelp,titlehelp,metadeschelp">
			</span>
			<ul class="wpglobus-wpseo-tabs-ul">    <?php
				$order = 0;
				foreach ( self::Config()->open_languages as $language ) { ?>
					<li id="wpseo-link-tab-<?php echo $language; ?>"
					    data-language="<?php echo $language; ?>"
					    data-order="<?php echo $order; ?>"
					    class="wpglobus-wpseo-tab"><a
							href="#wpseo-tab-<?php echo $language; ?>"><?php echo self::Config()->en_language_name[ $language ]; ?></a>
					</li> <?php
					$order ++;
				} ?>
			</ul>    <?php

			foreach ( self::Config()->open_languages as $language ) {
				$url        = WPGlobus_Utils::localize_url( $permalink['url'], $language );
				$metadesc   = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );
				$wpseotitle = get_post_meta( $post->ID, '_yoast_wpseo_title', true );
				$focuskw    = get_post_meta( $post->ID, '_yoast_wpseo_focuskw', true ); ?>
				<div id="wpseo-tab-<?php echo $language; ?>" class="wpglobus-wpseo-general"
				     data-language="<?php echo $language; ?>"
				     data-url-<?php echo $language; ?>="<?php echo $url; ?>"
				     data-permalink="<?php echo $permalink['action']; ?>"
				     data-metadesc="<?php echo WPGlobus_Core::text_filter( $metadesc, $language, WPGlobus::RETURN_EMPTY ); ?>"
				     data-wpseotitle="<?php echo WPGlobus_Core::text_filter( $wpseotitle, $language, WPGlobus::RETURN_EMPTY ); ?>"
				     data-focuskw="<?php echo WPGlobus_Core::text_filter( $focuskw, $language, WPGlobus::RETURN_EMPTY ); ?>">
				</div> <?php
			} ?>
		</div>
	<?php
	}

	/**
	 * Handle ajax process
	 */
	public function on_process_ajax() {

		$ajax_return = array();

		$order = $_POST['order'];

		switch ( $order['action'] ) :
			case 'wpglobus_select_lang':
				if ( $order['locale'] == 'en_US' ) {
					update_option( 'WPLANG', '' );
				} else {	
					update_option( 'WPLANG', $order['locale'] );
				}
				break;
			case 'get_titles':

				if ( $order['type'] == 'taxonomy' ) {
					/**
					 * Remove filter to get raw term description
					 * @todo Need to restore?
					 */
					remove_filter( 'get_term', array( 'WPGlobus_Filters', 'filter__get_term' ), 0 );
				}

				global $WPGlobus_Config;
				$result               = array();
				$bulkedit_post_titles = array();
				foreach ( $order['title'] as $id => $title ) {
					$result[ $id ]['source'] = $title['source'];

					$term = null; // should initialize before if because used in the next foreach

					if ( $order['type'] == 'taxonomy' && $order['taxonomy'] ) {
						$term = get_term( $id, $order['taxonomy'] );
						if ( is_wp_error( $term ) ) {
							$order['taxonomy'] = false;
						}
					}

					foreach ( $WPGlobus_Config->enabled_languages as $language ) {
						$return =
							$language == $WPGlobus_Config->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;

						$result[ $id ][ $language ]['name'] =
							WPGlobus_Core::text_filter( $title['source'], $language, $return );
						if ( $term && $order['type'] == 'taxonomy' && $order['taxonomy'] ) {
							$result[ $id ][ $language ]['description'] =
								WPGlobus_Core::text_filter( $term->description, $language, $return );
						}

						$bulkedit_post_titles[ $id ][ $language ]['name'] =
							WPGlobus_Core::text_filter( $title['source'], $language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
					}
				}
				$ajax_return['qedit_titles']         = $result;
				$ajax_return['bulkedit_post_titles'] = $bulkedit_post_titles;
				break;
		endswitch;

		echo json_encode( $ajax_return );
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
	 * Start WPGlobus on "init" hook, so if there is another ReduxFramework, it will be loaded first. Hopefully :-)
	 * Note: "init" hook is not guaranteed to stay in the future versions.
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
			WPGlobus_Config::on_activate( null, $options );

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
	function on_wp_redirect( $location ) {
		if ( is_admin() ) {
			if ( isset( $_POST['_wp_http_referer'] ) && false !== strpos( $_POST['_wp_http_referer'], 'wpglobus=off' ) ) {
				$location .= '&wpglobus=off';
			}
		} else {
			/**
			 * Get language code from cookie. Example: redirect $_SERVER[REQUEST_URI] = /wp-comments-post.php
			 */
			if ( false !== strpos( $_SERVER['REQUEST_URI'], 'wp-comments-post.php' ) ) {
				if ( ! empty( $_COOKIE['wpglobus-language'] ) ) {
					$location = WPGlobus_Utils::localize_url( $location, $_COOKIE['wpglobus-language'] );
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
	function user_can( $cap = '' ) {
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
	function user_has_role( $role = '' ) {
		global $current_user;
		if ( empty( $current_user ) ) {
			wp_get_current_user();
		}

		return in_array( $role, $current_user->roles );
	}

	/**
	 * Add switcher to publish metabox
	 * @return void
	 */
	function on_add_devmode_switcher() {

		if ( ! $this->user_can( 'wpglobus_toggle' ) ) {
			return;
		}

		global $post;

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		}

		$mode = 'off';
		if ( isset( $_GET['wpglobus'] ) && 'off' == $_GET['wpglobus'] ) {
			$mode = 'on';
		}
		?>
		<div class="misc-pub-section wpglobus-switch">
			<span
				id="wpglobus-raw">&nbsp;&nbsp;WPGlobus: <strong><?php echo strtoupper( $mode == 'on' ? 'off' : 'on' ); ?></strong></span>
			<a href="post.php?post=<?php echo $post->ID; ?>&action=edit&wpglobus=<?php echo $mode; ?>">Toggle</a>
		</div>
	<?php
	}

	function on_admin_enqueue_scripts() {
		/**
		 * See function on_admin_scripts()
		 */
		if ( ! wp_script_is( 'autosave', 'enqueued' ) ) {
			wp_enqueue_script( 'autosave' );
		}
	}

	/**
	 * Enqueue admin scripts
	 * @return void
	 */
	function on_admin_scripts() {

		/** @global $post */
		global $post;

		$type = empty( $post ) ? '' : $post->post_type;
		if ( $this->disabled_entity( $type ) ) {
			return;
		}

		/**
		 * Dequeue autosave for prevent alert from wp.autosave.server.postChanged() after run post_edit in wpglobus.admin.js
		 * @see wp-includes\js\autosave.js
		 */
		wp_dequeue_script( 'autosave' );

		/** @global $pagenow */
		global $pagenow;

		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;

		/**
		 * Set array of enabled pages for loading js
		 */
		$enabled_pages   = array();
		$enabled_pages[] = self::LANGUAGE_EDIT_PAGE;
		$enabled_pages[] = self::OPTIONS_PAGE_SLUG;
		$enabled_pages[] = 'post.php';
		$enabled_pages[] = 'post-new.php';
		$enabled_pages[] = 'nav-menus.php';
		$enabled_pages[] = 'edit-tags.php';
		$enabled_pages[] = 'edit.php';
		$enabled_pages[] = 'options-general.php';
		$enabled_pages[] = 'widgets.php';

		/**
		 * Init $post_content
		 */
		$post_content = '';

		/**
		 * Init $post_title
		 */
		$post_title = '';

		/**
		 * Init $post_title
		 */
		$post_excerpt = '';

		$page_action = '';

		/**
		 * Init array data depending on the context for localize script
		 */
		$data = array(
			'default_language'  => $WPGlobus_Config->default_language,
			'language'          => $WPGlobus_Config->language,
			'enabled_languages' => $WPGlobus_Config->enabled_languages,
			'open_languages'    => $WPGlobus_Config->open_languages,
			'en_language_name'  => $WPGlobus_Config->en_language_name,
			'locale_tag_start'  => self::LOCALE_TAG_START,
			'locale_tag_end'    => self::LOCALE_TAG_END
		);

		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		if ( '' == $page ) {
			/**
			 * Now get $pagenow
			 */
			$page = isset( $pagenow ) ? $pagenow : '';

			if ( 'post.php' == $page || 'post-new.php' == $page ) {

				$page_action = 'post-edit';

				/**
				 * We use $post_content, $post_title at edit post page
				 */

				/**
				 * Set $post_content for default language
				 * because we have text with all languages and delimiters in $post->post_content
				 * next we send $post_content to js with localize script
				 * @see post_edit() in admin.globus.js
				 */
				$post_content = WPGlobus_Core::text_filter( $post->post_content, $WPGlobus_Config->default_language );

				/**
				 * Set $post_title for default language
				 */
				$post_title = WPGlobus_Core::text_filter( $post->post_title, $WPGlobus_Config->default_language );

			}

		}

		if ( self::LANGUAGE_EDIT_PAGE === $page ) {

			/**
			 * Using the same 'select2-js' ID as Redux Plugin does, to avoid duplicate enqueueing
			 * @todo Check if we should do it only if redux origin is 'embedded'
			 */
			wp_register_script(
				'select2-js',
				self::$PLUGIN_DIR_URL . "vendor/ReduxCore/assets/js/vendor/select2/select2" .
				self::$_SCRIPT_SUFFIX . ".js",
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'select2-js' );

		}

		if ( in_array( $page, $enabled_pages ) ) {

			/**
			 * Init $tabs_suffix
			 */
			$tabs_suffix = array();

			if ( in_array( $page, array( 'post.php', 'post-new.php', 'edit-tags.php' ) ) ) {
				/**
				 * Enqueue jQueryUI tabs
				 *  @todo remove after checking workability, from now it is enough dependency @see line 1075
				 */
				//wp_enqueue_script( 'jquery-ui-tabs' );

				/**
				 * Make suffixes for tabs
				 */
				foreach ( $WPGlobus_Config->enabled_languages as $language ) {
					if ( $language == $WPGlobus_Config->default_language ) {
						$tabs_suffix[] = 'default';
					} else {
						$tabs_suffix[] = $language;
					}
				}

			}
			$i18n                            = array();
			$i18n['cannot_disable_language'] = __( 'You cannot disable the first enabled language.', 'wpglobus' );

			if ( 'post.php' == $page || 'post-new.php' == $page ) {

				/**
				 * Add template for standard excerpt meta box
				 */
				$data['template'] = '';
				foreach ( WPGlobus::Config()->enabled_languages as $language ) {

					$return =
						$language == WPGlobus::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;

					$classes =
						in_array( $language, WPGlobus::Config()->open_languages ) ? 'wpglobus-excerpt' : 'wpglobus-excerpt hidden';

					$data['template'] .= '<textarea data-language="' . $language . '" placeholder="' . WPGlobus::Config()->en_language_name[ $language ] . '" class="' . $classes . '" rows="1" cols="40" name="excerpt-' . $language . '" id="excerpt-' . $language . '">';
					$data['template'] .= WPGlobus_Core::text_filter( $post->post_excerpt, $language, $return );
					$data['template'] .= '</textarea>';

					if ( $this->vendors_scripts['WPSEO'] ) {
						/** WPSEO */
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

			} else if ( 'nav-menus.php' == $page ) {

				$page_action = 'menu-edit';
				$menu_items  = array();
				$post_titles = array();

				global $wpdb;
				$items =
					$wpdb->get_results( "SELECT ID, post_title, post_excerpt, post_name FROM {$wpdb->prefix}posts WHERE post_type = 'nav_menu_item'", OBJECT );

				foreach ( $items as $item ) {
					$item->post_title = trim( $item->post_title );
					if ( empty( $item->post_title ) ) :

						$item_object    = get_post_meta( $item->ID, '_menu_item_object', true );
						$item_object_id = get_post_meta( $item->ID, '_menu_item_object_id', true );

						if ( 'page' == $item_object ) {
							/**
							 * Check for menu item has post type page
							 * for autocomplete Navigation Label input field
							 */
							$post_title = get_post_field( 'post_title', $item_object_id );
							$new_title  = trim( $post_title );
							if ( ! empty( $new_title ) ) {
								$item->post_title = $new_title;
								/**
								 * Update translation of title for menu item
								 */
								$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_title = '%s' WHERE ID = %d", $new_title, $item->ID ) );
							}
						} elseif ( 'category' == $item_object ) {

							/**
							 * @todo Write comment why do we disable the filter here
							 */
							remove_filter( 'get_term', array( 'WPGlobus_Filters', 'filter__get_term' ), 0 );
							$term = get_term_by( 'id', $item_object_id, $item_object );
							add_filter( 'get_term', array( 'WPGlobus_Filters', 'filter__get_term' ), 0 );

							$new_title = trim( $term->name );

							if ( ! empty( $new_title ) ) {
								$item->post_title = $new_title;
								/**
								 * Update translation of title for menu item
								 */
								$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_title = '%s' WHERE ID = %d", $new_title, $item->ID ) );
							}

						}

					endif;

					$menu_items[ $item->ID ]['item-title'] =
						WPGlobus_Core::text_filter( $item->post_title, $WPGlobus_Config->default_language );

					$post_titles[ $item->post_title ] = $menu_items[ $item->ID ]['item-title'];

					foreach ( self::Config()->enabled_languages as $language ) {

						$return =
							$language == self::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;

						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-title']['caption']      =
							WPGlobus_Core::text_filter( $item->post_title, $language, $return );
						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-attr-title']['caption'] =
							WPGlobus_Core::text_filter( $item->post_excerpt, $language, $return );

						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-title']['class']      =
							'widefat wpglobus-menu-item wpglobus-item-title';
						$menu_items[ $item->ID ][ $language ]['input.edit-menu-item-attr-title']['class'] =
							'widefat wpglobus-menu-item wpglobus-item-attr';
					}
				}

				$data['items']       = $menu_items;
				$data['post_titles'] = $post_titles;

				$i18n['save_nav_menu'] = __( '*) Available after the menu is saved.', 'wpglobus' );

			} else if ( 'edit-tags.php' == $page ) {

				global $tag;

				$data['taxonomy']  = empty( $_GET['taxonomy'] ) ? false : $_GET['taxonomy'];
				$data['tag_id']    = empty( $_GET['tag_ID'] ) ? false : $_GET['tag_ID'];
				$data['has_items'] = true;

				if ( $data['tag_id'] ) {
					/**
					 * For example url: edit-tags.php?action=edit&taxonomy=category&tag_ID=4&post_type=post
					 */
					$page_action = 'taxonomy-edit';
				} else {
					/**
					 * For example url: edit-tags.php?taxonomy=category
					 * edit-tags.php?taxonomy=product_cat&post_type=product
					 */
					if ( ! empty( $_GET['taxonomy'] ) ) {
						$terms = get_terms( $_GET['taxonomy'], array( 'hide_empty' => false ) );
						if ( is_wp_error( $terms ) or empty( $terms ) ) {
							$data['has_items'] = false;
						}
					}
					$page_action = 'taxonomy-quick-edit';
				}

				if ( $data['tag_id'] ) {
					foreach ( $WPGlobus_Config->enabled_languages as $language ) {
						$lang = $language == $WPGlobus_Config->default_language ? 'default' : $language;
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

			} else if ( 'edit.php' == $page ) {

				$page_action = 'edit.php';
				$post_type   = 'post';
				if ( ! empty( $_GET['post_type'] ) ) {
					$post_type = $_GET['post_type'];
				}

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
							$data['tags'][]                   = $tag;
							$data['names'][ $tag ]            = 'tax_input[' . $tag . ']';
							$data['tag'][ $tag ]              = $terms;
							$data['value'][ $tag ]            = ''; // just init
							$data['value'][ $tag ]['post_id'] = ''; // just init
						}
					}
				}

			} else if ( 'options-general.php' == $page ) {

				$page_action = 'options-general.php';

			} else if ( 'widgets.php' == $page ) {
				
				$page_action = 'widgets.php';
			
			}

			/**
			 * Enqueue js for WPSEO support
			 * @since 1.0.8 
			 */			
			if ( $this->vendors_scripts['WPSEO'] && in_array( $page, array( 'post.php', 'post-new.php') ) ) {				
				wp_register_script(
					'wpglobus-vendor',
					self::$PLUGIN_DIR_URL . "includes/js/wpglobus-vendor-wpseo" . self::$_SCRIPT_SUFFIX . ".js",
					array( 'jquery' ),
					WPGLOBUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-vendor' );
				wp_localize_script(
					'wpglobus-vendor',
					'WPGlobusVendor',
					array(
						'version' => WPGLOBUS_VERSION,
						'vendor'  => $this->vendors_scripts
					)
				);
			}

			wp_register_script(
				'wpglobus-admin',
				self::$PLUGIN_DIR_URL . "includes/js/wpglobus-admin" . self::$_SCRIPT_SUFFIX . ".js",
				array( 'jquery', 'jquery-ui-dialog', 'jquery-ui-tabs' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-admin' );

			/**
			 * We need to send the HTML breaks and not \r\n to the JS,
			 * because we do element.text(...), and \r\n are being removed by TinyMCE
			 * See other places with the same bookmark.
			 * @bookmark EDITOR_LINE_BREAKS
			 */
			$post_content_autop = wpautop( $post_content );

			wp_localize_script(
				'wpglobus-admin',
				'WPGlobusAdmin',
				array(
					'version'      => WPGLOBUS_VERSION,
					'page'         => $page_action,
					'content'      => $post_content_autop,
					'title'        => $post_title,
					'excerpt'      => $post_excerpt,
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'parentClass'  => __CLASS__,
					'process_ajax' => __CLASS__ . '_process_ajax',
					'flag_url'     => $WPGlobus_Config->flags_url,
					'tabs'         => $tabs_suffix,
					'i18n'         => $i18n,
					'data'         => $data
				)
			);
			wp_localize_script(
				'wpglobus-admin',
				'WPGlobusCoreData',
				array(
					'version'      => WPGLOBUS_VERSION,
					'default_language'  => $WPGlobus_Config->default_language,
					'language'          => $WPGlobus_Config->language,
					'enabled_languages' => $WPGlobus_Config->enabled_languages,
					'open_languages'    => $WPGlobus_Config->open_languages,
					'en_language_name'  => $WPGlobus_Config->en_language_name,
					'locale_tag_start'  => self::LOCALE_TAG_START,
					'locale_tag_end'    => self::LOCALE_TAG_END					
				)
			);
			
			/**
			 * Enqueue js for ACF support
			 */
			if ( $this->vendors_scripts['ACF'] && in_array( $page, array( 'post.php', 'post-new.php') ) ) {
				wp_register_script(
					'wpglobus-acf',
					self::$PLUGIN_DIR_URL . "includes/js/wpglobus-vendor-acf" . self::$_SCRIPT_SUFFIX . ".js",
					array( 'jquery', 'wpglobus-admin' ),
					WPGLOBUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-acf' );				
			}
			
			if ( 'widgets.php' == $page ) {
				wp_register_script(
					'wpglobus-widgets',
					self::$PLUGIN_DIR_URL . "includes/js/wpglobus-widgets" . self::$_SCRIPT_SUFFIX . ".js",
					array( 'jquery', 'wpglobus-admin' ),
					WPGLOBUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-widgets' );				
			}			
			
		} 	// endif $enabled_pages
	}

	/**
	 * Get taxonomies for post type
	 *
	 * @param string $post_type
	 * @param string $type hierarchical, non-hierarchical or all taxonomies
	 *
	 * @return array
	 */
	function _get_taxonomies( $post_type, $type = 'all' ) {
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
	function _get_quickedit_template() {
		$t = '';
		foreach ( self::Config()->open_languages as $language ) {
			$t .= '<label>';
			$t .= '<span class="input-text-wrap">';
			$t .= '<input id="filled-in-js" data-language="' . $language . '" style="width:100%;" class="ptitle wpglobus-quick-edit-title" type="text" value="" name="post_title-' . $language . '" placeholder="' . self::Config()->en_language_name[ $language ] . '">';
			$t .= '</span>';
			$t .= '</label>';
		}

		return $t;
	}

	/**
	 * Enqueue admin styles
	 * @return void
	 */
	function on_admin_styles() {

		/** @global string $pagenow */
		global $pagenow;

		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style(
			'wpglobus-admin',
			self::$PLUGIN_DIR_URL . "includes/css/wpglobus-admin$suffix.css",
			array(),
			WPGLOBUS_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpglobus-admin' );
		
		wp_register_style(
			'dialog-ui',
			self::$PLUGIN_DIR_URL . "includes/css/wpglobus-dialog-ui$suffix.css",				
			array(),
			WPGLOBUS_VERSION,
			'all'
		);		
		wp_enqueue_style( 'dialog-ui' );		

		if ( self::LANGUAGE_EDIT_PAGE === $page ) {
			wp_register_style(
				'select2-css',
				self::$PLUGIN_DIR_URL . 'vendor/ReduxCore/assets/js/vendor/select2/select2.css',
				array(),
				WPGLOBUS_VERSION,
				'all'
			);
			wp_enqueue_style( 'select2-css' );
		}

		/** @global WP_Post $post */
		global $post;
		$type = empty( $post ) ? '' : $post->post_type;
		if ( ! $this->disabled_entity( $type ) ) {
			if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit-tags.php', 'widgets.php' ) ) ) {

				wp_register_style(
					'wpglobus-admin-tabs',
					self::$PLUGIN_DIR_URL . "includes/css/wpglobus-admin-tabs$suffix.css",
					array(),
					WPGLOBUS_VERSION,
					'all'
				);
				wp_enqueue_style( 'wpglobus-admin-tabs' );
			}
		}

	}

	/**
	 * Add hidden submenu for Language edit page
	 * @return void
	 */
	function on_admin_menu() {
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
	}

	/**
	 * Include file for WPGlobus about page
	 * @return void
	 */
	function wpglobus_about() {
		require_once 'admin/class-wpglobus-about.php';
		WPGlobus_About::about_screen();
	}

	/**
	 * Include file for language edit page
	 * @return void
	 */
	function on_language_edit() {
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
	function on_get_convert_url_menu_items( $sorted_menu_items ) {

		foreach ( $sorted_menu_items as $key => $item ) {
			if ( 'custom' == $item->type ) {
				$sorted_menu_items[ $key ]->url = WPGlobus_Utils::localize_url( $sorted_menu_items[ $key ]->url );
			}
		}

		return $sorted_menu_items;

	}

	/**
	 * Include file for new field 'table'
	 * @return string
	 */
	function on_field_table() {
		return dirname( __FILE__ ) . '/options/fields/table/field_table.php';
	}

	/**
	 * Enqueue styles
	 * @return void
	 */
	function on_wp_styles() {
		wp_register_style(
			'wpglobus',
			self::$PLUGIN_DIR_URL . "includes/css/wpglobus" . self::$_SCRIPT_SUFFIX . ".css",
			array(),
			WPGLOBUS_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpglobus' );
	}

	/**
	 * Enqueue scripts
	 * @return void
	 */
	function on_wp_scripts() {
		global $WPGlobus_Config;

		wp_register_script(
			'wpglobus',
			self::$PLUGIN_DIR_URL . "includes/js/wpglobus" . self::$_SCRIPT_SUFFIX . ".js",
			array( 'jquery', 'utils' ),
			WPGLOBUS_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus' );
		wp_localize_script(
			'wpglobus',
			'WPGlobus',
			array(
				'version'  => WPGLOBUS_VERSION,
				'language' => $WPGlobus_Config->language
			)
		);
	}

	/**
	 * Add rel="alternate" links to head section
	 * @return void
	 */
	function on_add_hreflang() {

		global $WPGlobus_Config;

		$scheme = 'http';
		if ( is_ssl() ) {
			$scheme = 'https';
		}

		$ref_source =
			$scheme . '://' . $WPGlobus_Config->url_info['host'] . '/%%lang%%' . $WPGlobus_Config->url_info['url'];

		foreach ( $WPGlobus_Config->enabled_languages as $language ) {
			$hreflang = str_replace( '_', '-', $WPGlobus_Config->locale[ $language ] );
			if ( $language == $WPGlobus_Config->default_language ) {
				$ref = str_replace( '%%lang%%/', '', $ref_source );
			} else {
				$ref = str_replace( '%%lang%%', $language, $ref_source );
			}
			?>
			<link rel="alternate" hreflang="<?php echo $hreflang; ?>" href="<?php echo $ref; ?>"/>
		<?php
		}

	}

	/**
	 * Add css styles to head section
	 * @return string
	 */
	function on_wp_head() {

		global $WPGlobus_Config;

		$css = '';
		foreach ( $WPGlobus_Config->enabled_languages as $language ) {
			$css .= '.wpglobus_flag_' . $language .
			        ' { background:url(' .
			        $WPGlobus_Config->flags_url . $WPGlobus_Config->flag[ $language ] . ') no-repeat }' . "\n";
		}

		$css .= strip_tags( $WPGlobus_Config->css_editor );

		if ( ! empty( $css ) ) {
			?>
<style type="text/css" media="screen">
	<?php echo $css; ?>
</style>
		<?php
		}

	}

	/**
	 * Add item to navigation menu which was created with wp_list_pages
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	function on_wp_list_pages( $output ) {
	
		if ( ! WPGlobus::Config()->selector_wp_list_pages ) {
			return $output;
		}	

		$extra_languages = array();
		foreach ( WPGlobus::Config()->enabled_languages as $languages ) {
			if ( $languages != WPGlobus::Config()->language ) {
				$extra_languages[] = $languages;
			}
		}

		$span_classes = array(
			'wpglobus_flag',
			'wpglobus_language_name'
		);

		$span_classes_lang   = $span_classes;
		$span_classes_lang[] = 'wpglobus_flag_' . WPGlobus::Config()->language;

		$output .= '<li class="page_item page_item_wpglobus_menu_switch page_item_has_children">
						<a href="' . WPGlobus_Utils::get_url( WPGlobus::Config()->language ) . '"><span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( WPGlobus::Config()->language ) . '</span></a>
						<ul class="children">';
		foreach ( $extra_languages as $language ) {
			$span_classes_lang   = $span_classes;
			$span_classes_lang[] = 'wpglobus_flag_' . $language;
			$output .= '<li class="page_item">
								<a href="' . WPGlobus_Utils::get_url( $language ) . '"><span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span></a>
							</li>';
		} // end foreach
		$output .= '	</ul>
					</li>';

		return $output;
	}

	/**
	 * Add language switcher to navigation menu
	 *
	 * @param array  $sorted_menu_items
	 * @param object $args An object containing wp_nav_menu() arguments.
	 *
	 * @return array
	 * @see wp_nav_menu()
	 */
	function on_add_item( $sorted_menu_items, $args ) {

		if ( empty( WPGlobus::Config()->nav_menu ) ) {
			/**
			 * User can use WPGlobus widget
			 * @since 1.0.7
			 */
			return $sorted_menu_items;
			
		} elseif ( 'all' == WPGlobus::Config()->nav_menu ) {
			/**
			 * Attach to every nav menu
			 * @since 1.0.7
			 */
		} else {	

			$items = array();
			foreach( $sorted_menu_items as $item ) {
				$items[] = $item->ID;
			}

			$return = true;
			foreach ( $this->menus as $key => $menu ) {
				$diff = array_diff( $items, $menu->menu_items );
				if ( empty( $diff ) && WPGlobus::Config()->nav_menu === $menu->slug ) {
					$return = false;
					break;
				}
			}
			
			if ( $return ) {
				return $sorted_menu_items;
			}

		}
		
		$extra_languages = array();
		foreach ( WPGlobus::Config()->enabled_languages as $languages ) {
			if ( $languages != WPGlobus::Config()->language ) {
				$extra_languages[] = $languages;
			}
		}

		/** main menu item classes */
		$menu_item_classes = array(
			'',
			'menu_item_wpglobus_menu_switch'
		);

		/** submenu item classes */
		$submenu_item_classes = array(
			'',
			'sub_menu_item_wpglobus_menu_switch'
		);

		$span_classes = array(
			'wpglobus_flag',
			'wpglobus_language_name'
		);

		$span_classes_lang   = $span_classes;
		$span_classes_lang[] = 'wpglobus_flag_' . WPGlobus::Config()->language;

		$item                   = new stdClass();
		$item->ID               = 9999999999; # 9 999 999 999
		$item->db_id            = 9999999999;
		$item->menu_item_parent = 0;
		$item->title            =
			'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( WPGlobus::Config()->language ) . '</span>';
		$item->url              = WPGlobus_Utils::get_url( WPGlobus::Config()->language );
		$item->classes          = $menu_item_classes;
		$item->description      = '';

		$sorted_menu_items[] = $item;

		foreach ( $extra_languages as $language ) {
			$span_classes_lang   = $span_classes;
			$span_classes_lang[] = 'wpglobus_flag_' . $language;

			$item                   = new stdClass();
			$item->ID               = 'wpglobus_menu_switch_' . $language;
			$item->db_id            = 'wpglobus_menu_switch_' . $language;
			$item->menu_item_parent = 9999999999;
			$item->title            =
				'<span class="' . implode( ' ', $span_classes_lang ) . '">' . $this->_get_flag_name( $language ) . '</span>';
			$item->url              = WPGlobus_Utils::get_url( $language );
			$item->classes          = $submenu_item_classes;
			$item->description      = '';

			$sorted_menu_items[] = $item;
		}

		return $sorted_menu_items;
	}

	/**
	 * Get flag name for navigation menu
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	function _get_flag_name( $language ) {

		global $WPGlobus_Config;

		switch ( $WPGlobus_Config->show_flag_name ) {
			case 'name' :
				$flag_name = $WPGlobus_Config->language_name[ $language ];
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
		
		foreach( $menus as $key=>$menu ) {
			
			$result = $wpdb->get_results( $wpdb->prepare("SELECT object_id FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = %d ORDER BY object_id ASC", $menu->term_id ), OBJECT_K);

			$result = array_keys($result);
			
			$menus[$key]->menu_items = $result;

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
	function on_add_wp_editors( $post ) {

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		}

		/**
		 * @todo Temporary workaround. Need to revise the related wpglobus-admin.js part
		 * If CPT does not support `editor` (no $post->content),
		 * we'll put the "dummy" editor DIVs, so our tabs won't break.
		 */
		// <editor-fold desc="No-editor CPTs: Dummy WYSIWYGs">
		if ( ! post_type_supports( $post->post_type, 'editor' ) ) {

			foreach ( self::Config()->open_languages as $language ) :
				$div_id = 'postdivrich' .
				          ( $language === self::Config()->default_language ? '' : '-' . $language );
				?><div id="<?php echo $div_id; ?>" class="postarea postdivrich-wpglobus"></div><?php
			endforeach;

			return;
		}
		// </editor-fold>

		foreach ( self::Config()->open_languages as $language ) :
			if ( $language == self::Config()->default_language ) {

				continue;

			} else { ?>

				<div id="postdivrich-<?php echo $language; ?>" class="postarea postdivrich-wpglobus">    <?php
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
					) ); ?>
				</div> <?php

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
	 * @deprecated 1.0.0 Use WPGlobus::add_locale_marks
	 * @see        WPGlobus::add_locale_marks
	 *
	 * @param string $text
	 * @param string $language
	 *
	 * @return string
	 */
	public static function tag_text( $text, $language ) {
		_deprecated_function( __METHOD__, 'WPGlobus 1.0.0', 'WPGlobus::add_locale_marks()' );

		return self::add_locale_marks( $text, $language );
	}

	/**
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 */
	function on_save_post_data( $data, $postarr ) {

		if ( 'revision' == $postarr['post_type'] ) {
			/**
			 * Don't working with revision
			 * note: revision there are 2 types, its have some differences
			 *        - [post_name] => {post_id}-autosave-v1    and [post_name] => {post_id}-revision-v1
			 *        - when [post_name] == {post_id}-autosave-v1  $postarr has [post_content] and [post_title] in default_language
			 *        - [post_name] == {post_id}-revision-v1 $postarr has [post_content] and [post_title] in all enabled languages with delimiters
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

		if ( 'trash' == $postarr['post_status'] ) {
			/**
			 * Don't working with move to trash
			 */
			return $data;			
		}		
		
		if ( isset($_GET['action']) &&  'untrash' == $_GET['action'] ) {
			/**
			 * Don't working with untrash
			 */
			return $data;			
		}			
		
		$devmode = false;
		if ( 'off' == WPGlobus::Config()->toggle ) {
			$devmode = true;
		}	
		
		if ( ! $devmode ) :

			$data['post_title'] = trim( $data['post_title'] );
			if ( ! empty( $data['post_title'] ) ) {
				$data['post_title'] =
					WPGlobus::add_locale_marks( $data['post_title'], WPGlobus::Config()->default_language );
			}

			$data['post_content'] = trim( $data['post_content'] );
			if ( ! empty( $data['post_content'] ) ) {
				$data['post_content'] =
					WPGlobus::add_locale_marks( $data['post_content'], WPGlobus::Config()->default_language );
			}

			foreach ( WPGlobus::Config()->open_languages as $language ) :
				if ( $language == WPGlobus::Config()->default_language ) {

					continue;

				} else {

					/**
					 * Join post title for enabled languages
					 */
					$title =
						isset( $postarr[ 'post_title_' . $language ] ) ? trim( $postarr[ 'post_title_' . $language ] ) : '';
					if ( ! empty( $title ) ) {
						$data['post_title'] .= WPGlobus::add_locale_marks( $postarr[ 'post_title_' . $language ], $language );
					}

					/**
					 * Join post content for enabled languages
					 */
					$content =
						isset( $postarr[ 'content_' . $language ] ) ? trim( $postarr[ 'content_' . $language ] ) : '';
					if ( ! empty( $content ) ) {
						$data['post_content'] .= WPGlobus::add_locale_marks( $postarr[ 'content_' . $language ], $language );
					}

				}
			endforeach;

		endif;  //  $devmode

		$data = apply_filters( 'wpglobus_save_post_data', $data, $postarr, $devmode );

		return $data;

	}

	/**
	 * Add wrapper for every table in enabled languages at edit-tags.php page
	 * @return void
	 */
	function on_add_taxonomy_form_wrapper() {
		foreach ( self::Config()->enabled_languages as $language ) {
			$classes = 'hidden'; 		?>
			<div id="taxonomy-tab-<?php echo $language; ?>" data-language="<?php echo $language; ?>"
			     class="<?php echo $classes; ?>">
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
	function on_add_language_tabs_edit_taxonomy($object, $taxonomy) {

		if ( $this->disabled_entity() ) {
			return;
		} 		?>

		<ul class="wpglobus-taxonomy-tabs-ul">    <?php
			foreach ( self::Config()->open_languages as $language ) {
				$return = $language == WPGlobus::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY;
						?>
				<li id="wpglobus-link-tab-<?php echo $language; ?>" class="" 
						data-language="<?php echo $language; ?>"
						data-name="<?php echo WPGlobus_Core::text_filter($object->name, $language, $return); ?>"
						data-description="<?php echo WPGlobus_Core::text_filter($object->description, $language, $return); ?>">
					<a href="#taxonomy-tab-<?php echo $language; ?>"><?php echo self::Config()->en_language_name[ $language ]; ?></a>
				</li> <?php
			} ?>
		</ul>    <?php
	}

	/**
	 * Add language tabs for jQueryUI
	 * @return void
	 */
	function on_add_language_tabs() {

		global $post;

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		} ?>

		<ul class="wpglobus-post-tabs-ul">    <?php
			$order = 0;
			foreach ( self::Config()->open_languages as $language ) {
				$tab_suffix = $language == self::Config()->default_language ? 'default' : $language; ?>
				<li id="link-tab-<?php echo $tab_suffix; ?>" data-language="<?php echo $language; ?>"
				    data-order="<?php echo $order; ?>"
				    class="wpglobus-post-tab">
					<a href="#tab-<?php echo $tab_suffix; ?>"><?php echo self::Config()->en_language_name[ $language ]; ?></a>
				</li> <?php
				$order ++;
			} ?>
		</ul>    <?php

	}

	/**
	 * Add title fields for enabled languages at post.php, post-new.php page
	 *
	 * @param $post
	 *
	 * @return void
	 */
	function on_add_title_fields( $post ) {

		if ( $this->disabled_entity( $post->post_type ) ) {
			return;
		}

		foreach ( self::Config()->open_languages as $language ) :

			if ( $language == self::Config()->default_language ) {

				continue;

			} else { ?>

				<div id="titlediv-<?php echo $language; ?>" class="titlediv-wpglobus">
					<div id="titlewrap-<?php echo $language; ?>" class="titlewrap-wpglobus">
						<label class="screen-reader-text" id="title-prompt-text-<?php echo $language; ?>"
						       for="title_<?php echo $language; ?>"><?php echo apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ); ?></label>
						<input type="text" name="post_title_<?php echo $language; ?>" size="30"
						       value="<?php echo esc_attr( htmlspecialchars( WPGlobus_Core::text_filter( $post->post_title, $language, WPGlobus::RETURN_EMPTY ) ) ); ?>"
						       id="title_<?php echo $language; ?>"
						       class="title_wpglobus"
						       data-language="<?php echo $language; ?>"
						       autocomplete="off"/>
					</div> <!-- #titlewrap -->
					<div class="inside">
						<div id="edit-slug-box-<?php echo $language; ?>" class="wpglobus-edit-slug-box hide-if-no-js">
							<b></b>
						</div>
					</div>
					<!-- .inside -->
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
	function disabled_entity( $entity = '' ) {
		if ( empty( $entity ) ) {
			/**
			 * Try get entity from url. Ex. edit-tags.php?taxonomy=product_cat&post_type=product
			 */
			if ( isset( $_GET['post_type'] ) ) {
				$entity = $_GET['post_type'];
			}
			if ( empty( $entity ) && isset( $_GET['taxonomy'] ) ) {
				$entity = $_GET['taxonomy'];
			}
		}
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
			$error = new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );

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
	function on_admin_title(
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
	function on_admin_footer() {

		$blogname = get_option( 'blogname' );
		$bn       =
			WPGlobus_Core::text_filter( $blogname, WPGlobus::Config()->language, WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );

		?>
		<script type='text/javascript'>
			/* <![CDATA[ */
			jQuery('#wp-admin-bar-site-name a').text("<?php echo $bn; ?>");
			/* ]]> */
		</script>
		<?php
	
		if ( WPGlobus_WP::is_pagenow(array('post.php', 'widgets.php')) ) {
			/**
			 * Output dialog form for window.WPGlobusDialogApp
			 */ 
			?>
			<div id="wpglobus-dialog-wrapper" title="" class="hidden">
				<form id="wpglobus-dialog-form" style="">	
					<div id="wpglobus-dialog-tabs">   
						<ul class="wpglobus-dialog-tabs-list">    <?php
							$order = 0;
							foreach ( WPGlobus::Config()->open_languages as $language ) { ?>
								<li id="dialog-link-tab-<?php echo $language; ?>"
									data-language="<?php echo $language; ?>"
									data-order="<?php echo $order; ?>"
									class="wpglobus-dialog-tab"><a
										href="#dialog-tab-<?php echo $language; ?>"><?php echo WPGlobus::Config()->en_language_name[ $language ]; ?></a>
								</li> <?php
								$order ++;
							} ?>
						</ul>    <?php

						foreach ( WPGlobus::Config()->open_languages as $language ) { 	?>
							<div id="dialog-tab-<?php echo $language; ?>" class="wpglobus-dialog-general">
								<textarea placeholder="" style="height:50%;" name="wpglobus-dialog-<?php echo $language; ?>" 
									id="wpglobus-dialog-<?php echo $language; ?>" class="wpglobus_dialog_textarea textarea"
									data-language="<?php echo $language; ?>"
									data-order="save_dialog"></textarea>
							</div> <?php
						} ?>
					</div>	
				</form>
			</div>		<?php
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

				<input type="text" class="regular-text wpglobus-blogname"
				       value="<?php echo WPGlobus_Core::text_filter( $blogname, $language, $return ); ?>"
				       id="blogname-<?php echo $language; ?>" name="blogname-<?php echo $language; ?>"
				       data-language="<?php echo $language; ?>"
				       placeholder="<?php echo self::Config()->en_language_name[ $language ]; ?>"><br/>

			<?php
			endforeach; ?>
		</div>

		<div id="wpglobus-blogdescription" class="hidden">        <?php
			foreach ( self::Config()->enabled_languages as $language ) :
				$return =
					$language == self::Config()->default_language ? WPGlobus::RETURN_IN_DEFAULT_LANGUAGE : WPGlobus::RETURN_EMPTY; ?>

				<input type="text" class="regular-text wpglobus-blogdesc"
				       value="<?php echo WPGlobus_Core::text_filter( $blogdesc, $language, $return ); ?>"
				       id="blogdescription-<?php echo $language; ?>" name="blogdescription-<?php echo $language; ?>"
				       data-language="<?php echo $language; ?>"
				       placeholder="<?php echo self::Config()->en_language_name[ $language ]; ?>"><br/>

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
		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;

		return $WPGlobus_Config;
	}

	/**
	 * Check for transient wpglobus_activated
	 * @since 1.0.0
	 * @return void
	 */
	function on_admin_init() {
		
		if ( false !== get_transient( 'wpglobus_activated' ) ) {
			delete_transient( 'wpglobus_activated' );
			wp_redirect( admin_url( add_query_arg( array( 'page' => 'wpglobus-about' ), 'admin.php' ) ) );
			die();
		}

		/**
		 * Filter the array of disabled entities returned for load tabs, scripts, styles.
		 * @since 1.0.0
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
		self::Config()->open_languages = apply_filters( 'wpglobus_open_languages', self::Config()->open_languages );

		/**
		 * @todo Proposed solution for the broken WPGlobus interface on CPTs without content editor.
		 *       DISABLED as of 15.03.14
		 */
		// <editor-fold desc="No-editor CPTs: Add to disabled_entities">
		if ( 0 ):
			/**
			 * Add CPT without 'editor' feature to disabled_entities array
			 */
			if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php' ) ) ) {
				/**
				 * Checks if this post type supports 'editor' feature.
				 */
				$post_type = '';

				if ( ! empty( $_GET['post'] ) ) {
					$post_type = get_post_field( 'post_type', $_GET['post'] );
				}

				if ( empty( $post_type ) && ! empty( $_GET['post_type'] ) ) {
					/**
					 * For post-new.php page
					 */
					$post_type = $_GET['post_type'];
				}

				if ( ! empty( $post_type ) && ! post_type_supports( $post_type, 'editor' ) ) {

					/**
					 * "Solution": we do not support such CPTs
					 */
					$this->disabled_entities[] = $post_type;

					/**
					 * "Hack": we add the editor and hide it
					 */
					// add_post_type_support( $post_type, 'editor' );
					// echo '<style>.wp-editor-wrap{display:none;}</style>';
				}

			}
		endif;
		// </editor-fold>

	}

	/**
	 * Add language selector to adminbar
	 * @since 1.0.8
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	function on_admin_bar_menu(WP_Admin_Bar $wp_admin_bar) {
		
		$available_languages = get_available_languages();
		
		$user_id      = get_current_user_id();

		if ( ! $user_id ) {
			return;
		}

		$wp_admin_bar->add_menu( array(
			'id'        => 'wpglobus-language-select',
			'parent'    => 'top-secondary',
			'title'     => WPGlobus::Config()->language_name[WPGlobus::Config()->language] . '&nbsp;&nbsp;&nbsp;<span><img src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[WPGlobus::Config()->language]  . '" /></span>',
			'href'      => '',
			'meta'      => array(
				'class'     => '',
				'title'     => __('My Account'),
			),
		) );	
		
		$add_more_languages = array();
		foreach( WPGlobus::Config()->enabled_languages as $language ) :
			
			if ( WPGlobus::Config()->language == $language ) { 
				continue;
			}
			
			$locale = WPGlobus::Config()->locale[$language];
			
			if ( $locale != 'en_US' ) { 
				if ( ! in_array( $locale, $available_languages ) ) {
					$add_more_languages[] = WPGlobus::Config()->language_name[$language];
					continue;
				}	
			}	
			
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpglobus-language-select',
				'id'     => 'wpglobus-' . $language,
				'title'  => '<span><img src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[$language]  . '" /></span>&nbsp;&nbsp;' . WPGlobus::Config()->language_name[$language],
				'href'   => admin_url( 'options-general.php' ),
				'meta'   => array(
					'tabindex' => -1,
					'onclick' => 'wpglobus_select_lang("' . $locale . '");return false;'
				),
			) );
			
		endforeach;
		
		if ( !empty($add_more_languages) ) {
			$title = __( 'Add', 'wpglobus' ) . ' (' . implode(', ', $add_more_languages ) . ')';
			$wp_admin_bar->add_menu( array(
				'parent' => 'wpglobus-language-select',
				'id'     => 'wpglobus-add-languages',
				'title'  => $title,
				'href'   => admin_url( 'options-general.php' ),
				'meta'   => array(
					'tabindex' => -1,
				),
			) );		
		}	
	?>
<!--suppress AnonymousFunctionJS -->
		<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($){	
		wpglobus_select_lang = function(locale) {
			$.post(ajaxurl, {
					action: 'WPGlobus_process_ajax',
					order: {action:'wpglobus_select_lang',locale:locale}
				}, function(d) {} )
				.done(function(){
					window.location.reload();
				})	;			
		}	
	});
//]]>
</script>	
		<?php
	}

}

# --- EOF