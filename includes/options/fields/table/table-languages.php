<?php
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LanguagesTable extends WP_List_table {

	var $data = array();

	var $table_fields	= array();

	/**
	 *  Constructor.
	*/	
	public function __construct() {

		parent::__construct( array(
			'singular'  => __( 'item' , '' ), 	// singular name of the listed records
			'plural'	=> __( 'items', '' ),	// plural name of the listed records
			'ajax'		=> true					// does this table support ajax?
		) );

		/** @global wpdb $wpdb */
		// global $wpdb;
		
	
		$this->get_data();

		$this->display_table();

	}

	/**
	 *
	 */
	function get_data() {

		global $WPGlobus_Config;

		$this->table_fields =  array(
			'file' 	=> array(
				'caption'	=> 'File',
				'sortable'  => false,
				'order' 	=> 'desc'
			),
			'flag' 	=> array(
				'caption'	=> 'Flag',
				'sortable'  => false,
				'order' 	=> 'desc'
			),
			'locale' 	=> array(
				'caption'	=> 'Locale',
				'sortable'  => false,
				'order' 	=> 'desc'
			),
			'code' 	=> array(
				'caption'	=> 'Language code',
				'sortable'  => true,
				'order' 	=> 'desc',
				'actions' => array(
					'edit' => array(
						'action' => 'edit',
						'caption' => 'Edit',
						'ajaxify' => true
					)
				)
			),
			'language_name' 	=> array(
				'caption' => 'Language name',
				'sortable' => false,
				'order' => 'desc'
			),
			'en_language_name' => array(
				'caption' => 'English language name',
				'sortable' => true
			)
		);

		$i = 0;
		foreach( $WPGlobus_Config->language_name as $code=>$name ) {

			$row['ID'] 		 		 = $code;
			$row['file']  			 = $WPGlobus_Config->flag[$code];
			$row['flag']  			 = '<img src="' . $WPGlobus_Config->flags_url . $WPGlobus_Config->flag[$code] . '" />';
			$row['locale'] 			 = $WPGlobus_Config->locale[$code];
			$row['code']  			 = $code;
			$row['language_name']  	 = $name;
			$row['en_language_name'] = $WPGlobus_Config->en_language_name[$code];

			$this->data[] = $row;
			if ( $i < 1 ) {
				$this->dummy_data[] = $row;
			}
			$i++;
			//if ($code == 'ru')	break;
		}

	}

	function no_items() {
		_e( 'No items found, dude.' );
	}

	function display_table() {

		$this->prepare_items();
		?>
		<div class="flag-table-wrapper">
			<input id="add_language" type="button" class="button button-primary" value="Add new language" onclick="return false;"/>

			<?php $this->prepare_dummy_items(); ?>
			<div class="table-dummy hidden table-wrap wrap">
				<form method="post">
					<?php $this->display_dummy_table(); ?>
				</form>
			</div>	<!-- .wrap -->

			<?php $this->prepare_items(); ?>
			<div class="table-wrap wrap">

				<form method="post">
					<?php $this->display(); ?>
				</form>
			</div>	<!-- .wrap -->
		</div>	<?php

	}


	function prepare_dummy_items() {
		//$this->prepare_items();

		$columns  = $this->get_columns();
		$hidden   = array();
		// $sortable = $this->get_sortable_columns();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->dummy_data;
	}

	/**
	 * Prepares the list of items for displaying.
	 * @access public
	 * @return void
	 */
	function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/**
		 * You can handle your row actions
		 *
		 */
		$this->process_row_action();


		usort( $this->data, array( &$this, 'usort_reorder' ) );

		$per_page = 1000;
		$current_page 	= $this->get_pagenum();
		$total_items 	= count( $this->data );

		// only necessary because we have sample data
		$this->found_data = array_slice( $this->data,( ( $current_page - 1 ) * $per_page ), $per_page );

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  	//WE have to calculate the total number of items
			'per_page'    => $per_page,                     	//WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items/$per_page )     //WE have to calculate the total number of pages
		) );

		/* 		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		) ); */
//error_log( print_r($this->found_data, true) );
		/** @var  WP_List_table class */
		$this->items = $this->found_data;

	}

	/**
	 *
	 */
	function get_columns() {

		$columns = array();

		/*
		if ( $this->table_first_field_is_checkbox ) {
			$columns['cb'] = '<input type="checkbox" />';
		};	// */

		foreach ( $this->table_fields as $field=>$attrs) {
			$columns[$field] = $attrs['caption'];
		}
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array();
		foreach ( $this->table_fields as $field=>$attrs ) {
			if ( $attrs['sortable'] ) {
				$sortable_columns[$field] = array( $field, false );
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
	 * @param $a
	 * @param $b
	 * @internal param $
	 * @return int
	 */
	function usort_reorder( $a, $b ) {
		// If no sort, get the default
		$i=0;
		$default_field = 'source';

		foreach ( $this->table_fields as $field=>$attrs) {
			$default_field = ($i==0) ? $field : $default_field;
			if ( isset($attrs['order']) ) break;
			$i++;
		}
		$field 	 = ( isset($attrs['order']) ) ? $field : $default_field;
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : $field;

		// If no order, default to asc
		if ( ! empty($_GET['order'] ) ) {
			$order = $_GET['order'];
		} else {
			$order = ( isset($attrs['order']) ) ? $attrs['order'] : 'asc';
		}

		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	/**
	 * Define function for add item actions by name 'column_flag'
	 * @since 1.0.0
	 * @param  $item array
	 * @return string
	 */
	function column_flag( $item  ) {
		return $item['flag'];
	}

	/**
	 * Define function for add item actions by name 'column_flag'
	 * @since 1.0.0
	 * @param  $item array
	 * @return string
	 */
	function column_locale( $item  ) {
		return $item['locale'];
	}

	/**
	 * Define function for add item actions by name 'column_code'
	 * @since 1.0.0
	 * @param  $item array
	 * @return string
	 */
	function column_code( $item  ) {

		if ( isset( $this->table_fields['code']['actions'] ) && !empty( isset( $this->table_fields['code']['actions'] ) ) ) {

			//error_log( print_r( $this->table_fields['code']['actions'], true ) );
			foreach( $this->table_fields['code']['actions'] as $action=>$data ) {
				/** add actions for language code */
				$class = $data['ajaxify'] ? 'class="ajaxify"' : '';
				switch ( $action ) {
				case 'edit' :
					$actions['edit'] = sprintf( '<a %1s href="#">%2s</a>', $class, $data['caption'] );
					break;
				}

			}
			return sprintf( '%1s %2s', $item['code'], $this->row_actions( $actions ) );

		} else {

			return $item['code'];

		}

	}


	/**
	 * Define function for add item actions by name 'column_default'
	 * @since 1.0.0
	 * @param  $item array
	 * @return string
	 */
	function column_default( $item, $column_name ) {

		if ( isset( $this->table_fields[$column_name] ) ) {
			return $item[$column_name];
		} else {
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}

	}

	/**
	 * Define function for add item actions by name 'column_cb'
	 * @since 1.0.0
	 * @param  $item array
	 * @return string
	 */
	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="item[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * Display the dummy table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function display_dummy_table() {
		extract( $this->_args );

		?>
		<table class="wp-list-table-dummy wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tbody id="the-list-dummy">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table>
		<?php

	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function display_tablenav( $which ) {
		//if ( 'top' == $which )
			//wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions(); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

			<br class="clear" />
		</div>
	<?php
	}

}	