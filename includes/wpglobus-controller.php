<?php
/**
 * Controller
 * All add_filter and add_action calls should be placed here
 * @package WPGlobus
 */

/** @todo Move the filter to Filters class */
add_action( 'plugins_loaded', array( 'WPGlobus', 'init' ), 0 );

/**
 * Description in @see WPGlobus_Filters::filter__get_the_terms
 */
if ( is_admin() ) {
	add_filter( 'get_the_terms', array( 'WPGlobus_Filters', 'filter__get_the_terms' ), 0 );
}

/**
 * Admin: use filter for @see get_terms_to_edit function. See meta-boxes.php file.
 * @scope admin Edit post: see "Tags" metabox
 *        Does NOT affect the "Categories" metabox
 * @scope front WC breadcrumb
 */
if ( is_admin() && ! empty( $_GET['wpglobus'] ) && 'off' === $_GET['wpglobus'] ) {
	/**
	 * nothing to do
	 */
} else {
	add_filter( 'wp_get_object_terms', array( 'WPGlobus_Filters', 'filter__wp_get_object_terms' ), 0 );
}


/**
 * Full description is in @see WPGlobus_Filters::filter__sanitize_title
 * @scope both
 */
add_filter( 'sanitize_title', array( 'WPGlobus_Filters', 'filter__sanitize_title' ), 0 );

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
add_filter( 'get_terms', array( 'WPGlobus_Filters', 'filter__get_terms' ), 11 );

/**
 * Filter for @see get_term
 * We need it only on front/AJAX and at the "Menus" admin screen.
 * There is an additional restriction in the filter itself.
 */
if ( WPGlobus_WP::is_doing_ajax() || ! is_admin() || WPGlobus_WP::is_pagenow( 'nav-menus.php' ) ) {
	add_filter( 'get_term', array( 'WPGlobus_Filters', 'filter__get_term' ), 0 );
}

/**
 * Filter for @see wp_setup_nav_menu_item
 */
if ( WPGlobus_WP::is_pagenow( 'nav-menus.php' ) ) {
	/**
	 * @todo temporarily disable the filter
	 * need to test js in work
	 */
	//add_filter( 'wp_setup_nav_menu_item', array( 'WPGlobus_Filters', 'filter__nav_menu_item' ), 0 );
}

if ( ! is_admin() ) {
	/**
	 * Filter for @see wp_nav_menu_objects
	 * We need it only on front for translate attribute title in nav menus
	 */
	add_filter( 'wp_nav_menu_objects', array( 'WPGlobus_Filters', 'filter__nav_menu_objects' ), 0 );
}

/**
 * Filter for @see nav_menu_description
 */
add_filter( 'nav_menu_description', array( 'WPGlobus_Filters', 'filter__nav_menu_description' ), 0 );

/**
 * Filter @see heartbeat_received
 */
add_filter( 'heartbeat_received', array( 'WPGlobus_Filters', 'filter__heartbeat_received' ), 501, 3 );

/**
 * Filter for @see home_url
 */
add_filter( 'home_url', array( 'WPGlobus_Filters', 'filter__home_url' ) );

/**
 * Filter @see get_pages
 */
add_filter( 'get_pages', array( 'WPGlobus_Filters', 'filter__get_pages' ), 0 );

/**
 * Filter @see comment_moderation_subject
 */
add_filter( 'comment_moderation_subject', array( 'WPGlobus_Filters', 'filter__comment_moderation' ), 10, 2 );

/**
 * Filter @see comment_moderation_text
 */
add_filter( 'comment_moderation_text', array( 'WPGlobus_Filters', 'filter__comment_moderation' ), 10, 2 );

/**
 * Filter @see the_category
 * @scope admin
 * @since 1.0.3
 * Show default category name in the current language - on the
 * wp-admin/edit-tags.php?taxonomy=category page, below the categories table
 */
if ( is_admin() && WPGlobus_WP::is_pagenow( 'edit-tags.php' ) ) {
	add_filter( 'the_category', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
}

/**
 * Basic post/page filters
 * -
 * Note: We don't use 'the_excerpt' filter because 'get_the_excerpt' will be run anyway
 * @see  the_excerpt()
 * @see  get_the_excerpt()
 * @todo look at 'the_excerpt_export' filter where the post excerpt used for WXR exports.
 */
add_filter( 'the_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
add_filter( 'the_content', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
add_filter( 'get_the_excerpt', array( 'WPGlobus_Filters', 'filter__text' ), 0 );

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
add_filter( 'single_post_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );

/**
 * @see post_type_archive_title has its own filter on $post_type_obj->labels->name
 *                              and is used by @see wp_title
 */
add_filter( 'post_type_archive_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );

/**
 * @see single_term_title() uses several filters depending on the term type
 */
add_filter( 'single_cat_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
add_filter( 'single_tag_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
add_filter( 'single_term_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );

if ( ! is_admin() ) {
	/**
	 * This is usually used in 'widget' methods of the @see WP_Widget - derived classes,
	 * for example in @see WP_Widget_Pages::widget
	 */
	add_filter( 'widget_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );

	/**
	 * This is for the widget parameters other than the title.
	 * For example, in the standard `Text` widget, this translates the widget body.
	 */
	add_filter( 'widget_display_callback', array( 'WPGlobus_Filters', 'filter__widget_display_callback' ), 0 );
}

/**
 * @see   get_bloginfo in general-template.php
 *                   Specific call example is get_option('blogdescription');
 * @see   get_option in option.php
 * For example this is used in the Twenty Fifteen theme's header.php:
 * $description = get_bloginfo( 'description', 'display' );
 * @scope Front. In admin we need to get the "raw" string.
 * @todo  We must not translate blogname in admin because it's used in many important non-visual places
 *       but we should JS the blogname at the admin bar
 * <li id="wp-admin-bar-site-name" class="menupop"><a ...>{:en}WPGlobus{:}{:ru}ВПГлобус{:}</a>
 */
if ( WPGlobus_WP::is_doing_ajax() || ! is_admin() ) {
	add_filter( 'option_blogdescription', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	add_filter( 'option_blogname', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
}

/**
 * For customizer
 */
if ( is_admin() && WPGlobus_WP::is_pagenow( 'customize.php' ) ) {
	//add_filter( 'option_blogdescription', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	//add_filter( 'option_blogname', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
}

/**
 * @see get_locale()
 */
add_filter( 'locale', array( 'WPGlobus_Filters', 'filter__get_locale' ), PHP_INT_MAX );

/**
 * @todo Refactor url_info after beta-testing
 */
add_action( 'init', array( 'WPGlobus_Filters', 'action__init_url_info' ), 2 );

/** @todo Move the filter to Filters class */
add_action( 'activated_plugin', array( 'WPGlobus', 'activated' ) );

/**
 * ACF filters
 * @todo Move to a separate controller
 */
if ( WPGlobus_WP::is_doing_ajax() || ! is_admin() ) {
	add_filter( 'acf/load_value/type=text', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	add_filter( 'acf/load_value/type=textarea', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
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
			add_filter( 'esc_html', array( 'WPGlobus_Filters', 'filter__wpseo_columns' ), 0 );
		}

	} else {
		/**
		 * Filter SEO title and meta description on front only, when the page header HTML tags are generated.
		 * AJAX is probably not required (waiting for a case).
		 */
		add_filter( 'wpseo_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
		add_filter( 'wpseo_metadesc', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	}


}

/**
 * All In One SEO Pack filters
 */
if ( defined( 'AIOSEOP_VERSION' ) ) {
	if ( ! is_admin() ) {
		add_filter( 'aioseop_description', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	}	
}

# --- EOF