<?php
/**
 * File: addon-yoast_seo.php
 * 
 * @since 2.8.4
 *
 * @package WPGlobus\Builders\Yoast_SEO
 * @author  Alex Gor(alexgff)
 */

if ( defined( 'WPSEO_VERSION' ) && version_compare( WPSEO_VERSION, '17.3', '>=' ) ) {

	$_file = 'wordpress-seo/wp-seo.php';
	$_plugin_name = 'Yoast SEO';
	if ( defined( 'WPGLOBUS_YOAST_PLUGIN_FILE' ) ) {
		$_file = WPGLOBUS_YOAST_PLUGIN_FILE;
		$_plugin_name = 'Yoast SEO('.$_file.')';
	}
	
	$_builder_label = 'Yoast SEO';
	if ( defined( 'WPSEO_PREMIUM_VERSION' ) ) {
		$_builder_label = 'Yoast SEO Premium';
	}		

	self::$add_on['yoast_seo'] = array(
		'id'                    => 'yoast_seo',
		'role'                  => 'builder',
		'admin_bar_label'       => 'Add-on',
		'supported_min_version' => '7.7',
		'const'                 => 'WPSEO_VERSION',
		'plugin_name'           => $_plugin_name,
		'plugin_uri'            => 'https://wordpress.org/plugins/wordpress-seo/',
		'path'                  => $_file,
		'stage'                 => 'production',
		'pro'					=> false,
		'admin_bar_builder_label' => $_builder_label,
	);

	self::$add_on['yoast_seo_premium'] = array(
		'id'                    => 'yoast_seo',
		'role'                  => 'builder',
		'admin_bar_label'       => 'Add-on',
		'supported_min_version' => '7.7',
		'const'                 => 'WPSEO_PREMIUM_VERSION',
		'plugin_name'           => 'Yoast SEO Premium',
		'plugin_uri'            => 'https://yoast.com/wordpress/plugins/seo/',
		'path'                  => 'wordpress-seo-premium/wp-seo-premium.php',
		'stage'                 => 'production',
		'pro'					=> true,
		'admin_bar_builder_label' => 'Yoast SEO Premium',
	);

} else {
 
	if ( file_exists( WP_PLUGIN_DIR . '/wordpress-seo-premium/wp-seo-premium.php' ) ) {

		self::$add_on['yoast_seo'] = array(
			'id'                    => 'yoast_seo',
			'role'                  => 'builder',
			'admin_bar_label'       => 'Add-on',
			'supported_min_version' => '7.7',
			'const'                 => 'WPSEO_VERSION',
			'plugin_name'           => 'Yoast SEO Premium',
			'plugin_uri'            => 'https://yoast.com/wordpress/plugins/seo/',
			'path'                  => 'wordpress-seo-premium/wp-seo-premium.php',
			'stage'                 => 'production',
		);

	}

	/**
	 * @since 2.3.11
	 */
	$_file = 'wordpress-seo/wp-seo.php';
	$_plugin_name = 'Yoast SEO';
	if ( defined( 'WPGLOBUS_YOAST_PLUGIN_FILE' ) ) {
		$_file = WPGLOBUS_YOAST_PLUGIN_FILE;
		$_plugin_name = 'Yoast SEO('.$_file.')';
	}

	if ( file_exists( WP_PLUGIN_DIR . '/' . $_file ) ) {

		if ( ! defined( 'WPSEO_PREMIUM_PLUGIN_FILE' ) ) {

			self::$add_on['yoast_seo'] = array(
				'id'                    => 'yoast_seo',
				'role'                  => 'builder',
				'admin_bar_label'       => 'Add-on',
				'supported_min_version' => '7.7',
				'const'                 => 'WPSEO_VERSION',
				'plugin_name'           => $_plugin_name,
				'plugin_uri'            => 'https://wordpress.org/plugins/wordpress-seo/',
				'path'                  => $_file,
				'stage'                 => 'production',
			);

		}
	}
	
}
 
# --- EOF