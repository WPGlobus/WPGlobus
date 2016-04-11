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
	 * @since 1.3.0
	 * @var WPGlobus_WP_Theme $WPGlobus_WP_Theme
	 */
	public $WPGlobus_WP_Theme;

	/**
	 * Constructor
	 */
	public function __construct() {

		/**
		 * @since 1.0.9 Hooked to 'plugins_loaded'. The 'init' is too late, because it happens after all
		 *        plugins already loaded their translations.
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
	 * @param array  $options
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
		 */
		$this->language_name['en'] = "English";
		$this->language_name['ru'] = "Русский";
		$this->language_name['de'] = "Deutsch";
		$this->language_name['zh'] = "中文";
		$this->language_name['fi'] = "Suomi";
		$this->language_name['fr'] = "Français";
		$this->language_name['nl'] = "Nederlands";
		$this->language_name['sv'] = "Svenska";
		$this->language_name['it'] = "Italiano";
		$this->language_name['ro'] = "Română";
		$this->language_name['hu'] = "Magyar";
		$this->language_name['ja'] = "日本語";
		$this->language_name['es'] = "Español";
		$this->language_name['vi'] = "Tiếng Việt";
		$this->language_name['ar'] = "العربية";
		$this->language_name['pt'] = "Português";
		$this->language_name['br'] = "Português do Brazil";
		$this->language_name['pl'] = "Polski";
		$this->language_name['gl'] = "Galego";
		$this->language_name['uk'] = "Українська";

		$this->en_language_name['en'] = "English";
		$this->en_language_name['ru'] = "Russian";
		$this->en_language_name['de'] = "German";
		$this->en_language_name['zh'] = "Chinese";
		$this->en_language_name['fi'] = "Finnish";
		$this->en_language_name['fr'] = "French";
		$this->en_language_name['nl'] = "Dutch";
		$this->en_language_name['sv'] = "Swedish";
		$this->en_language_name['it'] = "Italian";
		$this->en_language_name['ro'] = "Romanian";
		$this->en_language_name['hu'] = "Hungarian";
		$this->en_language_name['ja'] = "Japanese";
		$this->en_language_name['es'] = "Spanish";
		$this->en_language_name['vi'] = "Vietnamese";
		$this->en_language_name['ar'] = "Arabic";
		$this->en_language_name['pt'] = "Portuguese";
		$this->en_language_name['br'] = "Portuguese Brazil";
		$this->en_language_name['pl'] = "Polish";
		$this->en_language_name['gl'] = "Galician";
		$this->en_language_name['uk'] = "Ukrainian";

		#Locales
		$this->locale['en'] = "en_US";
		$this->locale['ru'] = "ru_RU";
		$this->locale['de'] = "de_DE";
		$this->locale['zh'] = "zh_CN";
		$this->locale['fi'] = "fi";
		$this->locale['fr'] = "fr_FR";
		$this->locale['nl'] = "nl_NL";
		$this->locale['sv'] = "sv_SE";
		$this->locale['it'] = "it_IT";
		$this->locale['ro'] = "ro_RO";
		$this->locale['hu'] = "hu_HU";
		$this->locale['ja'] = "ja";
		$this->locale['es'] = "es_ES";
		$this->locale['vi'] = "vi";
		$this->locale['ar'] = "ar";
		$this->locale['pt'] = "pt_PT";
		$this->locale['br'] = "pt_BR";
		$this->locale['pl'] = "pl_PL";
		$this->locale['gl'] = "gl_ES";
		$this->locale['uk'] = "uk";

		#flags
		$this->flag['en'] = 'us.png';
		$this->flag['ru'] = 'ru.png';
		$this->flag['de'] = 'de.png';
		$this->flag['zh'] = 'cn.png';
		$this->flag['fi'] = 'fi.png';
		$this->flag['fr'] = 'fr.png';
		$this->flag['nl'] = 'nl.png';
		$this->flag['sv'] = 'se.png';
		$this->flag['it'] = 'it.png';
		$this->flag['ro'] = 'ro.png';
		$this->flag['hu'] = 'hu.png';
		$this->flag['ja'] = 'jp.png';
		$this->flag['es'] = 'es.png';
		$this->flag['vi'] = 'vn.png';
		$this->flag['ar'] = 'arle.png';
		$this->flag['pt'] = 'pt.png';
		$this->flag['br'] = 'br.png';
		$this->flag['pl'] = 'pl.png';
		$this->flag['gl'] = 'galego.png';
		$this->flag['uk'] = 'uk.png';

	}

	/**
	 * Set default options
	 * @return void
	 */
	protected function _set_default_options() {

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
			$this->_set_default_options();

		}

		/**
		 * Get locales
		 */
		$this->locale = get_option( $this->option_locale );
		if ( empty( $this->locale ) ) {

			$this->_set_languages();
			$this->_set_default_options();

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
			$this->nav_menu = ( $wpglobus_option['use_nav_menu'] == 'all' ) ? 'all' : $wpglobus_option['use_nav_menu'];
			unset( $wpglobus_option['use_nav_menu'] );
		}
		if ( defined( 'WPGLOBUS_USE_NAV_MENU' ) ) {
			$this->nav_menu = WPGLOBUS_USE_NAV_MENU;
		}


		/**
		 * Get selector_wp_list_pages option
		 * @since 1.0.7
		 */
		if ( empty( $wpglobus_option['selector_wp_list_pages']['show_selector'] ) || $wpglobus_option['selector_wp_list_pages']['show_selector'] == 0 ) {
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
