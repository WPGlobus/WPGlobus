<?php
/**
 * Controller
 * All add_filter and add_action calls should be placed here
 * @package WPGlobus
 */

/**
 * @deprecated 15.01.20 Calls wp_get_object_terms, which is already filtered
 * @see        wpglobus_filter_get_terms
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

/**
 * Used by @see get_terms (3 places in the function)
 * @scope both
 * -
 * Example of WP core using this filter: @see _post_format_get_terms
 * -
 * Set priority to 11 for case ajax-tag-search action from post.php screen
 * @see   wp_ajax_ajax_tag_search() in wp-admin\includes\ajax-actions.php
 * Note: this filter is temporarily switched off in @see WPGlobus::_get_terms
 * @todo  Replace magic number 11 with a constant
 */
add_filter( 'get_terms', [ 'WPGlobus_Filters', 'filter__get_terms' ], 11 );

/**
 * Filter for @see get_term
 * We need it only on front/AJAX and at the "Menus" admin screen.
 * There is an additional restriction in the filter itself.
 */
if ( WPGlobus_WP::is_doing_ajax() || ! is_admin() || WPGlobus_WP::is_pagenow( 'nav-menus.php' ) ) {
	add_filter( 'get_term', [ 'WPGlobus_Filters', 'filter__get_term' ], 0 );
}

/**
 * Filter for @see home_url
 */
add_filter( 'home_url', [ 'WPGlobus_Filters', 'filter__home_url' ] );

/**
 * Filter @see get_pages
 */
add_filter( 'get_pages', [ 'WPGlobus_Filters', 'filter__get_pages' ], 0 );

/**
 * Basic post/page filters
 * -
 * Note: We don't use 'the_excerpt' filter because 'get_the_excerpt' will be run anyway
 * @see  the_excerpt()
 * @see  get_the_excerpt()
 * @todo look at 'the_excerpt_export' filter where the post excerpt used for WXR exports.
 */
add_filter( 'the_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );
add_filter( 'the_content', [ 'WPGlobus_Filters', 'filter__text' ], 0 );
add_filter( 'get_the_excerpt', [ 'WPGlobus_Filters', 'filter__text' ], 0 );

# --- EOF