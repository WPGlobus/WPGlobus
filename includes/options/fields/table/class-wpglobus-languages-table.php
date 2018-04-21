<?php
/**
 * File: class-wpglobus-languages-table.php
 *
 * @package     WPGlobus\Admin\Options\Field
 */

// Load the List_Table class.
if ( ! class_exists( 'WP_List_Table' ) ) {
	/** @noinspection PhpIncludeInspection */
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/** @noinspection PhpIncludeInspection */
require_once WPGlobus::plugin_dir_path() . 'includes/admin/class-wpglobus-language-edit-request.php';

/**
 * Class WPGlobus_Languages_Table.
 */
class WPGlobus_Languages_Table extends WP_List_Table {

	/**
	 * Data.
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * Table fields.
	 *
	 * @var array
	 */
	public $table_fields = array();

	/**
	 * Found data.
	 *
	 * @var array
	 */
	public $found_data = array();

	/**
	 * Column headers.
	 *
	 * @var array
	 */
	public $_column_headers = array();

	/**
	 * Field.
	 *
	 * @var array
	 */	
	public $field = array();

	/**
	 *  Constructor.
	 */
	public function __construct($field) {
	
		$this->field = $field;
	
		parent::__construct( array(
			// singular name of the listed records.
			'singular' => esc_html__( 'item', 'wpglobus' ),
			// plural name of the listed records.
			'plural'   => esc_html__( 'items', 'wpglobus' ),
			// does this table support ajax?
			'ajax'     => true,
		) );

		$this->get_data();

		$this->display_table();

	}

	/**
	 * Fill out table_fields and data arrays.
	 */
	public function get_data() {

		$config = WPGlobus::Config();

		$this->table_fields = array(
			'wpglobus_code'             => array(
				'caption'  => esc_html__( 'Code', 'wpglobus' ),
				'sortable' => true,
				'order'    => 'asc',
				'actions'  => array(
					'edit'   => array(
						'action'  => 'edit',
						'caption' => esc_html__( 'Edit', 'wpglobus' ),
						'ajaxify' => false,
					),
					'delete' => array(
						'action'  => 'delete',
						'caption' => esc_html__( 'Delete', 'wpglobus' ),
						'ajaxify' => false,
					),
				),
			),
			'wpglobus_file'             => array(
				'caption'  => esc_html__( 'File', 'wpglobus' ),
				'sortable' => false,
				'order'    => 'desc',
			),
			'wpglobus_flag'             => array(
				'caption'  => esc_html__( 'Flag', 'wpglobus' ),
				'sortable' => false,
				'order'    => 'desc',
			),
			'wpglobus_locale'           => array(
				'caption'  => esc_html__( 'Locale', 'wpglobus' ),
				'sortable' => true,
				'order'    => 'desc',
			),
			'wpglobus_language_name'    => array(
				'caption'  => esc_html__( 'Language name', 'wpglobus' ),
				'sortable' => false,
				'order'    => 'desc',
			),
			'wpglobus_en_language_name' => array(
				'caption'  => esc_html__( 'English language name', 'wpglobus' ),
				'sortable' => true,
			),
		);

		foreach ( $config->language_name as $code => $name ) {

			$row['wpglobus_ID']               = $code;
			$row['wpglobus_file']             = $config->flag[ $code ];
			$row['wpglobus_flag']             =
				'<img src="' . $config->flags_url . $config->flag[ $code ] . '" />';
			$row['wpglobus_locale']           = $config->locale[ $code ];
			$row['wpglobus_code']             = $code;
			$row['wpglobus_language_name']    = $name;
			$row['wpglobus_en_language_name'] = $config->en_language_name[ $code ];

			$this->data[] = $row;

		}

	}

	/**
	 * Show "no items" message.
	 */
	public function no_items() {
		esc_html_e( 'No items found', 'wpglobus' );
	}

	/**
	 * Display table.
	 */
	public function display_table() {

		$this->prepare_items();
		?>
		<div id="wpglobus-options-<?php echo $this->field['id']; ?>" class="wpglobus-languages-table-wrapper wpglobus-options-field" data-js-handler="handler<?php echo ucfirst($this->field['id']); ?>">
			<a id="wpglobus_add_language" href="<?php echo esc_url( WPGlobus_Language_Edit_Request::url_language_add() ); ?>" class="button button-primary">
				<i class="dashicons dashicons-plus-alt" style="line-height: inherit"></i>
				<?php esc_html_e( 'Add new Language', 'wpglobus' ); ?>
			</a>

			<?php $this->prepare_items(); ?>
			<div class="table-wrap">
				<form method="post">
					<?php $this->display(); ?>
				</form>
			</div>
			<!-- .wrap -->
		</div>
		<?php
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable,
		);

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/**
		 * You can handle your row actions.
		 */
		$this->process_row_action();

		usort( $this->data, array(
			$this,
			'usort_reorder',
		) );

		$per_page     = 1000;
		$current_page = $this->get_pagenum();
		$total_items  = count( $this->data );

		// Only necessary because we have sample data.
		$this->found_data = array_slice( $this->data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			// We have to calculate the total number of items.
			'total_items' => $total_items,
			// We have to determine how many items to show on a page.
			'per_page'    => $per_page,
			// We have to calculate the total number of pages.
			'total_pages' => ceil( $total_items / $per_page ),
		) );

		/**
		 * List table
		 *
		 * @var WP_List_table
		 */
		$this->items = $this->found_data;

	}

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = array();

		foreach ( $this->table_fields as $field => $attrs ) {
			$columns[ $field ] = $attrs['caption'];
		}

		return $columns;

	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 * The second format will make the initial sorting order be descending
	 *
	 * @access protected
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array();
		foreach ( $this->table_fields as $field => $attrs ) {
			if ( $attrs['sortable'] ) {
				$sortable_columns[ $field ] = array(
					$field,
					false,
				);
			}
		}

		return $sortable_columns;
	}

	/**
	 * Process bulk action.
	 */
	public function process_bulk_action() {}

	/**
	 * Process row action.
	 */
	public function process_row_action() {}

	/**
	 * User's defined function.
	 *
	 * @param array $a First value.
	 * @param array $b Second value.
	 *
	 * @return int
	 */
	public function usort_reorder( $a, $b ) {
		// TODO: check if this is needed.
		if ( 0 ) {
			check_admin_referer( WPGlobus_Language_Edit_Request::NONCE_ACTION );
		}

		// If no sort, get the default.
		$i             = 0;
		$default_field = 'source';
		$field         = $default_field;

		foreach ( $this->table_fields as $field => $attrs ) {
			$default_field = ( 0 === $i ? $field : $default_field );
			if ( isset( $attrs['order'] ) ) {
				break;
			}
			$i ++;
		}

		$field = ( isset( $attrs['order'] ) ? $field : $default_field );

		$orderby = ( ! empty( $_GET['orderby'] ) ) // WPCS: input var ok.
			? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) // WPCS: input var ok.
			: $field;

		// If no order, default to asc.
		if ( ! empty( $_GET['order'] ) ) {
			$order = sanitize_text_field( wp_unslash( $_GET['order'] ) ); // WPCS: input var ok.
		} else {
			$order = ( ! empty( $attrs['order'] ) ? $attrs['order'] : 'asc' );
		}

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		// Send final sort direction to usort.
		return 'asc' === $order ? $result : - $result;
	}

	/**
	 * Define function to add item actions by name 'column_flag'.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $item The item.
	 *
	 * @return string
	 */
	public function column_wpglobus_flag( $item ) {
		return $item['wpglobus_flag'];
	}

	/**
	 * Define function to add item actions by name 'column_locale'.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $item The item.
	 *
	 * @return string
	 */
	public function column_wpglobus_locale( $item ) {
		return $item['wpglobus_locale'];
	}

	/**
	 * Define function to add item actions by name 'column_code'.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $item The item.
	 *
	 * @return string
	 */
	public function column_wpglobus_code( $item ) {

		if ( ! empty( $this->table_fields['wpglobus_code']['actions'] ) ) {

			$config  = WPGlobus::Config();
			$actions = array();

			// Add actions to the language code column.
			foreach ( $this->table_fields['wpglobus_code']['actions'] as $action => $data ) {

				$class = array( 'button' );
				//$class = $data['ajaxify'] ? 'class="button button-primary ajaxify"' : 'class="button button-primary"';
				if ( ! empty( $data['ajaxify'] ) ) {
					$class[] = 'ajaxify';
				}

				switch ( $action ) {
					case WPGlobus_Language_Edit_Request::ACTION_EDIT:
						
						$class[] = 'button-primary';
						$link_class = 'class="' . implode(' ', $class) . '"';
						
						$actions['edit'] = sprintf( '<a %1s href="%2s">%3s</a>',
							$link_class,
							esc_url( WPGlobus_Language_Edit_Request::url_language_edit( $item['wpglobus_code'] ) ),
							esc_html( $data['caption'] )
						);

						break;

					case WPGlobus_Language_Edit_Request::ACTION_DELETE:
					
						$link_class = 'class="' . implode(' ', $class) . '"';
						
						if ( $item['wpglobus_code'] === $config->default_language ) {
							$actions['delete'] = '';
							//$actions['delete'] =
								//sprintf( '<a %1s href="#">%2s</a>', $link_class, esc_html__( 'Default language', 'wpglobus' ) );
						} else {
							$actions['delete'] = sprintf( '<a %1s href="%2s">%3s</a>',
								$link_class,
								esc_url( WPGlobus_Language_Edit_Request::url_language_delete( $item['wpglobus_code'] ) ),
								esc_html( $data['caption'] )
							);
						}

						break;
				}
			}

			return sprintf( '%1s %2s', $item['wpglobus_code'], $this->row_actions( $actions ) );

		} else {

			return $item['wpglobus_code'];

		}

	}


	/**
	 * Define function to add item actions by name 'column_default'.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $item        The item.
	 * @param  string $column_name Column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		// Debug printout was here print_r( $item, true ); - replaced with empty string.
		return isset( $this->table_fields[ $column_name ] ) ? $item[ $column_name ] : '';

	}

	/**
	 * Define function tot add item actions by name 'column_cb'.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $item The item.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="item[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * Define function for add item actions by name 'wpglobus_en_language_name'.
	 *
	 * @since 1.5.10
	 *
	 * @param  array $item The item.
	 *
	 * @return string
	 */
	public function column_wpglobus_en_language_name( $item ) {
		if ( in_array( $item['wpglobus_code'], WPGlobus::Config()->enabled_languages, true ) ) {
			return $item['wpglobus_en_language_name'] . ' (<strong>' . esc_html__( 'Installed', 'wpglobus' ) . '</strong>)';
		}

		return $item['wpglobus_en_language_name'];
	}

	/**
	 * Generate the table navigation above or below the table.
	 *
	 * @access protected
	 *
	 * @param string $which Above or below.
	 */
	protected function display_tablenav( $which ) {
		?>

		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions(); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

			<br class="clear"/>
		</div>
		<?php

	}

	/**
	 * Generates content for a single row of the table.
	 *
	 * @since  1.5.10
	 * @access public
	 *
	 * @param object $item The current item.
	 */
	public function single_row( $item ) {
		if ( in_array( $item['wpglobus_code'], WPGlobus::Config()->enabled_languages, true ) ) {
			echo '<tr style="background-color:#d3e4f4;">';
		} else {
			echo '<tr>';
		}

		$this->single_row_columns( $item );
		echo '</tr>';
	}
}
