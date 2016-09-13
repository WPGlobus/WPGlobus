<?php
/**
 * Admin interface
 * @package   WPGlobus\Updater
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobus_Updater_Menu' ) ) :

	/**
	 * Class WPGlobus_Updater_Menu
	 */
	class WPGlobus_Updater_Menu {

		/** @var  WPGlobus_Updater $WPGlobus_Updater */
		protected $WPGlobus_Updater;

		/**
		 * Load admin menu
		 *
		 * @param WPGlobus_Updater $WPGlobus_Updater
		 */
		public function __construct( WPGlobus_Updater $WPGlobus_Updater ) {

			$this->WPGlobus_Updater = $WPGlobus_Updater;

			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_init', array( $this, 'load_settings' ) );
			/**
			 * Do some actions after deactivation
			 */
			add_action( 'update_option_' . $this->WPGlobus_Updater->ame_deactivate_checkbox,
				array( $this, 'after_deactivation' ), 0, 2 );

		}

		/**
		 * Add option page menu
		 */
		public function add_menu() {

			$page =
				add_dashboard_page(
					$this->WPGlobus_Updater->ame_settings_menu_title,
					$this->WPGlobus_Updater->ame_settings_menu_title,
					'manage_options',
					$this->WPGlobus_Updater->ame_activation_tab_key,
					array( $this, 'config_page' )
				);
			add_action( 'admin_print_styles-' . $page, array( $this, 'css_scripts' ) );
		}

		/**
		 * Draw option page
		 */
		public function config_page() {

			$settings_tabs = array(
				$this->WPGlobus_Updater->ame_activation_tab_key   => $this->WPGlobus_Updater->ame_menu_tab_activation_title,
				$this->WPGlobus_Updater->ame_deactivation_tab_key => $this->WPGlobus_Updater->ame_menu_tab_deactivation_title
			);
			$current_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->WPGlobus_Updater->ame_activation_tab_key;
			$tab           = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->WPGlobus_Updater->ame_activation_tab_key;
			settings_errors();
			?>
			<div class='wrap'>
				<h2><?php echo $this->WPGlobus_Updater->ame_settings_title; ?></h2>

				<h2 class="nav-tab-wrapper">
					<?php
					foreach ( $settings_tabs as $tab_page => $tab_name ) {
						$active_tab = ( $current_tab === $tab_page ? ' nav-tab-active' : '' );
						echo '<a class="nav-tab' . $active_tab . '" href="?page=' .
						     $this->WPGlobus_Updater->ame_activation_tab_key .
						     '&amp;tab=' . $tab_page . '">' . $tab_name . '</a>';
					}
					?>
				</h2>

				<form class="wpglobus-updater-license" action="options.php" method="post">
					<div class="main">
						<?php
						if ( $tab === $this->WPGlobus_Updater->ame_activation_tab_key ) {
							settings_fields( $this->WPGlobus_Updater->ame_data_key );
							do_settings_sections( $this->WPGlobus_Updater->ame_activation_tab_key );
							submit_button( __( 'Save Changes', 'wpglobus' ) );

						} else {
							settings_fields( $this->WPGlobus_Updater->ame_deactivate_checkbox );
							do_settings_sections( $this->WPGlobus_Updater->ame_deactivation_tab_key );
							submit_button( __( 'Deactivate!', 'wpglobus' ) );

						}
						?>
					</div>
				</form>
			</div>
		<?php
		}

		/**
		 * Intro text on the License tab
		 */
		public function wc_am_api_key_text() {
			// translators: %s is an URL placeholder
			printf( __( 'Do not have a license? Please purchase one on %s', 'wpglobus' ),
				'<a href="' . esc_url( $this->WPGlobus_Updater->upgrade_url ) . '">' .
				esc_html( $this->WPGlobus_Updater->upgrade_url ) . '</a>' );
		}

		/**
		 * Intro text on the Deactivate tab
		 */
		public function wc_am_deactivate_text() {
		}

		/**
		 * Checkbox on the Deactivation tab
		 */
		public function field_deactivation_checkbox() {

			echo '<input type="checkbox" id="' . $this->WPGlobus_Updater->ame_deactivate_checkbox . '" name="' . $this->WPGlobus_Updater->ame_deactivate_checkbox . '" value="on"';
			//			echo checked( get_option( $this->WPGlobus_Updater->ame_deactivate_checkbox ), 'on' );
			echo '/>';
			?><span class="description"><?php
			esc_html_e( 'Deactivates License so it can be used on another site.', 'wpglobus' );
			?></span><?php
		}


		// Register settings
		public function load_settings() {

			register_setting( $this->WPGlobus_Updater->ame_data_key, $this->WPGlobus_Updater->ame_data_key, array(
				$this,
				'validate_options'
			) );

			// API Key
			add_settings_section( $this->WPGlobus_Updater->ame_api_key, __( 'License Activation', 'wpglobus' ), array(
				$this,
				'wc_am_api_key_text'
			), $this->WPGlobus_Updater->ame_activation_tab_key );
			add_settings_field( 'status', __( 'License Key Status', 'wpglobus' ), array(
				$this,
				'wc_am_api_key_status'
			), $this->WPGlobus_Updater->ame_activation_tab_key, $this->WPGlobus_Updater->ame_api_key );
			add_settings_field( $this->WPGlobus_Updater->ame_api_key, __( 'License Key', 'wpglobus' ), array(
				$this,
				'wc_am_api_key_field'
			), $this->WPGlobus_Updater->ame_activation_tab_key, $this->WPGlobus_Updater->ame_api_key );
			add_settings_field( $this->WPGlobus_Updater->ame_activation_email, __( 'License email', 'wpglobus' ), array(
				$this,
				'wc_am_api_email_field'
			), $this->WPGlobus_Updater->ame_activation_tab_key, $this->WPGlobus_Updater->ame_api_key );


			/**
			 * Deactivation
			 */

			/**
			 * option group
			 * option name
			 * "sanitize" callback
			 */
			register_setting(
				$this->WPGlobus_Updater->ame_deactivate_checkbox,
				$this->WPGlobus_Updater->ame_deactivate_checkbox,
				array(
					$this,
					'wc_am_license_key_deactivation'
				)
			);

			add_settings_section( 'section_deactivate', __( 'License Deactivation', 'wpglobus' ),
				array(
					$this,
					'wc_am_deactivate_text'
				),
				$this->WPGlobus_Updater->ame_deactivation_tab_key );

			add_settings_field( 'deactivate_button', __( 'Deactivate License Key', 'wpglobus' ),
				array(
					$this,
					'field_deactivation_checkbox'
				),
				$this->WPGlobus_Updater->ame_deactivation_tab_key, 'section_deactivate' );

		}

		// Returns the API License Key status from the WooCommerce API Manager on the server
		public function wc_am_api_key_status() {
			/** @var array $license_status */
			$license_status       = $this->license_key_status();

			if ( isset( $license_status[ WPGlobus_Updater::KEY_INTERNAL_ERROR ] ) ) {
				// Something was wrong with the connection to the API server.
				echo '<span class="wpglobus-mark wpglobus-warning">' .
				     esc_html( $license_status[ WPGlobus_Updater::KEY_INTERNAL_ERROR ] ) .
				     '</span><br/> ' .
				     esc_html__( 'Please contact support@wpglobus.com for assistance.', 'wpglobus' );
			}
			else {
				$license_status_check =
					( ! empty( $license_status['status_check'] ) &&
					  'active' === strtolower( $license_status['status_check'] ) ) ?
						__( 'Active', 'wpglobus' ) :
						__( 'Inactive', 'wpglobus' );
				if ( ! empty( $license_status_check ) ) {
					echo $license_status_check;
				}
				if ( ! empty( $license_status['activations_remaining'] ) ) {
					echo ' (' . $license_status['activations_remaining'] . ')';
				}
				if ( ! empty( $license_status['additional info'] ) ) {
					echo '<br/><span class="wpglobus-mark wpglobus-warning">' . $license_status['additional info'] . '</span>';
				}
			}
		}

		// Returns API License text field
		public function wc_am_api_key_field() {

			$value    = $this->WPGlobus_Updater->ame_options[ $this->WPGlobus_Updater->ame_api_key ];
			$icon     = $value ? 'complete' : 'warn';
			$url_icon = $this->WPGlobus_Updater->my_url() . 'assets/images/' . $icon . '.png';

			echo '<input id="api_key" name="' . $this->WPGlobus_Updater->ame_data_key .
			     '[' . $this->WPGlobus_Updater->ame_api_key . ']"' .
			     ' type="text" value="' . esc_attr( $value ) . '" />';

			echo '<span class="icon-pos"><img src="' . esc_url( $url_icon ) . '" alt=""/></span>';
		}

		/**
		 * Returns API License email text field
		 */
		public function wc_am_api_email_field() {

			$value    = $this->WPGlobus_Updater->ame_options[ $this->WPGlobus_Updater->ame_activation_email ];
			$icon     = $value ? 'complete' : 'warn';
			$url_icon = $this->WPGlobus_Updater->my_url() . 'assets/images/' . $icon . '.png';

			echo '<input id="activation_email" name="' . $this->WPGlobus_Updater->ame_data_key .
			     '[' . $this->WPGlobus_Updater->ame_activation_email . ']"' .
			     ' type="email" value="' . esc_attr( $value ) . '" />';

			echo '<span class="icon-pos"><img src="' . esc_url( $url_icon ) . '" alt=""/></span>';
		}

		/**
		 * Sanitizes and validates all input and output for Dashboard
		 *
		 * @param array $input
		 *
		 * @return array
		 */
		public function validate_options( $input ) {

			// Load existing options, validate, and update with changes from input before returning
			$options = $this->WPGlobus_Updater->ame_options;

			$options[ $this->WPGlobus_Updater->ame_api_key ]          =
				trim( $input[ $this->WPGlobus_Updater->ame_api_key ] );
			$options[ $this->WPGlobus_Updater->ame_activation_email ] =
				trim( $input[ $this->WPGlobus_Updater->ame_activation_email ] );

			/**
			 * Plugin Activation
			 */
			$api_email = trim( $input[ $this->WPGlobus_Updater->ame_activation_email ] );
			$api_key   = trim( $input[ $this->WPGlobus_Updater->ame_api_key ] );

			$activation_status = get_option( $this->WPGlobus_Updater->ame_activated_key );
			$checkbox_status   = get_option( $this->WPGlobus_Updater->ame_deactivate_checkbox );

			$current_api_key = $this->WPGlobus_Updater->ame_options[ $this->WPGlobus_Updater->ame_api_key ];

			// Should match the settings_fields() value
			if ( $_REQUEST['option_page'] !== $this->WPGlobus_Updater->ame_deactivate_checkbox ) {

				if ( $activation_status === 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status === 'on' || $current_api_key != $api_key ) {

					/**
					 * If this is a new key, and an existing key already exists in the database,
					 * deactivate the existing key before activating the new key.
					 */
					//					if ( $current_api_key != $api_key ) {
					//						$this->replace_license_key( $current_api_key );
					//					}

					$args = array(
						'email'       => $api_email,
						'licence_key' => $api_key,
					);

					/** @var array $activate_results */
					$activate_results = json_decode( $this->WPGlobus_Updater->key()->activate( $args ), true );

					if ( $activate_results['activated'] === true ) {
						add_settings_error(
							'activate_text',
							'activate_msg',
							$this->WPGlobus_Updater->ame_product_id . ': ' .
							__( 'License activated. ', 'wpglobus' ) . "{$activate_results['message']}.",
							'updated'
						);
						/**
						 * Additional actions after activation success
						 */
						add_action( 'update_option_' . $this->WPGlobus_Updater->ame_data_key,
							array( $this, 'after_activation_success' ), 0, 2 );

					} else {
						$this->_print_activation_error_message( $activate_results );
						/**
						 * Additional actions after activation failure
						 */
						add_action( 'update_option_' . $this->WPGlobus_Updater->ame_data_key,
							array( $this, 'after_activation_failure' ) );

					}
				}

			}

			return $options;
		}

		/**
		 * @param array|null $activate_results
		 */
		protected function _print_activation_error_message( $activate_results ) {

			if ( ! is_array( $activate_results ) || $activate_results == false ) {
				add_settings_error( 'api_key_check_text', 'api_key_check_error', __( 'Connection failed to the License Key API server. Try again later.', 'wpglobus' ), 'error' );
			}

			if ( isset( $activate_results['code'] ) ) {

				switch ( $activate_results['code'] ) {
					case '100':
						add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '101':
						add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '102':
						add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '103':
						add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '104':
						add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '105':
						add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '106':
						add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
				}

			}

		}

		/**
		 * Returns the API License Key status from the WooCommerce API Manager on the server
		 */
		public function license_key_status() {

			$args = array(
				'email'       => $this->WPGlobus_Updater->ame_options[ $this->WPGlobus_Updater->ame_activation_email ],
				'licence_key' => $this->WPGlobus_Updater->ame_options[ $this->WPGlobus_Updater->ame_api_key ],
			);

// Example of the server response when empty values are passed:
//			{"error":"Invalid Request","code":"100","additional info":"The email provided is invalid. Status error","activated":"inactive","timestamp":1465313704}

			// Do not call the server with empty arguments. Simulate the error response.
			if ( ! ( $args['email'] && $args['licence_key'] ) ) {
				$status = array(
					'error'           => 'Invalid Request',
					'code'            => '100',
					'additional info' => esc_html__( 'The License Key / Email pair is empty or invalid.', 'wpglobus' ),
					'activated'       => 'inactive',
					'timestamp'       => time(),
				);
			} else {
				$status = json_decode( $this->WPGlobus_Updater->key()->status( $args ), true );
			}
			return $status;
		}

		/**
		 * Deactivate the current license key before activating the new license key
		 *
		 * @param string $current_api_key
		 *
		 * @return bool
		 */
		public function replace_license_key( $current_api_key ) {

			$args = array(
				'email'       => $this->WPGlobus_Updater->ame_options[ $this->WPGlobus_Updater->ame_activation_email ],
				'licence_key' => $current_api_key,
			);

			$reset = $this->WPGlobus_Updater->key()->deactivate( $args ); // reset license key activation

			if ( $reset == true ) {
				return true;
			}

			add_settings_error( 'not_deactivated_text', 'not_deactivated_error', __( 'The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.', 'wpglobus' ), 'updated' );

			return false;
		}

		/**
		 * Deactivates the license key to allow key to be used on another blog
		 *
		 * @param string $input
		 *
		 * @return string
		 */
		public function wc_am_license_key_deactivation( $input ) {

			if ( $input !== 'on' ) {
				add_settings_error(
					'',
					'wpglobus-updater-dea-need-cb',
					esc_html__( 'Please select the checkbox to confirm your intention.', 'wpglobus' )
				);

				$input = 'off';

				return $input;
			}

			$activation_status = get_option( $this->WPGlobus_Updater->ame_activated_key );
			$args              = array(
				'email'       => $this->WPGlobus_Updater
					                 ->ame_options[ $this->WPGlobus_Updater->ame_activation_email ],
				'licence_key' => $this->WPGlobus_Updater
					                 ->ame_options[ $this->WPGlobus_Updater->ame_api_key ],
			);

			if (
				$activation_status !== 'Activated' or
				empty ( $args['licence_key'] ) or
				empty( $args['email'] )
			) {
				add_settings_error(
					'',
					'wpglobus-updater-dea-inactive',
					esc_html__( 'License / email not set or already deactivated.', 'wpglobus' )
				);
				$input = 'off';

				return $input;
			}


			/**
			 * Call the server to deactivate license
			 * @var array $activate_results
			 */
			$activate_results = json_decode( $this->WPGlobus_Updater->key()->deactivate( $args ), true );

			if ( $activate_results['deactivated'] === true ) {

				add_settings_error(
					'wc_am_deactivate_text',
					'wpglobus-updater-dea-ok',
					__( 'License deactivated. ', 'wpglobus' ) .
					"{$activate_results['activations_remaining']}.",
					'updated'
				);

				$input = 'DONE';

				return $input;
			}

			if ( isset( $activate_results['code'] ) ) {

				switch ( $activate_results['code'] ) {
					case '100':
						add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '101':
						add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '102':
						add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '103':
						add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '104':
						add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '105':
						add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
					case '106':
						add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						break;
				}

			}

			$input = 'off';

			return $input;


		}

		/**
		 * This is run after activation SUCCESS, on "update_option_{$option}" hook.
		 *
		 * @param string $ignore
		 * @param string $value
		 */
		public function after_activation_success(
			/** @noinspection PhpUnusedParameterInspection */
			$ignore, $value
		) {
			update_option( $this->WPGlobus_Updater->ame_activated_key, 'Activated' );

		}

		/**
		 * This is run after activation FAILURE, on "update_option_{$option}" hook.
		 */
		public function after_activation_failure() {
			update_option( $this->WPGlobus_Updater->ame_activated_key, 'Deactivated' );
			// TODO Clean the key?
			//			$options[ $this->WPGlobus_Updater->ame_activation_email ] = '';
			//			$options[ $this->WPGlobus_Updater->ame_api_key ]          = '';
		}

		/**
		 * This is run after deactivation, on "update_option_{$option}" hook.
		 *
		 * @param string $ignore
		 * @param string $value
		 */
		public function after_deactivation(
			/** @noinspection PhpUnusedParameterInspection */
			$ignore, $value
		) {
			if ( $value === 'DONE' ) {
				/**
				 * After deactivation, reset all options
				 */
				$this->WPGlobus_Updater->clean_options();
				$this->WPGlobus_Updater->store_options();

			} else {
				update_option( $this->WPGlobus_Updater->ame_activated_key, 'Deactivated' );
			}
		}

		/**
		 * Loads admin style sheets
		 */
		public function css_scripts() {
			if ( ! wp_style_is( 'wpglobus-updater' ) ) {
				wp_enqueue_style( 'wpglobus-updater',
					$this->WPGlobus_Updater->my_url() . 'assets/css/admin.css' );
			}
		}
	} // class

endif;

# --- EOF
