<?php
/**
 * WPGlobus_Customize_Options
 * @package    WPGlobus
 * @subpackage WPGlobus/Admin
 * @since      1.4.6
 *
 * @see http://www.narga.net/comprehensive-guide-wordpress-theme-options-with-customization-api/
 * @see https://developer.wordpress.org/themes/advanced-topics/customizer-api/#top
 * @see https://codex.wordpress.org/Theme_Customization_API
 * @see #customize-controls
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
 * 		WPGlobus option								Customizer setting @see $wp_customize->add_setting
 *
 * 	wpglobus_option[last_tab]  					=> are not used in customizer
 *
 * 	wpglobus_option[enabled_languages]  		=> wpglobus_customize_enabled_languages
 *
 * 	wpglobus_option[more_languages]  			=> are not used in customizer
 *
 * 	wpglobus_option[show_flag_name]  			=> wpglobus_customize_language_selector_mode
 *
 * 	wpglobus_option[use_nav_menu]  				=> wpglobus_customize_language_selector_menu
 *
 * 	wpglobus_option[selector_wp_list_pages] 	
 *		=> Array
 *       (
 *           [show_selector] => 1				=> wpglobus_customize_selector_wp_list_pages
 *       )
 *		
 * 	wpglobus_option[css_editor]  				=> wpglobus_customize_css_editor
 *
 */ 
if ( ! class_exists( 'WPGlobus_Customize_Options' ) ) :


	if ( ! class_exists( 'WP_Customize_Control' ) ) {
		require_once( ABSPATH . WPINC . '/class-wp-customize-control.php' );
	}

	/**
	 * Adds textbox support to the theme customizer
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
			$this->content = empty( $args['content'] ) ? '' : $args['content'];
			$this->statuses = array( '' => __( 'Default', 'wpglobus' ) );
			parent::__construct( $manager, $id, $args );
		}
 
		public function render_content() {
			
			echo $this->content;			
			
		}
		
	}

	/**
	 * Adds checkbox with title support to the theme customizer
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusCheckBox extends WP_Customize_Control {
		
		public $type = 'wpglobus_checkbox';
		
		public $title = '';
		
		public function __construct( $manager, $id, $args = array() ) {

			$this->title = empty( $args[ 'title' ] ) ? '' : $args[ 'title' ];
		
			$this->statuses = array( '' => __( 'Default', 'wpglobus' ) );
			
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
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
			</label>	<?php
		
		}		
	}	

	/**
	 * Adds link support to the theme customizer
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusLink extends WP_Customize_Control {
		
		public $type = 'wpglobus_link';
		
		public $args = array();
		
		public function __construct( $manager, $id, $args = array() ) {

			$this->args = $args;
		
			$this->statuses = array( '' => __( 'Default', 'wpglobus' ) );
			
			parent::__construct( $manager, $id, $args );
			
		}
 
		public function render_content() {  

		?>
		
			<label>
				<?php if ( ! empty( $this->args[ 'title' ] ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->args[ 'title' ] ); ?></span>
				<?php endif; ?>
				<a href="<?php echo $this->args[ 'href' ]; ?>" target="_blank"><?php echo $this->args[ 'text' ]; ?></a>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
			</label>	<?php
		
		}		
	}	
	
	/**
	 * Adds CheckBoxSet support to the theme customizer
	 *
	 * @see wp-includes\class-wp-customize-control.php
	 */
	class WPGlobusCheckBoxSet extends WP_Customize_Control {
		
		public $type = 'checkbox_set';
		
		public $skeleton = '';
		
		public $args = array();
		
		public function __construct( $manager, $id, $args = array() ) {
			$this->args 	= $args;
			$this->statuses = array( '' => __( 'Default', 'wpglobus' ) );
			
			$this->skeleton = 
				'<a href="{{edit-link}}" target="_blank"><span style="cursor:pointer;">Edit</span></a>&nbsp;' .
				'<img style="cursor:move;" {{flag}} />&nbsp;' .
				'<input name="wpglobus_item_{{name}}" id="wpglobus_item_{{id}}" type="checkbox" checked="{{checked}}" ' . 
					' class="{{class}}" ' .
					' data-order="{{order}}" data-language="{{language}}" disabled="{{disabled}}" />' .
				'<span style="cursor:move;">{{item}}</span>';
			
			parent::__construct( $manager, $id, $args );
			
		}
 
		public function render_content() { 	?>
			
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif;
				
				$new_item = str_replace( '{{class}}', 'wpglobus-checkbox ' . $this->args[ 'checkbox_class' ], $this->skeleton );
				echo '<div style="display:none;" id="wpglobus-item-skeleton">' . $new_item . '</div>';

				echo '<ul id="wpglobus-sortable" style="margin-top:10px;margin-left:20px;">';
				
				foreach( $this->args[ 'items' ] as $order=>$item ) {
					
					$disabled = $order == 0 ? ' disabled="disabled" ' : '';
					
					$li_item = str_replace( 
						'{{flag}}', 	  
						'src="' . WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $item ] . '"', 
						$this->skeleton 
					);
					$li_item = str_replace( '{{name}}', 	  			$item, 				 $li_item );
					$li_item = str_replace( '{{id}}', 		  			$item, 				 $li_item );
					$li_item = str_replace( 'checked="{{checked}}"',  	'checked="checked"', $li_item );
					$li_item = str_replace( 'disabled="{{disabled}}"', 	$disabled, 			 $li_item );
					$li_item = str_replace( '{{class}}', 	  'wpglobus-checkbox ' . $this->args[ 'checkbox_class' ], $li_item );
					$li_item = str_replace( '{{item}}', 	  WPGlobus::Config()->en_language_name[ $item ] . ' (' . $item . ')', $li_item );
					$li_item = str_replace( '{{order}}', 	  $order, $li_item );
					$li_item = str_replace( '{{language}}',   $item,  $li_item );
					$li_item = str_replace( 
						'{{edit-link}}',  
						admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&action=edit&lang=' . $item . '"',  $li_item 
					);

					echo '<li>' . $li_item . '</li>';
					
				}	
				
				echo '</ul>'; ?>
				
			</label>	<?php
			
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
	
		public static function controller() {

			/**
			 * @see \WP_Customize_Manager::wp_loaded
			 * It calls the `customize_register` action first,
			 * and then - the `customize_preview_init` action
			 */
			add_action( 'customize_register', array(
				'WPGlobus_Customize_Options',
				'action__customize_register'
			) );
			
			add_action( 'customize_preview_init', array(
				'WPGlobus_Customize_Options',
				'action__customize_preview_init'
			) );
		
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
			
		}

		/**
		 * Ajax handler
		 */
		public static function action__process_ajax() {
			
			$result 	 = true;
			$ajax_return = array();
			
			$order = $_POST[ 'order' ];

			switch ( $order[ 'action' ] ) :
				case 'wpglobus_customize_save':
					/** @var array $options */
					$options = get_option( WPGlobus::Config()->option );
					foreach( $order[ 'options' ] as $key=>$value ) {
						
						if ( 'show_selector' === $key ) {
							$options[ 'selector_wp_list_pages' ][ $key ] = $value; 
						} else {
							$options[ $key ] = $value;
						}	
						
					}	
					//error_log( print_r( $options, true ) );
					update_option( WPGlobus::Config()->option, $options );
				break;
			endswitch;
			
			if ( false === $result ) {
				wp_send_json_error( $ajax_return );
			}

			wp_send_json_success( $ajax_return );			
	
		}

		/**
		 * Section for message about unsupported theme
		 *
		 * @param WP_Customize_Manager $wp_customize
		 * @param WP_Theme $theme
		 */
		public static function sorry_section( $wp_customize, $theme ) {
			
			/**
			 * Sorry section
			 */
			$wp_customize->add_section( 'wpglobus_sorry_section' , array(
				'title'      => __( 'WPGlobus', 'wpglobus' ),
				'priority'   => 0,
				'panel'		 => 'wpglobus_settings_panel'
			) );			
			
			$wp_customize->add_setting( 'sorry_message', array( 
				'type' => 'option',
				'capability' => 'manage_options',
				'transport' => 'postMessage'
			) );
			$wp_customize->add_control( new WPGlobusTextBox( $wp_customize, 
				'sorry_message', array(
					'section'   => 'wpglobus_sorry_section',
					'settings'  => 'sorry_message',
					'priority'  => 0,
					'content'	=> self::get_content( 'sorry_message', $theme )
					
				) 
			) );				
			
		}
		
		/**
		 * Callback for customize_register
		 * 
		 * @param WP_Customize_Manager $wp_customize
		 */
		public static function action__customize_register( WP_Customize_Manager $wp_customize ) {
			
			$theme 		= wp_get_theme();
			$theme_name = strtolower( $theme->__get( 'name' ) );
			
			$disabled_themes = array(
				'customizr'
			);
		
			/**
			 * WPGlobus panel
			 */
			$wp_customize->add_panel( 'wpglobus_settings_panel', array(
				'priority'       => 1010,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'WPGlobus Settings', 'wpglobus' ),
				'description'    => '<div style="background-color:#eee;padding:10px 5px;">' . 
										self::get_content( 'welcome_message' )  .  
									'</div>' . self::get_content( 'deactivate_message' ),
			) );
		
			if ( in_array( $theme_name, $disabled_themes ) ) {
				
				self::sorry_section( $wp_customize, $theme );
				
				return;
				
			}	
 
			/** wpglobus_customize_language_selector_mode <=> wpglobus_option[show_flag_name] */
			update_option( 'wpglobus_customize_language_selector_mode', WPGlobus::Config()->show_flag_name );
			
			/**  */
			update_option( 'wpglobus_customize_language_selector_menu', WPGlobus::Config()->nav_menu );
 
			/** wpglobus_customize_selector_wp_list_pages <=> wpglobus_option[selector_wp_list_pages][show_selector]  */
			update_option( 'wpglobus_customize_selector_wp_list_pages', WPGlobus::Config()->selector_wp_list_pages );
			
			/** wpglobus_customize_css_editor <=> wpglobus_option[css_editor]  */
			update_option( 'wpglobus_customize_css_editor', WPGlobus::Config()->css_editor );
		
			/**
			 * SECTION: Language
			 */
			if ( 1 ) {
				
				$wp_customize->add_section( 'wpglobus_languages_section' , array(
					'title'      => __( 'Languages', 'wpglobus' ),
					'priority'   => 10,
					'panel'		 => 'wpglobus_settings_panel'
				) );
				self::$sections[ 'wpglobus_languages_section' ] =  'wpglobus_languages_section' ;	
					
				/** Enabled languages */
				$wp_customize->add_setting( 'wpglobus_customize_enabled_languages', array( 
					'type' => 'option',
					'capability' => 'manage_options',
					'transport' => 'postMessage'
				) );			
				$wp_customize->add_control( new WPGlobusCheckBoxSet( $wp_customize, 
					'wpglobus_customize_enabled_languages', array(
						'section'   => 'wpglobus_languages_section',
						'settings'  => 'wpglobus_customize_enabled_languages',
						'priority'  => 0,
						'items'		=> WPGlobus::Config()->enabled_languages,
						'label'		=> __( 'Enabled Languages', 'wpglobus' ),
						'checkbox_class' => 'wpglobus-listen-change wpglobus-language-item',
						'description'    => __( 'These languages are currently enabled on your site.', 'wpglobus' )
						
					) 
				) );					
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_enabled_languages' ][ 'type' ] 	= 'checkbox_set';
				/** @see option wpglobus_option['enabled_languages'] */
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_enabled_languages' ][ 'option' ] 	= 'enabled_languages';
				
				/** Add languages */
				
				/** Generate array $more_languages */
				/** @var array $more_languages */
				$more_languages = array();
				$more_languages[ 'select' ] = '---- select ----';
				
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
					__( 'Choose a language you would like to enable. <br>Press the [Save & Publish] button to confirm.',
					'wpglobus' ) . '<br />';
				// translators: %1$s and %2$s - placeholders to insert HTML link around 'here'
				$desc_add_languages .= sprintf( 
					__( 'or Add new Language %1$s here %2$s', 'wpglobus' ),
					'<a style="text-decoration:underline;" href="' . admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&action=add" target="_blank">',
					'</a>' 
				);			
				
				$wp_customize->add_setting( 'wpglobus_customize_add_language', array( 
					'type' => 'option',
					'capability' => 'manage_options',
					'transport' => 'postMessage'
				) );			
				$wp_customize->add_control( 'wpglobus_add_languages_select_box', array(
					'settings' 		=> 'wpglobus_customize_add_language',
					'label'   		=> __( 'Add Languages', 'wpglobus' ),
					'section' 		=> 'wpglobus_languages_section',
					'type'    		=> 'select',
					'priority'  	=> 10,
					'choices'    	=> $more_languages,
					'description' 	=> $desc_add_languages
				));			
				//self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_add_language' ] = 'select';

				/** Language Selector Mode */
				$wp_customize->add_setting( 'wpglobus_customize_language_selector_mode', array( 
					'type' => 'option',
					'capability' => 'manage_options',
					'transport' => 'refresh'
					#'transport' => 'postMessage'
				) );			
				$wp_customize->add_control( 'wpglobus_customize_language_selector_mode', array(
					'settings' 		=> 'wpglobus_customize_language_selector_mode',
					'label'   		=> __( 'Language Selector Mode', 'wpglobus' ),
					'section' 		=> 'wpglobus_languages_section',
					'type'    		=> 'select',
					'priority'  	=> 20,
					'choices'    	=> array(
						'code'      => __( 'Two-letter Code with flag (en, ru, it, etc.)', 'wpglobus' ),
						'full_name' => __( 'Full Name (English, Russian, Italian, etc.)', 'wpglobus' ),
						/* @since 1.2.1 */
						'name'      => __( 'Full Name with flag (English, Russian, Italian, etc.)', 'wpglobus' ),
						'empty'     => __( 'Flags only', 'wpglobus' )
					),
					'description' 	=> __( 'Choose the way language name and country flag are shown in the drop-down menu', 'wpglobus' )
				));				
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_language_selector_mode' ][ 'type' ]  = 'select';
				/** @see option wpglobus_option['show_flag_name'] */
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_language_selector_mode' ][ 'option'] = 'show_flag_name';
 
				/**
				 * @see https://make.wordpress.org/core/2016/03/22/implementing-selective-refresh-support-for-widgets/
				 * @see https://make.wordpress.org/core/2016/03/10/customizer-improvements-in-4-5/
				 *
				$wp_customize->selective_refresh->add_partial( 'wpglobus_customize_language_selector_mode', array(
					'selector' => '#site-navigation',
					'render_callback' => function() {
						wp_nav_menu();
					},
				) ); 
				// */
			
				/** Language Selector Menu */
				
				/** @var array $nav_menus */
				$nav_menus = WPGlobus::_get_nav_menus();

				foreach ( $nav_menus as $menu ) {
					$menus[ $menu->slug ] = $menu->name;
				}
				if ( ! empty( $nav_menus ) && count( $nav_menus ) > 1 ) {
					$menus[ 'all' ] = 'All';
				}			
				
				$wp_customize->add_setting( 'wpglobus_customize_language_selector_menu', array( 
					'type' => 'option',
					'capability' => 'manage_options',
					'transport' => 'postMessage'
				) );			
				$wp_customize->add_control( 'wpglobus_customize_language_selector_menu', array(
					'settings' 		=> 'wpglobus_customize_language_selector_menu',
					'label'   		=> __( 'Language Selector Menu', 'wpglobus' ),
					'section' 		=> 'wpglobus_languages_section',
					'type'    		=> 'select',
					'priority'  	=> 30,
					'choices'    	=> $menus,
					'description' 	=> __( 'Choose the navigation menu where the language selector will be shown', 'wpglobus' ),
				));	
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_language_selector_menu' ][ 'type' ] 	= 'select';
				/** @see option wpglobus_option['use_nav_menu'] */
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_language_selector_menu' ][ 'option' ] 	= 'use_nav_menu';
		
				/** "All Pages" menus Language selector */
				$wp_customize->add_setting( 'wpglobus_customize_selector_wp_list_pages', array( 
					'type' => 'option',
					'capability' => 'manage_options',
					'transport' => 'postMessage'
				) );
				$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize, 			
					'wpglobus_customize_selector_wp_list_pages', array(
						'settings' 		=> 'wpglobus_customize_selector_wp_list_pages',
						'title'   		=> __( '"All Pages" menus Language selector', 'wpglobus' ),
						'section' 		=> 'wpglobus_languages_section',
						'priority'  	=> 40,
						'label'		 	=> __( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
					)	
				) );	
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_selector_wp_list_pages' ][ 'type' ] 	= 'wpglobus_checkbox';
				/** @see option wpglobus_option['selector_wp_list_pages']['show_selector'] */
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_selector_wp_list_pages' ][ 'option' ]  = 'show_selector';
				
				/** Custom CSS */
				$wp_customize->add_setting( 'wpglobus_customize_css_editor', array( 
					'type' => 'option',
					'capability' => 'manage_options',
					'transport' => 'postMessage'
				) );			
				$wp_customize->add_control( 'wpglobus_customize_css_editor', array(
					'settings' 		=> 'wpglobus_customize_css_editor',
					'label'   		=> __( 'Custom CSS', 'wpglobus' ),
					'section' 		=> 'wpglobus_languages_section',
					'type'    		=> 'textarea',
					'priority'  	=> 50,
					'description' 	=> __( 'Here you can enter the CSS rules to adjust the language selector menu for your theme. Look at the examples in the `style-samples.css` file.', 'wpglobus' ),
				));
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_css_editor' ][ 'type' ]  	= 'textarea';
				/** @see option wpglobus_option['css_editor'] */
				self::$settings[ 'wpglobus_languages_section' ][ 'wpglobus_customize_css_editor' ][ 'option' ]  = 'css_editor';
				
			}	/** end SECTION: Language */
			
			/**
			 * SECTION: Post types
			 */
			if ( 1 ) {
			
				$section = 'wpglobus_post_types_section';
			
				$wp_customize->add_section( $section , array(
					'title'      => __( 'Post types', 'wpglobus' ),
					'priority'   => 40,
					'panel'		 => 'wpglobus_settings_panel'
				) );
				self::$sections[ $section ] =  $section ;	
				
				if ( false === ( $enabled_post_types = get_transient( self::$enabled_post_types_key ) ) ) {
					
					$post_types = get_post_types();
					
					$enabled_post_types = array();
					foreach ( $post_types as $post_type ) {
						if ( ! in_array( $post_type, array( 'attachment', 'revision', 'nav_menu_item' ), true ) ) {	
			
							if ( in_array( $post_type, array( 'post', 'page' ) ) ) {
								$enabled_post_types[ $post_type ] = $post_type;
								continue;	
							}				
						
							foreach( WPGlobus::O()->vendors_scripts as $script=>$status ) {
								
								if ( empty( $status ) ) {
									continue;
								}
								
								if ( $script == 'ACF' || $script == 'ACFPRO' ) {
									/**
									 * get list @see class-wpglobus.php:145
									 */
									if ( in_array( $post_type, array( 'acf-field-group', 'acf-field', 'acf' ) ) ) {
										continue 2;
									}		
								}	
								
								if ( $script == 'WOOCOMMERCE'  ) {
									/**
									 * get list  @see class-wpglobus.php:171
									 */
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
												'shop_webhook' ) 
										 ) ) {
										continue 2;
									}										
								}
						
								if ( $script == 'WPCF7' ) {
									/**
									 * get list @see class-wpglobus.php:195
									 */
									if ( in_array( $post_type, array( 'wpcf7_contact_form' ) ) ) {
										continue 2;
									}		
								}							
								
							}	

							$enabled_post_types[ $post_type ] = $post_type;

						}
					}
					
					set_transient( self::$enabled_post_types_key, $enabled_post_types, 60 );
					
				}	

				foreach( $enabled_post_types as $post_type ) :
					
					$status = '';
					
					if ( isset( WPGlobus::Config()->extended_options[ 'post_type' ][ $post_type ] ) ) {	
					
						if ( WPGlobus::Config()->extended_options[ 'post_type' ][ $post_type ] == 1 ) {
							$status = '1';
						}	
					
					} else {
						$status = '1';
					}	
					
					update_option( 'wpglobus_customize_post_type_' . $post_type, $status );
				
				endforeach;
				
				$i = 0; 
				foreach( $enabled_post_types as $post_type ) :
				
					$pst = 'wpglobus_customize_post_type_' . $post_type; 
				
					$wp_customize->add_setting( $pst, array( 
						'type' => 'option',
						'capability' => 'manage_options',
						'transport' => 'postMessage'
					) );			

					$title = '';	
					if ( $i == 0 ) {
						$title = __( 'Uncheck to disable WPGlobus', 'wpglobus' );
					}
					
					$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize, 			
						$pst, array(
							'settings' 		=> $pst,
							'title'   		=> $title,
							'label'   		=> $post_type,
							'section' 		=> $section,
							#'default'		=> '1',
							'priority'  	=> 10,
						)	
					) );	

					$i++;
					self::$settings[ $section ][ $pst ][ 'type' ] = 'wpglobus_checkbox';
					/** @see option wpglobus_option['post_type'] */
					self::$settings[ $section ][ $pst ][ 'option' ] = 'post_type';
					
				endforeach;
			
			}; /** end SECTION: Post types */
			
			/**
			 * SECTION: Add ons
			 */
			if ( 1 ) {

				global $wp_version; 

				self::$sections[ 'wpglobus_addons_section' ] = 'wpglobus_addons_section';
				
				if ( version_compare( $wp_version, '4.5-RC1', '<' ) ) :
					
					$wp_customize->add_section( self::$sections[ 'wpglobus_addons_section' ] , array(
						'title'      => __( 'Add-ons', 'wpglobus' ),
						'priority'   => 40,
						'panel'		 => 'wpglobus_settings_panel'
					) );			
			
					/** Add ons setting  */
					$wp_customize->add_setting( 'wpglobus_customize_add_ons', array( 
						'type' => 'option',
						'capability' => 'manage_options',
						'transport' => 'postMessage'
					) );			
					
					$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize, 			
						'wpglobus_customize_add_ons', array(
							'settings' 		=> 'wpglobus_customize_add_ons',
							'title'   		=> __( 'Title', 'wpglobus' ),
							'label'   		=> __( 'Label', 'wpglobus' ),
							'section' 		=> self::$sections[ 'wpglobus_addons_section' ],
							'type'    		=> 'checkbox',
							'priority'  	=> 10,
							'description' 	=> __( 'Description', 'wpglobus' ),
						)	
					) );
					
				else:
					/**
					 * @since WP 4.5
					 * @see https://make.wordpress.org/core/2016/03/10/customizer-improvements-in-4-5/
					 */
					$wp_customize->add_section( self::$sections[ 'wpglobus_addons_section' ] , array(
						'title'      => __( 'Add-ons', 'wpglobus' ),
						'priority'   => 40,
						'panel'		 => 'wpglobus_settings_panel'
					) );			
				
					$wp_customize->add_control(	'wpglobus_customize_add_ons', array(
							'section' 		=> self::$sections[ 'wpglobus_addons_section' ],
							'settings' 		=> array(),
							'type'    		=> 'button'
						)	
					);
				
				endif;	
			
			}; 		/** end SECTION: Add ons */
		
			/**
			 * Fires to add customize settings.
			 *
			 * @since 1.4.6
			 *
			 * @param WP_Customize_Manager $wp_customize.
			 */
			do_action( 'wpglobus_customize_register', $wp_customize );
			
			/** @var array $res */
			$res = apply_filters( 'wpglobus_customize_data', array( 'sections' => self::$sections, 'settings' => self::$settings ) );
			
			self::$sections = $res[ 'sections' ];
			self::$settings = $res[ 'settings' ];
			
		}
		
		/**
		 * Get content for WPGlobusTextBox element
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
				case 'welcome_message' :
				
					$content = '<div class="" style="width:100%;">' . 
									__( 'Thank you for installing WPGlobus!', 'wpglobus' ) .
									'<br/>' .
										'&bull; ' .
										'<a style="text-decoration:underline;" target="_blank" href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_ABOUT . '">' .
										__( 'Read About WPGlobus', 'wpglobus' ) .
										'</a>' .
										'<br/>' .
										'&bull; ' . __( 'Click the <strong>[Languages]</strong> tab at the left to setup the options.', 'wpglobus' ) .
										#'<br/>' .
										#'&bull; ' . __( 'Use the <strong>[Languages Table]</strong> section to add a new language or to edit the language attributes: name, code, flag icon, etc.', 'wpglobus' ) .
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
					            /* translators: %?$s: HTML codes for hyperlink. Do not remove. */
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
				            /* translators: %s: link to the Clean-up Tool */
				            sprintf( __( 'If there are just a few places, you should edit them manually. To automatically remove all translations at once, you can use the %s. WARNING: The clean-up operation is irreversible, so use it only if you need to completely uninstall WPGlobus.', 'wpglobus' ),
					            /* translators: %?$s: HTML codes for hyperlink. Do not remove. */
					            sprintf( __( '%1$sClean-up Tool%2$s', 'wpglobus' ),
						            '<a style="text-decoration:underline;" target="_blank" href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_CLEAN . '">',
						            '</a>'
					            ) ) .
				            '</p>';	
							
					break;
				case 'sorry_message' :
				
					$content = '<p><strong>' .
									/* translators: %s: name of current theme */
									sprintf( __( 'Sorry, WPGlobus customizer doesn\'t support current theme %s.', 'wpglobus' ),
										'<em>' . $attrs->__get( 'name' ) . '</em>'
									) .	
									'<br />' . 
									/* translators: %?$s: HTML codes for hyperlink. Do not remove. */
									sprintf( __( 'Please use %1$sWPGlobus options page%2$s instead.', 'wpglobus' ),
										'<a style="text-decoration:underline;" target="_blank" href="' . admin_url() . 'admin.php?page=' . WPGlobus::OPTIONS_PAGE_SLUG . '&tab=0">',
										'</a>'
									) .
								'</strong></p>';	
				
					break;
			endswitch;				
			
			return $content;
			
		}	
		
		/**
		 * Load Customize Preview JS
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
		 * Load Customize Control JS
		 */
		public static function action__customize_controls_enqueue_scripts() {

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
					'version' 		=> WPGLOBUS_VERSION,
					'config'  		=> WPGlobus::Config(),
					'ajaxurl'      	=> admin_url( 'admin-ajax.php' ),
					'process_ajax' 	=> __CLASS__ . '_process_ajax',
					'editLink'		=> admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&action=edit&lang={{language}}"',
					'settings'		=> self::$settings,
					'sections'		=> self::$sections,
					'addonsPage'	=> admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_ADDONS
				)
			);
			
		}

	} // class

endif;
# --- EOF
