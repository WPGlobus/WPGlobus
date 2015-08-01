<?php
/**
 * @package   WPGlobus/Admin
 */

/**
 * Class WPGlobus_Language_Edit
 */
class WPGlobus_Language_Edit {

	/** @var array All flag files */
	protected $all_flags = array();

	/** @var string Current action */
	protected $action = 'add';

	/** @var string Language code */
	protected $language_code = '';

	/** @var string Language name */
	protected $language_name = '';

	/** @var string Language name in English */
	protected $en_language_name = '';

	/** @var string Locale */
	protected $locale = '';

	/** @var string Flag of the current language */
	protected $flag = '';

	/** @var bool Set to true at submit form action */
	protected $submit = false;

	/** @var array Messages for form submit */
	protected $submit_messages = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		if ( isset( $_GET['action'] ) ) {
			if ( 'delete' === $_GET['action'] ) {
				$this->action = 'delete';
			} elseif ( 'edit' === $_GET['action'] ) {
				$this->action = 'edit';
			}
		}

		if ( ! empty( $_GET['lang'] ) ) {
			$this->language_code = $_GET['lang'];
		}

		if ( isset( $_POST['submit'] ) ) {
			$this->submit = true;
			$this->process_submit();
		} elseif ( isset( $_POST['delete'] ) ) {
			$this->process_delete();
			$this->action = 'done';
		} else {
			$this->get_data();
		}

		if ( $this->action !== 'done' ) {
			$this->display_table();
		}

		add_action( 'admin_footer', array( $this, 'on_print_scripts' ), 99 );

	}
	
	/**
	 * Add script in admin footer
	 * @return void
	 */
	public function on_print_scripts() {
		
		if ( 'done' === $this->action ) {
			$location = '?page=' . WPGlobus::OPTIONS_PAGE_SLUG; ?>

			<script>
				jQuery(document).ready(function () {
					window.location = window.location.protocol + '//' + window.location.host + window.location.pathname + '<?php echo $location; ?>'
				})
			</script>        <?php
		}

		wp_enqueue_script(
			'wpglobus-form',
			WPGlobus::$PLUGIN_DIR_URL . "includes/js/wpglobus-form" . WPGlobus::SCRIPT_SUFFIX() . ".js",
			array( 'jquery' ),
			WPGLOBUS_VERSION,
			true
		);

	}

	/**
	 * Process delete language action
	 * @return void
	 */
	protected function process_delete() {

		$config = WPGlobus::Config();

		/** @var array $opts */
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
	 * @return void
	 */
	protected function process_submit() {

		$code = isset( $_POST['wpglobus_language_code'] ) ? $_POST['wpglobus_language_code'] : '';
		if ( ! empty( $code ) && $this->language_code === $code ) {
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
		$this->_get_flags();

	}

	/**
	 * Save data language to DB
	 *
	 * @param bool $update_code If need to change language code
	 *
	 * @return void
	 */
	protected function save( $update_code = false ) {

		$config = WPGlobus::Config();

		$old_code = '';
		if ( $update_code && 'edit' === $this->action ) {
			$old_code = isset( $_GET['lang'] ) ? $_GET['lang'] : $old_code;
			if ( isset( $config->language_name[ $old_code ] ) ) {
				unset( $config->language_name[ $old_code ] );
			}

			/** @var array $opts */
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
			$this->action = 'done';
		}
	}

	/**
	 * Check form fields
	 *
	 * @param string $lang_code
	 * @param bool   $check_code Use for existence check language code
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

		if ( empty( $_POST['wpglobus_flags'] ) ) {
			$this->submit_messages['errors'][] = __( 'Please specify the language flag!', 'wpglobus' );
		}

		if ( empty( $_POST['wpglobus_language_name'] ) ) {
			$this->submit_messages['errors'][] = __( 'Please enter the language name!', 'wpglobus' );
		}

		if ( empty( $_POST['wpglobus_en_language_name'] ) ) {
			$this->submit_messages['errors'][] = __( 'Please enter the language name in English!', 'wpglobus' );
		}

		if ( empty( $_POST['wpglobus_locale'] ) ) {
			$this->submit_messages['errors'][] = __( 'Please enter the locale!', 'wpglobus' );
		}

		$this->language_code    = $lang_code;
		$this->flag             = isset( $_POST['wpglobus_flags'] ) ? $_POST['wpglobus_flags'] : '';
		$this->language_name    = isset( $_POST['wpglobus_language_name'] ) ? $_POST['wpglobus_language_name'] : '';
		$this->en_language_name =
			isset( $_POST['wpglobus_en_language_name'] ) ? $_POST['wpglobus_en_language_name'] : '';
		$this->locale           = isset( $_POST['wpglobus_locale'] ) ? $_POST['wpglobus_locale'] : '';

		if ( empty( $this->submit_messages['errors'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check existing language code in global $WPGlobus_Config
	 *
	 * @param string $code
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
	 * @return void
	 */
	protected function get_data() {

		if ( 'edit' === $this->action || 'delete' === $this->action ) {
			$config = WPGlobus::Config();
			$this->language_name    = $config->language_name[ $this->language_code ];
			$this->en_language_name = $config->en_language_name[ $this->language_code ];
			$this->locale           = $config->locale[ $this->language_code ];
			$this->flag             = $config->flag[ $this->language_code ];
		}
		$this->_get_flags();
	}

	/**
	 * Display language form
	 * @return void
	 */
	protected function display_table() {

		$disabled = '';
		if ( 'edit' === $this->action ) {
			$header = __( 'Edit Language', 'wpglobus' );
		} elseif ( 'delete' === $this->action ) {
			$header   = __( 'Are you sure you want to delete?', 'wpglobus' );
			$disabled = 'disabled';
		} else {
			$header = __( 'Add Language', 'wpglobus' );
		}
		?>
		<div class="wrap">
			<h1>WPGlobus: <?php echo $header; ?></h1>
			<?php if ( $this->submit ) {
				if ( ! empty( $this->submit_messages['errors'] ) ) {
					$mess = '';
					foreach ( $this->submit_messages['errors'] as $message ) {
						$mess .= $message . '<br />';
					} ?>
					<div class="error"><?php echo $mess; ?></div> <?php
				} elseif ( ! empty( $this->submit_messages['success'] ) ) {
					$mess = '';
					foreach ( $this->submit_messages['success'] as $message ) {
						$mess .= $message . '<br />';
					} ?>
					<div class="updated"><?php echo $mess; ?></div> <?php
				}
			} ?>
			<form id="wpglobus_edit_form" method="post" action="">
				<table class="form-table">
					<tr>
						<th scope="row"><label
								for="wpglobus_language_code"><?php _e( 'Language Code', 'wpglobus' ); ?></label>
						</th>
						<td>
							<input name="wpglobus_language_code" <?php echo $disabled; ?> type="text"
							       id="wpglobus_language_code"
							       value="<?php echo $this->language_code; ?>" class="regular-text"/>

							<p class="description"><?php _e( '2-Letter ISO Language Code for the Language you want to insert. (Example: en)', 'wpglobus' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wpglobus_flags"><?php _e( 'Language flag', 'wpglobus' ); ?></label>
						</th>
						<td>
							<select id="wpglobus_flags" name="wpglobus_flags" style="width:300px;"
							        class="populate">    <?php
								foreach ( $this->all_flags as $file_name ) :
									?>
									<option <?php selected( $this->flag === $file_name ); ?>
										value="<?php echo $file_name; ?>"><?php echo $file_name; ?></option>    <?php
								endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wpglobus_language_name"><?php _e( 'Name', 'wpglobus' ); ?></label>
						</th>
						<td><input name="wpglobus_language_name" type="text" id="wpglobus_language_name"
						           value="<?php echo $this->language_name; ?>" class="regular-text"/>

							<p class="description"><?php _e( 'The name of the language in its native alphabet. (Examples: English, Русский)', 'wpglobus' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label
								for="wpglobus_en_language_name"><?php _e( 'Name in English', 'wpglobus' ); ?></label>
						</th>
						<td><input name="wpglobus_en_language_name" type="text" id="wpglobus_en_language_name"
						           value="<?php echo $this->en_language_name; ?>" class="regular-text"/>

							<p class="description"><?php _e( 'The name of the language in English', 'wpglobus' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="wpglobus_locale"><?php _e( 'Locale', 'wpglobus' ); ?></label></th>
						<td><input name="wpglobus_locale" type="text" id="wpglobus_locale"
						           value="<?php echo $this->locale; ?>"
						           class="regular-text"/>

							<p class="description"><?php _e( 'PHP/WordPress Locale of the language. (Examples: en_US, ru_RU)', 'wpglobus' ); ?></p>
						</td>
					</tr>
				</table>    <?php

				if ( 'edit' === $this->action || 'add' === $this->action ) {
					?>
					<p class="submit"><input class="button button-primary" type="submit" name="submit"
					                         value="<?php esc_attr_e( 'Save Changes', 'wpglobus' ); ?>"></p>    <?php
				} elseif ( 'delete' === $this->action ) {
					?>
					<p class="submit"><input class="button button-primary" type="submit" name="delete"
					                         value="<?php esc_attr_e( 'Delete Language', 'wpglobus' ); ?>"></p>    <?php
				} ?>

			</form>

			<hr/>
			<a href="<?php echo admin_url('admin.php?page=wpglobus_options'); ?>">
				&larr;
				<?php esc_html_e( 'Back to the WPGlobus Settings', 'wpglobus' ); ?>
			</a>
		</div>
	<?php
	}

	/**
	 * Get flag files from directory
	 * @return void
	 */
	protected function _get_flags() {

		$dir = new DirectoryIterator( WPGlobus::$PLUGIN_DIR_PATH . 'flags/' );

		foreach ( $dir as $file ) {
			/** @var DirectoryIterator $file */
			if ( $file->isFile() ) {
				$this->all_flags[] = $file->getFilename();
			}
		}

	}

} // class

# --- EOF
