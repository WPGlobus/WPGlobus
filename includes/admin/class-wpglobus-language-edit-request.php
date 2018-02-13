<?php
/**
 * WPGlobus / Admin / Language Edit / Request
 * plugins/wpglobus/includes/admin/class-wpglobus-language-edit-request.php
 *
 * @package WPGlobus\Admin
 * @since   1.9.7.1
 */

/**
 * Class WPGlobus_Language_Edit_Request
 */
class WPGlobus_Language_Edit_Request {

	const NONCE_ACTION = 'wpglobus-language-edit';

	const ACTION_NONE = '';

	const ACTION_EDIT = 'edit';

	const ACTION_DELETE = 'delete';

	const ACTION_ADD = 'add';

	const ACTION_DONE = 'done';

	/**
	 * GET['action']
	 *
	 * @var string
	 */
	protected $action = self::ACTION_NONE;

	/**
	 * GET['lang']
	 *
	 * @var string
	 */
	protected $lang = '';

	/**
	 * POST['submit']
	 *
	 * @var bool
	 */
	protected $is_submit = false;

	/**
	 * POST['delete']
	 *
	 * @var bool
	 */
	protected $is_delete = false;


	/**
	 * POST['wpglobus_language_code']
	 *
	 * @var string
	 */
	protected $wpglobus_language_code = '';

	/**
	 * POST['wpglobus_flags']
	 *
	 * @var string
	 */
	protected $wpglobus_flags = '';

	/**
	 * POST['wpglobus_language_name']
	 *
	 * @var string
	 */
	protected $wpglobus_language_name = '';

	/**
	 * POST['wpglobus_en_language_name']
	 *
	 * @var string
	 */
	protected $wpglobus_en_language_name = '';

	/**
	 * POST['wpglobus_locale']
	 *
	 * @var string
	 */
	protected $wpglobus_locale = '';

	/**
	 * WPGlobus_Language_Edit_Request constructor.
	 */
	public function __construct() {
		$this->parse_request();
	}

	/**
	 * Parse GET and POST.
	 */
	protected function parse_request() {

		check_admin_referer( self::NONCE_ACTION );

		if ( isset( $_GET['action'] ) && is_string( $_GET['action'] ) ) { // WPCS: input var ok, sanitization ok.
			$action = sanitize_text_field( wp_unslash( $_GET['action'] ) ); // Input var okay.
			if ( in_array( $action, array(
				self::ACTION_ADD,
				self::ACTION_EDIT,
				self::ACTION_DELETE,
				self::ACTION_DONE,
			), true ) ) {
				$this->action = $action;
			} else {
				$this->action = self::ACTION_NONE;
			}
		}

		if ( isset( $_GET['lang'] ) && is_string( $_GET['lang'] ) ) { // WPCS: input var ok, sanitization ok.
			$this->lang = sanitize_text_field( wp_unslash( $_GET['lang'] ) ); // Input var okay.
		}

		if ( isset( $_POST['submit'] ) ) {  // Input var okay.
			$this->is_submit = true;
		}

		if ( isset( $_POST['delete'] ) ) {  // Input var okay.
			$this->is_delete = true;
		}

		foreach (
			array(
				'wpglobus_language_code',
				'wpglobus_flags',
				'wpglobus_language_name',
				'wpglobus_en_language_name',
				'wpglobus_locale',
			) as $var_name
		) {
			if ( isset( $_POST[ $var_name ] ) && is_string( $_POST[ $var_name ] ) ) { // WPCS: input var ok, sanitization ok.
				$this->$var_name = sanitize_text_field( wp_unslash( $_POST[ $var_name ] ) ); // Input var okay.
			}
		}
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_action() {
		return $this->action;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_lang() {
		return $this->lang;
	}

	/**
	 * Getter.
	 *
	 * @return bool
	 */
	public function is_submit() {
		return $this->is_submit;
	}

	/**
	 * Getter.
	 *
	 * @return bool
	 */
	public function is_delete() {
		return $this->is_delete;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_wpglobus_language_code() {
		return $this->wpglobus_language_code;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_wpglobus_flags() {
		return $this->wpglobus_flags;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_wpglobus_language_name() {
		return $this->wpglobus_language_name;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_wpglobus_en_language_name() {
		return $this->wpglobus_en_language_name;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_wpglobus_locale() {
		return $this->wpglobus_locale;
	}

	public static function url_language_add() {
		$url = add_query_arg( array(
			'page'   => WPGlobus::LANGUAGE_EDIT_PAGE,
			'action' => self::ACTION_ADD,
		), admin_url( 'admin.php' ) );

		return wp_nonce_url( $url, self::NONCE_ACTION );
	}

	public static function url_language_edit( $language_code ) {
		$url = add_query_arg( array(
			'page'   => WPGlobus::LANGUAGE_EDIT_PAGE,
			'lang'   => $language_code,
			'action' => self::ACTION_EDIT,
		), admin_url( 'admin.php' ) );

		return wp_nonce_url( $url, self::NONCE_ACTION );
	}

	public static function url_language_delete( $language_code ) {
		$url = add_query_arg( array(
			'page'   => WPGlobus::LANGUAGE_EDIT_PAGE,
			'lang'   => $language_code,
			'action' => self::ACTION_DELETE,
		), admin_url( 'admin.php' ) );

		return wp_nonce_url( $url, self::NONCE_ACTION );
	}
}
