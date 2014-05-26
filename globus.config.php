<?php
/**
 *
 */

class WPGlobus_Config {

	/*
	 * Plugin name
	 */
	const GLOBUS_PLUGIN_NAME = 'globus';

	/*
	 *	Url mode: query (questionmark)
	 */
	const GLOBUS_URL_QUERY = 1;

	/*
	 *	Url mode: pre-path
	 */
	const GLOBUS_URL_PATH = 2;

	/*
	 *	Url mode: pre-domain
	 */
	const GLOBUS_URL_DOMAIN = 3;

	/*
	 * Current language
	 *
	 * @var string
	 */
	public $language = 'en';

	/*
	 * Language by default
	 *
	 * @var string
	 */
	var $default_language = 'en';

	/*
	 * Enabled languages
	 *
	 * @var array
	 */
	var $enabled_languages = array('en','ru','de');

	/*
	 * Hide from URL language by default
	 *
	 * @var bool
	 */
	var $hide_default_language = true;

	/*
	 * URL mode
	 * query || pre-path || pre-domain
	 *
	 * @var int
	 */
	var $url_mode;

	/*
	 *	URL information
	 *
	 * @var array
	 */
	var $url_info = array();

	/*
	 *	Flag images configuration
	 *	Look in /flags/ directory for a huge list of flags for usage
	 *
	 * @var array
	 */
	var $flag = array();

	/*
	 * Location of flags (needs trailing slash!)
	 * 'plugins/globus/flags/';
	 * @var string
	 */
	var $flags_url = '';

	/*
	 *
	 * @var array
	 */
	var $language_name = array();
	
	/*
	 *
	 * @var array
	 */
	var $en_language_name = array();

	/*
	 *
	 * @var array
	 */
	var $locale = array();

	/*
	 * Use flag name for navigation menu : 'name' || 'code' || ''
	 * @var string
	 */
	var $show_flag_name = 'code';

	/*
	 * Use navigation menu by slug
	 * for use in all nav menu set value to ''
	 * @var string
	 */
	var $nav_menu = '';
	
	/*
	 * Custom CSS 
	 * @var string
	 */
	var $custom_css = '';	

	/*
	 * Constructor
	 */
	function __construct() {
		# set default url mode
		$this->url_mode = self::GLOBUS_URL_PATH;
		//$this->url_mode = self::GLOBUS_URL_QUERY;

		#
		$this->_set_languages();

		#
		$this->_get_options();
	}

	/*
	 *
	 */
	function get_url_mode(){
		return $this->url_mode;
	}

	/*
	 *
	 */
	function _set_flags(){

		$this->flag['en'] = 'gb.png';
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
		$this->flag['pt'] = 'br.png';
		$this->flag['pl'] = 'pl.png';
		$this->flag['gl'] = 'galego.png';

		$this->_set_flags_url();
	}

	/*
	 *
	 */
	function _set_flags_url() {
		$this->flags_url = plugins_url(self::GLOBUS_PLUGIN_NAME . '/flags/');
	}

	/*
	 *
	 */
	function _set_languages() {
		// Names for languages in the corresponding language, add more if needed
		$this->language_name['en'] = "English";
		$this->language_name['ru'] = "Русский";
		$this->language_name['de'] = "Deutsch";
		$this->language_name['zh'] = "中文";
		$this->language_name['fi'] = "suomi";
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
		$this->language_name['pl'] = "Polski";
		$this->language_name['gl'] = "galego";
		
		$this->en_language_name['en'] = "";
		$this->en_language_name['ru'] = "Russian";
		$this->en_language_name['de'] = "German";
		$this->en_language_name['zh'] = "Chinese";
		$this->en_language_name['fi'] = "suomi";
		$this->en_language_name['fr'] = "French";
		$this->en_language_name['nl'] = "Nederlands";
		$this->en_language_name['sv'] = "Svenska";
		$this->en_language_name['it'] = "Italian";
		$this->en_language_name['ro'] = "Română";
		$this->en_language_name['hu'] = "Magyar";
		$this->en_language_name['ja'] = "Japanese";
		$this->en_language_name['es'] = "Español";
		$this->en_language_name['vi'] = "Vietnamese";
		$this->en_language_name['ar'] = "Arabic";
		$this->en_language_name['pt'] = "Português";
		$this->en_language_name['pl'] = "Polish";
		$this->en_language_name['gl'] = "galego";

		// Locales
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
		$this->locale['pt'] = "pt_BR";
		$this->locale['pl'] = "pl_PL";
		$this->locale['gl'] = "gl_ES";

		#
		$this->_set_flags();
	}

	/*
	 * Get options from DB and wp-config.php
	 * @return void
	 */
	function _get_options(){

		$wpglobus_option = get_option('wpglobus_option');

		/*
		 * get default language
		 */
		if ( isset( $wpglobus_option['enabled_languages'] ) && ! empty($wpglobus_option['enabled_languages'])  ) {
			reset( $wpglobus_option['enabled_languages'] );
			$this->default_language = key( $wpglobus_option['enabled_languages'] );
		}
		if ( defined('WPGLOBUS_DEFAULT_LANGUAGE') ) {
			$this->default_language = $wpglobus_option['default_language'];
		}

		/*
		 * get enabled languages
		 */
		if ( isset( $wpglobus_option['enabled_languages'] ) && ! empty($wpglobus_option['enabled_languages'])  ) {

			$this->enabled_languages = array();
			$this->enabled_languages[] = $this->default_language;
			foreach( $wpglobus_option['enabled_languages'] as $code=>$language_name ) {
				if ( $code != $this->default_language && !empty($language_name)  )  {
					$this->enabled_languages[] = $code;
				}
			}
		}

		/*
		 * get option 'show_flag_name'
		 */
		if ( isset( $wpglobus_option['show_flag_name'] ) ) {
			$this->show_flag_name = $wpglobus_option['show_flag_name'];
		}
		if ( defined('WPGLOBUS_SHOW_FLAG_NAME') ) {
			if ( 'name' === WPGLOBUS_SHOW_FLAG_NAME ) {
				$this->show_flag_name = 'name';
			} elseif ( false === WPGLOBUS_SHOW_FLAG_NAME || '' === WPGLOBUS_SHOW_FLAG_NAME ) {
				$this->show_flag_name = '';
			}
		}

		/*
		 * get navigation menu slug for add flag in front-end 'use_nav_menu'
		 */
		if ( isset($wpglobus_option['use_nav_menu']) ) {
			$this->nav_menu = ( $wpglobus_option['use_nav_menu'] == 'all' ) ? '' : $wpglobus_option['use_nav_menu'];
		}
		if ( defined('WPGLOBUS_USE_NAV_MENU') ) {
			$this->nav_menu = WPGLOBUS_USE_NAV_MENU;
		}

		/*
		 * get custom CSS
		 */
		if ( isset($wpglobus_option['css_editor']) ) {
			$this->css_editor = $wpglobus_option['css_editor'];
		}
		
	}

}	// end class WPGlobus_Config
