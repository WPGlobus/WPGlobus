<?php
/**
 * File: class-wpglobus-options.php
 *
 * @package     WPGlobus\Admin\Options
 * @author      WPGlobus
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

	private $current_page;

	const DEFAULT_TAB = 'languages';

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->page_slug = WPGlobus::OPTIONS_PAGE_SLUG;

		$this->current_page = WPGlobus_Utils::safe_get( 'page' );

		$_tab = WPGlobus_Utils::safe_get( 'tab' );
		if ( empty( $_tab ) || ! is_string( $_tab ) ) {
			$_tab = self::DEFAULT_TAB;
		}
		$this->tab = sanitize_title_with_dashes( $_tab );

		add_action( 'init', array( $this, 'initSettings' ), PHP_INT_MAX - 1 ); // Before handle_submit().
		// Handle the main options form submit.
		// If data posted, the options will be updated, and page reloaded (so no continue to the next line).
		add_action( 'init', array( $this, 'handle_submit' ), PHP_INT_MAX );

		add_action( 'admin_menu', array( $this, 'on__admin_menu' ) );

		add_action( 'admin_print_scripts', array( $this, 'on__admin_scripts' ) );

		add_action( 'admin_print_styles', array( $this, 'on__admin_styles' ) );
	}

	public function initSettings() {

		$this->config = WPGlobus::Config();

		foreach (
			array(
				'wpglobus_info',
				'wpglobus_sortable',
				'wpglobus_select',
				'wpglobus_dropdown',
				'wpglobus_multicheck',
				'wpglobus_ace_editor',
				'wpglobus_checkbox',
				'table',
			) as $field_type
		) {
			add_filter( "wpglobus/options/field/{$field_type}", array(
					$this,
					'filter__add_custom_fields',
				)
				, 0, 2 );
		}

		// Set the default arguments.
		$this->setArguments();

		// Create the sections and fields.
		// This is delayed so we have, for example, all CPTs registered for the 'post_types' section.
		add_action( 'wp_loaded', array( $this, 'setSections' ) );
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
			<h1>WPGlobus <?php echo esc_html( WPGLOBUS_VERSION ); ?></h1>
			<div class="wpglobus-options-container">
				<form id="form-wpglobus-options" method="post">
					<div id="wpglobus-options-intro-text"><?php echo wp_kses_post( $this->args['intro_text'] ); ?></div>
					<div class="wpglobus-options-wrap">
						<div class="wpglobus-options-sidebar wpglobus-options-wrap__item">
							<ul class="wpglobus-options-menu">
								<?php foreach ( $this->sections as $section_tab => $section ): ?>
									<?php $section = $this->sanitize_section( $section ); ?>
									<li id="wpglobus-tab-link-<?php echo esc_attr( $section_tab ); ?>"
											class="<?php echo esc_attr( $section['li_class'] ); ?>"
											data-tab="<?php echo esc_attr( $section_tab ); ?>">
										<a href="<?php echo esc_url( $section['tab_href'] ); ?>" <?php echo $section['onclick']; // XSS ok. ?>
												data-tab="<?php echo esc_attr( $section_tab ); ?>">
											<i class="<?php echo esc_attr( $section['icon'] ); ?>"></i>
											<span class="group_title"><?php echo esc_html( $section['title'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div><!-- sidebar -->
						<div class="wpglobus-options-main wpglobus-options-wrap__item">
							<div class="wpglobus-options-info">
								<?php foreach ( $this->sections as $section_tab => $section ) {
									?>
									<div id="section-tab-<?php echo esc_attr( $section_tab ); ?>"
											class="wpglobus-options-tab"
											data-tab="<?php echo esc_attr( $section_tab ); ?>">
										<h2><?php echo esc_html( $section['title'] ); ?></h2>
										<?php
										if ( ! empty( $section['fields'] ) ) {
											foreach ( $section['fields'] as $field ) {
												$field = $this->sanitize_field( $field );
												if ( ! $field ) {
													// Invalid field.
													continue;
												}

												$field_type = $field['type'];
												$file       = apply_filters( "wpglobus/options/field/{$field_type}", '', $field );
												if ( $file && file_exists( $file ) ) :
													// Intentionally "require" and not "require_once".
													/** @noinspection PhpIncludeInspection */
													require $file;
												endif; ?>
											<?php }
											/** end foreach **/
										}
										?>
									</div><!-- .wpglobus-options-tab -->
								<?php } // endforeach; ?>
								<?php
								wp_nonce_field( self::NONCE_ACTION );
								?>
								<input type="hidden" name="wpglobus_options_current_tab"
										id="wpglobus_options_current_tab"
										value="<?php echo esc_attr( $this->tab ); ?>"/>
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
	 * All the possible arguments.
	 **/
	public function setArguments() {

		$this->args = array(
			// TYPICAL -> Change these values as you need/desire
			'opt_name'        => $this->config->option,
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
			 * Need to disable AJAX save,
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

		$this->sections['welcome']        = $this->welcomeSection();
		$this->sections['languages']      = $this->languagesSection();
		$this->sections['language-table'] = $this->languageTableSection();
		$this->sections['post-types']     = $this->section_post_types();
		$this->sections['custom-code']    = $this->section_custom_code();

		/**
		 * Filter the array of sections. Here add-ons can add their menus.
		 *
		 * @param array $sections Array of sections.
		 */
		$this->sections = apply_filters( 'wpglobus_option_sections', $this->sections );

		/**
		 * The below sections should be at the bottom.
		 */


		if ( class_exists( 'WooCommerce' ) ) {
			if ( ! defined( 'WOOCOMMERCE_WPGLOBUS_VERSION' ) ) {
				$this->sections['recommend-wpg-wc'] = $this->section_recommend_wpg_wc();
			}
			if ( ! defined( 'WPGLOBUS_MC_VERSION' ) ) {
				$this->sections['recommend-wpg-mc'] = $this->section_recommend_wpg_mc();
			}
		}


		/**
		 * Links to Admin Central
		 */

		if ( class_exists( 'WPGlobus_Admin_Central', false ) ) {
			if ( class_exists( 'WPGlobusMobileMenu', false ) ) {
				$this->sections['mobile-menu'] = $this->section_mobile_menu();
			}
			if ( class_exists( 'WPGlobus_Language_Widgets', false ) ) {
				$this->sections['language-widgets'] = $this->section_language_widgets();
			}
			if ( class_exists( 'WPGlobus_Featured_Images', false ) ) {
				$this->sections['featured-images'] = $this->section_featured_images();
			}
		}

		// Checking class_exists because those classes are not loaded in DOING_AJAX, but the options panel might use AJAX.

		if ( class_exists( 'WPGlobus_Admin_Page', false ) ) {
			$this->sections['addons'] = $this->addonsSection();
		}

		if ( class_exists( 'WPGlobus_Admin_HelpDesk', false ) ) {
			$this->sections['helpdesk'] = $this->helpdeskSection();
		}

		$this->sections['uninstall'] = $this->section_uninstall();

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
				'id'    => 'welcome_intro',
				'type'  => 'wpglobus_info',
				'title' => __( 'Thank you for installing WPGlobus!', 'wpglobus' ),
				'desc'  => '' .
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
				'class' => 'info',
			);

		return array(
			'wpglobus_id' => 'welcome',
			'title'       => __( 'Welcome!', 'wpglobus' ),
			'icon'        => 'dashicons dashicons-admin-site',
			'fields'      => $fields_home,
		);

	}

	protected function section_uninstall() {

		$fields_home = array();

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
			'wpglobus_id' => 'uninstall',
			'title'       => __( 'Uninstall', 'wpglobus' ),
			'icon'        => 'dashicons dashicons-no',
			'fields'      => $fields_home,
		);

	}

	public function helpdeskSection() {
		return array(
			'wpglobus_id'  => 'helpdesk',
			'title'        => __( 'Help Desk', 'wpglobus' ),
			'tab_href'     => WPGlobus_Admin_Page::url_helpdesk(),
			'icon'         => WPGlobus_Admin_HelpDesk::ICON_CLASS,
			'externalLink' => true,
		);
	}

	public function addonsSection() {
		return array(
			'wpglobus_id'  => 'addons',
			'title'        => __( 'All add-ons', 'wpglobus' ),
			'tab_href'     => WPGlobus_Admin_Page::url_addons(),
			'icon'         => 'dashicons dashicons-admin-plugins',
			'externalLink' => true,
		);
	}

	protected function section_mobile_menu() {
		return array(
			'wpglobus_id'  => 'mobile_menu',
			'title'        => __( 'Mobile Menu', 'wpglobus' ),
			'tab_href'     => WPGlobus_Admin_Page::url_admin_central( 'tab-mobile-menu' ),
			'icon'         => 'dashicons dashicons-smartphone',
			'externalLink' => true,
		);
	}

	protected function section_language_widgets() {
		return array(
			'wpglobus_id'  => 'mobile_menu',
			'title'        => __( 'Language Widgets', 'wpglobus' ),
			'tab_href'     => WPGlobus_Admin_Page::url_admin_central( 'tab-language-widgets' ),
			'icon'         => 'dashicons dashicons-archive',
			'externalLink' => true,
		);
	}

	protected function section_featured_images() {
		return array(
			'wpglobus_id'  => 'mobile_menu',
			'title'        => __( 'Featured Images', 'wpglobus' ),
			'tab_href'     => WPGlobus_Admin_Page::url_admin_central( 'tab-featured-images' ),
			'icon'         => 'dashicons dashicons-images-alt',
			'externalLink' => true,
		);
	}

	/**
	 * @todo Move it to...
	 * @see  \WPGlobus_Admin_Recommendations::for_woocommerce
	 * @return array
	 */
	protected function section_recommend_wpg_wc() {

		$id   = 'recommend_wpg_wc';
		$name = __( 'WooCommerce?..', 'wpglobus' );
		/**
		 * For Google Analytics.
		 */
		$ga_campaign = '?utm_source=wpglobus-admin&utm_medium=link&utm_campaign=' . $id;

		$url = WPGlobus_Utils::url_wpglobus_site() . 'product/woocommerce-wpglobus/' . $ga_campaign;

		ob_start();

		?>
		<p>
			<?php esc_html_e(
				'Thanks for installing WPGlobus! Now you have a multilingual website and can translate your blog posts and pages to many languages.', 'wpglobus' ); ?>
		</p>
		<p><strong>
				<?php esc_html_e(
					'The next step is to translate your WooCommerce-based store!', 'wpglobus' ); ?>
			</strong></p>
		<p class="wp-ui-notification" style="padding: 1em">
			<?php esc_html_e( 'With the WPGlobus for WooCommerce premium add-on, you will be able to translate product titles and descriptions, categories, tags and attributes.', 'wpglobus' ); ?>
		</p>
		<a class="button button-primary" href="<?php echo esc_url( $url ); ?>">
			<?php esc_html_e( 'Click here to download', 'wpglobus' ); ?>
		</a>
		<?php

		$content_body = ob_get_clean();

		$tab_content   = array();
		$tab_content[] =
			array(
				'id'   => $id . '_content',
				'type' => 'wpglobus_info',
				'desc' => $content_body,
			);

		return array(
			'wpglobus_id' => $id,
			'title'       => $name,
			'icon'        => 'dashicons dashicons-cart',
			'fields'      => $tab_content,
		);
	}

	/**
	 * @todo Move it to...
	 * @see  \WPGlobus_Admin_Recommendations::for_woocommerce
	 * @return array
	 */
	protected function section_recommend_wpg_mc() {

		$id   = 'recommend_wpg_mc';
		$name = __( 'Multi-currency?..', 'wpglobus' );
		/**
		 * For Google Analytics.
		 */
		$ga_campaign = '?utm_source=wpglobus-admin&utm_medium=link&utm_campaign=' . $id;
		$url         = WPGlobus_Utils::url_wpglobus_site() . 'product/wpglobus-multi-currency/' . $ga_campaign;

		ob_start();

		?>
		<p><strong>
				<?php printf( esc_html__(
					'Your WooCommerce-powered store is set to show prices and accept payments in %s.', 'wpglobus' ), get_woocommerce_currency() ); ?>
			</strong></p>
		<p>
			<?php esc_html_e( 'With WPGlobus, you can add multiple currencies to your store and charge UK customers in Pounds, US customers in Dollars, Spanish clients in Euros, etc. Accepting multiple currencies will strengthen your competitive edge and positioning for global growth!', 'wpglobus' ); ?>

		</p>
		<p class="wp-ui-notification" style="padding: 1em">
			<?php esc_html_e( 'The WPGlobus Multi-Currency premium add-on provides switching currencies and re-calculating prices on-the-fly.', 'wpglobus' ); ?>
		</p>
		<a class="button button-primary" href="<?php echo esc_url( $url ); ?>">
			<?php esc_html_e( 'Click here to download', 'wpglobus' ); ?>
		</a>
		<?php

		$content_body = ob_get_clean();

		$tab_content   = array();
		$tab_content[] =
			array(
				'id'   => $id . '_content',
				'type' => 'wpglobus_info',
				'desc' => $content_body,
			);


		return array(
			'wpglobus_id' => $id,
			'title'       => $name,
			'icon'        => 'dashicons dashicons-cart',
			'fields'      => $tab_content,
		);
	}

	/**
	 * SECTION: Languages.
	 */
	public function languagesSection() {

		$wpglobus_option = get_option( $this->args['opt_name'] );

		/** @var array $enabled_languages contains all enabled languages */
		$enabled_languages = array();

		/** @var array $defaults_for_enabled_languages Need for the sortable field setup */
		$defaults_for_enabled_languages = array();

		/** @var array $more_languages */
		$more_languages = array( '' => __( 'Select a language', 'wpglobus' ) );

		foreach ( $this->config->enabled_languages as $code ) {
			$lang_in_en = '';
			if ( isset( $this->config->en_language_name[ $code ] ) && ! empty( $this->config->en_language_name[ $code ] ) ) {
				$lang_in_en = ' (' . $this->config->en_language_name[ $code ] . ')';
			}

			$enabled_languages[ $code ]              = $this->config->language_name[ $code ] . $lang_in_en;
			$defaults_for_enabled_languages[ $code ] = true;
		}

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

		$nav_menus = WPGlobus::_get_nav_menus();

		$menus['all'] = __( 'All menus', 'wpglobus' );
		foreach ( $nav_menus as $menu ) {
			$menus[ $menu->slug ] = $menu->name;
		}

		$section = array(
			'wpglobus_id' => 'languages',
			'title'       => __( 'Languages', 'wpglobus' ),
			'icon'        => 'dashicons dashicons-translation',
			'fields'      => array(
				array(
					'id'    => 'languages_intro',
					'type'  => 'wpglobus_info',
					'title' => __( 'Instructions:', 'wpglobus' ),
					'html'  => $desc_languages_intro,
					'class' => 'normal',
				),
				array(
					'id'          => 'enabled_languages',
					'type'        => 'wpglobus_sortable',
					'title'       => __( 'Enabled Languages', 'wpglobus' ),
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
					'id'      => 'more_languages',
					'type'    => 'wpglobus_dropdown',
					'title'   => __( 'Add Languages', 'wpglobus' ),
					'desc'    => $desc_more_languages,
					'options' => $more_languages,
					'default' => '', // Do not remove.
				),
				array(
					'id'      => 'show_flag_name',
					'type'    => 'wpglobus_dropdown',
					'title'   => __( 'Language Selector Mode', 'wpglobus' ),
					'desc'    => __( 'Choose the way language name and country flag are shown in the drop-down menu', 'wpglobus' ),
					'options' => array(
						'code'      => __( 'Two-letter Code with flag (en, ru, it, etc.)', 'wpglobus' ),
						'full_name' => __( 'Full Name (English, Russian, Italian, etc.)', 'wpglobus' ),
						'name'      => __( 'Full Name with flag (English, Russian, Italian, etc.)', 'wpglobus' ),
						'empty'     => __( 'Flags only', 'wpglobus' ),
					),
					'default' => ( empty( $wpglobus_option['show_flag_name'] )
						? 'code'
						: $wpglobus_option['show_flag_name'] ),
					'name'    => 'wpglobus_option[show_flag_name]',
				),
				# $WPGlobus_Config->nav_menu
				array(
					'id'      => 'use_nav_menu',
					'type'    => 'wpglobus_dropdown',
					'title'   => __( 'Language Selector Menu', 'wpglobus' ),
					'desc'    => __( 'Choose the navigation menu where the language selector will be shown', 'wpglobus' ),
					'options' => $menus,
					'default' => ( empty( $wpglobus_option['use_nav_menu'] )
						? 'all'
						: $wpglobus_option['use_nav_menu'] ),
					'name'    => 'wpglobus_option[use_nav_menu]',
				),
				array(
					'id'       => 'selector_wp_list_pages',
					'type'     => 'wpglobus_checkbox',
					'title'    => esc_html__( '"All Pages" menus Language selector', 'wpglobus' ),
					'subtitle' => esc_html__( '(Found in some themes)', 'wpglobus' ),
					'desc'     => esc_html__( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
					'label'    => __( 'Enable', 'wpglobus' ),
				),
			),
		);

		return $section;

	}

	/**
	 * SECTION: Language table.
	 */
	public function languageTableSection() {
		$section = array(
			'wpglobus_id' => 'language_table',
			'title'       => esc_html__( 'Languages table', 'wpglobus' ),
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
					'id'   => 'languagesTable',
					'type' => 'table',
				),
			),
		);

		return $section;
	}

	/**
	 * SECTION: Post types.
	 *
	 * @return array
	 */
	protected function section_post_types() {

		/** @var WP_Post_Type[] $post_types */
		$post_types = get_post_types( array(), 'objects' );

		$disabled_entities = apply_filters( 'wpglobus_disabled_entities', $this->config->disabled_entities );

		$options = array();

		foreach ( $post_types as $post_type ) {

			/**
			 * @todo "SECTION: Post types" in includes\admin\class-wpglobus-customize-options.php to adjust post type list.
			 */
			if ( in_array( $post_type->name, WPGlobus_Post_Types::hidden_types(), true ) ) {
				continue;
			}

			$label   = $post_type->label . ' (' . $post_type->name . ')';
			$checked = ! in_array( $post_type->name, $disabled_entities, true );

			$options[ $post_type->name ] = array(
				'label'   => $label,
				'checked' => $checked,
			);
		}

		$fields = array();

		$fields[] =
			array(
				'id'     => 'wpglobus_post_types_intro',
				'type'   => 'wpglobus_info',
				'title'  => __( 'Uncheck to disable WPGlobus', 'wpglobus' ),
				'style'  => 'info',
				'notice' => false,
				'class'  => 'info',
			);

		$fields[] =
			array(
				'id'      => 'wpglobus_post_types_choose',
				'type'    => 'wpglobus_multicheck',
				'options' => $options,
				'name'    => 'wpglobus_option[post_type]',
				'desc'    => __( 'Please note that there are post types which status is managed by other plugins and cannot be changed here.', 'wpglobus' ),
			);

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$fields[] =
				array(
					'id'     => 'wpglobus_post_types_debug',
					'type'   => 'wpglobus_info',
					'title'  => 'Debug',
					'desc'   => '<xmp>'
								. 'WPGlobus::Config()->disabled_entities '
								. print_r( $this->config->disabled_entities, true )
								. 'WPGlobus_Post_Types::get_hidden_types() '
								. print_r( WPGlobus_Post_Types::hidden_types(), true )
								. '</xmp>',
					// TODO
					'style'  => 'normal',
					'notice' => false,
					'class'  => 'normal',
				);
		}

		return array(
			'wpglobus_id' => 'wpglobus_post_types',
			'title'       => __( 'Post Types', 'wpglobus' ),
			'icon'        => 'dashicons dashicons-admin-post',
			'fields'      => $fields,
		);

	}

	/**
	 * Section "Custom Code".
	 *
	 * @return array
	 */
	protected function section_custom_code() {

		$wpglobus_option = get_option( $this->args['opt_name'] );

		$fields = array();

		$fields[] =
			array(
				'id'     => 'wpglobus_custom_code_intro',
				'type'   => 'wpglobus_info',
				'title'  => __( 'Here you can enter the CSS rules and JavaScript code, which will be applied to all front pages of your website.', 'wpglobus' ),
				'style'  => 'normal',
				'notice' => false,
				'class'  => 'normal',
			);

		$fields[] =
			array(
				'id'       => 'wpglobus_custom_code_css',
				'type'     => 'wpglobus_ace_editor',
				'title'    => __( 'Custom CSS', 'wpglobus' ),
				'mode'     => 'css',
				'name'     => 'wpglobus_option[css_editor]',
				'value'    => $wpglobus_option['css_editor'],
				'subtitle' => '',
				'desc'     => '',
			);

		$fields[] =
			array(
				'id'       => 'wpglobus_custom_code_js',
				'type'     => 'wpglobus_ace_editor',
				'title'    => __( 'Custom JS Code', 'wpglobus' ),
				'mode'     => 'javascript',
				'name'     => 'wpglobus_option[js_editor]',
				'value'    => $wpglobus_option['js_editor'],
				'subtitle' => '',
				'desc'     => '',
			);

		return array(
			'wpglobus_id' => 'wpglobus_custom_code',
			'title'       => __( 'Custom Code', 'wpglobus' ),
			'icon'        => 'dashicons dashicons-edit',
			'fields'      => $fields,
		);
	}

	/**
	 * Tell where to find our custom fields.
	 *
	 * @since 1.2.2
	 *
	 * @param string $file  Path of the field class
	 * @param array  $field Field parameters
	 *
	 * @return string Path of the field class where we want to find it
	 */
	public function filter__add_custom_fields(
		/** @noinspection PhpUnusedParameterInspection */
		$file, $field
	) {

		$file = WPGlobus::$PLUGIN_DIR_PATH . "includes/options/fields/{$field['type']}/field_{$field['type']}.php";

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
				'newUrl'   => add_query_arg(
					array(
						'page' => $this->page_slug,
						'tab'  => '{*}',
					), admin_url()
				),
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

	public function handle_submit() {
		$option_name = $this->config->option;
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
		$posted_data = $this->sanitize_posted_data( $_POST[ $option_name ] );
		if ( $posted_data ) {
			update_option( $option_name, $posted_data );


			// Need to get back to the current tab after reloading.
			$tab = '';
			if ( ! empty( $_POST['wpglobus_options_current_tab'] ) ) {
				$tab = sanitize_text_field( $_POST['wpglobus_options_current_tab'] );
			}

			wp_safe_redirect( add_query_arg( array(
				'page' => $this->page_slug,
				'tab'  => $tab,
			), admin_url( 'admin.php' ) ) );
		}
	}

	/**
	 * Sanitize $_POST before saving it to the options table.
	 *
	 * @param array $posted_data The submitted data.
	 *
	 * @return array The sanitized data.
	 */
	protected function sanitize_posted_data( $posted_data ) {

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

		// Section `post_types` requires special processing to capture unchecked elements.
		if ( ! empty( $data['post_type'] ) && is_array( $data['post_type'] ) ) {
			// Extract "control" fields from the posted data.
			$control = $data['post_type']['control'];
			unset( $data['post_type']['control'] );

			// Sanitize control: fill with '0'.
			$control = array_fill_keys( array_keys( $control ), '0' );

			// Sanitize the posted checkboxes: fill with '1'.
			$data['post_type'] = array_fill_keys( array_keys( $data['post_type'] ), '1' );

			// We need to know only the disabled elements.
			// The control is the list of all post types, filled with zeroes, thus all disabled.
			// The "diff" removes from the control those that were posted as "enabled.
			// The result of "diff" is THE disabled post types.
			$data['post_type'] = array_diff_key( $control, $data['post_type'] );
		} else {
			// Invalid data posted (not an array)..fix.
			$data['post_type'] = array();
		}

		// Checkbox: if passed, make it `true`. No garbage.
		if ( ! empty( $data['selector_wp_list_pages'] ) ) {
			$data['selector_wp_list_pages'] = true;
		}

		return $data;
	}

	/**
	 * Check the field parameters, fill in defaults if necessary.
	 *
	 * @param array $field The field.
	 *
	 * @return array|false The sanitized field or false if the field is invalid.
	 */
	protected function sanitize_field( $field ) {

		if (
			empty( $field['type'] )
			|| empty( $field['id'] )
		) {
			return false;
		}

		$field = $this->field_backward_compatibility( $field );

		$wpglobus_option = get_option( $this->args['opt_name'] );

		if ( ! isset( $field['name'] ) ) {
			$field['name'] = $this->args['opt_name'] . '[' . $field['id'] . ']';
		}

		// If these are not passed, get them from options.

		if ( ! isset( $field['default'] ) ) {
			$field['default'] = isset( $wpglobus_option[ $field['id'] ] ) ? $wpglobus_option[ $field['id'] ] : '';
		}
		if ( ! isset( $field['checked'] ) ) {
			$field['checked'] = isset( $wpglobus_option[ $field['id'] ] );
		}

		// Fill some missing fields with blanks.
		foreach (
			array(
				'title',
				'subtitle',
				'desc',
				'class',
				'name_suffix',
				'style',
				'value',
				'mode',
			) as $parameter
		) {
			if ( ! isset( $field[ $parameter ] ) ) {
				$field[ $parameter ] = '';
			}
		}

		return $field;
	}

	/**
	 * Backward compatibility for fields.
	 *
	 * @param array $field The field parameters.
	 *
	 * @return array Converted to the new format if necessary.
	 */
	protected function field_backward_compatibility( $field ) {

		if ( 'switcher_menu_style' === $field['id'] && 'wpglobus_select' === $field['type'] ) {
			$field = self::field_switcher_menu_style();
		}

		return $field;
	}

	/**
	 * For WPGlobus Plus.
	 *
	 * @see \WPGlobusPlus_Menu::add_option
	 *
	 * @return array Field parameters.
	 */
	public static function field_switcher_menu_style() {
		return array(
			'id'       => 'switcher_menu_style',
			'type'     => 'wpglobus_dropdown',
			'title'    => __( 'Language Selector Menu Style', 'wpglobus' ),
			'subtitle' => '(' . __( 'WPGlobus Plus', 'wpglobus' ) . ')',
			'desc'     => __( 'Drop-down languages menu or Flat (in one line)', 'wpglobus' ),
			'options'  => array(
				''         => __( 'Do not change', 'wpglobus' ),
				'dropdown' => __( 'Drop-down (vertical)', 'wpglobus' ),
				'flat'     => __( 'Flat (horizontal)', 'wpglobus' ),
			),
		);
	}

	/**
	 * Sanitize section parameters.
	 * - handle real links vs. tabs
	 * - fix icons
	 * - etc.
	 *
	 * @param array $section The array of section parameters.
	 *
	 * @return array
	 */
	protected function sanitize_section( $section ) {

		$section = $this->section_backward_compatibility( $section );

		if ( empty( $section['tab_href'] ) ) {
			// No real link, just switch tab.
			$section['tab_href'] = '#';
			$section['li_class'] = 'wpglobus-tab-link';
		} else {
			// Real link specified. Use it and do not set the "tab switching" CSS class.
			$section['li_class'] = 'wpglobus-tab-external';
		}

		// Disable A-clicks unless it's a real (external) link.
		$section['onclick'] = 'onclick="return false;"';
		if ( ! empty( $section['externalLink'] ) && $section['externalLink'] ) {
			$section['onclick'] = '';
		}

		// Use the generic icon if not specified or deprecated (Elusive).
		if ( ! isset( $section['icon'] ) || 'el-icon' === substr( $section['icon'], 0, 7 ) ) {
			$section['icon'] = 'dashicons dashicons-admin-generic';
		}

		return $section;
	}

	/**
	 * Backward compatibility for sections.
	 *
	 * @param array $section The section parameters.
	 *
	 * @return array Converted to the new format if necessary.
	 */
	protected function section_backward_compatibility( $section ) {
		/**
		 * WPGlobus Translate Options.
		 *
		 * @link https://wordpress.org/plugins/wpglobus-translate-options/
		 * @see  wpglobus_add_options_section()
		 */
		if ( 'Translation options' === $section['title'] ) {
			$section = array(
				'wpglobus_id'  => 'translate_options_link',
				'title'        => __( 'Translate strings', 'wpglobus' ),
				'tab_href'     => add_query_arg( 'page', 'wpglobus-translate-options', admin_url( 'admin.php' ) ),
				'icon'         => 'dashicons dashicons-admin-generic',
				'externalLink' => true,
			);
		}

		return $section;
	}

}
