<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class LanguagesTable
 */
class LanguagesTable extends WP_List_Table {

	var $data = array();

	var $table_fields = array();

	var $found_data = array();

	var $_column_headers = array();

	/**
	 *  Constructor.
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'item', 'wpglobus' ),
			// singular name of the listed records
			'plural'   => __( 'items', 'wpglobus' ),
			// plural name of the listed records
			'ajax'     => true
			// does this table support ajax?
		) );

		$this->get_data();

		$this->display_table();

	}

	/**
	 * Fill out table_fields and data arrays
	 * @return void
	 */
	function get_data() {

		$config = WPGlobus::Config();

		$this->table_fields = array(
			'wpglobus_code'             => array(
				'caption'  => __( 'Code', 'wpglobus' ),
				'sortable' => true,
				'order'    => 'asc',
				'actions'  => array(
					'edit'   => array(
						'action'  => 'edit',
						'caption' => __( 'Edit', 'wpglobus' ),
						'ajaxify' => false
					),
					'delete' => array(
						'action'  => 'delete',
						'caption' => __( 'Delete', 'wpglobus' ),
						'ajaxify' => false
					)
				)
			),
			'wpglobus_file'             => array(
				'caption'  => __( 'File', 'wpglobus' ),
				'sortable' => false,
				'order'    => 'desc'
			),
			'wpglobus_flag'             => array(
				'caption'  => __( 'Flag', 'wpglobus' ),
				'sortable' => false,
				'order'    => 'desc'
			),
			'wpglobus_locale'           => array(
				'caption'  => __( 'Locale', 'wpglobus' ),
				'sortable' => true,
				'order'    => 'desc'
			),
			'wpglobus_language_name'    => array(
				'caption'  => __( 'Language name', 'wpglobus' ),
				'sortable' => false,
				'order'    => 'desc'
			),
			'wpglobus_en_language_name' => array(
				'caption'  => __( 'English language name', 'wpglobus' ),
				'sortable' => true
			)
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

	function no_items() {
		_e( 'No items found', 'wpglobus' );
	}

	function display_table() {

		$this->prepare_items();
		?>
		<div class="wpglobus_flag_table_wrapper">
			<a id="wpglobus_add_language"
			   href="<?php admin_url(); ?>admin.php?page=<?php echo WPGlobus::LANGUAGE_EDIT_PAGE; ?>&amp;action=add"
			   class="button button-primary"><?php esc_html_e( 'Add new Language', 'wpglobus' ); ?></a>

			<?php $this->prepare_items(); ?>
			<div class="table-wrap wrap">

				<form method="post">
					<?php $this->display(); ?>
				</form>
			</div>
			<!-- .wrap -->
		</div>    <?php

	}

	/**
	 * Prepares the list of items for displaying.
	 * @access public
	 * @return void
	 */
	function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable
		);

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/**
		 * You can handle your row actions
		 */
		$this->process_row_action();


		usort( $this->data, array(
			$this,
			'usort_reorder'
		) );

		$per_page     = 1000;
		$current_page = $this->get_pagenum();
		$total_items  = count( $this->data );

		// only necessary because we have sample data
		$this->found_data = array_slice( $this->data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			//WE have to calculate the total number of items
			'per_page'    => $per_page,
			//WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )
			//WE have to calculate the total number of pages
		) );

		/** @var  WP_List_table class */
		$this->items = $this->found_data;

	}

	/**
	 * @return array
	 */
	function get_columns() {

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
	 * @since  3.1.0
	 * @access protected
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array();
		foreach ( $this->table_fields as $field => $attrs ) {
			if ( $attrs['sortable'] ) {
				$sortable_columns[ $field ] = array(
					$field,
					false
				);
			}
		}

		return $sortable_columns;
	}

	function process_bulk_action() {
	}

	function process_row_action() {
	}

	/**
	 * User's defined function
	 * @since    0.1
	 *
	 * @param $a
	 * @param $b
	 *
	 * @internal param $
	 * @return int
	 */
	function usort_reorder( $a, $b ) {
		// If no sort, get the default
		$i     = 0;
		$field = $default_field = 'source';

		foreach ( $this->table_fields as $field => $attrs ) {
			$default_field = ( $i == 0 ) ? $field : $default_field;
			if ( isset( $attrs['order'] ) ) {
				break;
			}
			$i ++;
		}
		$field   = ( isset( $attrs['order'] ) ) ? $field : $default_field;
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : $field;

		// If no order, default to asc
		if ( ! empty( $_GET['order'] ) ) {
			$order = $_GET['order'];
		} else {
			$order = ( isset( $attrs['order'] ) ) ? $attrs['order'] : 'asc';
		}

		// Determine sort order
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : - $result;
	}

	/**
	 * Define function for add item actions by name 'column_flag'
	 * @since 1.0.0
	 *
	 * @param  $item array
	 *
	 * @return string
	 */
	function column_wpglobus_flag( $item ) {
		return $item['wpglobus_flag'];
	}

	/**
	 * Define function for add item actions by name 'column_locale'
	 * @since 1.0.0
	 *
	 * @param  $item array
	 *
	 * @return string
	 */
	function column_wpglobus_locale( $item ) {
		return $item['wpglobus_locale'];
	}

	/**
	 * Define function for add item actions by name 'column_code'
	 * @since 1.0.0
	 *
	 * @param  $item array
	 *
	 * @return string
	 */
	function column_wpglobus_code( $item ) {

		if ( ! empty( $this->table_fields['wpglobus_code']['actions'] ) ) {

			$config = WPGlobus::Config();
			$actions = array();

			foreach ( $this->table_fields['wpglobus_code']['actions'] as $action => $data ) {
				/** add actions for language code */
				$class = $data['ajaxify'] ? 'class="ajaxify"' : '';

				switch ( $action ) {
					case 'edit' :
						$actions['edit'] =
							sprintf( '<a %1s href="%2s">%3s</a>', $class, admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&lang=' . $item['wpglobus_code'] . '&action=edit', $data['caption'] );
						break;
					case 'delete' :
						if ( $item['wpglobus_code'] == $config->default_language ) {
							$actions['delete'] =
								sprintf( '<a %1s href="#">%2s</a>', $class, __( 'Default language', 'wpglobus' ) );
						} else {
							$actions['delete'] =
								sprintf( '<a %1s href="%2s">%3s</a>', $class, admin_url() . 'admin.php?page=' . WPGlobus::LANGUAGE_EDIT_PAGE . '&lang=' . $item['wpglobus_code'] . '&action=delete', $data['caption'] );
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
	 * Define function for add item actions by name 'column_default'
	 * @since 1.0.0
	 *
	 * @param  $item        array
	 * @param  $column_name string
	 *
	 * @return string
	 */
	function column_default( $item, $column_name ) {

		if ( isset( $this->table_fields[ $column_name ] ) ) {
			return $item[ $column_name ];
		} else {
			return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}

	}

	/**
	 * Define function for add item actions by name 'column_cb'
	 * @since 1.0.0
	 *
	 * @param  $item array
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="item[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * Generate the table navigation above or below the table
	 * @since  3.1.0
	 * @access protected
	 *
	 * @param string $which
	 */
	function display_tablenav( $which ) {
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

}

# --- EOF
