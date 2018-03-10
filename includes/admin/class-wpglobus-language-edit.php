<?php
/**
 * WPGlobus / Admin / Language Edit
 *
 * @package   WPGlobus\Admin
 */

// Load the Request class.
require_once dirname( __FILE__ ) . '/class-wpglobus-language-edit-request.php';

/**
 * Class WPGlobus_Language_Edit
 */
class WPGlobus_Language_Edit {

	/**
	 * All flag files.
	 *
	 * @var array
	 */
	protected $all_flags = array();

	/**
	 * Current action
	 *
	 * @var string
	 */
	protected $action = WPGlobus_Language_Edit_Request::ACTION_ADD;

	/**
	 * Language code
	 *
	 * @var string
	 */
	protected $language_code = '';

	/**
	 * Language name
	 *
	 * @var string
	 */
	protected $language_name = '';

	/**
	 * Language name in English
	 *
	 * @var string
	 */
	protected $en_language_name = '';

	/**
	 * Locale
	 *
	 * @var string
	 */
	protected $locale = '';

	/**
	 * Flag for the current language
	 *
	 * @var string
	 */
	protected $flag = '';

	/**
	 * Set to true when the form is submitted
	 *
	 * @var bool
	 */
	protected $submit = false;

	/**
	 * Diagnostic messages
	 *
	 * @var string[]
	 */
	protected $submit_messages = array();

	/**
	 * The Request object
	 *
	 * @var WPGlobus_Language_Edit_Request
	 */
	protected $request;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->request = new WPGlobus_Language_Edit_Request();

		if ( WPGlobus_Language_Edit_Request::ACTION_DELETE === $this->request->get_action() ) {
			$this->action = WPGlobus_Language_Edit_Request::ACTION_DELETE;
		} elseif ( WPGlobus_Language_Edit_Request::ACTION_EDIT === $this->request->get_action() ) {
			$this->action = WPGlobus_Language_Edit_Request::ACTION_EDIT;
		}

		$this->language_code = $this->request->get_lang();

		if ( $this->request->is_submit() ) {
			$this->submit = true;
			$this->process_submit();
		} elseif ( $this->request->is_delete() ) {
			$this->process_delete();
			$this->action = WPGlobus_Language_Edit_Request::ACTION_DONE;
		} else {
			$this->get_data();
		}

		if ( WPGlobus_Language_Edit_Request::ACTION_DONE !== $this->action ) {
			$this->display_table();
		}

		add_action( 'admin_footer', array( $this, 'on_print_scripts' ), 99 );

	}

	/**
	 * Add script in admin footer
	 */
	public function on_print_scripts() {

		if ( WPGlobus_Language_Edit_Request::ACTION_DONE === $this->action ) {
			$location = '?page=' . WPGlobus::OPTIONS_PAGE_SLUG;
			// @formatter:off
			?>
			<script>jQuery(document).ready(function () {window.location = window.location.protocol + '//' + window.location.host + window.location.pathname + '<?php echo $location; // WPCS: XSS ok. ?>'});</script>
			<?php
			// @formatter:on
		}

		wp_enqueue_script(
			'wpglobus-form',
			WPGlobus::plugin_dir_url() . 'includes/js/wpglobus-form' . WPGlobus::SCRIPT_SUFFIX() . '.js',
			array( 'jquery' ),
			WPGLOBUS_VERSION,
			true
		);

	}

	/**
	 * Process delete language action
	 */
	protected function process_delete() {

		$config = WPGlobus::Config();

		/**
		 * Get options
		 *
		 * @var array
		 */
		$opts = get_option( $config->option );

		if ( isset( $opts['enabled_languages'][ $this->language_code ] ) ) {

			unset( $opts['enabled_languages'][ $this->language_code ] );

			/** FIX: reset $opts['more_languages'] */
			if ( array_key_exists( 'more_languages', $opts ) ) {
				$opts['more_languages'] = '';
			}
			update_option( $config->option, $opts );

		}

		unset( $config->language_name[ $this->language_code ] );
		update_option( $config->option_language_names, $config->language_name );

		unset( $config->flag[ $this->language_code ] );
		update_option( $config->option_flags, $config->flag );

		unset( $config->en_language_name[ $this->language_code ] );
		update_option( $config->option_en_language_names, $config->en_language_name );

		unset( $config->locale[ $this->language_code ] );
		update_option( $config->option_locale, $config->locale );

	}

	/**
	 * Process submit action
	 */
	protected function process_submit() {

		$code = $this->request->get_wpglobus_language_code();
		if ( $code && $this->language_code === $code ) {
			if ( $this->check_fields( $code, false ) ) {
				$this->save();
				$this->submit_messages['success'][] = __( 'Options updated', 'wpglobus' );
			}
		} else {
			if ( $this->check_fields( $code ) ) {
				$this->save( true );
				$this->submit_messages['success'][] = __( 'Options updated', 'wpglobus' );
			}
		}
		$this->get_flags();

	}

	/**
	 * Save data language to DB
	 *
	 * @param bool $update_code If need to change language code.
	 */
	protected function save( $update_code = false ) {

		$config = WPGlobus::Config();

		$old_code = '';
		if ( $update_code && WPGlobus_Language_Edit_Request::ACTION_EDIT === $this->action ) {
			$old_code = $this->language_code ? $this->language_code : $old_code;
			if ( isset( $config->language_name[ $old_code ] ) ) {
				unset( $config->language_name[ $old_code ] );
			}

			/**
			 * Get options
			 *
			 * @var array
			 */
			$opts = get_option( $config->option );
			if ( isset( $opts['enabled_languages'][ $old_code ] ) ) {
				unset( $opts['enabled_languages'][ $old_code ] );
				update_option( $config->option, $opts );
			}
			if ( isset( $opts['more_languages'] ) && $old_code === $opts['more_languages'] ) {
				unset( $opts['more_languages'] );
				update_option( $config->option, $opts );
			}
		}
		$config->language_name[ $this->language_code ] = $this->language_name;
		update_option( $config->option_language_names, $config->language_name );

		if ( $update_code && isset( $config->flag[ $old_code ] ) ) {
			unset( $config->flag[ $old_code ] );
		}
		$config->flag[ $this->language_code ] = $this->flag;
		update_option( $config->option_flags, $config->flag );

		if ( $update_code && isset( $config->en_language_name[ $old_code ] ) ) {
			unset( $config->en_language_name[ $old_code ] );
		}
		$config->en_language_name[ $this->language_code ] = $this->en_language_name;
		update_option( $config->option_en_language_names, $config->en_language_name );

		if ( $update_code && isset( $config->locale[ $old_code ] ) ) {
			unset( $config->locale[ $old_code ] );
		}
		$config->locale[ $this->language_code ] = $this->locale;
		update_option( $config->option_locale, $config->locale );

		if ( $update_code ) {
			$this->action = WPGlobus_Language_Edit_Request::ACTION_DONE;
		}
	}

	/**
	 * Check form fields
	 *
	 * @param string $lang_code  Language code.
	 * @param bool   $check_code Use for language code existence check.
	 *
	 * @return bool True if no errors, false otherwise.
	 */
	protected function check_fields( $lang_code, $check_code = true ) {
		$this->submit_messages['errors'] = array();
		if ( $check_code && empty( $lang_code ) ) {
			$this->submit_messages['errors'][] = __( 'Please enter a language code!', 'wpglobus' );
		}

		if ( $check_code && $this->language_exists( $lang_code ) ) {
			$this->submit_messages['errors'][] = __( 'Language code already exists!', 'wpglobus' );
		}

		if ( ! $this->request->get_wpglobus_flags() ) {
			$this->submit_messages['errors'][] = __( 'Please specify the language flag!', 'wpglobus' );
		}

		if ( ! $this->request->get_wpglobus_language_name() ) {
			$this->submit_messages['errors'][] = __( 'Please enter the language name!', 'wpglobus' );
		}

		if ( ! $this->request->get_wpglobus_en_language_name() ) {
			$this->submit_messages['errors'][] = __( 'Please enter the language name in English!', 'wpglobus' );
		}

		if ( ! $this->request->get_wpglobus_locale() ) {
			$this->submit_messages['errors'][] = __( 'Please enter the locale!', 'wpglobus' );
		}

		$this->language_code    = $lang_code;
		$this->flag             = $this->request->get_wpglobus_flags();
		$this->language_name    = $this->request->get_wpglobus_language_name();
		$this->en_language_name = $this->request->get_wpglobus_en_language_name();
		$this->locale           = $this->request->get_wpglobus_locale();

		if ( empty( $this->submit_messages['errors'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check existing language code in global $WPGlobus_Config
	 *
	 * @param string $code Language code.
	 *
	 * @return bool true if language code exists
	 */
	protected function language_exists( $code ) {

		if ( array_key_exists( $code, WPGlobus::Config()->language_name ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get data for form fields
	 */
	protected function get_data() {

		if ( WPGlobus_Language_Edit_Request::ACTION_EDIT === $this->action
			 || WPGlobus_Language_Edit_Request::ACTION_DELETE === $this->action ) {

			$config = WPGlobus::Config();

			$this->language_name    = $config->language_name[ $this->language_code ];
			$this->en_language_name = $config->en_language_name[ $this->language_code ];
			$this->locale           = $config->locale[ $this->language_code ];
			$this->flag             = $config->flag[ $this->language_code ];
		}
		$this->get_flags();
	}

	/**
	 * Display language form
	 */
	protected function display_table() {

		$disabled = '';
		if ( WPGlobus_Language_Edit_Request::ACTION_EDIT === $this->action ) {
			$header = __( 'Edit Language', 'wpglobus' );
		} elseif ( WPGlobus_Language_Edit_Request::ACTION_DELETE === $this->action ) {
			$header   = __( 'Delete Language', 'wpglobus' );
			$disabled = 'disabled';
		} else {
			$header = __( 'Add Language', 'wpglobus' );
		}
		?>
		<div class="wrap">
			<h1>WPGlobus: <?php echo esc_html( $header ); ?></h1>
			<?php
			if ( $this->submit ) {
				if ( ! empty( $this->submit_messages['errors'] ) ) {
					$mess = '';
					foreach ( $this->submit_messages['errors'] as $message ) {
						$mess .= $message . '<br />';
					}
					?>
					<div class="error"><p><?php echo wp_kses( $mess, array( 'br' => array() ) ); ?></p></div>
					<?php
				} elseif ( ! empty( $this->submit_messages['success'] ) ) {
					$mess = '';
					foreach ( $this->submit_messages['success'] as $message ) {
						$mess .= $message . '<br />';
					}
					?>
					<div class="updated"><p><?php echo wp_kses( $mess, array( 'br' => array() ) ); ?></p></div>
					<?php
				}
			}
			?>
			<form id="wpglobus_edit_form" method="post" action="">
				<table class="form-table">
					<tr>
						<th scope="row"><label
									for="wpglobus_language_code"><?php esc_html_e( 'Language Code', 'wpglobus' ); ?></label>
						</th>
						<td>
							<input name="wpglobus_language_code" <?php echo esc_attr( $disabled ); ?> type="text"
									id="wpglobus_language_code"
									value="<?php echo esc_attr( $this->language_code ); ?>" class="regular-text"/>

							<p class="description"><?php esc_html_e( '2-Letter ISO Language Code for the Language you want to insert. (Example: en)', 'wpglobus' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
									for="wpglobus_flags"><?php esc_html_e( 'Language flag', 'wpglobus' ); ?></label>
						</th>
						<td>
							<select id="wpglobus_flags" name="wpglobus_flags" style="width:300px;"
									class="populate">
								<?php
								foreach ( $this->all_flags as $file_name ) :
									?>
									<option <?php selected( $this->flag === $file_name ); ?>
											value="<?php echo esc_attr( $file_name ); ?>"><?php echo esc_html( $file_name ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
									for="wpglobus_language_name"><?php esc_html_e( 'Name', 'wpglobus' ); ?></label>
						</th>
						<td><input name="wpglobus_language_name" type="text" id="wpglobus_language_name"
									value="<?php echo esc_attr( $this->language_name ); ?>" class="regular-text"/>

							<p class="description"><?php esc_html_e( 'The name of the language in its native alphabet. (Examples: English, Русский)', 'wpglobus' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
									for="wpglobus_en_language_name"><?php esc_html_e( 'Name in English', 'wpglobus' ); ?></label>
						</th>
						<td><input name="wpglobus_en_language_name" type="text" id="wpglobus_en_language_name"
									value="<?php echo esc_attr( $this->en_language_name ); ?>" class="regular-text"/>

							<p class="description"><?php esc_html_e( 'The name of the language in English', 'wpglobus' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
									for="wpglobus_locale"><?php esc_html_e( 'Locale', 'wpglobus' ); ?></label></th>
						<td><input name="wpglobus_locale" type="text" id="wpglobus_locale"
									value="<?php echo esc_attr( $this->locale ); ?>"
									class="regular-text"/>

							<p class="description"><?php esc_html_e( 'PHP/WordPress Locale of the language. (Examples: en_US, ru_RU)', 'wpglobus' ); ?></p>
						</td>
					</tr>
				</table>
				<?php

				if ( WPGlobus_Language_Edit_Request::ACTION_EDIT === $this->action
					 || WPGlobus_Language_Edit_Request::ACTION_ADD === $this->action ) {
					?>
					<input class="button button-primary" type="submit" name="submit"
							value="<?php esc_attr_e( 'Save Changes', 'wpglobus' ); ?>">
					<?php

					if ( WPGlobus_Language_Edit_Request::ACTION_EDIT === $this->action ) {
						?>

						<a class="button button-link-delete" style="margin-left: 1em" href="<?php echo esc_url( WPGlobus_Language_Edit_Request::url_language_delete( $this->language_code ) ); ?>">
							<i class="dashicons dashicons-trash" style="line-height: inherit;"></i>
							<?php esc_html_e( 'Delete Language', 'wpglobus' ); ?>&hellip;</a>
						<?php
					}
				} elseif ( WPGlobus_Language_Edit_Request::ACTION_DELETE === $this->action ) {
					?>
					<div class="notice-large wp-ui-notification"><?php esc_html_e( 'Are you sure you want to delete?', 'wpglobus' ); ?></div>
					<p class="submit"><input class="button button-primary" type="submit" name="delete"
								value="<?php esc_attr_e( 'Delete Language', 'wpglobus' ); ?>"></p>
				<?php } ?>

			</form>

			<hr/>
			<span class="dashicons dashicons-admin-site"></span>
			<a href="<?php echo esc_url( WPGlobus_Admin_Page::url_settings() ); ?>">
				<?php esc_html_e( 'Back to the WPGlobus Settings', 'wpglobus' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Get flag files from directory
	 */
	protected function get_flags() {

		$dir = new DirectoryIterator( WPGlobus::plugin_dir_path() . 'flags/' );

		foreach ( $dir as $file ) {
			/**
			 * File object
			 *
			 * @var DirectoryIterator $file
			 */
			if ( $file->isFile() ) {
				$this->all_flags[] = $file->getFilename();
			}
		}
	}
}
