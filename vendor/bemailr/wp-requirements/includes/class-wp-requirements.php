<?php
/**
 * File: wp-requirements.php
 *
 * @package WP-Requirements
 */

// To avoid the "Other declaration of class exists" warnings.
/* @noinspection PhpUndefinedClassInspection */

/**
 * Class WP_Requirements
 */
class WP_Requirements {

	/**
	 * Name of the configuration file.
	 *
	 * @var string
	 */
	const CONFIG_FILE_NAME = 'wp-requirements.json';

	/**
	 * Array of validation results.
	 *
	 * @var array
	 */
	public $results = array();

	/**
	 * Array of requirements.
	 *
	 * @var array
	 */
	public $required = array();

	/**
	 * Url to show instead of listing errors.
	 *
	 * @var string
	 */
	public $requirements_details_url = '';

	/**
	 * Default operator for version comparison.
	 *
	 * @var string
	 */
	public $version_compare_operator = '>=';

	/**
	 * List of actions to be performed if validation failed.
	 *
	 * @var array
	 */
	public $not_valid_actions = array( 'deactivate', 'admin_notice' );

	/**
	 * Whether to show validation success messages or only failures.
	 *
	 * @var bool
	 */
	protected $show_valid_results = false;

	/**
	 * Icon to mark the OK results.
	 *
	 * @var string
	 */
	protected $icon_good = '<span class="dashicons dashicons-yes"></span>&nbsp';

	/**
	 * Icon to mark the failed results.
	 *
	 * @var string
	 */
	protected $icon_bad = '<span class="dashicons dashicons-minus"></span>&nbsp';

	/**
	 * Plugin information.
	 *
	 * @var string[]
	 */
	protected $plugin = array();

	/**
	 * Helps loading translations once for all instances.
	 *
	 * @var bool
	 */
	protected static $_translations_loaded = false;

	/**
	 * WP_Requirements constructor.
	 *
	 * @param string $the__file__  Pass `__FILE__` from the loader.
	 * @param array  $requirements The array of requirements.
	 *                             Optional. If omitted, the wp-requirements.json file will be
	 *                             searched for the requirements.
	 */
	public function __construct( $the__file__, array $requirements = array() ) {

		// Plugin information is always required, so get it now.
		$this->set_plugin( $the__file__ );

		// Requirements can be specified in JSON file.
		if ( ! count( $requirements ) ) {
			$requirements = $this->load_json();
		}

		if ( ! self::$_translations_loaded ) {
			$this->load_translations();
			self::$_translations_loaded = true;
		}

		// Heavy processing here.
		$this->validate_requirements( $requirements );
	}

	/**
	 * Set paths, name etc for a plugin
	 *
	 * @param string $the__file__ Pass `__FILE__` from the loader through Constructor.
	 */
	protected function set_plugin( $the__file__ ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			/* @noinspection PhpIncludeInspection */
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin['fullpath'] = $the__file__;
		$this->plugin['basename'] = plugin_basename( $this->plugin['fullpath'] );
		list( $this->plugin['dirname'], $this->plugin['filename'] ) = explode( '/', $this->plugin['basename'] );

		$plugin_data          = get_plugin_data( $this->plugin['fullpath'] );
		$this->plugin['name'] = $plugin_data['Name'];
	}

	/**
	 * Load translations.
	 * Similar to the core function:
	 *
	 * @see load_plugin_textdomain
	 */
	protected function load_translations() {

		$domain = 'wp-requirements';
		$locale = get_locale();
		$mofile = $domain . '-' . $locale . '.mo';

		// Try to load from the languages directory first.
		if ( ! load_textdomain( $domain, WP_LANG_DIR . '/' . $mofile ) ) {
			// Then try to load from our languages folder.
			load_textdomain( $domain, dirname( __FILE__ ) . '/../languages/' . $mofile );
		}

	}

	/**
	 * All the requirements will be checked and become accessible here: $this->results.
	 *
	 * @param array $requirements Array of requirements.
	 */
	protected function validate_requirements( $requirements ) {
		if ( ! count( $requirements ) ) {
			return;
		}

		if ( ! empty( $requirements['params'] ) ) {
			$this->set_params( $requirements['params'] );
		}

		foreach ( $requirements as $key => $data ) {
			switch ( $key ) {
				case 'php':
					$this->validate_php( $data );
					break;
				case 'mysql':
					$this->validate_mysql( $data );
					break;
				case 'wordpress':
					$this->validate_wordpress( $data );
					break;
			}
		}
	}

	/**
	 * Redefine all params by those that were submitted by a user.
	 *
	 * @param array $params The array of parameters.
	 */
	protected function set_params( $params ) {
		$this->requirements_details_url = ! empty( $params['requirements_details_url'] ) ? esc_url( trim( (string) $params['requirements_details_url'] ) ) : $this->requirements_details_url;
		$this->version_compare_operator = ! empty( $params['version_compare_operator'] ) ? (string) $params['version_compare_operator'] : $this->version_compare_operator;
		$this->not_valid_actions        = ! empty( $params['not_valid_actions'] ) ? (array) $params['not_valid_actions'] : $this->not_valid_actions;
		$this->show_valid_results       = isset( $params['show_valid_results'] ) ? (bool) $params['show_valid_results'] : $this->show_valid_results;
	}

	/**
	 * Check all PHP related data, like version and extensions
	 *
	 * @param array $php PHP requirements.
	 */
	protected function validate_php( $php ) {
		$result = $required = array();

		foreach ( $php as $type => $data ) {
			switch ( $type ) {
				case 'version':
					$result[ $type ]   = version_compare( phpversion(), $data, $this->version_compare_operator );
					$required[ $type ] = $data;
					break;

				case 'extensions':
					// Check that all required PHP extensions are loaded.
					foreach ( (array) $data as $extension ) {
						if ( $extension && is_string( $extension ) ) {
							$result[ $type ][ $extension ] = extension_loaded( $extension );
							$required[ $type ][]           = $extension;
						}
					}

					break;
			}
		}

		$this->results['php']  = $result;
		$this->required['php'] = $required;
	}

	/**
	 * Check all MySQL related data, like version (so far)
	 *
	 * @param array $mysql MySQL requirements.
	 */
	protected function validate_mysql( $mysql ) {
		if ( ! empty( $mysql['version'] ) ) {
			$this->results['mysql']['version']  = version_compare( $this->get_current_mysql_ver(), $mysql['version'], $this->version_compare_operator );
			$this->required['mysql']['version'] = $mysql['version'];
		}
	}

	/**
	 * Check all WordPress related data, like version, plugins and theme
	 *
	 * @param array $wordpress WordPress requirements.
	 */
	protected function validate_wordpress( $wordpress ) {
		global $wp_version;

		$result = $required = array();

		foreach ( $wordpress as $type => $data ) {
			switch ( $type ) {
				case 'version':
					$result[ $type ]   = version_compare( $wp_version, $data, '>=' );
					$required[ $type ] = $data;
					break;

				case 'plugins':
					if ( ! function_exists( 'is_plugin_active' ) ) {
						/* @noinspection PhpIncludeInspection */
						include_once ABSPATH . 'wp-admin/includes/plugin.php';
					}

					foreach ( (array) $data as $plugin => $required_version ) {
						if ( $plugin && is_string( $plugin ) ) {
							$required[ $type ][ $plugin ] = $required_version;

							$is_plugin_active = is_plugin_active( $plugin );

							if ( is_bool( $required_version ) ) {

								/**
								 * When the required version is specified
								 * as a Boolean (`true` or `false`),
								 * then we do not need to check the version number,
								 * just active or not.
								 */
								$result[ $type ][ $plugin ] =
									( $is_plugin_active === $required_version );

							} else {

								/**
								 * Check that the plugin is active and
								 * that its version matches the requirements.
								 */

								$raw_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin,
									false, false
								);

								// If the plugin does not have a version (?!) then
								// we consider the version is OK.
								$is_plugin_version_ok = (
									! isset( $raw_data['Version'] ) ||
									version_compare(
										$raw_data['Version'],
										$required_version,
										$this->version_compare_operator
									)
								);

								$result[ $type ][ $plugin ] = $is_plugin_active && $is_plugin_version_ok;
							}
						}
					}

					break;

				case 'theme':
					$theme = (array) $data;

					$current_theme = wp_get_theme();

					// Now check the theme: user defined slug can be either template
					// (parent theme) or stylesheet (currently active theme).
					foreach ( $theme as $slug => $required_version ) {
						if (
							( $current_theme->get_template() === $slug ||
							  $current_theme->get_stylesheet() === $slug ) &&
							version_compare( $current_theme->get( 'Version' ), $required_version, $this->version_compare_operator )
						) {
							$result[ $type ][ $slug ] = true;
						} else {
							$result[ $type ][ $slug ] = false;
						}

						$required[ $type ][ $slug ] = $required_version;
					}

					break;
			}
		}

		$this->results['wordpress']  = $result;
		$this->required['wordpress'] = $required;
	}

	/**
	 * Check that requirements are met.
	 * If any of rules are failed, the whole check will return false.
	 * True otherwise.
	 *
	 * @return bool
	 */
	public function valid() {
		return ! $this->in_array_recursive( false, $this->results );
	}

	/**
	 * Get the list of registered actions and do everything defined by them
	 */
	public function process_failure() {
		if ( ! count( $this->results ) || ! count( $this->not_valid_actions ) ) {
			return;
		}

		foreach ( $this->not_valid_actions as $action ) {
			switch ( $action ) {
				case 'deactivate':
					deactivate_plugins( $this->get_plugin( 'basename' ), true );

					if ( isset( $_GET['activate'] ) ) {
						unset( $_GET['activate'] );
					}
					break;

				case 'admin_notice':
					add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
					break;
			}
		}
	}

	/**
	 * Display an admin notice in WordPress admin area
	 */
	public function display_admin_notice() {
		echo '<div class="notice is-dismissible error">';

		echo '<p>';

		printf(
			esc_html__( '%s will not function correctly because your site doesn\'t meet some of the requirements:', 'wp-requirements' ),
			'<strong>' . esc_html( $this->get_plugin( 'name' ) ) . '</strong>'
		);

		echo '</p>';

		// Display the link to more details, if we have it.
		if ( $this->requirements_details_url ) {
			printf(
				'<p>' . esc_html__( 'Please read more details %s here %s.', 'wp-requirements' ) . '</p>',
				'<a href="' . esc_url( $this->requirements_details_url ) . '">',
				'</a>'
			);
		} else { // So we need to display all the failures in a notice.
			echo '<ul>';
			foreach ( $this->results as $type => $data ) {
				$notices = $this->php_mysql_notices( $type, $data );
				echo '<li>' . implode( '</li><li>', $notices ) . '</li>'; // WPCS: XSS ok.
			}
			echo '</ul>';

		}

		echo '</div>';
	}

	/**
	 * Prepare a string, that will be displayed in a row for PHP and MySQL only
	 *
	 * @param string $type What's the type of the data: php or mysql.
	 * @param array  $data Contains version and extensions keys with their values.
	 *
	 * @return string[]
	 */
	protected function php_mysql_notices( $type, $data ) {
		$string_version        = esc_html__( '%s: current %s, required %s', 'wp-requirements' );
		$string_ext_loaded     = esc_html__( '%s is activated', 'wp-requirements' );
		$string_ext_not_loaded = esc_html__( '%s is not activated', 'wp-requirements' );
		$string_wp_loaded      = esc_html__( '%s is activated and has a required version %s', 'wp-requirements' );
		$string_wp_not_loaded  = esc_html__( '%s version %s must be activated', 'wp-requirements' );

		$string_must_be_activated = array(
			true  => esc_html__( '%s must be activated', 'wp-requirements' ),
			false => esc_html__( '%s must NOT be activated', 'wp-requirements' ),
		);

		$string_section = array(
			'PHP Version'       => esc_html__( 'PHP Version', 'wp-requirements' ),
			'PHP Extension'     => esc_html__( 'PHP Extension', 'wp-requirements' ),
			'MySQL Version'     => esc_html__( 'MySQL Version', 'wp-requirements' ),
			'WordPress Version' => esc_html__( 'WordPress Version', 'wp-requirements' ),
			'Plugin'            => esc_html__( 'Plugin', 'wp-requirements' ),
			'Theme'             => esc_html__( 'Theme', 'wp-requirements' ),
		);

		$notices = array();

		foreach ( $data as $key => $value ) { // Version : 5.5 || extensions : [curl,mysql].
			$section = $cur_version = '';

			if ( 'php' === $type ) {
				switch ( $key ) {
					case 'version':
						$section     = 'PHP Version';
						$cur_version = phpversion();
						break;
					case 'extensions':
						$section = 'PHP Extension';
						break;
				}
			} elseif ( 'mysql' === $type ) {
				$section     = 'MySQL Version';
				$cur_version = $this->get_current_mysql_ver();
			} elseif ( 'wordpress' === $type ) {
				switch ( $key ) {
					case 'version':
						$section = 'WordPress Version';
						global $wp_version;
						$cur_version = $wp_version;
						break;
					case 'plugins':
						$section = 'Plugin';
						break;
					case 'theme':
						$section = 'Theme';
						break;
				}
			}

			// Ordinary bool meant this is just a 'version'.
			if ( is_bool( $value ) && ( ! $value || $this->show_valid_results ) ) {
				$notices[] = $this->get_notice_status_icon( $value ) .
				             sprintf(
					             $string_version,
					             $string_section[ $section ],
					             $cur_version,
					             $this->version_compare_operator . $this->required[ $type ][ $key ]
				             );
			} elseif ( is_array( $value ) && count( $value ) ) {
				// We need to know - whether we work with PHP extensions or WordPress plugins/theme
				// Extensions are currently passed as an ordinary numeric (while plugins - associative) array
				if ( ! $this->is_array_associative( $this->required[ $type ][ $key ] ) ) { // These are extensions.
					foreach ( (array) $value as $entity => $is_valid ) {

						if ( $is_valid && ! $this->show_valid_results ) {
							continue;
						}

						$notices[] = $this->get_notice_status_icon( $is_valid ) .
						             sprintf(
							             $is_valid ? $string_ext_loaded : $string_ext_not_loaded,
							             $string_section[ $section ] . ' "' . $entity . '"'
						             );
					}
				} else {
					foreach ( (array) $value as $entity => $is_valid ) {

						if ( $is_valid && ! $this->show_valid_results ) {
							continue;
						}

						$entity_name = '';
						// Plugins and themes has different data sources.
						if ( 'plugins' === $key ) {
							if ( file_exists( trailingslashit( WP_PLUGIN_DIR ) . $entity ) ) {
								$entity_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $entity, false );
								$entity_name = $entity_data['Name'];
							} else {
								$entity_name = $entity;
							}
						} elseif ( 'theme' === $key ) {
							$entity_name = $is_valid
								// Name of the current theme, if valid version.
								? wp_get_theme()->get( 'Name' )
								// What's required, string "as-is".
								: $entity;
						}

						if ( is_bool( $this->required[ $type ][ $key ][ $entity ] ) ) {
							$notices[] = $this->get_notice_status_icon( $is_valid ) .
							             sprintf(
								             $string_must_be_activated[ $this->required[ $type ][ $key ][ $entity ] ],
								             $string_section[ $section ] . ' "' . $entity_name . '"'
							             );
						} else {

							$notices[] = $this->get_notice_status_icon( $is_valid ) .
							             sprintf(
								             $is_valid ? $string_wp_loaded : $string_wp_not_loaded,
								             $string_section[ $section ] . ' "' . $entity_name . '"',
								             $this->version_compare_operator . $this->required[ $type ][ $key ][ $entity ]
							             );
						}
					}
				}
			}
		} // endforeach

		return $notices;
	}

	/**
	 * Return a visual icon indicator of success or error
	 *
	 * @param bool $status True of false.
	 *
	 * @return string
	 */
	protected function get_notice_status_icon( $status ) {
		return ( true === $status ) ? $this->icon_good : $this->icon_bad;
	}

	/**
	 * Does $haystack contain $needle in any of the values?
	 * Adapted to our needs, works with arrays in arrays
	 *
	 * @param mixed $needle   What to search.
	 * @param array $haystack Where to search.
	 *
	 * @return bool
	 */
	protected function in_array_recursive( $needle, $haystack ) {
		foreach ( $haystack as $type => $v ) {
			if ( $needle === $v ) { // Useful for recursion only.
				return true;
			} elseif ( is_array( $v ) ) {
				// Basically checks only the version value.
				if ( in_array( $needle, $v, true ) ) {
					return true;
				}

				// Now, time for recursion.
				if ( ! $this->in_array_recursive( $needle, $v ) ) {
					continue;
				} else {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns a value indicating whether the given array is an associative array.
	 *
	 * @param array $array The array being checked.
	 *
	 * @return bool Whether the array is associative.
	 */
	protected function is_array_associative( $array ) {
		return array_keys( array_merge( $array ) ) !== range( 0, count( $array ) - 1 );
	}

	/**
	 * Get the MySQL version number based on data in global WPDB class
	 *
	 * @global wpdb $wpdb
	 * @return string MySQL version number, like 5.5
	 */
	protected function get_current_mysql_ver() {
		/**
		 * WPDB
		 *
		 * @global wpdb $wpdb
		 */
		global $wpdb;

		return $wpdb->db_version();
	}

	/**
	 * Retrieve current plugin data, like paths, name etc
	 *
	 * @param string $data Which data is requested.
	 *
	 * @return mixed
	 */
	public function get_plugin( $data = '' ) {
		// Get all the data.
		if ( ! $data ) {
			return $this->plugin;
		}

		// Get specific plugin data.
		if ( ! empty( $this->plugin[ $data ] ) ) {
			return $this->plugin[ $data ];
		}

		return null;
	}

	/**
	 * Load wp-requirements.json
	 *
	 * @return array
	 */
	protected function load_json() {
		$json_file = $this->locate_configuration_file();
		$json_data = '{}';

		if ( '' !== $json_file ) {
			$json_data = file_get_contents( $json_file );
		}

		return $json_data ? $this->parse_json( $json_data ) : array();
	}

	/**
	 * Search for a configuration file.
	 *
	 * @return string Path to the file found. Blank string if not found.
	 */
	protected function locate_configuration_file() {

		$located = '';

		// By default, search only in the plugin folder.
		$folders = array( dirname( $this->get_plugin( 'fullpath' ) ) );

		/**
		 * Filter to modify the array of configuration folders.
		 *
		 * @since 2.0.0
		 *
		 * @param string[] $folders The array of folders.
		 */
		$folders = (array) apply_filters( 'wp_requirements_configuration_folders', $folders );

		foreach ( $folders as $folder ) {
			$path = trailingslashit( $folder ) . self::CONFIG_FILE_NAME;
			if ( is_readable( $path ) ) {
				$located = $path;
				break;
			}
		}

		return $located;
	}

	/**
	 * Parse JSON string to make it an array that is usable for us
	 *
	 * @param string $json JSON string.
	 *
	 * @return array
	 */
	protected function parse_json( $json ) {

		$data = json_decode( $json, true );

		if ( ! is_array( $data ) ) {
			$data = array();
		}

		return $data;
	}

	/**
	 * Shortcut to construct the object and process the failure actions.
	 *
	 * @param string $the__file__  Pass `__FILE__` from the loader.
	 * @param array  $requirements The array of requirements.
	 *
	 * @return bool True if requirements met.
	 */
	public static function validate( $the__file__, array $requirements = array() ) {
		$_wpr = new WP_Requirements( $the__file__, $requirements );

		$is_valid = $_wpr->valid();

		if ( ! $is_valid ) {
			$_wpr->process_failure();
		}

		return $is_valid;
	}
}

/*EOF*/
