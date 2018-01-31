<?php
/**
 * Controller
 * All add_filter and add_action calls should be placed here
 * @package WPGlobus
 */

/**
 * Note the priority '2', and not '0'.
 * @see \WPGlobus_Config::__construct for the actions that must be performed before this one.
 */
add_action( 'plugins_loaded', array( 'WPGlobus', 'init' ), 2 );

/**
 * Description in @see WPGlobus_Filters::filter__get_the_terms
 */
if ( is_admin() ) {
	add_filter( 'get_the_terms', array( 'WPGlobus_Filters', 'filter__get_the_terms' ), 0 );
}

/**
 * Filter @see wp_get_object_terms()
 */
if ( empty( $_GET['wpglobus'] ) || 'off' !== $_GET['wpglobus'] ) { // WPCS: input var ok, sanitization ok.
	add_filter( 'wp_get_object_terms', array( 'WPGlobus_Filters', 'filter__wp_get_object_terms' ), 0 );
}

/**
 * Filter for the "Tags" metabox in post edit. Does NOT affect the "Categories" metabox.
 * @see   get_terms_to_edit() wp-admin\includes\taxonomy.php
 * @scope admin
 * @since 1.6.4
 */
if (
	( empty( $_GET['wpglobus'] ) || 'off' !== $_GET['wpglobus'] ) // WPCS: input var ok, sanitization ok.
	&& is_admin()
	&& WPGlobus_WP::is_pagenow( 'post.php' )
) {
	add_filter( 'terms_to_edit', array( 'WPGlobus_Filters', 'filter__terms_to_edit' ), 5 );
}

/**
 * Filter for the "Tags" box on edit.php page.
 * @see filter 'pre_insert_term' in wp-includes\taxonomy.php
 * @scope admin
 * @since 1.6.6
 */
if (
	WPGlobus_WP::is_http_post_action('inline-save')
	&& false !== strpos( $_SERVER[ 'HTTP_REFERER' ], 'edit.php' )
) {
	add_filter( 'pre_insert_term', array( 'WPGlobus_Filters', 'filter__pre_insert_term' ), 5, 2 );
}

/**
 * Filter for the "Tags" box on post.php page.
 * To debug check $term before and after line "$term = apply_filters( 'pre_insert_term', $term, $taxonomy );"
 *
 * @see filter 'pre_insert_term' in wp-includes\taxonomy.php
 * @scope admin
 * @since 1.7.0
 */
if (
	( empty( $_GET['wpglobus'] ) || 'off' !== $_GET['wpglobus'] ) // WPCS: input var ok, sanitization ok.
	&& is_admin()
	&& WPGlobus_WP::is_pagenow( 'post.php' )
) {
	add_filter( 'pre_insert_term', array( 'WPGlobus_Filters', 'filter__pre_insert_term' ), 5, 2 );
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
//if ( WPGlobus_WP::is_pagenow( 'nav-menus.php' ) ) {
	/**
	 * @todo temporarily disable the filter
	 * need to test js in work
	 */
	//add_filter( 'wp_setup_nav_menu_item', array( 'WPGlobus_Filters', 'filter__nav_menu_item' ), 0 );
//}

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
 * Filter @see wp_trim_words
 * @scope admin
 * @since 1.0.14
 * Trims text to a certain number of words in the current language
 */
if ( is_admin() && WPGlobus_WP::is_pagenow( 'index.php' ) ) {
	add_filter( 'wp_trim_words', array( 'WPGlobus_Filters', 'filter__wp_trim_words' ), 0, 4 );
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
 * @see   WPGlobus_Filters::filter__the_posts for the description
 * @scope front
 * @since 1.0.14
 */
if ( ! is_admin() ) {
	add_filter( 'the_posts', array( 'WPGlobus_Filters', 'filter__the_posts' ), 0, 2 );
}

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

/**
 * Feed options.
 * @see 'wp_feed_options' action in wp-includes\feed.php
 */
add_action( 'wp_feed_options', array( 'WPGlobus_Filters', 'fetch_feed_options' ) );

/**
 * Register the WPGlobus widgets
 * @see   WPGlobusWidget
 * @since 1.0.7
 */
add_action( 'widgets_init', array( 'WPGlobus_Filters', 'register_widgets' ) );


/**
 * Filters for widgets
 */
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

	/**
	 * Language-dependent conditions in the `Widget Logic` plugin.
	 *
	 * If the global var `$wl_options` is not empty then there are some logic conditions set,
	 * and we should filter them.
	 * If that variable came from somewhere else then the filter simply won't fire.
	 *
	 * The condition set in the default language works for all languages if not overwritten
	 * in the corresponding tab.
	 *
	 * @link https://wordpress.org/plugins/widget-logic/
	 *
	 * @since 1.6.0
	 */
	if ( ! empty( $GLOBALS['wl_options'] ) ) {
		add_filter( 'widget_logic_eval_override', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	}
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
 * @todo  See also action__admin_init where we do exceptions for the 'not on admin' rule.
 */
if ( WPGlobus_WP::is_doing_ajax() || ! is_admin() ) {
	add_filter( 'option_blogdescription', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	add_filter( 'option_blogname', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
}

/**
 * @see get_locale()
 */
add_filter( 'locale', array( 'WPGlobus_Filters', 'filter__get_locale' ), PHP_INT_MAX );

/** @todo Move the filter to Filters class */
add_action( 'activated_plugin', array( 'WPGlobus', 'activated' ) );

add_action( 'admin_init', array( 'WPGlobus_Filters', 'action__admin_init' ), 0 );

/**
 * Translate metadata
 * @since 1.2.1
 */
add_action( 'wp', array( 'WPGlobus_Filters', 'set_multilingual_meta_keys' ) );
add_filter( 'get_post_metadata', array( 'WPGlobus_Filters', 'filter__postmeta' ), 0, 4 );

/**
 * Filter CSS rules.
 */
if ( ! is_admin() ) {
	add_filter( 'wpglobus_styles', array( 'WPGlobus_Filters', 'filter__front_styles' ), 10, 2 );
}

/**
 * Let @see url_to_postid() work with localized URLs.
 * @since 1.8.4
 */
add_filter( 'url_to_postid', array( 'WPGlobus_Filters', 'filter__url_to_postid' ), - PHP_INT_MAX );

/**
 * Detect the language needed to correctly show oembed.
 * @since 1.8.4
 */
add_filter( 'oembed_request_post_id', array( 'WPGlobus_Filters', 'filter__oembed_request_post_id' ), - PHP_INT_MAX, 2 );

/**
 * Filter the oembed data returned by the /wp-json/oembed/... calls.
 * @since 1.8.4
 */
add_filter( 'oembed_response_data', array( 'WPGlobus_Filters', 'filter__oembed_response_data' ), - PHP_INT_MAX );

/**
 * Filters the name to associate with the "from" email address.
 * @see wp-includes\pluggable.php
 * @since 1.9.5
 */
add_filter( 'wp_mail_from_name', array( 'WPGlobus_Filters', 'filter__text' ), 5 );

/**
 * Filters the wp_mail() arguments.
 * @see wp-includes\pluggable.php
 * @since 1.9.5
 */
add_filter( 'wp_mail', array( 'WPGlobus_Filters', 'filter__wp_mail' ), 5 );

/**
 * Filters oEmbed HTML. 
 * Case when post has embedded local URL in content.
 *
 * @see wp-includes\class-wp-embed.php
 * @since 1.9.8
 */
add_filter( 'embed_oembed_html', array( 'WPGlobus_Filters', 'filter__embed_oembed_html' ), 5, 4 );

/**
 * ACF filters
 * @todo Move to a separate controller
 */
if ( WPGlobus_WP::is_doing_ajax() || ! is_admin() ) {
	add_filter( 'acf/load_value/type=text', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	add_filter( 'acf/load_value/type=textarea', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
	add_filter( 'acf/load_value/type=wysiwyg', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
}

/**
 * All In One SEO Pack filters
 */
if ( defined( 'AIOSEOP_VERSION' ) ) {
	if ( is_admin() ) {

		/**
		 * Filter for @see localization.
		 * @scope admin
		 * @since 1.2.1
		 */
		add_filter( 'localization', array( 'WPGlobus_Filters', 'filter__text' ), 0 );

	} else {

		require_once dirname( __FILE__ ) . '/vendor/class-wpglobus-aioseop.php';

		/**
		 * Filter for @see localization.
		 * @scope front
		 *
		 * @since 1.1.1
		 * add_filter( 'localization', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
		 *
		 * @since 1.6.6
		 */
		add_filter( 'localization', array( 'WPGlobus_All_in_One_SEO', 'filter__text' ), 0 );

		/**
		 * Filter for @see aioseop_description.
		 * @scope front
		 * @since 1.0.8
		 */
		add_filter( 'aioseop_description', array( 'WPGlobus_All_in_One_SEO', 'filter__description' ), 0 );

		/**
		 * Filter for @see aioseop_title
		 * @scope front
		 * @since 1.0.8
		 */
		add_filter( 'aioseop_title', array( 'WPGlobus_All_in_One_SEO', 'filter__title' ), 0 );

	}
}

if ( class_exists( 'Whistles_Load' ) ) {
	/**
	 * Translate "Whistles"
	 * https://wordpress.org/plugins/whistles/
	 */
	add_filter( 'whistle_content', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
}

if ( class_exists( 'Tribe__Events__Main' ) ) {

	/**
	 * Translate "The Events Calendar"
	 * https://wordpress.org/plugins/the-events-calendar/
	 */

	require_once dirname( __FILE__ ) . '/vendor/class-wpglobus-the-events-calendar.php';

	add_filter( 'tribe_events_template_data_array', array( 'WPGlobus_The_Events_Calendar', 'filter__events_data' ), 0, 3 );

}

if ( class_exists( 'Mega_Menu' ) ) {

	/**
	 * Translate "Max Mega Menu"
	 * https://wordpress.org/plugins/megamenu/
	 *
	 * @since 1.4.9
	 */
	add_filter( 'megamenu_the_title', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
}

if ( class_exists( 'RevSliderFront' ) ) {

	/* @noinspection NestedPositiveIfStatementsInspection */
	if (
		/**
		 * Filter to start the support Slider Revolution.
		 *
		 * @since 1.6.1
		 *
		 * @param bool true.
		 * @return bool
		*/
		apply_filters( 'wpglobus_revslider_start', true )
	) :

		/**
		 * Translate layers
		 * @see https://revolution.themepunch.com/
		 *
		 * @since 1.5.0
		 */
		require_once dirname( __FILE__ ) . '/vendor/class-wpglobus-revslider.php';
		WPGlobus_RevSlider::controller();

	endif;

}

if ( function_exists( '__mc4wp_flush' ) || function_exists( '_mc4wp_load_plugin' ) ) {

	/**
	 * MailChimp for WordPress
	 * @see https://wordpress.org/plugins/mailchimp-for-wp/
	 *
	 * @since 1.5.4
	 * @since 1.7.11
	 */
	require_once dirname( __FILE__ ) . '/vendor/class-wpglobus-mailchimp-for-wp.php';
	WPGlobus_MailChimp_For_WP::controller();
}

/*EOF*/
