<?php
/**
 * Controller
 * All add_filter and add_action calls should be placed here
 * @package WPGlobus
 */

/**
 * @see get_the_terms
 * Calls wp_get_object_terms, which is already filtered, only if terms were not cached
 * @todo Check what's going on with cache
 */
//add_filter( 'get_the_terms', [ 'WPGlobus_Filters', 'filter__get_the_terms' ], 0 );

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
	add_filter( 'wp_get_object_terms', [ 'WPGlobus_Filters', 'filter__wp_get_object_terms' ], 0, 4 );
//	return apply_filters( 'wp_get_object_terms', $terms, $object_ids, $taxonomies, $args );
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

/**
 * @internal
 * Do not need to apply the wp_title filter
 * but need to make sure all possible components of @see wp_title are filtered:
 * post_type_archive_title
 * single_term_title
 * blog_info
 * @todo Check date localization in date archives
 */
//add_filter( 'wp_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );

/**
 * The @see single_post_title has its own filter on $_post->post_title
 */
add_filter( 'single_post_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );

/**
 * @see post_type_archive_title has its own filter on $post_type_obj->labels->name
 *                              and is used by @see wp_title
 */
add_filter( 'post_type_archive_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );

/**
 * @see single_term_title() uses several filters depending on the term type
 */
add_filter( 'single_cat_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );
add_filter( 'single_tag_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );
add_filter( 'single_term_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );

/**
 * This is usually used in 'widget' methods of the @see WP_Widget - derived classes,
 * for example in @see WP_Widget_Pages::widget
 */
add_filter( 'widget_title', [ 'WPGlobus_Filters', 'filter__text' ], 0 );

/**
 * @see get_bloginfo in general-template.php
 *                   Specific call is get_option('blogdescription');
 * @see get_option in option.php
 * For example this is used in the Twenty Fifteen theme's header.php:
 * $description = get_bloginfo( 'description', 'display' );
 * @scope Front. In admin we need to get the "raw" string.
 */
if ( WPGlobus_WP::is_doing_ajax() || ! is_admin() ) {
	add_filter( 'option_blogdescription', [ 'WPGlobus_Filters', 'filter__text' ], 0 );
}


/**
 * Yoast filters
 * @todo Move to a separate controller
 */
if ( defined( 'WPSEO_VERSION' ) ) {

	if ( is_admin() ) {

		if ( WPGlobus_WP::is_pagenow( 'edit.php' ) ) {
			/**
			 * To translate Yoast columns on edit.php page
			 */
			add_filter( 'esc_html', [ 'WPGlobus_Filters', 'filter__wpseo_columns' ], 0 );
		}

	} else {
		/**
		 * Filter SEO title and meta description on front only, when the page header HTML tags are generated.
		 * AJAX is probably not required (waiting for a case).
		 */
		add_filter( 'wpseo_title', 'wpg_text_filter', 0 );
		add_filter( 'wpseo_metadesc', 'wpg_text_filter', 0 );
	}


}

# --- EOF