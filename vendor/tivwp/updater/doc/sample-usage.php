<?php
/**
 * File: sample-usage.php
 *
 * @package TIVWP_Updater
 */

/**
 * Load.
 */
if (
	version_compare( PHP_VERSION, '5.3.0', '>=' )
	&& file_exists( dirname( __FILE__ ) . '/vendor/tivwp/updater/updater.php' )
) {
	// No warning about missing file. This is a sample.
	/** @noinspection PhpIncludeInspection */
	require_once dirname( __FILE__ ) . '/vendor/tivwp/updater/updater.php';
}

/**
 * Setup.
 */
function tivwp_updater_sample_plugin__setup_updater() {
	// No warning about possible multiple files having this library.
	/** @noinspection PhpUndefinedClassInspection */
	new TIVWP_Updater( array(
		'plugin_file' => __FILE__,
		'product_id'  => 'My Plugin',
		'url_product' => 'http://www.example.com/product/my-plugin/',
	) );
}

add_action( 'tivwp_updater_factory', 'tivwp_updater_sample_plugin__setup_updater' );

/* EOF */
