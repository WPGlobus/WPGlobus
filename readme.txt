=== WPGlobus - Multilingual Everything! ===
Contributors: tivnetinc, tivnet, alexgff
Donate link: http://www.wpglobus.com/
Tags: ACF, bilingual, globalization, i18n, international, l10n, language, localization, multilanguage, multilingual, multilingual SEO, SEO, switcher, translate, translation, WPGlobus, TIVWP
Requires at least: 4.0
Tested up to: 4.1.1
Stable tag: trunk
License: GPLv2
License URI: https://github.com/WPGlobus/WPGlobus/blob/master/LICENSE

Multilingual / Globalization: URL-based multilanguage; easy translation interface, compatible with WP SEO by Yoast, All in One SEO Pack and ACF!

== Description ==

**WPGlobus** is a plugin for making bilingual / multilingual WordPress blogs and sites.

> **Versions 1.0.x are released to the general public as Beta Software. During the Beta stage, the software is tested for bugs, crashes, errors and inconsistencies. If you see some obscure errors that the development team might not yet discovered, please let us know! Your reports are appreciated!**

With the WPGlobus Free Core plugin, you can:

* Add one or several languages to your WP blog/site, so that the users with the required capabilities can:
	* translate Posts and Pages to multiple languages using an easy tabbed interface;
	* translate major taxonomies: categories and tags;
	* translate menus and widgets;
* Enable multilingual SEO features of:
    * WP SEO by Yoast;
    * All in One SEO Pack by Michael Torbert.
* Translate Custom Fields:
	* standard;
	* created with the Advanced Custom Fields plugin.
* Switch the languages at the Front using:
	* a drop-down menu extension;
	* a customizable widget with various display options;
* Switch the Administrator interface language using a top bar selector.

The WPGlobus option panel allows for selecting active languages as well as defining custom combinations of country flag and language abbreviation.

= More info =

* [WPGlobus.com website](http://www.wpglobus.com/).
* [WPGlobus code on GitHub](https://github.com/WPGlobus).
* WPGlobus on:
	* [LinkedIn](https://www.linkedin.com/company/wpglobus),
	* [Facebook](https://www.facebook.com/WPGlobus),
	* [Twitter](https://twitter.com/WPGlobus) and
	* [Google Plus](https://plus.google.com/+Wpglobus).

= WPGlobus is compatible with these popular plugins: =

* [WordPress SEO](https://yoast.com/wordpress/plugins/seo/) by [Joost de Valk](https://profiles.wordpress.org/joostdevalk/);
* [All in One SEO Pack](https://wordpress.org/plugins/all-in-one-seo-pack/) by [Michael Torbert](https://profiles.wordpress.org/hallsofmontezuma/);
* [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) by [Elliot Condon](https://profiles.wordpress.org/elliotcondon/);
* [Sidebar Login](https://wordpress.org/plugins/sidebar-login/) by [Mike Jolley](https://profiles.wordpress.org/mikejolley/).

= 3rd Party Software and Files Used =

* [ReduxFramework](http://reduxframework.com/) core is embedded in the plugin to provide a nice admin interface. If the [ReduxFramework Plugin](https://wordpress.org/plugins/redux-framework/) is already installed, it will be used instead of the embedded version.
* Most of the flag images are downloaded from the [Flags of the World](http://www.crwflags.com/FOTW/FLAGS/index.html) website.

== Installation ==

You can install this plugin directly from your WordPress dashboard:

1. Go to the *Plugins* menu and click *Add New*.
1. Search for *WPGlobus*.
1. Click *Install Now* next to the WPGlobus plugin.
1. Activate the plugin.

Alternatively, see the guide to [Manually Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= Do you support PHP 5.x? =

* We develop using PHP 5.5, and would like everyone to use at least that version.
* PHP 5.2 is highly discouraged. At this stage, we try to follow the WordPress guidelines and do not use any of the 5.3+ features (namespaces, anonymous functions, etc.) - but we do not guarantee that it will be the case in the future releases.
* So, please use PHP 5.5+. Everyone will feel better. Thank you!

= Do you support MSIE / Opera / Safari / Chrome / Firefox - Version x.x? =

* We believe that our CSS and JS run properly on the most recent versions of those browsers.
* We do our best to fix any problems you discover on Chrome, FF and MSIE 11+. Opera and Safari are excluded from our top list, but they should work fine. MSIE 10,9,8,7,etc. - completely ignored (thanks for understanding).

= Do you plan to support subdomains and URL query parameters? =

* WPGlobus does not support switching languages using the `/page/?lang={language}` URLs or subdomains `en.example.com; fr.example.com`. If we see a demand, we may release this functionality in a separate extension. Having it in the core would affect the overall performance.

= I am using WPML (qTranslate, Polylang, Multilingual Press, etc.). Can I switch to WPGlobus? =

* Switching from (m)qTranslate(-X) should be straightforward and smooth if you are using the URLs in the form `/en/page/`, `/fr/page`, etc.
* Switching from other plugins is also possible, but will require some manual work. In the future, we plan to release the transition routines.

= The theme/plugin 'X' I am using does not display the multiple languages correctly. Can you help? =

* If this is a free theme/plugin, available on WordPress.org, written using the WordPress 4.x standards, we'll do our best to find a solution;
* Unfortunately, we cannot help you with any commercial theme of plugin that demonstrates incompatibility with WPGlobus. However, if the theme/plugin's author wishes to contact us and work on the solution together - we'll be very happy to collaborate.

= Do you plan to support WooCommerce, EDD, other e-Commerce plugins? =

* Yes, we do. Currently working on WC. Other plugins will follow.

= How do I contribute to WPGlobus? =

We appreciate all contributions, ideas, critique, and help.

* To speed up our development, please report bugs, with reproduction steps, or post patches on [WPGlobus GitHub](https://github.com/WPGlobus/WPGlobus).
* Plugin and theme authors: please try WPGlobus and let us know if you find any compatibility problems.
* Contact us directly on [WPGlobus.com](http://www.wpglobus.com/contact-us/).

= More info? =

Please check out the [WPGlobus Website](http://www.wpglobus.com/) for additional information.

== Screenshots ==

1. Admin interface: languages setup.
2. Front: language switcher in the twentyfourteen theme menu.

== Upgrade Notice ==

No backward incompatibility issues in the 1.0.x releases. The 0.1.x is no longer supported. If you have WPGlobus 0.1.x, please upgrade now.

== Changelog ==

= 1.0.9 =
* ADDED:
	* Admin interface to enable/disable WPGlobus for selected metaboxes.
	* Admin interface to enable/disable WPGlobus for selected Custom Post Types.
* FIXED:
	* URL localization with or without `www`, regardless of its presence in `home_url`.
	* Admin language tabs work correctly with custom post types that don't have 'title' or 'editor'.
	* All in One SEO pack plugin works correctly on the `post-new.php` admin page.
	* Language is set correctly during AJAX calls, using `HTTP_REFERER` info.
	* Language is retrieved from the current URL before other plugins load their translations.
	
= 1.0.8.1 =
* FIXED:
	* Reset hierarchical taxonomies checkmarks after save post or update post's page.
	* Incorrect empty string returning when a non-string argument passed to the text filter.

= 1.0.8 =
* ADDED:
	* Partial support of the All in One SEO Pack plugin.
	* Change WP Admin language using an Admin bar selector.
* FIXED:
	* Changed flag to `us.png` for the `en_US` locale.
	* Some Admin interface improvements.
	* Corrected field updates at the `edit-tags.php` page.
	* Corrected post saving in WPGlobus developer's mode (toggle off).
	* Support of post types with no `editor` (content).

= 1.0.7.2 =
* FIXED:
	* URL switching when WordPress serves only part of the site, like `www.example.com/blog`. Reported by [IAmVincentLiu](https://wordpress.org/support/profile/iamvincentliu) - THANKS!

= 1.0.7.1 =
* FIXED:
	* Anonymous function call prevented installing on PHP 5.2. Related to the reports by [barques](https://wordpress.org/support/profile/barques) and [Jeff Brock](https://wordpress.org/support/profile/jeffbrockstudio) - THANKS!

= 1.0.7 =
* ADDED:
	* WPGlobus Language Selector widget.
	* Enable language selector in navigation menus created using `wp_list_pages`.
	* Frontend filter meta description for All In One SEO Pack plugin.
* FIXED:
	* CSS for WPGlobus Universal Editor buttons.

= 1.0.6 =
* ADDED:
	* Admin interface and front filter to translate widgets.
	* Deutsch (de_DE) PO / MO-Dateien für WPGlobus Administration.
* FIXED:
	* Clean subjects of the comment notification emails.

= 1.0.5 =
* ADDED:
	* Localization interface for ACF text and textarea fields; no need to format languages manually.
	* Localization interface for the standard Custom Fields.

= 1.0.4 =
* FIXED:
	* Disabled WPGlobus admin interface on ACF screens - until we support them properly.
* ADDED:
	* Frontend filter acf/load_value/type=text(area): works if the fields were manually formatted {:en}...{:}

= 1.0.3 =
* FIXED:
	* PHP notice on plugin activation hook when a theme is upgraded.
	* Language selector drop-down applied to all menus instead of the selected one.
	* Correct display of the default category name on the edit-tags.php?taxonomy=category page.

= 1.0.2 =
* FIXED:
	* Save posts correctly if no default language title entered
	* Preserve languages for trashed, and later restored posts
	* Save languages correctly at heartbeat for pending and drafts
* ADDED:
	* Filter to translate title attributes in nav menus

= 1.0.1 =
* FIXED:
	* Line breaks disappear in visual mode during autosave
	* Correct display of slug in WP-SEO panel

= 1.0.0 =
* Beta-version of the plugin.
* Can translate all basic elements of WordPress
* WP-SEO by Yoast is supported
* ?lang= URLs dropped

= 0.1.1 =
* FIX: Notice 'walker_nav_menu_start_el' filter in functions.php twentyfifteen theme

= 0.1.0 =
* Initial release (language switcher)