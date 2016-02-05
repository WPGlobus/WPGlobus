<?php
/**
 * @package   WPGlobus
 */

/**
 * Class WPGlobus_Options
 * Based on ReduxFramework Sample Config File
 * For full documentation, please visit: https://docs.reduxframework.com
 */
class WPGlobus_Options {

	public $args = array();
	public $sections = array();
	public $theme;
	public $ReduxFramework;

	private $menus = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		$nav_menus = WPGlobus::_get_nav_menus();

		foreach ( $nav_menus as $menu ) {
			$this->menus[ $menu->slug ] = $menu->name;
		}
		if ( ! empty( $nav_menus ) && count( $nav_menus ) > 1 ) {
			$this->menus['all'] = 'All';
		}

		add_action( 'init', array( $this, 'initSettings' ) );

		/** remove redux menu under the tools **/
		add_action( 'admin_menu', array( $this, 'remove_redux_menu' ), 12 );

	}

	public function remove_redux_menu() {
		remove_submenu_page( 'tools.php', 'redux-about' );
	}

	public function initSettings() {

		if ( ! class_exists( 'ReduxFramework' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once WPGlobus::$PLUGIN_DIR_PATH . 'lib/ReduxCore/framework.php';
		}

		$config = WPGlobus::Config();

		/**
		 * To avoid any conflict with ReduxFramework embedded in theme, always use our own field classes.
		 * Even the standard fields we use are forked and prefixed with 'wpglobus_'.
		 */
		foreach (
			array(
				'wpglobus_info',
				'wpglobus_sortable',
				'wpglobus_select',
				'wpglobus_checkbox',
				'wpglobus_ace_editor',
				'table',
				'post_types'
			) as $field_type
		) {

			add_filter( "redux/{$config->option}/field/class/{$field_type}", array(
					$this,
					'add_custom_redux_fields'
				)
				, 0, 2 );
		}

		// Set the default arguments
		$this->setArguments();

		// Set a few help tabs so you can see how it's done
		$this->setHelpTabs();

		// Create the sections and fields
		$this->setSections();

		if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
			return;
		}

		/** @noinspection PhpUndefinedClassInspection */
		$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
	}

	/**
	 * Tell Redux where to find our custom fields
	 *
	 * @since 1.2.2
	 *
	 * @param string $file  Path of the field class where Redux is looking for it
	 * @param array  $field Field parameters
	 *
	 * @return string Path of the field class where we want Redux to find it
	 */
	public function add_custom_redux_fields( $file, $field ) {
		if ( ! file_exists( $file ) ) {
			$file = WPGlobus::$PLUGIN_DIR_PATH .
			        "includes/options/fields/{$field['type']}/field_{$field['type']}.php";
		}

		return $file;
	}

	public function setSections() {

		$config = WPGlobus::Config();

		/** @var array $wpglobus_option */
		$wpglobus_option = get_option( $config->option );


		$fields_home = array();

		/**
		 * Display warning if an old Redux is loaded
		 *
		 * @see  ReduxFrameworkInstances::get_all_instances()
		 */
		/** @noinspection PhpUndefinedClassInspection */
		if ( version_compare( ReduxFramework::$_version, WPGlobus::$minimalReduxFramework_version ) < 0 ) {
			/** @noinspection PhpUndefinedClassInspection */
			$fields_home[] =
				array(
					'id'     => 'version_warning',
					'type'   => 'wpglobus_info',
					'title'  => esc_html__( 'WARNING: old version of the ReduxFramework is active!', 'wpglobus' ),
					'desc'   => '</br>' .
					            sprintf(
						            esc_html__( 'WPGlobus settings panel requires ReduxFramework %1$s or later.' ),
						            WPGlobus::$minimalReduxFramework_version
					            ) .
					            '</br>' .
					            '</br>' .
					            // translators: ReduxFramework - %1$s version, %2$s folder where installed
					            sprintf( esc_html__( 'The currently active ReduxFramework (version %1$s) was loaded from the %2$s folder.', 'wpglobus' ),
						            ReduxFramework::$_version,
						            '<code>' . ReduxFramework::$_dir . '</code>'
					            ) .
					            '</br>' .
					            '</br>' .
					            '<strong>' .
					            // translators: %1$s placeholder for the link to ReduxFramework plugin
					            sprintf( esc_html__( 'We recommend you to install the most recent version of the ReduxFramework plugin: %1$s.', 'wpglobus' ), '<a href="https://wordpress.org/plugins/redux-framework/">https://wordpress.org/plugins/redux-framework/</a>' ) .
					            '</strong>'
				,
					'style'  => 'critical',
					'notice' => false,
				);
		}

		/**
		 * The Welcome message
		 */
		$fields_home[] =
			array(
				'id'     => 'welcome_intro',
				'type'   => 'wpglobus_info',
				'title'  => __( 'Thank you for installing WPGlobus!', 'wpglobus' ),
				'desc'   => '' .
				            '<br/>' .
				            '&bull; ' .
				            '<a href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_ABOUT . '">' .
				            __( 'Read About WPGlobus', 'wpglobus' ) .
				            '</a>' .
				            '<br/>' .
				            '&bull; ' . __( 'Click the <strong>[Languages]</strong> tab at the left to setup the options.', 'wpglobus' ) .
				            '<br/>' .
				            '&bull; ' . __( 'Use the <strong>[Languages Table]</strong> section to add a new language or to edit the language attributes: name, code, flag icon, etc.', 'wpglobus' ) .
				            '<br/>' .
				            '<br/>' .
				            __( 'Should you have any questions or comments, please do not hesitate to contact us.', 'wpglobus' ) .
				            '<br/>' .
				            '<br/>' .
				            '<em>' .
				            __( 'Sincerely Yours,', 'wpglobus' ) .
				            '<br/>' .
				            __( 'The WPGlobus Team', 'wpglobus' ) .
				            '</em>' .
				            '',
				'style'  => 'info',
				'notice' => false,
			);

		/**
		 * For Google Analytics
		 */
		$ga_campaign = '?utm_source=wpglobus-admin-clean&utm_medium=link&utm_campaign=talk-to-us';

		$url_wpglobus_site               = WPGlobus_Utils::url_wpglobus_site();
		$url_wpglobus_site_submit_ticket = $url_wpglobus_site . 'support/submit-ticket/' . $ga_campaign;

		$fields_home[] =
			array(
				'id'     => 'wpglobus_clean',
				'type'   => 'wpglobus_info',
				'title'  => __( 'Deactivating / Uninstalling', 'wpglobus' ),
				'desc'   => '' .
				            '<p><em>' .
				            sprintf(
					            esc_html(
					            /* translators: %?$s: HTML codes for hyperlink. Do not remove. */
						            __( 'We would hate to see you go. If something goes wrong, do not uninstall WPGlobus yet. Please %1$stalk to us%2$s and let us help!', 'wpglobus' ) ),
					            '<a href="' . $url_wpglobus_site_submit_ticket . '" target="_blank">',
					            '</a>'
				            ) .
				            '</em></p>' .
				            '<hr/>' .
				            '<p><i class="el el-exclamation-sign" style="color:red"></i> <strong>' .
				            esc_html( __( 'Please note that if you deactivate WPGlobus, your site will show all the languages together, mixed up. You will need to remove all translations, keeping only one language.', 'wpglobus' ) ) .
				            '</strong></p>' .
				            '<p>' .
				            /* translators: %s: link to the Clean-up Tool */
				            sprintf( __( 'If there are just a few places, you should edit them manually. To automatically remove all translations at once, you can use the %s. WARNING: The clean-up operation is irreversible, so use it only if you need to completely uninstall WPGlobus.', 'wpglobus' ),
					            /* translators: %?$s: HTML codes for hyperlink. Do not remove. */
					            sprintf( __( '%1$sClean-up Tool%2$s', 'wpglobus' ),
						            '<a href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_CLEAN . '">',
						            '</a>'
					            ) ) .
				            '</p>' .
				            '',
				'style'  => 'normal',
				'notice' => false,
			);


		$this->sections[] = array(
			'wpglobus_id' => 'welcome',
			'title'       => __( 'Welcome!', 'wpglobus' ),
			'icon'        => 'el-icon-globe',
			'fields'      => $fields_home
		);

		/*
		 * SECTION: languages
		 */

		/** @var array $enabled_languages contains all enabled languages */
		$enabled_languages = array();

		/** @var array $defaults_for_enabled_languages Need for the sortable field setup */
		$defaults_for_enabled_languages = array();

		/** @var array $more_languages */
		$more_languages = array();

		foreach ( $config->enabled_languages as $code ) {
			$lang_in_en = '';
			if ( isset( $config->en_language_name[ $code ] ) && ! empty( $config->en_language_name[ $code ] ) ) {
				$lang_in_en = ' (' . $config->en_language_name[ $code ] . ')';
			}

			$enabled_languages[ $code ]              = $config->language_name[ $code ] . $lang_in_en;
			$defaults_for_enabled_languages[ $code ] = true;
		}

		/** Add language from 'more_language' option to array $enabled_languages */
		if ( isset( $wpglobus_option['more_languages'] ) && ! empty( $wpglobus_option['more_languages'] ) ) {

			$lang       = $wpglobus_option['more_languages'];
			$lang_in_en = '';
			if ( isset( $config->en_language_name[ $lang ] ) && ! empty( $config->en_language_name[ $lang ] ) ) {
				$lang_in_en = ' (' . $config->en_language_name[ $lang ] . ')';
			}

			$enabled_languages[ $lang ] = $config->language_name[ $lang ] . $lang_in_en;

			$wpglobus_option['enabled_languages'][ $wpglobus_option['more_languages'] ] =
				$config->language_name[ $wpglobus_option['more_languages'] ];
			update_option( $config->option, $wpglobus_option );

		}

		/** Generate array $more_languages */
		foreach ( $config->flag as $code => $file ) {
			if ( ! array_key_exists( $code, $enabled_languages ) ) {
				$lang_in_en = '';
				if ( isset( $config->en_language_name[ $code ] ) && ! empty( $config->en_language_name[ $code ] ) ) {
					$lang_in_en = ' (' . $config->en_language_name[ $code ] . ')';
				}
				$more_languages[ $code ] = $config->language_name[ $code ] . $lang_in_en;
			}
		}


		/*
		 * for miniGLOBUS
		 */
		if ( empty( $this->menus ) ) {
			$navigation_menu_placeholder = __( 'No navigation menu', 'wpglobus' );
		} else {
			$navigation_menu_placeholder = __( 'Select navigation menu', 'wpglobus' );
		}

		$desc_languages_intro = implode( '', array(
			'<ul style="list-style: disc; list-style-position: inside;">',
			// translators: %s placeholder for the icon (actual picture)
			'<li>' . sprintf( __( 'Place the <strong>main language</strong> of your site at the top of the list by dragging the %s icons.', 'wpglobus' ), '<i class="el el-move icon-large"></i>' ) . '</li>',
			'<li>' . __( '<strong>Uncheck</strong> the languages you do not plan to use.', 'wpglobus' ) . '</li>',
			'<li>' . __( '<strong>Add</strong> more languages using the section below.', 'wpglobus' ) . '</li>',
			'<li>' . __( 'When done, click the [Save Changes] button.', 'wpglobus' ) . '</li>',
			'</ul>'
		) );

		$desc_more_languages =
			__( 'Choose a language you would like to enable. <br>Press the [Save Changes] button to confirm.',
				'wpglobus' ) . '<br /><br />';
		// translators: %1$s and %2$s - placeholders to insert HTML link around 'here'
		$desc_more_languages .= sprintf( __( 'or Add new Language %1$s here %2$s', 'wpglobus' ),
			'<a href="?page=wpglobus_language_edit&action=add">', '</a>' );

		$this->sections[] = array(
			'wpglobus_id' => 'languages',
			'title'       => esc_html__( 'Languages', 'wpglobus' ),
			'icon'        => 'el-icon-wrench-alt',
			'fields'      => array(
				array(
					'id'       => 'languages_intro',
					'type'     => 'wpglobus_info',
					'title'    => esc_html__( 'Instructions:', 'wpglobus' ),
					'subtitle' => esc_html__( 'NOTE: you cannot remove the main language.', 'wpglobus' ),
					'desc'     => $desc_languages_intro,
					'style'    => 'info',
					'notice'   => false
				),
				array(
					'id'       => 'enabled_languages',
					'type'     => 'wpglobus_sortable',
					'title'    => __( 'Enabled Languages', 'wpglobus' ),
					'compiler' => 'false',
					'subtitle' => __( 'These languages are currently enabled on your site.', 'wpglobus' ),
					'options'  => $enabled_languages,
					'default'  => $defaults_for_enabled_languages,
					'mode'     => 'checkbox',
				),
				array(
					'id'          => 'more_languages',
					'type'        => 'wpglobus_select',
					'title'       => __( 'Add Languages', 'wpglobus' ),
					'compiler'    => 'false',
					'mode'        => false,
					'desc'        => $desc_more_languages,
					'placeholder' => __( 'Select a language', 'wpglobus' ),
					'options'     => $more_languages,
				),
				array(
					'id'       => 'show_flag_name',
					'type'     => 'wpglobus_select',
					'title'    => __( 'Language Selector Mode', 'wpglobus' ),
					'compiler' => 'false',
					'mode'     => false,
					'desc'     => __( 'Choose the way language name and country flag are shown in the drop-down menu', 'wpglobus' ),
					'select2'  => array(
						'allowClear'              => false,
						'minimumResultsForSearch' => - 1
					),
					'options'  => array(
						'code'      => __( 'Two-letter Code with flag (en, ru, it, etc.)', 'wpglobus' ),
						'full_name' => __( 'Full Name (English, Russian, Italian, etc.)', 'wpglobus' ),
						/* @since 1.2.1 */
						'name'      => __( 'Full Name with flag (English, Russian, Italian, etc.)', 'wpglobus' ),
						'empty'     => __( 'Flags only', 'wpglobus' )
					),
					'default'  => 'code'
				),
				array(
					'id'          => 'use_nav_menu',
					# $WPGlobus_Config->nav_menu
					'type'        => 'wpglobus_select',
					'title'       => __( 'Language Selector Menu', 'wpglobus' ),
					'compiler'    => 'false',
					'mode'        => false,
					'desc'        => __( 'Choose the navigation menu where the language selector will be shown', 'wpglobus' ),
					'select2'     => array(
						'allowClear'              => true,
						'minimumResultsForSearch' => - 1
					),
					'options'     => $this->menus,
					'placeholder' => $navigation_menu_placeholder,
				),
				array(
					'id'       => 'selector_wp_list_pages',
					'type'     => 'wpglobus_checkbox',
					'title'    => __( '"All Pages" menus Language selector', 'wpglobus' ),
					'subtitle' => __( '(Found in some themes)', 'wpglobus' ),
					'desc'     => __( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
					'compiler' => 'false',
					'default'  => 1,
					'options'  => array(
						'show_selector' => __( 'Enable', 'wpglobus' )
					),
				),
				array(
					'id'       => 'css_editor',
					'type'     => 'wpglobus_ace_editor',
					'title'    => __( 'Custom CSS', 'wpglobus' ),
					'mode'     => 'css',
					'theme'    => 'chrome',
					'compiler' => 'false',
					'desc'     => __( 'Here you can enter the CSS rules to adjust the language selector menu for your theme. Look at the examples in the `style-samples.css` file.', 'wpglobus' ),
					'subtitle' => __( '(Optional)', 'wpglobus' ),
					'default'  => '',
					'rows'     => 15
				)
			)
		);

		/*
		*	SECTION: Language table
		*/
		$this->sections[] = array(
			'wpglobus_id' => 'language_table',
			'title'       => __( 'Languages table', 'wpglobus' ),
			'icon'        => 'el-icon-th-list',
			'fields'      => array(
				array(
					'id'       => 'description',
					'type'     => 'wpglobus_info',
					'title'    => __( 'Use this table to add, edit or delete languages.', 'wpglobus' ),
					'subtitle' => __( 'NOTE: you cannot remove the main language.', 'wpglobus' ),
					'style'    => 'info',
					'notice'   => false
				),
				array(
					'id'   => 'lang_new',
					'type' => 'table'
				)
			)
		);

		/**
		 *    SECTION: Post types
		 */
		$post_types = get_post_types( array( '_builtin' => true ) );

		$fields = array(
			array(
				'id'     => 'description',
				'type'   => 'wpglobus_info',
				'title'  => __( 'Uncheck to disable WPGlobus', 'wpglobus' ),
				'style'  => 'info',
				'notice' => false
			),
		);

		$default         = array();
		$open_post_types = array();
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, array( 'attachment', 'revision', 'nav_menu_item' ), true ) ) {
				$open_post_types[ $post_type ] = $post_type;
				$default[ $post_type ]         = true;
			}
		}

		$fields[] = array(
			'id'       => 'post_type',
			'type'     => 'wpglobus_checkbox',
			'compiler' => false,
			'default'  => $default,
			'options'  => $open_post_types
		);

		$fields[] = array(
			'id'   => 'custom_post_types',
			'type' => 'post_types'
		);

		$this->sections[] = array(
			'wpglobus_id' => 'post_types',
			'title'       => __( 'Post types', 'wpglobus' ),
			'icon'        => 'el-icon-th-list',
			'fields'      => $fields
		);

		/**
		 * SECTION: Add-ons
		 * We need add it for menu item only
		 */
		$this->sections[] = array(
			'wpglobus_id' => 'add_ons',
			'title'       => __( 'Add-ons', 'wpglobus' ),
			'icon'        => 'el-icon-th-list',
			'class'       => 'wpglobus-addons-group hidden'
		);

		/**
		 * Filter the array of sections.
		 *
		 * @since 1.0.11
		 *
		 * @param array $sections Array of Redux sections.
		 */
		$this->sections = apply_filters( 'wpglobus_option_sections', $this->sections );

	}

	public function setHelpTabs() {
		$this->args['help_tabs']    = array();
		$this->args['help_sidebar'] = '';
	}

	/**
	 * All the possible arguments for Redux.
	 * For full documentation on arguments, please refer to:
	 * https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
	 **/
	public function setArguments() {

		$this->args = array(
			// TYPICAL -> Change these values as you need/desire
			'opt_name'        => WPGlobus::Config()->option,
			// This is where your data is stored in the database and also becomes your global variable name.
			'display_name'    => 'WPGlobus',
			// Name that appears at the top of your panel
			'display_version' => WPGLOBUS_VERSION,
			// Version that appears at the top of your panel
			'menu_type'       => 'menu',
			//Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
			'allow_sub_menu'  => true,
			// Show the sections below the admin menu item or not
			'menu_title'      => 'WPGlobus',
			'page_title'      => 'WPGlobus',
			// You will need to generate a Google API key to use this feature.
			// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
			'google_api_key'  => '',
			// Must be defined to add google fonts to the typography module

			'async_typography'   => false,
			// Use a asynchronous font on the front end or font string
			'admin_bar'          => false,
			// Show the panel pages on the admin bar
			'global_variable'    => '',
			// Set a different name for your global variable other than the opt_name
			'dev_mode'           => false,
			// Show the time the page took to load, etc
			'customizer'         => true,
			// Enable basic customizer support

			// OPTIONAL -> Give you extra features
			'page_priority'      => null,
			// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
			'page_parent'        => 'themes.php',
			// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
			'page_permissions'   => 'manage_options',
			// Permissions needed to access the options panel.
			'menu_icon'          => '',
			// Specify a custom URL to an icon
			'last_tab'           => '',
			// Force your panel to always open to a specific tab (by id)
			'page_icon'          => 'icon-themes',
			// Icon displayed in the admin panel next to your menu_title
			'page_slug'          => WPGlobus::OPTIONS_PAGE_SLUG,
			// Page slug used to denote the panel
			'save_defaults'      => true,
			// On load save the defaults to DB before user clicks save or not
			'default_show'       => false,
			// If true, shows the default value next to each field that is not the default value.
			'default_mark'       => '',
			// What to print by the field's title if the value shown is default. Suggested: *
			'show_import_export' => false,
			// Shows the Import/Export panel when not used as a field.

			// CAREFUL -> These options are for advanced use only
			'transient_time'     => 60 * MINUTE_IN_SECONDS,
			'output'             => true,
			// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
			'output_tag'         => true,
			// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
			'footer_credit'      => '&copy; Copyright 2014-' . date( 'Y' ) .
			                        ', <a href="' . WPGlobus::URL_WPGLOBUS_SITE . '">TIV.NET INC. / WPGlobus</a>.',
			'database'           => 'options',
			// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
			'system_info'        => false,
			// REMOVE

			'hide_reset'       => true,
			'disable_tracking' => true,
			/**
			 * With newer ReduxFramework, need to disable AJAX save,
			 * so that list of languages is always fresh, after save.
			 *
			 * @since 1.2.2
			 */
			'ajax_save'        => false,
			// HINTS
			'hints'            => array(
				'icon'          => 'icon-question-sign',
				'icon_position' => 'right',
				'icon_color'    => 'lightgray',
				'icon_size'     => 'normal',
				'tip_style'     => array(
					'color'   => 'light',
					'shadow'  => true,
					'rounded' => false,
					'style'   => '',
				),
				'tip_position'  => array(
					'my' => 'top left',
					'at' => 'bottom right',
				),
				'tip_effect'    => array(
					'show' => array(
						'effect'   => 'slide',
						'duration' => '500',
						'event'    => 'mouseover',
					),
					'hide' => array(
						'effect'   => 'slide',
						'duration' => '500',
						'event'    => 'click mouseleave',
					),
				),
			)
		);

		$this->args['intro_text'] = include 'wpglobus-options-header.php';

		// Add content after the form.
		//		$this->args['footer_text'] =
		//			'&copy; Copyright 2014-' . date( 'Y' ) . ', <a href="' . WPGlobus::URL_WPGLOBUS_SITE . '">WPGlobus</a>.';


		// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
		$ga_campaign = '?utm_source=wpglobus-options-socials&utm_medium=link&utm_campaign=options-panel';

		$this->args['share_icons'][] = array(
			'url'   => 'http://www.wpglobus.com/quick-start/' . $ga_campaign,
			'title' => esc_html__( 'Read the Quick Start Guide', 'wpglobus' ),
			'icon'  => 'el el-question-sign'
		);
		$this->args['share_icons'][] = array(
			'url'   => 'http://www.wpglobus.com/' . $ga_campaign,
			'title' => esc_html__( 'Visit our website', 'wpglobus' ),
			'icon'  => 'el el-globe'
		);
		$this->args['share_icons'][] = array(
			'url'   => 'http://www.wpglobus.com/shop/extensions/woocommerce-wpglobus/' . $ga_campaign,
			'title' => esc_html__( 'Buy WooCommerce WPGlobus extension', 'wpglobus' ),
			'icon'  => 'el el-icon-shopping-cart'
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://github.com/WPGlobus',
			'title' => esc_html__( 'Collaborate on GitHub', 'wpglobus' ),
			'icon'  => 'el el-github'
			//'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://www.facebook.com/WPGlobus',
			'title' => esc_html__( 'Like us on Facebook', 'wpglobus' ),
			'icon'  => 'el el-facebook'
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://twitter.com/WPGlobus',
			'title' => esc_html__( 'Follow us on Twitter', 'wpglobus' ),
			'icon'  => 'el el-twitter'
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://www.linkedin.com/company/wpglobus',
			'title' => esc_html__( 'Find us on LinkedIn', 'wpglobus' ),
			'icon'  => 'el el-linkedin'
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://plus.google.com/+Wpglobus',
			'title' => esc_html__( 'Circle us on Google+', 'wpglobus' ),
			'icon'  => 'el el-googleplus'
		);

	}

} // class

# --- EOF
