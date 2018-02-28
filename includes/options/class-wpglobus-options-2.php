<?php
/**
 * File: class-wpglobus-options-2.php
 *
 * @package     WPGlobus\Admin\Options
 * @author      WPGlobus
 */

// Load the Request class.
require_once dirname( dirname( __FILE__ ) ) . '/admin/class-wpglobus-language-edit-request.php';

/**
 * Class WPGlobus_Options.
 */
class WPGlobus_Options {

	const NONCE_ACTION = 'wpglobus-options-panel';

	public $args = array();
	public $sections = array();
	public $theme;

	private $config;

	private $page_slug;

	private $tab;

	private $menus = array();

	private $current_page;

	/**
	 * Constructor
	 */
	public function __construct() {

		// @todo 'wpglobus-options' make as WPGlobus::OPTIONS_PAGE_SLUG
//		$this->page_slug = WPGlobus::OPTIONS_PAGE_SLUG;

		$this->page_slug = 'wpglobus-options';

		// Handle the main options form submit.
		// If data posted, the options will be updated, and page reloaded (so no continue to the next line).
		$this->handle_submit();

		$this->current_page = WPGlobus_Utils::safe_get( 'page' );

		$_tab = WPGlobus_Utils::safe_get( 'tab' );
		if ( empty( $_tab ) ) {
			$_tab = 0;
		}
		$this->tab = (int) $_tab;

		// error_log(print_r('HERE $this->tab : '.$this->tab, true));


		$nav_menus = WPGlobus::_get_nav_menus();

		foreach ( $nav_menus as $menu ) {
			$this->menus[ $menu->slug ] = $menu->name;
		}
		if ( ! empty( $nav_menus ) && count( $nav_menus ) > 1 ) {
			$this->menus['all'] = 'All';
		}

		add_action( 'init', array( $this, 'initSettings' ) );

		add_action( 'admin_menu', array(
			$this,
			'on__admin_menu',
		), 10 );

		add_action( 'admin_print_scripts', array(
			$this,
			'on__admin_scripts',
		) );

		add_action( 'admin_print_styles', array(
			$this,
			'on__admin_styles',
		) );

	}

	public function initSettings() {

		/*
		if ( ! class_exists( 'ReduxFramework' ) ) {
			require_once WPGlobus::$PLUGIN_DIR_PATH . 'lib/ReduxCore/framework.php';
		} */

		$this->config = WPGlobus::Config();

		/**
		 * To avoid any conflict with ReduxFramework embedded in theme, always use our own field classes.
		 * Even the standard fields we use are forked and prefixed with 'wpglobus_'.
		 */
		//*
		foreach (
			array(
				'wpglobus_info',
				'wpglobus_sortable',
				'wpglobus_select',
				'wpglobus_checkbox',
				'wpglobus_ace_editor',
				'table',
				'post_types',
			) as $field_type
		) {
			add_filter( "wpglobus/options/field/{$field_type}", array(
					$this,
					'filter__add_custom_fields',
				)
				, 0, 2 );
		}
		// */

		// Set the default arguments.
		$this->setArguments();

		// Set a few help tabs so you can see how it's done
		//$this->setHelpTabs();

		// Create the sections and fields.
		$this->setSections();

		if ( ! isset( $this->args['opt_name'] ) ) { // No errors please
			return;
		}

		/** @noinspection PhpUndefinedClassInspection */
		//$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
	}

	/**
	 * @todo add doc.
	 * @return void
	 */
	public function on__admin_menu() {
		add_menu_page(
			$this->args['page_title'],
			$this->args['menu_title'],
			'administrator',
			$this->page_slug,
			array( $this, 'pageOptions' ),
			'dashicons-admin-site'
		);
	}

	public function pageOptions() {
		?>
		<div class="wrap">
			<h1>WPGlobus <?php echo esc_html( WPGLOBUS_VERSION ); /** or anyway to stay this header empty to insert admin notices correctly */ ?></h1>
			<div class="wpglobus-options-container">
				<form action="" method="post">
					<div id="wpglobus-options-intro-text"><?php echo $this->args['intro_text']; ?></div>
					<div class="wpglobus-options-wrap">
						<div class="wpglobus-options-sidebar wpglobus-options-wrap__item">
							<ul class="wpglobus-options-menu">
								<?php foreach ( $this->sections as $section_tab => $section ) {
//									if ( $section['wpglobus_id'] == 'languages' ) {
									//error_log(print_r($section, true)); // !!!!!!
//									}

									if ( empty( $section['tab_href'] ) ) {
										// No real link, just switch tab.
										$tab_href = '#';
										$li_class = 'wpglobus-tab-link';
									} else {
										// Real link specified. Use it and do not set the tab switching CSS class.
										$tab_href = $section['tab_href'];
										$li_class = '';
									}
									?>
									<li id="wpglobus-tab-link-<?php echo esc_attr( $section_tab ); ?>"
											class="<?php echo esc_attr( $li_class ); ?>"
											data-tab="<?php echo esc_attr( $section_tab ); ?>">
										<a href="<?php echo esc_url( $tab_href ); ?>"
												data-tab="<?php echo esc_attr( $section_tab ); ?>">
											<i class="<?php echo esc_attr( $section['icon'] ); ?>"></i>
											<span class="group_title"><?php echo esc_html( $section['title'] ); ?></span>
										</a>
									</li>
								<?php } ?>
							</ul>
						</div><!-- sidebar -->
						<div class="wpglobus-options-main wpglobus-options-wrap__item">
							<div class="wpglobus-options-info">
								<?php foreach ( $this->sections as $section_tab => $section ) {
									?>
									<div id="section-tab-<?php echo $section_tab; ?>" class="wpglobus-options-tab">
										<h2><?php echo $section['title']; ?></h2>
										<?php
										if ( ! empty( $section['fields'] ) ) {
											foreach ( $section['fields'] as $field ) {
												$field_type = $field['type'];
												$file       = apply_filters( "wpglobus/options/field/{$field_type}", '', $field );
												if ( $file && file_exists( $file ) ) :
													require $file;
												endif; ?>
											<?php }
											/** end foreach **/
										}
										?>
									</div><!-- .wpglobus-options-tab -->
								<?php } ?>
								<?php
								wp_nonce_field( self::NONCE_ACTION );
								?>
							</div><!-- .wpglobus-options-info -->
						</div><!-- wpglobus-options-main block -->
						<?php submit_button(); ?>
					</div>
				</form>
			</div>
			<div class="clear"></div>
		</div><!-- .wrap -->
		<?php

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
			'menu_title'      => 'WPGlobus 2',
			// @todo remove 2 after deleting old options.
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
			'page_slug'          => $this->page_slug,
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
									', <a href="' . WPGlobus_Utils::url_wpglobus_site() . '">TIV.NET INC. / WPGlobus</a>.',
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
			),
		);

		// TODO use this space.
		$this->args['intro_text'] = '&nbsp;';

		// Add content after the form.
		//		$this->args['footer_text'] =
		//			'&copy; Copyright 2014-' . date( 'Y' ) . ', <a href="' . WPGlobus::URL_WPGLOBUS_SITE . '">WPGlobus</a>.';


		// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
		$ga_campaign = '?utm_source=wpglobus-options-socials&utm_medium=link&utm_campaign=options-panel';

		$this->args['share_icons'][] = array(
			'url'   => WPGlobus_Utils::url_wpglobus_site() . 'quick-start/' . $ga_campaign,
			'title' => esc_html__( 'Read the Quick Start Guide', 'wpglobus' ),
			'icon'  => 'el el-question-sign',
		);
		$this->args['share_icons'][] = array(
			'url'   => WPGlobus_Utils::url_wpglobus_site() . $ga_campaign,
			'title' => esc_html__( 'Visit our website', 'wpglobus' ),
			'icon'  => 'el el-globe',
		);
		$this->args['share_icons'][] = array(
			'url'   => WPGlobus_Utils::url_wpglobus_site() . 'product/woocommerce-wpglobus/' . $ga_campaign,
			'title' => esc_html__( 'Buy WooCommerce WPGlobus extension', 'wpglobus' ),
			'icon'  => 'el el-icon-shopping-cart',
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
			'icon'  => 'el el-facebook',
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://twitter.com/WPGlobus',
			'title' => esc_html__( 'Follow us on Twitter', 'wpglobus' ),
			'icon'  => 'el el-twitter',
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://www.linkedin.com/company/wpglobus',
			'title' => esc_html__( 'Find us on LinkedIn', 'wpglobus' ),
			'icon'  => 'el el-linkedin',
		);
		$this->args['share_icons'][] = array(
			'url'   => 'https://plus.google.com/+Wpglobus',
			'title' => esc_html__( 'Circle us on Google+', 'wpglobus' ),
			'icon'  => 'el el-googleplus',
		);

	}

	/**
	 * Set sections.
	 */
	public function setSections() {

		/** @var array $wpglobus_option */
		//$wpglobus_option = get_option( $this->config->option );

		$this->sections[] = $this->welcomeSection();
		$this->sections[] = $this->languagesSection();
		$this->sections[] = $this->languageTableSection();

		// Checking class_exists because those classes are not loaded in DOING_AJAX, but the entire options panel is.

		if ( class_exists( 'WPGlobus_Admin_HelpDesk', false ) ) {
			$this->sections[] = $this->helpdeskSection();
		}

		if ( class_exists( 'WPGlobus_Admin_Page', false ) ) {
			$this->sections[] = $this->addonsSection();
		}

		if ( ! defined( 'WOOCOMMERCE_WPGLOBUS_VERSION' ) && class_exists( 'WooCommerce' ) ) {
			$this->sections[] = $this->section_recommend_wpg_wc();
		}

		/**
		 * Filter the array of sections.
		 *
		 * @param array $sections Array of sections.
		 */
		$this->sections = apply_filters( 'wpglobus_option_sections', $this->sections );

	}

	/**
	 * SECTION: Welcome.
	 */
	public function welcomeSection() {

		$fields_home = array();

		/**
		 * The Welcome message.
		 */
		$fields_home[] =
			array(
				'id'     => 'welcome_intro',
				'type'   => 'wpglobus_info',
				'title'  => esc_html__( 'Thank you for installing WPGlobus!', 'wpglobus' ),
				'desc'   => '' .
							'&bull; ' .
							'<a href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_ABOUT . '">' .
							esc_html__( 'Read About WPGlobus', 'wpglobus' ) .
							'</a>' .
							'<br/>' .
							'&bull; ' . sprintf( esc_html__( 'Click the %1$s[Languages]%2$s tab at the left to setup the options.', 'wpglobus' ), '<strong>', '</strong>' ) .
							'<br/>' .
							'&bull; ' . sprintf( esc_html__( 'Use the %1$s[Languages Table]%2$s section to add a new language or to edit the language attributes: name, code, flag icon, etc.', 'wpglobus' ), '<strong>', '</strong>' ) .
							'<br/>' .
							'<br/>' .
							esc_html__( 'Should you have any questions or comments, please do not hesitate to contact us.', 'wpglobus' ) .
							'<br/>' .
							'<br/>' .
							'<em>' .
							esc_html__( 'Sincerely Yours,', 'wpglobus' ) .
							'<br/>' .
							esc_html__( 'The WPGlobus Team', 'wpglobus' ) .
							'</em>' .
							'',
				'style'  => 'info',
				'notice' => false,
				'class'  => '',
			);

		/**
		 * For Google Analytics.
		 */
		$ga_campaign = '?utm_source=wpglobus-admin-clean&utm_medium=link&utm_campaign=talk-to-us';

		$url_wpglobus_site               = WPGlobus_Utils::url_wpglobus_site();
		$url_wpglobus_site_submit_ticket = $url_wpglobus_site . 'support/submit-ticket/' . $ga_campaign;

		$fields_home[] =
			array(
				'id'     => 'wpglobus_clean',
				'type'   => 'wpglobus_info',
				'title'  => esc_html__( 'Deactivating / Uninstalling', 'wpglobus' ),
				'desc'   => '' .
							'<em>' .
							sprintf(
								esc_html(
								/// translators: %?$s: HTML codes for hyperlink. Do not remove.
									esc_html__( 'We would hate to see you go. If something goes wrong, do not uninstall WPGlobus yet. Please %1$stalk to us%2$s and let us help!', 'wpglobus' ) ),
								'<a href="' . $url_wpglobus_site_submit_ticket . '" target="_blank">',
								'</a>'
							) .
							'</em>' .
							'<hr/>' .
							'<i class="dashicons dashicons-flag" style="color:red"></i> <strong>' .
							esc_html( __( 'Please note that if you deactivate WPGlobus, your site will show all the languages together, mixed up. You will need to remove all translations, keeping only one language.', 'wpglobus' ) ) .
							'</strong>' .
							'<hr>' .
							sprintf(
							/// translators: %s: link to the Clean-up Tool
								esc_html__( 'If there are just a few places, you should edit them manually. To automatically remove all translations at once, you can use the %s. WARNING: The clean-up operation is irreversible, so use it only if you need to completely uninstall WPGlobus.', 'wpglobus' ),
								sprintf(
								/// translators: %?$s: HTML codes for hyperlink. Do not remove.
									esc_html__( '%1$sClean-up Tool%2$s', 'wpglobus' ),
									'<a href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_CLEAN . '">',
									'</a>'
								) ) .
							'',
				'style'  => 'normal',
				'notice' => false,
				'class'  => 'normal',
			);

		return array(
			'wpglobus_id' => 'welcome',
			'title'       => esc_html__( 'Welcome!', 'wpglobus' ),
			#'icon'        => 'el-icon-globe',
			'icon'        => 'dashicons dashicons-admin-site',
			'fields'      => $fields_home,
		);

	}

	public function helpdeskSection() {

		return array(
			'wpglobus_id' => 'helpdesk',
			'title'       => esc_html__( 'Help Desk', 'wpglobus' ),
			'tab_href'    => admin_url( 'admin.php?page=' ) . WPGlobus::PAGE_WPGLOBUS_HELPDESK,
			'icon'        => WPGlobus_Admin_HelpDesk::ICON_CLASS,
		);

	}

	public function addonsSection() {

		return array(
			'wpglobus_id' => 'addons',
			'title'       => esc_html__( 'Add-ons', 'wpglobus' ),
			'tab_href'    => WPGlobus_Admin_Page::url_addons_2(),
			'icon'        => 'dashicons dashicons-before dashicons-admin-plugins',
		);

	}

	/**
	 * @todo Move it to...
	 * @see  \WPGlobus_Admin_Recommendations::for_woocommerce
	 * @return array
	 */
	protected function section_recommend_wpg_wc() {

		$name                   = esc_html__( 'WPGlobus for WooCommerce', 'wpglobus' );
		$url                    = WPGlobus_Utils::url_wpglobus_site() . 'product/woocommerce-wpglobus/';
		$content_title_template = esc_html__( 'Recommended add-on: %s', 'wpglobus' );
		$content_title          = sprintf( $content_title_template, $name );

		$content_body = esc_html__( 'To translate WooCommerce product titles and descriptions, categories, tags and attributes, you need to install our premium WooCommerce extension.', 'wpglobus' );
		$content_body .= '<h3>' . esc_html__( 'Click here to download:', 'wpglobus' ) . '</h3>';
		$content_body .= '<a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a>';

		$tab_content[] =
			array(
				'id'     => 'recommend_wpg_wc_content',
				'type'   => 'wpglobus_info',
				'title'  => $content_title,
				'desc'   => $content_body,
				'style'  => 'info',
				'notice' => false,
				'class'  => '',
			);

		return array(
			'wpglobus_id' => 'recommend_wpg_wc',
			'title'       => esc_html__( 'WooCommerce', 'wpglobus' ),
			'icon'        => 'dashicons dashicons-format-status',
			'fields'      => $tab_content,
		);
	}

	/**
	 * SECTION: Languages.
	 */
	public function languagesSection() {

		$wpglobus_option = get_option( $this->config->option );

		/** @var array $enabled_languages contains all enabled languages */
		$enabled_languages = array();

		/** @var array $defaults_for_enabled_languages Need for the sortable field setup */
		$defaults_for_enabled_languages = array();

		/** @var array $more_languages */
		$more_languages = array();

		foreach ( $this->config->enabled_languages as $code ) {
			$lang_in_en = '';
			if ( isset( $this->config->en_language_name[ $code ] ) && ! empty( $this->config->en_language_name[ $code ] ) ) {
				$lang_in_en = ' (' . $this->config->en_language_name[ $code ] . ')';
			}

			$enabled_languages[ $code ]              = $this->config->language_name[ $code ] . $lang_in_en;
			$defaults_for_enabled_languages[ $code ] = true;
		}

		// Moved to sanitized_posted_data()
		/** Add language from 'more_language' option to array $enabled_languages. */
//		if ( isset( $wpglobus_option['more_languages'] ) && ! empty( $wpglobus_option['more_languages'] ) ) {
//
//			$lang       = $wpglobus_option['more_languages'];
//			$lang_in_en = '';
//			if ( isset( $this->config->en_language_name[ $lang ] ) && ! empty( $this->config->en_language_name[ $lang ] ) ) {
//				$lang_in_en = ' (' . $this->config->en_language_name[ $lang ] . ')';
//			}
//
//			if ( ! empty( $this->config->language_name[ $lang ] ) ) {
//				$enabled_languages[ $lang ] = $this->config->language_name[ $lang ] . $lang_in_en;
//			}
//
//			if (
//				! empty( $wpglobus_option['more_languages'] )
//				&& isset( $this->config->language_name[ $wpglobus_option['more_languages'] ] )
//			) {
//				$wpglobus_option['enabled_languages'][ $wpglobus_option['more_languages'] ] =
//					$this->config->language_name[ $wpglobus_option['more_languages'] ];
//			}
//
//			update_option( $this->config->option, $wpglobus_option );
//
//		}

		/** Generate array $more_languages */
		foreach ( $this->config->flag as $code => $file ) {
			if ( ! array_key_exists( $code, $enabled_languages ) ) {
				$lang_in_en = '';
				if ( isset( $this->config->en_language_name[ $code ] ) && ! empty( $this->config->en_language_name[ $code ] ) ) {
					$lang_in_en = ' (' . $this->config->en_language_name[ $code ] . ')';
				}
				$more_languages[ $code ] = $this->config->language_name[ $code ] . $lang_in_en;
			}
		}


		/**
		 * for miniGLOBUS.
		 */
		if ( empty( $this->menus ) ) {
			$navigation_menu_placeholder = esc_html__( 'No navigation menu', 'wpglobus' );
		} else {
			$navigation_menu_placeholder = esc_html__( 'Select navigation menu', 'wpglobus' );
		}

		$desc_languages_intro = implode( '', array(
			'<ul style="list-style: disc inside;">',
			'<li>' . sprintf(
			/// translators: %3$s placeholder for the icon (actual picture)
				esc_html__( 'Place the %1$smain language%2$s of your site at the top of the list by dragging the %3$s icons.', 'wpglobus' ), '<strong>', '</strong>', '<i class="dashicons dashicons-move"></i>' ) . '</li>',
			'<li>' . sprintf( esc_html__( '%1$sUncheck%2$s the languages you do not plan to use.', 'wpglobus' ), '<strong>', '</strong>' ) . '</li>',
			'<li>' . sprintf( esc_html__( '%1$sAdd%2$s more languages using the section below.', 'wpglobus' ), '<strong>', '</strong>' ) . '</li>',
			'<li>' . esc_html__( 'When done, click the [Save Changes] button.', 'wpglobus' ) . '</li>',
			'</ul>',
		) );

		$desc_more_languages =
			esc_html__( 'Choose a language you would like to enable.', 'wpglobus' )
			. '<br />'
			/// translators: %s - placeholder for the "Save Changes" button text.
			. sprintf( esc_html__( 'Press the %s button to confirm.', 'wpglobus' ),
				/// DO NOT TRANSLATE
				'<code>[' . esc_html__( 'Save Changes' ) . ']</code>' )
			. '<br /><br />'
			. sprintf(
			/// translators: %1$s and %2$s - placeholders to insert HTML link around 'here'
				esc_html__( 'or Add new Language %1$s here %2$s', 'wpglobus' ),
				'<a href="' . esc_url( WPGlobus_Language_Edit_Request::url_language_add() ) . '">', '</a>'
			);

		if ( empty( $wpglobus_option['enabled_languages'] ) ) {
			$_value_for_enabled_languages = $defaults_for_enabled_languages;
		} else {
			$_value_for_enabled_languages = $wpglobus_option['enabled_languages'];
		}

		$section = array(
			'wpglobus_id' => 'languages',
			'title'       => esc_html__( 'Languages', 'wpglobus' ),
			#'icon'        => 'el-icon-wrench-alt',
			'icon'        => 'dashicons dashicons-admin-tools',
			'fields'      => array(
				array(
					'id'     => 'languages_intro',
					'type'   => 'wpglobus_info',
					'title'  => esc_html__( 'Instructions:', 'wpglobus' ),
					'html'   => $desc_languages_intro,
					'style'  => 'info',
					'notice' => false,
				),
				array(
					'id'          => 'enabled_languages',
					'type'        => 'wpglobus_sortable',
					'title'       => esc_html__( 'Enabled Languages', 'wpglobus' ),
					'subtitle'    => esc_html__( 'These languages are currently enabled on your site.', 'wpglobus' ),
					'compiler'    => 'false',
					'options'     => $enabled_languages,
					'default'     => $defaults_for_enabled_languages,
					'mode'        => 'checkbox',
					'name'        => 'wpglobus_option[enabled_languages]',
					'name_suffix' => '',
					'value'       => $_value_for_enabled_languages,
					'class'       => 'wpglobus-enabled_languages',
				),
				array(
					'id'          => 'more_languages',
					'type'        => 'wpglobus_select',
					'title'       => esc_html__( 'Add Languages', 'wpglobus' ),
					'compiler'    => 'false',
					'mode'        => false,
					'desc'        => $desc_more_languages,
					'placeholder' => esc_html__( 'Select a language', 'wpglobus' ),
					'options'     => $more_languages,
					'name'        => 'wpglobus_option[more_languages]',
					'name_suffix' => '',
					'class'       => '',
				),
				array(
					'id'          => 'show_flag_name',
					'type'        => 'wpglobus_select',
					'title'       => esc_html__( 'Language Selector Mode', 'wpglobus' ),
					'compiler'    => 'false',
					'mode'        => false,
					'desc'        => esc_html__( 'Choose the way language name and country flag are shown in the drop-down menu', 'wpglobus' ),
					'select2'     => array(
						'allowClear'              => false,
						'minimumResultsForSearch' => - 1,
					),
					'options'     => array(
						'code'      => esc_html__( 'Two-letter Code with flag (en, ru, it, etc.)', 'wpglobus' ),
						'full_name' => esc_html__( 'Full Name (English, Russian, Italian, etc.)', 'wpglobus' ),
						'name'      => esc_html__( 'Full Name with flag (English, Russian, Italian, etc.)', 'wpglobus' ),
						'empty'     => esc_html__( 'Flags only', 'wpglobus' ),
					),
					'default'     => 'code',
					'name'        => 'wpglobus_option[show_flag_name]',
					'name_suffix' => '',
					'class'       => '',
				),
				array(
					'id'          => 'use_nav_menu',
					# $WPGlobus_Config->nav_menu
					'type'        => 'wpglobus_select',
					'title'       => esc_html__( 'Language Selector Menu', 'wpglobus' ),
					'compiler'    => 'false',
					'mode'        => false,
					'desc'        => esc_html__( 'Choose the navigation menu where the language selector will be shown', 'wpglobus' ),
					'select2'     => array(
						'allowClear'              => true,
						'minimumResultsForSearch' => - 1,
					),
					'options'     => $this->menus,
					'placeholder' => $navigation_menu_placeholder,
					'name'        => 'wpglobus_option[use_nav_menu]',
					'name_suffix' => '',
					'class'       => '',
				),
				array(
					'id'       => 'selector_wp_list_pages',
					'type'     => 'wpglobus_checkbox',
					'title'    => esc_html__( '"All Pages" menus Language selector', 'wpglobus' ),
					'subtitle' => esc_html__( '(Found in some themes)', 'wpglobus' ),
					'desc'     => esc_html__( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
					'compiler' => 'false',
					'default'  => 1,
					'options'  => array(
						'show_selector' => esc_html__( 'Enable', 'wpglobus' ),
					),
				),
				array(
					'id'       => 'css_editor',
					'type'     => 'wpglobus_ace_editor',
					'title'    => esc_html__( 'Custom CSS', 'wpglobus' ),
					'mode'     => 'css',
					'theme'    => 'chrome',
					'compiler' => 'false',
					'desc'     => esc_html__( 'Here you can enter the CSS rules to adjust the language selector menu for your theme. Look at the examples in the `style-samples.css` file.', 'wpglobus' ),
					'subtitle' => esc_html__( '(Optional)', 'wpglobus' ),
					'default'  => '',
					'rows'     => 15,
				),
				array(
					'id'       => 'js_editor',
					'type'     => 'wpglobus_ace_editor',
					'title'    => esc_html__( 'Custom JS Code', 'wpglobus' ),
					'mode'     => 'javascript',
					'theme'    => 'chrome',
					'compiler' => 'false',
					//'desc'     => esc_html__( '', 'wpglobus' ),
					'subtitle' => esc_html__( '(Paste your JS code here.)', 'wpglobus' ),
					'default'  => '',
					'rows'     => 15,
				),
			),
		);

		return $section;

	}

	/**
	 *    SECTION: Language table.
	 */
	public function languageTableSection() {
		$section = array(
			'wpglobus_id' => 'language_table',
			'title'       => esc_html__( 'Languages table', 'wpglobus' ),
			#'icon'        => 'el-icon-th-list',
			'icon'        => 'dashicons dashicons-list-view',
			'fields'      => array(
				array(
					'id'       => 'description',
					'type'     => 'wpglobus_info',
					'title'    => esc_html__( 'Use this table to add, edit or delete languages.', 'wpglobus' ),
					'subtitle' => esc_html__( 'NOTE: you cannot remove the main language.', 'wpglobus' ),
					'style'    => 'info',
					'notice'   => false,
				),
				array(
					'id'   => 'lang_new',
					'type' => 'table',
				),
			),
		);

		return $section;
	}

	/**
	 * Tell where to find our custom fields.
	 *
	 * @since 1.2.2
	 *
	 * @param string $file  Path of the field class where Redux is looking for it
	 * @param array  $field Field parameters
	 *
	 * @return string Path of the field class where we want Redux to find it
	 */
	public function filter__add_custom_fields(
		/** @noinspection PhpUnusedParameterInspection */
		$file, $field
	) {

		$file = WPGlobus::$PLUGIN_DIR_PATH . "includes/options/fields2/{$field['type']}/field_{$field['type']}.php";

		if ( ! file_exists( $file ) ) {
			return false;
		}

		return $file;
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public function on__admin_scripts() {

		if ( $this->current_page != $this->page_slug ) {
			return;
		}

		wp_register_script(
			'wpglobus-options',
			WPGlobus::$PLUGIN_DIR_URL . 'includes/options/assets/js/wpglobus-options' . WPGlobus::SCRIPT_SUFFIX() . '.js',
			#array( 'jquery', 'jquery-ui-dialog', 'jquery-ui-tabs', 'jquery-ui-tooltip' ),
			array( 'jquery', 'jquery-ui-sortable' ),
			WPGLOBUS_VERSION,
			true
		);
		wp_enqueue_script( 'wpglobus-options' );
		wp_localize_script(
			'wpglobus-options',
			'WPGlobusOptions',
			array(
				'version'  => WPGLOBUS_VERSION,
				'tab'      => $this->tab,
				'sections' => $this->sections,
			)
		);

		/**
		 * Enable jQuery-UI touch support.
		 *
		 * @link  http://touchpunch.furf.com/
		 * @link  https://github.com/furf/jquery-ui-touch-punch/
		 * @since 1.9.10
		 */
		wp_enqueue_script(
			'wpglobus-options-touch',
			WPGlobus::$PLUGIN_DIR_URL . 'includes/options/assets/js/jquery.ui.touch-punch' . WPGlobus::SCRIPT_SUFFIX() . '.js',
			array( 'wpglobus-options' ),
			WPGLOBUS_VERSION,
			true
		);
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @return void
	 */
	public function on__admin_styles() {

		if ( $this->current_page != $this->page_slug ) {
			return;
		}

		wp_register_style(
			'wpglobus-options',
			WPGlobus::$PLUGIN_DIR_URL . 'includes/options/assets/css/wpglobus-options' . WPGlobus::SCRIPT_SUFFIX() . '.css',
			array( 'wpglobus-admin' ),
			WPGLOBUS_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpglobus-options' );

	}

	protected function handle_submit() {
		$option_name = WPGlobus::Config()->option;
		if ( empty( $_POST[ $option_name ] ) || ! is_array( $_POST[ $option_name ] ) ) {
			// No data or invalid data submitted.
			return;
		}

		// WP anti-hacks.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized user' );
		}
		check_admin_referer( self::NONCE_ACTION );

		// Sanitize, and if OK then save the options and reload the page.
		$posted_data = $this->sanitized_posted_data( $_POST[ $option_name ] );
		if ( $posted_data ) {
			update_option( $option_name, $posted_data );
			wp_safe_redirect( add_query_arg( array( 'page' => $this->page_slug ), admin_url( 'admin.php' ) ) );
		}
	}

	/**
	 * Sanitize $_POST before saving it to the options table.
	 *
	 * @param array $posted_data The submitted data.
	 *
	 * @return array The sanitized data.
	 */
	protected function sanitized_posted_data( $posted_data ) {

		// Standard WP anti-hack. Should return a clean array.
		$data = wp_unslash( $posted_data );
		if ( ! is_array( $data ) ) {
			// Something is wrong. This should never happen. Do not save.
			wp_die( 'WPGlobus: options data sanitization error' );
		}

		if ( empty( $data['enabled_languages'] ) || ! is_array( $data['enabled_languages'] ) ) {
			// Corrupted data / hack. This should never happen. Do not save this.
			wp_die( 'WPGlobus: options data without enabled_languages' );
		}

		// All enabled languages must be in the form [code] => true.
		// Remove the unchecked languages (empty values).
		$data['enabled_languages'] = array_filter( $data['enabled_languages'] );
		// Fill the rest with true.
		$data['enabled_languages'] = array_fill_keys( array_keys( $data['enabled_languages'] ), true );

		// "More languages" is appended to the "Enabled Languages".
		if ( ! empty( $data['more_languages'] ) && is_string( $data['more_languages'] ) ) {
			$data['enabled_languages'][ $data['more_languages'] ] = true;
		}
		unset( $data['more_languages'] );

		return $data;
	}

} // class

# --- EOF
