<?php
/**
 * Controller
 * All add_filter and add_action calls should be placed here
 * @package WPGlobus
 */

/**
 * @deprecated 15.01.20 Calls wp_get_object_terms, which is already filtered
 * @see wpglobus_filter_get_terms
 */
//add_filter( 'get_the_terms', 'wpglobus_filter_get_terms', 0 );

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


/**
 * Full description is in @see WPGlobus_Filters::filter__sanitize_title
 * @scope both
 */
add_filter( 'sanitize_title', [ 'WPGlobus_Filters', 'filter__sanitize_title' ], 0 );

# --- EOF