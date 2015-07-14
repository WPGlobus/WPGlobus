<?php
/**
 * Multilingual Customizer
 * @package    WPGlobus
 * @subpackage WPGlobus/Admin
 * @since      1.2.1
 */

/**
 * Class WPGlobus_Customize
 */
class WPGlobus_Customize {

	public static function controller() {
		/**
		 * @see \WP_Customize_Manager::wp_loaded
		 * It calls the `customize_register` action first,
		 * and then - the `customize_preview_init` action
		 */
		add_action( 'customize_register', array(
			'WPGlobus_Customize',
			'action__customize_register'
		) );
		add_action( 'customize_preview_init', array(
			'WPGlobus_Customize',
			'action__customize_preview_init'
		) );

		/**
		 * This is called by wp-admin/customize.php
		 */
		add_action( 'customize_controls_enqueue_scripts', array(
			'WPGlobus_Customize',
			'action__customize_controls_enqueue_scripts'
		), 1000 );
	}

	/**
	 * Add multilingual controls.
	 * The original controls will be hidden.
	 * @param WP_Customize_Manager $wp_customize
	 */
	public static function action__customize_register( WP_Customize_Manager $wp_customize ) {

		/**
		 * Blog Name
		 */
		$wp_customize->add_setting( 'wpglobus_blogname', array(
			'default' => WPGlobus_Core::text_filter( get_bloginfo( 'name' ), WPGlobus::Config()->language )
		) );
		$wp_customize->get_setting( 'wpglobus_blogname' )->transport = 'postMessage';
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'wpglobus_blogname', array(
				'label'    => __( 'Site Title' ),
				'type'     => 'text',
				'section'  => 'title_tagline',
				'settings' => 'wpglobus_blogname',
				'values'   => WPGlobus_Core::text_filter( get_bloginfo( 'name' ), WPGlobus::Config()->language )
			)
		) );

		/**
		 * Blog Description
		 */
		$wp_customize->add_setting( 'wpglobus_blogdescription', array(
			'default' => WPGlobus_Core::text_filter( get_bloginfo( 'description' ), WPGlobus::Config()->language )
		) );
		$wp_customize->get_setting( 'wpglobus_blogdescription' )->transport = 'postMessage';
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'wpglobus_blogdescription', array(
				'label'    => __( 'Tagline' ),
				'type'     => 'text',
				'section'  => 'title_tagline',
				'settings' => 'wpglobus_blogdescription'
			)
		) );

	}

	/**
	 * Load Customize Preview JS
	 * Used by hook: 'customize_preview_init'
	 * @see 'customize_preview_init'
	 */
	public static function action__customize_preview_init() {
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
				'version'         => WPGLOBUS_VERSION,
				'blogname'        => WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->language ),
				'blogdescription' => WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language )
			)
		);
	}

	/**
	 * Load Customize Control JS
	 */
	public static function action__customize_controls_enqueue_scripts() {
		wp_enqueue_script(
			'wpglobus-customize-control',
			WPGlobus::$PLUGIN_DIR_URL . '/includes/js/wpglobus-customize-control.js',
			array( 'jquery' ),
			WPGLOBUS_VERSION,
			true
		);
	}

} // class

# --- EOF