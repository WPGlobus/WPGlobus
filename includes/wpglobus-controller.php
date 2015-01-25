<?php
/**
 * Controller
 * All add_filter and add_action calls should be placed here
 * @package WPGlobus
 */

/**
 * Admin: now use filter for get_terms_to_edit function. See meta-boxes.php file.
 * @scope admin Edit post: see "Tags" metabox
 *        Does NOT affect the "Categories" metabox
 * @scope front WC breadcrumb
 */
if ( is_admin() && ! empty( $_GET['wpglobus'] ) && 'off' == $_GET['wpglobus'] ) {
	/**
	 * nothing to do
	 * @todo в том файле где этот фильтр будет размещён, нужно предусмотреть его отключение
	 * для $_GET['wpglobus'] == 'off'
	 * see class-wpglobus.php:135,
	 * возможно ещё какие-то фильтры попадают под этот случай
	 */
} else {
	add_filter( 'wp_get_object_terms', [ 'WPGlobus_Filters', 'filter__wp_get_object_terms' ], 0 );
}

# --- EOF