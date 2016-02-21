<?php
/**
 * WPGlobus_Customize_Options
 * @package    WPGlobus
 * @subpackage WPGlobus/Admin
 * @since      1.4.5
 *
 * @see http://www.narga.net/comprehensive-guide-wordpress-theme-options-with-customization-api/
 * @see https://developer.wordpress.org/themes/advanced-topics/customizer-api/#top
 * @see https://codex.wordpress.org/Theme_Customization_API
 * @see #customize-controls
 */

 
 /*
  a:9:{s:8:"last_tab";s:1:"3";s:17:"enabled_languages";a:4:{s:2:"en";s:1:"1";s:2:"ru";s:1:"1";s:2:"es";s:8:"Español";s:2:"de";s:7:"Deutsch";}s:14:"more_languages";s:0:"";s:14:"show_flag_name";s:4:"name";s:12:"use_nav_menu";s:3:"all";s:22:"selector_wp_list_pages";a:1:{s:13:"show_selector";s:1:"1";}s:19:"switcher_menu_style";s:0:"";s:10:"css_editor";s:36:"                                    ";s:9:"post_type";a:7:{s:4:"post";s:1:"1";s:4:"page";s:1:"1";s:10:"qsot-event";s:1:"1";s:10:"qsot-venue";s:1:"1";s:15:"qsot-event-area";s:1:"1";s:11:"testimonial";s:1:"1";s:19:"testimonial_rotator";s:1:"1";}}
  */
 // wpglobus_option
 // wpglobus_option_flags
 // wpglobus_option_locale
 // wpglobus_option_en_language_names
 // wpglobus_option_language_names
 // wpglobus_option_post_meta_settings
 
 
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

		public static $sections = array();
		
		public static $settings = array();
	
		public static function controller() {
			
			self::$sections[ 'addons' ] = 'wpglobus_addons_section';
			
			self::$settings[ 'addons' ] = 'wpglobus_customize_add_ons';
			
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

			//add_action ( 'customize_controls_print_footer_scripts'  , array( 'WPGlobus_Customize_Options', 'tc_print_js_templates1' ) );
			
			/**
			 * This is called by wp-admin/customize.php
			 */
			 
			//* 
			add_action( 'customize_controls_enqueue_scripts', array(
				'WPGlobus_Customize_Options',
				'action__customize_controls_enqueue_scripts'
			), 1010 );
			// */
 		
			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array(
				'WPGlobus_Customize_Options',
				'action__process_ajax'
			) );
			
		}

		/**
		 * @todo
		 * 
		 */
		public static function action__process_ajax() {
			
			$result 	 = true;
			$ajax_return = array();
			
			$order = $_POST[ 'order' ];

			//error_log( print_r( $order, true ) );

			$opts = get_option( 'wpglobus_option' );
			//error_log( print_r( $opts, true ) );			


			switch ( $order[ 'action' ] ) :
				case 'clean':
				
				break;
				case 'wpglobus-reset':			
				break;
			endswitch;
			
			if ( false === $result ) {
				wp_send_json_error( $ajax_return );
			}

			wp_send_json_success( $ajax_return );			
	
		}	

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
		 * @todo
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
			 * Welcome section
			 */
			/* 
			$wp_customize->add_section( 'wpglobus_welcome_section' , array(
				'title'      => __( 'WPGlobus Welcome', 'wpglobus' ),
				'priority'   => 0,
				'panel'		 => 'wpglobus_settings_panel'
			) );			
			
			$wp_customize->add_setting( 'welcome_message' );
			//$wp_customize->get_setting( 'welcome_message' )->transport = 'postMessage'; 
			$wp_customize->add_control( new WPGlobusTextBox( $wp_customize, 
				'welcome_message', array(
					'section'   => 'wpglobus_welcome_section',
					'settings'  => 'welcome_message',
					'priority'  => 0,
					'content'	=> self::get_content( 'welcome_message' )
					
				) 
			) );				
			// */
			/** end section */
			
			/**
			 * SECTION: Language
			 */		
			$wp_customize->add_section( 'wpglobus_languages_section' , array(
				'title'      => __( 'Languages', 'wpglobus' ),
				'priority'   => 10,
				'panel'		 => 'wpglobus_settings_panel'
			) );
				
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


			/** Language Selector Mode */
			$wp_customize->add_setting( 'wpglobus_customize_language_selector_mode', array( 
				'type' => 'option',
				'capability' => 'manage_options',
				#'transport' => 'postMessage'
				'transport' => 'refresh'
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
					#'label'   		=> __( '', 'wpglobus' ),
					'section' 		=> 'wpglobus_languages_section',
					#'type'    		=> 'checkbox',
					'priority'  	=> 40,
					'label'		 	=> __( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
					#'description' 	=> __( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
				)	
			) );	
						
			/*
			$wp_customize->add_control( 'wpglobus_customize_selector_wp_list_pages', array(
				'settings' 		=> 'wpglobus_customize_selector_wp_list_pages',
				'label'   		=> __( '"All Pages" menus Language selector', 'wpglobus' ),
				'section' 		=> 'wpglobus_languages_section',
				'type'    		=> 'checkbox',
				'priority'  	=> 40,
				'default'    	=> 1,
				'description' 	=> __( 'Adds language selector to the menus that automatically list all existing pages (using `wp_list_pages`)', 'wpglobus' ),
			));			// */	
			
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
			/** end section */

			/**
			 * Languages table section
			 */
			/* 
			$wp_customize->add_section( 'wpglobus_languages_table_section' , array(
				'title'      => __( 'Languages table', 'wpglobus' ),
				'priority'   => 30,
				'panel'		 => 'wpglobus_settings_panel'
			) );
			// */
			/**
			 *
			 */
			/* 
			$wp_customize->add_setting( 'wpglobus_customize_post_type1', array( 
				'type' => 'option',
				'capability' => 'manage_options',
				'transport' => 'postMessage'
			) );	
			$wp_customize->add_control( 'wpglobus_customize_post_type1', array(
				'settings' 		=> 'wpglobus_customize_post_type1',
				'label'   		=> __( 'Language Selector Menu 111', 'wpglobus' ),
				'section' 		=> 'wpglobus_languages_table_section',
				'type'    		=> 'select',
				'priority'  	=> 10,
				'choices'    	=> array( '1111'=>'11111', '22222'=>'22222' ),
				'description' 	=> __( 'Choose the navigation menu where the language selector will be shown', 'wpglobus' ),
			));	
			// */
			/** end SECTION: Language */
			
			/**
			 * SECTION: Post types
			 */	
			$wp_customize->add_section( 'wpglobus_post_types_section' , array(
				'title'      => __( 'Post types', 'wpglobus' ),
				'priority'   => 40,
				'panel'		 => 'wpglobus_settings_panel'
			) );
			
			if ( false === ( $enabled_post_types = get_transient( 'wpglobus_customize_enabled_post_types' ) ) ) {
				
				$post_types = get_post_types();
				
				$enabled_post_types = array();
				foreach ( $post_types as $post_type ) {
					if ( ! in_array( $post_type, array( 'attachment', 'revision', 'nav_menu_item' ), true ) ) {	
						if ( ! in_array( $post_type, WPGlobus::Config()->disabled_entities, true ) ) {	
							$enabled_post_types[ $post_type ] = $post_type;
						}
					}
				}
				
				set_transient( 'wpglobus_customize_enabled_post_types', $enabled_post_types, 30 );
				
			}	
			
			/**
			 *
			 */
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
						'section' 		=> 'wpglobus_post_types_section',
						'default'		=> '1',
						'priority'  	=> 10,
					)	
				) );	

				$i++;
				
			endforeach;
			/** end SECTION: Post types */
			
			/**
			 * SECTION: Add ons
			 */		
			$wp_customize->add_section( 'wpglobus_addons_section' , array(
				'title'      => __( 'Add-ons', 'wpglobus' ),
				'priority'   => 40,
				'panel'		 => 'wpglobus_settings_panel'
			) );			
		
			/** Add ons setting  */
			$wp_customize->add_setting( self::$settings[ 'addons' ], array( 
				'type' => 'option',
				'capability' => 'manage_options',
				'transport' => 'postMessage'
			) );			
			
			$wp_customize->add_control( new WPGlobusCheckBox( $wp_customize, 			
				self::$settings[ 'addons' ], array(
					'settings' 		=> self::$settings[ 'addons' ],
					'title'   		=> __( 'Title', 'wpglobus' ),
					'label'   		=> __( 'Label', 'wpglobus' ),
					'section' 		=> 'wpglobus_addons_section',
					'type'    		=> 'checkbox',
					'priority'  	=> 10,
					'description' 	=> __( 'Description', 'wpglobus' ),
				)	
			) );	
			/** end SECTION: Add ons */
			
			/**
			 * Deactivating section
			 */
			/* 
			$wp_customize->add_section( 'wpglobus_deactivate_section' , array(
				'title'      => __( 'WPGlobus deactivating', 'wpglobus' ),
				'priority'   => 1000,
				'panel'		 => 'wpglobus_settings_panel'
			) );					
			$wp_customize->add_setting( 'deactivate_message' );
			//$wp_customize->get_setting( 'deactivate_message' )->transport = 'postMessage'; 
			$wp_customize->add_control( new WPGlobusTextBox( $wp_customize, 
				'deactivate_message', array(
					'section'   => 'wpglobus_deactivate_section',
					'settings'  => 'deactivate_message',
					'priority'  => 0,
					'content'	=> self::get_content( 'deactivate_message' )
				) 
			) );
			/** end section */
			// */
			
			/**
			 * Fires to add customize settings.
			 *
			 * @since 1.4.5
			 *
			 * @param WP_Customize_Manager $wp_customize.
			 */
			do_action( 'wpglobus_customize_register', $wp_customize );
			
		}
		
		/**
		 * Get content for WPGlobusTextBox element
		 * @param 
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
										'<a target="_blank" href="' . admin_url() . 'admin.php?page=' . WPGlobus::PAGE_WPGLOBUS_ABOUT . '">' .
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
