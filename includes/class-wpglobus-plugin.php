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
	 * Constructor.
	 *
	 * @param string $the__file__ Pass `__FILE__` from the loader.
	 */
	public function __construct( $the__file__ ) {
		$this->plugin_file     = $the__file__;
		$this->plugin_basename = plugin_basename( $this->plugin_file );
		$this->plugin_dir_url  = plugin_dir_url( $this->plugin_file );
	}
}

/* EOF */
