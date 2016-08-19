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

		add_action( 'upgrader_process_complete', array( $this, 'on_activate' ), 10, 2 );


		$this->_get_options();
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
			if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
				$url_to_check = $_SERVER['HTTP_REFERER'];
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
	 * Set current language
	 *
	 * @param string $locale
	 */
	public function set_language( $locale ) {
		/**
		 * @todo Maybe use option for disable/enable setting current language corresponding with $locale ?
		 */
		foreach ( $this->locale as $language => $value ) {
			if ( $locale === $value ) {
				$this->language = $language;
				break;
			}
		}
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
		load_plugin_textdomain( 'wpglobus', false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
	}

	/**
	 * Set flags URL
	 * @return void
	 */
	public function _set_flags_url() {
		$this->flags_url = WPGlobus::$PLUGIN_DIR_URL . 'flags/';
	}

	/**
	 *    Set languages by default
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
			'tw' => array( 'Chinese (TW)', '&#32321;&#39636;&#20013;&#25991;', 'zh_CN', 'cn.png' ),
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
		if ( ! defined( 'DOING_AJAX' ) && ! empty( $_GET['wpglobus-reset-language-table'] ) && is_admin() ) {
			delete_option( $this->option_language_names );
		}


		$wpglobus_option = get_option( $this->option );

		/**
		 * FIX: after "Reset All" Redux options we must reset all WPGlobus options
		 * first of all look at $wpglobus_option['more_languages']
		 */
		if ( isset( $wpglobus_option['more_languages'] ) && is_array( $wpglobus_option['more_languages'] ) ) {

			$wpglobus_option = array();
			delete_option( $this->option );
			delete_option( $this->option_language_names );
			delete_option( $this->option_en_language_names );
			delete_option( $this->option_locale );
			delete_option( $this->option_flags );

		}

		if ( isset( $wpglobus_option['more_languages'] ) ) {
			unset( $wpglobus_option['more_languages'] );
		}

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
		 * Get navigation menu slug for add flag in front-end 'use_nav_menu'
		 */
		$this->nav_menu = '';
		if ( isset( $wpglobus_option['use_nav_menu'] ) ) {
			$this->nav_menu = $wpglobus_option['use_nav_menu'];
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
		if ( isset( $_GET['wpglobus'] ) && 'off' === $_GET['wpglobus'] ) {
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
			empty( $_SERVER['QUERY_STRING'] )
			&& isset( $_SERVER['HTTP_REFERER'] )
			&& WPGlobus_WP::is_pagenow( 'post.php' )
			&& false !== strpos( $_SERVER['HTTP_REFERER'], 'wpglobus=off' )
		) {
			$this->toggle = 'off';
		}

		if ( isset( $wpglobus_option['last_tab'] ) ) {
			unset( $wpglobus_option['last_tab'] );
		}

		/**
		 * Remaining wpglobus options after unset() is extended options
		 * @since 1.2.3
		 */
		$this->extended_options = $wpglobus_option;

	}

} //class

# --- EOF
