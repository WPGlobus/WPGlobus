<?php
/**
 * @package   WPGlobus
 */

/**
 * Class WPGlobus_Config
 */
class WPGlobus_Config {

	/**
	 * Language by default
	 * @var string
	 */
	public $default_language = 'en';

	/**
	 * Current language. Should be set to default initially.
	 * @var string
	 */
	public $language = 'en';

	/**
	 * Enabled languages
	 * @var string[]
	 */
	public $enabled_languages = array(
		'en',
		'es',
		'de',
		'fr',
		'ru',
	);

	/**
	 * Hide from URL language by default
	 * @var bool
	 */
	public $hide_default_language = true;

	/**
	 * Opened languages
	 * @var string[]
	 */
	public $open_languages = array();

	/**
	 * Flag images configuration
	 * Look in /flags/ directory for a huge list of flags for usage
	 * @var array
	 */
	public $flag = array();

	/**
	 * Location of flags (needs trailing slash!)
	 * @var string
	 */
	public $flags_url = '';

	/**
	 * Path to flags.
	 * @var array
	 * @since 1.9.17
	 */
	public $flag_path = array();
	
	/**
	 * Location of flags.
	 * @var array
	 * @since 1.9.17
	 */	
	public $flag_urls = array();
	
	/**
	 * Stores languages in pairs code=>name
	 * @var array
	 */
	public $language_name = array();

	/**
	 * Stores languages names in English
	 * @var array
	 */
	public $en_language_name = array();

	/**
	 * Stores locales
	 * @var array
	 */
	public $locale = array();

	/**
	 * Stores enabled locales
	 * @since 1.0.10
	 * @var array
	 */
	public $enabled_locale = array();

	/**
	 * Stores version and update from WPGlobus Mini info
	 * @var array
	 */
	public $version = array();

	/**
	 * Use flag name for navigation menu : 'name' || 'code' || ''
	 * @var string
	 */
	public $show_flag_name = 'code';

	/**
	 * Use navigation menu by slug
	 * for use in all nav menu set value to 'all'
	 * @var string
	 */
	public $nav_menu = '';

	/**
	 * Add language selector to navigation menu which was created with wp_list_pages
	 * @since 1.0.7
	 * @var bool
	 */
	public $selector_wp_list_pages = true;

	/**
	 * Custom CSS
	 * @var string
	 */
	public $custom_css = '';

	/**
	 * WPGlobus option key
	 * @var string
	 */
	public $option = 'wpglobus_option';

	/**
	 * WPGlobus option versioning key
	 * @var string
	 */
	public static $option_versioning = 'wpglobus_option_versioning';

	/**
	 * WPGlobus option key for $language_name
	 * @var string
	 */
	public $option_language_names = 'wpglobus_option_language_names';

	/**
	 * WPGlobus option key for $en_language_name
	 * @var string
	 */
	public $option_en_language_names = 'wpglobus_option_en_language_names';

	/**
	 * WPGlobus option key for $locale
	 * @var string
	 */
	public $option_locale = 'wpglobus_option_locale';

	/**
	 * WPGlobus option key for $flag
	 * @var string
	 */
	public $option_flags = 'wpglobus_option_flags';

	/**
	 * WPGlobus option key for meta settings
	 * @var string
	 */
	public $option_post_meta_settings = 'wpglobus_option_post_meta_settings';

	/**
	 * @var string
	 */
	public $css_editor = '';

	/**
	 * @var string
	 */
	public $js_editor = '';

	/**
	 * WPGlobus devmode.
	 * @var string
	 */
	public $toggle = 'on';

	/**
	 * @todo Refactor this
	 * Duplicate var @see WPGlobus
	 * @var array
	 */
	public $disabled_entities = array();

	/**
	 * WPGlobus extended options can be added via filter 'wpglobus_option_sections'
	 *
	 * @since 1.2.3
	 * @var array
	 */
	public $extended_options = array();

	/**
	 * @var array
	 * @since 1.8.0
	 */
	public $browser_redirect;

	/**
	 * Used to temporarily store the language detected from the URL processed by oembed.
	 * @var  string
	 * @since 1.8.4
	 */
	protected $language_for_oembed = '';

	/**
	 * Builder.
	 *
	 * @var WPGlobus_Config_Builder
	 * @since 1.9.17
	 */
	public $builder = null;

	/**
	 * True if builder is disabled.
	 *
	 * @var boolean
	 * @since 1.9.17
	 */
	public $builder_disabled = true;

	/**
	 * If '1', use the old style language switcher in Gutenberg. Set through the Options Panel.
	 *
	 * @var string
	 * @since 2.2.3
	 */
	public $block_editor_old_fashioned_language_switcher = '';
	
	/**
	 * Type of switcher button for WPGlobusSwitcherPlugin. 
	 *
	 * @var string
	 * @since 2.2.14
	 */	
	public $block_editor_switcher_plugin_button_type = '';

	/**
	 * Can get it only once.
	 *
	 * @since 1.8.4
	 * @return string
	 */
	public function getAndResetLanguageForOembed() {
		$to_return                 = $this->language_for_oembed;
		$this->language_for_oembed = '';

		return $to_return;
	}

	/**
	 * Setter.
	 * @param string $language_for_oembed
	 * @since 1.8.4
	 */
	public function setLanguageForOembed( $language_for_oembed ) {
		$this->language_for_oembed = $language_for_oembed;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		/**
		 * @since 1.0.9 Hooked to 'plugins_loaded'. The 'init' is too late, because it happens after all plugins already loaded their translations.
		 */
		add_action( 'plugins_loaded', array(
			$this,
			'init_current_language'
		), 0 );

		add_action( 'plugins_loaded', array(
			$this,
			'on_load_textdomain'
		), 1 );

		/**
		 * Sets the current language and switches the translations according to the given locale.
		 *
		 * @param string $locale The locale to switch to.
		 *
		 * @since 1.9.14
		 */
		add_action( 'switch_locale', array( $this, 'on_switch_locale' ), - PHP_INT_MAX );

		/**
		 * Sets the current language and switches the translations according to the given locale.
		 *
		 * @param string $locale The locale to switch to.
		 *
		 * @since 1.9.14
		 */
		add_action( 'restore_previous_locale', array( $this, 'on_switch_locale' ), - PHP_INT_MAX );

		add_action( 'upgrader_process_complete', array( $this, 'on_activate' ), 10, 2 );


		$this->_get_options();
	}

	/**
	 * Sets the current language and switches the translations according to the given locale.
	 *
	 * @param string $locale The locale to switch to.
	 *
	 * @since 1.9.14
	 */
	public function on_switch_locale( $locale ) {
		$this->set_language( $locale );
		$this->on_load_textdomain();
	}

	/**
	 * Set the current language: if not found in the URL or REFERER, then keep the default
	 * @since 1.1.1
	 */
	public function init_current_language() {

		/**
		 * Keep the default language if any of the code before does not detect another one.
		 */
		$this->language = $this->default_language;

		/**
		 * Theoretically, we might not have any URL to get the language info from.
		 */
		$url_to_check = '';

		if ( WPGlobus_WP::is_doing_ajax() ) {
			/**
			 * If DOING_AJAX, we cannot retrieve the language information from the URL,
			 * because it's always `admin-ajax`.
			 * Therefore, we'll rely on the HTTP_REFERER (if it exists).
			 */
			if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) { // WPCS: input var ok, sanitization ok.
				$url_to_check = $_SERVER['HTTP_REFERER']; // WPCS: input var ok, sanitization ok.
			}
		} else {
			/**
			 * If not AJAX and not ADMIN then we are at the front. Will use the current URL.
			 */
			if ( ! is_admin() ) {
				$url_to_check = WPGlobus_Utils::current_url();
			}
		}

		/**
		 * If we have an URL, extract language from it.
		 * If extracted, set it as a current.
		 */
		if ( $url_to_check ) {
			$language_from_url = WPGlobus_Utils::extract_language_from_url( $url_to_check );
			if ( $language_from_url ) {
				$this->language = $language_from_url;
			}
			/**
			 * @since 1.9.17
			 * Set language for builder.
			 * For compatibility we set language here for front-end only.
			 * As for the setting in admin @see wpglobus\includes\builders\class-wpglobus-config-builder.php
			 */
			if ( $this->builder && ! is_admin() ) {
				/**
				 * We can work with Gutenberg that was defined as front-end but we should set 'language' for real front-end without builder.
				 * Any builder may have behavior like Gutenberg.
				 * @todo check each builder that WPGlobus will be support.
				 */
				if ( ! $this->builder->is_builder_page() ) {
					$this->builder->set_language($this->language);
				}	
			}			
		}

	}


	/**
	 * Check plugin version and update versioning option
	 *
	 * @param stdClass $object Plugin_Upgrader
	 * @param array    $options
	 *
	 * @return void
	 */
	public function on_activate(
		/** @noinspection PhpUnusedParameterInspection */
		$object = null,
		$options = array()
	) {

		if (
			empty( $options['plugin'] ) or $options['plugin'] !== WPGLOBUS_PLUGIN_BASENAME or
			empty( $options['action'] ) or $options['action'] !== 'update'
		) {
			/**
			 * Not our business
			 */
			return;
		}

		/**
		 * Here we can read the previous version value and do some actions if necessary.
		 * For example, warn the users about breaking changes.
		 * $version = get_option( self::$option_versioning );
		 * ...
		 */

		/**
		 * Store the current version
		 */
		update_option( self::$option_versioning, array(
			'current_version' => WPGLOBUS_VERSION
		) );

	}

	/**
	 * Set the current language to match the given locale.
	 *
	 * @since 1.9.14 : If we do not know such locale, set to default.
	 *
	 * @param string $locale The locale ('en_US', 'fr_FR', etc.).
	 */
	public function set_language( $locale ) {

		$locale_to_language = array_flip( $this->locale );

		$this->language = empty( $locale_to_language[ $locale ] )
			? $this->default_language
			: $locale_to_language[ $locale ];
	}

	/**
	 * Check for enabled locale
	 * @since 1.0.10
	 *
	 * @param string $locale
	 *
	 * @return boolean
	 */
	public function is_enabled_locale( $locale ) {
		return in_array( $locale, $this->enabled_locale, true );
	}

	/**
	 * Load textdomain
	 * @since 1.0.0
	 * @return void
	 */
	public function on_load_textdomain() {
		self::load_mofile();

		/**
		 * Can use this action to load additional translations.
		 *
		 * @since 1.9.14
		 */
		do_action( 'wpglobus_after_load_textdomain' );
	}

	/**
	 * Load .MO file from the plugin's `languages` folder.
	 * Used instead of @see load_plugin_textdomain to ignore translation files from WordPress.org, which are outdated.
	 * To force loading from a different place, use the `load_textdomain_mofile` filter.
	 *
	 * @since 1.9.6
	 */
	protected function load_mofile() {
		$domain = 'wpglobus';

		/**
		 * Delete translations that could be loaded already from the main /languages/ folder.
		 *
		 * @since 1.9.10
		 */
		unload_textdomain( $domain );

		/**
		 * Load our translations.
		 */
		$locale = apply_filters( 'plugin_locale', is_admin() ? get_user_locale() : get_locale(), $domain );
		$mofile = WPGlobus::languages_path() . '/' . $domain . '-' . $locale . '.mo';
		load_textdomain( $domain, $mofile );
	}

	/**
	 * Set flags URL.
	 * @return void
	 */
	public function _set_flags_url() {
		$this->flags_url = WPGlobus::$PLUGIN_DIR_URL . 'flags/';
		/**
		 * @since 1.9.17
		 */
		$this->flag_urls['small'] = WPGlobus::$PLUGIN_DIR_URL . 'flags/';
		$this->flag_urls['big']   = WPGlobus::$PLUGIN_DIR_URL . 'flags/big/';
	}

	/**
	 * Set flag PATH.
	 * @return void
	 */
	public function _set_flag_path() {
		$this->flag_path['small'] = WPGlobus::$PLUGIN_DIR_PATH . 'flags/';
		$this->flag_path['big']   = WPGlobus::$PLUGIN_DIR_PATH . 'flags/big/';
	}	
	
	/**
	 * Set languages by default.
	 */
	public function _set_languages() {

		/**
		 * Names, flags and locales
		 * Useful links
		 * - languages in ISO 639-1 format http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
		 * - regions http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
		 * - WordPress locales https://make.wordpress.org/polyglots/teams/
		 * - converter https://www.unicodetools.com/unicode/convert-to-html.php
		 */

		/* @noinspection SpellCheckingInspection */
		$language_table = array(
			// Prefix => Name, Native name, locale, flag.
			'ar' => array( 'Arabic', '&#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577;', 'ar', 'arle.png' ),
			'en' => array( 'English', 'English', 'en_US', 'us.png' ),
			'au' => array( 'English (AU)', 'English (AU)', 'en_AU', 'au.png' ),
			'ca' => array( 'English (CA)', 'English (CA)', 'en_CA', 'ca.png' ),
			'gb' => array( 'English (UK)', 'English (UK)', 'en_GB', 'uk.png' ),
			'zh' => array( 'Chinese', '&#31616;&#20307;&#20013;&#25991;', 'zh_CN', 'cn.png' ),
			'tw' => array( 'Chinese (TW)', '&#32321;&#39636;&#20013;&#25991;', 'zh_TW', 'mm.png' ),
			'da' => array( 'Danish', 'Dansk', 'da_DK', 'dk.png' ),
			'nl' => array( 'Dutch', 'Nederlands', 'nl_NL', 'nl.png' ),
			'gl' => array( 'Galician', 'Galego', 'gl_ES', 'galego.png' ),
			'de' => array( 'German', 'Deutsch', 'de_DE', 'de.png' ),
			'fi' => array( 'Finnish', 'Suomi', 'fi', 'fi.png' ),
			'fr' => array( 'French', 'Français', 'fr_FR', 'fr.png' ),
			'qc' => array( 'French (CA)', 'Français (CA)', 'fr_CA', 'fr_CA.png' ),
			'he' => array( 'Hebrew', '&#1506;&#1489;&#1512;&#1497;&#1514;', 'he_IL', 'il.png' ),
			'hi' => array( 'Hindi', '&#2361;&#2367;&#2344;&#2381;&#2342;&#2368;', 'hi_IN', 'in.png' ),
			'hu' => array( 'Hungarian', 'Magyar', 'hu_HU', 'hu.png' ),
			'it' => array( 'Italian', 'Italiano', 'it_IT', 'it.png' ),
			'ja' => array( 'Japanese', '&#26085;&#26412;&#35486;', 'ja', 'jp.png' ),
			'ko' => array( 'Korean', '&#54620;&#44397;&#50612;', 'ko_KR', 'kr.png' ),
			'no' => array( 'Norwegian', 'Norsk', 'nb_NO', 'no.png' ),
			'fa' => array( 'Persian', '&#1601;&#1575;&#1585;&#1587;&#1740;', 'fa_IR', 'ir.png' ),
			'pl' => array( 'Polish', 'Polski', 'pl_PL', 'pl.png' ),
			'pt' => array( 'Portuguese', 'Português', 'pt_PT', 'pt.png' ),
			'br' => array( 'Portuguese (BR)', 'Português (BR)', 'pt_BR', 'br.png' ),
			'ro' => array( 'Romanian', 'Română', 'ro_RO', 'ro.png' ),
			'ru' => array( 'Russian', 'Русский', 'ru_RU', 'ru.png' ),
			'es' => array( 'Spanish', 'Español', 'es_ES', 'es.png' ),
			'mx' => array( 'Spanish (MX)', 'Español (MX)', 'es_MX', 'mx.png' ),
			'sv' => array( 'Swedish', 'Svenska', 'sv_SE', 'se.png' ),
			'tr' => array( 'Turkish', 'Türkçe', 'tr_TR', 'tr.png' ),
			'uk' => array( 'Ukrainian', 'Українська', 'uk', 'ua.png' ),
			'vi' => array( 'Vietnamese', 'Tiếng Việt', 'vi', 'vn.png' ),
			'cy' => array( 'Welsh', 'Cymraeg', 'cy', 'cy.png' ),
			);

		foreach ( $language_table as $language => $data ) {
			list(
				$this->en_language_name[ $language ],
				$this->language_name[ $language ],
				$this->locale[ $language ],
				$this->flag[ $language ]
				) = $data;
		}

	}

	/**
	 * Initialize the language table with the hard-coded names, locales and flags.
	 *
	 * @see _set_languages For the hard-coded table.
	 */
	protected function _init_language_table() {

		update_option( $this->option_language_names, $this->language_name );
		update_option( $this->option_en_language_names, $this->en_language_name );
		update_option( $this->option_locale, $this->locale );
		update_option( $this->option_flags, $this->flag );

	}

	/**
	 * Get options from DB and wp-config.php
	 * @return void
	 */
	protected function _get_options() {

		/**
		 * For developers use only. Re-creates language table with no warning! Irreversible!
		 *
		 * @link wp-admin/?wpglobus-reset-language-table=1
		 */
		if ( ! defined( 'DOING_AJAX' ) && ! empty( $_GET['wpglobus-reset-language-table'] ) && is_admin() ) { // WPCS: input var ok, sanitization ok.
			delete_option( $this->option_language_names );
		}


		$wpglobus_option = get_option( $this->option );

		/**
		 * Get enabled languages and default language ( just one main language )
		 */
		if ( isset( $wpglobus_option['enabled_languages'] ) && ! empty( $wpglobus_option['enabled_languages'] ) ) {
			$this->enabled_languages = array();
			foreach ( $wpglobus_option['enabled_languages'] as $lang => $value ) {
				if ( ! empty( $value ) ) {
					$this->enabled_languages[] = $lang;
				}
			}

			/**
			 * Set default language
			 */
			$this->default_language = $this->enabled_languages[0];

			unset( $wpglobus_option['enabled_languages'] );
		}

		/**
		 * Set available languages for editors
		 */
		$this->open_languages = $this->enabled_languages;

		/**
		 * Set flags URL
		 */
		$this->_set_flags_url();

		/**
		 * Set flags PATH.
		 */		
		$this->_set_flag_path();

		/**
		 * Get languages name
		 * big array of used languages
		 */
		$this->language_name = get_option( $this->option_language_names );

		if ( empty( $this->language_name ) ) {

			$this->_set_languages();
			$this->_init_language_table();

		}

		/**
		 * Get locales
		 */
		$this->locale = get_option( $this->option_locale );
		if ( empty( $this->locale ) ) {

			$this->_set_languages();
			$this->_init_language_table();

		}

		/**
		 * Get enabled locales
		 */
		foreach ( $this->enabled_languages as $language ) {
			$this->enabled_locale[] = $this->locale[ $language ];
		}

		/**
		 * Get en_language_name
		 */
		$this->en_language_name = get_option( $this->option_en_language_names );

		/**
		 * Get option 'show_flag_name'
		 */
		if ( isset( $wpglobus_option['show_flag_name'] ) ) {
			$this->show_flag_name = $wpglobus_option['show_flag_name'];
			unset( $wpglobus_option['show_flag_name'] );
		}
		if ( defined( 'WPGLOBUS_SHOW_FLAG_NAME' ) ) {
			if ( 'name' === WPGLOBUS_SHOW_FLAG_NAME ) {
				$this->show_flag_name = 'name';
			} elseif ( false === WPGLOBUS_SHOW_FLAG_NAME || '' === WPGLOBUS_SHOW_FLAG_NAME ) {
				$this->show_flag_name = '';
			}
		}

		/**
		 * Get navigation menu slug for add flag in front-end 'use_nav_menu'.
		 */
		$this->nav_menu = '';

		if ( isset( $wpglobus_option['use_nav_menu'] ) ) {
			if ( '--none--' != $wpglobus_option['use_nav_menu'] ) {
				$this->nav_menu = $wpglobus_option['use_nav_menu'];
			}
			unset( $wpglobus_option['use_nav_menu'] );
		}
		
		// This can be used in `wp-config` to override the options settings.
		if ( defined( 'WPGLOBUS_USE_NAV_MENU' ) ) {
			$this->nav_menu = WPGLOBUS_USE_NAV_MENU;
		}


		/**
		 * Get selector_wp_list_pages option
		 * @since 1.0.7
		 */
		if ( empty( $wpglobus_option['selector_wp_list_pages']['show_selector'] ) ||
		     (int) $wpglobus_option['selector_wp_list_pages']['show_selector'] === 0
		) {
			$this->selector_wp_list_pages = false;
		}
		if ( isset( $wpglobus_option['selector_wp_list_pages'] ) ) {
			unset( $wpglobus_option['selector_wp_list_pages'] );
		}

		/**
		 * Get custom CSS
		 */
		if ( isset( $wpglobus_option['css_editor'] ) ) {
			$this->css_editor = $wpglobus_option['css_editor'];
			unset( $wpglobus_option['css_editor'] );
		}

		/**
		 * Get custom JS.
		 * @since 1.7.6
		 */
		if ( isset( $wpglobus_option['js_editor'] ) ) {
			$this->js_editor = $wpglobus_option['js_editor'];
			unset( $wpglobus_option['js_editor'] );
		}

		/**
		 * Old fashioned language switcher for Block Editor (Gutenberg).
		 * @since 2.2.3
		 */
		if ( isset( $wpglobus_option['block_editor_old_fashioned_language_switcher'] ) ) {
			$this->block_editor_old_fashioned_language_switcher = $wpglobus_option['block_editor_old_fashioned_language_switcher'];
			unset( $wpglobus_option['block_editor_old_fashioned_language_switcher'] );
		}

		/**
		 * Type of switcher button for WPGlobusSwitcherPlugin (Gutenberg).
		 * @since 2.2.14
		 */
		if ( isset( $wpglobus_option['block_editor_switcher_plugin_button_type'] ) ) {
			$this->block_editor_switcher_plugin_button_type = $wpglobus_option['block_editor_switcher_plugin_button_type'];
			unset( $wpglobus_option['block_editor_switcher_plugin_button_type'] );
		}
		
		/**
		 * Get flag files without path
		 */
		$option = get_option( $this->option_flags );
		if ( ! empty( $option ) ) {
			$this->flag = $option;
		}

		/**
		 * Get versioning info
		 */
		$option = get_option( self::$option_versioning );
		if ( empty( $option ) ) {
			$this->version = array();
		} else {
			$this->version = $option;
		}

		/**
		 * WPGlobus devmode.
		 */
		if ( isset( $_GET['wpglobus'] ) && 'off' === $_GET['wpglobus'] ) { // WPCS: input var ok, sanitization ok.
			$this->toggle = 'off';
		} else {
			$this->toggle = 'on';
		}

		/**
		 * Need additional check for devmode (toggle=OFF)
		 * in case 'wpglobus' was not set to 'off' at /wp-admin/post.php
		 * and $_SERVER[QUERY_STRING] is empty at the time of `wp_insert_post_data` action
		 * @see WPGlobus::on_save_post_data
		 */
		if (
			empty( $_SERVER['QUERY_STRING'] ) // WPCS: input var ok, sanitization ok.
			&& isset( $_SERVER['HTTP_REFERER'] ) // WPCS: input var ok, sanitization ok.
			&& WPGlobus_WP::is_pagenow( 'post.php' )
			&& false !== strpos( $_SERVER['HTTP_REFERER'], 'wpglobus=off' ) // WPCS: input var ok, sanitization ok.
		) {
			$this->toggle = 'off';
		}

		if ( isset( $wpglobus_option['last_tab'] ) ) {
			unset( $wpglobus_option['last_tab'] );
		}

		/**
		 * Builders.
		 * @since 1.9.17
		 */
		if ( isset( $wpglobus_option['builder_disabled'] ) && 1 === (int) $wpglobus_option['builder_disabled'] ) {
		
			require_once dirname( __FILE__ ).'/builders/class-wpglobus-config-builder.php' ; 
			$this->builder = new WPGlobus_Config_Builder(false);
			
			$this->builder_disabled = true;
			unset( $wpglobus_option['builder_disabled'] );

		} else {
			
			$this->builder_disabled = false;

			/**
			 * @since 2.2.11
			 */
			if ( empty( $wpglobus_option['builder_post_types'] ) ) {
				$builder_post_types = array();			
			} else {
				$builder_post_types = $wpglobus_option['builder_post_types'];
				unset( $wpglobus_option['builder_post_types'] );
			}
			
			/**
			 * Init post types settings.
			 *
			 * @since 2.2.11
			 */
			$builder_default_post_types = array(
				'post' => true,
				'page' => true,
				'attachment' => false
			);
			
			/** $wpglobus_option['post_type'] contains disabled post types. */
			if ( empty( $wpglobus_option['post_type'] ) ) {
				$post_types_disabled = array();
			} else {
				$post_types_disabled = array_intersect_key($builder_default_post_types, $wpglobus_option['post_type']);
			}

			if ( ! empty( $post_types_disabled ) ) {
				foreach( $post_types_disabled as $_post_type=>$status ) {
					if ( array_key_exists($_post_type, $builder_default_post_types) ) {
						$builder_default_post_types[$_post_type] = false;
					}
				}
			}
			
			if ( empty($builder_post_types) ) {
				$builder_post_types = $builder_default_post_types;
			} else {
				$builder_post_types = array_merge( $builder_default_post_types,  $builder_post_types );
			}

			require_once dirname( __FILE__ ).'/builders/class-wpglobus-config-builder.php' ; 
			$this->builder = new WPGlobus_Config_Builder(
				true, 
				array(
					'default_language' => $this->default_language, 
					'post_types' => $builder_post_types
				)
			);
		
			if ( is_admin() ) {
				
				require_once dirname( __FILE__ ) . '/class-wpglobus-config-vendor.php';
				$config_vendor = WPGlobus_Config_Vendor::get_instance( $this->builder );

				require_once dirname( __FILE__ ).'/admin/meta/class-wpglobus-meta.php' ; 
				WPGlobus_Meta::get_instance( $config_vendor::get_meta_fields(), $this->builder );
				$this->meta = $config_vendor::get_meta_fields();
				
				require_once dirname( __FILE__ ).'/wp_options/class-wpglobus-wp_options.php' ; 
				WPGlobus_WP_Options::get_instance( $config_vendor::get_wp_options() );

				$this->builder->set_multilingual_fields($config_vendor::get_ml_fields());
			}
		}

		/**
		 * @since 2.2.11
		 */
		if ( isset( $wpglobus_option['post_type'] ) ) {
			unset( $wpglobus_option['post_type'] );
		}
		
		/**
		 * Remaining wpglobus options after unset() is extended options
		 * @since 1.2.3
		 */
		$this->extended_options = $wpglobus_option;
		
		/**
		 * Option browser_redirect.
		 * @since 1.8.0
		 */
		if ( isset( $wpglobus_option['browser_redirect'] ) ) {
			$this->browser_redirect = $wpglobus_option['browser_redirect'];
			unset( $wpglobus_option['browser_redirect'] );
		}
	}

} //class

# --- EOF
