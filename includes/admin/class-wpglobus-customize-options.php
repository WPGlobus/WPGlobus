<?php
/**
 * WPGlobus_Customize_Options
 *
 * @package    WPGlobus\Admin\Customizer
 * @since      1.4.6
 *
 * @see        http://www.narga.net/comprehensive-guide-wordpress-theme-options-with-customization-api/
 * @see        https://developer.wordpress.org/themes/advanced-topics/customizer-api/#top
 * @see        https://codex.wordpress.org/Theme_Customization_API
 * @see        #customize-controls
 */

/**
 * wpglobus_option
 * wpglobus_option_flags
 * wpglobus_option_locale
 * wpglobus_option_en_language_names
 * wpglobus_option_language_names
 * wpglobus_option_post_meta_settings
 */

/**
 *        WPGlobus option                                Customizer setting @see $wp_customize->add_setting
 *
 *    wpglobus_option[last_tab]                    => are not used in customizer
 *
 *    wpglobus_option[enabled_languages]        => wpglobus_customize_enabled_languages
 *
 *    wpglobus_option[more_languages]            => are not used in customizer
 *
 *    wpglobus_option[show_flag_name]            => wpglobus_customize_language_selector_mode
 *
 *    wpglobus_option[use_nav_menu]                => wpglobus_customize_language_selector_menu
 *
 *    wpglobus_option[selector_wp_list_pages]
 *        => Array
 *       (
 *           [show_selector] => 1                => wpglobus_customize_selector_wp_list_pages
 *       )
 *
 *    wpglobus_option[css_editor]                => wpglobus_customize_css_editor
 *
 */
if ( ! class_exists( 'WPGlobus_Customize_Options' ) ) :


	if ( ! class_exists( 'WP_Customize_Control' ) ) {
		require_once( ABSPATH . WPINC . '/class-wp-customize-control.php' );
	}

	/**
	 * Class WPGlobusTextBox.
	 * Adds textbox support to the theme customizer.
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusTextBox extends WP_Customize_Control {

		public $type = 'textbox';

		public $content = '';

		/**
		 * Constructor.
		 *
		 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
		 * @param string               $id      Control ID.
		 * @param array                $args    Optional. Arguments to override class property defaults.
		 */
		public function __construct( $manager, $id, $args = array() ) {
			$this->content  = empty( $args['content'] ) ? '' : $args['content'];
			$this->statuses = array( '' => esc_html__( 'Default', 'wpglobus' ) );
			parent::__construct( $manager, $id, $args );
		}

		public function render_content() {

			echo $this->content;

		}

	}

	/**
	 * Adds checkbox with title support to the theme customizer.
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusCheckBox extends WP_Customize_Control {

		public $type = 'wpglobus_checkbox';

		public $title = '';

		public function __construct( $manager, $id, $args = array() ) {

			$this->title = empty( $args['title'] ) ? '' : $args['title'];

			$this->statuses = array( '' => esc_html__( 'Default', 'wpglobus' ) );

			parent::__construct( $manager, $id, $args );

		}

		public function render_content() {

			?>

			<label>
				<?php if ( ! empty( $this->title ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->title ); ?></span>
				<?php endif; ?>
				<div style="display:flex;">
					<div style="flex:1">
						<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
					</div>
					<div style="flex:8">
						<?php echo esc_html( $this->label ); ?>
					</div>
				</div>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
				<?php endif; ?>
			</label>    <?php

		}
	}

	/**
	 * Adds link support to the theme customizer.
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusLink extends WP_Customize_Control {

		public $type = 'wpglobus_link';

		public $args = array();

		public function __construct( $manager, $id, $args = array() ) {

			$this->args = $args;

			$this->statuses = array( '' => esc_html__( 'Default', 'wpglobus' ) );

			parent::__construct( $manager, $id, $args );

		}

		public function render_content() {

			?>

			<label>
				<?php if ( ! empty( $this->args['title'] ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->args['title'] ); ?></span>
				<?php endif; ?>
				<a href="<?php echo $this->args['href']; // WPCS: XSS ok. ?>" target="_blank"><?php echo $this->args['text']; // WPCS: XSS ok. ?></a>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
				<?php endif; ?>
			</label>    <?php

		}
	}

	/**
	 * Adds CheckBoxSet support to the theme customizer.
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusCheckBoxSet extends WP_Customize_Control {

		public $type = 'checkbox_set';

		public $skeleton = '';

		public $args = array();

		public function __construct( $manager, $id, $args = array() ) {
			$this->args     = $args;
			$this->statuses = array( '' => esc_html__( 'Default', 'wpglobus' ) );

			$this->skeleton =
				'<a href="{{edit-link}}" target="_blank"><span style="cursor:pointer;">Edit</span></a>&nbsp;' .
				'<img style="cursor:move;" {{flag}} />&nbsp;' .
				'<input name="wpglobus_item_{{name}}" id="wpglobus_item_{{id}}" type="checkbox" checked="{{checked}}" ' .
				' class="{{class}}" ' .
				' data-order="{{order}}" data-language="{{language}}" disabled="{{disabled}}" />' .
				'<span style="cursor:move;">{{item}}</span>';

			parent::__construct( $manager, $id, $args );

		}

		public function render_content() { ?>

			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
				<?php endif;

				$new_item = str_replace( '{{class}}', 'wpglobus-checkbox ' . $this->args['checkbox_class'], $this->skeleton );
				echo '<div style="display:none;" id="wpglobus-item-skeleton">' . $new_item . '</div>'; // WPCS: XSS ok.

				echo '<ul id="wpglobus-sortable" style="margin-top:10px;margin-left:20px;">';

				foreach ( $this->args['items'] as $order => $item ) {

					$disabled = $order == 0 ? ' disabled="disabled" ' : '';

					$li_item = str_replace(
						'{{flag}}',
						'src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $item ] . '"',
						$this->skeleton
					);
					$li_item = str_replace( '{{name}}', $item, $li_item );
					$li_item = str_replace( '{{id}}', $item, $li_item );
					$li_item = str_replace( 'checked="{{checked}}"', 'checked="checked"', $li_item );
					$li_item = str_replace( 'disabled="{{disabled}}"', $disabled, $li_item );
					$li_item = str_replace( '{{class}}', 'wpglobus-checkbox ' . $this->args['checkbox_class'], $li_item );
					$li_item = str_replace( '{{item}}', WPGlobus::Config()->en_language_name[ $item ] . ' (' . $item . ')', $li_item );
					$li_item = str_replace( '{{order}}', $order, $li_item );
					$li_item = str_replace( '{{language}}', $item, $li_item );
					$li_item = str_replace(
						'{{edit-link}}',
						admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&action=edit&lang=' . $item . '"', $li_item
					);

					echo '<li>' . $li_item . '</li>'; // WPCS: XSS ok.

				}

				echo '</ul>'; ?>

			</label> <hr />   <?php

		}

	}

	/**
	 * Adds Fields Settings Control support to the theme customizer.
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusFieldsSettingsControl extends WP_Customize_Control {

		public $type = 'wpglobus_fields_settings_control';

		public $args = array();

		public $section_template = '';

		public function __construct( $manager, $id, $args = array() ) {

			$this->args = $args;

			$this->section_template = "<div id='wpglobus-settings-{{section}}' style='border-bottom:1px solid black;margin-bottom:5px;padding:5px;' class='items-box' data-section='{{section}}'>";
			$this->section_template .= esc_html__( 'Section', 'wpglobus' ) . ": <a href='#' onclick='wp.customize.section({{section_id}}).expand();'><b>{{section_title}}</b></a>";
			$this->section_template .= "<div class='items' style='padding-top:10px;'>{{items}}</div>";
			$this->section_template .= "</div>";

			parent::__construct( $manager, $id, $args );

		}

		public function render_content() {
			?>
			<div class="wpglobus-fields_settings_control_box" data-section-template="<?php echo $this->section_template; // WPCS: XSS ok. ?>">
				<?php if ( $this->args['start_section'] ) : ?>
					<div style="border-bottom:1px solid black;margin: 0 0 5px;padding-left:5px;">
						<a href="#" onclick="jQuery('.wpglobus-fields_settings_control_box .items-box' ).css('display','block');"><b><?php esc_html_e( 'Show all sections', 'wpglobus' ); ?></b></a>
					</div>
					<input type="submit" style="float:right;" name="<?php echo WPGlobus_Customize_Options::$controls_save_button; // WPCS: XSS ok. ?>" id="<?php echo WPGlobus_Customize_Options::$controls_save_button; // WPCS: XSS ok. ?>" class="button button-primary save" value="<?php esc_html_e( 'Save &amp; Reload', 'wpglobus' ); ?>">
				<?php else: ?>
					<div>
						<?php if ( ! empty( $this->args['message'] ) ) {
							echo $this->args['message']; // WPCS: XSS ok.
						} ?>
					</div>
				<?php endif; ?>
			</div>    <!-- .wpglobus-fields_settings_control_box -->    <?php
		}
	}

	/**
	 * Class WPGlobus_Customize_Options
	 */
	class WPGlobus_Customize_Options {

		/**
		 * Array of sections
		 */
		public static $sections = array();

		/**
		 * Array of settings
		 */
		public static $settings = array();

		/**
		 * Set transient key
		 */
		public static $enabled_post_types_key = 'wpglobus_customize_enabled_post_types';

		/**
		 * Set option key for customizer
		 */
		public static $options_key = 'wpglobus_customize_options';

		/**
		 * Save button ID
		 */
		public static $controls_save_button = 'wpglobus-user-controls-save';

		/**
		 * Current theme.
		 *
		 * @var WP_Theme
		 */
		public static $theme;

		/**
		 * Current theme name.
		 *
		 * @var string
		 */
		public static $theme_name = '';

		/**
		 * Array of disabled themes.
		 * @var string[]
		 */
		public static $disabled_themes = array();

		public static function controller() {

			self::$theme      = wp_get_theme();
			self::$theme_name = self::get_theme( 'name' );

			self::$disabled_themes = array(
				'customizr',
				'customizr pro'
			);

			/**
			 * @since 1.7.7
			 * Don't standard loading of the option 'theme_mods_experon'. Theme uses redux.
			 * Don't standard behavior in customizer.
			 * @see https://wordpress.org/themes/experon/
			 */
			self::$disabled_themes[] = 'experon';

			add_action( 'wp_loaded', array( __CLASS__, 'init' ) );

			/**
			 * @see \WP_Customize_Manager::wp_loaded
			 * It calls the `customize_register` action first,
			 * and then - the `customize_preview_init` action
			 */
			add_action( 'customize_register', array(
				'WPGlobus_Customize_Options',
				'action__customize_register'
			) );

			/**
			 * @since 1.6.0
			 */
			add_action( 'customize_register', array(
				'WPGlobus_Customize_Options',
				'action__customize_fields_settings'
			) );

			add_action( 'customize_preview_init', array(
				'WPGlobus_Customize_Options',
				'action__customize_preview_init'
			), 11 );

			/**
			 * This is called by wp-admin/customize.php
			 */

			add_action( 'customize_controls_enqueue_scripts', array(
				'WPGlobus_Customize_Options',
				'action__customize_controls_enqueue_scripts'
			), 1010 );

			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array(
				'WPGlobus_Customize_Options',
				'action__process_ajax'
			) );

			/**
			 * @since 1.9.8
			 */
			add_filter( 'wpglobus_customize_disabled_setting_mask', array(
				__CLASS__,
				'filter__disabled_setting_mask'
			) );

		}
		
		/**
		 * Delayed processes.
		 * @since 1.6.0
		 */
		public static function init() {

			/**
			 * Hook to modify the `$disabled_themes` array.
			 *
			 * @param string[] self              ::$disabled_themes
			 *                                   Enter the lowercase theme name (not slug, no dashes).
			 *                                   For example, to disable the "Parallax One" theme,
			 *                                   enter 'parallax one'.
			 *
			 * @see second param.
			 *
			 * @param          string            self::$theme_name Name of current theme.
			 * @param          WP_Theme          Object    self::$theme      Current theme.
			 */
			self::$disabled_themes = apply_filters( 'wpglobus_customizer_disabled_themes', self::$disabled_themes, self::$theme_name, self::$theme );
		}

		/**
		 * Ajax handler.
		 */
		public static function action__process_ajax() {

			$result      = true;
			$ajax_return = array();

			$post_order = isset( $_POST['order'] ) ? $_POST['order'] : null; // WPCS: input var ok, sanitization ok.

			$order = array();
			if ( $post_order ) {
				$order['action']   = sanitize_text_field( $post_order['action'] );
				$order['options']  = isset($post_order['options'])  ? $post_order['options']  : array();
				$order['controls'] = isset($post_order['controls']) ? $post_order['controls'] : array();
			}

			switch ( $order['action'] ) {
				case 'wpglobus_customize_save':
					/**
					 * Options array.
					 *
					 * @var array
					 */
					$options = get_option( WPGlobus::Config()->option );

					foreach ( $order['options'] as $key => $value ) {

						switch ( $key ) :
							case 'show_selector':
								$options['selector_wp_list_pages'][ $key ] = $value;
								break;
							case 'redirect_by_language':
								// @todo check this option which do we really need?
								$options['browser_redirect'][ $key ] = $value;
								$options[ $key ]                     = $value;
								break;
							case 'use_nav_menu':
								if ( '0' == $value ) {
									$value = '';
								} else {
									$value = sanitize_text_field( $value );
								}
								$options[ $key ] = $value;
								break;
							case 'js_editor':
								$value = trim($value);
								if ( ! empty( $value ) ) {
									$value = str_replace( '\"', '"', $value );
									$value = str_replace( "\'", "'", $value );
									$value = esc_html( $value );
								}
								$options[ $key ] = $value;
								break;
							default:
								$options[ $key ] = $value;
						endswitch;

					}

					update_option( WPGlobus::Config()->option, $options );
					break;

				case 'cb-controls-save':
					$options = get_option( self::$options_key );

					if ( empty( $order['controls'] ) ) {
						if ( ! empty( $options['customize_user_control'][ self::$theme_name ] ) ) {
							unset( $options['customize_user_control'][ self::$theme_name ] );
						}
					} else {

						$cntrls = array();
						foreach ( $order['controls'] as $cntr => $status ) {
							$cntr = str_replace( '{{', '[', $cntr );
							$cntr = str_replace( '}}', ']', $cntr );

							$cntrls[ $cntr ] = $status;
						}

						$options['customize_user_control'][ self::$theme_name ] = $cntrls;
					}

					if ( empty( $options['customize_user_control'] ) ) {
						unset( $options['customize_user_control'] );
					}

					if ( empty( $options ) ) {
						delete_option( self::$options_key );
					} else {
						$result = update_option( self::$options_key, $options, false );

					}

					break;
			}

			if ( false === $result ) {
				wp_send_json_error( $ajax_return );
			}

			wp_send_json_success( $ajax_return );
		}

		/**
		 * Section for message about unsupported theme.
		 *
		 * @param WP_Customize_Manager $wp_customize
		 * @param WP_Theme             $theme
		 */
		public static function sorry_section( $wp_customize, $theme ) {

			/**
			 * Sorry section
			 */
			$wp_customize->add_section( 'wpglobus_sorry_section', array(
				'title'    => esc_html__( 'WPGlobus', 'wpglobus' ),
				'priority' => 0,
				'panel'    => 'wpglobus_settings_panel'
			) );

			$wp_customize->add_setting( 'sorry_message', array(
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage'
			) );
			$wp_customize->add_control( new WPGlobusTextBox( $wp_customize,
				'sorry_message', array(
					'section'  => 'wpglobus_sorry_section',
					'settings' => 'sorry_message',
					'priority' => 0,
					'content'  => self::get_content( 'sorry_message', $theme )

				)
			) );

		}

		/**
		 * Callback for register fields settings section.
		 *
		 * @since 1.6.0
		 *
		 * @param WP_Customize_Manager $wp_customize
		 */
		public static function action__customize_fields_settings( WP_Customize_Manager $wp_customize ) {

			if ( ! self::is_theme_enabled() ) {
				return;
			}

			/**
			 * SECTION: fields settings
			 */
			if ( 1 ) {

				/**
				 * @since WP 4.5
				 * @see   https://make.wordpress.org/core/2016/03/10/customizer-improvements-in-4-5/
				 */

				global $wp_version;

				$start_section = true;
				$message       = '';
				if ( version_compare( $wp_version, '4.5-RC1', '<' ) ) :
					$start_section = false;
					$message       = esc_html__( 'You need to update WordPress to 4.5 or later to get Fields Settings section', 'wpglobus' );
				endif;

				self::$sections['wpglobus_fields_settings_section'] = 'wpglobus_fields_settings_section';

				$wp_customize->add_section( self::$sections['wpglobus_fields_settings_section'], array(
					'title'       => esc_html__( 'Fields Settings', 'wpglobus' ),
					'priority'    => 500,
					'panel'       => 'wpglobus_settings_panel',
					'description' => '<div class="inner" style="background-color:#fff;padding:0 5px;">' .
					                 self::get_content( 'settings_section_help' ) .
					                 '</div>'
				) );

				/** setting  */
				$wp_customize->add_setting( 'wpglobus_fields_settings_setting', array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'transport'  => 'postMessage'
				) );

				/** control */
				$wp_customize->add_control( new WPGlobusFieldsSettingsControl( $wp_customize,
					self::$sections['wpglobus_fields_settings_section'], array(
						'section'       => self::$sections['wpglobus_fields_settings_section'],
						'settings'      => 'wpglobus_fields_settings_setting',
						'priority'      => 0,
						'start_section' => $start_section,
						'message'       => $message

					)
				) );

			}

		}

		/**
		 * Callback for customize_register.
		 *
		 * @param WP_Customize_Manager $wp_customize
		 */
		public static function action__customize_register( WP_Customize_Manager $wp_customize ) {

			/**
			 * WPGlobus panel
			 */
			$wp_customize->add_panel( 'wpglobus_settings_panel', array(
				'priority'       => 1010,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => esc_html__( 'WPGlobus Settings', 'wpglobus' ),
				'description'    => '<div style="background-color:#eee;padding:10px 5px;">' .
				                    self::get_content( 'welcome_message' ) .
				                    '</div>' . self::get_content( 'deactivate_message' ),
			) );

			if ( ! self::is_theme_enabled() ) {

				self::sorry_section( $wp_customize, self::$theme );

				return;

			}

			/**
			 * Updating options for customizer accordingly with WPGlobus::Config().
			 */
			/** wpglobus_customize_language_selector_mode <=> wpglobus_option[show_flag_name] */
			update_option( 'wpglobus_customize_language_selector_mode', WPGlobus::Config()->show_flag_name );

			/**  */
			if ( empty( WPGlobus::Config()->nav_menu ) ) {
				/**
				 * Menu item '--- select navigation menu ---' has value 0.
				 * It is used when 'Language Selector Menu' setting is not selected.
				 */
				update_option( 'wpglobus_customize_language_selector_menu', '0' );
			} else {
				update_option( 'wpglobus_customize_language_selector_menu', WPGlobus::Config()->nav_menu );
			}

			/** wpglobus_customize_selector_wp_list_pages <=> wpglobus_option[selector_wp_list_pages][show_selector]  */
			update_option( 'wpglobus_customize_selector_wp_list_pages', WPGlobus::Config()->selector_wp_list_pages );

			/** wpglobus_customize_css_editor <=> wpglobus_option[css_editor]  */
			update_option( 'wpglobus_customize_css_editor', WPGlobus::Config()->css_editor );

			/** wpglobus_customize_redirect_by_language <=> wpglobus_option[browser_redirect][redirect_by_language]  */
			if ( empty( WPGlobus::Config()->browser_redirect['redirect_by_language'] ) || (int) WPGlobus::Config()->browser_redirect['redirect_by_language'] == 0 ) {
				update_option( 'wpglobus_customize_redirect_by_language', '' );
			} else {
				update_option( 'wpglobus_customize_redirect_by_language', WPGlobus::Config()->browser_redirect['redirect_by_language'] );
			}
			
			/** wpglobus_customize_js_editor <=> wpglobus_option[js_editor]  */
			if ( empty( WPGlobus::Config()->js_editor ) ) {
				update_option( 'wpglobus_customize_js_editor', '' );
			} else {
				update_option( 'wpglobus_customize_js_editor', WPGlobus::Config()->js_editor );
			}

			
			/** end updating options */
			
			/**
			 * Init section priority.
			 */
			$section_priority = 0;
			
			/**
			 * SECTION: Help.
			 */
			if ( 0 ) {
				
				$section_priority = $section_priority + 0;
				
				self::$sections['wpglobus_help_section'] = 'wpglobus_help_section';
				$wp_customize->add_section( self::$sections['wpglobus_help_section'], array(
					'title'    => esc_html__( 'Help', 'wpglobus' ),
					'priority' => $section_priority,
					'panel'    => 'wpglobus_settings_panel',
				) );

				$wp_customize->add_control( 'wpglobus_customize_add_onsZZZ', array(
						'section'  => self::$sections['wpglobus_help_section'],
						'settings' => array(),
						'type'     => 'button',
					)
				);
			}			
			/** end SECTION: Help */
			
			/**
			 * SECTION: Language.
			 */
			if ( 1 ) {
				
				$section_priority = $section_priority + 10;
				
				$wp_customize->add_section( 'wpglobus_languages_section', array(
					'title'    => esc_html__( 'Languages', 'wpglobus' ),
					'priority' => $section_priority,
					'panel'    => 'wpglobus_settings_panel'
				) );
				self::$sections['wpglobus_languages_section'] = 'wpglobus_languages_section';

				/** 
				 * Setting: Enabled languages.
				 */
				$wp_customize->add_setting( 'wpglobus_customize_enabled_languages', array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'transport'  => 'postMessage'
				) );
				$wp_customize->add_control( new WPGlobusCheckBoxSet( $wp_customize,
					'wpglobus_customize_enabled_languages', array(
						'section'        => 'wpglobus_languages_section',
						'settings'       => 'wpglobus_customize_enabled_languages',
						'priority'       => 0,
						'items'          => WPGlobus::Config()->enabled_languages,
						'label'          => esc_html__( 'Enabled Languages', 'wpglobus' ),
						'checkbox_class' => 'wpglobus-listen-change wpglobus-language-item',
						'description'    => esc_html__( 'These languages are currently enabled on your site.', 'wpglobus' )

					)
				) );
				self::$settings['wpglobus_languages_section']['wpglobus_customize_enabled_languages']['type'] = 'checkbox_set';
				/** @see option wpglobus_option['enabled_languages'] */
				self::$settings['wpglobus_languages_section']['wpglobus_customize_enabled_languages']['option'] = 'enabled_languages';

				/** 
				 * Setting: Add languages.
				 */
				/** Generate array $more_languages */
				/** @var array $more_languages */
				$more_languages           = array();
				$more_languages['select'] = '---- select ----';

				foreach ( WPGlobus::Config()->flag as $code => $file ) {
					if ( ! in_array( $code, WPGlobus::Config()->enabled_languages ) ) {
						$lang_in_en = '';
						if ( ! empty( WPGlobus::Config()->en_language_name[ $code ] ) ) {
							$lang_in_en = ' (' . WPGlobus::Config()->en_language_name[ $code ] . ')';
						}
						// '<img src="' . WPGlobus::Config()->flags_url . $file . '" />'
						$more_languages[ $code ] = WPGlobus::Config()->language_name[ $code ] . $lang_in_en;
					}
				}

				$desc_add_languages =
					esc_html__( 'Choose a language you would like to enable.', 'wpglobus' ).
					'<br />'.
					esc_html__( 'Press the [Save & Publish] button to confirm.', 'wpglobus' ).
					'<br />';
				$desc_add_languages .= sprintf(
					// translators: %1$s and %2$s - placeholders to insert HTML link around 'here'
					esc_html__( 'or Add new Language %1$s here %2$s', 'wpglobus' ),
					'<a style="text-decoration:underline;" href="' . admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&action=add" target="_blank">',
					'</a>'
				);

				$wp_customize->add_setting( 'wpglobus_customize_add_language', array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'transport'  => 'postMessage'
				) );
				$wp_customize->add_control( 'wpglobus_add_languages_select_box', array(
					'settings'    => 'wpglobus_customize_add_language',
					'label'       => esc_html__( 'Add Languages', 'wpglobus' ),
					'section'     => 'wpglobus_languages_section',
					'type'        => 'select',
					'priority'    => 10,
					'choices'     => $more_languages,
					'description' => $desc_add_languages
				) );
				//self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_add_language' ] = 'select';

				/** 
				 * Setting: Language Selector Mode.
				 */
				$wp_customize->add_setting( 'wpglobus_customize_language_selector_mode', array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'transport'  => 'refresh'
					#'transport' => 'postMessage'
				) );
				$wp_customize->add_control( 'wpglobus_customize_language_selector_mode', array(
					'settings'    => 'wpglobus_customize_language_selector_mode',
					'label'       => esc_html__( 'Language Selector Mode', 'wpglobus' ),
					'section'     => 'wpglobus_languages_section',
					'type'        => 'select',
					'priority'    => 20,
					'choices'     => array(
						'code'      => esc_html__( 'Two-letter Code with flag (en, ru, it, etc.)', 'wpglobus' ),
						'full_name' => esc_html__( 'Full Name (English, Russian, Italian, etc.)', 'wpglobus' ),
						/* @since 1.2.1 */
						'name'      => esc_html__( 'Full Name with flag (English, Russian, Italian, etc.)', 'wpglobus' ),
						'empty'     => esc_html__( 'Flags only', 'wpglobus' )
					),
					'description' => esc_html__( 'Choose the way language name and country flag are shown in the drop-down menu', 'wpglobus' )
				) );
				self::$settings['wpglobus_languages_section']['wpglobus_customize_language_selector_mode']['type'] = 'select';
				/** @see option wpglobus_option['show_flag_name'] */
				self::$settings['wpglobus_languages_section']['wpglobus_customize_language_selector_mode']['option'] = 'show_flag_name';

				/**
				 * @see https://make.wordpress.org/core/2016/03/22/implementing-selective-refresh-support-for-widgets/
				 * @see https://make.wordpress.org/core/2016/03/10/customizer-improvements-in-4-5/
				 *
				 * $wp_customize->selective_refresh->add_partial( 'wpglobus_customize_language_selector_mode', array(
				 * 'selector' => '#site-navigation',
				 * 'render_callback' => function() {
				 * wp_nav_menu();
				 * },
				 * ) );
				 * // */

				/** 
				 * Setting: Language Selector Menu.
				 */
				/** @var array $nav_menus */
				$nav_menus = WPGlobus::_get_nav_menus();

				$menus = array();

				foreach ( $nav_menus as $menu ) {
					$menus[ $menu->slug ] = $menu->name;
				}
				if ( ! empty( $nav_menus ) && count( $nav_menus ) > 1 ) {
					$menus['all'] = 'All';
				}
				if ( ! empty( $nav_menus ) ) {
					array_unshift(
						$menus, 
						'--- ' . esc_html__('select navigation menu', 'wpglobus'). ' ---'
					);
				}

				if ( empty( $menus ) ) {

					$wp_customize->add_control( new WPGlobusLink( $wp_customize,
						'wpglobus_customize_language_selector_menu', array(
							'section'     => 'wpglobus_languages_section',
							'title'       => esc_html__( 'Language Selector Menu', 'wpglobus' ),
							'settings'    => array(),
							'priority'    => 30,
							'type'        => 'wpglobus_link',
							/**
							 * We are in Customizer, so we can "focus" to the menus and not go to menus in admin.
							 * The JS code and the message below are copied from
							 * @see WP_Nav_Menu_Widget::form
							 */
							'href'        => esc_attr( 'javascript: wp.customize.panel( "nav_menus" ).focus();' ),
							'text'        => esc_html__( 'No menus have been created yet. Create some.', 'wpglobus' ),
							'description' => esc_html__( 'Choose the navigation menu where the language selector will be shown', 'wpglobus' ),
						)
					) );

					self::$settings['wpglobus_languages_section']['wpglobus_customize_language_selector_menu']['type']   = 'wpglobus_link';
					self::$settings['wpglobus_languages_section']['wpglobus_customize_language_selector_menu']['option'] = array();

				} else {

					$wp_customize->add_setting( 'wpglobus_customize_language_selector_menu', array(
						'type'       => 'option',
						'capability' => 'manage_options',
						'transport'  => 'postMessage'
					) );
					$wp_customize->add_control( 'wpglobus_customize_language_selector_menu', array(
						'settings'    => 'wpglobus_customize_language_selector_menu',
						'label'       => esc_html__( 'Language Selector Menu', 'wpglobus' ),
						'section'     => 'wpglobus_languages_section',
						'type'        => 'select',
						'priority'    => 30,
						'choices'     => $menus,
						'description' => esc_html__( 'Choose the navigation menu where the language selector will be shown', 'wpglobus' ),
					) );

					self::$settings['wpglobus_languages_section']['wpglobus_customize_language_selector_menu']['type'] = 'select';
					/** @see option wpglobus_option['use_nav_menu'] */
					self::$settings['wpglobus_languages_section']['wpglobus_customize_language_selector_menu']['option'] = 'use_nav_menu';

				}

				/** 
				 * Setting: "All Pages" menus Language selector.
				 */
				$wp_customize->add_setting( 'wpglobus_customize_selector_wp_list_pages', array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'transport'  => 'postMessage'
				) );
				$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize,
					'wpglobus_customize_selector_wp_list_pages', array(
						'settings' => 'wpglobus_customize_selector_wp_list_pages',
						'title'    => esc_html__( '"All Pages" menus Language selector', 'wpglobus' ),
						'section'  => 'wpglobus_languages_section',
						'priority' => 40,
						'label'    => esc_html__( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
					)
				) );
				self::$settings['wpglobus_languages_section']['wpglobus_customize_selector_wp_list_pages']['type'] = 'wpglobus_checkbox';
				/** @see option wpglobus_option['selector_wp_list_pages']['show_selector'] */
				self::$settings['wpglobus_languages_section']['wpglobus_customize_selector_wp_list_pages']['option'] = 'show_selector';

				/**
				 * Setting: Custom CSS.
				 */
				$wp_customize->add_setting( 'wpglobus_customize_css_editor', array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'transport'  => 'postMessage'
				) );
				$wp_customize->add_control( 'wpglobus_customize_css_editor', array(
					'settings'    => 'wpglobus_customize_css_editor',
					'label'       => esc_html__( 'Custom CSS', 'wpglobus' ),
					'section'     => 'wpglobus_languages_section',
					'type'        => 'textarea',
					'priority'    => 50,
					'description' => esc_html__( 'Here you can enter the CSS rules to adjust the language selector menu for your theme. Look at the examples in the `style-samples.css` file.', 'wpglobus' ),
				) );
				self::$settings['wpglobus_languages_section']['wpglobus_customize_css_editor']['type'] = 'textarea';
				/** @see option wpglobus_option['css_editor'] */
				self::$settings['wpglobus_languages_section']['wpglobus_customize_css_editor']['option'] = 'css_editor';

			}
			/** end SECTION: Language */

			/**
			 * SECTION: Post types.
			 */
			if ( 1 ) {
				
				$section_priority = $section_priority + 10;
				
				$section = 'wpglobus_post_types_section';

				$wp_customize->add_section( $section, array(
					'title'    => esc_html__( 'Post types', 'wpglobus' ),
					'priority' => $section_priority,
					'panel'    => 'wpglobus_settings_panel'
				) );
				self::$sections[ $section ] = $section;

				if ( false === ( $enabled_post_types = get_transient( self::$enabled_post_types_key ) ) ) {

					$post_types = get_post_types();

					$enabled_post_types = array();
					
					foreach ( $post_types as $post_type ) {
						/**
						 * @see "SECTION: Post types" in includes\options\class-wpglobus-options.php for complete post type array.
						 */
						if ( ! in_array( $post_type, WPGlobus_Post_Types::hidden_types(), true ) ) {

							/**
							 * Obsolete from 1.9.10.
							 * @todo Remove after testing.
							 */
							/**
							
							if ( in_array( $post_type, array( 'post', 'page' ) ) ) {
								$enabled_post_types[ $post_type ] = $post_type;
								continue;
							}							
							
							foreach ( WPGlobus::O()->vendors_scripts as $script => $status ) {

								if ( empty( $status ) ) {
									continue;
								}

								if ( $script == 'ACF' || $script == 'ACFPRO' ) {
									if ( in_array( $post_type, array( 'acf-field-group', 'acf-field', 'acf' ) ) ) {
										continue 2;
									}
								}

								if ( $script == 'WOOCOMMERCE' ) {
									if ( in_array(
										$post_type,
										array(
											'product',
											'product_tag',
											'product_cat',
											'shop_order',
											'shop_coupon',
											'product_variation',
											'shop_order_refund',
											'shop_webhook'
										)
									) ) {
										continue 2;
									}
								}

								if ( $script == 'WPCF7' ) {
									if ( in_array( $post_type, array( 'wpcf7_contact_form' ) ) ) {
										continue 2;
									}
								}

							}
							// */
							
							$enabled_post_types[ $post_type ] = $post_type;

						}
					}

					set_transient( self::$enabled_post_types_key, $enabled_post_types, 60 );

				}

				foreach ( $enabled_post_types as $post_type ) :

					$status = '';

					if ( isset( WPGlobus::Config()->extended_options['post_type'][ $post_type ] ) ) {

						if ( WPGlobus::Config()->extended_options['post_type'][ $post_type ] == 1 ) {
							$status = '1';
						}

					} else {
						$status = '1';
					}

					update_option( 'wpglobus_customize_post_type_' . $post_type, $status );

				endforeach;

				$i = 0;
				foreach ( $enabled_post_types as $post_type ) :

					$pst = 'wpglobus_customize_post_type_' . $post_type;

					$wp_customize->add_setting( $pst, array(
						'type'       => 'option',
						'capability' => 'manage_options',
						'transport'  => 'postMessage'
					) );

					$title = '';
					if ( $i == 0 ) {
						$title = esc_html__( 'Uncheck to disable WPGlobus', 'wpglobus' );
					}

					$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize,
						$pst, array(
							'settings' => $pst,
							'title'    => $title,
							'label'    => $post_type,
							'section'  => $section,
							#'default'		=> '1',
							'priority' => 10,
						)
					) );

					$i ++;
					self::$settings[ $section ][ $pst ]['type'] = 'wpglobus_checkbox';
					/** @see option wpglobus_option['post_type'] */
					self::$settings[ $section ][ $pst ]['option'] = 'post_type';

				endforeach;

			};
			/** end SECTION: Post types */

			/**
			 * SECTION: Redirect.
			 */
			if ( 1 ) {
				
				$section_priority = $section_priority + 10;
				
				self::$sections['wpglobus_redirect_section'] = 'wpglobus_redirect_section';
				
				$wp_customize->add_section( self::$sections['wpglobus_redirect_section'], array(
					'title'    => esc_html__( 'Redirect', 'wpglobus' ),
					'priority' => $section_priority,
					'panel'    => 'wpglobus_settings_panel',
				) );
				
				/**
				 * Option
				 *  [browser_redirect] => Array
				 *	(
				 *		[redirect_by_language] => 0
				 *	)
				 */
				
				/** 
				 * Setting wpglobus_customize_redirect_by_language.
				 */
				$wp_customize->add_setting( 'wpglobus_customize_redirect_by_language', array(
					'type'       => 'option',
					'capability' => 'manage_options',
					'transport'  => 'postMessage'
				) );
				$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize,
					'wpglobus_customize_redirect_by_language', array(
						'section'  		=> self::$sections['wpglobus_redirect_section'],
						'settings' 		=> 'wpglobus_customize_redirect_by_language',
						'title'    		=> esc_html__( 'Choose the language automatically, based on:', 'wpglobus' ),
						'priority' 		=> 10,
						'label'    		=> esc_html__( 'Preferred language set in the browser', 'wpglobus' ),
						'description' 	=> esc_html__('When a user comes to the site for the first time, try to find the best matching language version of the page.', 'wpglobus')
					)
				) );
			
				self::$settings[ self::$sections['wpglobus_redirect_section'] ]['wpglobus_customize_redirect_by_language']['type'] 		= 'wpglobus_checkbox';
				/** @see option wpglobus_option[browser_redirect][redirect_by_language] */
				self::$settings[ self::$sections['wpglobus_redirect_section'] ]['wpglobus_customize_redirect_by_language']['option'] 	= 'redirect_by_language';

			}
			/** end SECTION: Redirect */
			
			/**
			 * SECTION: Custom JS Code.
			 */
			if ( 1 ) {
				
				$section_priority = $section_priority + 10;
				
				self::$sections['wpglobus_js_editor_section'] = 'wpglobus_js_editor_section';
				
				/** 
				 * Setting 'wpglobus_customize_js_editor'.
				 */
				 
				/**
				 * class WP_Customize_Code_Editor_Control
				 * @since WordPress 4.9.0
				 * @see https://developer.wordpress.org/reference/classes/wp_customize_code_editor_control/
				 */
				if ( ! class_exists('WP_Customize_Code_Editor_Control') ) {

					$content = esc_html__( 'To add a Custom JS Code in Customizer, you need to upgrade WordPress to version 4.9 or later.', 'wpglobus' );
					$content .= '<br /><br />' .
								esc_html__( 'With your version of WordPress, please use the', 'wpglobus' ) .
								' <a style="text-decoration:underline;" target="_blank" href="' . esc_url( admin_url() . 'admin.php?page=' . WPGlobus::OPTIONS_PAGE_SLUG . '&tab=0' ) . '">' .
								esc_html__( 'WPGlobus Settings page', 'wpglobus' ) .
								'.</a>';
					
					$wp_customize->add_section( self::$sections['wpglobus_js_editor_section'], array(
						'title'    => esc_html__( 'Custom JS Code', 'wpglobus' ),
						'priority' => $section_priority,
						'panel'    => 'wpglobus_settings_panel',
					) );
					
					$wp_customize->add_setting( 'wpglobus_customize_js_editor', array(
						'type'       => 'option',
						'capability' => 'manage_options',
						'transport'  => 'postMessage'
					) );
					
					$wp_customize->add_control( new WPGlobusTextBox( $wp_customize,
						'wpglobus_customize_js_editor', array(
							'section'  => self::$sections['wpglobus_js_editor_section'],
							'settings' => 'wpglobus_customize_js_editor',
							'content'  => $content
						)
					) );
					
				} else {
					
					$wp_customize->add_section( self::$sections['wpglobus_js_editor_section'], array(
						'title'    => esc_html__( 'Custom JS Code', 'wpglobus' ),
						'priority' => $section_priority,
						'panel'    => 'wpglobus_settings_panel',
					) );
		

					$wp_customize->add_setting( 'wpglobus_customize_js_editor', array(
						'type'       => 'option',
						'capability' => 'manage_options',
						'transport'  => 'postMessage'
					) );
					$wp_customize->add_control( new WP_Customize_Code_Editor_Control( $wp_customize,
						'wpglobus_customize_js_editor', array(
							'code_type' 	=> 'javascript',
							#'mode'			=> 'javascript',
							'input_attrs'	=> array('rows'=>80),
							'section'  		=> self::$sections['wpglobus_js_editor_section'],
							'settings' 		=> 'wpglobus_customize_js_editor',
							'title'    		=> esc_html__( 'Title', 'wpglobus' ),
							'priority' 		=> 10,
							'label'    		=> esc_html__( 'Custom JS Code', 'wpglobus' ),
							'description' 	=> esc_html__( '(Paste your JS code here.)', 'wpglobus' )
						)
					) );
		
					self::$settings[ self::$sections['wpglobus_js_editor_section'] ]['wpglobus_customize_js_editor']['type'] = 'code_editor';
					/** @see option wpglobus_option['css_editor'] */
					self::$settings[ self::$sections['wpglobus_js_editor_section'] ]['wpglobus_customize_js_editor']['option'] = 'js_editor';	
				
				}
			}				
			/** end SECTION: Custom JS Code */
			
			/**
			 * SECTION: Add ons.
			 */
			if ( 1 ) {

				$section_priority = $section_priority + 10;
			
				global $wp_version;

				self::$sections['wpglobus_addons_section'] = 'wpglobus_addons_section';

				if ( version_compare( $wp_version, '4.5-RC1', '<' ) ) {

					$wp_customize->add_section( self::$sections['wpglobus_addons_section'], array(
						'title'    => esc_html__( 'Add-ons', 'wpglobus' ),
						'priority' => $section_priority,
						'panel'    => 'wpglobus_settings_panel',
					) );

					/** Add ons setting  */
					$wp_customize->add_setting( 'wpglobus_customize_add_ons', array(
						'type'       => 'option',
						'capability' => 'manage_options',
						'transport'  => 'postMessage',
					) );

					$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize,
						'wpglobus_customize_add_ons', array(
							'settings'    => 'wpglobus_customize_add_ons',
							'title'       => esc_html__( 'Title', 'wpglobus' ),
							'label'       => esc_html__( 'Label', 'wpglobus' ),
							'section'     => self::$sections['wpglobus_addons_section'],
							'type'        => 'checkbox',
							'priority'    => 10,
							'description' => esc_html__( 'Description', 'wpglobus' ),
						)
					) );

				} else {

					/**
					 * Changes in WP 4.5
					 * @link https://make.wordpress.org/core/2016/03/10/customizer-improvements-in-4-5/
					 */

					$wp_customize->add_section( self::$sections['wpglobus_addons_section'], array(
						'title'    => esc_html__( 'Add-ons', 'wpglobus' ),
						'priority' => $section_priority,
						'panel'    => 'wpglobus_settings_panel',
					) );

					$wp_customize->add_control( 'wpglobus_customize_add_ons', array(
							'section'  => self::$sections['wpglobus_addons_section'],
							'settings' => array(),
							'type'     => 'button',
						)
					);
				}
			}
			/** end SECTION: Add ons */

			/**
			 * Fires to add customize settings.
			 *
			 * @since 1.4.6
			 *
			 * @param WP_Customize_Manager $wp_customize The WP Customize Manager.
			 */
			do_action( 'wpglobus_customize_register', $wp_customize );

			/** @var array $res */
			$res = apply_filters( 'wpglobus_customize_data', array(
				'sections' => self::$sections,
				'settings' => self::$settings,
			) );

			self::$sections = $res['sections'];
			self::$settings = $res['settings'];

		}

		/**
		 * Get content for WPGlobusTextBox element.
		 *
		 * @param string $control
		 * @param mixed  $attrs
		 *
		 * @return string
		 */
		public static function get_content( $control = '', $attrs = null ) {

			if ( '' == $control ) {
				return '';
			}

			$content = '';
			switch ( $control ) :
				case 'settings_section_help' :
					$content = esc_html__( 'Here you can specify which fields should be considered multilingual by WPGlobus. To exclude a field, uncheck it and then press the button below.', 'wpglobus' );

					break;
				case 'welcome_message' :

					$content = '<div style="width:100%;">' .
					           esc_html__( 'Thank you for installing WPGlobus!', 'wpglobus' ) .
					           '<br/>' .
					           '&bull; ' .
					           '<a style="text-decoration:underline;" target="_blank" href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_ABOUT . '">' .
					           esc_html__( 'Read About WPGlobus', 'wpglobus' ) .
					           '</a>' .
					           '<br/>' .
					           '&bull; ' . esc_html__( 'Click the <strong>[Languages]</strong> tab at the left to setup the options.', 'wpglobus' ) .
					           #'<br/>' .
					           #'&bull; ' . esc_html__( 'Use the <strong>[Languages Table]</strong> section to add a new language or to edit the language attributes: name, code, flag icon, etc.', 'wpglobus' ) .
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
					           '</div>';

					break;
				case 'deactivate_message' :

					/**
					 * For Google Analytics
					 */
					$ga_campaign = '?utm_source=wpglobus-admin-clean&utm_medium=link&utm_campaign=talk-to-us';

					$url_wpglobus_site               = WPGlobus_Utils::url_wpglobus_site();
					$url_wpglobus_site_submit_ticket = $url_wpglobus_site . 'support/submit-ticket/' . $ga_campaign;

					$content = '<p><em>' .
					           sprintf(
						           esc_html(
						           		// translators: %?$s: HTML codes for hyperlink. Do not remove.
							           __( 'We would hate to see you go. If something goes wrong, do not uninstall WPGlobus yet. Please %1$stalk to us%2$s and let us help!', 'wpglobus' ) ),
						           '<a href="' . $url_wpglobus_site_submit_ticket . '" target="_blank" style="text-decoration:underline;">',
						           '</a>'
					           ) .
					           '</em></p>' .
					           '<hr/>' .
					           '<p><i class="el el-exclamation-sign" style="color:red"></i> <strong>' .
					           esc_html( __( 'Please note that if you deactivate WPGlobus, your site will show all the languages together, mixed up. You will need to remove all translations, keeping only one language.', 'wpglobus' ) ) .
					           '</strong></p>' .
					           '<p>' .
					           sprintf(
						           // translators: %s: link to the Clean-up Tool
					           		esc_html__( 'If there are just a few places, you should edit them manually. To automatically remove all translations at once, you can use the %s. WARNING: The clean-up operation is irreversible, so use it only if you need to completely uninstall WPGlobus.', 'wpglobus' ),
						           sprintf(
						           // translators: %?$s: HTML codes for hyperlink. Do not remove.
						           	esc_html__( '%1$sClean-up Tool%2$s', 'wpglobus' ),
							           '<a style="text-decoration:underline;" target="_blank" href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_CLEAN . '">',
							           '</a>'
						           ) ) .
					           '</p>';

					break;
				case 'sorry_message' :

					$content = '<p><strong>' .
					           sprintf(
					           	// translators: %s: name of current theme
					           	esc_html__( 'Sorry, WPGlobus customizer doesn\'t support current theme %s.', 'wpglobus' ),
						           '<em>' . $attrs->__get( 'name' ) . '</em>'
					           ) .
					           '<br />' .
					           sprintf(
						           // translators: %?$s: HTML codes for hyperlink. Do not remove.
					           	esc_html__( 'Please use %1$sWPGlobus options page%2$s instead.', 'wpglobus' ),
						           '<a style="text-decoration:underline;" target="_blank" href="' . admin_url() . 'admin.php?page=' . WPGlobus::OPTIONS_PAGE_SLUG . '&tab=0">',
						           '</a>'
					           ) .
					           '</strong></p>';

					break;
			endswitch;

			return $content;

		}

		/**
		 * Load Customize Preview JS.
		 *
		 * Used by hook: 'customize_preview_init'
		 * @see 'customize_preview_init'
		 */
		public static function action__customize_preview_init() {

			/*
			wp_enqueue_script(
				'wpglobus-customize-options-preview',
				WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-customize-options-preview' .
				WPGlobus::SCRIPT_SUFFIX() . '.js',
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);
			// */
			/*
			wp_localize_script(
				'wpglobus-customize-options-preview',
				'WPGlobusCustomize',
				array(
					'version'         => WPGLOBUS_VERSION,
					#'blogname'        => WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->language ),
					#'blogdescription' => WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language )
				)
			); // */

		}

		/**
		 * Load Customize Control JS.
		 */
		public static function action__customize_controls_enqueue_scripts() {

			/**
			 * Get customize_user_control options which there are specifically for every theme.
			 */
			$options = get_option( self::$options_key );
			if ( $options == '' ) {
				$options = null;
			} else {
				if ( empty( $options['customize_user_control'] ) ) {
					$options = null;
				} else {
					$options = $options['customize_user_control'];
				}
			}

			$i18n = array();
			$i18n['expandShrink'] = esc_html__('Expand/Shrink', 'wpglobus');
			
			wp_register_script(
				'wpglobus-customize-options',
				WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-customize-options' . WPGlobus::SCRIPT_SUFFIX() . '.js',
				array( 'jquery', 'jquery-ui-draggable' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_enqueue_script( 'wpglobus-customize-options' );
			wp_localize_script(
				'wpglobus-customize-options',
				'WPGlobusCustomizeOptions',
				array(
					'version'                => WPGLOBUS_VERSION,
					'i18n'					 => $i18n,
					'config'                 => WPGlobus::Config(),
					'ajaxurl'                => admin_url( 'admin-ajax.php' ),
					'process_ajax'           => __CLASS__ . '_process_ajax',
					'editLink'               => admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&action=edit&lang={{language}}"',
					'settings'               => self::$settings,
					'sections'               => self::$sections,
					'addonsPage'             => admin_url() . 'plugin-install.php?tab=search&s=WPGlobus&source=WPGlobus',
					'themeName'              => self::$theme_name,
					'themeEnabled'           => self::is_theme_enabled() ? 'true' : 'false',
					'helpButton'             => '<span style="float:right;cursor:pointer;" class="wpglobus-customize-icon-help customize-help-toggle dashicons dashicons-editor-help" tabindex="0" aria-expanded="false"></span>',
					'userControl'            => $options,
					'userControlSaveButton'  => self::$controls_save_button,
					'userControlIcon'        => WPGlobus::$PLUGIN_DIR_URL . 'includes/css/images/checkbox-icon.png',
					'userControlIconClass'   => 'wpglobus-customize-user-control-icon',
					'userControlBoxSelector' => '.wpglobus-fields_settings_control_box .items-box'
					# @see WPGlobusFieldsSettingsControl class
				)
			);

		}

		/**
		 * Get current theme or its property.
		 *
		 * @since 1.6.0
		 *
		 * @param string $param
		 *
		 * @return string|WP_Theme
		 */
		public static function get_theme( $param = '' ) {

			if ( 'name' === $param ) {
				return strtolower( self::$theme->name );
			}

			return self::$theme;

		}

		/**
		 * Check for enabled theme.
		 *
		 * @since 1.6.0
		 * @return boolean
		 */
		public static function is_theme_enabled() {

			if ( in_array( self::$theme_name, self::$disabled_themes ) ) {
				return false;
			}

			return true;
		}
		
				
		/**
		 * Filter to disable the making multilingual our own settings.
		 *
		 * @since 1.9.8
		 * @return array
		 */
		public static function filter__disabled_setting_mask($disabled_setting_mask) {
			$disabled_setting_mask[] = 'wpglobus_customize_js_editor';
			return $disabled_setting_mask;
		}

	} // class

endif;
# --- EOF
