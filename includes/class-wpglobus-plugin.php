<?php
/**
 * File: class-wpglobus-plugin.php
 *
 * @package WPGlobus
 * @since   1.6.1
 */

/**
 * Class WPGlobus_Plugin
 */
abstract class WPGlobus_Plugin {

	/**
	 * `__FILE__` from the loader.
	 *
	 * @var string
	 */
	public $plugin_file = '';

	/**
	 * Basename from `__FILE__`.
	 *
	 * @var string
	 */
	public $plugin_basename = '';

	/**
	 * Plugin directory URL. Initialized by the constructor.
	 *
	 * @var string
	 */
	public $plugin_dir_url = '';

	/**
	 * Parameter for the updater: slug of the product URL.
	 *
	 * @var string
	 */
	protected $product_slug = '';

	/**
	 * Parameter for the updater: product ID (name).
	 *
	 * @var string
	 */
	protected $product_id = '';

	/**
	 * Used in the `load_textdomain` call.
	 *
	 * @var string
	 */
	protected $textdomain = '';

	/**
	 * Constructor.
	 *
	 * @param string $the__file__ Pass `__FILE__` from the loader.
	 */
	public function __construct( $the__file__ ) {
		$this->plugin_file     = $the__file__;
		$this->plugin_basename = plugin_basename( $this->plugin_file );
		$this->plugin_dir_url  = plugin_dir_url( $this->plugin_file );
	}

	/**
	 * Setup updater.
	 * All parameters must be set by the child class' constructor.
	 */
	public function setup_updater() {
		if ( $this->plugin_file && $this->product_id && $this->product_slug ) {
			/* @noinspection PhpUndefinedClassInspection */
			new TIVWP_Updater( array(
				'plugin_file' => $this->plugin_file,
				'product_id'  => $this->product_id,
				'url_product' => WPGlobus::URL_WPGLOBUS_SITE . 'product/' .
				                 $this->product_slug . '/',
			) );
		}
	}

	/**
	 * Load PO/MO.
	 * The parameter must be set by the child class' constructor.
	 */
	public function load_translations() {
		if ( $this->textdomain ) {
			load_plugin_textdomain( $this->textdomain, false,
				dirname( $this->plugin_basename ) . '/languages'
			);
		}
	}
}

/* EOF */
