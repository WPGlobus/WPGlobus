<?php
/**
 * @package WPGlobus
 * @subpackage WPGlobus/Admin
 * @since 1.2.1
 */
	
add_action( 'customize_preview_init', 'wpglobus_customize_preview_init' ); 			
function wpglobus_customize_preview_init() {
	wp_enqueue_script( 
		'wpglobus-customize-preview',
		WPGlobus::$PLUGIN_DIR_URL . '/includes/js/wpglobus-customize-preview.js',
		array( 'jquery', 'customize-preview' ),
		WPGLOBUS_VERSION,
		true
	);
	wp_localize_script(
		'wpglobus-customize-preview',
		'WPGlobusCustomize',
		array(
			'version'   => WPGLOBUS_VERSION,
			'blogname' 	=> WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->language ),
			'blogdescription' 	=> WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language )
		)
	);		
}	 
 
/**
 * Class WPGlobus_Customize
 */
class WPGlobus_Customize {
	
	
	public static function init() {
		add_action( 'customize_register', array( 'WPGlobus_Customize', 'register_customize_sections' ) );	
		//add_action( 'customize_preview_init',  	array( 'WPGlobus_Customize', 'customize_preview_init' ) ); 		
		add_action( 'customize_controls_enqueue_scripts', array( 'WPGlobus_Customize', 'controls_enqueue_scripts' ), 1000 ); 
	}

	/**
	 * @param WP_Customize_Manager $wp_customize
	 */
	public static function register_customize_sections( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting( 'wpglobus_blogname', array(
			'default' => WPGlobus_Core::text_filter( get_bloginfo('name'), WPGlobus::Config()->language )
		) );		
		
		$wp_customize->add_setting( 'wpglobus_blogdescription', array(
			'default' 	=> WPGlobus_Core::text_filter( get_bloginfo('description'), WPGlobus::Config()->language )
		) );			
		
		$wp_customize->get_setting( 'wpglobus_blogname' )->transport = 'postMessage';
		
		$wp_customize->get_setting( 'wpglobus_blogdescription' )->transport = 'postMessage';	
		
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'wpglobus_blogname', array(
				'label'     => __( 'Site Title' ),
				'type'      => 'text',
				'section'   => 'title_tagline',
				'settings'  => 'wpglobus_blogname',
				'values'    => WPGlobus_Core::text_filter( get_bloginfo('name'), WPGlobus::Config()->language )
			)
		) );		

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'wpglobus_blogdescription', array(
				'label'     => __( 'Tagline' ),
				'type'      => 'text',
				'section'   => 'title_tagline',
				'settings'  => 'wpglobus_blogdescription'
			)
		) );

	}	
	 
	/**
	 * Used by hook: 'customize_preview_init'
	 * 
	 * @see 'customize_preview_init'
	 */

	public static function customize_preview_init() {
		wp_enqueue_script( 
			'wpglobus-customize-preview',
			WPGlobus::$PLUGIN_DIR_URL . '/includes/js/wpglobus-customize-preview.js',
			array( 'jquery', 'customize-preview' ),
			WPGLOBUS_VERSION,
			true
		);
		wp_localize_script(
			'wpglobus-customize-preview',
			'WPGlobusCustomize',
			array(
				'version'   => WPGLOBUS_VERSION,
				'blogname' 	=> WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->language ),
				'blogdescription' 	=> WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language )
			)
		);		
	}	

	public static function controls_enqueue_scripts() {
		wp_enqueue_script( 
			'wpglobus-customize-control',
			WPGlobus::$PLUGIN_DIR_URL . '/includes/js/wpglobus-customize-control.js',
			array( 'jquery' ),
			WPGLOBUS_VERSION,
			true
		);
	}
 	
}	